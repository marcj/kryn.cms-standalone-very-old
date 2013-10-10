<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@Kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

namespace Core;

use Core\Models\Acl;
use Core\Models\AclQuery;
use Propel\Runtime\ActiveQuery\Criteria;

class Permission
{
    /**
     * targetType
     */
    const GROUP = 1;
    const USER = 0;

    /**
     * mode
     */
    const ALL = 0;
    const LISTING = 1;
    const VIEW = 2;
    const ADD = 3;
    const UPDATE = 4;
    const DELETE = 5;

    /**
     * constraintType
     */
    const CONSTRAINT_ALL = 0;
    const CONSTRAINT_EXACT = 1;
    const CONSTRAINT_CONDITION = 2;

    /**
     * @var array
     */
    private static $cache = array();

    /**
     * @var array
     */
    private static $acls = array();

    /**
     * If we use caching in getRules or not. Useless in testsuits.
     *
     * @var bool
     */
    private static $caching = true;

    /**
     * Activates or disables the caching mechanism.
     *
     * @param $caching
     */
    public static function setCaching($caching)
    {
        static::$caching = $caching;
    }

    /**
     * Returns true if caching mechanism is activated.
     *
     * @return bool
     */
    public static function getCaching()
    {
        return static::$caching;
    }

    public static function clearCache()
    {
        static::$caching = array();
    }

    /**
     *
     * Mode table:
     *
     *  0 all
     *  1 list
     *  2 view
     *  3 add
     *  4 update
     *  5 delete
     *
     *
     * @static
     *
     * @param        $objectKey
     * @param  int   $mode
     * @param  bool  $force
     *
     * @return mixed
     *
     */
    public static function &getRules($objectKey, $mode = 1, $targetType = null, $targetId = null, $force = false)
    {
        $objectKey = Object::normalizeObjectKey($objectKey);

        if ($targetType === null && Kryn::getClient()->hasSession()) {
            $user = Kryn::getClient()->getUser();
            $targetType = static::USER;
        }

        if ($targetType === self::USER && (($user && $targetId && $user->getId() != $targetId) || !$user)) {
            $user = Kryn::getPropelCacheObject('Users\\Models\\User', $targetId);
        }

        if ($targetType != static::USER) {
            $targetType = static::GROUP;
        }

        if ($user) {
            $targetId = $userId = $user->getId();
            $userGroups = $user->getUserGroups();

            if (count($userGroups) > 0) {
                foreach ($userGroups as $group) {
                    $inGroups[] = $group->getGroupId();
                }
                $inGroups = implode(', ', $inGroups);
            } else {
                $inGroups = '0';
            }
        }

        if (static::getCaching()) {
            $cacheKey = $targetType.'.'.$targetId.'.'.$objectKey . '.' . $mode;
            $cached = Kryn::getDistributedCache('core/acl-rules/' . $cacheKey);
            if (null !== $cached) {
                return $cached;
            }
        }

        if ($targetType == self::GROUP) {
            $inGroups = $targetId + 0;
        }

        if (!$inGroups) {
            $inGroups = '0';
        }

        $mode += 0;

        $data = array($objectKey, $mode);

        $targets = array();

        $targets[] = "( target_type = 1 AND target_id IN (?))";
        $data[] = $inGroups;

        if ($targetType === null || $targetType == self::USER) {
            $targets[] = "( target_type = 0 AND target_id = ?)";
            $data[] = $userId;
        }

        $query = "
                SELECT constraint_type, constraint_code, mode, access, sub, fields FROM " . pfx . "system_acl
                WHERE
                object = ? AND
                (mode = ? OR mode = 0) AND
                (
                    " . implode(' OR ', $targets) . "
                )
                ORDER BY prio DESC
        ";

        $res = dbQuery($query, $data);
        $rules = array();

        while ($rule = dbFetch($res)) {
            if ($rule['fields'] && substr($rule['fields'], 0, 1) == '{') {
                $rule['fields'] = json_decode($rule['fields'], true);
            }
            if ($rule['constraint_type'] == 2 && substr($rule['constraint_code'], 0, 1) == '[') {
                $rule['constraint_code'] = json_decode($rule['constraint_code'], true);
            }
            $rules[] = $rule;
        }

        dbFree($res);

        if (static::getCaching()) {
            Kryn::setDistributedCache('core/acl-rules/' . $cacheKey, $rules);
            return $rules;
        } else {
            return $rules;
        }
    }

    public static function removeObjectRules($objectKey)
    {
        $objectKey = Object::normalizeObjectKey($objectKey);
        $query = AclQuery::create();

        $query->filterByObject($objectKey);

        $query->delete();

    }

    /**
     * Get a condition object for item listings.
     *
     * @param  string $objectKey
     * @param  string $table
     *
     * @return array
     */
    public static function getListingCondition($objectKey, $table = '')
    {
        $objectKey = Object::normalizeObjectKey($objectKey);
        $rules =& self::getRules($objectKey, 1);

        if (count($rules) == 0) {
            return false;
        }

        if (self::$cache['sqlList_' . $objectKey]) {
            return self::$cache['sqlList_' . $objectKey];
        }

        $condition = '';
        $result = '';

        if (!$table) {
            $table = Object::getTable($objectKey);
        }

        $primaryList = Object::getPrimaryList($objectKey);
        $isNested = Object::isNested($objectKey);
        $primaryKey = current($primaryList);

        $lastBracket = '';

        $allowList = array();
        $denyList = array();

        $conditionObject[] = array('1', '!=', '1');

        foreach ($rules as $rule) {

            if ($rule['constraint_type'] == '1') {
                //todo, $rule['constraint_code'] can be a (urlencoded) composite pk
                //todo constraint_code is always urlencoded;
                $condition = array(dbQuote($primaryKey, $table), '=', $rule['constraint_code']);
            }

            if ($rule['constraint_type'] == '2') {
                $condition = $rule['constraint_code'];
            }

            if ($rule['constraint_type'] == '0') {
                $condition = array('1', '=', '1');
            } elseif ($rule['sub']) {
                if ($rule['constraint_type'] == '2') {
                    $pkCondition = dbConditionToSql($rule['constraint_code'], $table);
                } else {
                    $pkCondition = dbQuote($primaryKey, $table) . ' = ' . $rule['constraint_code'];
                }

                $childrenCondition = "($table.lft > (SELECT lft FROM $table WHERE $pkCondition ORDER BY lft ) AND ";
                $childrenCondition .= "$table.rgt < (SELECT rgt FROM $table WHERE $pkCondition ORDER BY rgt DESC))";
                $condition = array($condition, 'OR', $childrenCondition);
            }

            if ($rule['access'] == 1) {

                if ($conditionObject) {
                    $conditionObject[] = 'OR';
                }

                $conditionObject[] = $condition;

                if ($denyList) {
                    $conditionObject[] = 'AND NOT';
                    $conditionObject[] = $denyList;
                }

            }

            if ($rule['access'] != 1) {
                if ($denyList) {
                    $denyList[] = 'AND NOT';
                }

                $denyList[] = $condition;
            }
        }

        return $conditionObject;
    }

    public static function checkList(
        $objectKey,
        $targetType = null,
        $targetId = null,
        $rootHasAccess = false
    ) {
        return self::check($objectKey, null, null, self::LISTING, $targetType, $targetId, $rootHasAccess);
    }

    public static function checkListExact(
        $objectKey,
        $objectId,
        $targetType = null,
        $targetId = null,
        $rootHasAccess = false
    ) {
        return self::check($objectKey, $objectId, null, self::LISTING, $targetType, $targetId, $rootHasAccess);
    }

    public static function checkUpdate(
        $objectKey,
        $fields = null,
        $targetType = null,
        $targetId = null,
        $rootHasAccess = false
    ) {
        return self::check($objectKey, null, $fields, self::UPDATE, $targetType, $targetId, $rootHasAccess);
    }

    public static function checkDelete(
        $objectKey,
        $fields = null,
        $targetType = null,
        $targetId = null,
        $rootHasAccess = false
    ) {
        return self::check($objectKey, null, $fields, self::DELETE, $targetType, $targetId, $rootHasAccess);
    }

    public static function checkUpdateExact(
        $objectKey,
        $objectId,
        $fields = null,
        $targetType = null,
        $targetId = null,
        $rootHasAccess = false
    ) {
        return self::check($objectKey, $objectId, $fields, self::UPDATE, $targetType, $targetId, $rootHasAccess);
    }

    public static function checkDeleteExact(
        $objectKey,
        $objectId,
        $fields = null,
        $targetType = null,
        $targetId = null,
        $rootHasAccess = false
    ) {
        return self::check($objectKey, $objectId, $fields, self::DELETE, $targetType, $targetId, $rootHasAccess);
    }

    /**
     * @param string $objectKey
     * @param array $objectId
     *
     * @return bool
     */
    public static function isUpdatable($objectKey, $objectId = null)
    {
        if (null !== $objectId) {
            return static::checkUpdateExact($objectKey, $objectId);
        } else {
            return static::checkUpdate($objectKey);
        }
    }

    /**
     * @param string $objectKey
     * @param array $objectId
     *
     * @return bool
     */
    public static function isDeletable($objectKey, $objectId = null)
    {
        if (null !== $objectId) {
            return static::checkDeleteExact($objectKey, $objectId);
        } else {
            return static::checkDelete($objectKey);
        }
    }

    public static function checkAdd(
        $objectKey,
        $objectId,
        $fields = null,
        $targetType = null,
        $targetId = null,
        $rootHasAccess = false
    ) {
        return self::check($objectKey, $objectId, $fields, self::ADD, $targetType, $targetId, $rootHasAccess);
    }

    /*
    public static function checkRead($pObjectKey, $pObjectId, $pField = false)
    {
        return self::check($pObjectKey, $pObjectId, $pField, 2);
    }

    public static function checkAdd($pObjectKey, $pParentId, $pField = false)
    {
        return self::check($pObjectKey, $pParentId, $pField, 3, false, true);
    }

    public static function checkUpdate($pObjectKey, $pObjectId, $pField = false)
    {
        return self::check($pObjectKey, $pObjectId, $pField, 3);
    }
    */

    public static function setObjectList(
        $objectKey,
        $targetType,
        $targetId,
        $access,
        $fields = null,
        $withSub = false
    ) {
        return self::setObject(
            self::LISTING,
            $objectKey,
            self::CONSTRAINT_ALL,
            null,
            $withSub,
            $targetType,
            $targetId,
            $access,
            $fields
        );
    }

    public static function setObjectListExact(
        $objectKey,
        $objectPk,
        $targetType,
        $targetId,
        $access,
        $fields = null,
        $withSub = false
    ) {
        return self::setObject(
            self::LISTING,
            $objectKey,
            self::CONSTRAINT_EXACT,
            $objectPk,
            $withSub,
            $targetType,
            $targetId,
            $access,
            $fields
        );
    }

    public static function setObjectListCondition(
        $objectKey,
        $condition,
        $targetType,
        $targetId,
        $access,
        $fields = null,
        $withSub = false
    ) {
        return self::setObject(
            self::LISTING,
            $objectKey,
            self::CONSTRAINT_CONDITION,
            $condition,
            $withSub,
            $targetType,
            $targetId,
            $access,
            $fields
        );
    }

    public static function setObjectUpdate(
        $objectKey,
        $targetType,
        $targetId,
        $access,
        $fields = null,
        $withSub = false
    ) {
        return self::setObject(
            self::UPDATE,
            $objectKey,
            self::CONSTRAINT_ALL,
            null,
            $withSub,
            $targetType,
            $targetId,
            $access,
            $fields
        );
    }

    public static function setObject(
        $mode,
        $objectKey,
        $constraintType,
        $constraintCode,
        $withSub = false,
        $targetType,
        $targetId,
        $access,
        $fields = null
    ) {

        $objectKey = Object::normalizeObjectKey($objectKey);
        $acl = new Acl();

        $acl->setMode($mode);
        $acl->setTargetType($targetType);
        $acl->setTargetId($targetId);
        $acl->setSub($withSub);
        $acl->setAccess($access);

        if ($fields) {
            $acl->setFields(json_encode($fields));
        }

        $acl->setObject($objectKey);
        $acl->setConstraintCode(is_array($constraintCode) ? json_encode($constraintCode) : $constraintCode);
        $acl->setConstraintType($constraintType);

        $query = new AclQuery();
        $query->select('Prio');
        $query->filterByObject($objectKey);
        $query->filterByMode($mode);
        $query->orderByPrio(Criteria::DESC);
        $highestPrio = $query->findOne();

        $acl->setPrio($highestPrio + 1);

        self::$cache[$objectKey . '_' . $mode] = null;

        $acl->save();

        return $acl;
    }

    /**
     * @param       $objectKey
     * @param       $pObjectId
     * @param  bool $field
     * @param  int  $mode
     * @param  bool $rootHasAccess
     * @param  bool $asParent
     *
     * @return bool
     */
    public static function check(
        $objectKey,
        $pk,
        $field = false,
        $mode = 1,
        $targetType = null,
        $targetId = null,
        $rootHasAccess = false,
        $asParent = false
    ) {

        $objectKey = Object::normalizeObjectKey($objectKey);
        if (($targetId === null && $targetType === null) && Kryn::getAdminClient() && Kryn::getAdminClient()->hasSession()) {
            $targetId = Kryn::getAdminClient()->getUserId();
            $targetType = static::USER;

        } elseif (($targetId === null && $targetType === null) && Kryn::getClient() && Kryn::getClient()->hasSession()) {
            $targetId = Kryn::getClient()->getUserId();
            $targetType = static::USER;
        }

        if ($targetType === null) {
            $targetType = self::USER;
        }

        if ($targetId == 1) {
            return true;
        }

        if ($pk) {
            $pkString = Object::getObjectUrlId($objectKey, $pk);
            $cacheKey = $targetType.'.'.$targetId . '.'.$objectKey . '/' . $pkString . '/' . $field;
            $cached = Kryn::getDistributedCache('core/acl/'.$cacheKey);
            if (null !== $cached) {
                return $cached;
            }
        }

        $rules = self::getRules($objectKey, $mode, $targetType, $targetId);

        if (count($rules) == 0) {
            return false;
        }

        $access = false;

        $currentObjectPk = $pk;

        $definition = Object::getDefinition($objectKey);

        $not_found = true;
        $parent_acl = $asParent;

        $fIsArray = is_array($field);
        if ($fIsArray) {
            $fCount = count($field);

            $fKey = key($field);
            $fValue = current($field);
        }

        $depth = 0;
        while ($not_found) {
            $currentObjectPkString = Object::getObjectUrlId($objectKey, $currentObjectPk);
            $depth++;

            if ($depth > 50) {
                $not_found = false;
                break;
            }

            foreach ($rules as $acl) {

                if ($parent_acl && $acl['sub'] == 0) {
                    continue;
                }

                //print $acl['id'].', '.$acl['code'] .' == '. $currentObjectPk.'<br/>';
                if ($acl['constraint_type'] == 2 && $pk &&
                    ($objectItem = Object::get($objectKey, $currentObjectPk))
                ) {
                    if (!Object::satisfy($objectItem, $acl['constraint_code'])) {
                        continue;
                    }
                }

                if (
                    $acl['constraint_type'] != 1 ||
                    ($currentObjectPk && $acl['constraint_type'] == 1 && $acl['constraint_code'] == $currentObjectPkString)
                ) {

                    $field2Key = $field;

                    if ($field) {
                        if ($fIsArray && $fCount == 1) {
                            if (is_string($fKey) && is_array($acl['fields'][$fKey])) {
                                //this field has limits
                                if (($field2Acl = $acl['fields'][$fKey]) !== null) {
                                    if (is_array($field2Acl[0])) {
                                        //complex field rule, $field2Acl = ([{access: no, condition: [['id', '>', 2], ..]}, {}, ..])
                                        foreach ($field2Acl as $fRule) {

                                            if (($f = $definition->getField($fKey)) && $f->getType() == 'object') {
                                                $uri = $f->getObject() . '/' . $fValue;
                                                $satisfy = Object::satisfyFromUrl($uri, $fRule['condition']);
                                            } else {
                                                $satisfy = Object::satisfy($field, $fRule['condition']);
                                            }
                                            if ($satisfy) {
                                                return ($fRule['access'] == 1) ? true : false;
                                            }

                                        }

                                        //if no field rules fits, we consider the whole rule
                                        if ($acl['access'] != 2) {
                                            return ($acl['access'] == 1) ? true : false;
                                        }

                                    } else {

                                        //simple field rule $field2Acl = ({"value1": yes, "value2": no}

                                        if ($field2Acl[$fValue] !== null) {
                                            return ($field2Acl[$fValue] == 1) ? true : false;
                                        } else {
                                            //current($field) is not exactly defined in $field2Acl, so we set $access to $acl['access']
                                            //
                                            //if access = 2 then wo do not know it, cause 2 means 'inherited', so maybe
                                            //a other rule has more detailed rule
                                            if ($acl['access'] != 2) {
                                                $access == ($acl['access'] == 1) ? true : false;
                                                break;
                                            }
                                        }
                                    }
                                }
                            } else {
                                //this field has only true or false
                                $field2Key = $fKey;
                            }
                        }

                        if (!is_array($field2Key)) {
                            if ($acl['fields'] && ($field2Acl = $acl['fields'][$field2Key]) !== null && !is_array(
                                $acl['fields'][$field2Key]
                            )
                            ) {
                                $access = ($field2Acl == 1) ? true : false;
                                break;
                            } else {
                                //$field is not exactly defined, so we set $access to $acl['access']
                                //and maybe a rule with the same code has the field defined
                                // if access = 2 then this rule is only for exactly define fields
                                if ($acl['access'] != 2) {
                                    $access = ($acl['access'] == 1) ? true : false;
                                    break;
                                }
                            }
                        }
                    } else {
                        $access = ($acl['access'] == 1) ? true : false;
                        break;
                    }
                }
            }

            if ($definition->isNested() && $pk) {
                if (null === ($currentObjectPk = Object::getParentPk($objectKey, $currentObjectPk))) {
                    $access = $rootHasAccess ? true : $access;
                    break;
                }

                $parent_acl = true;
            } else {
                break;
            }
        }

        if ($pk) {
            Kryn::setDistributedCache('core/acl/'.$cacheKey, $access);
        }

        return $access;
    }

    /**
     *
     * Returns the acl infos for the specified id
     *
     * @param string  $object
     * @param integer $code
     *
     * @return array
     * @internal
     */
    public static function &getItem($object, $code)
    {

        self::normalizeCode($code);
        $acls =& self::getRules($object);

        foreach ($acls as $item) {
            $code2 = str_replace('%', '', $item['code']);
            $t = explode('[', $code2);
            $code2 = $t[0];
            self::normalizeCode($code2);
            if ($code2 == $code) {
                return $item;
            }
        }

        return false;
    }

    /*
        public static function set($pType, $pTargetType, $pTargetId, $pCode, $pActions, $pWithSub)
        {
            self::normalizeCode($pCode);
            $pType += 0;
            $pTargetType += $pTargetType;
            $pTargetId += $pTargetId;
            $pCode = esc($pCode);

            self::removeAcl($pType, $pTargetType, $pTargetId, $pCode);

            if ($pWithSub)
                $pCode .= '%';

            $pCode = '[' . implode(',', $pActions) . ']';

            $last_id = dbInsert('system_acl', array(
                'type' => $pType,
                'target_type' => $pTargetType,
                'target_id' => $pTargetId,
                'code' => $pCode
            ));

            self::$cache[$pType] = null;

            return $last_id;
        }

        public static function remove($pType, $pTargetType, $pTargetId, $pCode)
        {
            self::normalizeCode($pCode);

            $pType += 0;
            $pTargetType += $pTargetType;
            $pTargetId += $pTargetId;
            $pCode = esc($pCode);

            dbDelete('system_acl', "1=1
             AND type = $pType
             AND target_type = $pTargetType
             AND target_id = $pTargetId
             AND code LIKE '$pCode%'");

            self::$cache[$pType] = null;

        }
    */

    public static function normalizeCode(&$code)
    {
        $code = str_replace('//', '/', $code);

        if (substr($code, 0, 1) != '/') {
            $code = '/' . $code;
        }

        if (substr($code, -1) == '/') {
            $code = substr($code, 0, -1);
        }

    }

}

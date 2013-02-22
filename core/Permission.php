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

namespace core;

use Users\User;

class Permission
{
    const GROUP = 1;

    const USER = 0;

    const ALL = 0;

    const LISTING = 1;

    const VIEW = 2;

    const ADD = 3;

    const UPDATE = 4;

    const DELETE = 5;

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
     * @param $pCaching
     */
    public static function setCaching($pCaching)
    {
        static::$caching = $pCaching;
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
     * @param $pObjectKey
     * @param  int   $pMode
     * @param  bool  $pForce
     * @return mixed
     *
     */
    public static function &getRules($pObjectKey, $pMode = 1, $pTargetType = null, $pTargetId = null, $pForce = false) {

        $pObjectKey = str_replace('.', '\\', $pObjectKey);

        if (static::getCaching()) {
            if (self::$cache[$pObjectKey.'_'.$pMode] && $pForce == false)
                return self::$cache[$pObjectKey.'_'.$pMode];
        }

        if ($pTargetType === null && Kryn::getClient()->hasSession()) {
            $user = Kryn::getClient()->getUser();
        }

        if ($pTargetType === self::USER) {
            $user = Kryn::getPropelCacheObject('Users\\User', $pTargetId);
        }

        if ($user) {
            $userId = $user->getId();
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

        if ($pTargetType == self::GROUP) {
            $inGroups = $pTargetId+0;
        }

        if (!$inGroups) $inGroups = '0';

        $pMode += 0;

        $data = array($pObjectKey, $pMode);

        $targets = array();

        $targets[] = "( target_type = 1 AND target_id IN (?))";
        $data[] = $inGroups;

        if ($pTargetType === null || $pTargetType == self::USER) {
            $targets[] = "( target_type = 0 AND target_id = ?)";
            $data[] = $userId;
        }

        $query = "
                SELECT constraint_type, constraint_code, mode, access, sub, fields FROM ".pfx."system_acl
                WHERE
                object = ? AND
                (mode = ? OR mode = 0) AND
                (
                    ".implode(' OR ', $targets)."
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
            self::$cache[$pObjectKey.'_'.$pMode] = $rules;

            return self::$cache[$pObjectKey.'_'.$pMode];
        } else return $rules;
    }

    public static function removeObjectRules($pObjectKey)
    {
        $query = AclQuery::create();

        $query->filterByObject($pObjectKey);

        $query->delete();

    }

    /**
     * Get a condition object for item listings.
     *
     * @param  string $pObjectKey
     * @param  string $pTable
     * @return array
     */
    public static function getListingCondition($pObjectKey, $pTable = '')
    {
        $rules =& self::getRules($pObjectKey, 1);

        if (count($rules) == 0) return false;

        if (self::$cache['sqlList_' . $pObjectKey])
            return self::$cache['sqlList_' . $pObjectKey];

        $condition = '';
        $result = '';

        if (!$pTable)
            $pTable = Object::getTable($pObjectKey);

        $primaryList = Object::getPrimaryList($pObjectKey);
        $isNested = Object::isNested($pObjectKey);
        $primaryKey = current($primaryList);

        $lastBracket = '';

        $allowList = array();
        $denyList  = array();

        $conditionObject = array();

        foreach ($rules as $rule) {

            if ($rule['constraint_type'] == '1') {
                $condition = array(dbQuote($primaryKey, $pTable), '=', $rule['constraint_code']);
            }

            if ($rule['constraint_type'] == '2') {
                $condition = $rule['constraint_code'];

            }

            if ($rule['constraint_type'] == '0') {
                $condition = array('1', '=', '1');
            } elseif ($rule['sub']) {

                if ($rule['constraint_type'] == '2')
                    $pkCondition = dbConditionToSql($rule['constraint_code'], $pTable);
                else
                    $pkCondition = dbQuote($primaryKey, $pTable) . ' = ' . $rule['constraint_code'];

                $childrenCondition  = "($pTable.lft > (SELECT lft FROM $pTable WHERE $pkCondition ORDER BY lft ) AND ";
                $childrenCondition .= "$pTable.rgt < (SELECT rgt FROM $pTable WHERE $pkCondition ORDER BY rgt DESC))";
                $condition = array($condition, 'OR', $childrenCondition);

            }

            if ($rule['access'] == 1) {

                if ($conditionObject)
                    $conditionObject[] = 'OR';

                $conditionObject[] = $condition;

                if ($denyList) {
                    $conditionObject[] = 'AND NOT';
                    $conditionObject[] = $denyList;
                }

            }

            if ($rule['access'] != 1) {

                if ($denyList)
                    $denyList[] = 'AND NOT';

                $denyList[] = $condition;

                //$denyList .= ($denyList==''?'':' AND NOT ').$condition;
                //
                if ($rule['sub']) {
                    //$denyList .= ' AND NOT ';
                }
            } else {
                /*
                if (count($allowList) > 0)
                    $allowList[] = 'OR';
                $allowList[] = $condition;
                 */
                //$allowList .= ($allowList==''?'':' OR ').$condition;
            }

        }
        //$result .= ')';
        //
        return $conditionObject;

        return "\n(\n$result\n)\n";

        return array($fields, $condition);

    }

    public static function checkList($pObjectKey, $pTargetType = null, $pTargetId = null,
                                     $pRootHasAccess = false){
        return self::check($pObjectKey, null, null, self::LISTING, $pTargetType, $pTargetId, $pRootHasAccess);
    }

    public static function checkListExact($pObjectKey, $pObjectId, $pTargetType = null, $pTargetId = null,
                                     $pRootHasAccess = false){
        return self::check($pObjectKey, $pObjectId, null, self::LISTING, $pTargetType, $pTargetId, $pRootHasAccess);
    }

    public static function checkUpdate($pObjectKey, $pFields = null, $pTargetType = null, $pTargetId = null,
                                       $pRootHasAccess = false){
        return self::check($pObjectKey, null, $pFields, self::UPDATE, $pTargetType, $pTargetId, $pRootHasAccess);
    }

    public static function checkUpdateExact($pObjectKey, $pObjectId, $pFields = null, $pTargetType = null, $pTargetId = null,
                                       $pRootHasAccess = false){
        return self::check($pObjectKey, $pObjectId, $pFields, self::UPDATE, $pTargetType, $pTargetId, $pRootHasAccess);
    }

    public static function checkAdd($pObjectKey, $pObjectId, $pFields = null, $pTargetType = null, $pTargetId = null,
                                       $pRootHasAccess = false){
        return self::check($pObjectKey, $pObjectId, $pFields, self::ADD, $pTargetType, $pTargetId, $pRootHasAccess);
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

    public static function setObjectList($pObjectKey, $pTargetType, $pTargetId, $pAccess, $pFields = null, $pWithSub = false)
    {
        return self::setObject(self::LISTING, $pObjectKey, self::CONSTRAINT_ALL, null, $pWithSub, $pTargetType, $pTargetId, $pAccess, $pFields);
    }

    public static function setObjectListExact($pObjectKey, $pObjectPk, $pTargetType, $pTargetId, $pAccess, $pFields = null, $pWithSub = false)
    {
        return self::setObject(self::LISTING, $pObjectKey, self::CONSTRAINT_EXACT, $pObjectPk, $pWithSub, $pTargetType, $pTargetId, $pAccess, $pFields);
    }

    public static function setObjectListCondition($pObjectKey, $pCondition, $pTargetType, $pTargetId, $pAccess, $pFields = null, $pWithSub = false)
    {
        return self::setObject(self::LISTING, $pObjectKey, self::CONSTRAINT_CONDITION, $pCondition, $pWithSub, $pTargetType, $pTargetId, $pAccess, $pFields);
    }

    public static function setObjectUpdate($pObjectKey, $pTargetType, $pTargetId, $pAccess, $pFields = null, $pWithSub = false)
    {
        return self::setObject(self::UPDATE, $pObjectKey, self::CONSTRAINT_ALL, null, $pWithSub, $pTargetType, $pTargetId, $pAccess, $pFields);
    }

    public static function setObject($pMode, $pObjectKey, $pConstraintType, $pConstraintCode, $pWithSub = false,
                                     $pTargetType, $pTargetId, $pAccess, $pFields = null){

        $acl = new Acl();

        $acl->setMode($pMode);
        $acl->setTargetType($pTargetType);
        $acl->setTargetId($pTargetId);
        $acl->setSub($pWithSub);
        $acl->setAccess($pAccess);

        if ($pFields)
            $acl->setFields(json_encode($pFields));

        $acl->setObject($pObjectKey);
        $acl->setConstraintCode(is_array($pConstraintCode)?json_encode($pConstraintCode):$pConstraintCode);
        $acl->setConstraintType($pConstraintType);

        $query = new AclQuery();
        $query->select('prio');
        $query->filterByObject($pObjectKey);
        $query->filterByMode($pMode);
        $query->orderByPrio(\Criteria::DESC);
        $highestPrio = $query->findOne();

        $acl->setPrio($highestPrio+1);

        self::$cache[$pObjectKey.'_'.$pMode] = null;

        $acl->save();

        return $acl;
    }

    /**
     * @param $pObjectKey
     * @param $pObjectId
     * @param  bool $pField
     * @param  int  $pMode
     * @param  bool $pRootHasAccess
     * @param  bool $pAsParent
     * @return bool
     */
    public static function check($pObjectKey, $pPk, $pField = false, $pMode = 1,
                                 $pTargetType, $pTargetId,
                                 $pRootHasAccess = false, $pAsParent = false) {

        if (($pTargetId === null && $pTargetType === null) && Kryn::getAdminClient() && Kryn::getAdminClient()->hasSession()) {
            $pTargetId = Kryn::getAdminClient()->getUserId();
            $pTargetType = static::USER;
        } elseif (($pTargetId === null && $pTargetType === null) && Kryn::getClient() && Kryn::getClient()->hasSession()) {
            $pTargetId = Kryn::getClient()->getUserId();
            $pTargetType = static::USER;
        }

        if ($pTargetType === null)
            $pTargetType = self::USER;

        if ($pTargetId == 1) return true;

        //var_dump('type: '.(($pTargetType == self::GROUP)?'group':'user').', id: '.$pTargetId.', mode: '.$pMode);
        $rules =& self::getRules($pObjectKey, $pMode, $pTargetType, $pTargetId);

        if (count($rules) == 0) return false;

        if ($pPk) {
            $pPk = Object::getObjectUrlId($pObjectKey, $pPk);

            if (self::$cache['checkAckl_' . $pObjectKey . '_' . $pPk . '__' . $pField])
                return self::$cache['checkAckl_' . $pObjectKey . '_' . $pPk . '__' . $pField];
        }

        $access = false;

        $currentObjectPk = Object::getObjectUrlId($pObjectKey, $pPk);

        $definition = Object::getDefinition($pObjectKey);
        $fields =& $definition['fields'];

        $not_found = true;
        $parent_acl = $pAsParent;

        $fIsArray = is_array($pField);
        if ($fIsArray) {
            $fCount   = count($pField);

            $fKey   = key($pField);
            $fValue = current($pField);
        }

        $depth = 0;
        while ($not_found) {
            $depth++;

            if ($depth > 50) {
                $not_found = false;
                break;
            }

            foreach ($rules as $acl) {

                if ($parent_acl && $acl['sub'] == 0) continue;

                //print $acl['id'].', '.$acl['code'] .' == '. $currentObjectPk.'<br/>';
                if ($acl['constraint_type'] == 2 && $pPk &&
                    ($objectItem = Object::get($pObjectKey, $currentObjectPk))){
                    if (!Object::satisfy($objectItem, $acl['constraint_code'])) continue;
                }

                if (
                    $acl['constraint_type'] != 1 ||
                    ($currentObjectPk && $acl['constraint_type'] == 1 && $acl['constraint_code'] == $currentObjectPk)
                ){

                    $fieldKey = $pField;

                    if ($pField) {

                        if ($fIsArray && $fCount == 1) {

                            if (is_string($fKey) && is_array($acl['fields'][$fKey])) {
                                //this field has limits

                                if ( ($fieldAcl = $acl['fields'][$fKey]) !== null) {

                                    if (is_array($fieldAcl[0])) {

                                        //complex field rule, $fieldAcl = ([{access: no, condition: [['id', '>', 2], ..]}, {}, ..])

                                        foreach ($fieldAcl as $fRule) {

                                            if ($fields[$fKey]['type'] == 'object') {
                                                $uri = $fields[$fKey]['object'].'/'.$fValue;
                                                $satisfy = Object::satisfyFromUrl($uri, $fRule['condition']);
                                            } else {
                                                $satisfy = Object::satisfy($pField, $fRule['condition']);
                                            }
                                            if ($satisfy) {
                                                return ($fRule['access'] == 1) ? true : false;
                                            }

                                        }

                                        //if no field rules fits, we consider the whole rule
                                        if ($acl['access'] != 2)
                                            return ($acl['access'] == 1) ? true : false;

                                    } else {

                                        //simple field rule $fieldAcl = ({"value1": yes, "value2": no}

                                        if ($fieldAcl[$fValue] !== null) {
                                            return ($fieldAcl[$fValue] == 1) ? true : false;
                                        } else {
                                            //current($pField) is not exactly defined in $fieldAcl, so we set $access to $acl['access']
                                            //
                                            //if access = 2 then wo do not know it, cause 2 means 'inherited', so maybe
                                            //a other rule has more detailed rule
                                            if ($acl['access'] != 2)
                                                return ($acl['access'] == 1) ? true : false;
                                        }
                                    }
                                }
                            } else {
                                //this field has only true or false
                                $fieldKey = $fKey;
                            }
                        }

                        if (!is_array($fieldKey)) {
                            if ($acl['fields'] && ($fieldAcl = $acl['fields'][$fieldKey]) !== null && !is_array($acl['fields'][$fieldKey])) {
                                return ($fieldAcl == 1) ? true : false;
                            } else {
                                //$pField is not exactly defined, so we set $access to $acl['access']
                                //and maybe a rule with the same code has the field defined
                                // if access = 2 then this rule is only for exactly define fields
                                if ($acl['access'] != 2)
                                    return ($acl['access'] == 1) ? true : false;
                            }
                        }
                    } else {
                        return ($acl['access'] == 1) ? true : false;
                    }
                }
            }

            if ($definition['nested'] && $pPk) {
                if (!$currentObjectPk = krynObjects::getParentId($pObjectKey, $currentObjectPk)) {
                    return $pRootHasAccess?true:$access;
                }

                $parent_acl = true;
            } else {
                break;
            }
        }

        if ($pPk)
            self::$cache['checkAckl_' . $pObjectKey . '_' . $pPk . '__' . $pField] = $access;

        return $access;
    }

    /**
     *
     * Returns the acl infos for the specified id
     *
     * @param string  $pObject
     * @param integer $pCode
     *
     * @return array
     * @internal
     */
    public static function &getItem($pObject, $pCode) {

        self::normalizeCode($pCode);
        $acls =& self::getRules($pObject);

        foreach ($acls as $item) {
            $code = str_replace('%', '', $item['code']);
            $t = explode('[', $code);
            $code = $t[0];
            self::normalizeCode($code);
            if ($code == $pCode) {
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

    public static function normalizeCode(&$pCode)
    {
        $pCode = str_replace('//', '/', $pCode);

        if (substr($pCode, 0, 1) != '/')
            $pCode = '/' . $pCode;

        if (substr($pCode, -1) == '/')
            $pCode = substr($pCode, 0, -1);

    }

}

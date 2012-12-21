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

class Permission {


    public static $cache = array();


    public static $acls = array();

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
     * @param int $pMode
     * @param bool $pForce
     * @return mixed
     *
     */
    public static function &getRules($pObjectKey, $pMode = 1, $pForce = false) {

        if (self::$cache[$pObjectKey.'_'.$pMode] && $pForce == false)
            return self::$cache[$pObjectKey.'_'.$pMode];

        $user = Kryn::getAdminClient()->getUser();
        $userId = $user->getId();
        $userGroups = $user->getUserGroups();

        if (count($userGroups) > 0){
            foreach ($userGroups as $group){
                $inGroups[] = $group->getGroupId();
            }
            $inGroups = implode(', ', $inGroups);
        } else {
            $inGroups = '0';
        }

        if (!$inGroups) $inGroups = '0';
        if (!$userId) $userId = '0';

        $pObjectKey = esc($pObjectKey);
        $pMode += 0;

        $query = "
                SELECT constraint_type, constraint_code, mode, access, sub, fields FROM ".pfx."system_acl
                WHERE
                object = '$pObjectKey' AND
                (mode = $pMode OR mode = 0) AND
                (
                    ( target_type = 1 AND target_id IN ($inGroups))
                    OR
                    ( target_type = 2 AND target_id = $userId)
                )
                ORDER BY prio DESC
        ";

        $res = dbExec($query);
        $rules = array();

        while ($rule = dbFetch($res)){
            if ($rule['fields'] && substr($rule['fields'], 0, 1) == '{'){
                $rule['fields'] = json_decode($rule['fields'], true);
            }
            if ($rule['constraint_type'] == 2 && substr($rule['constraint_code'], 0, 1) == '['){
                $rule['constraint_code'] = json_decode($rule['constraint_code'], true);
            }
            $rules[] = $rule;
        }

        dbFree($res);

        self::$cache[$pObjectKey.'_'.$pMode] = $rules;
        return self::$cache[$pObjectKey.'_'.$pMode];
    }


    /**
     * Get a condition object for item listings.
     * 
     * @param  string $pObjectKey
     * @param  string $pTable
     * @return array
     */
    public static function getListingCondition($pObjectKey, $pTable = ''){

        $rules =& self::getRules($pObjectKey, 1);

        if (count($rules) == 0) return false;

        if (self::$cache['sqlList_' . $pObjectKey])
            return self::$cache['sqlList_' . $pObjectKey];

        $condition = '';
        $result = '';

        $primaryList = Object::getPrimaryList($pObjectKey);
        $primaryKey = current($primaryList);

        $isNested = Kryn::$objects[$pObjectKey]['nested'];
        $table = dbQuote(dbTableName(Kryn::$objects[$pObjectKey]['table']));

        $lastBracket = '';

        $allowList = array();
        $denyList  = array();

        $conditionObject = array();

        foreach($rules as $rule){

            if ($rule['constraint_type'] == '1' ){
                //todo, check for multiple primarykey
                $condition = array(dbQuote($primaryKey, $pTable), '=', $rule['constraint_code']);

                //$condition = dbQuote($primaryKey, $pTable) . ' = ' . $rule['constraint_code'];
                /*
                if ($isNested && $rule['sub']){
                    $sCondition = dbQuote($primaryKey) . ' = ' . $rule['constraint_code'];
                    $sub  = "(lft > (SELECT lft FROM $table WHERE $sCondition) AND ";
                    $sub .= "rgt < (SELECT rgt FROM $table WHERE $sCondition))";
                    $condition = "($condition OR $sub)";
                }*/
            }

            if ($rule['constraint_type'] == '2'){
                $condition = $rule['constraint_code'];
                //$condition = dbConditionToSql($rule['constraint_code'], $pTable);
                /*var_dump($condition);
                if ($isNested && $rule['sub']){
                    $sCondition = dbConditionToSql($rule['constraint_code']);
                    $sub  = "(lft > (SELECT lft FROM $table WHERE $sCondition ORDER BY lft ) AND ";
                    $sub .= "rgt < (SELECT rgt FROM $table WHERE $sCondition ORDER BY rgt DESC))";
                    $condition = "($condition OR $sub)";
                }*/
            }

            if ($rule['constraint_type'] == '0')
                $condition = array('1', '=', '1');


            if ($rule['access'] == 1){

                if ($result) $result .= ")\n\nOR\n";

                if ($conditionObject)
                    $conditionObject[] = 'OR';
                
                $conditionObject[] = $condition;

                //$result .= $condition;

                if ($denyList){
                    $conditionObject[] = 'AND NOT';
                    $conditionObject[] = $denyList;
                }

            }

            if ($rule['access'] != 1){

                if ($denyList)
                    $denyList[] = 'AND NOT';
                
                $denyList[] = $condition;

                //$denyList .= ($denyList==''?'':' AND NOT ').$condition;
                //
                if ($rule['sub']){
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

    public static function checkList($pObjectKey, $pObjectId, $pField = false){
        return self::check($pObjectKey, $pObjectId, $pField, 1);
    }

    public static function checkRead($pObjectKey, $pObjectId, $pField = false){
        return self::check($pObjectKey, $pObjectId, $pField, 2);
    }

    public static function checkAdd($pObjectKey, $pParentId, $pField = false){
        return self::check($pObjectKey, $pParentId, $pField, 3, false, true);
    }

    public static function checkUpdate($pObjectKey, $pObjectId, $pField = false){
        return self::check($pObjectKey, $pObjectId, $pField, 3);
    }

    /**
     * @static
     * @param $pObjectKey
     * @param $pObjectId
     * @param bool|string|array $pField
     * @param int $pMode
     * @param bool $pRootHasAccess
     * @return bool
     */
    public static function check($pObjectKey, $pObjectId, $pField = false, $pMode = 1, $pRootHasAccess = false, $pAsParent = false) {

        if (Kryn::getClient()->getUserId() == 1) return true;

        $rules =& self::getRules($pObjectKey, $pMode);

        if (count($rules) == 0) return false;

        if (self::$cache['checkAckl_' . $pObjectKey . '_' . $pObjectId . '__' . $pField])
            return self::$cache['checkAckl_' . $pObjectKey . '_' . $pObjectId . '__' . $pField];

        $access = false;

        $current_code = $pObjectId;

        $definition =& Kryn::$objects[$pObjectKey];
        $fields =& $definition['fields'];


        $not_found = true;
        $parent_acl = $pAsParent;
        $objectItem = false;
        $codes = array();


        $fIsArray = is_array($pField);
        if ($fIsArray){
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

            foreach ($rules as $acl){

                if ($parent_acl && $acl['sub'] == 0) continue;

                //print $acl['id'].', '.$acl['code'] .' == '. $current_code.'<br/>';
                if ($acl['constraint_type'] == 2 &&
                    ($objectItem = Object::get($pObjectKey, $current_code))){
                    if (!Object::satisfy($objectItem, $acl['constraint_code'])) continue;
                }

                if (
                    $acl['constraint_type'] != 1 ||
                    ($acl['constraint_type'] == 1 && $acl['constraint_code'] == $current_code)
                ){

                    $fieldKey = $pField;

                    if ($pField){

                        if ($fIsArray && $fCount == 1){

                            if (is_string($fKey) && is_array($acl['fields'][$fKey])){
                                //this field has limits

                                if ( ($fieldAcl = $acl['fields'][$fKey]) !== null){

                                    if (is_array($fieldAcl[0])){

                                        foreach ($fieldAcl as $fRule){

                                            $uri = $fields[$fKey]['object'].'/'.$fValue;
                                            $satisfy = Object::satisfyFromUri($uri, $fRule['condition']);
                                            if ($satisfy){
                                                return ($fRule['access'] == 1) ? true : false;
                                            }
                                            if ($acl['access'] != 2)
                                                return ($acl['access'] == 1) ? true : false;
                                            //var_dump(array($uri => $satisfy));

                                        }

                                    } else {

                                        if ($fieldAcl[$fValue] !== null){
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

                        if(!is_array($fieldKey)){
                            if ($acl['fields'] && ($fieldAcl = $acl['fields'][$fieldKey]) !== null && !is_array($acl['fields'][$fieldKey])){
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

            if ($definition['nested'] ){
                if (!$current_code = krynObjects::getParentId($pObjectKey, $current_code)){
                    return $pRootHasAccess?true:$access;
                }

                $parent_acl = true;
            } else {
                break;
            }
        }

        self::$cache['checkAckl_' . $pObjectId . '__' . $pField] = $access;

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


    public static function set($pType, $pTargetType, $pTargetId, $pCode, $pActions, $pWithSub) {

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

    public static function remove($pType, $pTargetType, $pTargetId, $pCode) {

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

    public static function normalizeCode(&$pCode) {

        $pCode = str_replace('//', '/', $pCode);

        if (substr($pCode, 0, 1) != '/')
            $pCode = '/' . $pCode;

        if (substr($pCode, -1) == '/')
            $pCode = substr($pCode, 0, -1);

    }

}

?>
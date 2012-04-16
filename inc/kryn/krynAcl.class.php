<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */


class krynAcl {


    public static $cache = array();


    public static $acls = array();


    public static function &getRules($pType, $pForce = false) {
        global $client;

        if (self::$cache[$pType] && $pForce == false)
            return self::$cache[$pType];

        $userRsn = $client->user_rsn;
        $inGroups = $client->user['inGroups'];

        $pType = esc($pType);

        $sql = "
                SELECT code, access, sub, fields FROM %pfx%system_acl
                WHERE
                object = '$pType' AND
                (
                    ( target_type = 1 AND target_rsn IN ($inGroups))
                    OR
                    ( target_type = 2 AND target_rsn = $userRsn)
                )
                ORDER BY code DESC, prio DESC
        ";

        self::$cache[$pType] = dbExfetch($sql, DB_FETCH_ALL);

        foreach (self::$cache[$pType] as &$acl){
            if ($acl['fields'] && substr($acl['fields'], 0, 1) == '{'){
                $acl['fields'] = json_decode($acl['fields'], true);
            }
        }

        return self::$cache[$pType];
    }

    /**
     * @static
     * @param $pObject
     * @param $pCode
     * @param bool $pField
     * @param bool $pRootHasAccess
     * @return bool
     */
    public static function checkAccess($pObject, $pCode, $pField = false, $pRootHasAccess = false) {

        self::normalizeCode($pCode);
        $acls =& self::getRules($pObject);

        if (count($acls) == 0) return true;

        if (self::$cache['checkAckl_' . $pObject . '_' . $pCode . '__' . $pField])
            return self::$cache['checkAckl_' . $pObject . '_' . $pCode . '__' . $pField];


        $access = false;

        $current_code = $pCode;

        $not_found = true;
        $parent_acl = false;

        $codes = array();

        $i = 0;
        while ($not_found) {
            $i++;

            if ($i > 20) {
                $not_found = false;
                break;
            }

            foreach ($acls as $acl){

                //print $acl['rsn'].', '.$acl['code'] .' == '. $current_code.'<br/>';
                if ($acl['code'] == $current_code){

                    if ($parent_acl && $acl['sub'] == 0) continue;

                    $fieldKey = $pField;

                    if ($pField){

                        if (is_array($pField)){

                            if (is_array($acl['fields'][key($pField)])){
                                //this field has limits

                                if ( ($fieldAcl = $acl['fields'][key($pField)]) !== null){
                                    if ($fieldAcl[current($pField)] !== null){
                                        return ($fieldAcl[current($pField)] == 1) ? true : false;
                                    } else {
                                        //current($pField) is not exactly defined in $fieldAcl, so we set $access to $acl['access']
                                        //
                                        //if access = 2 then wo do not know it, cause 2 means 'inherited', so maybe
                                        //a other rule has more detailed rule
                                        if ($acl['access'] != 2)
                                            return ($acl['access'] == 1) ? true : false;
                                    }
                                }
                            } else {
                                //this field has only true or false
                                $fieldKey = key($pField);
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

            if (!$current_code = krynObject::getParentId(krynObject::toUri($pObject, $current_code))){
                if ($pRootHasAccess)
                    $access = true;
                return $access;
            }

            //print "--parent--\n";
            $parent_acl = true;
        }

        self::$cache['checkAckl_' . $pCode . '__' . $pField] = $access;
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


    public static function setAcl($pType, $pTargetType, $pTargetId, $pCode, $pActions, $pWithSub) {

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
            'target_rsn' => $pTargetId,
            'code' => $pCode
        ));

        self::$cache[$pType] = null;

        return $last_id;
    }

    public static function removeAcl($pType, $pTargetType, $pTargetId, $pCode) {

        self::normalizeCode($pCode);

        $pType += 0;
        $pTargetType += $pTargetType;
        $pTargetId += $pTargetId;
        $pCode = esc($pCode);

        dbDelete('system_acl', "1=1 
         AND type = $pType
         AND target_type = $pTargetType
         AND target_rsn = $pTargetId
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
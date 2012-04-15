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

        $pType += 0;

        $sql = "
                SELECT code, access FROM %pfx%system_acl
                WHERE
                type = $pType AND
                (
                    ( target_type = 1 AND target_rsn IN ($inGroups))
                    OR
                    ( target_type = 2 AND target_rsn = $userRsn)
                )
                ORDER BY code DESC, prio DESC
        ";

        self::$cache[$pType] = dbExfetch($sql, DB_FETCH_ALL);

        return self::$cache[$pType];
    }

    /**
     * @static
     * @param $pType
     * @param $pCode
     * @param bool $pAction
     * @param bool $pRootHasAccess
     * @return bool
     */
    public static function checkAccess($pType, $pCode, $pAction = false, $pRootHasAccess = false) {

        self::normalizeCode($pCode);
        $acls =& self::getRules($pType);

        if (count($acls) == 0) return true;

        if (self::$cache['checkAckl_' . $pType . '_' . $pCode . '__' . $pAction])
            return self::$cache['checkAckl_' . $pType . '_' . $pCode . '__' . $pAction];


        $access = false;

        $current_code = $pCode;

        $not_found = true;
        $parent_acl = false;

        $codes = array();

        $i = 0;
        while ($not_found) {
            $i++;

            if ($i > 10) {
                $not_found = false;
                break;
            }

            $acl = self::getItem($pType, $current_code);

            if ($acl && $acl['code']) {

                $code = str_replace(']', '', $acl['code']);
                $t = explode('[', $code);
                $codes = $t[1]?explode(",", $t[1]):array();

                if (!$pAction || in_array($pAction, $codes)) {
                    if (
                        ($parent_acl == false) || //i'am not a parent
                        ($parent_acl == true && strpos($acl['code'], '%') !== false) //i'am a parent
                    ) {
                        $access = ($acl['access'] == 1) ? true : false;
                        $not_found = false; //done
                        continue;
                    }
                }
            }

            if ($current_code == '/') {
                //we are at the top. no parents left
                if ($pRootHasAccess)
                    $access = true;
                $not_found = false; //go out
            }

            //go to parent
            if ($not_found == true && $current_code != '/') {
                //search and set parent
                if (substr($current_code, -1, 1) == '/') {
                    $pos = strrpos(substr($current_code, 0, -1), '/');
                    $current_code = substr($current_code, 0, $pos);
                } else {
                    $pos = strrpos($current_code, '/');
                    $current_code = substr($current_code, 0, $pos + 1);
                }
                if ($current_code == '')
                    $current_code = '/';

                $parent_acl = true;
            }
        }

        self::$cache['checkAckl_' . $pCode . '__' . $pAction] = $access;
        return $access;
    }


    /**
     *
     * Returns the acl infos for the specified id
     *
     * @param string  $pType
     * @param integer $pCode
     *
     * @return array
     * @internal
     */
    public static function &getItem($pType, $pCode) {

        self::normalizeCode($pCode);
        $acls =& self::getRules($pType);

        foreach ($acls as &$item) {
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
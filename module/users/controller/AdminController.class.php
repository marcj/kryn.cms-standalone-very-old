<?php

namespace Users;

use RestService\Server;

class AdminController extends Server {
    
    public function run($pEntryPoint){

        $this->addGetRoute('acl/search', 'getSearch');
        $this->addGetRoute('acl', 'loadAcl');
        $this->addPostRoute('acl', 'saveAcl');

        return parent::run();

    }

    /**
     * Gets all rules from given type and id;
     *
     * @param int $pType
     * @param int $pId
     * @param bool $pAsCount
     * @return array|int
     */
    public function loadAcl($pType, $pId, $pAsCount = false){

        $pType = ($pType == 'user') ? 0:1;

        $where = 'target_type = '.($pType+0);
        $where .= ' AND target_id = '.($pId+0);

        $where .= " ORDER BY prio DESC";

        if (!$pAsCount)
            return dbTableFetchAll('system_acl', $where);
        else
            return dbCount('system_acl', $where);

    }

    /**
     * Saves the given rules.
     *
     * @param int  $pTargetId
     * @param int  $pTargetType
     * @param array $pRules
     * @return bool
     */
    public function saveAcl($pTargetId, $pTargetType, $pRules){

        $pTargetId += 0;
        $pTargetType += 0;


        dbDelete('system_acl', array(
            'target_type' => $pTargetType,
            'target_id' => $pTargetId
        ));

        if (count($pRules) == 0) return true;

        $i = 1;
        foreach ($pRules as $rule){

            unset($rule['id']);
            $rule['prio'] = $i;
            $rule['target_type'] = $pTargetType;
            $rule['target_id'] = $pTargetId;
            dbInsert('system_acl', $rule);
            $i++;
        }

        return true;
    }

    /**
     *
     * @internal
     * @param $pItems
     * @param $pType
     */
    public static function setAclCount(&$pItems, $pType){

        foreach ($pItems as &$item){

            $item['ruleCount'] = self::load($pType, $item['id'], true);

        }

    }

    public static function load($pType, $pId, $pAsCount = false){

        $where = 'target_type = '.($pType+0);
        $where .= ' AND target_id = '.($pId+0);

        $where .= " ORDER BY prio DESC";

        if (!$pAsCount)
            return dbTableFetch( 'system_acl', DB_FETCH_ALL, $where );
        else
            return dbCount( 'system_acl', $where );

    }

    /**
     * Search user and group.
     *
     * @return array array('users' => array, 'groups' => array())
     */
    public function getSearch(){

        $q = getArgv('q', 1);
        $q = str_replace("*", "%", $q);

        $userFilter = array();
        $groupFilter = array();

        if ($q){
            $userFilter = array(
                array('username', 'like', "$q%"), 'OR',
                array('first_name', 'like', "$q%"), 'OR',
                array('last_name', 'like', "$q%"), 'OR',
                array('email', 'like', "$q%"),
            );
            $groupFilter = array(
                array('name', 'like', "$q%")
            );
        }

        $users = \Core\Object::getList('user', $userFilter, array(
            'limit' => 10,
            'fields' => 'id,username, email, groups, first_name, last_name'
        ));

        self::setAclCount($users, 0);

        $groups = \Core\Object::getList('group', $groupFilter, array(
            'fields' => 'name',
            'limit' => 10
        ));

        self::setAclCount($groups, 1);

        json( array(
            'users' => $users,
            'groups' => $groups
        ));
    }

}
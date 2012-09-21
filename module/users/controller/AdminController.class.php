<?php

namespace Users;

use RestService\Server;

class AdminController extends Server {
    
    public function run($pEntryPoint){

        $this->addGetRoute('acl/search', 'getSearch');
        $this->addGetRoute('acl', 'load');

        return parent::run();

    }
    
    public static function load($pType, $pId, $pAsCount = false){

        $where = 'target_type = '.($pType+0);
        $where .= ' AND target_id = '.($pId+0);

        $where .= " ORDER BY prio DESC";

        if (!$pAsCount)
            return dbTableFetchAll('system_acl', $where);
        else
            return dbCount('system_acl', $where);

    }

    public static function setAclCount(&$pItems, $pType){

        foreach ($pItems as &$item){

            $item['ruleCount'] = self::load($pType, $item['id'], true);

        }

    }

    public static function getSearch(){

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
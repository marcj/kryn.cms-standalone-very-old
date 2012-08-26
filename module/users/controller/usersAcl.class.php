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


class usersAcl {
    
    public static function init(){
        switch( getArgv(4) ){
            case 'search':
                return self::search();
            case 'loadTree':
                return self::loadTree();
            case 'load':
                return self::load(getArgv('acl_target_type'), getArgv('acl_target_id'));
            case 'loadDomains':
                return self::loadDomains();
            case 'save':
                return self::save();
            case 'getPageItemInfo':
            	return self::getPageItemInfo();
            default:
                return self::getAcls(getArgv('type')=='user'?2:1, getArgv('id')+0);
        }
    }

    public static function getAcls($pTargetType, $pTargetId){

        $sql = "
                SELECT * FROM %pfx%system_acl
                WHERE ";

        $sql .= "target_type = ".(($pTargetType==1)?1:2);
        $sql .= "AND target_id = $pTargetId ORDER BY prio DESC";

        $items = dbExFetch($sql, -1);

        return $items;
    }
    
    public static function getPageItemInfo(){
    	$code = getArgv('code');
    	$code = str_replace('%', '', $code);
    	
    	
    	$rcode = substr( $code, 1, strlen($code) );
    	$t = explode( '[', $rcode );
    	$id = $t[0]+0;
    	
    	if( substr($code, 0, 1) == 'd' ){
    		//domain

            $domain = dbExfetch('SELECT domain, lang FROM %pfx%system_domains WHERE id = '.$id);
    		$res['title'] = $domain['domain'];
    		$res['path'] = $domain['lang'];
    		
    	} else {
    		//page
    		$page = dbExfetch('SELECT title FROM %pfx%system_page WHERE id = '.$id);
    		$res['title'] = $page['title'];
    		$res['path'] = kryn::pageUrl( $id, false, true );
    		
    	}
    	
    	
    	json($res);
    }
    
    public static function loadDomains(){
    	
    	$lang = getArgv('lang', 2);
    	
    	$domains = dbExfetch("SELECT id, domain FROM %pfx%system_domains WHERE lang = '$lang'", -1);
    	json($domains);
    }

    public static function save(){

        $targetType = getArgv('target_type')+0;
        $targetRsn = getArgv('target_id')+0;

        dbDelete('system_acl', array(
            'target_type' => $targetType,
            'target_id' => $targetRsn
        ));

        $rules = getArgv('rules');
        if (count($rules) == 0) return true;

        $i = 1;
        foreach ($rules as $rule){

            unset($rule['id']);
            $rule['prio'] = $i;
            $rule['target_type'] = $targetType;
            $rule['target_id'] = $targetRsn;
            dbInsert('system_acl', $rule);
            $i++;
        }

        return true;

        //$target_id = getArgv('id')+0;
        //$type = (getArgv('type',1) == 'user')?'users':'groups';

        //$target_type = ($type=='users')?2:1;

        
        //$acl_type = getArgv('acl_type', 2)+0;
        $acl_target_type = getArgv('acl_target_type', 2)+0;
        $acl_target_id = getArgv('acl_target_id', 2)+0;
        
        if( $acl_target_id == 0 ) json(0);
        
        $aclsAdmin = json_decode( getArgv('aclsadmin'), true );
        $aclsPages = json_decode( getArgv('aclspages'), true );
        
        //backend ACLs ( == post 'acls' )
        dbDelete('system_acl', "target_type = $acl_target_type AND target_id = $acl_target_id");
        
        $row = dbExfetch('SELECT MAX(prio) as maxium FROM %pfx%system_acl');
        $prio = $row['maxium']+1+count($aclsAdmin)+count($aclsPages);
        
        if( count($aclsAdmin) ){
            foreach( $aclsAdmin as $code => $access ){
                dbInsert('system_acl', array(
                    'type' => 1,
                    'prio' => $prio,
                    'target_type' => $acl_target_type,
                    'target_id'  => $acl_target_id,
                    'access' => $access,
                    'code' => $code
                ));
                $prio--;
            }
        }
        
        if( count($aclsPages) ){
            foreach( $aclsPages as $code => $access ){
                dbInsert('system_acl', array(
                    'type' => 2,
                    'prio' => $prio,
                    'target_type' => $acl_target_type,
                    'target_id'  => $acl_target_id,
                    'access' => $access,
                    'code' => $code
                ));
                $prio--;
            }
        }

        // todo
        //frontend ACLs( == post 'front' )

        json(1);
    }

    public static function loadTree(){
        $res = array();

        $dbmods = dbTableFetch('system_modules', -1, 'activated = 1');
        foreach( $dbmods as $mod ){
            $res[ $mod['name'] ] = kryn::$configs[$mod['name']];
            $res[ $mod['name'] ]['name'] = $mod['name'];
        }

        json( $res );
    }



    public static function getInfo( $pParentCode, $pLinks ){
        $res = array();
        if( count($pLinks) > 0 ){
            foreach( $pLinks as $key => $link ){
                $code = $pParentCode . '/' . $key;
                if( $link['childs'] ){
                    $res = array_merge( $res, self::getInfo( $code, $link['childs'] ) );
                }
                $link['childs'] = null;
                $res[$code] = $link; 
            }
        }
        return $res;
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

    public static function setAclCount(&$pItems, $pType){

        foreach ($pItems as &$item){

            $item['ruleCount'] = self::load($pType, $item['id'], true);

        }

    }
    
    public static function search(){

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

        $users = krynObjects::getList('user', $userFilter, array(
            'limit' => 10,
            'fields' => 'id,username, email, groups, first_name, last_name'
        ));

        self::setAclCount($users, 0);

        $groups = krynObjects::getList('group', $groupFilter, array(
            'limit' => 10
        ));

        self::setAclCount($groups, 1);

        json( array(
            'users' => $users,
            'groups' => $groups
        ));
    }

}
?>
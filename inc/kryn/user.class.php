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


/**
 * User class
 * 
 * @package Kryn
 * @subpackage Core
 * @author Kryn.labs <info@krynlabs.com>
 * 
 */


define('GUEST', 0);

class user {

    public $user_rsn = 0;
    public $sessionid;
    public $user = array('username' => 'Guest', 'rsn' => 0);
    public $session;
    public $loggedIn = false;
    public $groups;
    
    /**
     * 
     * constructor
     * @internal
     */
    function __construct() {
        global $kryn, $lang;

        # Delete expired sessions

        $this->sessionid = ($_REQUEST['krynsessionid']!='')?esc($_REQUEST['krynsessionid']):esc($_COOKIE['krynsessionid']);

        if($this->sessionid != ""){
            $row = dbExfetch("SELECT * FROM " . pfx . "system_sessions WHERE id = '$this->sessionid'");
            $this->sessionrow = $row;
            if($row['rsn'] > 0){
                $this->user_rsn = $row['user_rsn'];
                $this->session = $row;
            } else if( $row['rsn'] === 0 ) {
                $this->user_rsn = GUEST;
            } else {
                # Session doesn't exist in table. Down to guest.
                $this->user_rsn = GUEST;
                $this->newSession($this->user_rsn);
            }
        } else {
            # Client doenst have a sessionid. Down to guest.
            $this->user_rsn = GUEST;
            $this->newSession($this->user_rsn);
        }

        switch($_REQUEST['user']){
            case 'login':
                $this->login();
                if($this->user_rsn > 0){
                    dbExec( 'UPDATE %pfx%system_user SET logins = '.($this->user['logins']+1).' WHERE rsn = '.$this->user_rsn );
                    $this->newSession($this->user_rsn);
                }
                if( getArgv(1) == 'admin' ){
                    if( $this->user_rsn > 0 ){
                        $this->loadUser();
                        if( !kryn::checkUrlAccess('admin/backend/', $this) ) json(0);
                        $this->newSession($this->user_rsn);
                        
                        klog('authentication', 'Login success');
                        
                        if($this->user_rsn > 0){
                            dbExec( 'UPDATE %pfx%system_user SET lastlogin = '.(time()).' WHERE rsn = '.$this->user_rsn );
                            
                            $this->clearCache();
                        }
                        json(array('user_rsn' => $this->user_rsn, 'sessionid' => $this->sessionid, 
                            'username' => $_REQUEST['username'],  'lastlogin' => $this->user['lastlogin'],
                            'lang' => $this->user['settings']['adminLanguage']));
                    } else {
                        klog('authentication', str_replace("%s", $_REQUEST['username'], "SECURITY Login failed for '%s' to administration"));
                        json(0);
                    }
                }
                if($this->user_rsn > 0){
                    dbExec( 'UPDATE %pfx%system_user SET lastlogin = '.(time()).' WHERE rsn = '.$this->user_rsn );
                }
                
            case 'logout':
                $this->logout();
                if( getArgv(1) == 'admin' ){
                    json(1);
                }
                //header("Location: ".$kryn->cfg['path']);
                break;
            default:
                $this->refresh();
        }

        $this->loadUser();
        if( $this->user_rsn > 0 )
            $this->loggedIn = true;
        $this->user['loggedIn'] = $this->loggedIn;
    }
    
    
    /**
     * returns the current language for this session
     * 
     */
    public function getSessionLanguage(){
        return $this->session['language'] ? $this->session['language'] : 'en';
    }
    
    /**
     * 
     * set the current language for this session
     * 
     * @param string $pLang Example: en, de or fr
     */
    public function setSessionLanguage( $pLang ){
        $pLang = esc( $pLang );
        dbExfetch("UPDATE " . pfx . "system_sessions SET language = '$pLang' WHERE id = '$this->sessionid'");
        $this->session['language'] = $pLang;
    }
    
    /**
     * 
     * @internal
     */
    private function clearCache(){
        $cacheCode = "user_".$this->user_rsn;
        kryn::removePhpCache($cacheCode);
    }

    /**
     * 
     * Returns all groups as array
     * 
     * @param int pUserId The rsn of the system_user table
     * @params bool force to reload the cache
     * @return &array
     */
    public static function &getGroups( $pUserId, $pForceReload = false ){
    	
    	$pUserId += 0;
    	$cacheCode = 'system_users_groups_'.$pUserId;
    	
    	$groups =& cache::get( $cacheCode );
    	$count = count($groups);
    	
    	if( $groups === false || $pForceReload ){ //cache not initialized
    		
    		$newGroups = array();
    		$statement = dbExec(
    		  'SELECT group_rsn FROM %pfx%system_groupaccess
    		  WHERE user_rsn = '.$pUserId );
    		
    		while( $row = dbFetch($statement) ){
    			$newGroups[] = $row['group_rsn'];
    		}
    		
    		cache::set( $cacheCode, $newGroups );
    		$groups =& cache::get( $cacheCode );
    		
    	} else if( is_array($groups) && $count == 0 ) {//initialized but no groups 
    		return false;
    	}
    	
    	return $groups;
    }

    
    /**
     * 
     * Checks if a user is in the given group
     * @param int $pGroup
     * @return bool
     */
    public function isInGroup( $pGroup ){
        foreach( $this->groups as $group ){
            if( $pGroup == $group['group_rsn'] )
                return true;
        }
        return false;
    }
    
    /**
     * 
     * Returns the id of the given username if found
     * @param int $pUsername
     * @return int returns false if not found
     */
    public static function getIdForUsername( $pUsername ){
    
        $userRow = dbExfetch(
            'SELECT rsn FROM %pfx%system_user WHERE
            username = \''.esc($pUsername).'\'', 1);
        
        if( $userRow['rsn'] > 0 )
            return $userRow['rsn'];
            
    	return false;
    }
    
    /**
     * 
     * Search the user by the given username and returns the complete user hash
     * as ref.
     *
     * @params $pUserName The username you want the id.
     * @return &array returns false if not found
     */
    
    public static function &getUserForUsername( $pUsername ){
    	
        $userId = self::getIdForUsername( $pUsername );
        if( !$userId ) return false;
        
        $user =& self::getUser( $userId);
        if( !$user ) return false;
        
        return $user;
    }
    
    /**
     * 
     * Get the user hash as ref
     * 
     * @param int $pUserId The rsn of the system_user table
     * @params bool force to reload the cache
     * @return &array returns false if not found
     */
    public static function &getUser( $pUserId, $pForceReload = false ){
    	global $user;
    	
    	$pUserId += 0;
    	
    	$cacheCode = 'system_users_'.$pUserId;
    	$result =& cache::get( $cacheCode );
    	
    	if( $result == false || $pForceReload ){
            $result = dbExfetch("SELECT * FROM %pfx%system_user WHERE rsn = " . $pUserId, 1);
            
            if( $result['rsn'] <= 0 ) return false;
            
            $result['settings'] = unserialize($result['settings']);
            
            if( $result['settings']['userBg'] == '' )
                $user['settings']['userBg'] = '/admin/images/userBgs/defaultImages/1.jpg';
                
            if( $user->session )
                $result['sessiontime'] = $user->session['time'];

            $result['groups'] =& self::getGroups( $pUserId, $pForceReload );
            
            $result['inGroups'] = '';
            if( count( $result['groups'] ) >  0)
                foreach( $result['groups'] as $group )
                    $result['inGroups'] .= ','.$group['rsn'];

            $result['inGroups'] .= '0';
            
            cache::set( $cacheCode, $result );
        }
        
    	if( $result ) return $result;
    	
        return false;
    }
    
    /**
     * 
     * @internal
     */
    private function loadUser($pRsn = 0){

        if( $pRsn > 0 ) $this->user_rsn = $pRsn;

        $user =& self::getUser( $this->user_rsn );
        
        $this->groups = $user['groups'];
        
        $user['sessionid'] = $this->sessionid;
        $user['session'] = $this->sessionrow;
        $user['ip'] = $_SERVER['REMOTE_ADDR'];
        $this->user = $user;
        tAssign("user", $user);
        
        return $user;
    }
    
    
    /**
     * 
     * @internal
     */
    public function login(){
        global $kryn, $lang;
        
        $query = "SELECT * FROM " . pfx . "system_user
                    WHERE
                        username = '" . esc($_REQUEST['username']) . "'
                        AND passwd = '" . md5($_REQUEST['passwd']) . "'
                        AND rsn > 0";
        if($row = dbExfetch($query)){
            $this->user_rsn = $row['rsn'];
            $row['passwd'] = '';
            $this->user = $row;
        } else {
            $this->user_rsn = 0;
        }
        return $this->user_rsn;
    }
    
    /**
     * 
     * @internal
     */
    public function newSession($pUser_rsn){
        global $kryn;
        srand(microtime()*1000000);
        $id = md5(rand(1,1000000000));
        if( $pUser_rsn > 0 )
            $this->user_rsn = $pUser_rsn;
        
        # if sessionid already in table, delete'm.
        if($this->sessionid != ""){
            dbExec("DELETE FROM " . pfx . "system_sessions WHERE id = '".$this->sessionid."'");
        }
        
        # check if id already exist
        $found = true;
        while($found == true){
            $row = dbExfetch("SELECT * FROM " . pfx . "system_sessions WHERE id = '$id'");
            if($row['rsn'] == ''){
                $found = false;
            } else {
                $id = md5(rand(1,1000000000));
            }
        }


        # Save
        $t = time();
        $ip = $_SERVER['REMOTE_ADDR'];
        $useragent = esc($_SERVER['HTTP_USER_AGENT']);
        $page = esc(kryn::$baseUrl.$_REQUEST['_kurl']);
        
        $session = array(
            'id' => $id, 'user_rsn' => $this->user_rsn, 'time' => $t, 'ip' => $ip, 'page' => $page,
            'useragent' => $useragent
        );
        
        if( $kryn->installedMods['users']['db']['system_sessions']['refreshed'] )
            $session['refreshed'] = 0;
        
        if( $kryn->installedMods['users']['db']['system_sessions']['created'] )
            $session['created'] = time();
        
        $res = dbInsert('system_sessions', $session);
        
        if( !$res ){
            unset($session['refreshed']);
            unset($session['created']);
            dbInsert('system_sessions', $session);
        }
        
        $this->sessionrow = $session;
        $this->sessionid = $id;
        setCookie("krynsessionid", '', time()-3600*24*700, "/"); 
        setCookie("krynsessionid", '', time()-3600*24*700, "/admin");
        setCookie("krynsessionid", '', time()-3600*24*700, "/admin/");
        setCookie("krynsessionid", $id, time()+3600*24*7, "/"); # 7 Days
        return true;
    }
    
    /**
     * 
     * @internal
     */
    public function logout(){
        dbExec("DELETE FROM " . pfx . "system_sessions WHERE id = '".$this->sessionid."'");
        $this->user_rsn = GUEST;
        $this->newSession($this->user_rsn);
    }
    
    
    /**
     * 
     * @internal
     */
    private function refresh(){
        global $cfg, $kryn;
        
        $useragent = esc($_SERVER['HTTP_USER_AGENT']);
        $page = esc(kryn::$baseUrl.$_REQUEST['_kurl']);
        $ip = $_SERVER['REMOTE_ADDR'];
        
        $withRefresh = "";
        if( $kryn->installedMods['users']['db']['system_sessions']['refreshed'] )
            $withRefresh = ", refreshed = refreshed+1";
        
        dbExec("UPDATE " . pfx . "system_sessions SET 
                time = " . time() . ",
                ip = '$ip',
                page = '$page',
                useragent = '$useragent'
                $withRefresh
                
                WHERE id = '$this->sessionid'");
        $time = (time()-60*$cfg['sessiontime']);
        dbExec("DELETE FROM ".pfx."system_sessions WHERE time < ".$time );
    }
    
}

?>
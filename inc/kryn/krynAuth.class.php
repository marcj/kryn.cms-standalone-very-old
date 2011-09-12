<?php

/**
* krynAuth - class to handle the sessions.
*
* @package Kryn
* @subpackage Auth
*/

class krynAuth {

    /**
    * The auth token. (which is basically stored as cookie on the client side)
    */
    public $token = false;
    
    /**
    * Some session informations
    * Modified by set() and get()
    */
    private $session;
    
    /*
    * For backwards compatibility the user_rsn from $this->user['rsn']
    */
    public $user_rsn = 0;
    
    /**
    * Some user informations from the system_user table.
    */
    public $user;

    /**
    * Defines whether set() was called and changed $session and therefore
    * we need at the end of the script a sync to the backend (database/memcache)
    * Idea behind this: We get more speed, when only saving the combined data at the end,
    * instead of saving as far as it has been changed.
    */
    private $needSync = false;

    /**
    * Constructor
    */
    function __construct(){

        tAssign("client", $this);
        $this->token = $this->getToken();
        $this->session = $this->loadSession();

        $this->startSession = $this->session;

        if( !$this->session ){

            //no session found, create new one
            $this->session = $this->newSession();

        } else {
    
            //maybe we wanna check the ip ?
            if( $cfg['session_ipcheck'] == 1 ){
                $ip = $this->get('ip');

                if( $ip != $_SERVER['REMOTE_ADDR'] ){
                    $this->setUser(0); //force down to guest
                }
            }
            
            $this->loadUser( $this->session['user_rsn'] );
            $this->updateSession();
        }
        
        $this->processClient();
        
        if( $cfg['session_autoremove'] == 1 )
            $this->removeExpiredSessions();
    }
    
    /**
    * Updates the time and refreshed-counter of a session.
    */
    public function updateSession(){
        
        $this->set('time', time());
        $this->set('refreshed', $this->get('refreshed')+1);
        
    }
    
    /**
    * Handles the input of the client. Therefore the login/logout arguments.
    */
    public function processClient(){
    
        if( getArgv('user') == 'login' ){
        
            $login = getArgv('username');
            
            if( getArgv('login') )
                $login = getArgv('login');
            
            $user = $this->login( $login, getArgv('passwd') );

            if( !$user ){

                klog('authentication', str_replace("%s", getArgv('username'), "SECURITY Login failed for '%s'"));
                if( getArgv(1) == 'admin' ){
                    json(0);
                }
                
            } else {
    
                $this->setUser( $user['rsn'], false );
                
                $this->user = $user;
                $this->user_rsn = $user['rsn'];
                
                if( getArgv(1) == 'admin' ){

                    $this->syncStore();
                    if( !kryn::checkUrlAccess('admin/backend/', $this) ){
                        json(0);
                    }
                    
                    klog('authentication', 'Successfully login to administration for user '.$this->user['username']);
                    
                    if( $user['rsn'] > 0 ){
                        dbUpdate('system_user', 'rsn = '.$user['rsn'], array('lastlogin' => time()) );
                        $this->clearCache();
                    }
                    json(array('user_rsn' => $this->user_rsn, 'sessionid' => $this->token, 
                        'username' => getArgv('username'),  'lastlogin' => $this->user['lastlogin'],
                        'lang' => $this->user['settings']['adminLanguage']));
                }
                
            }
        }
        
        if( getArgv('user') == 'logout' ){
            $this->logout();
        }
    }
    
    /**
    * Set the current user of the session.
    */
    public function setUser( $pUserRsn, $pLoadUser = true ){

        $this->set( 'user_rsn', $pUserRsn ); //will be saved at shutdown

        if( $pLoadUser )
            $this->loadUser( $pUserRsn );
    }
    
    /**
    * Auth against the internal user table.
    */
    protected function internalLogin( $pLogin, $pPassword ){
        $state = $this->checkCredentialsDatabase( $pLogin, $pPassword );
        return $state;
    }
    
    /**
    * Do the authentication against the defined backend
    */
    public function &login( $pLogin, $pPassword ){
    
        if( $pLogin == 'admin' )
            $state = $this->internalLogin( $pLogin, $pPassword );
        else
            $state = $this->checkCredentials( $pLogin, $pPassword );
    
        if( $state == false ) {
            return false;
        }
        
        //Search user in the system_user table. If not exist, create it
        return $this->getOrCreateUser( $pLogin );
    }
    
    /**
    * Checks whether a valid logins exists in our system_user database.
    *
    */
    public function &getOrCreateUser( $pLogin ){
        global $cfg;
    
        
        if( $this->credentials_row ){
            $user =& $this->credentials_row;
        } else {
            $where = "username = '".esc($pLogin)."'";
            if( !$cfg['auth_class'] || $cfg['auth_class'] == 'kryn' )
                $where .= " AND (auth IS NULL OR auth = 'kryn')";
            else
                $where .= " AND auth = '".$cfg['auth_class']."'";
                
            $user = dbExfetch("
            SELECT rsn FROM %pfx%system_user
            WHERE
                username = '".esc($pLogin)."' AND
                auth = '".$cfg['auth_class']."'",
            1);
        }
        
        if( !$user ){
            $rsn = dbInsert( 'system_user', array('username' => $pLogin, 'auth' => $cfg['auth_class']) );
            $user = dbTableFetch('system_user', 'rsn = '.$rsn, 1);
            $this->firstLogin( $user );
        }

        return $this->getUser( $user['rsn'] );
    }
    
    
    /**
    * User was not found after the authentication in the system_user table. So
    * maybe we want to add this user to a defined group. 
    * Other auth class may want to extract some user/group-informations from
    * the authentication backend.
    */
    public function firstLogin( $pUser ){
        global $cfg;
        
        if( $cfg['auth_default_group'] ){
            dbInsert('system_groupaccess', array(
                'group_rsn' => $cfg['auth_default_group'],
                'user_rsn' => $pUser['rsn']
            ));
        }
        
    }

    /**
     * Clears the cache of the current user.
     * @internal
     */
    private function clearCache(){
        $this->getUser($this->user_rsn, true);
    }
    
    /**
    *
    */
    public function loadUser( $pUserRsn ){
        
        if( $pUserRsn == 0 ){
            $this->user = array(
                'rsn' => 0,
                'username' => 'Guest'
            );
        } else {
            $this->user =& $this->getUser( $pUserRsn );
        }
        
        $this->user_rsn =& $this->user['rsn'];
        
        tAssign("user", $this->user);
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
    	global $cfg;

    	$pUserId += 0;

    	$cacheCode = 'system_users_'.$pUserId;
    	$result =& cache::get( $cacheCode );
    	
    	if( $result == false || $pForceReload ){
            $result = dbExfetch("SELECT * FROM %pfx%system_user WHERE rsn = " . $pUserId, 1);
            
            if( $result['rsn'] <= 0 ) return false;
            
            $result['settings'] = unserialize($result['settings']);
            
            if( $result['settings']['userBg'] == '' )
                $result['settings']['userBg'] = '/admin/images/userBgs/defaultImages/1.jpg';


            $result['groups'] = array();
    		$statement = dbExec(
    		  'SELECT group_rsn FROM %pfx%system_groupaccess
    		  WHERE user_rsn = '.$pUserId );
    		
    		while( $row = dbFetch($statement) ){
    			$result['groups'][] = $row['group_rsn'];
    		}
            
            $result['inGroups'] = '0';
            if( count( $result['groups'] ) >  0)
                foreach( $result['groups'] as $group )
                    $result['inGroups'] .= ','.$group;
            
            cache::set( $cacheCode, $result );
            $result =& cache::get( $cacheCode );
    	
        }
        
    	return $result;
    }

    /**
    * Do the logout mechanism
    */
    public function logout(){
        global $cfg;
        $this->setUser(0);
    }
    
    
    /**
    * Removes all expired sessions.
    * If the user configured no 'session_autoremove', then this method
    * is called through a cronjob. Last method is basically better regarding
    * the performance.
    */
    public function removeExpiredSessions(){
        global $cfg;
        
        $lastTime = time()-$cfg['session_timeout'];
        dbDelete('system_sessions', 'time < '.$lastTime);

    }
    
    /**
    * Sets the language of the current session
    */
    public function setLang( $pLang ){
        if( $this->getLang() != $pLang )
            $this->set( 'language', $pLang );
    }
    
    /**
    * Gets the language of the current session
    */
    public function getLang(){
        return $this->get( 'language' );
    }
    
    /**
    * When the scripts ends, we need to sync the stored data ($this->session, which has been changed with set())
    * to the backend (memcached/database/etc)
    */
    public function syncStore(){
        if( $this->needSync != true ) return;
        global $cfg;

        switch( $cfg['session_storage'] ){
            case 'memcached':
                
                break;
            case 'database':
            default:
                
                $session['user_rsn'] = $this->user['rsn'];
                $session['language'] = $this->session['language'];
                $session['time'] = $this->session['time'];
                $session['refreshed'] = $this->session['refreshed'];
                $session['ip'] = $this->session['ip'];
                
                $sessionExtra = $this->session;
                unset($sessionExtra['language']);
                unset($sessionExtra['time']);
                unset($sessionExtra['refreshed']);
                unset($sessionExtra['ip']);
                
                $session['extra'] = json_encode($sessionExtra);
                
                dbUpdate('system_sessions', "id = '".esc($this->token)."'", $session);
        }
        
    }
    
    /**
    * Gets values of the current session
    */
    public function &get( $pCode ){
        return $this->session[$pCode];
    }
    
    /**
    * Stores additional information into the current session.
    * The system uses following codes, so your should't override it:
    *    language, time, refreshed, ip, user_rsn, page, useragent
    */
    public function set( $pCode, $pValue ){
        if( $this->session[$pCode] == $pValue ) return;

        $this->needSync = true;
        $this->session[$pCode] = $pValue;
    }
    
    /**
    * Creates a new token and session in the backend
    */
    public function newSession(){
        global $cfg;
        
        $session = false;

        for( $i=1; $i <= 25; $i++ ){
            switch( $cfg['session_storage'] ){
                case 'memcached':
                    $session = $this->newSessionMemcached();
                case 'database':
                default:
                    $session = $this->newSessionDatabase();
            }
            if( $session ){
                setCookie("krynsessionid", '', time()-3600*24*700, "/"); 
                setCookie("krynsessionid", '', time()-3600*24*700, "/admin");
                setCookie("krynsessionid", '', time()-3600*24*700, "/admin/");
                setCookie("krynsessionid", $this->token, time()+3600*24*7, "/"); //7 Days
                return $session;
            }
        }
        
        //after 25 tries, we stop and log it.
        klog('session', _l("The system just tried to create a session 25 times, but can't generate a new free session id."));
        return false;
    }
    
    
    /**
    * Creates a new token and session in the memcached-server
    */
    public function newSessionMemcached(){
        global $cfg;
        
        $token = $this->generateSessionId();
        //TODO
        $row = dbExfetch("SELECT rsn FROM %pfx%system_sessions WHERE id = '$token'", 1);
        if( $row['rsn'] > 0 ){
            //another session with this id exists
            return false;
        }
        
        $session = array(
            'id' => $token,
            'user_rsn' => 0,
            'time' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'page' => esc(kryn::$baseUrl.$_REQUEST['_kurl']),
            'useragent' => esc($_SERVER['HTTP_USER_AGENT']),
            'refreshed' => 0
        );
        
        dbInsert('system_sessions', $session);
        $this->token = $token;
        unset($session['id']);
        return $session;
    }
    
    
    /**
    * Creates a new token and session in the database
    */
    public function newSessionDatabase(){
        global $cfg;
        
        $token = $this->generateSessionId();
        $row = dbExfetch("SELECT rsn FROM %pfx%system_sessions WHERE id = '$token'", 1);
        if( $row['rsn'] > 0 ){
            //another session with this id exists
            return false;
        }
        
        $session = array(
            'id' => $token,
            'user_rsn' => 0,
            'time' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'page' => esc(kryn::$baseUrl.$_REQUEST['_kurl']),
            'useragent' => esc($_SERVER['HTTP_USER_AGENT']),
            'refreshed' => 0
        );
        
        dbInsert('system_sessions', $session);
        $this->token = $token;
        unset($session['id']);
        return $session;
    }
    
    /**
    * Generates a new token/session id
    */
    public function generateSessionId(){
        return md5( microtime(true).mt_rand().mt_rand(50,60*100) );
    }

    /**
    * Loads the session based on the given token from the client
    */
    public function loadSession(){
        global $cfg;

        if( !$this->token ) return false;

        switch( $cfg['session_storage'] ){
            case 'memcached':
                return $this->loadSessionMemcached();
            case 'database':
            default:
                return $this->loadSessionDatabase();
        }
        return false;
    }


    /**
    * Loads the session based on the given token from the client in the database
    */
    public function loadSessionDatabase(){
        global $cfg;

        $row = dbExfetch('SELECT * FROM %pfx%system_sessions WHERE id = \''.esc($this->token).'\'', 1);

        if( !$row ) return false;
        if( $row['time']+$cfg['session_timeout'] < time() ){
            dbDelete('system_sessions', 'id = \''.esc($this->token).'\'');
            return false;
        }

        unset($row['rsn']);
        unset($row['created']);
        unset($row['id']);
    
        if( $row['extra'] ){    
            $extra = @json_decode( $row['extra'], true );
            if( is_array() )
                $row = array_merge( $row, $extra );
        }
        
        return $row;
    }
    
    /**
    * Loads the session based on the given token from the client in the memcached server
    */
    public function loadSessionMemcached(){
        //TODO
    }
    
    /**
    * Returns the token from the client
    * @return string
    */
    public function getToken(){
        
        if( $_GET['krynsessionid'] ) return $_GET['krynsessionid'];
        if( $_POST['krynsessionid'] ) return $_POST['krynsessionid'];
        if( $_COOKIE['krynsessionid'] ) return $_COOKIE['krynsessionid'];
        
        return false;
    }

    /**
    * Checks the given credentials.
    * @param $pLogin string
    * @param $pPassword string
    * @return bool
    */
    public function checkCredentials( $pLogin, $pPassword ){
        global $cfg;
        
        return $this->checkCredentialsDatabase( $pLogin, $pPassword );
    }
    
    /**
    * Checks the given credentials in the database
    */
    protected function checkCredentialsDatabase( $pLogin, $pPassword ){

        $login = esc($pLogin);
        $password = md5( $pPassword );

        $row = dbExfetch(
        'SELECT rsn FROM %pfx%system_user WHERE rsn > 0 AND '.
        "username = '$login' AND passwd = '$password'",
        1);
        
        if( $row['rsn'] > 0 ){
            $this->credentials_row = $row;
            return true;
        }
        return false;
    }
}

?>
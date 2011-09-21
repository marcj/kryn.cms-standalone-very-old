<?php

/**
 * krynAuth - class to handle the sessions.
 *
 * @author MArc Schmidt <marc@kryn.org>
 */

class krynAuth {

    /**
    * The auth token. (which is basically stored as cookie on the client side)
    */
    public $token = false;
    
    /**
    * The token id (the name of the cookie on the client side)
    */
    public $tokenid = 'krynsessionid';
    
    
    /**
    * Some session informations
    * Modified by set() and get()
    * The system uses following items, so your should't override it:
    *    language, time, refreshed, ip, user_rsn, page, useragent
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
    * Contains the config. Items: 'session_timeout', 'session_storage', 'auth_class', 'auth_params' => array('<auth_class>' => array())
    */
    private $cfg = array();

    /**
    * Constructor
    */
    function __construct( $pConfig ){
        
        $this->cfg = $pConfig;

        $this->initializeMemcached();

        tAssign("client", $this);
        $this->token = $this->getToken();
        $this->session = $this->loadSession();

        $this->startSession = $this->session;

        if( !$this->session ){

            //no session found, create new one
            $this->session = $this->newSession();

        } else {
    
            //maybe we wanna check the ip ?
            if( $this->cfg['session_ipcheck'] == 1 ){
                $ip = $this->get('ip');

                if( $ip != $_SERVER['REMOTE_ADDR'] ){
                    $this->setUser(0); //force down to guest
                }
            }
            
            $this->loadUser( $this->session['user_rsn'] );
            $this->updateSession();
        }
        
        $this->processClient();
        
        if( $this->cfg['session_autoremove'] == 1 )
            $this->removeExpiredSessions();
    }
    
    /**
    * Initialize memcached object
    */
    public function initializeMemcached(){

        if( $this->cfg['session_storage'] == 'memcached' ){
            
            if( class_exists('Memcache') ){
                
                $this->memcache = new Memcache;
                foreach( $this->cfg['session_storage_memcached_servers'] as $server ){
                    $this->memcache->addServer( $server['ip'], $server['port']+0 );
                }

            } else {
                if( class_exists('Memcached') ){
                    $this->memcached = new Memcached;
                    foreach( $this->cfg['session_storage_memcached_servers'] as $server ){
                        $this->memcached->addServer( $server['ip'], $server['port']+0 );
                    }
                } else {
                    $this->cfg['session_storage'] = 'database';
                    klog('session', 'Can not find Memcache/Memcached extension in php. Fallback to database as session storage.');
                }
            }
            
        }
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

        
        if( $this->credentials_row ){
            $user =& $this->credentials_row;
        } else {
            $where = "username = '".esc($pLogin)."'";
            if( !$this->cfg['auth_class'] || $this->cfg['auth_class'] == 'kryn' )
                $where .= " AND (auth IS NULL OR auth = 'kryn')";
            else
                $where .= " AND auth = '".$this->cfg['auth_class']."'";
                
            $user = dbExfetch("
            SELECT rsn FROM %pfx%system_user
            WHERE
                username = '".esc($pLogin)."' AND
                auth = '".$this->cfg['auth_class']."'",
            1);
        }
        
        if( !$user ){
            $rsn = dbInsert( 'system_user', array('username' => $pLogin, 'auth' => $this->cfg['auth_class']) );
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

        if( $this->cfg['auth_default_group'] ){
            dbInsert('system_groupaccess', array(
                'group_rsn' => $this->cfg['auth_default_group'],
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
        $this->setUser(0);
    }
    
    
    /**
    * Removes all expired sessions.
    * If the user configured no 'session_autoremove', then this method
    * is called through a cronjob. Last method is basically better regarding
    * the performance.
    */
    public function removeExpiredSessions(){
        
        $lastTime = time()-$this->cfg['session_timeout'];
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

        $session['user_rsn'] = $this->user['rsn'];
        
        switch( $this->cfg['session_storage'] ){
            case 'memcached':
                
                $expired = time()+$this->cfg['session_timeout'];
                
                if( $this->memcache )
                    $this->memcache->set( $this->tokenid.'_'.$this->token, $this->session, 0, $expired);
                else if( $this->memcached )
                    $this->memcached->set( $this->tokenid.'_'.$this->token, $this->session, $expired);
                    
                break;
            case 'database':
            default:

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

        $session = false;

        for( $i=1; $i <= 25; $i++ ){
            switch( $this->cfg['session_storage'] ){
                case 'memcached':
                    $session = $this->newSessionMemcached();
                    break;
                case 'database':
                default:
                    $session = $this->newSessionDatabase();
            }
            if( $session ){
                setCookie($this->tokenid, '', time()-3600*24*700, "/"); 
                setCookie($this->tokenid, '', time()-3600*24*700, "/admin");
                setCookie($this->tokenid, '', time()-3600*24*700, "/admin/");
                setCookie($this->tokenid, $this->token, time()+3600*24*7, "/"); //7 Days
                return $session;
            }
        }
        
        //after 25 tries, we stop and log it.
        klog('session', _l("The system just tried to create a session 25 times, but can't generate a new free session id. Maybe the memached server s full or you forgot to setup a cronjob for the garbage collector."));
        return false;
    }
    
    
    /**
    * Creates a new token and session in the memcached-server
    */
    public function newSessionMemcached(){

        $token = $this->generateSessionId();
        
        if( $this->memcache )
            $exist = $this->memcache->get( $this->tokenid.'_'.$token );
        else if( $this->memached )
            $exist = $this->memcached->get( $this->tokenid.'_'.$token );
            
        if( $exist !== false ){
            return false;
        }
        
        $session = array(
            'user_rsn' => 0,
            'time' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'page' => esc(kryn::$baseUrl.$_REQUEST['_kurl']),
            'useragent' => esc($_SERVER['HTTP_USER_AGENT']),
            'refreshed' => 0
        );
        
        $expired = time()+$this->cfg['session_timeout'];
        if( $this->memcache ){
            if( !$this->memcache->set( $this->tokenid.'_'.$token, $session, 0, $expired) )
                return false;
        } else if( $this->memcached )
            if( !$this->memcached->set( $this->tokenid.'_'.$token, $session, $expired) )
                return false;
            
        
        $this->token = $token;
        return $session;
    }
    
    
    /**
    * Creates a new token and session in the database
    */
    public function newSessionDatabase(){

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

        if( !$this->token ) return false;

        switch( $this->cfg['session_storage'] ){
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

        $row = dbExfetch('SELECT * FROM %pfx%system_sessions WHERE id = \''.esc($this->token).'\'', 1);

        if( !$row ) return false;
        if( $row['time']+$this->cfg['session_timeout'] < time() ){
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

        if( $this->memcache )
            $session = $this->memcache->get( $this->tokenid.'_'.$this->token );
        else if( $this->memcached )
            $session = $this->memcached->get( $this->tokenid.'_'.$this->token );

        if( $session && $session['time']+$this->cfg['session_timeout'] < time() ){
            if( $this->memcache )
                $this->memcache->delete( $this->tokenid.'_'.$this->token );
            else if( $this->memcached )
                $this->memcached->delete( $this->tokenid.'_'.$this->token );
            
            return false;
        }

        if( !$session ) return false;

        return $session;
    }
    
    /**
    * Returns the token from the client
    * @return string
    */
    public function getToken(){
        
        if( $_GET[$this->tokenid] ) return $_GET[$this->tokenid];
        if( $_POST[$this->tokenid] ) return $_POST[$this->tokenid];
        if( $_COOKIE[$this->tokenid] ) return $_COOKIE[$this->tokenid];
        
        return false;
    }

    /**
    * Checks the given credentials.
    * @param $pLogin string
    * @param $pPassword string
    * @return bool
    */
    public function checkCredentials( $pLogin, $pPassword ){

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
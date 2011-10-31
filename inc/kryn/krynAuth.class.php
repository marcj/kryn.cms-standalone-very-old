<?php

/**
 * krynAuth - class to handle the sessions and authentication.
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
    public $config = array();
    
    /**
    * Object of krynCache.class
    */
    public $cache;
    


    /**
    * Constructor
    */
    function __construct( $pConfig ){
        
        $this->config = $pConfig;

        if( $pConfig['session_tokenid'] ){
            $this->tokenid = $pConfig['session_tokenid'];
        }

        if( $pConfig['session_storage'] != 'database' ){
            $this->cache = new krynCache( $pConfig['session_storage'], $pConfig['session_storage_config'] );
        }

        tAssign("client", $this);
        
    }
    
    public function start(){

        $this->token = $this->getToken();
        $this->session = $this->loadSession();

        $this->startSession = $this->session;

        if( !$this->session ){

            //no session found, create new one
            $this->session = $this->newSession();

        } else {
    
            //maybe we wanna check the ip ?
            if( $this->config['session_ipcheck'] == 1 ){
                $ip = $this->get('ip');

                if( $ip != $_SERVER['REMOTE_ADDR'] ){
                    $this->setUser(0); //force down to guest
                }
            }
            
            $this->loadUser( $this->session['user_rsn'] );
            $this->updateSession();
        }
        $this->processClient();
        
        if( $this->config['session_autoremove'] == 1 )
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
                
            $passwd = getArgv('passwd')?getArgv('passwd'):getArgv('password');
            
            $user = $this->login( $login, $passwd );

            if( !$user ){

                klog('authentication', str_replace("%s", getArgv('username'), "SECURITY Login failed for '%s'"));
                if( getArgv(1) == 'admin' ){
                    json(0);
                }
                
            } else {

                $this->user = $user;
                $this->user_rsn = $user['rsn'];
                
                if( getArgv(1) == 'admin' ){

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
            $this->syncStore();
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
    * Do the authentication against the defined backend and return the new user if login was sucessful
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
        $user = $this->getOrCreateUser( $pLogin );
        $this->setUser( $user['rsn'] );
        $this->syncStore();
        
        return $user;
    }
    
    /**
    * Checks whether a valid logins exists in our system_user database.
    */
    public function &getOrCreateUser( $pLogin ){

        
        if( $this->credentials_row ){
            //since we have already the data with checkCredentialsDatabase we just use this
            //information instead of fetching it new
            $user =& $this->credentials_row;
        } else {
            $where = "username = '".esc($pLogin)."'";
            if( !$this->config['auth_class'] || $this->config['auth_class'] == 'kryn' )
                $where .= " AND (auth_class IS NULL OR auth_class = 'kryn')";
            else
                $where .= " AND auth_class = '".$this->config['auth_class']."'";
                
            $user = dbExfetch('
            SELECT rsn FROM %pfx%system_user
            WHERE '.$where,
            1);
        }
        
        if( !$user ){
            $rsn = dbInsert( 'system_user', array('username' => $pLogin, 'auth_class' => $this->config['auth_class']) );
            $user = dbTableFetch('system_user', 'rsn = '.$rsn, 1);
            $this->firstLogin( $user );
        }

        return $this->getUser( $user['rsn'] );
    }
    
    
    /**
    * If the user was not found in the system_user table, we've created it and
    * maybe the auth class want to map some groups to this new user.
    * Don't forget to clear the cache after updating.
    *
    * The default of this function searches 'default_group' in the auth_params
    * and maps the user automatically to the defined groups.
    * 'default_groups' => array(
    *    array('login' => 'LoginOrRegex', 'group' => 'group_rsn')
    * );
    * You can perfectly use the following ka.field in your auth properties:
    *
    *    "default_group": {
    *        "label": "Group mapping",
    *        "desc": "Regular expression are possible in the login field. The group will be attached after the first login.",
    *        "type": "array",
    *        "columns": [
    *            {"label": "Login"},
    *            {"label": "Group", "width": "65%"}
    *        ],
    *        "fields": {
    *            "login": {
    *                "type": "text"
    *            },
    *            "group": {
    *                "type": "textlist",
    *                "multi": true,
    *                "store": "admin/backend/stores/groups"
    *            }
    *        }
    *    }
    *
    * @param array $pUser The newly created user (scheme as in system_user table)
    */
    public function firstLogin( $pUser ){

        if( is_array($this->config['default_group']) ){
            foreach( $this->config['default_group'] as $item ){
                
                if( preg_match('/'.$item['login'].'/', $pUser['username'] ) == 1 ){                    
                    dbInsert('system_groupaccess', array(
                        'group_rsn' => $item['group'],
                        'user_rsn' => $pUser['rsn']
                    ));
                    $this->clearCache( $pUser['rsn'] );
                }

            }
        }
        
    }

    /**
     * Clears the cache of the current user.
     * @internal
     */
    private function clearCache( $pUserRsn = false ){
        if( !$pUserRsn ) $this->user_rsn;
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
        
        $this->user_rsn = $this->user['rsn'];
        
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
    	$result =& kryn::getCache( $cacheCode );
    	
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
            
            kryn::setCache( $cacheCode, $result );
            $result =& kryn::getCache( $cacheCode );
    	
        }
        
    	return $result;
    }

    /**
    * Change the user_rsn in the session object. Means: is logged out then
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
        
        $lastTime = time()-$this->config['session_timeout'];
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
    * to the backend.
    */
    public function syncStore(){
        if( $this->needSync != true ) return;

        $session['user_rsn'] = $this->user['rsn'];
        
        if( $this->config['session_storage'] == 'database' ){

            $session['language'] = $this->session['language'];
            $session['time'] = $this->session['time'];
            $session['refreshed'] = $this->session['refreshed'];
            $session['ip'] = $this->session['ip'];
            
            $sessionExtra = $this->session;
            $notInExtra = array('language', 'time', 'refreshed', 'ip', 'user_rsn', 'page', 'useragent', 'extra');
            foreach( $notInExtra as $temp )
                unset($sessionExtra[$temp]);
            
            $session['extra'] = json_encode($sessionExtra);
            
            dbUpdate('system_sessions', "id = '".esc($this->token)."'", $session);
                        
        } else {
            $expired = $this->config['session_timeout'];
            $this->cache->set( $this->tokenid.'_'.$this->token, $this->session, $expired );
        }
        
    }
    
    /**
    * Gets values of the current session
    * @return mixed
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
    * @return array The session object
    */
    public function newSession(){

        $session = false;
        
        for( $i=1; $i <= 25; $i++ ){
            if( $this->config['session_storage'] == 'database' ){
                $session = $this->newSessionDatabase();
            } else {
                $session = $this->newSessionCache();
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
    * Creates a new token and session
    */
    public function newSessionCache(){

        $token = $this->generateSessionId();
        
        $exist = $this->cache->get( $this->tokenid.'_'.$token );
        
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
        
        $expired = $this->config['session_timeout'];
        
        if( !$this->cache->set( $this->tokenid.'_'.$token, $session, $expired) )
            return false;
            
        $this->token = $token;
        return $session;
    }
    
    
    /**
    * Creates a new token and session in the database
    * @return array The session object
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
    * @return string The session id
    */
    public function generateSessionId(){
        return md5( microtime(true).mt_rand().mt_rand(50,60*100) );
    }

    /**
    * Loads the session based on the given token from the client
    *
    * @return array Session object
    */
    public function loadSession(){

        if( !$this->token ) return false;
        
        if( $this->config['session_storage'] == 'database' )
            return $this->loadSessionDatabase();
        else
            return $this->loadSessionCache();
    }


    /**
    * Loads the session based on the given token from the client in the database
    */
    public function loadSessionDatabase(){

        $row = dbExfetch('SELECT * FROM %pfx%system_sessions WHERE id = \''.esc($this->token).'\'', 1);

        if( !$row ) return false;
        if( $row['time']+$this->config['session_timeout'] < time() ){
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
    * Loads the session based on the given token from the client
    */
    public function loadSessionCache(){


        $session = $this->cache->get( $this->tokenid.'_'.$this->token );

        if( $session && $session['time']+$this->config['session_timeout'] < time() ){
            $this->cache->delete( $this->tokenid.'_'.$this->token );
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
        
        $userColumn = (strpos($pLogin, '@') !== false && strpos($pLogin, '.') !== false) ? 'email' : 'username';

        $row = dbExfetch("
            SELECT rsn
            FROM %pfx%system_user
            WHERE 
                    rsn > 0
                AND $userColumn = '$login'
                AND passwd = '$password'
                AND (auth_class IS NULL OR auth_class = 'kryn')",
            1);
        
        if( $row['rsn'] > 0 ){
            $this->credentials_row = $row;
            return true;
        }
        return false;
    }
}

?>
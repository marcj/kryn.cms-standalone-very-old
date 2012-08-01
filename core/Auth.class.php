<?php

/**
 * krynAuth - class to handle the sessions and authentication.
 *
 * @author MArc Schmidt <marc@Kryn.org>
 */

namespace Core;

class Auth {

    /**
     * The auth token. (which is basically stored as cookie on the client side)
     */
    private $token = false;

    /**
     * The token id (the name of the cookie on the client side)
     */
    private $tokenId = 'krynsessionid';


    /**
     * Current session object.
     *
     * @var \Session
     */
    private $session;

    /**
     * Contains the config. Items: 'session_timeout', 'session_storage', 'auth_class', 'auth_params' => array('<auth_class>' => array())
     */
    private $config = array();

    /**
     * Object of krynCache.class
     */
    private $cache;

    /**
     * Defines whether processHandler() is called initially
     * @var bool
     */
    private $autoLoginLogout = false;

    /**
     * The HTTP GET/POST key which triggers the login.
     * admin?users-login=1
     * @var string
     */
    private $loginTrigger = 'users-login';

    /**
     * The HTTP GET/POST key which triggers the logout.
     * admin?users-logout=1
     * @var string
     */
    private $logoutTrigger = 'users-logout';


    /**
     * Increases for each valid request the 'refreshed' value in session values
     *
     * @var bool
     */
    public $refreshing = true;

    /**
     * Constructor
     */
    function __construct($pConfig, $pWithRefreshing = true) {

        $this->config = $pConfig;

        if ($pConfig['session_tokenId']) {
            $this->tokenId = $pConfig['session_tokenId'];
        }

        $this->refreshing = $pWithRefreshing;

        if (!$this->config['session_storage'])
            $this->config['session_storage'] = 'database';

        if (!$this->config['session_timeout'])
            $this->config['session_timeout'] = 3600 * 12;

        if ($pConfig['session_storage'] != 'database') {
            $this->cache = new Cache($pConfig['session_storage'], $pConfig['session_storage_config']);
        }

    }

    public function start() {

        $this->token = $this->getClientToken();
        $this->session = $this->loadSession();

        error_log('sessionid: '.$this->token);

        $this->startSession = $this->session;

        if (!$this->session) {

            //no session found, create new one
            $this->session = $this->newSession();

        } else {

            //maybe we wanna check the ip ?
            if ($this->config['session_ipcheck'] == 1) {
                $ip = $this->get('ip');

                if ($ip != $_SERVER['REMOTE_ADDR']) {
                    $this->setUser(); //force down to guest
                }
            }

            if ($this->refreshing)
                $this->updateSession();

        }

        if ($this->autoLoginLogout)
            $this->handleClientLoginLogout();

        if ($this->config['session_autoremove'] == 1)
            $this->removeExpiredSessions();
    }

    /**
     * Updates the time and refreshed-counter of a session.
     */
    public function updateSession() {

        $this->session->setTime(time());
        $this->session->setRefreshed( $this->session->getRefreshed()+1 );
        $this->session->setPage(Kryn::getRequestedPath(true));

        $this->session->save();
    }

    /**
     * Handles the input (login/logout) of the client.
     */
    public function handleClientLoginLogout() {

        if (getArgv($this->loginTrigger)) {

            $login = getArgv('username');

            if (getArgv('login'))
                $login = getArgv('login');

            $passwd = getArgv('passwd') ? getArgv('passwd') : getArgv('password');

            $userId = $this->login($login, $passwd);

            if (!$userId) {

                klog('authentication', str_replace("%s", getArgv('username'), "SECURITY Login failed for '%s'"));
                if (getArgv(1) == 'admin') {
                    json(0);
                }

            } else {

                if (getArgv(1) == 'admin') {

                    if (!Kryn::checkUrlAccess('admin/backend/', $this)) {
                        json(0);
                    }

                    klog('authentication', 'Successfully login to administration for user ' . $this->getSession()->getUser()->getUsername());

                    if ($userId > 0) {
                        $this->getSession()->getUser()->setLastlogin(time());
                        $this->getSession()->getUser()->save();
                    }
                    json(array('user_id' => $userId, 'sessionid' => $this->token,
                        'username' => getArgv('username'), 'lastlogin' => $this->getSession()->getUser()->getLastlogin()));
                }

            }
        }

        if (getArgv($this->logoutTrigger)) {
            $this->logout();
            $this->syncStore();
            if (getArgv(1) == 'admin') {
                json(true);
            }
        }
    }

    /**
     * Returns the user from current session.
     *
     * @return \User
     */
    public function getUser(){
        return $this->getSession()->getUser();
    }

    /**
     * Auth against the internal user table.
     *
     * @param $pLogin
     * @param $pPassword
     * @return bool
     */
    protected function internalLogin($pLogin, $pPassword) {
        $state = $this->checkCredentialsDatabase($pLogin, $pPassword);
        return $state;
    }


    /**
     * Do the authentication against the defined backend and return the new user if login was successful
     * @param $pLogin
     * @param $pPassword
     * @return bool
     */
    public function &login($pLogin, $pPassword) {

        if ($pLogin == 'admin')
            $state = $this->internalLogin($pLogin, $pPassword);
        else
            $state = $this->checkCredentials($pLogin, $pPassword);

        if ($state == false) {
            return false;
        }

        $this->setUser($state);
        $this->syncStore();

        return true;
    }

    /**
     * Checks whether a valid logins exists in our system_user database.
     * @param $pLogin
     * @return \User
     */
    public function &getOrCreateUser($pLogin) {


        if ($this->credentials_row) {
            //since we have already the data with checkCredentialsDatabase we just use this
            //information instead of fetching it new
            $user =& $this->credentials_row;
        } else {
            $where = "username = '" . esc($pLogin) . "'";
            if (!$this->config['auth_class'] || $this->config['auth_class'] == 'Kryn')
                $where .= " AND (auth_class IS NULL OR auth_class = 'Kryn')";
            else
                $where .= " AND auth_class = '" . $this->config['auth_class'] . "'";

            $user = dbExfetch('
            SELECT id FROM %pfx%system_user
            WHERE ' . $where,
                1);
        }

        if (!$user) {
            $id = dbInsert('system_user', array('username' => $pLogin, 'auth_class' => $this->config['auth_class']));
            $user = dbTableFetch('system_user', 'id = ' . $id, 1);
            $this->firstLogin($user);
        }

        return $this->getUser($user['id']);
    }


    /**
     * If the user was not found in the system_user table, we've created it and
     * maybe the auth class want to map some groups to this new user.
     * Don't forget to clear the cache after updating.
     *
     * The default of this function searches 'default_group' in the auth_params
     * and maps the user automatically to the defined groups.
     * 'default_groups' => array(
     *    array('login' => 'LoginOrRegex', 'group' => 'group_id')
     * );
     * You can perfectly use the following ka.Field in your auth properties:
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
    public function firstLogin($pUser) {

        if (is_array($this->config['default_group'])) {
            foreach ($this->config['default_group'] as $item) {

                if (preg_match('/' . $item['login'] . '/', $pUser['username']) == 1) {
                    dbInsert('system_groupaccess', array(
                        'group_id' => $item['group'],
                        'user_id' => $pUser['id']
                    ));
                }

            }
        }

    }


    /**
     * Setter for current user
     *
     * @param int $pUserId
     *
     * @return Kryn\Auth $this
     * @throws \Exception
     */
    public function setUser($pUserId = null) {

        if ($pUserId){
            $user = \UserQuery::create()->findPk($pUserId);

            if (!$user){
                throw new \Exception('User not found '.$pUserId);
            }

            $this->session->setUser($user);
        } else {
            $this->session->setUserId(null);
        }

        return $this;
    }


    /**
     * Change the user_id in the session object. Means: is logged out then
     */
    public function logout() {
        $this->setUser();
        $this->syncStore();
    }

    /**
     * Removes all expired sessions.
     * If the user configured no 'session_autoremove', then this method
     * is called through a cronjob. Last method is basically better regarding
     * the performance.
     */
    public function removeExpiredSessions() {
        $lastTime = time() - $this->config['session_timeout'];
        dbDelete('system_session', 'time < ' . $lastTime);

    }

    /**
     * When the scripts ends, we need to sync the session data to the backend.
     */
    public function syncStore() {

        if ($this->config['session_storage']) {

            $this->session->save();

        } else {
            $expired = $this->config['session_timeout'];
            $this->cache->set($this->tokenId . '_' . $this->token, $this->session, $expired);

        }

    }

    /**
     * Creates a new token and session in the backend
     * @return array The session object
     */
    public function newSession() {

        $session = false;

        for ($i = 1; $i <= 25; $i++) {

            if ($this->config['session_storage'] == 'database') {
                $session = $this->newSessionDatabase();
            } else {
                $session = $this->newSessionCache();
            }
            if ($session) {
                $this->token = $session->getId();
                setCookie($this->tokenId, $this->token, time() + 3600 * 24 * 7, Kryn::$config['path']); //7 Days
                return $session;
            }
        }

        //after 25 tries, we stop and log it.
        klog('session', t("The system just tried to create a session 25 times, but can't generate a new free session id. Maybe the caching server is full or you forgot to setup a cronjob for the garbage collector."));
        return false;
    }


    /**
     * Creates a new token and session
     */
    public function newSessionCache() {

        $token = $this->generateSessionId();

        if ($this->cache->get($this->tokenId . '_' . $token)) return false;

        $session = new \Session();
        $session->setId($token)
            ->setTime(time())
            ->setIp($_SERVER['REMOTE_ADDR'])
            ->setPage(Kryn::getRequestedPath(true))
            ->setUseragent($_SERVER['HTTP_USER_AGENT'])
            ->setIsStoredInDatabase(false);

        $expired = $this->config['session_timeout'];

        if (!$this->cache->set($this->tokenId . '_' . $token, $session, $expired))
            return false;

        return $session;
    }

    /**
     * Defined whether or not the class should process the client login/logout.
     *
     * @param boolean $pEnabled
     * @return \Auth $this
     */
    public function setAutoLoginLogout($pEnabled){
        $this->autoLoginLogout = $pEnabled;
        return $this;
    }

    public function getToken(){
        return $this->token;
    }

    public function getTokenId(){
        return $this->tokenId;
    }


    public function setToken($pToken){
        $this->token = $pToken;
        return $this;
    }


    public function setTokenId($pTokenId){
        $this->tokenId = $pTokenId;
        return $this;
    }


    /**
     * Creates a new token and session in the database
     * @return array The session object
     */
    public function newSessionDatabase() {

        $token = $this->generateSessionId();

        try {
            $session = new \Session();
            $session->setId($token)
                ->setTime(time())
                ->setIp($_SERVER['REMOTE_ADDR'])
                ->setPage(Kryn::getRequestedPath(true))
                ->setUseragent($_SERVER['HTTP_USER_AGENT']);

            $session->save();

            return $session;
        } catch(Exception $e){
            return false;
        }

    }

    /**
     * Generates a new token/session id
     * @return string The session id
     */
    public function generateSessionId() {
        return md5(microtime(true) . mt_rand() . mt_rand(50, 60 * 100));
    }

    /**
     * Loads the session based on the given token from the client
     *
     * @return array Session object
     */
    public function loadSession() {

        if (!$this->token) return false;

        if ($this->config['session_storage'] == 'database')
            return $this->loadSessionDatabase();
        else
            return $this->loadSessionCache();
    }


    /**
     * Loads the session based on the given token from the client in the database
     */
    public function loadSessionDatabase() {

        $session = \SessionQuery::create()->findPK($this->token);

        if (!$session) return false;

        if ($session->getTime() + $this->config['session_timeout'] < time()) {
            $session->delete();
            return false;
        }

        /*if ($session->getExtra()) {
            $extra = @json_decode($session->getExtra(), true);
            if (is_array($extra))
                $row = array_merge($session->asArray(), $extra);
            $this->session->setExtra($extra);
        }*/

        return $session;
    }

    /**
     * Loads the session based on the given token from the client
     */
    public function loadSessionCache() {

        $session = $this->cache->get($this->tokenId . '_' . $this->token);

        if ($session && $session['time'] + $this->config['session_timeout'] < time()) {
            $this->cache->delete($this->tokenId . '_' . $this->token);
            return false;
        }

        if (!$session) return false;

        return $session;
    }

    /**
     * Returns the token from the client
     * @return string
     */
    public function getClientToken() {

        if ($_GET[$this->tokenId]) return $_GET[$this->tokenId];
        if ($_POST[$this->tokenId]) return $_POST[$this->tokenId];
        if ($_COOKIE[$this->tokenId]) return $_COOKIE[$this->tokenId];

        return false;
    }

    /**
     * Checks the given credentials.
     *
     * @param $pLogin    string
     * @param $pPassword string
     *
     * @return bool
     */
    public function checkCredentials($pLogin, $pPassword) {

        return $this->checkCredentialsDatabase($pLogin, $pPassword);
    }

    /**
     * Checks the given credentials in the database
     *
     * @param $pLogin
     * @param $pPassword
     * @return bool
     */
    protected function checkCredentialsDatabase($pLogin, $pPassword) {

        $login = $pLogin;

        $userColumn = 'username';

        if ($this->config['auth_params']['email_login'] && strpos($pLogin, '@') !== false && strpos($pLogin, '.') !== false)
            $userColumn = 'email';

        $row = dbExfetch("
            SELECT id, passwd, passwd_salt
            FROM %pfx%system_user
            WHERE 
                    id > 0
                AND $userColumn = ?
                AND (auth_class IS NULL OR auth_class = 'kryn')",
            $login);

        if ($row['id'] > 0) {

            if ($row['passwd_salt']) {
                $hash = self::getHashedPassword($pPassword, $row['passwd_salt']);
            } else {
                if (Kryn::$config['passwd_hash_compat'] != 1) return false;
                //compatibility
                $hash = md5($pPassword);
            }

            if ($hash != $row['passwd']) return false;


            return $row['id'];
        }
        return false;
    }

    /**
     *
     * Generates a salt for a hashed password
     */
    public static function getSalt($pLength = 12) {

        $salt = 'a';

        for ($i = 0; $i < $pLength; $i++) {
            $salt[$i] = chr(mt_rand(33, 122));
        }

        return $salt;
    }

    /**
     *
     * Returns a hashed password with salt through some rounds.
     */
    public static function getHashedPassword($pPassword, $pSalt) {

        $hash = md5(($pPassword . $pSalt) . $pSalt);

        for ($i = 0; $i < 5000; $i++) {
            for ($j = 0; $j < 32; $j++) {
                $hash[$j] = chr(ord($hash[$j]) + ord(Kryn::$config['passwd_hash_key'][$j]));
            }
            $hash = md5($hash);
        }

        return $hash;
    }

    /**
     * @param string $loginTrigger
     * @return Auth $this
     */
    public function setLoginTrigger($loginTrigger){
        $this->loginTrigger = $loginTrigger;
        return $this;
    }

    /**
     * @return string
     */
    public function getLoginTrigger(){
        return $this->loginTrigger;
    }

    /**
     * @param string $logoutTrigger
     * @return Kryn\Auth $this
     */
    public function setLogoutTrigger($logoutTrigger){
        $this->logoutTrigger = $logoutTrigger;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogoutTrigger(){
        return $this->logoutTrigger;
    }

    /**
     * @param \Session $session
     */
    public function setSession($session){
        $this->session = $session;
    }

    /**
     * @return \Session
     */
    public function getSession(){
        return $this->session;
    }

    /**
     * @param array $config
     */
    public function setConfig($config){
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig(){
        return $this->config;
    }
}


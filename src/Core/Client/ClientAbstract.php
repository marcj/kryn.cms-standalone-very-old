<?php

/**
 * krynAuth - class to handle the sessions and authentication.
 *
 * @author MArc Schmidt <marc@Kryn.org>
 */

namespace Core\Client;

use Core\Config\Client;
use Core\Config\SessionStorage;
use Core\Event;
use Core\Kryn;
use Core\Utils;
use Users\Models\Session;

/**
 * Client class.
 *
 * Handles authentification and sessions.
 *
 */
abstract class ClientAbstract
{
    /**
     * The auth token. (which is basically stored as cookie on the client side)
     */
    private $token = false;

    /**
     * Token id (cookie id)
     */
    private $tokenId = 'session_id';

    /**
     * Current session instance.
     *
     * @var Session
     */
    private $session;

    /**
     * Contains the config.
     */
    public $config = array(
        'tokenId' => 'session_id',
        'timeout' =>  43200,
        'cookieDomain' => null,
        'cookiePath' => '/',
        'autoLoginLogout' => false,
        'loginTrigger' => 'auth-login',
        'logoutTrigger' => 'auth-logout',
        'loginDelay' => false,
        'ipCheck' => false,
        'garbageCollector' => false
    );

    /**
     * Detects if start() has been called or not.
     *
     * @var bool
     */
    private $started = false;

    /**
     * Instance of Cache class
     *
     * @var \Core\Cache\Controller
     */
    private $store;

    /**
     * @var Client
     */
    private $clientConfig;

    /**
     * Constructor
     *
     * @param Client $clientConfig
     */
    public function __construct(Client $clientConfig)
    {
        $this->clientConfig = $clientConfig;
        $this->config = array_merge($this->config, $clientConfig->getOptions()->toArray());

        if ($this->config['tokenId']) {
            $this->tokenId = $this->config['tokenId'];
        }

        if ('database' !== $clientConfig->getSessionStorage()->getClass()) {
            $this->store = new \Core\Cache\Controller($clientConfig->getSessionStorage());
        }
    }

    public function start()
    {
        Kryn::getLogger()->addDebug(
            'Start session tracking: sessionid=' . $this->getToken() . ' - ' . Kryn::getRequestedPath()
        );
        $this->session = $this->fetchSession();

        if (!$this->session) {

            //no session found, create new one
            $this->session = $this->createSession();

        } else {

            //maybe we wanna check the ip ?
            if ($this->config['ipCheck']) {
                $ip = $this->get('ip');

                if ($ip != $_SERVER['REMOTE_ADDR']) {
                    $this->logout(); //force down to guest
                }
            }

            if ($this->getSession()->getTime() + 5 < time()) //do only all 5 seconds an session update
            {
                $this->updateSession();
            }

        }

        if ($this->config['autoLoginLogout']) {
            $this->handleClientLoginLogout();
        }

        if ($this->config['garbageCollector']) {
            $this->removeExpiredSessions();
        }

        $this->setStarted(true);
    }

    /**
     * @param boolean $started
     */
    public function setStarted($started)
    {
        $this->started = $started;
    }

    /**
     * @return boolean
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * Updates the time and refreshed-counter of a session,
     * and updates the cookie timeout on the client side.
     *
     */
    public function updateSession()
    {
        $this->getSession()->setTime(time());
        $this->getSession()->setRefreshed($this->session->getRefreshed() + 1);
        $this->getSession()->setPage(substr(Kryn::getRequestedPath(true), 0, 255));

        setCookie(
            $this->getTokenId(),
            $this->getToken(),
            time() + $this->config['timeout'],
            $this->config['cookiePath'],
            $this->config['cookieDomain']
        );

    }

    /**
     * Handles the input (login/logout) of the client.
     */
    public function handleClientLoginLogout()
    {
        if (getArgv($this->config['loginTrigger'])) {

            $login = getArgv('username');

            if (getArgv('login')) {
                $login = getArgv('login');
            }

            $passwd = getArgv('passwd') ? getArgv('passwd') : getArgv('password');

            $userId = $this->login($login, $passwd);

            if (!$userId) {
                klog('authentication', str_replace("%s", getArgv('username'), "SECURITY Login failed for '%s'"));
            }
        }

        if (getArgv($this->config['logoutTrigger'])) {
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
     * @return \Users\Models\User
     */
    public function getUser()
    {
        if (!$this->getStarted()) {
            $this->start();
        }
        if (!$this->getSession()->getUserId()) {
            return null;
        }

        if (null === $this->user) {
            $this->user = Kryn::getPropelCacheObject('Users\Models\User', $this->getSession()->getUserId());
        }

        return $this->user;
    }

    /**
     * Returns the user from current session.
     *
     * @return int
     */
    public function getUserId()
    {
        if (!$this->getStarted()) {
            $this->start();
        }
        return $this->getSession()->getUserId();
    }

    /**
     * Auth against the internal user table.
     *
     * @param $login
     * @param $password
     *
     * @return bool
     */
    protected function internalLogin($login, $password)
    {
        $clientConfig = new Client();
        $storage = new SessionStorage();
        $storage->setClass('database');
        $clientConfig->setSessionStorage($storage);

        $krynUsers = new \Core\Client\KrynUsers($clientConfig);

        $state = $krynUsers->checkCredentials($login, $password);

        return $state;
    }

    /**
     * Check credentials and set user_id to the session.
     *
     * @param  string $login
     * @param  string $password
     *
     * @return bool
     */
    public function login($login, $password)
    {
        if (!$this->getStarted()) {
            $this->start();
        }

        if (!$this->config['noDelay']) {
            // sleep(1);
        }

        if ($login == 'admin') {
            $state = $this->internalLogin($login, $password);
        } else {
            $state = $this->checkCredentials($login, $password);
        }

        if ($state == false) {
            return false;
        }

        $this->setUser($state);
        $this->syncStore();

        return true;
    }

    /**
     * If the user has not been found in the system_user table, we've created it and
     * maybe this class want to map some groups to this new user.
     *
     * Don't forget to clear the cache after updating.
     *
     * The default of this function searches 'default_group' in the auth_params
     * and maps the user automatically to the defined groups.
     *
     * 'defaultGroups' => array(
     *    array('login' => 'LoginOrRegex', 'group' => 'group_id')
     * );
     *
     * You can perfectly use the following ka.Field definition in your client properties:
     *
     *    "defaultGroup": {
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
     *
     * @param User $user The newly created user.
     */
    public function firstLogin($user)
    {
        if (is_array($this->config['defaultGroup'])) {
            foreach ($this->config['defaultGroup'] as $item) {

                if (preg_match('/' . $item['login'] . '/', $user['username']) == 1) {
                    dbInsert(
                        'system_user_group',
                        array(
                             'group_id' => $item['group'],
                             'user_id' => $user['id']
                        )
                    );
                }
            }
        }

    }

    /**
     * Setter for current user
     *
     * @param int $userId
     *
     * @return \Core\Client\ClientAbstract $this
     * @throws \Exception
     */
    public function setUser($userId = null)
    {
        if (!$this->getStarted()) {
            $this->start();
        }

        if ($userId !== null) {
            $user = \Users\Models\UserQuery::create()->findPk($userId);

            if (!$user) {
                throw new \Exception('User not found ' . $userId);
            }

            $this->getSession()->setUser($user);
        } else {
            $this->getSession()->setUserId(null);
        }

        return $this;
    }

    /**
     * Change the user_id in the session object to 0. Means: is logged out then
     */
    public function logout()
    {
        if (!$this->getStarted()) {
            $this->start();
        }
        $this->setUser();
    }

    /**
     * Removes all expired sessions.
     *
     */
    public function removeExpiredSessions()
    {
        if ($this->config['store']['class'] == 'database') {
            //todo
        }
    }

    /**
     * When the scripts ends, we need to sync the session data to the backend.
     *
     */
    public function syncStore()
    {
        if (!$this->getStarted()) {
            return;
        }

        $time = microtime(true);
        if ($this->hasSession()) {
            if ('database' === $this->clientConfig->getSessionStorage()->getClass()) {
                $this->getSession()->save();
                Kryn::setPropelCacheObject('\Users\Models\Session', $this->getSession()->getId(), $this->getSession());
            } else {
                $this->store->set(
                    $this->tokenId . '_' . $this->token,
                    $this->session->exportTo('JSON'),
                    $this->config['timeout']
                );
            }
        }
        \Core\Utils::$latency['session'][] = microtime(true) - $time;
    }

    /**
     * Create new session in the backend and stores the newly created session id
     * into $this->token. Also set cookie.
     *
     * @return bool|Session The session object
     */
    public function createSession()
    {
        $time = microtime(true);
        for ($i = 1; $i <= 25; $i++) {
            $session = $this->createSessionById($this->generateSessionId());

            if ($session) {

                if ('database' !== $this->clientConfig->getSessionStorage()->getClass()) {
                    $session->setIsStoredInDatabase(false);
                }

                $this->setToken($session->getId());

                if (php_sapi_name() !== 'cli') {
                    setCookie(
                        $this->tokenId,
                        $this->token,
                        time() + $this->config['timeout'],
                        $this->config['cookiePath'],
                        $this->config['cookieDomain']
                    );
                }

                \Core\Utils::$latency['session'][] = microtime(true) - $time;
                return $session;
            }

        }

        //after 25 tries, we stop and log it.
        trigger_error(
            "The system just tried to create a session 25 times, but can't generate a new free session id.'.
                        'Maybe the caching server is full or you forgot to setup a cronjob for the garbage collector."
        );

    }

    /**
     * Creates a Session object and store it in the current backend.
     *
     * @param $id
     *
     * @return bool|\Users\Models\Session Returns false, if something went wrong otherwise a Session object.
     * @throws \Exception
     */
    public function createSessionById($id)
    {
        $cacheKey = $this->tokenId . '_' . $id;

        //this is a critical section, since between checking whether a session exists
        //and setting the session object, another thread or another server (in the cluster)
        //can write the cache key.
        //So we LOCK all kryn php instances, like in multi-threaded apps, but with all
        //cluster buddies too.
        Utils::appLock('ClientCreateSession');

        //session id already used?
        $session = $this->fetchSession($id);
        if ($session) {
            return false;
        }

        $session = new \Users\Models\Session();
        $session->setId($id)
            ->setTime(time())
            ->setPage(Kryn::getRequestedPath(true))
            ->setRefreshed(0)
            ->setUseragent($_SERVER['HTTP_USER_AGENT']);

        //in some countries it's not allowed to store the IP per default
        if (!$this->config['noIPStorage']) {
            $session->setIp($_SERVER['X-Forwarded-For'] ? : $_SERVER['REMOTE_ADDR']);
        }

        if ('database' === $this->clientConfig->getSessionStorage()->getClass()) {
            try {
                $session->save();
            } catch (\Exception $e) {
                Utils::appRelease('ClientCreateSession');
                throw $e;

                return false;
            }
        } else {
            if (!$this->store->set($cacheKey, $session->exportTo('JSON'), $this->config['timeout'])) {
                Utils::appRelease('ClientCreateSession');

                return false;
            }
        }

        Utils::appRelease('ClientCreateSession');

        return $session;
    }

    /**
     * Defined whether or not the class should process the client login/logout.
     *
     * @param  boolean        $enabled
     *
     * @return ClientAbstract $this
     */
    public function setAutoLoginLogout($enabled)
    {
        $this->config['autoLoginLogout'] = $enabled;

        return $this;
    }

    /**
     * The actual value of the token.
     *
     * @return bool
     */
    public function getToken()
    {
        if (!$this->token) {
            $this->token = $this->getClientToken();
        }

        return $this->token;
    }

    /**
     * The name of the token.
     *
     * @return string
     */
    public function getTokenId()
    {
        return $this->tokenId;
    }

    /**
     * @param $token
     *
     * @events Fires core/client/token-changed($newToken)
     *
     * @return ClientAbstract
     */
    public function setToken($token)
    {
        if ($this->token != $token) {
            $changed = true;
        }

        $this->token = $token;

        if ($changed) {
            Event::fire('core/client/token-changed', $this->token);
        }

        return $this;
    }

    /**
     * @param $tokenId
     *
     * @return ClientAbstract
     */
    public function setTokenId($tokenId)
    {
        $this->tokenId = $tokenId;

        return $this;
    }

    /**
     * Generates a new token/session id.
     *
     * @return string The session id
     */
    public function generateSessionId()
    {
        return md5(microtime(true) . mt_rand() . mt_rand(50, 60 * 100));
    }

    /**
     * Tries to load a session based on current token or pToken from the cache or database backend.
     *
     * @param  string       $token
     *
     * @return bool|Session false if the session does not exist, and Session object, if found.
     */
    protected function fetchSession($token = null)
    {
        $token2 = $this->token;
        if ($token) {
            $token2 = $token;
        }

        if (!$token2) {
            return false;
        }

        $time = microtime(true);

        if ('database' === $this->clientConfig->getSessionStorage()->getClass()) {
            $session = $this->loadSessionDatabase($token2);
        } else {
            $session = $this->loadSessionCache($token2);
        }

        \Core\Utils::$latency['session'][] = microtime(true) - $time;
        return $session;
    }


    /**
     * Tries to load a session based on pToken from the database backend.
     *
     * @param $token
     *
     * @return \BaseObject|bool false if the session does not exist, and Session object, if found.
     */
    protected function loadSessionDatabase($token)
    {
        $session = \Users\Models\SessionQuery::create()->findOneById($token);

        if (!$session) {
            return false;
        }

        if ($session->getTime() + $this->config['timeout'] < time()) {
            Kryn::removePropelCacheObject('\Users\Models\Session', $token);
            $session->delete();

            return false;
        }

        return $session;
    }

    /**
     * Tries to load a session based on current pToken from the cache backend.
     *
     * @param $token
     *
     * @return \Users\Models\Session false if the session does not exist, and Session object, if found.
     */
    public function loadSessionCache($token)
    {
        $cacheKey = $this->tokenId . '_' . $token;
        $sessionData = $this->store->get($cacheKey);
        if (!$sessionData) {
            return;
        }

        $session = new Session;
        $session->importFrom('JSON', $sessionData);

        if (!$session->getId()) {
            return;
        }

        if ($session && $session->getTime() + $this->config['timeout'] < time()) {
            $this->store->delete($cacheKey);

            return;
        }

        return $session;

    }

    /**
     * Returns the token from the client
     *
     * @return string
     */
    public function getClientToken()
    {
        if ($_COOKIE[$this->tokenId]) {
            return $_COOKIE[$this->tokenId];
        }
        if ($_GET[$this->tokenId]) {
            return $_GET[$this->tokenId];
        }
        if ($_POST[$this->tokenId]) {
            return $_POST[$this->tokenId];
        }
        return false;
    }

    /**
     * Checks the given credentials.
     *
     * @param string $pLogin
     * @param string $pPassword
     *
     * @return bool|integer Returns false if credentials are wrong and returns the user id, if credentials are correct.
     */
    abstract public function checkCredentials($pLogin, $pPassword);


    /**
     * Generates a salt for a hashed password
     *
     * @param  int   $pLenth
     *
     * @return tring ascii
     */
    public static function getSalt($length = 64)
    {
        $salt = str_repeat('0', $length);

        for ($i = 0; $i < $length; $i++) {
            $salt[$i] = chr(mt_rand(32, 127));
        }

        return $salt;
    }

    /**
     * Injects the passwd hash from config.php into $string
     *
     * @param  string $string
     *
     * @return binary
     */
    public static function injectConfigPasswdHash($string)
    {
        $result = '';
        $len = mb_strlen($string);
        $clen = mb_strlen(Kryn::$config['passwdHashKey']);

        for ($i = 0; $i < $len; $i++) {
            $s = hexdec(bin2hex(mb_substr($string, $i, 1)));
            $j = $i;
            while ($j > $clen) {
                $j -= $clen + 1;
            } //CR
            $c = hexdec(bin2hex(mb_substr(Kryn::$config['passwdHashKey'], $j, 1)));
            $result .= pack("H*", $s + $c);
        }

        return $result;
    }

    /**
     * Returns a hashed password with salt.
     *
     */
    public static function getHashedPassword($password, $salt)
    {
        $hash = hash('sha512', ($password . $salt) . $salt) . hash(
            'sha512',
            $salt . ($password . $salt . $password)
        );

        for ($i = 0; $i < 201; $i++) {
            $hash = self::injectConfigPasswdHash($hash);
            $hash = hash(
                'sha512',
                $i % 2 ?
                    $hash . $salt . $password . $hash . $salt :
                    $salt . $password . $hash . $password . $hash . $password . $hash
            ) . hash('sha512', $password . $hash . $salt . $password);
        }

        return $hash;
    }

    /**
     * @param  string $loginTrigger
     *
     * @return Auth   $this
     */
    public function setLoginTrigger($loginTrigger)
    {
        $this->config['loginTrigger'] = $loginTrigger;

        return $this;
    }

    /**
     * @return string
     */
    public function getLoginTrigger()
    {
        return $this->config['loginTrigger'];
    }

    /**
     * @param  string                      $logoutTrigger
     *
     * @return \Core\Client\ClientAbstract $this
     */
    public function setLogoutTrigger($logoutTrigger)
    {
        $this->config['logoutTrigger'] = $logoutTrigger;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogoutTrigger()
    {
        return $this->config['logoutTrigger'];
    }

    /**
     * @param Session $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * Returns true if a session has already been loaded or
     * a valid session cookie has been delivered.
     *
     * @return bool
     */
    public function hasSession()
    {
        if (!$this->session && $this->getToken()) {
            $this->session = $this->fetchSession();
        }

        return $this->session instanceof Session;
    }

    /**
     * Returns the session object. If no session exists, we create one.
     *
     * So be carefully: If you just want to check whether a session exists, use
     * hasSession() instead, since otherwise this method here
     * creates a overhead with creating a session id, storing it in the backend and sending a cookie.
     *
     * @return Session
     */
    public function getSession()
    {
        if (!$this->session) {
            $this->session = $this->fetchSession();
        }

        if (!$this->session) {
            $this->session = $this->createSession();
        }

        return $this->session;
    }

    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $store
     */
    public function setStore($store)
    {
        $this->store = $store;
    }

    /**
     * @return \Core\Cache\Controller
     */
    public function getStore()
    {
        return $this->store;
    }

}

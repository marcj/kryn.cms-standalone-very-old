<?php

/**
 * krynAuth - class to handle the sessions and authentication.
 *
 * @author MArc Schmidt <marc@Kryn.org>
 */

namespace Core\Client;

use Core\Kryn;
use Core\Event;
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
     *
     * items:
     *   passwdHashCompat = false,
     *   passwdHashKey = <diggets>
     *   tokenId = "cookieName"
     *   timeout = <seconds> (Default is time()+12*3600)
     *   cookieDomain = '' (default is null)
     *   cookiePath = '' (default is '/')
     *   autoLoginLogout = false
     *   loginTrigger = auth-login
     *   logoutTrigger = auth-logout
     *   noDelay = false
     *   ipCheck = false
     *   garbageCollector = false
     *   store = array(
     *       class  = "\Core\Cache\Files",
     *       config = array(
     *       )
     *   )
     */
    public $config = array();

    /**
     * Detects if start() has been called or not.
     *
     * @var bool
     */
    private $started = false;

    /**
     * Instance of Cache class
     */
    private $store;

    /**
     * Constructor
     *
     * @see $config
     */
    public function __construct($pConfig = array(), $pStore = array())
    {
        $this->config = array_merge($this->config, $pConfig);
        $this->config['store'] = $pStore;

        if (!$this->config['store']['class'])
            $this->config['store']['class'] = '\\Core\\Cache\\Files';

        if ($this->config['tokenId'])
            $this->tokenId = $this->config['tokenId'];

        if (!$this->config['timeout'])
            $this->config['timeout'] = 12*3600;

        if (!$this->config['cookiePath'])
            $this->config['cookiePath'] = '/';

        $this->config['store']['config']['ClientInstance'] = $this;

        if ($this->config['store']['class'] != 'database')
            $this->store = new \Core\Cache\Controller($this->config['store']['class'], $this->config['store']['config']);

    }

    public function start()
    {
        Kryn::getLogger()->addDebug('Start session tracking: sessionid='.$this->getToken().' - '.Kryn::getRequestedPath());
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

            if ($this->getSession()->getTime()+5 < time()) //do only all 5 seconds an session update
                $this->updateSession();

        }

        if ($this->config['autoLoginLogout'])
            $this->handleClientLoginLogout();

        if ($this->config['garbageCollector'] )
            $this->removeExpiredSessions();

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
        $this->getSession()->setRefreshed( $this->session->getRefreshed()+1 );
        $this->getSession()->setPage(Kryn::getRequestedPath(true));

        setCookie($this->getTokenId(), $this->getToken(), time() + $this->config['timeout'],
            $this->config['cookiePath'], $this->config['cookieDomain']);

    }

    /**
     * Handles the input (login/logout) of the client.
     */
    public function handleClientLoginLogout()
    {
        if (getArgv($this->config['loginTrigger'])) {

            $login = getArgv('username');

            if (getArgv('login'))
                $login = getArgv('login');

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
        if (!$this->getStarted()) $this->start();
        if (!$this->getSession()->getUserId()) return null;

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
        if (!$this->getStarted()) $this->start();
        return $this->getSession()->getUserId();
    }

    /**
     * Auth against the internal user table.
     *
     * @param $pLogin
     * @param $pPassword
     * @return bool
     */
    protected function internalLogin($pLogin, $pPassword)
    {
        $krynUsers = new \Core\Client\KrynUsers(array('store' => array('class' => 'database')));

        $state = $krynUsers->checkCredentials($pLogin, $pPassword);

        return $state;
    }

    /**
     * Check credentials and set user_id to the session.
     *
     * @param  string $pLogin
     * @param  string $pPassword
     * @return bool
     */
    public function login($pLogin, $pPassword)
    {
        if (!$this->getStarted()) $this->start();

        if (!$this->config['noDelay']) {
            // sleep(1);
        }

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
     * @param User $pUser The newly created user.
     */
    public function firstLogin($pUser)
    {
        if (is_array($this->config['defaultGroup'])) {
            foreach ($this->config['defaultGroup'] as $item) {

                if (preg_match('/' . $item['login'] . '/', $pUser['username']) == 1) {
                    dbInsert('system_user_group', array(
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
     * @return \Core\Client\ClientAbstract $this
     * @throws \Exception
     */
    public function setUser($pUserId = null)
    {
        if (!$this->getStarted()) $this->start();

        if ($pUserId !== null) {
            $user = \Users\Models\UserQuery::create()->findPk($pUserId);

            if (!$user) {
                throw new \Exception('User not found '.$pUserId);
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
        if (!$this->getStarted()) $this->start();
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
        if (!$this->getStarted()) return;

        $time = microtime(true);
        if ($this->hasSession()) {
            if ($this->config['store']['class'] == 'database') {
                $this->getSession()->save();
                Kryn::setPropelCacheObject('\Users\Models\Session', $this->getSession()->getId(), $this->getSession());
            } else {
                $this->store->set($this->tokenId . '_' . $this->token, $this->session->exportTo('JSON'), $this->config['timeout']);
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

                if ($this->config['store']['class'] != 'database')
                    $session->setIsStoredInDatabase(false);

                $this->setToken($session->getId());

                setCookie($this->tokenId, $this->token, time() + $this->config['timeout'],
                    $this->config['cookiePath'], $this->config['cookieDomain']);

                \Core\Utils::$latency['session'][] = microtime(true) - $time;
                return $session;
            }

        }

        //after 25 tries, we stop and log it.
        trigger_error("The system just tried to create a session 25 times, but can't generate a new free session id.'.
            'Maybe the caching server is full or you forgot to setup a cronjob for the garbage collector.");

    }

    /**
     * Creates a Session object and store it in the current backend.
     * @param $pId
     * @return bool|\Users\Models\Session Returns false, if something went wrong otherwise a Session object.
     * @throws \Exception
     */
    public function createSessionById($pId)
    {
        $cacheKey = $this->tokenId . '_' . $pId;

        //this is a critical section, since between checking whether a session exists
        //and setting the session object, another thread or another server (in the cluster)
        //can write the cache key.
        //So we LOCK all kryn php instances, like in multi-threaded apps, but with all
        //cluster buddies too.
        Utils::appLock('ClientCreateSession');

        //session id already used?
        $session = $this->fetchSession($pId);
        if ($session) return false;

        $session = new \Users\Models\Session();
        $session->setId($pId)
            ->setTime(time())
            ->setPage(Kryn::getRequestedPath(true))
            ->setRefreshed(0)
            ->setUseragent($_SERVER['HTTP_USER_AGENT']);

        //in some countries it's not allowed to store the IP per default
        if (!$this->config['noIPStorage'])
            $session->setIp($_SERVER['X-Forwarded-For'] ?: $_SERVER['REMOTE_ADDR']);

        if ($this->config['store']['class'] == 'database') {
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
     * @param  boolean        $pEnabled
     * @return ClientAbstract $this
     */
    public function setAutoLoginLogout($pEnabled)
    {
        $this->config['autoLoginLogout'] = $pEnabled;

        return $this;
    }

    /**
     * The actual value of the token.
     *
     * @return bool
     */
    public function getToken()
    {
        if (!$this->token)
            $this->token = $this->getClientToken();

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
     * @param $pToken
     *
     * @events Fires core/client/token-changed($newToken)
     *
     * @return ClientAbstract
     */
    public function setToken($pToken)
    {
        if ($this->token != $pToken) $changed = true;

        $this->token = $pToken;

        if ($changed)
            Event::fire('core/client/token-changed', $this->token);

        return $this;
    }

    /**
     * @param $pTokenId
     * @return ClientAbstract
     */
    public function setTokenId($pTokenId)
    {
        $this->tokenId = $pTokenId;

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
     * @param  string       $pToken
     * @return bool|Session false if the session does not exist, and Session object, if found.
     */
    protected function fetchSession($pToken = null)
    {
        $token = $this->token;
        if ($pToken) $token = $pToken;

        if (!$token) return false;

        $time = microtime(true);
        if ($this->config['store']['class'] == 'database') {
            $session =  $this->loadSessionDatabase($token);
        } else {
            $session = $this->loadSessionCache($token);
        }

        \Core\Utils::$latency['session'][] = microtime(true) - $time;
        return $session;
    }


    /**
     * Tries to load a session based on pToken from the database backend.
     *
     * @param $pToken
     * @return \BaseObject|bool false if the session does not exist, and Session object, if found.
     */
    protected function loadSessionDatabase($pToken)
    {
        $session = \Users\Models\SessionQuery::create()->findOneById($pToken);

        if (!$session) return false;

        if ($session->getTime() + $this->config['timeout'] < time()) {
            Kryn::removePropelCacheObject('\Users\Models\Session', $pToken);
            $session->delete();

            return false;
        }

        return $session;
    }

    /**
     * Tries to load a session based on current pToken from the cache backend.
     * @param $pToken
     * @return \Users\Models\Session false if the session does not exist, and Session object, if found.
     */
    public function loadSessionCache($pToken)
    {
        $cacheKey = $this->tokenId.'_'.$pToken;
        $sessionData = $this->store->get($cacheKey);
        if (!$sessionData) return;

        $session = new Session;
        $session->importFrom('JSON', $sessionData);

        if (!$session->getId()) return;

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
        if ($_COOKIE[$this->tokenId]) return $_COOKIE[$this->tokenId];
        if ($_GET[$this->tokenId]) return $_GET[$this->tokenId];
        if ($_POST[$this->tokenId]) return $_POST[$this->tokenId];
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
     * @return tring ascii
     */
    public static function getSalt($pLength = 64)
    {
        $salt = str_repeat('0', $pLength);

        for ($i = 0; $i < $pLength; $i++) {
            $salt[$i] = chr(mt_rand(32, 127));
        }

        return $salt;
    }

    /**
     * Injects the passwd hash from config.php into $pString
     *
     * @param  string $pString
     * @return binary
     */
    public static function injectConfigPasswdHash($pString)
    {
        $result = '';
        $len  = mb_strlen($pString);
        $clen = mb_strlen(Kryn::$config['passwdHashKey']);

        for ($i = 0; $i < $len; $i++) {
            $s = hexdec(bin2hex(mb_substr($pString, $i, 1)));
            $j = $i;
            while ($j > $clen) $j -= $clen+1; //CR
            $c = hexdec(bin2hex(mb_substr(Kryn::$config['passwdHashKey'], $j, 1)));
            $result .= pack("H*", $s+$c);
        }

        return $result;
    }

    /**
     * Returns a hashed password with salt.
     *
     */
    public static function getHashedPassword($pPassword, $pSalt)
    {
        $hash = hash('sha512', ($pPassword . $pSalt) . $pSalt).hash('sha512', $pSalt.($pPassword . $pSalt.$pPassword));

        for ($i = 0; $i < 501; $i++) {
            $hash = self::injectConfigPasswdHash($hash);
            $hash = hash('sha512', $i%2 ? $hash.$pSalt.$pPassword.$hash.$pSalt:$pSalt.$pPassword.$hash.$pPassword.$hash.$pPassword.$hash).
                    hash('sha512', $pPassword.$hash.$pSalt.$pPassword);
        }

        return $hash;
    }

    /**
     * @param  string $loginTrigger
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
        if (!$this->session && $this->getToken())
            $this->session = $this->fetchSession();

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
        if (!$this->session)
            $this->session = $this->fetchSession();

        if (!$this->session)
            $this->session = $this->createSession();

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

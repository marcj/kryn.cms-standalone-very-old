<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@Kryn.org>
 *
 * To get the full copyright and license information, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

Namespace Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Kryn.core class
 * @author MArc Schmidt <marc@Kryn.org>
 */

class Kryn {

    /**
     * Contains all parent pages, which can be shown as a breadcrumb navigation.
     * This is filled automatically. If you want to add own items, use Kryn::addMenu( $pName, $pUrl );
     * @type: array
     * @internal
     * @static
     */
    public static $breadcrumbs;

    /**
     * Contains all translations as key -> value pair.
     * @var array
     * @static
     * @internal
     */
    public static $lang;

    /**
     * Contains the current language code.
     * Example: 'de', 'en'
     * @var string
     * @static
     * @internal
     */
    public static $language;

    /**
     * Contains all used system language.
     * Example: 'de', 'en'
     * @var string
     * @static
     * @internal
     */
    public static $languages;

    /**
     * Defines the current baseUrl (also use in html <header>)
     * @var string
     * @static
     */
    public static $baseUrl;

    /**
     * Contains the current domain with all information (as defined in the database system_domain)
     * @var Domain
     *
     * @static
     */
    public static $domain;

    /**
     * Contains the current page with all information
     * @var Node
     * @static
     */
    public static $page;

    /**
     * Contains the current page with all information as copy for staging in \Render.
     * 
     * @var array ref
     * @static
     */
    public static $current_page;

    /**
     * State where describes, \Render should really write content
     * 
     * @var boolean
     * @static
     */
    public static $forceKrynContent;

    /**
     * Contains the complete builded HTML.
     * To change this, you can change it on the destructor in your extension-class.
     * @var string
     * @static
     */
    public static $pageHtml;

    /**
     * Contains the current requested URL without http://, urlencoded
     * use urldecode(htmlspecialchars(Kryn::$url)) to display it safely in your page.
     * 
     * @var string
     */
    public static $url;

    /**
     * Contains the current requested URL without http:// and with _GET params, urlencoded
     * use urldecode(htmlspecialchars(Kryn::$urlWithGet)) to display it safely in your page.
     * 
     * @var string
     * @static
     */
    public static $urlWithGet;

    /**
     * Contains the values of the properties from current theme.
     * 
     * @var array
     * @static
     */
    public static $currentTheme = array();

    /**
     * Contains the values of the properties from current theme.
     * @var array
     * @static
     */
    public static $themeProperties = array();

    /**
     * Contains the values of the domain-properties from the current domain.
     * @var array
     * 
     * @static
     */
    public static $domainProperties = array();

    /**
     * Contains the values of the page-properties from the current page.
     * @var array
     * @static
     */
    public static $pageProperties = array();

    /**
     * Defines whether force-ssl is enabled or not.
     * @var bool
     * @static
     * @internal
     */
    public static $ssl = false;

    /**
     * Contains the current port
     * @static
     * @var integer
     * @internal
     */
    public static $port = 0;

    /**
     * Contains all object definitions based on the extension configs.
     *
     * @var array
     * @static
     */
    //public static $objects = array();

    /**
     * Contains the current slot information.
     * Items: index, maxItems, isFirst, isLast
     * @var array
     */
    public static $slot;

    /**
     * Contains all current contents
     * Example:
     * $contents = array (
     *      'slotId1' => array(
     *       array(type => 'text', 'content' => 'Hello World')
     *    ),
     *      'slotId2' => array(
     *       array(type => 'text', 'content' => 'Hello World in other slot')
     *    )
     * )
     * @var array
     * @static
     */
    public static $contents;

    /**
     * Defines whether we are at the startpage
     * @var bool
     * @static
     */
    public static $isStartpage;

    /**
     * Contains all config.json as object from all activated extension.
     * Only available in the administration area.
     * @var array
     * @static
     */
    public static $configs;

    /**
     * Contains all extension class instances of all installed extensions
     *
     * @var array
     * @static
     */
    public static $modules;

    /**
     * Contains all installed extensions
     * Example: array('core', 'admin', 'users', 'sitemap', 'publication');
     * @var array
     * @static
     */
    public static $extensions = array('core', 'admin', 'users');

    /**
     * Contains all installed themes
     * @static
     * @var array
     */
    public static $themes;

    /**
     * Contains the current propel pdo connection instance.
     * @var PDO
     * @static
     */
    public static $dbConnection;

    /**
     * Contains the master/slave state of the current db connection.
     *
     * @var bool
     */
    public static $dbConnectionIsSlave = \Propel::CONNECTION_WRITE;


    /**
     * Current Smarty template object
     *
     * @var Smarty
     */
    public static $smarty = array();

    /**
     * Contains the system config (config.php).
     * @var array
     * @static
     */
    public static $config;

    /**
     * Ref to Kryn::$config for compatibility
     * @var array
     * @static
     */
    public static $cfg;

    /**
     * The Auth user object of the backend user.
     * @var Auth
     * @static
     */
    private static $adminClient;

    /**
     * The krynAuth object of the frontend user.
     * It's empty when we're in the backend.
     *
     * @var krynAuth
     * @static
     */
    private static $client;

    /**
     * Contains all page objects of each Render::renderPage() call.
     * For example {page id=<id>} calls this function.
     *
     * @var array
     */
    public static $nestedLevels = array();

    /**
     * @internal
     * @static
     * @var string
     */
    public static $unsearchableBegin = '<!--unsearchable-begin-->';

    /**
     * @internal
     * @static
     * @var string
     */
    public static $unsearchableEnd = '<!--unsearchable-end-->';

    /**
     * Contains full relative URL to the url of the current page.
     * Example: /my/path/to/page
     * @var string
     * @static
     * @internal
     */
    public static $pageUrl = '';

    /**
     * Contains the full absolute (canonical) URL to the current content.
     * Example: http://domain.com/my/path/to/page
     * @var string
     * @internal
     * @static
     */
    public static $canonical = '';


    /**
     * Defines whether the content check before sending the html to the client is activate or not.
     * @var bool
     * @static
     * @internal
     */
    public static $disableSearchEngine = false;

    /**
     * Contains the Kryn\Cache object
     *
     * @var Cache
     * @static
     */
    public static $cache;

    /**
     * Contains the Kryn\Cache object for file caching
     * See Kryn::setFastCache for more informations.
     *
     * @static
     * @var Cache
     */
    public static $cacheFast;

    /**
     * Defines whether we are in the administration area or not.
     * Equal to getArgv(1)=='admin'
     *
     * @var boolean
     * @static
     */
    public static $admin = false;

    /**
     * Cached object of the current domains's urls to id, id to url, alias to id
     * @static
     * @var array
     */
    public static $urls;

    /**
     * Cached path of temp folder.
     * @var string
     */
    private static $cachedTempFolder = '';

    /**
     * The event dispatcher object.
     *
     * @var EventDispatcher
     */
    private static $eventDispatcher;

    /**
     * The ControllerResolver object.
     *
     * @var ControllerResolver
     */
    private static $controllerResolver;

    /**
     * The HttpKernel object.
     *
     * @var HttpKernel
     */
    private static $httpKernel;

    /**
     * The Request object.
     *
     * @var Request
     */
    private static $request;

    /**
     * The page response object.
     *
     * @var PageResponse
     */
    private static $response;


    /**
     * The monolog logger object.
     *
     * @var \Monolog\Logger
     */
    private static $monolog;

    /**
     * The response object. Here you can add
     *   - css files
     *   - javascript files
     *   - cookies
     *   - http headers
     *   - add plain javascript/css
     *
     * or modify the content in the 'core.send-page' event.
     *
     * @return PageResponse
     */
    public static function getResponse(){
        if (!self::$response){
            self::$response = new PageResponse();
        }
        return self::$response;
    }

    public static function getRequest(){
        if (!self::$request){
            self::$request = HttpRequest::createFromGlobals();
        }
        return self::$request;
    }

    /**
     * Returns the logger object.
     *
     * @return \Monolog\Logger
     */
    public static function getLogger(){
        if (!self::$monolog){
            self::$monolog = new \Monolog\Logger('kryn');

            //todo, setup via config
            self::$monolog->pushProcessor(function($log){
                global $_start;
                static $lastDebugPoint;

                $timeUsed = round((microtime(true)-$_start)*1000, 2);
                $bytes = convertSize(memory_get_usage(true));
                $last = $lastDebugPoint ? 'diff '.round((microtime(true)-$lastDebugPoint)*1000, 2).'ms' : '';
                $lastDebugPoint = microtime(true);

                $log['message'] = "[$bytes, {$timeUsed}ms, $last] - ".$log['message'];
                return $log;
            });
            self::$monolog->pushHandler(new \Monolog\Handler\SyslogHandler('kryn'));
        }
        return self::$monolog;
    }

    /**
     * Returns the http kernel. This initialise it if necessary.
     *
     * @return HttpKernel
     */
    public static function getHttpKernel(){
        if (!self::$httpKernel){
            self::$controllerResolver = new ControllerResolver();
            self::$httpKernel = new HttpKernel(self::getEventDispatcher(), self::$controllerResolver);
        }
        return self::$httpKernel;
    }

    /**
     * Returns the event dispatcher. This initialise it if necessary.
     *
     * @return EventDispatcher
     */
    public static function getEventDispatcher(){
        if (!self::$eventDispatcher){
            self::$eventDispatcher = new EventDispatcher();
        }
        return self::$eventDispatcher;
    }

    /**
     * Adds a new crumb to the breadcrumb array.
     *
     * @param \Page $pPage
     *
     * @static
     */
    public static function addBreadcrumb($pPage) {

        Kryn::$breadcrumbs[] = $pPage;
        tAssignRef("breadcrumbs", Kryn::$breadcrumbs);
    }

    /**
     * Sets the doctype in the Kryn\Render class
     * Possible doctypes are:
     * 'html 4.01 strict', 'html 4.01 transitional', 'html 4.01 frameset',
     * 'xhtml 1.0 strict', 'xhtml 1.0 transitional', 'xhtml 1.0 frameset',
     * 'xhtml 1.1 dtd', 'html5'
     * If you want to add a own doctype, you have to extend the static var:
     *     Kryn\Render::$docTypeMap['<id>'] = '<fullDocType>';
     * The default is 'xhtml 1.0 transitional'
     * Can also be called through the smarty function {setDocType value='html 4.01 strict'}
     * @static
     *
     * @param string $pDocType
     */
    public static function setDocType($pDocType) {
        Render::$docType = $pDocType;
    }

    /**
     * Returns the current defined doctype
     * @static
     * @return string Doctype
     */
    public static function getDocType() {
        return Render::$docType;
    }


    /**
     * Get some information about the system Kryn was installed and Kryn itself
     * @static
     */
    public static function getDebugInformation() {

        $infos = array();

        foreach (Kryn::$extensions as $extension) {
            $config = Kryn::getModuleConfig($extension, 'en');
            $infos['extensions'][$extension] = array(
                'version' => $config['version']
            );
        }

        $infos['phpversion'] = phpversion();

        $infos['database_type'] = Kryn::$config['db_type'];

        $infos['config'] = Kryn::$config;

        unset($infos['config']['db_passwd']);
        unset($infos['config']['db_server']);

        return $infos;
    }

    public static function loadActiveModules($pWithoutDefaults = false) {
        Kryn::$extensions = $pWithoutDefaults ? array() : array('core', 'admin', 'users');
        if (Kryn::$config['activeModules'])
            Kryn::$extensions = array_merge(Kryn::$extensions, Kryn::$config['activeModules']);
    }

    /**
     * Loads all activated extension configs and tables
     * 
     * @internal
     */
    public static function loadModuleConfigs() {

        $md5 = '';

        foreach (Kryn::$extensions as $extension) {
            $path = ($extension == 'core') ? 'core/config.json' : PATH_MODULE . $extension . '/config.json';
            if (file_exists($path)) {
                $md5 .= '.' . filemtime($path);
            }
        }

        $md5 = md5($md5);
        Kryn::$themes =& Kryn::getFastCache('core/themes');
        Kryn::$configs =& Kryn::getFastCache('core/configs');

        if ((!Kryn::$themes || $md5 != Kryn::$themes['__md5']) ||
            (!Kryn::$configs || $md5 != Kryn::$configs['__md5'])) {

            foreach (Kryn::$extensions as $extension) {
                Kryn::$configs[$extension] = Kryn::getModuleConfig($extension, false, true);
            }

            foreach (Kryn::$configs as $extension => $config) {

                if (is_array($config['extendConfig'])) {
                    foreach ($config['extendConfig'] as $extendModule => &$extendConfig) {
                        if (Kryn::$configs[$extendModule]) {
                            Kryn::$configs[$extendModule] =
                                array_merge_recursive_distinct(Kryn::$configs[$extendModule], $extendConfig);
                        }
                    }
                }
            }
            Kryn::$configs['__md5'] = $md5;
            Kryn::setFastCache('core/configs', Kryn::$configs);
        }

        /*
         * load themes
         */
        if (!Kryn::$themes || $md5 != Kryn::$themes['__md5']) {

            Kryn::$themes = array();
            Kryn::$themes['__md5'] = $md5;

            foreach (Kryn::$extensions as &$extension) {

                $config = Kryn::$configs[$extension];
                if ($config['themes'])
                    Kryn::$themes[$extension] = $config['themes'];
            }
            Kryn::setFastCache('core/themes', Kryn::$themes);
        }
        unset(Kryn::$themes['__md5']);

    }


    /**
     * Returns the current language for the client
     * based on the domain or current session language (in administration)
     *
     * @static
     * @return array|string
     */
    public static function getLanguage() {

        if (Kryn::$domain && Kryn::$domain->getLang()) {
            return Kryn::$domain->getLang();
        } else if ( getArgv(1) == 'admin' && getArgv('lang', 2)) {
            return getArgv('lang', 2);
        } else if (Kryn::getAdminClient() && Kryn::getAdminClient()->hasSession()) {
            return Kryn::getAdminClient()->getSession()->getLanguage();
        }
        return 'en';

    }


    /**
     * Returns the config hash of the specified extension.
     *
     * @static
     * @param $pModule
     * @param bool $pLang
     * @param bool $pNoCache
     *
     * @return array All config values from the config.json
     */
    public static function getModuleConfig($pModule, $pLang = false, $pNoCache = false ) {

        $pModule = str_replace('.', '', $pModule);
        $pModule = self::getModuleDir($pModule);

        $config = $pModule."config.json";

        if (!file_exists($config)) {
            return false;
        }

        $mtime = filemtime($config);
        $lang = $pLang ? $pLang : Kryn::getLanguage();

        if (!$pNoCache) {

            $cacheCode = 'moduleConfig-' . $pModule . '.' . $lang;
            $configObj = Kryn::getFastCache($cacheCode);

        }

        if (!$configObj || $configObj['mtime'] != $mtime) {

            $json = Kryn::translate(SystemFile::getContent($config));

            $configObj = json_decode($json, 1);

            if (!is_array($configObj)) {
                $configObj = array('_corruptConfig' => true);
            } else {
                $configObj['mtime'] = $mtime;
            }
            if (!$pNoCache) {
                Kryn::setFastCache($cacheCode, $configObj);
            }
        }

        return $configObj;
    }

    /**
     * Load and initialise all activated extension classes.
     * @internal
     */
    public static function initModules() {

        Kryn::$modules['users'] = new \Users\Controller();

        foreach (Kryn::$extensions as $mod) {
            if ($mod != 'core' && $mod != 'admin' && $mod != 'users') {
                $clazz = '\\'.ucfirst($mod).'\\Controller';
                if (class_exists($clazz))
                    Kryn::$modules[$mod] = new $mod();
            }
        }
    }

    /**
     * Checks whether a module is active/enabled or not.
     *
     * @param string $pModuleKey
     * @return bool
     */
    public static function isActiveModule($pModuleKey){
        $pModuleKey = strtolower($pModuleKey);
        return $pModuleKey=='admin' || $pModuleKey == 'users' || $pModuleKey == 'core' ||
            array_search($pModuleKey, self::$config['activeModules']) !== false;
    }

    /**
     * Convert a string to a mod-rewrite compatible string.
     *
     * @param string $pString
     *
     * @return string
     * @static
     */
    public static function toModRewrite($pString) {
        $res = @str_replace('ä', "ae", strtolower($pString));
        $res = @str_replace('ö', "oe", $res);
        $res = @str_replace('ü', "ue", $res);
        $res = @str_replace('ß', "ss", $res);
        $res = @preg_replace('/[^a-zA-Z0-9]/', "-", $res);
        $res = @preg_replace('/--+/', '-', $res);
        return $res;
    }

    /**
     * Replaces all page links within the builded HTML to their full URL.
     *
     * @param string $pContent The content of this variable will be modified.
     *
     * @static
     * @internal
     */
    public static function replacePageIds(&$pContent) {
        $pContent = preg_replace_callback(
            '/href="(\d+)"/',
            create_function(
                '$pP',
                '
                return \'href="\'.Core\Kryn::pageUrl($pP[1]).\'"\';
            '
            ),
            $pContent
        );
    }

    /**
     * Translates all string which are surrounded with [[ and ]].
     *
     * @param string $pContent
     *
     * @static
     * @internal
     * @return mixed The result of preg_replace_callback()
     */
    public static function translate($pContent) {
        Kryn::loadLanguage();
        return preg_replace_callback(
            '/([^\\\\]?)\[\[([^\]]*)\]\]/',
            create_function(
                '$pP',
                '
                return $pP[1].t( $pP[2] );
                '
            ),
            $pContent
        );
    }

    /**
     * Redirect the user to specified URL within the system.
     * Relative to the baseUrl.
     *
     * @param string $pUrl
     *
     * @static
     */
    public static function redirect($pUrl = '') {

        if (strpos($pUrl, 'http') === false && Kryn::$domain) {

            if (Kryn::$domain->getMaster() != 1)
                $pUrl = Kryn::$domain->getLang() . '/' . $pUrl;

            $domain = Kryn::$domain->getDomain();
            $path = Kryn::$domain->getPath();

            if (substr($domain, 0, -1) != '/')
                $domain .= '/';
            if ($path != '' && substr($path, 0, 1) == '/')
                $path = substr($path, 1);
            if ($path != '' && substr($path, 0, -1) == '/')
                $path .= '/';
            if ($pUrl != '' && substr($path, 0, 1) == '/')
                $pUrl = substr($pUrl, 1);

            if ($pUrl == '/')
                $pUrl = '';

            $pUrl = 'http://' . $domain . $path . $pUrl;
        }


        header("HTTP/1.1 301 Moved Permanently");
        header('Location: ' . $pUrl);
        exit;
    }

    /**
     * Initialize config.
     * @internal
     */
    public static function initConfig() {

        if (!self::$config['cache'])
            self::$config['cache']['class'] = '\Core\Cache\Files';

        if (self::$config['id'] === null)
            self::$config['id'] = 'kryn-no-id';
    }

    /**
     * Init cache and cacheFast instances.
     * 
     */
    public static function initCache() {

        //global normal cache
        Kryn::$cache = new Cache\Controller(self::$config['cache']['class'], self::$config['cache_params']);

        $fastestCacheClass = Cache\Controller::getFastestCacheClass();
        Kryn::$cacheFast   = new Cache\Controller($fastestCacheClass);
    }

    /**
     * Init admin and frontend client.
     */
    public static function initClient() {

        if (!self::$config['client'])
            self::internalError('Client configuration', 'There is no client handling class configured. Please run the installer.');

        $defaultClientClass = Kryn::$config['client']['class'];
        $defaultClientConfig = Kryn::$config['client']['config'];
        $defaultClientStore = Kryn::$config['client']['config']['store'];
        $defaultAutoStart = Kryn::$config['client']['autoStart'];

        if (self::$admin) {

            self::$client = self::$adminClient = new $defaultClientClass($defaultClientConfig, $defaultClientStore);

            self::getAdminClient()->start();
        }

        if (!Kryn::isAdmin()) {

            $sessionProperties = self::getDomain() ? self::getDomain()->getSessionProperties() : array();

            $frontClientClass = $defaultClientClass;
            $frontClientConfig = $defaultClientConfig;
            $frontendClientStore = $defaultClientStore;
            $frontendAutoStart = $defaultAutoStart;

            if ($sessionProperties['class']){
                $frontClientClass = $sessionProperties['class'];
                $frontClientConfig = $sessionProperties['config'];
            }

            if ($sessionProperties['autoStart'])
                $frontendAutoStart = $sessionProperties['autoStart'];

            if ($sessionProperties['store'])
                $frontendClientStore = $frontClientConfig['store'];

            self::$client = new $frontClientClass($frontClientConfig, $frontendClientStore);

            if ($frontendAutoStart)
                self::$client->start();

        }

        //TODO, if session language differs from laoded language, do loadLanguage(newLang)
    }

    /**
     * Returns current client instance.
     * If we are in the administration area, then this return the admin client (same as getAdminClient())
     * 
     * @return \Core\Client\ClientAbstract
     */
    public static function getClient(){
        return self::$client;
    }

    /**
     * Returns current admin client instance.
     * Only available if the call is in admin area. (Kryn::isAdmin())
     * 
     * @return \Core\Client\ClientAbstract
     */
    public static function getAdminClient(){
        return self::$adminClient;
    }

    /**
     * Redirect the user to specified page
     *
     * @param integer $pId
     * @param string $pParams
     *
     * @static
     */
    public static function redirectToPage($pId, $pParams = '') {
        self::redirect(self::pageUrl($pId) . ($pParams ? '?' . $pParams : ''));
    }


    /**
     * Returns the requested path in the URL, without http and domain name.
     *
     * @static
     * @return string
     * @internal
     */
    public static function getRequestedPath() {

        return self::getRequest()->getRequestUri();

    }

    /**
     * Check whether specified pLang is a valid language
     *
     * @param string $pLang
     *
     * @return bool
     * @internal
     */
    public static function isValidLanguage($pLang) {

        if (!Kryn::$config['languages'] && $pLang == 'en') return true; //default

        if (Kryn::$config['languages'])
            return array_search($pLang, Kryn::$config['languages']) !== true;
        else return $pLang == 'en';
    }

    /**
     * Clears the language caches.
     *
     * @internal
     * @param null $pLang
     * @return bool
     */
    public static function clearLanguageCache($pLang = null) {
        if ($pLang == false) {

            $langs = dbTableFetch('system_langs', DB_FETCH_ALL, 'visible = 1');
            foreach ($langs as $lang) {
                Kryn::clearLanguageCache($lang['code']);
            }
            return false;
        }
        $code = 'cacheLang_' . $pLang;
        Kryn::setFastCache($code, false);
    }

    /**
     * Load all translations of the specified language
     *
     * @static
     * @internal
     * @param string $pLang
     * @param bool $pForce
     */
    public static function loadLanguage($pLang = null, $pForce = false) {

        if (!$pLang) $pLang = Kryn::getLanguage();

        if (!Kryn::isValidLanguage($pLang))
            $pLang = 'en';

        if( Kryn::$lang && Kryn::$lang['__lang'] && Kryn::$lang['__lang'] == $pLang && $pForce == false )
            return;

        if (!$pLang) return;

        $code = 'cacheLang_' . $pLang;
        Kryn::$lang =& Kryn::getFastCache($code);

        $md5 = '';
        //<div
        foreach (Kryn::$extensions as $key) {
            if ($key == 'core')
                $md5 .= @filemtime(PATH_CORE.'lang/' . $pLang . '.po');
            else
                $md5 .= @filemtime(PATH_MODULE . $key . '/lang/' . $pLang . '.po');
        }

        $md5 = md5($md5);

        if ((!Kryn::$lang || count(Kryn::$lang) == 0) || Kryn::$lang['__md5'] != $md5) {

            Kryn::$lang = array('__md5' => $md5, '__plural' => Lang::getPluralForm($pLang), '__lang' => $pLang);

            foreach (Kryn::$extensions as $key) {

                $po = Lang::getLanguage($key, $pLang);
                Kryn::$lang = array_merge(Kryn::$lang, $po['translations']);

            }
            Kryn::setFastCache($code, Kryn::$lang);
        }

        if (!TempFile::exists('core_gettext_plural_fn_' . $pLang . '.php') ||
            !MediaFile::exists('cache/core_gettext_plural_fn_' . $pLang . '.js')) {

            //write gettext_plural_fn_<langKey> so that we dont need to use eval()
            $pos = strpos(Kryn::$lang['__plural'], 'plural=');
            $pluralForm = substr(Kryn::$lang['__plural'], $pos + 7);

            $code = "<?php \nfunction gettext_plural_fn_$pLang(\$n){\n";
            $code .= "    return " . str_replace('n', '$n', $pluralForm) . ";\n";
            $code .= "}\n?>";
            TempFile::setContent('core_gettext_plural_fn_' . $pLang . '.php', $code);


            $code = "function gettext_plural_fn_$pLang(n){\n";
            $code .= "    return " . $pluralForm . ";\n";
            $code .= "}";
            MediaFile::setContent('cache/core_gettext_plural_fn_' . $pLang . '.js', $code);

        }

        include_once(self::getTempFolder().'core_gettext_plural_fn_' . $pLang . '.php');
    }


    /**
     * Cleans up the class variables. Is used in the test suite.
     */
    public static function cleanup(){

        self::$breadcrumbs = null;
        self::$lang = null;
        self::$language = null;
        self::$languages = null;
        self::$baseUrl = null;
        self::$domain = null;
        self::$page = null;
        self::$current_page = null;
        self::$forceKrynContent = null;
        self::$pageHtml = null;
        self::$url = null;
        self::$urlWithGet = null;
        self::$currentTheme = array();
        self::$themeProperties = array();
        self::$domainProperties = array();
        self::$pageProperties = array();
        self::$ssl = false;
        self::$port = 0;
        //self::$objects = array();
        self::$slot = null;
        self::$contents = null;
        self::$isStartpage = null;
        self::$configs = null;
        self::$modules = null;
        self::$extensions = array('core', 'admin', 'users');
        self::$themes = null;
        self::$dbConnection = null;
        self::$dbConnectionIsSlave = \Propel::CONNECTION_WRITE;
        self::$smarty = array();
        self::$config = null;
        self::$cfg = null;
        self::$adminClient = null;
        self::$client = null;
        self::$nestedLevels = array();
        self::$unsearchableBegin = '<!--unsearchable-begin-->';
        self::$unsearchableEnd = '<!--unsearchable-end-->';
        self::$pageUrl = '';
        self::$canonical = '';
        self::$disableSearchEngine = false;
        self::$cache = null;
        self::$cacheFast = null;
        self::$admin = false;
        self::$urls = null;
        //self::$cachedTempFolder = '';
    }

    /**
     * Returns Domain object
     *
     * @param int $pDomainId If not defined, it returns the current domain.
     *
     * @return \Domain 
     * @static
     */
    public static function getDomain($pDomainId = null) {

        if (!$pDomainId) return self::$domain;

        if ($domainSerialized = self::getCache('core/object-domain/'.$pDomainId)){
            return unserialize($domainSerialized);
        }

        $domain = DomainQuery::create()->findPk($pDomainId);

        if (!$domain){
            return false;
        }


        //todo, do it via setFastCache
        self::setCache('core/object-domain/'.$pDomainId, serialize($domain));

        return $domain;
    }


    /**
     * Returns cached propel object.
     * 
     * @param  int   $pObjectClassName If not defined, it returns the current page.
     * @param  mixed $pObjectPk        Propel PK for $pObjectClassName int, string or array
     * @return \BaseObject Propel object
     * @static
     */
    public static function getPropelCacheObject($pObjectClassName, $pObjectPk) {

        if (is_array($pObjectPk)){
            $npk = '';
            foreach ($pObjectPk as $k){
                $npk .= urlencode($k).'_';
            }
        } else {
            $pk = urlencode($pObjectPk);
        }

        $cacheKey = 'core/object-caching.'.strtolower(preg_replace('/[^\w]/', '.',$pObjectClassName)).'/'.$pk;
        if ($serialized = self::getCache($cacheKey)){
            return unserialize($serialized);
        }

        return self::setPropelCacheObject($pObjectClassName, $pObjectPk);
    } 

    /**
     * Returns propel object and cache it.
     *
     * @param  int   $pObjectClassName If not defined, it returns the current page.
     * @param  mixed $pObjectPk        Propel PK for $pObjectClassName int, string or array
     * @param  mixed $pObject          Pass the object, if you did already fetch it.
     *
     * @return \BaseObject Propel object
     */
    public static function setPropelCacheObject($pObjectClassName, $pObjectPk, $pObject = false) {

        $pk = $pObjectPk;
        if ($pk === null && $pObject){
            $pk = $pObject->getPrimaryKey();
        }

        if (is_array($pk)){
            $npk = '';
            foreach ($pk as $k){
                $npk .= urlencode($k).'_';
            }
        } else {
            $pk = urlencode($pk);
        }

        $cacheKey = 'core/object-caching.'.strtolower(preg_replace('/[^\w]/', '.',$pObjectClassName)).'/'.$pk;

        $clazz = $pObjectClassName.'Query';
        $object = $pObject;
        if (!$object)
            $object = $clazz::create()->findPk($pObjectPk);

        if (!$object){
            return false;
        }

        self::setCache($cacheKey, serialize($object));

        return $object;

    }

    /**
     * Removes a object from the cache.
     *
     * @param  int   $pObjectClassName If not defined, it returns the current page.
     * @param  mixed $pObjectPk        Propel PK for $pObjectClassName int, string or array
     */
    public static function removePropelCacheObject($pObjectClassName, $pObjectPk = null){

        $pk = $pObjectPk;
        if ($pk !== null){
            if (is_array($pk)){
                $npk = '';
                foreach ($pk as $k){
                    $npk .= urlencode($k).'_';
                }
            } else {
                $pk = urlencode($pk);
            }
        }
        $cacheKey = 'core/object-caching.'.strtolower(preg_replace('/[^\w]/', '.',$pObjectClassName));

        if ($pObjectPk){
            self::deleteCache($cacheKey.'/'.$pk);
        } else {
            self::invalidateCache($cacheKey);
        }
    }



    /**
     * Returns a super fast cached Page object.
     * 
     * @param  int $pPageId If not defined, it returns the current page.
     * @return \Page
     * @static
     */
    public static function getPage($pPageId = null) {

        if (!$pPageId) return self::$page;

        $created = self::getCache('core/object.page.'.$pPageId.'.created');
        $data    = self::getFastCache('core/object.page.'.$pPageId);

        if ($data && $created == $data['!created']){
            return unserialize($data['page']);
        }

        $page = NodeQuery::create()->findPk($pPageId);

        if (!$page){
            return false;
        }

        $data['page'] = serialize($page);
        $data['!created'] = microtime();
        self::setFastCache('core/object.page.'.$pPageId, $data);
        self::setCache('core/object.page.'.$pPageId.'.created', $data['!created']);

        return $page;

    }

    /**
     * Reads the requested URL and try to extract the requested language.
     * @return string Empty string if nothing found.
     * @internal
     */
    public static function getPossibleLanguage() {

        $uri = self::getRequest()->getRequestUri();

        if (strpos($uri, '/') > 0)
            $first = substr($uri, 0, strpos($uri, '/'));
        else
            $first = $uri;

        if (self::isValidLanguage($first)) {
            return $first;
        }

        return '';
    }


    /**
     * Tries to detect the domain/host name, if they is available in this system.
     *
     * @static
     * @param bool $pNoRefreshCache
     * @return Domain|null
     *
     * @event core.domain-redirect
     * @event core.domain-not-found
     */
    public static function detectDomain($pNoRefreshCache = false){

        $request    = self::getRequest();
        $dispatcher = self::getEventDispatcher();
        $hostname   = $request->get('kryn_domain') ?: $request->getHost();

        $possibleLanguage     = self::getPossibleLanguage();
        $hostnameWithLanguage = $hostname . '/' . $possibleLanguage;

        $cachedDomains        = self::getFastCache('core/domains');
        $cachedDomainsCreated = self::getCache('core/domains.created'); //for loadBalanced scenarios
        if ($cachedDomains) $cachedDomains = \unserialize($cachedDomains);

        if (!$cachedDomains || $cachedDomains['!created'] != $cachedDomainsCreated){

            $cachedDomains = array();
            $domains = DomainQuery::create()->find();
            foreach ($domains as $domain){
                $key = $domain->getDomain();
                $langKey = '';

                if (!$domain->getMaster()){
                    $langKey = '/'.$domain->getLanguage();
                }

                $cachedDomains[$key.$langKey] = $domain;

                if ($domain->getRedirect()){
                    $redirects = $domain->getRedirect();
                    $redirects = explode(',', str_replace(' ', '', $redirects));
                    foreach ($redirects as $redirectDomain){
                        $cachedDomains['!redirects'][$redirectDomain.$langKey] = $key.$langKey;
                    }
                }

                if ($domain->getAlias()){
                    $aliases = $domain->getAlias();
                    $aliases = explode(',', str_replace(' ', '', $aliases));
                    foreach ($aliases as $aliasDomain){
                        $cachedDomains['!aliases'][$aliasDomain.$langKey] = $key.$langKey;
                    }
                }
            }

            $created = microtime();
            $cachedDomains['!created'] = $created;
            self::setFastCache('core/domains', \serialize($cachedDomains));
            self::setCache('core/domains.created', $created);

        }
        //search redirect
        if ($redirectToDomain = $cachedDomains['!redirects'][$hostnameWithLanguage] ||
            $redirectToDomain = $cachedDomains['!redirects'][$hostname]
        ){
            $domain = $cachedDomains[$redirectToDomain];

            $dispatcher->dispatch('core.domain-redirect', new GenericEvent($domain));
            return null;
        }

        //search alias
        if (($aliasHostname = $cachedDomains['!aliases'][$hostnameWithLanguage]) ||
            ($aliasHostname = $cachedDomains['!aliases'][$hostname])
        ){
            $domain = $cachedDomains[$aliasHostname];
            $hostname = $aliasHostname;
        } else {
            $domain = $cachedDomains[$hostname];
        }

        if (!$domain){
            $dispatcher->dispatch('core.domain-not-found', new GenericEvent($hostname));
            return;
        }

        $domain->setRealDomain($hostname);

        return $domain;
    }

    /**
     * Setups the HTTPKernel.
     */
    public static function setupHttpKernel(){

        $dispatcher = self::getEventDispatcher();
        $request    = self::getRequest();

        $dispatcher->addListener(KernelEvents::EXCEPTION, function(GetResponseForExceptionEvent $event){
            Utils::exceptionHandler($event->getException());
        });

    }

    /**
     * Attaches the page and their plugin routes as routeCollection to the the event dispatcher
     * for later usage in HTTPKernel.
     */
    public static function setupPageRoutes(){

        self::$domain = self::detectDomain();
        if (!self::$domain) return;
        $dispatcher   = self::getEventDispatcher();

        $page = self::searchPage();

        if (!$page){
            $dispatcher->dispatch('core.page-not-found');
            return;
        }

        Kryn::$page = self::getPage($page);
        $dispatcher->dispatch('core.set-page', new GenericEvent(Kryn::$page));

        $routes = new RouteCollection();
        $dispatcher->dispatch('core.setup-routes-pre', new GenericEvent($routes));

        Kryn::$page->addRoutes($routes);

        $dispatcher->dispatch('core.setup-routes', new GenericEvent($routes));

        $matcher = new UrlMatcher($routes, new RequestContext());

        $dispatcher->addSubscriber(new RouterListener($matcher));


    }

    /**
     * Check whether we have a static version of the current request.
     * If so and if static caching is activated, it prints the cached html
     * and exits.
     *
     */
    public static function checkStaticCaching(){

        //caching
        if (true || Kryn::isEditMode()) return;

        $key = md5(self::getRequest()->getRequestUri());
        $caching = Kryn::getCache('core/static-caching');

        if (!$caching){
            //reload static-caching index
        }

        if ($caching[$key] && file_exists($file = 'media/cache/core.static.'.$key.'.html')){

            $response = new Response(file_get_contents($file), 200);
            $response->send();
            exit;
        }
    }

    /**
     * Handles the actual request.
     *
     * This exits the application.
     *
     * @event core.response-send-pre
     * @event core.response-send
     */
    public static function handleRequest(){
        global $_start;

        $kernel     = self::getHttpKernel();
        $request    = self::getRequest();
        $dispatcher = self::getEventDispatcher();

        $dispatcher->addListener('core.domain-redirect', function(GenericEvent $event){
            $domain = $event->getSubject();
            $response = new \Symfony\Component\HttpFoundation\RedirectResponse($domain->getUrl(Kryn::$ssl), 301);
            $response->send();
            exit;
        });

        $dispatcher->addListener('core.domain-not-found', function(GenericEvent $event){
            Kryn::internalError(t('Domain not found'), tf('Domain `%s` not found.', $event->getSubject()));
        });

        $dispatcher->addListener('core.domain-no-start-page', function(GenericEvent $event){
            Kryn::internalError(null, tf('There is no start page for domain `%s`.', Kryn::$domain->getDomain()));
        });

        //search domain and set to Core\Kryn::$domain
        self::detectDomain();

        $response = $kernel->handle($request);

        if ($response instanceof PluginResponse) {
           PageController::injectPlugin($response);
           $response = PageController::getResponse();
        }


        //caching
        if (false && !Kryn::isEditMode()){
            $dispatcher->addListener('core.response-send-pre', function(){
                $caching       = Kryn::getCache('core/static-caching');
                $key           = md5(Kryn::getRequest()->getRequestUri());
                $caching[$key] = true;
                $file          = 'media/cache/core.static.'.$key.'.html';
                file_put_contents($file, Kryn::getResponse()->getContent());
                Kryn::setCache('core/static-caching', $caching, 60);
            });
        }

        if (Kryn::isEditMode() && $response instanceof PageResponse){

            \Admin\AdminController::handleKEditor();
        }

        $dispatcher->dispatch('core.response-send-pre', new GenericEvent($response));
        $response->send();
        $dispatcher->dispatch('core.response-send', new GenericEvent($response));

        Kryn::getLogger()->addDebug('Done. Generation time: '.(microtime(true)-$_start).' seconds.');

        exit;
    }

    /**
     * Checks if we're in the frtonend editor mode.
     * Only true if ?_kryn_editor=1 is set and the current user has update-access to the Core\\Node object.
     *
     * @return bool
     */
    public static function isEditMode(){
        return Kryn::getRequest()->get('_kryn_editor') == 1 && Kryn::$page && Permission::checkUpdate('Core.Node', Kryn::$page->getId());
    }

    /**
     * @static
     * @return string
     */
    public static function getBaseUrl(){
        return Kryn::$baseUrl;
    }


    /**
     * @static
     */
    public static function setBaseUrl($pBaseUrl){
        Kryn::$baseUrl = $pBaseUrl;
    }

    /**
     * $admin is initialised during the bootstrap.php and is basically:
     *
     *     Core\Kryn::$admin = (getArgv(1) == 'admin');
     *
     * @return bool
     */
    public static function isAdmin(){
        return self::$admin;
    }

    /**
     * Checks the specified page.
     * Internal function.
     *
     * @param   Page      $page
     * @param   bool       $pWithRedirect
     *
     * @return  array|bool False if no access
     * @internal
     */
    public static function checkPageAccess($page, $pWithRedirect = true) {

        $oriPage = $page;

        if ($page->getAccessFrom() > 0 && ($page->getAccessFrom() > time()))
            $page = false;

        if ($page->getAccessTo() > 0 && ($page->getAccessTo() < time()))
            $page = false;

        if ($page->getAccessFromGroups() != '') {

            $access = false;
            $groups = ',' . $page->getAccessFromGroups() . ","; //eg ,2,4,5,

            $cgroups = null;
            if ($page['access_need_via'] == 0) {
                $cgroups =& Kryn::getClient()->user['groups'];
            } else {
                $htuser = Kryn::getClient()->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

                if ($htuser['id'] > 0) {
                    $cgroups =& $htuser['groups'];
                }
            }

            if ($cgroups) {
                foreach ($cgroups as $group) {
                    if (strpos($groups, "," . $group['group_id'] . ",") !== false) {
                        $access = true;
                    }
                }
            }

            if (!$access) {
                //maybe we have access through the backend auth?
                foreach (Kryn::getAdminClient()->user['groups'] as $group) {
                    if (strpos($groups, "," . $group . ",") !== false) {
                        $access = true;
                        break;
                    }
                }
            }

            if (!$access) {
                $page = false;
            }
        }


        if (!$page && $pWithRedirect && $oriPage['access_need_via'] == 0) {

            if ($oriPage['access_redirectto'] + 0 > 0)
                Kryn::redirectToPage($oriPage['access_redirectto']);
        }

        if (!$page && $pWithRedirect && $oriPage['access_need_via'] == 1) {
            header('WWW-Authenticate: Basic realm="' .
                   t('Access denied. Maybe you are not logged in or have no access.') . '"');
            header('HTTP/1.0 401 Unauthorized');

            exit;
        }
        return $page;
    }

    /**
     * Returns the domain of the specified page
     * @static
     * @param $pId
     * @return bool|string
     */
    public static function getDomainOfPage($pId) {
        $id = false;

        $page2Domain =& Kryn::getCache('systemPages2Domain');

        if (!is_array($page2Domain)) {
            $page2Domain = Render::updatePage2DomainCache();
        }

        $pId = ',' . $pId . ',';
        foreach ($page2Domain as $domain_id => &$pages) {
            $pages = ',' . $pages . ',';
            if (strpos($pages, $pId) !== false) {
                $id = $domain_id;
            }
        }
        return $id;
    }


    /**
     * Returns a array with all urls to id pairs.
     *
     * @param integer $pDomainId
     * @return array
     */
    public static function &getCachedUrlToPage($pDomainId){

        $urls        = self::getFastCache('core/urls');
        $urlsCreated = self::getCache('core/urls.created');

        if (!$urls || $urls['!created'] != $urlsCreated) {

            $nodes = NodeQuery::create()
                ->select(array('id', 'url', 'lvl', 'type'))
                ->filterByDomainId($pDomainId)
                ->orderByBranch()
                ->find();

            //build urls array
            $urls         = array();
            $level        = array();

            $url = '';

            foreach ($nodes as $node){
                if ($node['lvl'] == 0) continue; //root
                if ($node['type'] == 3) continue; //deposit

                if ($node['type'] == 2 || $node['url'] == ''){
                    //folder or empty url
                    $level[$node['lvl']+0] = ($level[$node['lvl']-1]) ?: '';
                    continue;
                }

                $url = ($level[$node['lvl']-1]) ?: '';
                $url .= '/'.$node['url'];

                $level[$node['lvl']+0] = $url;

                $urls[$url] = $node['id'];
            }

            $urls['!created'] = microtime();
            self::setFastCache('core/urls', $urls);
            self::setCache('core/urls.created', $urls['!created']);
        }
        return $urls;
    }


    /**
     * @param integer $pDomainId
     * @return array
     */
    public static function &getCachedPageToUrl($pDomainId){
        static $flipped;

        if (!$flipped){
            $flipped = array_flip(self::getCachedUrlToPage($pDomainId));
        }
        return $flipped;
    }

    /**
     * Search the current page or the start page, loads all information and checks the access.
     * @internal
     * @return int
     */
    public static function searchPage() {

        $url = self::getRequest()->getPathInfo();

        $domain = Kryn::$domain->getId();
        $urls   = self::getCachedUrlToPage($domain);

        //extract extra url attributes
        $found = $end = false;
        $possibleUrl = $next = $url;
        $oriUrl = $possibleUrl;

        do {

            $id = $urls[$possibleUrl];

            if ($id > 0 || $possibleUrl == '') {
                $found = true;
            } else if (!$found) {
                $id = Kryn::$urls['alias'][$possibleUrl];
                if ($id > 0) {
                    $found = true;
                    //we found a alias
                    Kryn::redirectToPage($id);
                } else {
                    $possibleUrl = $next;
                }
            }

            if ($next == false) {
                $end = true;
            } else {
                /*
                //maybe we found a alias in the parens with have a alias with "withsub"
                $aliasId = Kryn::$urls['alias'][$next . '/%'];

                if ($aliasId) {

                    //links5003/test => links5003_5/test

                    $aliasPageUrl = Kryn::$urls['id']['id=' . $aliasId];

                    $urlAddition = str_replace($next, $aliasPageUrl, $url);

                    $toUrl = $urlAddition;

                    //go out, and redirect the user to this url
                    Kryn::redirect($urlAddition);
                    $end = true;
                }
                */
            }

            $pos = strrpos($next, '/');
            if ($pos !== false)
                $next = substr($next, 0, $pos);
            else
                $next = false;

        } while (!$end);

        $diff = substr($url, strlen($possibleUrl), strlen($url));

        if (substr($diff, 0, 1) != '/')
            $diff = '/' . $diff;

        $extras = explode("/", $diff);
        if (count($extras) > 0) {
            foreach ($extras as $nr => $extra) {
                $_REQUEST['e' . $nr] = $extra;
            }
        }
        $url = $possibleUrl;

        Kryn::$isStartpage = false;

        if ($url == '') {
            $pageId = Kryn::$domain->getStartnodeId();

            if (!$pageId > 0) {
                self::getEventDispatcher()->dispatch('core.domain-no-start-page');
            }

            Kryn::$isStartpage = true;
        } else {
            $pageId = $id;
        }

        return $pageId;
    }


    /**
     * Initialize the breadcrumb list.
     *
     * Loads current parents and publish the Kryn::$breadcrumbs to the template engine.
     *
     * @internal
     */
    public static function loadBreadcrumb() {
        Kryn::$breadcrumbs = Kryn::$page->getParents();
        tAssignRef('breadcrumbs', Kryn::$breadcrumbs);
    }

    /**
     * Prints the Kryn/404-page.tpl template to the client and exit, if defined redirect the the 404-page
     * defined in the domain settings or opens the 404-interface file which is also defined
     * in the domain settings.
     *
     * @static
     */
    public static function notFound() {

        $msg = sprintf(t('Route not found %s'), Kryn::$domain->getDomain() . '/' . Kryn::getRequestedPath(true));
        klog('404', $msg);
        self::internalError('Not Found', $msg);
        exit;
    }

    /**
     * Prints the internal-error.tpl template to the client and exist.
     *
     * @static
     * @param $pTitle
     * @param $pMsg
     */
    public static function internalError($pTitle = '', $pMsg) {
        tAssign('msg', $pMsg);
        tAssign('title', $pTitle?$pTitle:'Internal system error');
        $response = new Response(tFetch('kryn/internal-error.tpl'), 404);
        $response->send();
        exit;
    }

    /**
     * Prints the Kryn/internal-message.tpl template to the client and exist.
     *
     * @static
     * @param $pTitle
     * @param $pMsg
     */
    public static function internalMessage($pTitle, $pMsg = '') {
        tAssign('title', $pTitle);
        tAssign('msg', $pMsg);
        print tFetch('kryn/internal-message.tpl');
        exit;
    }


    /**
     * Loads the layout from the current page and generate header and body HTML. Send to client.
     *
     * @param bool $pReturn Return instead of exit()
     *
     * @return bool
     *
     * @internal
     */
    public static function display($pReturn = false) {
        global $_start;

        Kryn::$pageUrl = '/' . Kryn::getRequestedPath(true); //Kryn::$baseUrl.$possibleUrl;

        # search page for requested URL and sets to Kryn::$page
        Kryn::searchPage();

        if (!Kryn::$page) {
            return Kryn::notFound();
        }

        Kryn::$page = self::checkPageAccess(Kryn::$page);

        if (!Kryn::$page || !Kryn::$page->getId() > 0) { //no access
            return Kryn::notFound();
            return false;
        }

        Kryn::$canonical = Kryn::getBaseUrl() . Kryn::getRequestedPath(true);

        $pageCacheKey =
            'systemWholePage-' . Kryn::$domain->getId() . '_' . Kryn::$page->getId() . '-' . md5(Kryn::$canonical);

        if (Kryn::$domainProperties['core']['cachePagesForAnons'] == 1 && Kryn::getClient()->user['id'] == 0 &&
            count($_POST) == 0
        ) {

            $cache =& Kryn::getCache($pageCacheKey);
            if ($cache) {
                print $cache;
                exit;
            }

        }

        if (Kryn::$domain->getStartnodeId() == Kryn::$page->getId() && !Kryn::$isStartpage) {
            Kryn::redirect(Kryn::$baseUrl);
        }


        if (Kryn::$page->getType() == 0) { //is page
            if (Kryn::$page->getForceHttps() == 1 && Kryn::$ssl == false) {
                header('Location: ' . str_replace('http://', 'https://', Kryn::$baseUrl) . Kryn::$page->getFullUrl());
                exit;
            }



            tAssignRef('themeProperties', Kryn::$themeProperties);
        }


        Kryn::loadBreadcrumb();

        Kryn::$breadcrumbs[] = Kryn::$page;


        if (!Kryn::$page->getLayout()) {
            Kryn::$pageHtml = self::internalError(t('No layout'), tf('No layout chosen for the page %s.', Kryn::$page->getTitle()));
        } else {
            Kryn::$pageHtml = Render::renderPage();
        }

        Kryn::$pageHtml = str_replace('\[[', '[[', Kryn::$pageHtml);
        Kryn::replacePageIds(Kryn::$pageHtml);

        //htmlspecialchars(urldecode(Kryn::$url));
        Kryn::$pageHtml = preg_replace('/href="#(.*)"/', 'href="' . Kryn::$url . '#$1"', Kryn::$pageHtml);

        foreach (Kryn::$modules as $key => $mod) {
            Kryn::$modules[$key] = null;
        }

        if (Kryn::$disableSearchEngine == false) {
            $resCode = SearchEngine::createPageIndex(Kryn::$pageHtml);

            if ($resCode == 2) {
                Kryn::notFound('invalid-arguments');
            }
        }

        self::removeSearchBlocks(Kryn::$pageHtml);

        header("Content-Type: text/html; charset=utf-8");

        if (Kryn::$domainProperties['core']['cachePagesForAnons'] == 1 && self::$client->getUser()->getId() == 0 &&
            count($_POST) == 0
        ) {

            $page = Render::getPage(Kryn::$pageHtml);
            Kryn::setCache($pageCacheKey, $page, 10);
            print $page;

        } else {

            Render::printPage(Kryn::$pageHtml);
        }

        exit;
    }

    /**
     * Returns the wrapped content with the unsearchable block tags.
     * @static
     *
     * @param string $pContent
     *
     * @return string Wrapped content
     */
    public static function unsearchable($pContent) {
        return '<!--unsearchable-begin-->' . $pContent . '<!--unsearchable-end-->';
    }

    /**
     * Removes all unsearchable block tags.
     * @static
     *
     * @param string $pHtml
     */
    public static function removeSearchBlocks(&$pHtml) {
        $pHtml = str_replace('<!--unsearchable-begin-->', '', $pHtml);
        $pHtml = str_replace('<!--unsearchable-end-->', '', $pHtml);
    }

    /**
     * Deactivates the 404 content check
     */
    public static function disableSearchEngine() {

        self::$disableSearchEngine = true;

    }

    /**
     * Compress given string
     *
     * @param string $pString
     *
     * @return string
     * @static
     * @internal
     */
    public static function compress($pString) {
        $res = $pString;
        $res = preg_replace('/\s\s+/', ' ', $res);
        $res = preg_replace('/\t/', '', $res);
        $res = preg_replace('/\n\n+/', "\n", $res);
        return $res;
    }

    /**
     * Removes a value for the specified cache-key
     *
     * @param string $pCode
     */
    public static function deleteCache($pCode) {
        if (!self::$cache)
            self::initCache();
        Kryn::$cache->delete($pCode);
    }

    /**
     * Sets a content to the specified cache-key.
     * 
     * If you want to save php class objects, you should serialize it before.
     *
     * @param string  $pCode
     * @param string  $pValue
     * @param integer $pTimeout In seconds. Default is one hour
     * @return boolean
     * @static
     */
    public static function setCache($pCode, $pValue, $pTimeout = null) {
        if (!self::$cache)
            self::initCache();
        return Kryn::$cache->set($pCode, $pValue, $pTimeout);
    }

    /**
     * Marks a code as invalidate beginning at $pTime.
     *
     * @param string  $pCode
     * @param integer $pTime Unix timestamp. Default is microtime(true)
     * @return boolean
     */
    public static function invalidateCache($pCode, $pTime = null) {
        if (!self::$cache)
            self::initCache();
        return Kryn::$cache->invalidate($pCode, $pTime ? $pTime : microtime(true));
    }

    /**
     * Returns the content of the specified cache-key
     *
     * @param string $pCode
     *
     * @return string
     * @static
     */
    public static function &getCache($pCode) {
        if (!self::$cache)
            self::initCache();
        return Kryn::$cache->get($pCode);
    }

    /**
     * Sets data to the specified cache-key.
     * This function saves the value in a generated php file
     * as php code or via apc_store.
     *
     * If you want to save php class objects, you should serialize it before!
     * 
     * The idea behind this: If the server has active apc or
     * other opcode caching, then this method is way faster then tcp caching-server.
     * Please be sure, that you really want to use that: This
     * is not compatible with load balanced Kryn.cms installations
     * and should only be used, if you are really sure, that
     * a other machine in a load balanced scenario does not
     * need information about this cache.
     * A good purpose for this is for example caching converted
     * local json files (like the installed extension configs).
     *
     * Use otherwise setCache() to save the whole data or save only
     * a timestamp in the setCache() method and check it later against your
     * getFastCache().
     *  Example:
     *
     *    $data['content'] = $actualData;
     *    $data['created'] = microtime();
     *    Kryn::setFastCache('data', $data);
     *    Kryn::setCache('data.created', $data['created']);
     *
     *    //then later
     *
     *    $data        = Kryn::getFastCache('data');
     *    $dataCreated = Kryn::getCache('data.created');
     *    if (!$data || $data['created'] != $dataCreated)
     *        //data invalid/outdated
     *
     *  This makes sure, all machines in a load balanced scenario knows the data status.
     *
     * @param string $pCode
     * @param string $pValue
     * @param int    $pTimeout
     * @return boolean
     * @static
     */
    public static function setFastCache($pCode, $pValue, $pTimeout = null) {
        if (!self::$cache)
            self::initCache();
        return Kryn::$cacheFast->set($pCode, $pValue, $pTimeout);
    }

    /**
     * Returns the content of the specified cache-key.
     * See Kryn::setFastCache for more information.
     *
     * @param string $pCode
     * @return boolean
     * @static
     */
    public static function &getFastCache($pCode) {
        if (!self::$cache)
            self::initCache();
        return Kryn::$cacheFast->get($pCode);
    }

    /**
     * Reads all files of the specified folders.
     *
     * @param string $pPath
     * @param bool   $pWithExt Return file extensions or not
     *
     * @return array
     * @static
     */
    public static function readFolder($pPath, $pWithExt = false) {
        $h = @opendir($pPath);
        if (!$h) {
            return false;
        }
        while ($file = readdir($h)) {
            if (substr($file, 0, 1) != '.') {
                if (!$pWithExt) {
                    $file = substr($file, 0, (strpos($file, '.') > 0) ? strrpos($file, '.') : strlen($file));
                }
                $files[] = $file;
            }
        }
        return $files;
    }

    /**
     * Returns the servers temp folder, where you should store dynamic generated stuff.
     *
     * You can access these files also through
     * the \Core\TempFile class as you would with the \Core\File class.
     *
     * @static
     * @internal
     * @param  bool $pWithKrynContext Adds the 'id' value of the config as sub folder. This makes sure, multiple kryn installations
     *                                does not overwrite each other files.
     * 
     * @return string Path with trailing slash
     * @throws FileIOException
     */
    public static function getTempFolder($pWithKrynContext = true){

        if (!self::$cachedTempFolder){

            $folder = Kryn::$config['fileTemp'];
            if (!$folder && getenv('TMP')) $folder = getenv('TMP');
            if (!$folder && getenv('TEMP')) $folder = getenv('TEMP');
            if (!$folder && getenv('TMPDIR')) $folder = getenv('TMPDIR');
            if (!$folder && getenv('TEMPDIR')) $folder = getenv('TEMPDIR');

            if (!$folder) $folder = sys_get_temp_dir();

            self::$cachedTempFolder = realpath($folder);

            if (substr(self::$cachedTempFolder, -1) != DIRECTORY_SEPARATOR)
                self::$cachedTempFolder .= DIRECTORY_SEPARATOR;
        }

        if ($pWithKrynContext){

            if (!is_writable(self::$cachedTempFolder))
                throw new \FileIOException('Temp directory is not writeable. '.$folder);

            //add our id to folder, so this installation works inside of a own directory.
            $folder = self::$cachedTempFolder . self::getId() . DIRECTORY_SEPARATOR;

            if (!is_dir($folder))
                mkdir($folder);

            return $folder;
        }

        return self::$cachedTempFolder;
    }

    public static function getId(){
        return 'kryn-'.(self::$config['id'] ?: 'no-id');
    }

    /**
     * Creates a temp folder and returns its path.
     * Please use TempFile::createFolder() class instead.
     *
     * @static
     * @internal
     * @param  string $pPrefix
     * @return string Path with trailing slash
     */
    public static function createTempFolder($pPrefix = ''){

        $tmp = self::getTempFolder();

        do {
            $path = $tmp . $pPrefix . dechex(time() / mt_rand(100, 500));
        } while (is_dir($path));

        mkdir($path);

        if (substr($path, -1) != '/')
            $path .= '/';

        return $path;
    }

    /**
     * Returns the module directory.
     *
     * @param string $pModule
     * @return string
     */
    public static function getModuleDir($pModule){
        return $pModule == 'core' ? 'core/' : PATH_MODULE.strtolower($pModule).'/';
    }


    /**
     * Replaces all object://<objectKey>/<pk> strings by its real url.
     *
     * @param string $pHtml
     * @return string
     */
    public static function parseObjectUrls($pHtml){

        return preg_replace_callback(
            '|object://([a-zA-Z0-9\.\\\\]+)/([^"/,]+)|',
            '\\Core\\Kryn::replaceObjectUrl',
            $pHtml
        );

    }

    public static function replaceObjectUrl($pMatch){
        return Object::getPublicUrl($pMatch[1], $pMatch[2]);
    }

    /**
     * Returns the URL of the specified page
     *
     * @param integer  $pId
     * @param boolean  $pAbsolute
     * @param bool|int $pDomainId
     *
     * @return string
     * @static
     */
    public static function pageUrl($pId = 0, $pAbsolute = false, $pDomainId = false) {

        return 'object://node/'.$pId;

    }

}

?>
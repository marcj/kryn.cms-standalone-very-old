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

use Core\Config\Client;
use Core\Config\SystemConfig;
use Core\Exceptions\BundleNotFoundException;
use Core\Models\Base\DomainQuery;
use Core\Models\ContentQuery;
use Core\Models\Node;
use Core\Models\NodeQuery;
use Core\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Kryn.core class
 *
 * @author MArc Schmidt <marc@Kryn.org>
 */
class Kryn extends Controller
{
    /**
     * Contains all parent pages, which can be shown as a breadcrumb navigation.
     * This is filled automatically. If you want to add own items, use Kryn::addMenu( $pName, $pUrl );
     *
     * @type: array
     * @internal
     * @static
     */
    public static $breadcrumbs;

    /**
     * Contains all translations as key -> value pair.
     *
     * @var array
     * @static
     * @internal
     */
    public static $lang;

    /**
     * Contains the current language code.
     * Example: 'de', 'en'
     *
     * @var string
     * @static
     * @internal
     */
    public static $language;

    /**
     * Contains all used system language.
     * Example: 'de', 'en'
     *
     * @var string
     * @static
     * @internal
     */
    public static $languages;

    /**
     * Defines the current baseUrl (also use in html <header>)
     *
     * @var string
     * @static
     */
    public static $baseUrl = '/';

    /**
     * Contains the current domain with all information (as defined in the database system_domain)
     *
     * @var Domain
     *
     * @static
     */
    public static $domain;

    /**
     * Contains the current page with all information
     *
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
     *
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
     *
     * @var array
     * @static
     */
    public static $themeProperties = array();

    /**
     * Contains the values of the domain-properties from the current domain.
     *
     * @var array
     *
     * @static
     */
    public static $domainProperties = array();

    /**
     * Contains the values of the page-properties from the current page.
     *
     * @var array
     * @static
     */
    public static $pageProperties = array();

    /**
     * Defines whether force-ssl is enabled or not.
     *
     * @var bool
     * @static
     * @internal
     */
    public static $ssl = false;

    /**
     * Contains the current port
     *
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
     *
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
     *
     * @var array
     * @static
     */
    public static $contents;

    /**
     * Defines whether we are at the startpage
     *
     * @var bool
     * @static
     */
    public static $isStartpage;

    /**
     * @var \Core\Config\Bundle[]|\Core\Config\Configs
     */
    public static $configs;

    /**
     * Contains all installed extensions
     * Example: array('core', 'admin', 'users', 'sitemap', 'publication');
     *
     * @var array
     * @static
     */
    public static $bundles = ['Core\CoreBundle', 'Admin\AdminBundle', 'Users\UsersBundle'];

    private static $bundleInstances = array();

    /**
     * Contains all installed themes
     *
     * @static
     * @var array
     */
    public static $themes;

    /**
     * Contains the current propel pdo connection instance.
     *
     * @var PDO
     * @static
     */
    public static $dbConnection;

    /**
     * Contains the system config (app/config/config.xml).
     *
     * @var \Core\Config\SystemConfig
     * @static
     */
    public static $config;

    /**
     * @var \Core\Config\SystemConfig
     */
    private static $systemConfig;

    /**
     * The Auth user object of the backend user.
     *
     * @var \Core\Client\ClientAbstract
     * @static
     */
    private static $adminClient;

    /**
     * The krynAuth object of the frontend user.
     * It's empty when we're in the backend.
     *
     * @var \Core\Client\ClientAbstract
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
     *
     * @var string
     * @static
     * @internal
     */
    public static $pageUrl = '';

    /**
     * Contains the full absolute (canonical) URL to the current content.
     * Example: http://domain.com/my/path/to/page
     *
     * @var string
     * @internal
     * @static
     */
    public static $canonical = '';

    /**
     * Defines whether the content check before sending the html to the client is activate or not.
     *
     * @var bool
     * @static
     * @internal
     */
    public static $disableSearchEngine = false;

    /**
     * Contains the Kryn\Cache object
     *
     * @var Cache\CacheInterface
     * @static
     */
    public static $cache;

    /**
     * Contains the Core\Cache\* object for file caching.
     * See Kryn::setFastCache for more information.
     *
     * @static
     * @var Cache\CacheInterface
     */
    public static $cacheFast;

    /**
     * Cached object of the current domains's urls to id, id to url, alias to id
     *
     * @static
     * @var array
     */
    public static $urls;

    /**
     * Cached path of temp folder.
     *
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
     * @var RouteCollection
     */
    public static $routes;

    /**
     * The monolog logger object.
     *
     * @var \Monolog\Logger
     */
    private static $monolog;

    /**
     * Current instance, factoring.
     *
     * @var Kryn
     */
    private static $instance;

    /**
     * AutoLoader instance.
     *
     * @var \Composer\Autoload\ClassLoader
     */
    private static $loader;

    public static function setSystemConfig()
    {
        if (null === static::$systemConfig) {
            static::$systemConfig = new SystemConfig();
            //todo, read from config.xml
        }

        return static::$systemConfig;
    }

    /**
     * @param \Composer\Autoload\ClassLoader $loader
     */
    public static function setLoader($loader)
    {
        self::$loader = $loader;
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        return self::$loader;
    }

    /**
     * The response object. Here you can add
     *   - css files
     *   - javascript files
     *   - cookies
     *   - http headers
     *   - add plain javascript/css
     *
     * or modify the content in the 'core/send-page' event.
     *
     * @return PageResponse
     */
    public static function getResponse()
    {
        if (!self::$response) {
            self::$response = new PageResponse();
        }

        return self::$response;
    }

    public static function getRequest()
    {
        if (!self::$request) {
            self::$request = HttpRequest::createFromGlobals();
        }

        return self::$request;
    }

    /**
     * @param $path
     *
     * return Template\EngineInterface
     */
    public static function getTemplateEngineForFileName($path)
    {
        return Template\Engine::createForFileName($path);
    }

    /**
     * Returns the logger object.
     *
     * @return \Monolog\Logger
     */
    public static function getLogger()
    {
        if (!self::$monolog) {
            self::$monolog = new \Monolog\Logger('kryn');

            //todo, setup via config
            self::$monolog->pushProcessor(
                function ($log) {
                    global $_start;
                    static $lastDebugPoint;

                    $timeUsed = round((microtime(true) - $_start) * 1000, 2);
                    $bytes = convertSize(memory_get_usage(true));
                    $last = $lastDebugPoint ? 'diff ' . round(
                        (microtime(true) - $lastDebugPoint) * 1000,
                        2
                    ) . 'ms' : '';
                    $lastDebugPoint = microtime(true);

                    $log['message'] = "[$bytes, {$timeUsed}ms, $last] - " . $log['message'];

                    return $log;
                }
            );
            self::$monolog->pushHandler(new \Monolog\Handler\SyslogHandler('kryn'));
        }

        return self::$monolog;
    }

    /**
     * Returns the http kernel. This initialise it if necessary.
     *
     * @return HttpKernel
     */
    public static function getHttpKernel()
    {
        if (!self::$httpKernel) {
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
    public static function getEventDispatcher()
    {
        if (!self::$eventDispatcher) {
            self::$eventDispatcher = new EventDispatcher();
        }

        return self::$eventDispatcher;
    }

    /**
     * Adds a new crumb to the breadcrumb array.
     *
     * @param Page $page
     *
     * @static
     */
    public static function addBreadcrumb($page)
    {
        Kryn::$breadcrumbs[] = $page;
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
     *
     * @static
     *
     * @param string $docType
     */
    public static function setDocType($docType)
    {
        Render::$docType = $docType;
    }

    /**
     * Returns the current defined doctype
     *
     * @static
     * @return string Doctype
     */
    public static function getDocType()
    {
        return Render::$docType;
    }

    public static function getSystemConfig()
    {
        return static::$config;
    }

    /**
     * Loads all active bundles to self::$bundles.
     */
    public static function loadActiveModules()
    {
        self::$bundles = ['Core\CoreBundle', 'Admin\AdminBundle', 'Users\UsersBundle'];
        if ($bundles = self::getSystemConfig()->getBundles()) {
            foreach ($bundles as $bundle) {
                static::$bundles[] = $bundle;
            }
        }
        $bundles = static::getBundleClasses(); //triggers loading of all bundles to $bundleInstances
    }

    /**
     * @return string[]
     */
    public static function getBundles()
    {
        return self::$bundles;
    }

    /**
     * @return Bundle[]
     */
    public static function getBundleClasses()
    {
        $bundles = [];
        foreach (self::$bundles as $bundleName) {
            if ($bundle = self::getBundle($bundleName)) {
                $bundles[] = $bundle;
            }
        }
        return $bundles;
    }

    /**
     * Creates all symlink in /web/<bundleName> to <bundlePath>/Resources/public
     * if not already.
     */
    public static function prepareWebSymlinks()
    {
        $bundles = PATH_WEB . 'bundles/';
        if (!is_dir($bundles)) {
            if (!mkdir($bundles)) {
                die(sprintf('Can not create `%s` directory. Please check permissions.', getcwd().'/'.$bundles));
            }
        }

        foreach (Kryn::$bundles as $bundle) {
            $bundle = self::getBundle($bundle);
            if ($bundle) {
                $public = $bundle->getPath() . 'Resources/public';
                if (is_dir($public)) {
                    $web = $bundles . strtolower($bundle->getName(true));
                    if (!is_link($web)) {
                        symlink(realpath($public), $web);
                    }
                }
            }
        }
    }

    /**
     * Loads all activated extension configs and tables
     *
     * @param bool $forceNoCache
     *
     * @internal
     */
    public static function loadModuleConfigs($forceNoCache = false)
    {
        $cached = self::getFastCache('core/configs');
        $bundles = Kryn::getBundleClasses();
        $hashes = [];
        foreach ($bundles as $bundle) {
            $hashes[] = $bundle->getConfigHash();
        }
        $hash = md5(implode('.', $hashes));

        if ($cached) {
            $cached = unserialize($cached);
            if (is_array($cached) && $cached['md5'] == $hash){
                self::$configs = $cached['data'];
            }
        }

        if (!self::$configs) {
            self::$configs = new Config\Configs(Kryn::$bundles);
            self::$configs->setup();
            $cached = serialize([
                'md5'  => $hash,
                'data' => self::$configs
            ]);
            self::setFastCache('core/configs', $cached);
        }
        self::prepareWebSymlinks();


        foreach (self::$configs as $bundleConfig) {
            if ($events = $bundleConfig->getListeners() ) {
                foreach ($events as $event) {
                    static::getEventDispatcher()->attachEvent($event);
                }
            }
        }

        return;

        //TODO, check what we need.

        $md5 = md5($md5);
        if (!$forceNoCache) {
            Kryn::$themes =& Kryn::getFastCache('core/themes');
            Kryn::$configs =& Kryn::getFastCache('core/configs');
        }

        if ((!Kryn::$themes || $md5 != Kryn::$themes['__md5']) ||
            (!Kryn::$configs || $md5 != Kryn::$configs['__md5'])
        ) {

            Kryn::$configs = array();
            foreach (Kryn::$bundles as $extension) {
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

                //resolve relations
                //if a object has a MANY_TO_ONE relation to another, then we create a virtual field to other need
                //that points to the referencing object with the opposite relation.
                if (is_array($config['objects'])) {
                    foreach ($config['objects'] as $objectKey => $object) {
                        if (is_array($object['fields'])) {
                            foreach ($object['fields'] as $fieldKey => $field) {
                                if ($field['objectRelation'] == ORM\ORMAbstract::MANY_TO_ONE) {
                                    $objectName = Object::getName($field['object']);
                                    $module = strtolower(Object::getModule($field['object']));
                                    $fieldName = $field['objectRefRelationName'] ? : lcfirst($objectKey);
                                    Kryn::$configs[$module]['objects'][$objectName]['fields'][$fieldName] = array(
                                        'virtual' => true,
                                        'label' => 'Auto Object relation (' . ORM\ORMAbstract::MANY_TO_ONE . ')',
                                        'object' => $extension . '\\' . $objectKey,
                                        'objectRelation' => ORM\ORMAbstract::ONE_TO_MANY
                                    );
                                }
                            }
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

            foreach (Kryn::$bundles as &$extension) {

                $config = Kryn::$configs[$extension];
                if ($config['themes']) {
                    Kryn::$themes[$extension] = $config['themes'];
                }
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
    public static function getLanguage()
    {
        if (Kryn::$domain && Kryn::$domain->getLang()) {
            return Kryn::$domain->getLang();
        } elseif (getArgv(1) == 'admin' && getArgv('lang', 2)) {
            return getArgv('lang', 2);
        } elseif (Kryn::getAdminClient() && Kryn::getAdminClient()->hasSession()) {
            return Kryn::getAdminClient()->getSession()->getLanguage();
        }

        return 'en';

    }

    /**
     * Checks whether a module is active/enabled or not.
     *
     * @param  string $bundleName
     *
     * @return bool
     */
    public static function isActiveBundle($bundleName)
    {
        $bundle = self::getBundle($bundleName);
        if ($bundle) {
            $className = $bundle->getClassName();

            return in_array($className, self::$bundles);
        }
        return false;
    }

    /**
     * Convert a string to a mod-rewrite compatible string.
     *
     * @param string $string
     *
     * @return string
     * @static
     */
    public static function toModRewrite($string)
    {
        $res = @str_replace('ä', "ae", strtolower($string));
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
     * @param string $content The content of this variable will be modified.
     *
     * @static
     * @internal
     */
    public static function replacePageIds(&$content)
    {
        $content = preg_replace_callback(
            '/href="(\d+)"/',
            create_function(
                '$p2P',
                '

                return \'href="\'.Core\Kryn::pageUrl($p2P[1]).\'"\';
            '
            ),
            $content
        );
    }

    /**
     * Translates all string which are surrounded with [[ and ]].
     *
     * @param string $content
     *
     * @static
     * @internal
     * @return mixed The result of preg_replace_callback()
     */
    public static function translate($content)
    {
        Kryn::loadLanguage();

        return preg_replace_callback(
            '/([^\\\\]?)\[\[([^\]]*)\]\]/',
            create_function(
                '$p2P',
                '

                return $p2P[1].t( $p2P[2] );
                '
            ),
            $content
        );
    }

    /**
     * Redirect the user to specified URL within the system.
     * Relative to the baseUrl.
     *
     * @param string $url
     *
     * @static
     */
    public static function redirect($url = '')
    {
        if (strpos($url, 'http') === false && Kryn::$domain) {

            if (Kryn::$domain->getMaster() != 1) {
                $url = Kryn::$domain->getLang() . '/' . $url;
            }

            $domain = Kryn::$domain->getDomain();
            $path = Kryn::$domain->getPath();

            if (substr($domain, 0, -1) != '/') {
                $domain .= '/';
            }
            if ($path != '' && substr($path, 0, 1) == '/') {
                $path = substr($path, 1);
            }
            if ($path != '' && substr($path, 0, -1) == '/') {
                $path .= '/';
            }
            if ($url != '' && substr($path, 0, 1) == '/') {
                $url = substr($url, 1);
            }

            if ($url == '/') {
                $url = '';
            }

            $url = 'http://' . $domain . $path . $url;
        }

        header("HTTP/1.1 301 Moved Permanently");
        header('Location: ' . $url);
        exit;
    }

    /**
     * Init cache and cacheFast instances.
     *
     */
    public static function initCache()
    {
        //global normal cache
        Kryn::$cache = new Cache\Controller(
            static::$config->getCache()
        );
    }

    /**
     * Init admin and frontend client.
     */
    public static function initClient()
    {

        $systemClientConfig = static::getSystemConfig()->getClient();
        $defaultClientClass = $systemClientConfig->getClass();

//        $defaultClientClass = Kryn::$config['client']['class'];
//        $defaultClientConfig = Kryn::$config['client']['config'];
//        $defaultClientStore = Kryn::$config['client']['config']['store'];
//        $defaultAutoStart = Kryn::$config['client']['autoStart'];

        if (static::isAdmin()) {
            static::$client = static::$adminClient = new $defaultClientClass($systemClientConfig);
            static::getAdminClient()->start();
        }

        if (!static::isAdmin()) {

            $domainClientConfigXml = self::getDomain() ? self::getDomain()->getSessionProperties() : '';
            $domainClientConfig = $systemClientConfig;

            if ($domainClientConfigXml) {
                $domainClientConfig = new Client($domainClientConfigXml);
            }

            $domainClientClass = $domainClientConfig->getClass();

//            $sessionProperties = self::getDomain() ? self::getDomain()->getSessionProperties() : array();
//
//            $frontClientClass = $defaultClientClass;
//            $frontClientConfig = $defaultClientConfig;
//            $frontendClientStore = $defaultClientStore;
//            $frontendAutoStart = $defaultAutoStart;

//            if ($sessionProperties['class']) {
//                $frontClientClass = $sessionProperties['class'];
//                $frontClientConfig = $sessionProperties['config'];
//            }
//
//            if ($sessionProperties['autoStart']) {
//                $frontendAutoStart = $sessionProperties['autoStart'];
//            }
//
//            if ($sessionProperties['store']) {
//                $frontendClientStore = $frontClientConfig['store'];
//            }

            self::$client = new $domainClientClass($domainClientConfig);
            if ($domainClientConfig->isAutoStart()) {
                self::$client->start();
            }

        }

        //TODO, if session language differs from laoded language, do loadLanguage(newLang)
    }

    /**
     * Returns current client instance.
     * If we are in the administration area, then this return the admin client (same as getAdminClient())
     *
     * @return \Core\Client\ClientAbstract
     */
    public static function getClient()
    {
        return self::$client;
    }

    /**
     * Returns current admin client instance.
     * Only available if the call is in admin area. (Kryn::isAdmin())
     *
     * @return \Core\Client\ClientAbstract
     */
    public static function getAdminClient()
    {
        return self::$adminClient;
    }

    /**
     * Redirect the user to specified page
     *
     * @param integer $id
     * @param string  $params
     *
     * @static
     */
    public static function redirectToPage($id, $params = '')
    {
        self::redirect(self::pageUrl($id) . ($params ? '?' . $params : ''));
    }

    /**
     * Returns the requested path in the URL, without http and domain name.
     *
     * @static
     * @return string
     * @internal
     */
    public static function getRequestedPath()
    {
        return self::getRequest()->getRequestUri();

    }

    /**
     * Check whether specified pLang is a valid language
     *
     * @param string $lang
     *
     * @return bool
     * @internal
     */
    public static function isValidLanguage($lang)
    {
        if (!isset(Kryn::$config['languages']) && $lang == 'en') {
            return true;
        } //default

        if (Kryn::$config['languages']) {
            return array_search($lang, Kryn::$config['languages']) !== true;
        } else {
            return $lang == 'en';
        }
    }

    /**
     * Clears the language caches.
     *
     * @internal
     *
     * @param  null $lang
     *
     * @return bool
     */
    public static function clearLanguageCache($lang = null)
    {
        if ($lang == false) {

            $lang2s = dbTableFetch('system_langs', DB_FETCH_ALL, 'visible = 1');
            foreach ($lang2s as $lang2) {
                Kryn::clearLanguageCache($lang2['code']);
            }

            return false;
        }
        $code = 'cacheLang_' . $lang;
        Kryn::setFastCache($code, false);
    }

    /**
     * Load all translations of the specified language
     *
     * @static
     * @internal
     *
     * @param string $lang
     * @param bool   $force
     */
    public static function loadLanguage($lang = null, $force = false)
    {
        if (!$lang) {
            $lang = Kryn::getLanguage();
        }

        if (!Kryn::isValidLanguage($lang)) {
            $lang = 'en';
        }

        if (Kryn::$lang && Kryn::$lang['__lang'] && Kryn::$lang['__lang'] == $lang && $force == false) {
            return;
        }

        if (!$lang) {
            return;
        }

        $code = 'cacheLang_' . $lang;
        Kryn::$lang =& Kryn::getFastCache($code);

        $md5 = '';
        $bundles = array();
        foreach (Kryn::$bundles as $key) {
            $path = self::getBundleDir($key);
            if ($path) {
                $path .= "Resources/translations/$lang.po";
                $md5 .= @filemtime($path);
                $bundles[] = $key;
            }
        }

        $md5 = md5($md5);

        if (true || (!Kryn::$lang || count(Kryn::$lang) == 0) || Kryn::$lang['__md5'] != $md5) {

            Kryn::$lang = array('__md5' => $md5, '__plural' => Lang::getPluralForm($lang), '__lang' => $lang);

            foreach ($bundles as $key) {
                $po = Lang::getLanguage($key, $lang);
                Kryn::$lang = array_merge(Kryn::$lang, $po['translations']);
            }
            Kryn::setFastCache($code, Kryn::$lang);
        }

        include_once(Lang::getPluralPhpFunctionFile($lang));
    }

    /**
     * Cleans up the class variables. Is used in the test suite.
     */
    public static function cleanup()
    {
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
        self::$bundles = array('Core\CoreBundle', 'Admin\AdminBundle', 'Users\UsersBundle');
        self::$themes = null;
        self::$dbConnection = null;
        self::$config = null;
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
        self::$urls = null;
        //self::$cachedTempFolder = '';
    }

    /**
     * Returns Domain object
     *
     * @param int $domainId If not defined, it returns the current domain.
     *
     * @return \Core\Models\Domain
     * @static
     */
    public static function getDomain($domainId = null)
    {
        if (!$domainId) {
            return self::$domain;
        }

        if ($domainSerialized = self::getCache('core/object-domain/' . $domainId)) {
            return unserialize($domainSerialized);
        }

        $domain = Models\DomainQuery::create()->findPk($domainId);

        if (!$domain) {
            return false;
        }

        //todo, do it via setFastCache
        self::setCache('core/object-domain/' . $domainId, serialize($domain));

        return $domain;
    }

    /**
     * Returns cached propel object.
     *
     * @param  int   $objectClassName If not defined, it returns the current page.
     * @param  mixed $objectPk        Propel PK for $objectClassName int, string or array
     *
     * @return mixed Propel object
     * @static
     */
    public static function getPropelCacheObject($objectClassName, $objectPk)
    {
        if (is_array($objectPk)) {
            $npk = '';
            foreach ($objectPk as $k) {
                $npk .= urlencode($k) . '_';
            }
        } else {
            $pk = urlencode($objectPk);
        }

        $cacheKey = 'core/object-caching.' . strtolower(preg_replace('/[^\w]/', '.', $objectClassName)) . '/' . $pk;
        if ($serialized = self::getCache($cacheKey)) {
            return unserialize($serialized);
        }

        return self::setPropelCacheObject($objectClassName, $objectPk);
    }

    /**
     * Returns propel object and cache it.
     *
     * @param int   $objectClassName If not defined, it returns the current page.
     * @param mixed $objectPk        Propel PK for $objectClassName int, string or array
     * @param mixed $object          Pass the object, if you did already fetch it.
     *
     * @return \BaseObject Propel object
     */
    public static function setPropelCacheObject($object2ClassName, $object2Pk, $object = false)
    {
        $pk = $object2Pk;
        if ($pk === null && $object) {
            $pk = $object->getPrimaryKey();
        }

        if (is_array($pk)) {
            $npk = '';
            foreach ($pk as $k) {
                $npk .= urlencode($k) . '_';
            }
        } else {
            $pk = urlencode($pk);
        }

        $cacheKey = 'core/object-caching.' . strtolower(preg_replace('/[^\w]/', '.', $object2ClassName)) . '/' . $pk;

        $clazz = $object2ClassName . 'Query';
        $object2 = $object;
        if (!$object2) {
            $object2 = $clazz::create()->findPk($object2Pk);
        }

        if (!$object2) {
            return false;
        }

        self::setCache($cacheKey, serialize($object2));

        return $object2;

    }

    /**
     * Removes a object from the cache.
     *
     * @param int   $objectClassName If not defined, it returns the current page.
     * @param mixed $objectPk        Propel PK for $objectClassName int, string or array
     */
    public static function removePropelCacheObject($objectClassName, $objectPk = null)
    {
        $pk = $objectPk;
        if ($pk !== null) {
            if (is_array($pk)) {
                $npk = '';
                foreach ($pk as $k) {
                    $npk .= urlencode($k) . '_';
                }
            } else {
                $pk = urlencode($pk);
            }
        }
        $cacheKey = 'core/object-caching.' . strtolower(preg_replace('/[^\w]/', '.', $objectClassName));

        if ($objectPk) {
            self::deleteCache($cacheKey . '/' . $pk);
        } else {
            self::invalidateCache($cacheKey);
        }
    }

    /**
     * Returns a super fast cached Page object.
     *
     * @param  int $pageId If not defined, it returns the current page.
     *
     * @return \Page
     * @static
     */
    public static function getPage($pageId = null)
    {
        if (!$pageId) {
            return self::$page;
        }

        $created = self::getCache('core/object.page.' . $pageId . '.created');
        $data = self::getFastCache('core/object.page.' . $pageId);

        if ($data && $created == $data['!created']) {
            return unserialize($data['page']);
        }

        $page = NodeQuery::create()->findPk($pageId);

        if (!$page) {
            return false;
        }

        $data['page'] = serialize($page);
        $data['!created'] = microtime();
        self::setFastCache('core/object.page.' . $pageId, $data);
        self::setCache('core/object.page.' . $pageId . '.created', $data['!created']);

        return $page;

    }

    /**
     * Reads the requested URL and try to extract the requested language.
     *
     * @return string Empty string if nothing found.
     * @internal
     */
    public static function getPossibleLanguage()
    {
        $uri = self::getRequest()->getRequestUri();

        if (strpos($uri, '/') > 0) {
            $first = substr($uri, 0, strpos($uri, '/'));
        } else {
            $first = $uri;
        }

        if (self::isValidLanguage($first)) {
            return $first;
        }

        return '';
    }

    /**
     * Tries to detect the domain/host name, if they is available in this system.
     *
     * @static
     *
     * @param  bool $noRefreshCache
     *
     * @return Domain|null
     *
     * @event core/domain-redirect
     * @event core/domain-not-found
     */
    public static function detectDomain($noRefreshCache = false)
    {
        $request = self::getRequest();
        $dispatcher = self::getEventDispatcher();
        if (static::isEditMode() && $domainId = $request->get('_kryn_editor_domain')) {
            $hostname = DomainQuery::create()->select('domain')->findPk($domainId);
        } else {
            $hostname = $request->get('_kryn_domain') ? : $request->getHost();
        }

        $possibleLanguage = self::getPossibleLanguage();
        $hostnameWithLanguage = $hostname . '/' . $possibleLanguage;

        $cachedDomains = self::getFastCache('core/domains');
        $cachedDomainsCreated = self::getCache('core/domains.created'); //for loadBalanced scenarios

        if ($cachedDomains) {
            $cachedDomains = \unserialize($cachedDomains);
        }

        if (!$cachedDomains || $cachedDomains['!created'] != $cachedDomainsCreated) {

            $cachedDomains = array();
            $domains = Models\DomainQuery::create()->find();
            foreach ($domains as $domain) {
                $key = $domain->getDomain();
                $langKey = '';

                if (!$domain->getMaster()) {
                    $langKey = '/' . $domain->getLanguage();
                }

                $cachedDomains[$key . $langKey] = $domain;

                if ($domain->getRedirect()) {
                    $redirects = $domain->getRedirect();
                    $redirects = explode(',', str_replace(' ', '', $redirects));
                    foreach ($redirects as $redirectDomain) {
                        $cachedDomains['!redirects'][$redirectDomain . $langKey] = $key . $langKey;
                    }
                }

                if ($domain->getAlias()) {
                    $aliases = $domain->getAlias();
                    $aliases = explode(',', str_replace(' ', '', $aliases));
                    foreach ($aliases as $aliasDomain) {
                        $cachedDomains['!aliases'][$aliasDomain . $langKey] = $key . $langKey;
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
        ) {
            $domain = $cachedDomains[$redirectToDomain];
            $dispatcher->dispatch('core/domain-redirect', new GenericEvent($domain));
            return null;
        }

        //search alias
        if (($aliasHostname = $cachedDomains['!aliases'][$hostnameWithLanguage]) ||
            ($aliasHostname = $cachedDomains['!aliases'][$hostname])
        ) {
            $domain = $cachedDomains[$aliasHostname];
            $hostname = $aliasHostname;
        } else {
            $domain = $cachedDomains[$hostname];
        }

        if (!$domain) {
            $dispatcher->dispatch('core/domain-not-found', new GenericEvent($hostname));
            return;
        }

        $domain->setRealDomain($hostname);

        return $domain;
    }

    /**
     * Setups the HTTPKernel.
     */
    public static function setupHttpKernel()
    {
        $dispatcher = self::getEventDispatcher();

        $dispatcher->addListener(
            KernelEvents::EXCEPTION,
            function (GetResponseForExceptionEvent $event) {
                Utils::exceptionHandler($event->getException());
            }
        );

        $dispatcher->addListener(
            KernelEvents::VIEW,
            function (GetResponseForControllerResultEvent $event) {

                $data = $event->getControllerResult();

                if (null !== $data) {
                    if ($data instanceof PluginResponse) {
                        $response = $data;
                    } else {
                        $response = new PluginResponse($data);
                    }
                    $response->setControllerRequest($event->getRequest());
                    $event->setResponse($response);
                } else {
                    $content = $event->getRequest()->attributes->get('_content');
                    if ($content) {
                        $foundRoute = false;
                        foreach (Kryn::$routes as $idx => $route) {
                            /** @var \Symfony\Component\Routing\Route $route */
                            if ($content == $route->getDefault('_content')) {
                                Kryn::$routes->remove($idx);
                                $foundRoute = true;
                                break;
                            }
                        }
                        if ($foundRoute) {
                            //we've remove the route and fire now again a sub request
                            Kryn::getRequest()->attributes = new ParameterBag();
                            $response = Kryn::getHttpKernel()->handle(
                                Kryn::getRequest(),
                                HttpKernelInterface::SUB_REQUEST
                            );
                            $event->setResponse($response);

                            return;
                        }
                    }
                }
            }
        );

    }

    /**
     * Attaches the page and their plugin routes as routeCollection to the the event dispatcher
     * for later usage in HTTPKernel.
     */
    public static function setupPageRoutes()
    {
        $dispatcher = self::getEventDispatcher();

        if (!Kryn::$page) {
            $page = self::searchPage();

            if (!$page) {
                $dispatcher->dispatch('core/page-not-found');
                return;
            }

            Kryn::$page = self::getPage($page);

            if (!$page) {
                $dispatcher->dispatch('core/page-not-found');
                return;
            }
        }

        Kryn::$page = self::checkPageAccess(Kryn::$page);

        $dispatcher->dispatch('core/set-page', new GenericEvent(Kryn::$page));

        $routes = new RouteCollection();
        $dispatcher->dispatch('core/setup-routes-pre', new GenericEvent($routes));

        $clazz = 'Core\\PageController';
        $domainUrl = (!Kryn::$domain->getMaster()) ? '/' . Kryn::$domain->getLang() : '';
        $url = $domainUrl . Node::getUrl(Kryn::$page);

        $controller = $clazz . '::handle';

        if ('' !== $url && '/' !== $url && Kryn::$domain && Kryn::$domain->getStartnodeId() == Kryn::$page->getId()) {
            //This is the start page, so add a redirect controller
            $routes->add(
                $routes->count() + 1,
                new Route(
                    $url,
                    array('_controller' => $clazz . '::redirectToStartPage')
                )
            );

            $url = $domainUrl;
        }

        $routes->add(
            $routes->count() + 1,
            new Route(
                $url,
                array('_controller' => $controller)
            )
        );

        $cacheKey = 'core/node/plugins-' . Kryn::$page->getId();
        $plugins = Kryn::getDistributedCache($cacheKey);

        if ($plugins === null) {
            $plugins = ContentQuery::create()
                ->filterByNodeId(Kryn::$page->getId())
                ->filterByType('plugin')
                ->find();

            Kryn::setDistributedCache($cacheKey, serialize($plugins));
        } else {
            $plugins = unserialize($plugins);
        }

        foreach ($plugins as $plugin) {
            if (!$plugin->getContent()) {
                continue;
            }
            $data = json_decode($plugin->getContent(), true);
            if (!$data) {
                self::getLogger()->addAlert(
                    tf(
                        'On page `%s` [%d] is a invalid plugin `%d`.',
                        Kryn::$page->getTitle(),
                        Kryn::$page->getId(),
                        $plugin->getId()
                    )
                );
                continue;
            }

            $bundleName = $data['module'] ? : $data['bundle'];

            $config = Kryn::getConfig($bundleName);
            if (!$config) {
                self::getLogger()->addAlert(
                    tf(
                        'Bundle `%s` for plugin `%s` on page `%s` [%d] does not not exist.',
                        $bundleName,
                        $data['plugin'],
                        Kryn::$page->getTitle(),
                        Kryn::$page->getId()
                    )
                );
                continue;
            }

            $pluginDefinition = $config->getPlugin($data['plugin']);

            if (!$pluginDefinition) {
                self::getLogger()->addAlert(
                    tf(
                        'In bundle `%s` the plugin `%s` on page `%s` [%d] does not not exist.',
                        $bundleName,
                        $data['plugin'],
                        Kryn::$page->getTitle(),
                        Kryn::$page->getId()
                    )
                );
                continue;
            }

            if ($pluginRoutes = $pluginDefinition->getRoutes()) {
                foreach ($pluginRoutes as $route) {

                    $clazz = $pluginDefinition->getClass();
                    if (false !== strpos($clazz, '\\')) {
                        $controller = $clazz . '::' . $pluginDefinition->getMethod();
                    } else {
                        $controller = $clazz . '\\' . $pluginDefinition->getClass(
                        ) . '::' . $pluginDefinition->getMethod();
                    }

                    $defaults = array(
                        '_controller' => $controller,
                        '_content' => $plugin,
                        'options' => $data['options']
                    );

                    if ($route->getDefaults()) {
                        $defaults = array_merge($defaults, $route->getArrayDefaults());
                    }

                    $routes->add(
                        $route->getId() ? : $routes->count() + 1,
                        new Route(
                            $url . '/' . $route->getPattern(),
                            $defaults,
                            $route->getArrayRequirements() ? : array()
                        )
                    );
                }
            }
        }

        $dispatcher->dispatch('core/setup-routes', new GenericEvent($routes));

        self::$routes = $routes;
        $matcher = new UrlMatcher(self::$routes, new RequestContext());
        $routerListener = new RouterListener($matcher);
        $dispatcher->addSubscriber($routerListener);
    }

    public static function loadSystemConfig()
    {
        $fastestCacheClass = Cache\Controller::getFastestCacheClass();
        $configFile = PATH . 'app/config/config.xml';

        if (file_exists($configFile)) {
            if ('\Core\Cache\Files' === $fastestCacheClass->getClass()) {
                $systemConfigCached = @file_get_contents('app/config/config.cache.php');
            } else {
                $systemConfigCached = static::getFastCache('core/config');
            }
            $systemConfigHash   = md5($fastestCacheClass->getClass() . filemtime($configFile));

            if ($systemConfigCached) {
                $systemConfigCached = unserialize($systemConfigCached);
                if (is_array($systemConfigCached) && $systemConfigCached['md5'] == $systemConfigHash){
                    self::$config = $systemConfigCached['data'];
                }
            }

            if (!self::$config) {
                static::$config = new SystemConfig(file_get_contents($configFile));
                $cached = serialize([
                    'md5'  => $systemConfigHash,
                    'data' => self::$config
                ]);
                if ('\Core\Cache\Files' === $fastestCacheClass->getClass()) {
                    @file_put_contents('app/config/config.cache.php', $cached);
                } else {
                    self::setFastCache('core/config', $cached);
                }
            }
        } else {
            static::$config = new SystemConfig();
        }
    }

    /**
     * Bootstrap.
     *
     * @param null $loader
     */
    public static function bootstrap($loader = null)
    {
        if ($loader) {
            $loader->add('', __DIR__ . '/../../tests/bundles');
            self::$loader = $loader;
        }

        //load main config, setup some constants and check some requirements.
        require(__DIR__ . '/bootstrap.php');

        /**
         * Check and loading config.php or redirect to install.php
         */
        $configFile = 'app/config/config.xml';

        if (!file_exists($configFile) && !defined('KRYN_INSTALLER') && false === strpos($_SERVER['PHP_SELF'], 'install.php')) {
            header("Location: install.php");
            exit;
        }

        self::loadSystemConfig();

        if (!defined('pfx')) {
            define('pfx', self::$config->getDatabase(true)->getPrefix());
        }

        if (false !== strpos($_SERVER['PHP_SELF'], 'install.php')) {
            try {
                self::getLoader()->add('', self::getTempFolder() . 'propel-classes/');
            } catch (\Exception $e){
                //catch it silence.
            }
            return;
        }

        self::getLoader()->add('', self::getTempFolder() . 'propel-classes/');

        self::checkStaticCaching();

        $http = 'http://';
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on')) {
            $http = 'https://';
        }

        $host = $_SERVER['HTTP_HOST'];
        if (!$host) {
            $port = '';
            if (($_SERVER['SERVER_PORT'] != 80 && $http == 'http://') ||
                ($_SERVER['SERVER_PORT'] != 443 && $http == 'https://')
            ) {
                $port = ':' . $_SERVER['SERVER_PORT'];
            }
            $host = $_SERVER['SERVER_NAME'] . $port;
        }

        self::setBaseUrl($http . $host . str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));

        /**
         * Load active modules into Kryn::$bundles.
         */
        self::loadActiveModules();

        if (isset($cfg['timezone'])) {
            date_default_timezone_set($cfg['timezone']);
        }

        if (isset($cfg['locale'])) {
            setlocale(LC_ALL, $cfg['locale']);
        }


        @ini_set('display_errors', 1);

        if (!self::getSystemConfig()->getErrors()->getDisplay()) {
            @ini_set('display_errors', 0);
        }

        if (!defined('KRYN_TESTS')) {
            if (self::$config->getErrors()->getDisplay()) {
                set_exception_handler("coreUtilsExceptionHandler");
                set_error_handler(
                    "coreUtilsErrorHandler",
                    E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR | E_ERROR | E_CORE_ERROR | E_USER_ERROR | E_PARSE
                );
            }
            register_shutdown_function('coreUtilsShutdownHandler');
        }

        /*
         * Propel orm initialisation.
         */
        if (!PropelHelper::loadConfig()) {
            PropelHelper::init();
            PropelHelper::loadConfig();
        }

        /**
         * Initialize caching controllers
         */
        self::initCache();

        /*
         * Load themes and configs, setup the config
         */
        self::loadModuleConfigs();

        /*
         * Load current language
         */
        self::loadLanguage();

        /*
         * Initialize the client objects for backend and frontend.
         */
        Kryn::initClient();
    }

    /**
     * Starts up the application.
     */
    public static function startup($loader = null)
    {
        self::bootstrap($loader);

        //Setup the HTTPKernel.
        self::setupHttpKernel();

        //Handle the request.
        self::handleRequest();
    }

    /**
     * @param $bundleName
     *
     * @return Config\Bundle
     */
    public static function getConfig($bundleName)
    {
        return self::$configs ? self::$configs->getConfig($bundleName) : null;
    }

    /**
     * @return \Core\Config\Bundle[]|\Core\Config\Configs
     */
    public static function getConfigs()
    {
        return self::$configs;
    }

    /**
     * Check whether we have a static version of the current request.
     * If so and if static caching is activated, it prints the cached html
     * and exits.
     *
     */
    public static function checkStaticCaching()
    {
        //caching
        if (true || Kryn::isEditMode()) {
            return;
        }

        $key = md5(self::getRequest()->getRequestUri());
        $caching = Kryn::getCache('core/static-caching');

        if (!$caching) {
            //reload static-caching index
        }

        if ($caching[$key] && file_exists($file = 'media/cache/core/static.' . $key . '.html')) {

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
     * @event core/response-send-pre
     * @event core/response-send
     */
    public static function handleRequest()
    {
        if (self::isAdmin()) {
            $admin = new \Admin\Controller\AdminController();
            return $admin->run();
        }

        $kernel = self::getHttpKernel();
        $request = self::getRequest();
        $dispatcher = self::getEventDispatcher();

        $dispatcher->addListener(
            'core/domain-redirect',
            function (GenericEvent $event) {
                $domain = $event->getSubject();
                $response = new \Symfony\Component\HttpFoundation\RedirectResponse($domain->getUrl(Kryn::$ssl), 301);
                $response->send();
                exit;
            }
        );

        $dispatcher->addListener(
            'core/domain-not-found',
            function (GenericEvent $event) {
                throw new \LogicException(tf('Domain `%s` not found.', $event->getSubject()));
            }
        );

        $dispatcher->addListener(
            'core/domain-no-start-page',
            function () {
                throw new \LogicException(tf('There is no start page for domain `%s`.', Kryn::$domain->getDomain()));
            }
        );

        if ($nodeId = $request->get('_kryn_editor_node')) {
            Kryn::$page = self::getPage($request->get('_kryn_editor_node'));
            if (Kryn::isEditMode()){
                Kryn::$domain = static::getDomain(Kryn::$page->getDomainId());

                $hostname = $request->get('_kryn_editor_host') ?: $request->getHost();
                $path     = $request->get('_kryn_editor_path') ?: $request->getBasePath();

                if ($layout = $request->get('_kryn_editor_layout')){
                    Kryn::$page->setLayout($layout);
                }

                Kryn::$domain->setRealDomain($hostname);
                Kryn::$domain->setPath($path);
                $response = Kryn::getResponse();
            } else {
                Kryn::internalError('Access Denied', 'No Access.');
            }
        } else {
            //search domain and set to Core\Kryn::$domain
            Kryn::$domain = self::detectDomain();

            //setup page/plugin routes
            self::setupPageRoutes();

            $response = $kernel->handle($request);
        }

        if ($response instanceof PluginResponse) {
            $response = Kryn::getResponse()->setPluginResponse($response);
        }

        //caching
        if (false && !Kryn::isEditMode()) {
            //todo
            $dispatcher->addListener(
                'core.response-send-pre',
                function () {
                    $caching = Kryn::getCache('core/static-caching');
                    $key = md5(Kryn::getRequest()->getRequestUri());
                    $caching[$key] = true;
                    $file = 'media/cache/core/static.' . $key . '.html';
                    file_put_contents($file, Kryn::getResponse()->getContent());
                    Kryn::setCache('core/static-caching', $caching, 60);
                }
            );
        }

        if (Kryn::isEditMode() && $response instanceof PageResponse) {
            \Admin\Controller\AdminController::handleKEditor();
        }

        self::sendResponse($response);
    }

    /**
     * Sends the response and exits the process.
     *
     * @event core/response-send-pre
     * @event core/response-send
     *
     * @param Response $response
     */
    public static function sendResponse(Response $response)
    {
        global $_start;
        $dispatcher = self::getEventDispatcher();

        $dispatcher->dispatch('core.response-send-pre', new GenericEvent($response));
        $response->send();
        $dispatcher->dispatch('core.response-send', new GenericEvent($response));

        Kryn::getLogger()->addDebug('Done. Generation time: ' . (microtime(true) - $_start) . ' seconds.');

    }

    /**
     * Checks if we're in the frontend editor mode.
     * Only true if ?_kryn_editor=1 is set and the current user has update-access to the Core\\Node object.
     *
     * @return bool
     */
    public static function isEditMode()
    {
        return 1 == Kryn::getRequest()->get('_kryn_editor') && Kryn::$page && Permission::checkUpdate(
            'core/Node',
            Kryn::$page->getId()
        );
    }

    /**
     * @static
     * @return string
     */
    public static function getBaseUrl()
    {
        return Kryn::$baseUrl;
    }

    /**
     * @static
     */
    public static function setBaseUrl($baseUrl)
    {
        Kryn::$baseUrl = $baseUrl;
    }

    /**
     * $admin is initialised during the bootstrap.php and is basically:
     *
     *     Core\Kryn::$admin = (getArgv(1) == 'admin');
     *
     * @return bool
     */
    public static function isAdmin()
    {
        $adminUrl = '/' . self::getAdminPrefix() . '/';
        return strpos(self::getRequest()->getPathInfo() . '/', $adminUrl) === 0;
    }

    public static function getAdminPrefix()
    {
        return self::$config->getAdminUrl();
    }

    /**
     * Checks the specified page.
     * Internal function.
     *
     * @param Node $page
     * @param bool $withRedirect
     *
     * @return array|bool False if no access
     * @internal
     */
    public static function checkPageAccess(Node $page, $withRedirect = true)
    {
        $oriPage = $page;

        if ($page->getAccessFrom() > 0 && ($page->getAccessFrom() > time())) {
            $page = false;
        }

        if ($page->getAccessTo() > 0 && ($page->getAccessTo() < time())) {
            $page = false;
        }

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

        if (!$page && $withRedirect && $oriPage->getAccessNeedVia() == 0) {

            if ($oriPage->getAccessRedirectto() + 0 > 0) {
                Kryn::redirectToPage($oriPage->getAccessRedirectto());
            }
        }

        if (!$page && $withRedirect && $oriPage->getAccessNeedVia() == 1) {
            header(
                'WWW-Authenticate: Basic realm="' .
                    t('Access denied. Maybe you are not logged in or have no access.') . '"'
            );
            header('HTTP/1.0 401 Unauthorized');

            exit;
        }

        return $page;
    }

    /**
     * Returns the domain of the given $id page.
     *
     * @static
     *
     * @param  integer $id
     *
     * @return integer|null
     */
    public static function getDomainOfPage($id)
    {
        $id2 = null;

        $page2Domain = Kryn::getDistributedCache('core/node/toDomains');

        if (!is_array($page2Domain)) {
            $page2Domain = Render::updatePage2DomainCache();
        }

        $id = ',' . $id . ',';
        foreach ($page2Domain as $domain_id => &$pages) {
            $pages = ',' . $pages . ',';
            if (strpos($pages, $id) !== false) {
                $id2 = $domain_id;
            }
        }

        return $id2;
    }

    /**
     * Returns a array with all urls to id pairs.
     *
     * @param  integer $domainId
     *
     * @return array
     */
    public static function &getCachedUrlToPage($domainId)
    {

        $cacheKey = 'core/urls/' . $domainId;
        $urls = self::getDistributedCache($cacheKey);

        if (!$urls) {

            $nodes = NodeQuery::create()
                ->select(array('id', 'urn', 'lvl', 'type'))
                ->filterByDomainId($domainId)
                ->orderByBranch()
                ->find();

            //build urls array
            $urls = array();
            $level = array();

            foreach ($nodes as $node) {
                if ($node['lvl'] == 0) {
                    continue;
                } //root
                if ($node['type'] == 3) {
                    continue;
                } //deposit

                if ($node['type'] == 2 || $node['urn'] == '') {
                    //folder or empty url
                    $level[$node['lvl'] + 0] = ($level[$node['lvl'] - 1]) ? : '';
                    continue;
                }

                $url = ($level[$node['lvl'] - 1]) ? : '';
                $url .= '/' . $node['urn'];

                $level[$node['lvl'] + 0] = $url;

                $urls[$url] = $node['id'];
            }

            self::setDistributedCache($cacheKey, $urls);

        }

        return $urls;
    }

    /**
     * @param  integer $domainId
     *
     * @return array
     */
    public static function &getCachedPageToUrl($domainId)
    {
        static $flipped;

        if (!$flipped) {
            $flipped = array_flip(self::getCachedUrlToPage($domainId));
        }

        return $flipped;
    }

    /**
     * Search the current page or the start page, loads all information and checks the access.
     *
     * @internal
     * @return int
     */
    public static function searchPage()
    {
        $url = self::getRequest()->getPathInfo();

        $domain = Kryn::$domain->getId();
        $urls = self::getCachedUrlToPage($domain);

        //extract extra url attributes
        $found = $end = false;
        $possibleUrl = $next = $url;
        $oriUrl = $possibleUrl;

        do {

            $id = $urls[$possibleUrl];

            if ($id > 0 || $possibleUrl == '') {
                $found = true;
            } elseif (!$found) {
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
            if ($pos !== false) {
                $next = substr($next, 0, $pos);
            } else {
                $next = false;
            }

        } while (!$end);

        $diff = substr($url, strlen($possibleUrl), strlen($url));

        if (substr($diff, 0, 1) != '/') {
            $diff = '/' . $diff;
        }

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
                self::getEventDispatcher()->dispatch('core/domain-no-start-page');
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
    public static function loadBreadcrumb()
    {
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
    public static function notFound()
    {
        $msg = sprintf(t('Route not found %s'), Kryn::$domain->getDomain() . '/' . Kryn::getRequestedPath(true));
        klog('404', $msg);
        self::internalError('Not Found', $msg);
        exit;
    }

    public static function getInstance()
    {
        return self::$instance ? : self::$instance = new Kryn();
    }

    /**
     * Prints the internal-error.tpl template to the client and exist.
     *
     * @param string $title
     * @param string $message
     * @param array  $data
     */
    public static function internalError($title = '', $message, $data = null)
    {
        $data = $data ? : array();
        $data['title'] = $title ? : 'Internal system error';
        $data['msg'] = $message;
        $response = new Response(
            self::getInstance()->renderView('@CoreBundle/internal-message.html.smarty', $data, false),
            404
        );
        $response->send();
        exit;
    }

    /**
     * Prints the Kryn/internal-message.tpl template to the client and exist.
     *
     * @static
     *
     * @param $title
     * @param $msg
     */
    public static function internalMessage($title, $msg = '')
    {
        tAssign('title', $title);
        tAssign('msg', $msg);
        print tFetch('kryn/internal-message.tpl');
        exit;
    }

    /**
     * Returns the wrapped content with the unsearchable block tags.
     *
     * @static
     *
     * @param string $content
     *
     * @return string Wrapped content
     */
    public static function unsearchable($content)
    {
        return '<!--unsearchable-begin-->' . $content . '<!--unsearchable-end-->';
    }

    /**
     * Removes all unsearchable block tags.
     *
     * @static
     *
     * @param string $html
     */
    public static function removeSearchBlocks(&$html)
    {
        $html = str_replace('<!--unsearchable-begin-->', '', $html);
        $html = str_replace('<!--unsearchable-end-->', '', $html);
    }

    /**
     * Deactivates the 404 content check
     */
    public static function disableSearchEngine()
    {
        self::$disableSearchEngine = true;

    }

    /**
     * Compress given string
     *
     * @param string $string
     *
     * @return string
     * @static
     * @internal
     */
    public static function compress($string)
    {
        $res = $string;
        $res = preg_replace('/\s\s+/', ' ', $res);
        $res = preg_replace('/\t/', '', $res);
        $res = preg_replace('/\n\n+/', "\n", $res);

        return $res;
    }

    /**
     * Removes a value for the specified cache-key
     *
     * @param string $key
     */
    public static function deleteCache($key)
    {
        if (!self::$cache) {
            self::initCache();
        }
        Kryn::$cache->delete($key);
    }

    /**
     * Sets a distributed cache.
     *
     * This may be a distributed cache controller. If you want to make
     * sure, that your cache is known distributed over several Kryn.cms
     * installations and is at the same time very fast, then you should use
     * `setDistributedCache`. But if you have a huge amount of data to cache
     * you're probably better with this method, since the `setDistributedCache`
     * uses `APC` or another high-performance local caching and its very limited in RAM.
     *
     * @param  string  $key
     * @param  string  $value    Only simple data types. Serialize your value if you have objects/arrays.
     * @param  integer $lifeTime In seconds. Default is one hour
     *
     * @return boolean
     * @static
     */
    public static function setCache($key, $value, $lifeTime = null)
    {
        if (!self::$cache) {
            self::initCache();
        }

        return Kryn::$cache->set($key, $value, $lifeTime);
    }

    /**
     * Marks a code as invalidate beginning at $time.
     * This is the distributed cache controller. Use it if you want
     * to invalidate caches on a distributed backend (use by `setCache()`
     * and `setDistributedCache()`.
     *
     * You don't have to define the full key, instead you can pass only the starting part of the key.
     * This means, if you have following caches defined:
     *
     *   - news/list/2
     *   - news/list/3
     *   - news/list/4
     *   - news/comments/134
     *   - news/comments/51
     *
     * you can mark all listing caches as invalid by calling
     *   - invalidateCache('news/list');
     *
     * or mark all caches as invalid which starts with `news/` you can call:
     *   - invalidateCache('news');
     *
     *
     * The invalidation mechanism explodes the key by / and checks all levels whether they're marked
     * as invalid (through a microsecond timestamp) or not.
     *
     * Default is $time is `mark all caches as invalid which are older than CURRENT`.
     *
     * @param  string  $key
     * @param  integer $time Unix timestamp. Default is microtime(true). Uses float for ms.
     *
     * @return boolean
     */
    public static function invalidateCache($key, $time = null)
    {
        if (!self::$cache) {
            self::initCache();
        }

        return Kryn::$cache->invalidate($key, $time ?: microtime(true));
    }

    /**
     * Returns the content of the specified cache-key.
     * This is the distributed cache controller. Use it if you want
     * to store/retrieve caches on a distributed backend.
     *
     * @param string $key
     *
     * @return string
     * @static
     */
    public static function &getCache($key)
    {
        if (!self::$cache) {
            self::initCache();
        }

        return Kryn::$cache->get($key);
    }

    /**
     * Returns a distributed cache.
     *
     * @see \Core\Kryn::setDistributedCache for more information
     *
     * @param string $key
     *
     * @return mixed Null if not found
     */
    public static function getDistributedCache($key)
    {
        static::initFastCache();
        $invalidationKey = $key . '/!invalidationCheck';
        $timestamp = self::getCache($invalidationKey);
        $cache = null;

        if ($timestamp !== null) {
            $cache = self::getFastCache($key);
            if ($cache['timestamp'] == $timestamp) {
                return $cache['data'];
            }
        }

        return null;
    }

    /**
     * Sets a distributed cache.
     *
     * This stores a ms timestamp on the distributed cache (Kryn::setCache())
     * and the actual data on the high-speed cache driver (Kryn::setFastCache()).
     * This mechanism makes sure, you gain the maximum performance by using the
     * fast cache driver to store the actual data and using the distributed cache driver
     * to store a ms timestamp where we can check (over several kryn.cms installations)
     * whether the cache is still valid or not.
     *
     * Use Kryn::invalidateCache($key) to invalidate this cache.
     * You don't have to define the full key, instead you can pass only a part of the key.
     *
     * @see \Core\Kryn::invalidateCache for more information.
     *
     * Don't mix the usage of getDistributedCache() and getCache() since this method
     * stores extra values at the value, which makes getCache() returning something invalid.
     *
     * @param string $key
     * @param mixed  $value    Only simple data types. Serialize your value if you have objects/arrays.
     * @param int    $lifeTime
     *
     * @return boolean
     * @static
     */
    public static function setDistributedCache($key, $value, $lifeTime = null)
    {
        static::initFastCache();
        $invalidationKey = $key . '/!invalidationCheck';
        $timestamp = microtime();

        $cache['data'] = $value;
        $cache['timestamp'] = $timestamp;

        return Kryn::setFastCache($key, $cache, $lifeTime) && Kryn::setCache(
            $invalidationKey,
            $timestamp,
            $lifeTime
        );
    }

    public static function initFastCache()
    {
        if (!self::$cacheFast) {
            $fastestCacheClass = Cache\Controller::getFastestCacheClass();
            Kryn::$cacheFast = new Cache\Controller($fastestCacheClass);
        }
    }

    /**
     * Sets a local cache.
     *
     * This function saves the value in a generated php file
     * as php code, via apc_store or other high-performance caching driver.
     *
     * If you want a distributed cache, use `setDistributedCache()`.
     *
     * @param string $key
     * @param string $value    Only simple data types. Serialize your value if you have objects/arrays.
     * @param int    $lifeTime
     *
     * @return boolean
     * @static
     */
    public static function setFastCache($key, $value, $lifeTime = null)
    {
        static::initFastCache();

        return Kryn::$cacheFast->set($key, $value, $lifeTime);
    }

    /**
     * Returns the content of the specified cache-key.
     * See Kryn::setFastCache for more information.
     *
     * @param  string $key
     *
     * @return boolean
     * @static
     */
    public static function &getFastCache($key)
    {
        static::initFastCache();

        return Kryn::$cacheFast->get($key);
    }

    /**
     * Returns the servers temp folder, where you should store dynamic generated stuff.
     *
     * You can access these files also through
     * the \Core\TempFile class as you would with the \Core\File class.
     *
     * @static
     * @internal
     *
     * @param bool $withKrynContext  Adds the 'id' value of the config as sub folder. This makes sure, multiple kryn installations
     *                                does not overwrite each other files.
     *
     * @return string           Path with trailing slash
     * @throws \FileIOException
     */
    public static function getTempFolder($withKrynContext = true)
    {
        if (!self::$cachedTempFolder) {

            $folder = static::$config->getTempDir();
            if (!$folder && getenv('TMP')) {
                $folder = getenv('TMP');
            }
            if (!$folder && getenv('TEMP')) {
                $folder = getenv('TEMP');
            }
            if (!$folder && getenv('TMPDIR')) {
                $folder = getenv('TMPDIR');
            }
            if (!$folder && getenv('TEMPDIR')) {
                $folder = getenv('TEMPDIR');
            }

            if (!$folder) {
                $folder = sys_get_temp_dir();
            }

            self::$cachedTempFolder = $folder;

            if (substr(self::$cachedTempFolder, -1) != DIRECTORY_SEPARATOR) {
                self::$cachedTempFolder .= DIRECTORY_SEPARATOR;
            }
        }

        if ($withKrynContext) {

            //add our id to folder, so this installation works inside of a own directory.
            $folder = self::$cachedTempFolder . self::getId() . DIRECTORY_SEPARATOR;

            return $folder;
        }

        return self::$cachedTempFolder;
    }

    /**
     * Returns the installation id.
     *
     * @return string
     */
    public static function getId()
    {
        return 'kryn-' . (self::$config->getId() ? : 'no-id');
    }

    /**
     * Creates a temp folder and returns its path.
     * Please use TempFile::createFolder() class instead.
     *
     * @static
     * @internal
     *
     * @param  string $prefix
     * @param  bool   $fullPath Returns the full path on true and the relative to the current TempFolder on false.
     *
     * @return string Path with trailing slash
     */
    public static function createTempFolder($prefix = '', $fullPath = true)
    {
        $tmp = self::getTempFolder();

        do {
            $path = $tmp . $prefix . dechex(time() / mt_rand(100, 500));
        } while (is_dir($path));

        mkdir($path);

        if ('/' !== substr($path, -1)) {
            $path .= '/';
        }

        return $fullPath ? $path : substr($path, strlen($tmp));
    }

    /**
     * Returns the bundle directory.
     *
     * @param  string $bundleName
     *
     * @return string
     */
    public static function getBundleDir($bundleName)
    {
        $bundle = self::getBundle($bundleName);
        if ($bundle) {
            return $bundle->getPath();
        }
    }

    public static function getBundleName($bundleName, $withoutSuffix = false)
    {
        $bundle = self::getBundle($bundleName);
        if ($bundle) {
            return $bundle->getName($withoutSuffix);
        }
    }

    /**
     * @param string $bundleName
     * @param bool   $activeCheck
     *
     * @return Bundle
     */
    public static function getBundle($bundleName, $activeCheck = true)
    {
        preg_match('/\@+([a-zA-Z0-9\-_\\\\]+)/', $bundleName, $matches);
        if (0 !== count($matches)) {
            $clazz = str_replace('.', '\\', $matches[1]);
        } else {
            if ('bundle' !== strtolower(substr($bundleName, -6))) {
                $bundleName .= 'Bundle';
            }
            $clazz = $bundleName;
        }

        $clazzIdx = strtolower($clazz);
        if (self::$bundleInstances[$clazzIdx]) {
            return self::$bundleInstances[$clazzIdx];
        }

        if ($activeCheck) {
            if (!in_array($clazz, static::$bundles)) {
                return null;
            }
        }

        if (!class_exists($clazz)) {
            return null;
        }

        $bundle = new $clazz();
        self::$bundleInstances[$clazzIdx] = $bundle;
        self::$bundleInstances[strtolower($bundle->getName())] = $bundle;

        return self::$bundleInstances[$clazzIdx];
    }

    /**
     * Resolves a internal path (`@PublicationBundle/Resources/view/folder`, `Resources/views`) into the real filesystem path.
     * => `vendor/krynlabs/publication-bundle/Resources/views/in/view/folder.
     *
     * @param        $path
     * @param string $suffix Will be appended onto the replaced module path
     *
     * @return mixed
     * @throws Exceptions\BundleNotFoundException
     */
    public static function resolvePath($path, $suffix = '')
    {
        if (strpos($path, '@') !== false) {
            preg_match_all('/(\@[a-zA-Z0-9\-_\.\\\\]+)/', $path, $matches);
            if ($matches) {
                foreach ($matches as $match) {
                    $dir = self::getBundleDir($match[0]);
                    if (!$dir) {
                        throw new BundleNotFoundException(sprintf('Bundle for `%s` not found. [%s]', $match[0], json_encode(array_keys(static::$bundleInstances))));
                    }
                    $path = str_replace($match[0], $dir . $suffix, $path);
                }
            }
        }
        return $path;
    }

    /**
     * @param        $path
     * @param string $suffix Will be appended onto the replaced module path
     *
     * @return mixed
     * @throws Exceptions\BundleNotFoundException
     */
    public static function resolvePublicPath($path, $suffix = '')
    {
        if (strpos($path, '@') !== false) {
            preg_match_all('/(\@[a-zA-Z0-9\-_\.]+)/', $path, $matches);
            if ($matches) {
                foreach ($matches as $match) {
                    $dir = strtolower(\Core\Kryn::getBundleName($match[0], true));
                    if (!$dir) {
                        throw new BundleNotFoundException(sprintf('Bundle for `%s` not found.', $match[0]));
                    }
                    $path = str_replace($match[0], 'bundles/' . $dir . $suffix, $path);
                }
            }
        }
        return $path;
    }

    /**
     * Replaces all object://<objectKey>/<pk> strings by its real url.
     *
     * @param  string $html
     *
     * @return string
     */
    public static function parseObjectUrls($html)
    {
        return preg_replace_callback(
            '|object://([a-zA-Z0-9\.\\\\]+)/([^"/,]+)|',
            '\\Core\\Kryn::replaceObjectUrl',
            $html
        );

    }

    public static function replaceObjectUrl($match)
    {
        return Object::getPublicUrl($match[1], $match[2]);
    }

    /**
     * Returns the URL of the specified page
     *
     * @param integer  $id
     * @param boolean  $absolute
     * @param bool|int $domainId
     *
     * @return string
     * @static
     */
    public static function pageUrl($id = 0, $absolute = false, $domainId = false)
    {
        return 'object://node/' . $id;
    }

    public static function urlEncode($string)
    {
	    $string = rawurlencode($string);
        $string = str_replace('%2F', '%25252F', $string);
        return $string;
    }

    public static function urlDecode($string)
    {
        $string = str_replace('%25252F', '%2F', $string);
        return rawurldecode($string);
    }

}

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
     * Contains all additional html header values.
     * Use Kryn::addHeader( $pHeader ) to add additional headers.
     * @var array
     * @internal
     * $static
     */
    public static $header = array();

    /**
     * Contains all paths to javascript files as each item
     * Use Kryn::addJs( $pPath ) to add javascript files.
     * @var array
     * @internal
     * $static
     */
    public static $jsFiles = array();

    /**
     * Contains all paths to css files as each item.
     * Use Kryn::addCss( $pPath ) to add css files.
     * @var array
     * @internal
     * $static
     */
    public static $cssFiles = array('css/kryn_defaults.css');

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
     * @var \Domain
     *
     * @static
     */
    public static $domain;

    /**
     * Contains the current page with all information
     * @var \Page
     * @static
     */
    public static $page;

    /**
     * Contains the current page with all information as copy
     * @var array ref
     * @static
     */
    public static $current_page;

    /**
     * State where describes, krynContent should really write content
     * @var boolean
     * @static
     */
    public static $forceKrynContent;

    /**
     * Contains the complete builded HTML.
     * To change this, you can changed it on the destructor in your extension-class.
     * @var string
     * @static
     */
    public static $pageHtml;

    /**
     * Contains the current requested URL without http://, urlencoded
     * use urldecode(htmlspecialchars(Kryn::$url)) to display it in your page.
     * @var string
     */
    public static $url;

    /**
     * Contains the current requested URL without http:// and with _GET params, urlencoded
     * use urldecode(htmlspecialchars(Kryn::$urlWithGet)) to display it in your page.
     * @var string
     * @static
     */
    public static $urlWithGet;

    /**
     * Contains the values of the properties from current theme.
     * Template: $currentTheme
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
    public static $objects = array();

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
     * Contains all installed database tables from config.json#db
     * Example: array('publication' => array('publication_news' => array([fields], 'publication_news_category' => array()))
     * @static
     * @var array
     */
    public static $tables;

    /**
     * Contains all installed extensions
     * Example: array('kryn', 'admin', 'users', 'sitemap', 'publication');
     * @var array
     * @static
     */
    public static $extensions = array('kryn', 'admin', 'users');

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
     * Cache of the propel gererated classes for the autloader.
     *
     * @var array
     */
    public static $propelClassMap = array();


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
    public static $adminClient;

    /**
     * The krynAuth object of the frontend user.
     * It's empty when we're in the backend.
     *
     * @var krynAuth
     * @static
     */
    public static $client;

    /**
     * Contains all page objects of each Render::renderPageContents() call.
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
     * Placeholder to inject own html.
     * @var string
     * @static
     */
    public static $htmlHeadTop;

    /**
     * Placeholder to inject own html.
     * @var string
     * @static
     */
    public static $htmlHeadEnd;

    /**
     * Placeholder to inject own html.
     * @var string
     * @static
     */
    public static $htmlBodyTop;

    /**
     * Placeholder to inject own html.
     * @var string
     * @static
     */
    public static $htmlBodyEnd;


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
     * Adds a new css file to the <header>. Use relative paths from media/ without a / as start
     * Absolute paths with http:// also possible.
     *
     * @param string|array $pCss
     *
     * @static
     */
    public static function addCss($pCss) {
        if (is_array($pCss)){
            foreach ($pCss as $css)
                if (!in_array($css, Kryn::$cssFiles))
                    Kryn::$cssFiles[] = $css;
        } else if (is_string($pCss) && !in_array($pCss, Kryn::$cssFiles))
            Kryn::$cssFiles[] = $pCss;
    }

    /**
     * Adds a new javascript file to the <header>. Use relative paths from media/ without a / as start
     *
     * @param string|array $pJs
     * @static
     */
    public static function addJs($pJs) {

        if (is_array($pJs)){
            foreach ($pJs as $js)
                if (!in_array($js, Kryn::$cssFiles))
                    Kryn::$jsFiles[] = $js;
        } else if (is_string($pJs) && !in_array($pJs, Kryn::$jsFiles))
            Kryn::$jsFiles[] = $pJs;
    }

    /**
     * Resets all javascript files.
     */
    public static function resetJs() {
        Kryn::$jsFiles = array();
    }

    /**
     * Resets all css files.
     */
    public static function resetCss() {
        Kryn::$cssFiles = array('css/kryn_defaults.css');
    }


    /**
     * Adds additional headers.
     *
     * @param string $pHeader
     *
     * @static
     */
    public static function addHeader($pHeader) {

        if (array_search($pHeader, Kryn::$header) === false)
            Kryn::$header[] = $pHeader;
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

    public static function loadActiveModules() {
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
            $path = ($extension == 'kryn') ? 'core/config.json' : PATH_MODULE . $extension . '/config.json';
            if (file_exists($path)) {
                $md5 .= '.' . filemtime($path);
            }
        }

        $md5 = md5($md5);

        //Kryn::$tables =& Kryn::getCache('systemTablesv2');
        Kryn::$themes =& Kryn::getFastCache('systemThemes');
        Kryn::$objects =& Kryn::getFastCache('systemObjects');

        //check if we need to load all config objects and do the extendConfig part
        if (/*!Kryn::$tables || $md5 != Kryn::$tables['__md5'] ||*/
            !Kryn::$themes || $md5 != Kryn::$themes['__md5'] ||
            !Kryn::$objects || $md5 != Kryn::$objects['__md5']
            ) {

            foreach (Kryn::$extensions as &$extension) {
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
        }

        /*
        * load object definitions
        */

        if (!Kryn::$objects || $md5 != Kryn::$objects['__md5']){

            Kryn::$objects = array();
            Kryn::$objects['__md5'] = $md5;

            foreach (Kryn::$extensions as &$extension) {

                $config = Kryn::$configs[$extension];

                if ($config['objects'] && is_array($config['objects'])){

                    foreach ($config['objects'] as $objectId => $objectDefinition){
                        $objectDefinition['_extension'] = $extension; //caching
                        Kryn::$objects[$objectId] = $objectDefinition;
                    }
                }

            }
            Kryn::setFastCache('systemObjects', Kryn::$objects);
        }
        unset(Kryn::$objects['__md5']);

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
            Kryn::setFastCache('systemThemes', Kryn::$themes);
        }
        unset(Kryn::$themes['__md5']);

    }

    /**
     * Loads all config.json from all activated extensions to Kryn::$configs.
     * @internal
     * @static
     */
    public static function loadConfigs() {

        Kryn::$configs = array();

        foreach (Kryn::$extensions as $extension) {
            Kryn::$configs[$extension] = Kryn::getModuleConfig($extension);
        }

        foreach (Kryn::$configs as &$config) {
            if (is_array($config['extendConfig'])) {
                foreach ($config['extendConfig'] as $extendModule => &$extendConfig) {
                    if (Kryn::$configs[$extendModule]) {
                        Kryn::$configs[$extendModule] =
                            array_merge_recursive_distinct(Kryn::$configs[$extendModule], $extendConfig);
                    }
                }
            }
            if ($config['db']) {
                foreach ($config['db'] as $key => &$table) {
                    if (Kryn::$tables[$key])
                        Kryn::$tables[$key] = array_merge(Kryn::$tables[$key], $table);
                    else
                        Kryn::$tables[$key] = $table;
                }
            }
        }
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
        } else if (Kryn::$adminClient) {
            return Kryn::$adminClient->getSession()->getLanguage();
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

        if ($pModule == 'kryn')
            $config = "core/config.json";
        else
            $config = PATH_MODULE . "$pModule/config.json";

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

            $json = Kryn::translate(Kryn::fileRead($config));

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
            if ($mod != 'kryn' && $mod != 'admin' && $mod != 'users') {
                $clazz = '\\'.ucfirst($mod).'\\Controller';
                if (class_exists($clazz))
                    Kryn::$modules[$mod] = new $mod();
            }
        }
    }

    public static function isActiveModule($pModuleKey){
        $pModuleKey = strtolower($pModuleKey);
        return $pModuleKey=='admin' || $pModuleKey == 'users' ||
            array_search($pModuleKey, self::$config['activeModules']);
    }

    /**
     * Sends a E-Mail in UTF-8
     *
     * @param string $pTo
     * @param string $pSubject
     * @param string $pBody
     * @param string $pFrom If not set, the Email of the current domain is used. If both is not defined the scheme is info@<currentDomain>
     *
     * @static
     */
    public static function sendMail($pTo, $pSubject, $pBody, $pFrom = false) {
        $pTo = str_replace("\n", "", $pTo);
        if (!$pFrom) {
            $pFrom = Kryn::$domain['email'];
            if ($pFrom == '')
                $pFrom = 'info@' . Kryn::$domain['domain'];
        }
        #$pTo = mb_encode_mimeheader( $pTo, 'utf-8', 'Q' );
        #$pSubject = mb_encode_mimeheader( $pSubject, 'utf-8', 'Q' );
        #$pFrom = mb_encode_mimeheader( $pFrom, 'utf-8', 'Q' );
        @mail($pTo, '=?UTF-8?B?' . base64_encode($pSubject) . '?=', $pBody,
            'From: ' . $pFrom . "\r\n" . 'Content-Type: text/plain; charset=utf-8');
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
     * @param string $pContent
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
     * @param string &$pContent
     *
     * @static
     * @internal
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
     * Checks the access to specified /admin pUrl.
     *
     * @param string $pUrl
     * @param krynAuth|bool $pClient If you want to use another user object.
     *
     * @return bool
     * @static
     */
    public static function checkUrlAccess($pUrl, $pClient = false) {

        return true;

        if (!$pClient)
            global $client;
        else
            $client = $pClient;

        if (substr($pUrl, 0, 6) != 'admin/') {
            $pUrl = 'admin/' . $pUrl;
        }

        /*
            types:
                1: admin ($admin) and frontend
                2: pages (backend access for special uses)
                3: files (internal)

            target_type:
                1: group
                2: user
        */

        $inGroups = $client->user['inGroups'];
        if (!$inGroups) $inGroups = 0;

        $code = esc($pUrl);
        if (substr($code, -1) != '/')
            $code .= '/';

        $userId = $client->user_id;

        $acls = dbExfetch("
                SELECT code, access FROM %pfx%system_acl
                WHERE
                type = 1 AND
                (
                    ( target_type = 1 AND target_id IN ($inGroups) AND '$code' LIKE code )
                    OR
                    ( target_type = 2 AND target_id IN ($userId) AND '$code' LIKE code )
                )
                ORDER BY code DESC
        ", DB_FETCH_ALL);

        //$acls = krynAcl::getRules(1);

        if (count($acls) > 0) {
            $firstCode = $acls[0]['code'];
            $count = 1;
            foreach ($acls as $acl) {
                if ($count == 1 && $acl['access'] == 1) {
                    //first acl granted access
                    return true;
                }
                if ($count > 1 && $firstCode == $acl['code'] && $acl['access'] == 1) {
                    //same code as first (same prio) but grant access
                    return true;
                }
                $count++;
            }
            return false;
        } else {
            return false;
        }
    }

    /**
     * Checks the access to the administration URLs and redirect to administration login if no access.
     * @internal
     * @static
     */
    public static function checkAccess() {
        return true;

        $bypass = array('loadJs', 'loadCss');
        if (in_array(getArgv(2), $bypass))
            return true;

        $url = Kryn::getRequestedPath();

        if (getArgv(1) == 'admin' && !Kryn::checkUrlAccess($url)) {

            if (getArgv('getLanguage') != '')
                admin::printLanguage();

            if (getArgv('getPossibleLangs') == '1')
                admin::printPossibleLangs();

            if (getArgv('getLanguagePluralForm'))
                admin::getLanguagePluralForm();



            if (!getArgv(2)) {
                if (Kryn::$adminClient->user_id > 0){
                    tAssign('noAdminAccess', true);
                }
                admin::showLogin();
                exit;
            } else {
                json(array('error' => 'access_denied'));
            }
        }
    }


    /**
     * Escape ' to \\' to use string in queries which uses ' as string delimiter.
     *
     * @param string $pString
     *
     * @return string Filtered string
     * @deprecated Use the global esc() instead
     * @static
     */
    public static function esc($pString) {

        $search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
        return str_replace($search, $replace, $pString);
    }

    /**
     * Initialize config. Establish connections.
     * @internal
     */
    public static function initConfig() {

        if (!self::$config['client'])
            throw new \Exception('There is not client handling configured. Please run the installer.');

        if (!self::$config['cache'])
            self::$config['cache']['class'] = '\Core\Cache\Files';

        //global normal cache 
        Kryn::$cache = new Cache\Controller(self::$config['cache']['class'], self::$config['cache_params']);

        $fastestCacheClass = Cache\Controller::getFastestCacheClass();
        Kryn::$cacheFast   = new Cache\Controller($fastestCacheClass);

    }

    public static function initClient() {


        $defaultClientClass = Kryn::$config['client']['class'];
        $defaultClientConfig = Kryn::$config['client']['config'];

        if (Kryn::$admin) {
            
            Kryn::$adminClient = new $defaultClientClass($defaultClientConfig);

            Kryn::$adminClient->setAutoLoginLogout(true)
                              ->setLoginTrigger('admin-users-login')
                              ->setLogoutTrigger('admin-users-logout')
                              ->start();

            Kryn::$client = Kryn::$adminClient;
        }

        if (!Kryn::$admin) {

            $sessionProperties = Kryn::getDomain() ? Kryn::getDomain()->getSessionProperties() : array();

            $frontClientClass = $defaultClientClass;
            $frontClientConfig = $defaultClientConfig;

            if ($sessionProperties['class']){
                $frontClientClass = $sessionProperties['class'];
                $frontClientConfig = $sessionProperties['config'];
            }

            Kryn::$client = new $frontClientClass($frontClientConfig);
            Kryn::$client->start();
        }

        tAssignRef('client', Kryn::$client);
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
     * If $pPage belongs to another domain than
     * the current, we add the domain name plus http(s)://
     * to the fullUrl and return it. If $pPage belongs
     * to the current domain, we change nothing, since
     * the baseUrl is already defined in the html header.
     *
     * @static
     * @param \Page $pPage
     *
     * @return string
     */
    public static function fullUrl($pPage){

        $url = $pPage->getFullUrl(true);

        if ($pPage->getDomainId() != Kryn::$domain->getId()){

            $domain = Kryn::getDomain($pPage->getDomainId());

            $domainName = $domain->getRealDomain();
            if ($domain->getMaster() != 1) {
                $url = $domainName . $domain->getPath() . $domain->getLang() . '/' . $url;
            } else {
                $url = $domainName . $domain->getPath() . $url;
            }

            return 'http' . (Kryn::$ssl ? 's' : '') . '://' . $url;
        } else {
            return $url;
        }
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

        if (!$pId){
            $pId = Kryn::$page->getId();
            $domain_id = Kryn::$domain->getId();
        } else {
            $domain_id = $pDomainId?$pDomainId:Kryn::getDomainOfPage($pId);
        }

        if (!$domain_id) {
            return 'domain_not_found';
        }

        if ($domain_id == Kryn::$domain){
            $cachedUrls =& Kryn::$urls;
        } else {
            $cachedUrls =& Kryn::getCache('systemUrls-' . $domain_id);

            if (!$cachedUrls || !$cachedUrls['id']) {
                $cachedUrls = \adminPages::updateUrlCache($domain_id);
            }
        }

        $url = $cachedUrls['id']['id=' . $pId];

        if ($pAbsolute || $domain_id != Kryn::$domain->getId()){
            if ($domain_id != Kryn::$domain->getId())
                $domain = Kryn::getDomain($domain_id);
            else
                $domain = Kryn::$domain;

            $domainName = $domain->getRealDomain();
            if ($domain->getMaster() != 1) {
                $url = $domainName . $domain->getPath() . $domain->getLang() . '/' . $url;
            } else {
                $url = $domainName . $domain->getPath() . $url;
            }

            $url = 'http' . (Kryn::$ssl ? 's' : '') . '://' . $url;
        }

        if (substr($url, -1) == '/')
            $url = substr($url, 0, -1);

        if ($url == '/')
            $url = '.';

        if (substr($url, -1) == '/')
            $url = substr($url, 0, -1);

        if ($url == '/')
            $url = '.';

        return $url;
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
     * Returns the requested path in the URL, without http and domain name/path
     *
     * @static
     * @return string
     *
     * @param boolean pWithAdditionalParameter If you want to get KGETs too
     *
     * @internal
     */
    public static function getRequestedPath($pWithAdditionalParameter = false) {

        return $pWithAdditionalParameter ? Kryn::$urlWithGet: Kryn::$url;

    }

    /**
     * Reads all parameter out of the URL and insert them to $_REQUEST
     * @internal
     */
    public static function prepareUrl() {

        $url = $_GET['__url'];

        if (($pos = strpos($url, '?')) !== false){
            $query = substr($url, $pos+1);
            $url = substr($url, 0, $pos);
            parse_str($query, $_GET);
            $_GET['__url'] = $url;
        }

        if (substr($url, 0, 1) == '/')
            $url = substr($url, 1);

        Kryn::$url = '';

        $t = explode("/", $url);
        $c = 1;

        foreach ($t as $i) {
            if (strpos($i, "=")) {
                $param = explode("=", $i);
                $_GET[$param[0]] = $param[1];
                Kryn::$url .= '/' . urlencode($param[0]) . '=' . urlencode($param[1]);
            } elseif (strpos($i, ":")) {
                $param = explode(":", $i);
                $_GET[$param[0]] = $param[1];
                Kryn::$url .= '/' . urlencode($param[0]) . '=' . urlencode($param[1]);
            } else {
                Kryn::$url .= '/' . urlencode($i);
                $c++;
            }
        }


        if (substr(Kryn::$url, 0, 1) == '/')
            Kryn::$url = substr(Kryn::$url, 1);

        Kryn::$urlWithGet = Kryn::$url;
        $f = false;

        foreach ($_GET as $k => &$v) {
            if ($k == '__url') continue;
            if (is_array($v)) continue;
            Kryn::$urlWithGet .= (!$f ? '?' : '&') . urlencode($k) . (($v)?'=' . urlencode($v):'');
            if ($f == false) $f = true;
        }

    }

    /**
     * Check whether specified pLang is a valid language
     *
     * @param string $pLang
     *
     * @return bool
     * @internal
     */
    public static function validLanguage($pLang) {
        if (strlen($pLang) != 2) return false;

        $languages = Kryn::getCache('systemLanguages');

        if (!$languages) {
            $languages = dbExfetch('SELECT code FROM %pfx%system_langs WHERE visible = 1', -1);
            Kryn::setCache('systemLanguages', $languages);
        }

        foreach ($languages as $l) {
            if ($l['code'] == $pLang) {
                return true;
            }
        }
        return false;
    }

    /**
     * Clears the language chaces
     *
     * @param string $pLang
     *
     * @internal
     */
    public static function clearLanguageCache($pLang = false) {
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
     * @param string $pLang de, en, ...
     *
     * @static
     * @internal
     */
    public static function loadLanguage($pLang = false, $pForce = false) {

        if (!$pLang) $pLang = Kryn::getLanguage();

        if( Kryn::$lang && Kryn::$lang['__lang'] && Kryn::$lang['__lang'] == $pLang && $pForce == false )
            return;

        if (!$pLang) return;

        $code = 'cacheLang_' . $pLang;
        Kryn::$lang =& Kryn::getFastCache($code);

        $md5 = '';
        foreach (Kryn::$extensions as $key) {
            if ($key == 'kryn')
                $md5 .= @filemtime(PATH_CORE.'lang/' . $pLang . '.po');
            else
                $md5 .= @filemtime(PATH_MODULE . $key . '/lang/' . $pLang . '.po');
        }

        $md5 = md5($md5);

        if ((!Kryn::$lang || count(Kryn::$lang) == 0) || Kryn::$lang['__md5'] != $md5) {

            Kryn::$lang = array('__md5' => $md5, '__plural' => Language::getPluralForm($pLang), '__lang' => $pLang);

            foreach (Kryn::$extensions as $key) {

                $po = Language::getLanguage($key, $pLang);
                Kryn::$lang = array_merge(Kryn::$lang, $po['translations']);

            }
            Kryn::setFastCache($code, Kryn::$lang);
        }

        if (!file_exists(PATH_PUBLIC_CACHE.'gettext_plural_fn_' . $pLang . '.php') ||
            !file_exists(PATH_PUBLIC_CACHE.'gettext_plural_fn_' . $pLang . '.js')) {
            //write gettext_plural_fn_<langKey> so that we dont need to use eval()
            $pos = strpos(Kryn::$lang['__plural'], 'plural=');
            $pluralForm = substr(Kryn::$lang['__plural'], $pos + 7);

            $code = "<?php \nfunction gettext_plural_fn_$pLang(\$n){\n";
            $code .= "    return " . str_replace('n', '$n', $pluralForm) . ";\n";
            $code .= "}\n?>";
            Kryn::fileWrite(PATH_PUBLIC_CACHE.'gettext_plural_fn_' . $pLang . '.php', $code);


            $code = "function gettext_plural_fn_$pLang(n){\n";
            $code .= "    return " . $pluralForm . ";\n";
            $code .= "}";
            Kryn::fileWrite(PATH_PUBLIC_CACHE.'gettext_plural_fn_' . $pLang . '.js', $code);
        }

        include_once(PATH_PUBLIC_CACHE.'gettext_plural_fn_' . $pLang . '.php');
    }

    /**
     * Returns Domain object
     *
     * @param int $pDomainId
     *
     * @return \Domain If not defined, it returns the current domain.
     * @static
     */
    public static function getDomain($pDomainId = null) {

        //todo cache
        if (!$pDomainId) return self::$domain;

        $domain = \DomainQuery::create()->findPk($pDomainId);
        if (!$domain){
            return false;
        }

        return $domain;
    }

    /**
     * Reads the requested URL and try to extract the requested language.
     * @return string Empty string if nothing found.
     * @internal
     */
    public static function getPossibleLanguage() {

        if (strpos($_GET['__url'], '/') > 0)
            $first = substr($_GET['__url'], 0, strpos($_GET['__url'], '/'));
        else
            $first = $_GET['__url'];

        if (self::validLanguage($first)) {
            $_GET['__url'] = substr($_GET['__url'], strlen($first) + 1); //cut langcode
            return $first;
        }

        return "";
    }


    /**
     * @static
     *
     * @param bool $pNoRefreshCache
     * @return Domain
     */
    public static function detectDomain($pNoRefreshCache = false){

        $domainName = $_SERVER['SERVER_NAME'];

        if (getArgv('kryn_domain')) {
            $domainName = getArgv('kryn_domain', 1);
        }

        $possibleLanguage = self::getPossibleLanguage();

        $domains =& Kryn::getCache('systemDomains');

        if (!$domains || $domains['r2d']) {
            $domains = \adminPages::updateDomainCache();
        }

        if ($domains['_redirects'][$domainName]) {
            header("HTTP/1.1 301 Moved Permanently");
            $redirect = Kryn::getDomain($domains['_redirects'][$domainName]);
            header('Location: ' . self::$ssl?'https://':'http://' . $redirect['domain'] . $redirect['path']);
            exit;
        }

        $findDomainId = $domains[$domainName];
        if (!$findDomainId){
            $findDomainId = $domains[$domainName . '_' . $possibleLanguage];
        }

        if (!($domain = Kryn::getDomain($findDomainId)) && !$pNoRefreshCache){
            //we refresh the cache and try it again one times.

            \adminPages::updateDomainCache();
            return self::detectDomain(true);
        }

        if (!$domain) {
            klog("system", "Domain <i>$domainName</i> not found. Language: $possibleLanguage");
            Kryn::internalError('Domain not found', tf('Domain %s not found', $domainName));
        }


        $domain->setRealDomain($domainName);

        return $domain;
    }

    /**
     * Loads the current domain based on the requested URL
     *
     * @internal
     */
    public static function searchDomain() {

        Kryn::$languages =& Kryn::getCache('systemLanguages');
        tAssignRef("languages", Kryn::$languages);

        if (getArgv(1) != 'admin') {

            $http = 'http://';
            if ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on') {
                $http = 'https://';
                Kryn::$ssl = true;
            }

            Kryn::$port = '';
            if ((
                ($_SERVER['SERVER_PORT'] != 80 && $http == 'http://') ||
                    ($_SERVER['SERVER_PORT'] != 443 && $http == 'https://')
            ) && $_SERVER['SERVER_PORT'] + 0 > 0
            ) {
                Kryn::$port = ':' . $_SERVER['SERVER_PORT'];
            }


            self::$domain = self::detectDomain();

            Kryn::$language = self::$domain->getLang();

            if (Kryn::$domain->getPhplocale()) {
                setlocale(LC_ALL, Kryn::$domain->getPhplocale());
            }

            Kryn::$baseUrl = $http . self::$domain->getRealDomain() . Kryn::$port . Kryn::$domain->getPath();
            if (Kryn::$domain->getMaster() != 1 && getArgv(1) != 'admin') {
                Kryn::$baseUrl = $http . self::$domain->getRealDomain() . Kryn::$port . Kryn::$domain->getPath()
                    . Kryn::$domain->getLang(). '/';
            }

            tAssignRef("language", $language);

            if (getArgv(1) == 'robots.txt' && Kryn::$domain->getRobots() != "") {
                header('Content-Type: text/plain');
                print Kryn::$domain->getRobots();
                exit();
            }

            if (Kryn::$domain->getFavicon() != "") {
                Kryn::addHeader('<link rel="shortcut icon" href="' . Kryn::$baseUrl . Kryn::$domain->getFavicon() . '" />');
            }


            $tUrl = explode("?", $_GET['__url']);
            if (substr($tUrl[0], -1) == '/') {
                $get = array();
                foreach ($_GET as $k => $v)
                    if ($k != '__url')
                        $get[] = $k . "=" . $v;

                $toUrl = substr($tUrl[0], 0, -1);
                if (count($get) > 0)
                    $toUrl .= '?' . implode("&", $get);

                if (count($_POST) == 0) //only when the browser don't send data
                    Kryn::redirect($toUrl);
            }
        }

    }

    /**
     * @static
     * @return string
     */
    public static function getBaseUrl(){
        return Kryn::$baseUrl;
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
                $cgroups =& Kryn::$client->user['groups'];
            } else {
                $htuser = Kryn::$client->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

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
                foreach (Kryn::$adminClient->user['groups'] as $group) {
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
            $page2Domain = \adminPages::updatePage2DomainCache();
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
     * Search the current page or the start page, loads all information and checks the access.
     * @internal
     * @return int
     */
    public static function searchPage() {

        if (getArgv(1) == 'admin') return;

        $url = Kryn::getRequestedPath();

        $domain = Kryn::$domain->getId();


        Kryn::$urls =& Kryn::readCache('systemUrls');

        if (!Kryn::$urls || !Kryn::$urls['url']) {
            Kryn::$urls = \adminPages::updateUrlCache($domain);
        }

        //extract extra url attributes
        $found = $end = false;
        $possibleUrl = $next = $url;
        $oriUrl = $possibleUrl;

        do {

            $id = Kryn::$urls['url']['url=' . $possibleUrl];

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

        //if the url is a file request we throw a 404 because files have to check via checkFile.php
        if (strpos($oriUrl, ".") !== FALSE) {
            $page = array();
            $url = "404";
            $id = 0;
        }

        Kryn::$isStartpage = false;

        $pageId = 0;

        if ($url == '') {
            $pageId = Kryn::$domain->getStartpageId();

            if (!$pageId > 0) {
                Kryn::internalError(null, tf('There is no start page for domain %s', Kryn::$domain->getDomain()));
            }

            Kryn::$isStartpage = true;
        } else {
            $pageId = $id;
        }

        Kryn::$page = \PageQuery::create()->findPk($pageId);
        if (!Kryn::$page) return false;

        $title = (self::$page->getPageTitle()) ? self::$page->getPageTitle() : self::$page->getTitle();

        $e = explode('::', self::$domain->getTitleFormat());
        if ($e[0] && $e[1] && $e[0] != 'admin' && $e[0] != 'self' && method_exists($e[0], $e[1])) {
            $title = call_user_func(array($e[0], $e[1]));
        } else {

            $title = str_replace(
                array('%title', '%domain'),
                array(
                    $title,
                    $_SERVER['SERVER_NAME']),
                    kryn::$domain->getTitleFormat()
            );

            if (strpos($title, '%path') !== false) {
                $title = str_replace('%path', self::getBreadcrumpPath(), $title);
            }
        }

        Kryn::$domain->setTitle($title);

        return $page;
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


    public static function &getPage($pPageId) {

        //todo cache

        $page = \PageQuery::create()->findPk($pPageId);
        if (!$page) return false;


        return $page;

    }

    /**
     * Prints the Kryn/404-page.tpl template to the client and exit, if defined redirect the the 404-page
     * defined in the domain settings or opens the 404-interface file which is also defined
     * in the domain settings.
     *
     * @static
     */
    public static function notFound() {

        $msg = sprintf(t('Page not found %s'), Kryn::$domain->getDomain() . '/' . Kryn::getRequestedPath(true));
        klog('404', $msg);

        Event::fire('onNotFound');

        if (Kryn::$domain->getPage404interface() != '') {
            if (strpos(Kryn::$domain->getPage404interface(), "media") !== FALSE) {
                include(Kryn::$domain->getPage404interface());
            } else {
                include(PHP_MODULE . Kryn::$domain->getPage404interface());
            }
        } else if (Kryn::$domain->getPage404id() > 0) {
            Kryn::redirectToPage(Kryn::$domain->getPage404id(), 'error=' . 404);
        } else {
            self::internalError('404 Not Found', $msg);
        }
        exit;
    }

    /**
     * Prints the Kryn/internal-error.tpl template to the client and exist.
     *
     * @static
     * @param $pTitle
     * @param $pMsg
     */
    public static function internalError($pTitle = '', $pMsg) {
        tAssign('msg', $pMsg);
        tAssign('title', $pTitle?$pTitle:'Internal system error');
        print tFetch('kryn/internal-error.tpl');
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


        Kryn::$canonical = Kryn::$baseUrl . Kryn::getRequestedPath(true);

        $pageCacheKey =
            'systemWholePage-' . Kryn::$domain->getId() . '_' . Kryn::$page->getId() . '-' . md5(Kryn::$canonical);

        if (Kryn::$domainProperties['kryn']['cachePagesForAnons'] == 1 && Kryn::$client->user['id'] == 0 &&
            count($_POST) == 0
        ) {

            $cache =& Kryn::getCache($pageCacheKey);
            if ($cache) {
                print $cache;
                exit;
            }

        }

        if (Kryn::$domain->getStartpageId() == Kryn::$page->getId() && !Kryn::$isStartpage) {
            Kryn::redirect(Kryn::$baseUrl);
        }

        if (Kryn::$page->getType() == 1) { //is link
            $to = Kryn::$page->getLink();
            if (!$to) {
                Kryn::internalError(t('Redirect failed'), tf('Current page with title %s has no target link.', Kryn::$page->getTitle()));
            }

            if ($to+0 > 0) {
                Kryn::redirectToPage($to);
            } else {
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: $to");
            }
            exit;
        }



        if (Kryn::$page->getType() == 0) { //is page
            if (Kryn::$page->getForceHttps() == 1 && Kryn::$ssl == false) {
                header('Location: ' . str_replace('http://', 'https://', Kryn::$baseUrl) . Kryn::$page->getFullUrl());
                exit;
            }


            Kryn::$themeProperties = array();
            $propertyPath = '';

            foreach (Kryn::$themes as $extKey => &$themes) {
                foreach ($themes as $tKey => &$theme) {
                    if ($theme['layouts']) {
                        foreach ($theme['layouts'] as $lKey => &$layout) {
                            if ($layout == Kryn::$page->getLayout()) {
                                $propertyPath = $extKey.'/'.$tKey;
                                break;
                            }
                        }
                    }
                    if ($propertyPath) break;
                }
                if ($propertyPath) break;
            }
            if ($propertyPath) {
                if ($themeProperties = kryn::$domain->getThemeProperties())
                    Kryn::$themeProperties = $themeProperties->getByPath($propertyPath);
            }

            tAssignRef('themeProperties', Kryn::$themeProperties);
        }


        Kryn::loadBreadcrumb();

        Kryn::$breadcrumbs[] = Kryn::$page;


        if (!Kryn::$page->getLayout()) {
            Kryn::$pageHtml = self::internalError(t('No layout'), tf('No layout chosen for the page %s.', Kryn::$page->getTitle()));
        } else {
            Kryn::$pageHtml = Render::renderPageContents();
        }

        Kryn::$pageHtml = str_replace('\[[', '[[', Kryn::$pageHtml);
        Kryn::replacePageIds(Kryn::$pageHtml);

        //htmlspecialchars(urldecode(Kryn::$url));
        Kryn::$pageHtml = preg_replace('/href="#(.*)"/', 'href="' . Kryn::$url . '#$1"', Kryn::$pageHtml);

        foreach (Kryn::$modules as $key => $mod) {
            Kryn::$modules[$key] = NULL;
        }


        if (Kryn::$disableSearchEngine == false) {
            $resCode = Search::createPageIndex(Kryn::$pageHtml);

            if ($resCode == 2) {
                Kryn::notFound('invalid-arguments');
            }
        }

        self::removeSearchBlocks(Kryn::$pageHtml);

        header("Content-Type: text/html; charset=utf-8");

        if (Kryn::$domainProperties['kryn']['cachePagesForAnons'] == 1 && self::$client->getUser()->getId() == 0 &&
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
     * Return the content of a file
     *
     * @param string $pPath Relative to installation dir
     *
     * @return string
     * @static
     */
    public static function fileRead($pPath) {
        $file = $pPath;
        if (!file_exists($file)) return '';
        $handle = @fopen($file, "r");
        $fs = @filesize($file);
        if ($fs > 0)
            $n = @fread($handle, $fs);
        @fclose($handle);
        return $n;
    }

    /**
     * Writes content to a file
     *
     * @param string $pPath
     * @param string $pContent
     *
     * @return bool
     * @static
     */
    public static function fileWrite($pPath, $pContent) {

        $pPath = (substr($pPath,0,1) == '/' || substr($pPath,1,1) == ':') ? $pPath : PATH . $pPath;

        $h = @fopen($pPath, 'w');
        if ($h) {
            fwrite($h, $pContent);
            fclose($h);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Removes a value for the specified cache-key
     *
     * @param string $pCode
     */
    public static function deleteCache($pCode) {
        if (Kryn::$cache)
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
     *
     * @static
     */
    public static function setCache($pCode, $pValue, $pTimeout = false) {
        if (Kryn::$cache)
            return Kryn::$cache->set($pCode, $pValue, $pTimeout);
        return false;
    }

    /**
     * Marks a code as invalidate until $pTime
     *
     * @param string  $pCode
     * @param integer $pTime Timestamp. Default is time()
     */

    public static function invalidateCache($pCode, $pTime = false) {
        if (Kryn::$cache)
            return Kryn::$cache->invalidate($pCode, $pTime);
        return false;
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
        if (Kryn::$cache)
            return Kryn::$cache->get($pCode);
        return false;
    }

    /**
     * Sets a content to the specified cache-key.
     * This function saves the value in a generated php file
     * as php code or via apc_store.
     *
     * If you want to save php class objects, you should serialize it before.
     * 
     * The idea behind this: If the server has active apc or
     * other optcode caching, then this method is way
     * faster then tcp caching-server.
     * Please be sure, that you really want to use that: This
     * is not compatible with load balanced Kryn.cms installations
     * and should only be used, if you are really sure, that
     * a other machine in a load balanced scenario does not
     * need information about this cache.
     * A good purpose for this is for example caching converted
     * local json files (like the installed extension configs).
     *
     * @param string $pCode
     * @param string $pValue
     *
     * @static
     */
    public static function setFastCache($pCode, $pValue) {
        return Kryn::$cacheFast?Kryn::$cacheFast->set($pCode, $pValue):false;
    }

    /**
     * Returns the content of the specified cache-key.
     * See Kryn::setFastCache for more informations.
     *
     * @param string $pCode
     *
     * @return string
     * @static
     */
    public static function &getFastCache($pCode) {
        return Kryn::$cacheFast?Kryn::$cacheFast->get($pCode):false;
    }

    /**
     * Internal function to return cache values depended on a domain.
     *
     * @static
     * @param string $pCode
     * @return mixed
     */
    public static function &readCache($pCode) {
        $id = Kryn::$domain->getId();
        $pCode = str_replace('..', '', $pCode);
        return Kryn::getCache($pCode . '-' . $id);
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
     * Returns the webservers temp folder.
     *
     * @static
     * @return string
     */
    public static function getTempFolder(){

        if ($_ENV['TMP']) return $_ENV['TMP'];
        if ($_ENV['TEMP']) return $_ENV['TEMP'];
        if ($_ENV['TMPDIR']) return $_ENV['TMPDIR'];
        if ($_ENV['TEMPDIR']) return $_ENV['TEMPDIR'];

        return sys_get_temp_dir();
    }

    /**
     * Creates a temp folder and returns its path.
     *
     * @static
     * @param string $pPrefix
     * @return string
     */
    public static function createTempFolder($pPrefix = ''){

        $string = self::getTempFolder();
        if (substr($string, -1) != '/')
            $string .= '/';

        do {
            $path = $string . $pPrefix . dechex(time() / mt_rand(100, 500));
        } while (is_dir($path));

        mkdir($path);
        return $path;
    }
}

?>
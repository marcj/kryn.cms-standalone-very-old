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
 * Kryn.core class
 * @author MArc Schmidt <marc@kryn.org>
 */

class kryn {

    /**
     * Contains all parent pages, which can be shown as a breadcrumb navigation.
     * This is filled automaticaly. If you want to add own items, use kryn::addMenu( $pName, $pUrl );
     * @type: array
     * @internal
     * $static
     */
    public static $breadcrumbs;

    /**
     * Contains all additional html header values.
     * Use kryn::addHeader( $pHeader ) to add additional headers.
     * @var array
     * @internal
     * $static
     */
    public static $header = array();

    /**
     * Contains all paths to javascript files as each item
     * Use kryn::addJs( $pPath ) to add javascript files.
     * @var array
     * @internal
     * $static
     */
    public static $jsFiles = array();

    /**
     * Contains all paths to css files as each item.
     * Use kryn::addCss( $pPath ) to add css files.
     * @var array
     * @internal
     * $static
     */
    public static $cssFiles = array('css/kryn_defaults.css' => 1);

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
     * Defines the current baseUrl (also use in html <header>)
     * @var string
     * $static
     */
    public static $baseUrl;

    /**
     * Contains the current domain with all information (as defined in the database system_domain)
     * @var array
     * $static
     */
    public static $domain;

    /**
     * Contains the current page with all information
     * @var array
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
     * use urldecode(htmlspecialchars(kryn::$url)) to display it in your page.
     * @var string
     */
    public static $url;

    /**
     * Contains the current requested URL without http:// and with _GET params, urlencoded
     * use urldecode(htmlspecialchars(kryn::$urlWithGet)) to display it in your page.
     * @var string
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
     * @deprecated Use $themeProperties instead.
     * @static
     */
    public static $publicProperties = array();

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
     * Defines whether the frontend editor is enabled or not.
     * @var bool
     * @static
     * @internal
     */
    public static $kedit = false;

    /**
     * Defines whether force-ssl is enabled or not.
     * @var bool
     * @static
     * @internal
     */
    private static $ssl = false;

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
     */
    public static $objects = array();

    /**
     * Defines whether the user has acces to the frontend-editor or not.
     * @var bool
     * @static
     * @private
     * @internal
     */
    private static $keditAccess = false;


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
     * $static
     */
    public static $contents;

    /**
     * Defines whether we are at the startpage
     * @var bool
     * $static
     */
    public static $isStartpage;

    /**
     * Contains all config.json as object from all activated extension.
     * Only available in the administration area.
     * @var array
     * $static
     */
    public static $configs;

    /**
     * Contains all installed database tables from config.json#db
     * Example: array('publication' => array('publication_news' => array([fields], 'publication_news_category' => array()))
     * @static
     */
    public static $tables;

    /**
     * Contains all installed extensions
     * Example: array('kryn', 'admin', 'users', 'sitemap', 'publication');
     * @static
     */
    public static $extensions;


    /**
     * Contains all installed themes
     * @static
     */
    public static $themes;

    /**
     * Contains the system config (inc/config.php).
     * @var array
     * $static
     */
    public static $config;

    /**
     * Ref to kryn::$config for compatibility

     */
    public static $cfg;


    /**
     * @internal
     * @static
     */
    public static $unsearchableBegin = '<!--unsearchable-begin-->';

    /**
     * @internal
     * @static
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
     * $static
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
     * Contains the krynCache object
     * $static
     */
    public static $cache;

    /**
     * Contains the krynCache object for file caching
     * See kryn::setFastCache for more informations.
     * $static
     */
    public static $cacheFast;


    /**
     * Defines whether we are in the administration area or not.
     * Equal to getArgv(1)=='admin'
     *$static
     */
    public static $admin = false;

    /**
     * Contains the ACLs as cache
     * @var array
     * @static
     * @private
     * @internal
     */
    private static $cachedPageAcls = array();

    /**
     * Cached object of the current domains's urls to rsn, rsn to url, alias to rsn
     * @static
     * @var array
     */
    public static $urls;


    /**
     * Placeholder to inject own html.
     * @static
     */
    public static $htmlHeadTop;

    /**
     * Placeholder to inject own html.
     * @static
     */
    public static $htmlHeadEnd;

    /**
     * Placeholder to inject own html.
     * @static
     */
    public static $htmlBodyTop;

    /**
     * Placeholder to inject own html.
     * @static
     */
    public static $htmlBodyEnd;


    /**
     * Defines all possible ACL items for the permission management on page level
     * @var array
     * @static
     * @internal
     */
    public static $pageAcls = array(
        'domainAll' => array(
            'tree' => array(
                'showDomain',
                'deleteDomain'
            ),
            'domain' => array(
                'domainName',
                'domainTitle',
                'domainStartpage',
                'domainPath',
                'domainFavicon',
                'domainLanguage',
                'domainLanguageMaster',
                'domainEmail',
            ),
            'theme' => array(
                'themeProperties',
                'limitLayouts',
            ),
            'domainProperties' => array(),
            'settings' => array(
                'aliasRedirect',
                'aliasRedirect',
                'phpLocale',
                'robotRules',
                '404',
                'domainOther'
            )
        ),
        'pageAll' => array(
            'tree' => array(
                'showPage',
                'addPages',
                'movePages',
                'deletePages',
            ),
            'general' => array(
                'type',
                'title',
                'page_title',
                'url',
                'meta',
            ),
            'access' => array(
                'visible',
                'access_denied',
                'force_https',
                'releaseDates',
                'limitation',
            ),
            'contents' => array(
                'canPublish',
                'canChangeLayout',
                'content-text',
                'content-layoutelement',
                'content-picture',
                'content-plugin',
                'content-pointer',
                'content-navigation',
                'content-template',
                'content-html',
                'content-php',
            ),
            'resources' => array(
                'css',
                'js',
            ),
            'properties' => array(),
            'search' => array(
                'setBlacklist',
                'exludeSearch',
                'searchKeys',
            ),
            'versions' => array(
                'setLive',
                'loadVersion',
            )
        )
    );

    /**
     * Defines whether this version can compare or not.
     * @var bool
     * @internal
     */
    public static $canCompare = true;


    /**
     * Adds a new crumb to the breadcrumb array.
     *
     * @param string $pName
     * @param string $pUrl
     *
     * @static
     */
    public static function addBreadcrumb($pName, $pUrl = "") {

        kryn::$breadcrumbs[] = array("title" => $pName, "realUrl" => $pUrl);
        tAssignRef("breadmcrumbs", kryn::$breadcrumbs);
    }

    /**
     * Adds a new css file to the <header>. Use relative paths from inc/template without a / as start
     * Absolute paths with http:// also possible.
     *
     * @param string|array $pCss
     *
     * @static
     */
    public static function addCss($pCss) {
        if (is_array($pCss))
            foreach ($pCss as $k => $v)
                kryn::$cssFiles[(is_string($k) ? $k : $v)] = 1;
        else if (is_string($pCss))
            kryn::$cssFiles[$pCss] = 1;
    }

    /**
     * Adds a new javascript file to the <header>. Use relative paths from inc/template without a / as start
     *
     * @param string|array $pJs
     *
     * @static
     */
    public static function addJs($pJs) {

        if (is_array($pJs))
            foreach ($pJs as $k => $v)
                kryn::$jsFiles[(is_string($k) ? $k : $v)] = 1;
        else if (is_string($pJs))
            kryn::$jsFiles[$pJs] = 1;
    }

    /**
     * Resets all javascript files.
     */
    public static function resetJs() {
        kryn::$jsFiles = array();
    }

    /**
     * Resets all css files.
     */
    public static function resetCss() {
        kryn::$cssFiles = array('css/kryn_defaults.css');
    }


    /**
     * Adds additional headers.
     *
     * @param string $pHeader
     *
     * @static
     */
    public static function addHeader($pHeader) {

        if (array_search($pHeader, kryn::$header) === false)
            kryn::$header[] = $pHeader;
    }

    /**
     * Sets the doctype in the krynHtml class
     * Possible doctypes are:
     * 'html 4.01 strict', 'html 4.01 transitional', 'html 4.01 frameset',
     * 'xhtml 1.0 strict', 'xhtml 1.0 transitional', 'xhtml 1.0 frameset',
     * 'xhtml 1.1 dtd', 'html5'
     * If you want to add a own doctype, you have to extend the static var:
     *     krynHtml::$docTypeMap['<id>'] = '<fullDocType>';
     * The default is 'xhtml 1.0 transitional'
     * Can also be called through the smarty function {setDocType value='html 4.01 strict'}
     * @static
     *
     * @param string $pDocType
     */
    public static function setDocType($pDocType) {
        krynHtml::$docType = $pDocType;
    }

    /**
     * Returns the current defined doctype
     * @static
     * @return string Doctype
     */
    public static function getDocType() {
        return krynHtml::$docType;
    }


    /**
     * Get some information about the system kryn was installed and kryn itself
     * @static
     */
    public static function getDebugInformation() {

        $infos = array();

        foreach (kryn::$extensions as $extension) {
            $config = kryn::getModuleConfig($extension, 'en');
            $infos['extensions'][$extension] = array(
                'version' => $config['version']
            );
        }

        $infos['phpversion'] = phpversion();

        $infos['database_type'] = kryn::$config['db_type'];

        $infos['config'] = kryn::$config;

        unset($infos['config']['db_passwd']);
        unset($infos['config']['db_server']);

        return $infos;
    }

    public static function loadActiveModules() {

        $extensions =& kryn::getCache('activeModules');

        if (!$extensions || is_array($extensions[0])) {
            $extensions = array();
            $dbMods =
                dbExFetch('SELECT name FROM %pfx%system_modules WHERE activated = 1 AND name != \'admin\' AND name != \'users\'', -1);
            foreach ($dbMods as &$mod) {
                $extensions[] = $mod['name'];
            }
            kryn::setCache('activeModules', $extensions);
        }

        $extensions[] = 'kryn';
        $extensions[] = 'admin';
        $extensions[] = 'users';
        kryn::$extensions = $extensions;
    }

    /**
     * Loads all activated extension configs and tables
     * @internal
     */
    public static function loadModuleConfigs() {

        $md5 = '';
        foreach (kryn::$extensions as $extension) {
            $path = ($extension == 'kryn') ? 'inc/kryn/config.json' : PATH_MODULE . '' . $extension . '/config.json';
            if (file_exists($path)) {
                $md5 .= '.' . filemtime($path);
            }
        }

        $md5 = md5($md5);

        kryn::$tables =& kryn::getCache('systemTablesv2');

        /*
         * load tables
         */
        if (!kryn::$tables || $md5 != kryn::$tables['__md5']) {

            kryn::$tables = array();
            kryn::$tables['__md5'] = $md5;

            foreach (kryn::$extensions as &$extension) {
                kryn::$configs[$extension] = kryn::getModuleConfig($extension, false, true);
            }

            foreach (kryn::$configs as $extension => $config) {

                if (is_array($config['extendConfig'])) {
                    foreach ($config['extendConfig'] as $extendModule => &$extendConfig) {
                        if (kryn::$configs[$extendModule]) {
                            kryn::$configs[$extendModule] =
                                array_merge_recursive_distinct(kryn::$configs[$extendModule], $extendConfig);
                        }
                    }
                }
            }

            foreach (kryn::$configs as $extension => $config) {

                if ($config['db']) {
                    foreach ($config['db'] as $key => &$table) {
                        if (kryn::$tables[$key])
                            kryn::$tables[$key] = array_merge(kryn::$tables[$key], $table);
                        else
                            kryn::$tables[$key] = $table;
                    }
                }

            }

            kryn::setCache('systemTablesv2', kryn::$tables);
        }

        unset(kryn::$tables['__md5']);

        /*
         * load themes
         */
        kryn::$themes =& kryn::getCache('systemThemes');
        if (!kryn::$themes || $md5 != kryn::$themes['__md5']) {

            kryn::$themes = array();
            kryn::$themes['__md5'] = $md5;

            foreach (kryn::$extensions as &$extension) {

                $config = kryn::$configs[$extension] ? kryn::$configs[$extension]:kryn::getModuleConfig($extension, false, true);
                if ($config['themes'])
                    kryn::$themes[$extension] = $config['themes'];
            }
            kryn::setCache('systemThemes', kryn::$themes);
        }
        unset(kryn::$themes['__md5']);


        /*
         * load object definitions
         */
        kryn::$objects =& kryn::getCache('systemObjects');

        if (!kryn::$objects || $md5 != kryn::$objects['__md5']){

            kryn::$objects = array();
            kryn::$objects['__md5'] = $md5;

            foreach (kryn::$extensions as &$extension) {

                $config = kryn::$configs[$extension] ? kryn::$configs[$extension]:kryn::getModuleConfig($extension, false, true);

                if ($config['objects'] && is_array($config['objects'])){

                    foreach ($config['objects'] as $objectId => $objectDefinition){
                        $objectDefinition['_extension'] = $extension; //caching
                        kryn::$objects[$objectId] = $objectDefinition;
                    }
                }

            }
            kryn::setCache('systemObjects', kryn::$objects);
        }
        unset(kryn::$objects['__md5']);
    }

    /**
     * Loads all config.json from all activated extensions to kryn::$configs.
     * @internal
     * @static
     */
    public static function loadConfigs() {

        if (getArgv(1) == 'admin') {

            kryn::$configs = array();

            foreach (kryn::$extensions as &$extension) {
                kryn::$configs[$extension] = kryn::getModuleConfig($extension);
            }
            //print_r(kryn::$configs['admin']);
            //die();

            foreach (kryn::$configs as &$config) {
                if (is_array($config['extendConfig'])) {
                    foreach ($config['extendConfig'] as $extendModule => &$extendConfig) {
                        if (kryn::$configs[$extendModule]) {
                            kryn::$configs[$extendModule] =
                                array_merge_recursive_distinct(kryn::$configs[$extendModule], $extendConfig);
                        }
                    }
                }
                if ($config['db']) {
                    foreach ($config['db'] as $key => &$table) {
                        if (kryn::$tables[$key])
                            kryn::$tables[$key] = array_merge(kryn::$tables[$key], $table);
                        else
                            kryn::$tables[$key] = $table;
                    }
                }
            }
        }
    }

    /**
     * Returns the current language for the client
     * based on the domain or current session language (in administration)
     */
    public static function getLanguage() {
        global $adminClient;

        if (kryn::$domain && kryn::$domain['lang']) {
            return kryn::$domain['lang'];
        } else if ( getArgv(1) == 'admin' && getArgv('lang', 2)) {
            return getArgv('lang', 2);
        } else if ($adminClient) {
            return $adminClient->getLang();
        }
        return 'en';

    }

    /**
     * Returns the config hash of the specified extension.
     *
     * @param string $pModule
     *
     * @return array All config values from the config.json
     * @static
     */
    public static function getModuleConfig($pModule, $pLang = false, $pNoCache = false ) {
        global $adminClient;

        $pModule = str_replace('.', '', $pModule);

        if ($pModule == 'kryn')
            $config = "inc/kryn/config.json";
        else
            $config = PATH_MODULE . "$pModule/config.json";

        if (!file_exists($config)) {
            return false;
        }

        $mtime = filemtime($config);
        $lang = $pLang ? $pLang : kryn::getLanguage();

        if (!$pNoCache) {

            $cacheCode = 'moduleConfig-' . $pModule . '.' . $lang;
            $configObj = kryn::getFastCache($cacheCode);

        }

        if (!$configObj || $configObj['mtime'] != $mtime) {

            $json = kryn::translate(kryn::fileRead($config));

            $configObj = json_decode($json, 1);

            if (!is_array($configObj)) {
                $configObj = array('_corruptConfig' => true);
            } else {
                $configObj['mtime'] = $mtime;
            }
            if (!$pNoCache) {
                kryn::setFastCache($cacheCode, $configObj);
            }
        }

        return $configObj;
    }

    /**
     * Load and initialise all activated extension classes.
     * @internal
     */
    public static function initModules() {
        global $modules;

        include_once(PATH_MODULE . "users/users.class.php");
        $modules['users'] = new users();

        foreach (kryn::$extensions as $mod) {
            $classFile = PATH_MODULE . '' . $mod . '/' . $mod . '.class.php';
            if ($mod != 'admin' && $mod != 'users') {
                if (file_exists($classFile)) {
                    include_once($classFile);
                    $modules[$mod] = new $mod();
                }
            }
        }
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
            $pFrom = kryn::$domain['email'];
            if ($pFrom == '')
                $pFrom = 'info@' . kryn::$domain['domain'];
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
     * Function to compate two versions with a operator.
     * Max Version: 999.999.999
     * Min Version: 0.0.1
     *
     * @param string $pModuleVersion extension key or a version
     * @param string $pOp            <,<=,>,>=,=
     * @param string $pVersion
     *
     * @return bool
     * @static
     */
    public static function compareVersion($pModuleVersion, $pOp, $pVersion) {

        if (kryn::$configs[$pModuleVersion])
            $pModuleVersion = kryn::$configs[$pModuleVersion]['version'];

        $versions = explode(".", $pModuleVersion);

        $major = $versions[0];
        $minor = $versions[1];
        $patch = $versions[2];


        $tversions = explode(".", $pVersion);
        $tmajor = $tversions[0];
        $tminor = $tversions[1];
        $tpatch = $tversions[2];

        //100 000 000
        $bversion = $major * 1000 * 1000;
        $bversion += $minor * 1000;
        $bversion += $patch;


        //100 000 000
        $tversion = $tmajor * 1000 * 1000;
        $tversion += $tminor * 1000;
        $tversion += $tpatch;

        if ($pOp == '<' && $bversion < $tversion)
            return true;

        if ($pOp == '<=' && $bversion <= $tversion)
            return true;

        if ($pOp == '=<' && $bversion <= $tversion)
            return true;

        if ($pOp == '=' && $bversion == $tversion)
            return true;

        if ($pOp == '>=' && $bversion >= $tversion)
            return true;

        if ($pOp == '=>' && $bversion >= $tversion)
            return true;

        if ($pOp == '>' && $bversion > $tversion)
            return true;

        return false;
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
            '/href="(\d*)"/',
            create_function(
                '$pP',
                '
            return \'href="\'.kryn::pageUrl($pP[1]).\'"\';
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
        kryn::loadLanguage();
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
        global $cfg;


        if (strpos($pUrl, 'http') === false && kryn::$domain) {

            if (kryn::$domain['master'] != 1)
                $pUrl = kryn::$domain['lang'] . '/' . $pUrl;

            $domain = kryn::$domain['domain'];
            $path = kryn::$domain['path'];

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
     * @param array  $pUser If you want to use a other user object.
     *
     * @return bool
     * @static
     */
    public static function checkUrlAccess($pUrl, $pClient = false) {

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

        $userRsn = $client->user_rsn;

        $acls = dbExfetch("
                SELECT code, access FROM %pfx%system_acl
                WHERE
                type = 1 AND
                (
                    ( target_type = 1 AND target_rsn IN ($inGroups) AND '$code' LIKE code )
                    OR
                    ( target_type = 2 AND target_rsn IN ($userRsn) AND '$code' LIKE code )
                )
                ORDER BY code DESC
        ", DB_FETCH_ALL);

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

        $bypass = array('loadJs', 'loadCss');
        if (in_array(getArgv(2), $bypass))
            return true;

        $url = kryn::getRequestPageUrl();

        if (getArgv(1) == 'admin' && !kryn::checkUrlAccess($url)) {

            if (getArgv('getLanguage') != '')
                admin::printLanguage();
            if (getArgv('getPossibleLangs') == '1')
                admin::printPossibleLangs();

            if (!getArgv(2)) {
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
        global $cfg;

        $cfg['templatepath'] = $cfg['path'] . 'inc/template';

        if (!$cfg['sessiontime'] && !$cfg['session_timeout'])
            $cfg['session_timeout'] = 3600;

        if ($cfg['sessiontime'] && !$cfg['session_timeout'])
            $cfg['session_timeout'] = $cfg['sessiontime'];

        if (!$cfg['session_tokenid']) {
            $cfg['session_tokenid'] = 'krynsessionid_ba';
        }

        if (!$cfg['auth_class'])
            $cfg['auth_class'] = 'kryn';

        if (!$cfg['show_banner'])
            $cfg['show_banner'] = 1;

        if (!$cfg['session_storage'])
            $cfg['session_storage'] = 'database';

        if (!$cfg['cache_type'])
            $cfg['cache_type'] = 'files';

        tAssignRef('path', $cfg['path']);
        tAssignRef("cfg", $cfg);

        if (!$cfg['cronjob_key']) {
            $cfg['cronjob_key'] = dechex(time() / mt_rand(100, 500));
            kryn::fileWrite('inc/config.php', "<?php \n\$cfg = " . var_export($cfg, true) . "\n?>");
        }

        if (!$cfg['passwd_hash_key']) {
            $cfg['passwd_hash_compatibility'] = 1;
            $cfg['passwd_hash_key'] = krynAuth::getSalt(32);
            kryn::fileWrite('inc/config.php', "<?php \n\$cfg = " . var_export($cfg, true) . "\n?>");
        }

        if ($cfg['cache_type'] == 'files') {

            if (!$cfg['cache_params'] || $cfg['cache_params']['files_path'] == '') {
                $cfg['cache_params']['files_path'] = 'cache/object/';
            }

        }

        kryn::$cache = new krynCache($cfg['cache_type'], $cfg['cache_params']);

        if (function_exists('apc_store'))
            kryn::$cacheFast = new krynCache('apc');
        else
            kryn::$cacheFast = new krynCache('files', array('files_path' => 'cache/object/'));

        if (!$cfg['media_cache'])
            $cfg['media_cache'] = 'cache/media/';

        if (!is_dir($cfg['media_cache'])) {
            if (!@mkdir($cfg['media_cache']))
                kryn::internalError('Can not create folder for template caching: ' . $cfg['media_cache']);
        }

        kryn::$config =& $cfg;
        kryn::$cfg =& $cfg;
    }

    public static function initAuth() {
        global $cfg, $user, $client, $adminClient;


        if (($_COOKIE[$cfg['session_tokenid']] || $_GET[$cfg['session_tokenid']] || $_POST[$cfg['session_tokenid']])
            || getArgv(1) == 'admin'
        ) {

            if (!$cfg['auth_class'] || $cfg['auth_class'] == 'kryn') {

                $adminClient = new krynAuth($cfg);

            } else {

                $ex = explode('/', $cfg['auth_class']);
                $class = PATH_MODULE . "" . $ex[0] . "/" . $ex[1] . ".class.php";

                if (file_exists($class)) {
                    require_once($class);
                    $authClass = $ex[1];
                    $adminClient = new $authClass($cfg);
                }

            }
            $adminClient->autoLoginLogout = true;
            $adminClient->loginKey = 'admin-users-login';
            $adminClient->logoutKey = 'admin-users-logout';

            $adminClient->start();
            $client = $adminClient;
        }

        if (getArgv(1) != 'admin') {

            $sessionDefinition = kryn::$domain['session'];

            $sessionDefinition['session_tokenid'] =
                ($sessionDefinition['session_tokenid']) ? $sessionDefinition['session_tokenid'] : 'krynsessionid';

            if ($sessionDefinition['auth_class'] == 'kryn' || !$sessionDefinition['auth_class']) {
                $client = new krynAuth($sessionDefinition);
            } else {
                $ex = explode('/', $sessionDefinition['auth_class']);
                $class = PATH_MODULE . "" . $ex[0] . "/" . $ex[1] . ".class.php";
                if (file_exists($class)) {
                    require_once($class);
                    $authClass = $ex[1];
                    $client = new $authClass($sessionDefinition);
                }
            }

            $client->start();
        }

        tAssignRef('client', $client);

        //compatibility with older extension
        $user = $client;
        if ($client->user['rsn'] != 0) {
            $user->user_logged_in = true;
        }
    }

    /**
     * Return all parents as array
     *
     * @param int page id
     *
     * @return array
     * @static

     */
    public static function getPageParents($pPageRsn = false, $pOnlyPages = true) {

        if (!$pPageRsn) $pPageRsn = kryn::$page['rsn'];

        $page =& kryn::getPage($pPageRsn);
        if ($page['prsn'] == 0) return array();

        $res = array();

        while (true) {
            $page =& kryn::getPage($page['prsn']);
            if (!$pOnlyPages || ($pOnlyPages && $page['type'] == 0)) {
                $res[] = $page;
            }
            if ($page['prsn'] == 0) break;
        }
        rsort($res);

        return $res;
    }

    /**
     * Returns the full human readable path to given pageRsn delimited
     * with $pDelimiter
     *
     * @param int    $pPageRsn
     * @param string $pDelimiter Default ' » '
     *
     * @static
     */
    public static function getPagePath($pPageRsn, $pDelimiter = ' » ' ) {

        $parents = kryn::getPageParents($pPageRsn);
        $page =& kryn::getPage($pPageRsn);
        $domain = kryn::getDomain($page['domain_rsn']);

        $path = '';
        if ($domain['master'] != 1)
            $path = '[' . $domain['lang'] . '] ';
        $path .= $domain['domain'];

        foreach ($parents as &$parent) {
            $path .= $pDelimiter . $parent['title'];
        }

        $path .= $pDelimiter . $page['title'];

        return $path;

    }

    /**
     * Returns the full human readable path from the current $breadcrumb items
     * delimited with $pDelimiter
     *
     * @param string $pDelimiter Default ' » '
     * @param bool   $pAsArray
     *
     * @static
     */
    public static function getBreadcrumpPath($pDelimiter = ' » ') {
        global $kcache;

        $path = '';
        foreach (kryn::$breadcrumbs as $item) {
            $path .= ($path == '' ? '' : $pDelimiter) . $item['title'];
        }

        return $path;

    }


    /**
     * Returns whether the given page has childs or not
     * $param integer $pRsn
     * @return bool
     * @static
     */
    public static function pageHasChilds($pRsn) {
        $pRsn += 0;

        $page =& self::getPage($pRsn);

        return $page['hasChilds'] ? true : false;
    }

    /**
     * Returns the URL of the specified page
     *
     * @param integer $pRsn
     * @param bool    $pDomainRsn   With domain or without
     * @param boool   $pWithoutHttp With HTTP/S or without
     *
     * @return string
     * @static
     */
    public static function pageUrl($pRsn = 0, $pWithoutHttp = false, $pWithoutStartpageCheck = false) {

        $domain_rsn = kryn::$domain['rsn'];


        if (!$pRsn > 0)
            $pRsn = kryn::$page['rsn'];
        else
            $domain_rsn = kryn::getDomainOfPage($pRsn);

        if ($pWithoutStartpageCheck == false && kryn::$domain['startpage_rsn'] == $pRsn)
            return '.';

        if ($domain_rsn == kryn::$domain['rsn']) {
            $cachedUrls =& kryn::$urls;
            $domain_rsn = kryn::$domain['rsn'];
        } else {
            $cachedUrls =& kryn::getCache('urls-' . $domain_rsn);
        }

        if (!$cachedUrls) {
            require_once(PATH_MODULE . 'admin/adminPages.class.php');
            $cachedUrls = adminPages::updateUrlCache($domain_rsn);
        }

        $url = $cachedUrls['rsn']['rsn=' . $pRsn];

        if ($domain_rsn != kryn::$domain['rsn']) {

            $domain = kryn::getDomain($domain_rsn);

            if ($domain['master'] != 1) {
                $url = $domain['domain'] . $domain['path'] . $domain['lang'] . '/' . $url;
            } else {
                $url = $domain['domain'] . $domain['path'] . $url;
            }

            if (!$pWithoutHttp)
                $url = 'http' . (kryn::$ssl ? 's' : '') . '://' . $url;

            if (substr($url, -1) == '/')
                $url = substr($url, 0, -1);

            if ($url == '/')
                $url = '.';

            return $url;

        }

        if (substr($url, -1) == '/')
            $url = substr($url, 0, -1);

        if ($url == '/')
            $url = '.';

        return $url;
    }

    /**
     * Redirect the user to specified page
     *
     * @param integer $pRsn
     *
     * @static
     */
    public static function redirectToPage($pRsn, $pParams = '') {
        self::redirect(self::pageUrl($pRsn) . ($pParams ? '?' . $pParams : ''));
    }

    /**
     * returns the path of the current request without parameters
     */
    public static function getRequestPath() {
        return self::getRequestPageUrl();
    }

    /**
     * Returns the requested URL without parameter
     * @static
     * @return string
     *
     * @param pWithAdditionalParameter If you want to get KGETs too
     *
     * @internal
     */
    public static function getRequestPageUrl($pWithAdditionalParameter = false) {
        global $_AGET;

        $kurl = $_REQUEST['_kurl'];

        $t = explode('/', $kurl);
        $url = '';
        foreach ($t as $s) {
            if (!strpos($s, '=') > 0 && !strpos($s, ':') && $s != '')
                $url .= $s . '/';
        }


        #if( substr( $url, -2 ) == "//" )
        #    $url = substr( $url, 0, -2 );

        if (substr($url, -1) == "/")
            $url = substr($url, 0, -1);

        if ($pWithAdditionalParameter) {
            if (count($_AGET) > 0) {
                $url .= "?";
                foreach ($_AGET as $key => $val) {
                    $url .= $key . ":" . $val;
                }
            }
        }

        return $url;
    }

    /**
     * Reads all parameter out of the URL and insert them to $_REQUEST
     * @internal
     */
    public static function prepareUrl() {
        global $_AGET;

        $url = $_REQUEST['_kurl'];

        kryn::$url = '';

        $t = explode("/", $url);
        $c = 1;

        foreach ($t as $i) {
            if (strpos($i, "=")) {
                $param = explode("=", $i);
                $_REQUEST[$param[0]] = $param[1];
                $_AGET[$param[0]] = $param[1];
                kryn::$url .= '/' . urlencode($param[0]) . '=' . urlencode($param[1]);
            } elseif (strpos($i, ":")) {
                $param = explode(":", $i);
                $_REQUEST[$param[0]] = $param[1];
                $_AGET[$param[0]] = $param[1];
                kryn::$url .= '/' . urlencode($param[0]) . '=' . urlencode($param[1]);
            } else {
                $_REQUEST['param' . $c] = $i;
                $_REQUEST[$c] = $i;
                kryn::$url .= '/' . urlencode($i);
                $c++;
            }
        }

        kryn::$urlWithGet = kryn::$url;
        $f = false;
        foreach ($_GET as $k => &$v) {
            if ($k == '_kurl') continue;
            if (is_array($v)) continue;
            kryn::$urlWithGet .= (!$f ? '?' : '&') . urlencode($k) . '=' . urlencode($v);
            if ($f == false) $f = true;
        }

        //small securty check for third party modules
        /*
        if( getArgv(1) != 'admin' ){
            $blacklist = array(' union ', 'system_user', 'http://', 'https://');
            foreach( $_GET as $id => &$req ){
                foreach( $blacklist as $key ){
                    if( stripos($req, $key) !== false ){
                        klog('Security', 'Possible attack to your system over attributes! '.$id.': '.$req);
                        kryn::bannUser();
                        die(_l("Kryn.cms has detected an possible attack attempt. Your are banned."));
                    }
                }
            }
        }*/

        tAssignRef('request', $_REQUEST);
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

        $languages = kryn::getCache('systemLanguages');

        if (!$languages) {
            $languages = dbExfetch('SELECT code FROM %pfx%system_langs WHERE visible = 1', -1);
            kryn::setCache('systemLanguages', $languages);
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
                kryn::clearLanguageCache($lang['code']);
            }
            return false;
        }
        $code = 'cacheLang_' . $pLang;
        kryn::setFastCache($code, false);
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

        if (!$pLang) $pLang = kryn::getLanguage();

        if( kryn::$lang && kryn::$lang['__lang'] && kryn::$lang['__lang'] == $pLang && $pForce == false )
            return;

        if (!$pLang) return;

        $code = 'cacheLang_' . $pLang;
        kryn::$lang =& kryn::getFastCache($code);

        $md5 = '';
        foreach (kryn::$extensions as $key) {
            if ($key == 'kryn')
                $md5 .= @filemtime('inc/kryn/lang/' . $pLang . '.po');
            else
                $md5 .= @filemtime('inc/module/' . $key . '/lang/' . $pLang . '.po');
        }

        $md5 = md5($md5);

        if ((!kryn::$lang || count(kryn::$lang) == 0) || kryn::$lang['__md5'] != $md5) {

            kryn::$lang = array('__md5' => $md5, '__plural' => krynLanguage::getPluralForm($pLang), '__lang' => $pLang);

            foreach (kryn::$extensions as $key) {

                $po = krynLanguage::getLanguage($key, $pLang);
                kryn::$lang = array_merge(kryn::$lang, $po['translations']);

            }
            kryn::setFastCache($code, kryn::$lang);
        }

        if (!file_exists(kryn::$config['media_cache'].'gettext_plural_fn_' . $pLang . '.php') ||
            !file_exists(kryn::$config['media_cache'].'gettext_plural_fn_' . $pLang . '.js')) {
            //write gettext_plural_fn_<langKey> so that we dont need to use eval()
            $pos = strpos(kryn::$lang['__plural'], 'plural=');
            $pluralForm = substr(kryn::$lang['__plural'], $pos + 7);

            $code = "<?php \nfunction gettext_plural_fn_$pLang(\$n){\n";
            $code .= "    return " . str_replace('n', '$n', $pluralForm) . ";\n";
            $code .= "}\n?>";
            kryn::fileWrite(kryn::$config['media_cache'].'gettext_plural_fn_' . $pLang . '.php', $code);


            $code = "function gettext_plural_fn_$pLang(n){\n";
            $code .= "    return " . $pluralForm . ";\n";
            $code .= "}";
            kryn::fileWrite(kryn::$config['media_cache'].'gettext_plural_fn_' . $pLang . '.js', $code);
        }

        include_once(kryn::$config['media_cache'].'gettext_plural_fn_' . $pLang . '.php');
    }

    /**
     * Returns domain informations of the specified domain
     *
     * @param unknown_type $pDomainRsn
     *
     * @return array
     * @static
     */
    public static function getDomain($pDomainRsn) {

        $pDomainRsn += 0;
        $cacheKey = 'domain-' . $pDomainRsn;

        if ($pDomainRsn == 0) return;

        if ($cache = kryn::getCache($cacheKey)) {
            return $cache;
        } else {
            $domain = dbExfetch('SELECT * FROM %pfx%system_domains WHERE rsn = ' . $pDomainRsn, 1);

            if ($domain['publicproperties'] && !is_array($domain['publicproperties'])) {
                $domain['themeproperties'] = @json_decode($domain['publicproperties'], true);
            }

            if ($domain['themeproperties'] && !is_array($domain['themeproperties'])) {
                $domain['themeproperties'] = @json_decode($domain['themeproperties'], true);
            }

            if ($domain['session'] && !is_array($domain['session'])) {
                $domain['session'] = @json_decode($domain['session'], true);
            }

            if ($domain['extproperties'] && !is_array($domain['extproperties'])) {
                $domain['extproperties'] = @json_decode($domain['extproperties'], true);
            }

            kryn::setCache($cacheKey, $domain);
        }

        return $domain;
    }

    /**
     * Reads the requested URL and try to extract the requested language.
     * @return string Empty string if nothing found.
     * @internal
     */
    public static function getPossibleLanguage() {

        if (strpos($_REQUEST['_kurl'], '/') > 0)
            $first = substr($_REQUEST['_kurl'], 0, strpos($_REQUEST['_kurl'], '/'));
        else
            $first = $_REQUEST['_kurl'];

        if (self::validLanguage($first)) {
            $_REQUEST['_kurl'] = substr($_REQUEST['_kurl'], strlen($first) + 1); //cut langcode
            return $first;
        }

        return "";
    }


    /**
     * Loads the current domain based in the requested URL
     * @internal
     */
    public static function searchDomain() {
        global $cfg, $languages, $lang, $language;

        $languages =& kryn::getCache('systemLanguages');
        $language = $languages[0];
        tAssignRef("languages", $languages);

        $http = 'http://';
        if ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on') {
            $http = 'https://';
            kryn::$ssl = true;
        }

        $port = '';
        if ((
                ($_SERVER['SERVER_PORT'] != 80 && $http == 'http://') ||
                ($_SERVER['SERVER_PORT'] != 443 && $http == 'https://')
            ) && $_SERVER['SERVER_PORT'] + 0 > 0
        ) {
            $port = ':' . $_SERVER['SERVER_PORT'];
        }
        kryn::$port = $port;

        $domainName = $_SERVER['SERVER_NAME'];

        if (getArgv('kryn_domain') && getArgv('enableSearchIndexMode')) {
            $domainName = getArgv('kryn_domain', 1);
        }

        if (getArgv(1) != 'admin') {

            $possibleLanguage = self::getPossibleLanguage();
            $domains =& kryn::getCache('domains');

            if (!$domains || $domains['r2d']) {
                require_once(PATH_MODULE . 'admin/adminPages.class.php');
                $domains = adminPages::updateDomainCache();
            }

            if ($domains['_redirects'][$domainName]) {
                header("HTTP/1.1 301 Moved Permanently");
                $redirect = kryn::getDomain($domains['_redirects'][$domainName]);
                header('Location: ' . $http . $redirect['domain'] . $redirect['path']);
                exit;
            }

            if ($possibleLanguage == '') {

                $domain = kryn::getDomain($domains[$domainName]);
                $possibleLanguage = $domain['lang'];

            } else {

                $domain = kryn::getDomain($domains[$domainName . '_' . $possibleLanguage]);
                kryn::$baseUrl = $http . $domainName . $port . $cfg['path'] . $possibleLanguage . '/';

                if (!$domain) {
                    $domain = kryn::getDomain($domains[$domainName]);
                    $possibleLanguage = $domain['lang'];
                }

            }

            if (!$domain['rsn'] > 0) {
                klog("system", "Domain <i>$domainName</i> not found. Language: $possibleLanguage");
                kryn::internalError("Domain <i>$domainName</i> not found.");
            }
            $language = $possibleLanguage;
            kryn::$domain = $domain;

            if ($domain['phplocale']) {
                setlocale(LC_ALL, $domain['phplocale']);
            }

            kryn::$domainProperties =& kryn::$domain['extproperties'];
            kryn::$themeProperties =& kryn::$domain['themeproperties'];
            kryn::$publicProperties =& kryn::$domain['themeproperties'];

            if ($domain['path'] != '') {
                tAssignRef('path', $domain['path']);
                $cfg['path'] = $domain['path'];
                $cfg['templatepath'] = $domain['path'] . 'inc/template';
                tAssignRef('cfg', $cfg);
                tAssignRef('_path', $domain['path']);
            }

            $domain['_languagePrefix'] = $possibleLanguage;

            kryn::$baseUrl = $http . $domainName . $port . $cfg['path'];
            if ($domain['master'] != 1 && getArgv(1) != 'admin') {
                kryn::$baseUrl = $http . $domainName . $port . $cfg['path'] . $possibleLanguage . '/';
            }

            tAssignRef("language", $language);

            if (getArgv(1) == 'robots.txt' && $domain['robots'] != "") {
                header('Content-Type: text/plain');
                print $domain['robots'];
                exit();
            }

            if ($domain['favicon'] != "") {
                kryn::addHeader('<link rel="shortcut icon" href="' . kryn::$baseUrl . $domain['favicon'] . '" />');
            }

            tAssignRef('baseUrl', kryn::$baseUrl);
            kryn::$language =& $language;
            kryn::$domain = $domain;
            tAssignRef('domain', kryn::$domain);
            tAssignRef('_domain', $domain); //compatibility
            tAssignRef("lang", $lang);


            $tUrl = explode("?", $_REQUEST["_kurl"]);
            if (substr($tUrl[0], -1) == '/') {
                $get = array();
                foreach ($_GET as $k => $v)
                    if ($k != '_kurl')
                        $get[] = $k . "=" . $v;

                $toUrl = substr($tUrl[0], 0, -1);
                if (count($get) > 0)
                    $toUrl .= '?' . implode("&", $get);

                if (count($_POST) == 0) //only when the browser don't send data
                    kryn::redirect($toUrl);
            }
        }

    }


    /**
     * Checks the specified page.
     * Internal function.
     *
     * @param   array      $page
     * @param   bool       $pWithRedirect
     *
     * @return  array|bool False if no access
     * @internal
     */
    public static function checkPageAccess($page, $pWithRedirect = true) {
        global $client, $adminClient;

        $oriPage = $page;

        if ($page['access_from'] > 0 && ($page['access_from'] > time()))
            $page = false;

        if ($page['access_to'] > 0 && ($page['access_to'] < time()))
            $page = false;

        if ($page['access_from_groups'] != '') {

            $access = false;
            $groups = ',' . $page['access_from_groups'] . ","; //eg ,2,4,5,

            $cgroups = null;
            if ($page['access_need_via'] == 0) {
                $cgroups =& $client->user['groups'];
            } else {
                $htuser = $client->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

                if ($htuser['rsn'] > 0) {
                    $cgroups =& $htuser['groups'];
                }
            }

            if ($cgroups) {
                foreach ($cgroups as $group) {
                    if (strpos($groups, "," . $group['group_rsn'] . ",") !== false) {
                        $access = true;
                    }
                }
            }

            if (!$access && $adminClient) {
                foreach ($adminClient->user['groups'] as $group) {
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
                kryn::redirectToPage($oriPage['access_redirectto']);
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
     * Returns all system_acls for current user
     * @return array
     * @internal
     */
    public static function getPageAcls() {
        global $client;
        if (kryn::$cachedPageAcls) return kryn::$cachedPageAcls;

        $userRsn = $client->user_rsn;
        $inGroups = $client->user['inGroups'];

        kryn::$cachedPageAcls = dbExfetch("
                SELECT code, access FROM %pfx%system_acl
                WHERE
                type = 2 AND
                (
                    ( target_type = 1 AND target_rsn IN ($inGroups))
                    OR
                    ( target_type = 2 AND target_rsn IN ($userRsn))
                )
                ORDER BY code DESC
        ", DB_FETCH_ALL);

        return kryn::$cachedPageAcls;
    }


    /**
     * Checks whether the specified code has specified action
     *
     * @param array  $pCodes
     * @param string $pAction
     *
     * @return array Specified pCode if not found or the parent if found there.
     * @internal
     */
    public static function checkPageAccessHasCode($pCodes, $pAction) {
        $access = in_array($pAction, $pCodes);

        if (!$access) {

            $acl_tab = false;


            foreach (kryn::$pageAcls as $keyAll => $subAll) {

                foreach ($subAll as $tabKey => $tabSubs) {
                    if (in_array($pAction, $tabSubs) && $tabKey != 'tree') {
                        $acl_tab = $tabKey;
                    }
                }
            }

            if ($acl_tab) {
                $access = in_array($acl_tab, $pCodes);
            }
        }
        return $access;
    }

    /**
     * Checks the access of a domain
     *
     * @param integer $pRsn
     * @param string  $pAction
     *
     * @return bool
     */
    public static function checkDomainAccess($pRsn, $pAction) {
        return self::checkPageAcl($pRsn, $pAction, 'd');
    }

    /**
     * Checks the access of a node (page)
     *
     * @param integer $pRsn
     * @param string  $pAction
     *
     * @return bool

     */
    public static function checkNodeAccess($pRsn, $pAction) {
        return self::checkPageAcl($pRsn, $pAction, 'p');
    }

    /**
     * Checks the access of a page or domain
     *
     * @param integer $pRsn
     * @param string  $pAction
     * @param string  $pType p,d
     *
     * @return bool
     * @internal
     */
    public static function checkPageAcl($pRsn, $pAction, $pType = 'p') {
        $acls = kryn::getPageAcls();

        if (count($acls) == 0) return true;

        $access = false;

        $current_rsn = $pRsn;
        $current_type = $pType;

        $not_found = true;
        $parent_acl = false;

        $codes = array();

        while ($not_found) {

            $acl = kryn::_getAcl($current_rsn, $current_type);
            if ($acl && $acl['code']) {

                $code = str_replace(']', '', $acl['code']);
                $t = explode('[', $code);
                $codes = explode(",", $t[1]);

                if (self::checkPageAccessHasCode($codes, $pAction)) {
                    if (
                        ($parent_acl == false) ||
                        ($parent_acl == true && strpos($acl['code'], '%') !== false)
                    ) {
                        $access = ($acl['access'] == 1) ? true : false;
                        $not_found = false;
                    }
                }
            }

            if ($not_found == true && $current_type == 'd') {
                //no parent acl on domain-level
                $not_found = false;
            }

            if ($not_found == true && $current_type == 'p') {
                //search and set parent
                $parent = kryn::getPageAclParent($current_rsn);
                if ($parent['domain']) {
                    //parent is domain
                    $current_rsn = $parent['domain_rsn'];
                    $current_type = 'd';
                } else {
                    $current_rsn = $parent['rsn'];
                }
                $parent_acl = true;
            }
        }
        return $access;
    }

    /**
     * Returns the parent of the acl page
     *
     * @param integer $pRsn
     *
     * @return array
     * @internal
     */
    public static function getPageAclParent($pRsn) {

        $domain_rsn = kryn::getDomainOfPage($pRsn);

        $menus = kryn::getCache('menus-' . $domain_rsn);
        $page_tree = $menus[$pRsn];
        $result = array('prsn' => 0, 'domain_rsn' => $domain_rsn, 'domain' => true);

        if (!$page_tree) return $result;

        $page = $page_tree[count($page_tree) - 1];

        if (count($page_tree) >= 1 && $page) {
            $result = $page;
            $result['domain_rsn'] = $domain_rsn;
        }

        return $result;

    }


    /**
     * Returns the domain of the specified page
     *
     * @param integer $pRsn
     * return integer
     */
    public static function getDomainOfPage($pRsn) {
        $rsn = false;

        $page2Domain =& kryn::getCache('page2Domains');
        if (!is_array($page2Domain)) {
            require_once(PATH_MODULE . 'admin/adminPages.class.php');
            $page2Domain = adminPages::updatePage2DomainCache();
        }

        $pRsn = ',' . $pRsn . ',';
        foreach ($page2Domain as $domain_rsn => &$pages) {
            $pages = ',' . $pages . ',';
            if (strpos($pages, $pRsn) !== false) {
                $rsn = $domain_rsn;
            }
        }
        return $rsn;
    }


    /**
     * Returns the acl infos for the specified id
     *
     * @param integer $pRsn
     * @param string  $pType
     *
     * @return array
     * @internal
     */
    public static function _getAcl($pRsn, $pType) {
        $acl = false;

        $acls = kryn::getPageAcls();
        foreach ($acls as $item) {

            $type = substr($item['code'], 0, 1);
            if ($pType == $type) {

                $code = str_replace('%', '', $item['code']);
                $code = substr($code, 1);
                $t = explode('[', $code);
                $rsn = $t[0];
                if ($rsn == $pRsn) {
                    $acl = $item;
                }
            }
        }
        return $acl;
    }


    /**
     * Search the current page or the startpage, loads all informations and checks the access.
     * Internal function
     * @internal
     */
    public static function searchPage() {
        global $path, $cfg, $_start, $client, $kcache;

        if (getArgv(1) == 'admin') return;

        $url = kryn::getRequestPageUrl();

        tAssign('url', $url);

        $domain = kryn::$domain['rsn'];
        kryn::$urls =& kryn::readCache('urls');

        if (!is_array(kryn::$urls)) {
            require_once(PATH_MODULE . 'admin/adminPages.class.php');
            adminPages::updateUrlCache($domain);
            kryn::$urls =& kryn::readCache('urls');
        }

        //extract extra url attributes
        $found = $end = false;
        $possibleUrl = $next = $url;
        $oriUrl = $possibleUrl;

        do {

            $rsn = kryn::$urls['url']['url=' . $possibleUrl];

            if ($rsn > 0 || $possibleUrl == '') {
                $found = true;
            } else if (!$found) {
                $rsn = kryn::$urls['alias'][$possibleUrl];
                if ($rsn > 0) {
                    $found = true;
                    //we found a alias
                    kryn::redirectToPage($rsn);
                } else {
                    $possibleUrl = $next;
                }
            }

            if ($next == false) {
                $end = true;
            } else {
                //maybe we found a alias in the parens with have a alias with "withsub"
                $aliasRsn = kryn::$urls['alias'][$next . '/%'];

                if ($aliasRsn) {

                    //links5003/test => links5003_5/test

                    $aliasPageUrl = kryn::$urls['rsn']['rsn=' . $aliasRsn];

                    $urlAddition = str_replace($next, $aliasPageUrl, $url);

                    $toUrl = $urlAddition;

                    //go out, and redirect the user to this url
                    kryn::redirect($urlAddition);
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
        tAssignRef('request', $_REQUEST);

        //if the url is a file request we throw a 404 because files have to check via checkFile.php
        if (strpos($oriUrl, ".") !== FALSE) {
            $page = array();
            $url = "404";
            $rsn = 0;
        }

        kryn::$isStartpage = false;

        $pageRsn = 0;

        if ($url == '') {
            $pageRsn = kryn::$domain['startpage_rsn'];

            if (!$pageRsn > 0) {
                kryn::internalError('There is no startpage for domain ' . kryn::$domain['domain']);
            }

            kryn::$isStartpage = true;
        } else {
            $pageRsn = $rsn;
        }

        return $pageRsn;
    }


    /**
     * Initialize the breadcrumb list.
     * Loads current parents and publish the kryn::$breadcrumbs to template.
     * After this, we can add new menu items to the b
     * @internal
     */
    public static function loadBreadcrumb() {
        kryn::$breadcrumbs = kryn::getPageParents();
        tAssignRef('breadcrumbs', kryn::$breadcrumbs);
    }


    public static function &getPage($pPageRsn = false) {
        global $kcache;

        if (!$pPageRsn) {
            $pPageRsn = kryn::searchPage();
        }

        $page = kryn::getCache('page-' . $pPageRsn);

        if (!$page) {

            $page = dbTableFetch('system_pages', 1, "rsn = $pPageRsn");
            $curVersion = dbTableFetch('system_pagesversions', 1, "page_rsn = $pPageRsn AND active = 1");

            $page['extensionProperties'] = json_decode($page['properties'], true);
            $page['properties'] = $page['extensionProperties'];

            if ($page['domain_rsn'] != kryn::$domain['rsn']) {
                $realUrls =& kryn::getCache('urls-' . $page['domain_rsn']);
            } else {
                $realUrls =& kryn::$urls;
            }

            $page['realUrl'] = $realUrls['rsn']['rsn=' . $page['rsn']];

            $page['active_version_rsn'] = $curVersion['rsn'];

            $row = dbExfetch('SELECT rsn FROM %pfx%system_pages WHERE prsn = ' . $pPageRsn . ' LIMIT 1', 1);
            if ($row['rsn'] + 0 > 0)
                $page['hasChilds'] = true;
            else
                $page['hasChilds'] = false;

            kryn::setCache('page-' . $pPageRsn, $page);
            $page = kryn::getCache('page-' . $pPageRsn);
        }


        return $page;

    }

    /**
     * Prints the kryn/404-page.tpl template to the client and exit, if defined redirect the the 404-page
     * defined in the domain settings or opens the 404-interface file which is also defined
     * in the domain settings.
     */
    public static function notFound($pError = '404') {

        klog('404', sprintf(
            $pError . ': ' . _l('Page not found %s'), kryn::$domain['domain'] . '/' . kryn::getRequestPageUrl(true)));

        if (kryn::$domain['page404interface'] != '') {
            if (strpos(kryn::$domain['page404interface'], "inc/template") !== FALSE) {
                include(kryn::$domain['page404interface']);
            } else {
                include('inc/template' . kryn::$domain['page404interface']);
            }
        } else if (kryn::$domain['page404_rsn'] > 0) {
            kryn::redirectToPage(kryn::$domain['page404_rsn'], 'error=' . $pError);
        } else {
            header("HTTP/1.0 404 Not Found");
            tAssign('error', $pError);
            print tFetch('kryn/404-page.tpl');
        }
        exit;
    }

    /**
     * Printts the kryn/internal-error.tpl template to the client and exist.
     */
    public static function internalError($pMsg) {
        tAssign('msg', $pMsg);
        print tFetch('kryn/internal-error.tpl');
        exit;
    }


    /**
     * Loads the layout from the current page and generate header and body HTML. Send to client.
     *
     * @param bool $pReturn Return instead of exit()
     *
     * @internal
     */
    public static function display($pReturn = false) {
        global $_start, $modules, $client;

        kryn::$pageUrl = '/' . kryn::getRequestPageUrl(true); //kryn::$baseUrl.$possibleUrl;

        # search page for requested URL
        $page = kryn::getPage();

        if (!$page) {
            return kryn::notFound('404');
        }

        $page = self::checkPageAccess($page);

        if (!$page || !$page['rsn'] > 0) { //no access
            return kryn::notFound('no_access');
            return false;
        }

        kryn::$canonical = kryn::$baseUrl . kryn::getRequestPageUrl(true);

        $pageCacheKey =
            'systemWholePage-' . kryn::$domain['rsn'] . '_' . kryn::$page['rsn'] . '-' . md5(kryn::$canonical);

        if (kryn::$domainProperties['kryn']['cachePagesForAnons'] == 1 && $client->user['rsn'] == 0 &&
            count($_POST) == 0
        ) {

            $cache =& kryn::getCache($pageCacheKey);
            if ($cache) {
                print $cache;
                exit;
            }

        }

        tAssignRef('realUrls', kryn::$urls);

        if (kryn::$domain['startpage_rsn'] == $page['rsn'] && kryn::$isStartpage) {
            $page['realUrl'] = '';
        }

        if (kryn::$domain['startpage_rsn'] == $page['rsn'] && !kryn::$isStartpage) {
            kryn::redirect(kryn::$baseUrl);
        }

        if ($page['type'] == 1) { //is link
            $to = $page['link'];

            if ($page['link'] + 0 > 0) {
                kryn::redirectToPage($page['link']);
            } else {
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: $to");
            }
            exit;
        }

        if ($page['type'] == 0) { //is page
            if ($page['access_forcessl'] == 1 && kryn::$ssl == false) {
                header('Location: ' . str_replace('http://', 'https://', kryn::$baseUrl) . $page['realUrl']);
                exit;
            }

            foreach (kryn::$themes as $extKey => &$themes) {

                foreach ($themes as $tKey => &$theme) {
                    if ($theme['layouts']) {
                        foreach ($theme['layouts'] as $lKey => &$layout) {
                            if ($layout == $page['layout']) {
                                if (is_array(kryn::$themeProperties)) {
                                    kryn::$themeProperties = kryn::$themeProperties[$extKey][$tKey];
                                    kryn::$publicProperties =& kryn::$themeProperties;
                                }
                            }
                        }
                    }
                }
            }

            tAssignRef('themeProperties', kryn::$themeProperties);
            tAssignRef('publicProperties', kryn::$themeProperties);
        }

        //prepage for ajax
        if (getArgv('kGetPage', 1) != '') {
            if (getArgv('kGetPage') + 0 > 0)
                $page = dbTableFetch("system_pages", 1, "rsn = " . (getArgv('kGetPage', 1) + 0));
            else {
                $url = getArgv('kGetPage', 1);
                if (substr($url, -1) == '/')
                    $url = substr($url, 0, -1);
                $rsn = kryn::$urls['url']['url=' . $url];

                $page = dbTableFetch("system_pages", 1, "rsn = " . ($rsn));

                $page = self::checkPageAccess($page);

            }
            $domainRsn = $page['domain_rsn'];
            $domain = dbTableFetch('system_domains', 1, "rsn = $domainRsn");
            //todo check ACL
        }

        kryn::$page = $page;
        tAssignRef('page', kryn::$page);

        kryn::loadBreadcrumb();
        kryn::$breadcrumbs[] = array(
            'rsn' => kryn::$page['rsn'],
            'title' => (kryn::$page['page_title'] != '' ? kryn::$page['page_title'] : kryn::$page['title'])
        );
        kryn::initModules();

        if (kryn::$keditAccess) {
            kryn::addJs('admin/js/ka.kedit.js');
            kryn::addCss('admin/css/ka.kedit.css');
            kryn::addJs('admin/js/ka.Button.js');
            kryn::addCss('admin/css/ka.Button.css');
        }

        if (kryn::$kedit == true) {
            kryn::addJs('kryn/mootools-core.js');
            kryn::addJs('kryn/mootools-more.js');
            kryn::addJs('admin/kEditLayout.js');
            kryn::addJs('js=global.js/');
            kryn::addCss('admin/kedit.css');
            kryn::addCss('admin/kEditLayout.css');
            //$content = kryn::fileRead( 'inc/template/kryn/layouts/' . kryn::$page['layout'] . '.tpl' );
        }

        if (!file_exists("inc/template/" . kryn::$page['layout']))
            kryn::internalError("Cannot found layout: " . kryn::$page['layout'] . " in page <b>" . kryn::$page['rsn'] .
                                "</b><br />Maybe you removed a theme extension.");


        if (kryn::$page['layout'] == "") {
            kryn::$pageHtml = _l("Error: No layout chosen for this page.");
        } else {
            kryn::$pageHtml = krynHtml::renderPageContents();
        }

        kryn::$pageHtml = str_replace('\[[', '[[', kryn::$pageHtml);
        kryn::replacePageIds(kryn::$pageHtml);

        //htmlspecialchars(urldecode(kryn::$url));
        kryn::$pageHtml = preg_replace('/href="#(.*)"/', 'href="' . kryn::$url . '#$1"', kryn::$pageHtml);

        foreach ($modules as $key => $mod) {
            $modules[$key] = NULL;
        }

        $pageTitle = (kryn::$page['page_title']) ? kryn::$page['page_title'] : kryn::$page['title'];
        $title = str_replace(
            array('%title', '%domain'),
            array(
                $pageTitle,
                $_SERVER['SERVER_NAME']),
            $domain['title_format']);

        kryn::$page['title_full'] = $title;


        //output for json eg.
        if (getArgv('kGetPage') != '') {
            if (getArgv('json') == 1) {
                $page['rsn'] = kryn::$page['rsn'];
                $page['title'] = kryn::$page['title'];
                $page['title_full'] = kryn::$page['title_full'];
                $page['url'] = kryn::$page['url'];
                json(array('content' => kryn::$pageHtml, 'page' => $page));
            } else {
                die(kryn::$pageHtml);
            }
        }

        if (kryn::$disableSearchEngine == false) {
            $resCode = krynSearch::createPageIndex(kryn::$pageHtml);

            if ($resCode == 2) {
                kryn::notFound('invalid-arguments');
            }
        }

        self::removeSearchBlocks(kryn::$pageHtml);


        if (kryn::$domainProperties['kryn']['cachePagesForAnons'] == 1 && $client->user['rsn'] == 0 &&
            count($_POST) == 0
        ) {

            $page = krynHtml::getPage(kryn::$pageHtml);
            kryn::setCache($pageCacheKey, $page, 10);
            print $page;

        } else {
            krynHtml::printPage(kryn::$pageHtml);
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
     * Return the content of a template file.
     *
     * @param string $pPath Path relative to inc/template/
     *
     * @return string
     * @static
     */
    public static function readTempFile($pPath) {
        global $cfg;
        $pPath = str_replace("..", "", $pPath);
        $file = "inc/template/" . $pPath;
        if (!file_exists($file)) return '';
        $handle = @fopen($file, "r");
        $fs = @filesize($file);
        if ($fs > 0)
            $n = @fread($handle, $fs);
        @fclose($handle);
        return $n;
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
     * Writes content to a template file
     *
     * @param string $pPath
     * @param string $pContent
     *
     * @static
     */
    public static function writeTempFile($pPath, $pContent) {
        global $cfg;
        $pPath = str_replace("..", "", $pPath);
        $file = "inc/template/$pPath";
        kryn::fileWrite($file, $pContent);
    }

    /**
     * Writes content to a file
     *
     * @param string $pPath
     * @param string $pContent
     *
     * @static
     */
    public static function fileWrite($pPath, $pContent) {

        $h = @fopen((substr($pPath,0,1) == '/') ? $pPath : PATH . $pPath, 'w');
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
        if (kryn::$cache)
            kryn::$cache->delete($pCode);
    }

    /**
     * Sets a content to the specified cache-key.
     *
     * @param string  $pCode
     * @param string  $pValue
     * @param integer $pTimeout In seconds. Default is one hour
     *
     * @static
     */
    public static function setCache($pCode, $pValue, $pTimeout = false) {
        if (kryn::$cache)
            return kryn::$cache->set($pCode, $pValue, $pTimeout);
        return false;
    }

    /**
     * Marks a code as invalidate until $pTime
     *
     * @param string  $pCode
     * @param integer $pTime Timestamp. Default is time()
     */

    public static function invalidateCache($pCode, $pTime = false) {
        if (kryn::$cache)
            return kryn::$cache->invalidate($pCode, $pTime);
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
        if (kryn::$cache)
            return kryn::$cache->get($pCode);
        return false;
    }

    /**
     * Sets a content to the specified cache-key.
     * This function saves the value in a generated php file
     * as php code or via apc_store.
     * The idea behind this: If the server has active apc or
     * other optcode caching, then this method is way
     * faster then tcp caching-server.
     * Please be sure, that you really want to use that: This
     * is not compatible with load balanced kryn installations
     * and should only be used, if you are really sure, that
     * a other machine in a load balanced scenario does not
     * need informations about this cache.
     * A good purpose for this is for example caching converted
     * local json files (like the installed extension configs).
     *
     * @param string $pCode
     * @param string $pValue
     *
     * @static
     */
    public static function setFastCache($pCode, $pValue) {
        kryn::$cacheFast->set($pCode, $pValue);
    }

    /**
     * Returns the content of the specified cache-key.
     * See kryn::setFastCache for more informations.
     *
     * @param string $pCode
     *
     * @return string
     * @static
     */
    public static function &getFastCache($pCode) {
        return kryn::$cacheFast->get($pCode);
    }

    /**
     * Internal function for returning cache system-informations.
     *
     * @param string $pCode
     *
     * @static
     * @internal
     */
    public static function &readCache($pCode) {
        $rsn = kryn::$domain['rsn'];
        $pCode = str_replace('..', '', $pCode);
        return kryn::getCache($pCode . '-' . $rsn);
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
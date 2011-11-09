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
 * 
 * @author MArc Schmidt <marc@kryn.org>
 */

class kryn {

    /**
    * Contains all history-items, which will be shown as history-navigation.
    * This is filled automaticaly. If you want to add own Page-Hierarchy-Item use Kryn::addMenu( $pName, $pUrl );
    * @type: array
    * @internal
     * $static
    */
    public static $menus;
    
    /**
     * 
     * Contains all additional html header values.
     * Use kryn::addHeader( $pHeader ) to add additional headers.
     * @var array
     * @internal
     * $static
     */
    public static $header = array();
    
    /**
     * 
     * Contains all paths to javascript files as each item
     * Use kryn::addJs( $pPath ) to add javascript files.
     * @var array
     * @internal
     * $static
     */
    public static $jsFiles = array();
    
    /**
     * 
     * Contains all paths to css files as each item.
     * Use kryn::addCss( $pPath ) to add css files.
     * @var array
     * @internal
     * $static
     */
    public static $cssFiles = array('css/kryn_defaults.css');
    
    /**
     * 
     * Contains all translations as key -> value pair. 
     * @var array
     * @static
     * @internal
     */
    public static $lang;
    
    /**
     * 
     * Contains the current language code.
     * Example: 'de', 'en'
     * @var string
     * @static
     * @internal
     */
    public static $language;
    
    /**
     * 
     * Defines the current baseUrl (also use in html <header>)
     * @var string
     * $static
     */
    public static $baseUrl;
    
    /**
     * 
     * Contains the current domain with all information (as defined in the database system_domain)
     * @var array
     * $static
     */
    public static $domain;
    
    /**
     * 
     * Contains the current page with all information
     * @var array
     * @static
     */
    public static $page;
    
    /**
     * 
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
     * 
     * Contains the complete builded HTML.
     * To change this, you can changed it on the destructor in your extension-class.
     * @var string
     * @static
     */
    public static $pageHtml;
    
    
    /**
     * Contains the current requested URL without http://, urlencoded
     * use urldecode(htmlspecialchars(kryn::$url)) to display it in your page.
     * 
     * @var string 
     */
    public static $url;
    
    /**
     * Contains the current requested URL without http:// and with _GET params, urlencoded
     * use urldecode(htmlspecialchars(kryn::$urlWithGet)) to display it in your page.
     *
     * @var string 
     */
    public static $urlWithGet;
    
    /**
     * 
     * Contains the values of the properties from current theme.
     * Template: $currentTheme
     * @var array
     * @static
     */
    public static $currentTheme = array();
    
    /**
     * 
     * Contains the values of the properties from current theme.
     * @var array
     * @deprecated Use $themeProperties instead.
     * @static
     */
    public static $publicProperties = array();
    
     /**
     * 
     * Contains the values of the properties from current theme.
     * @var array
     * @static
     */
    public static $themeProperties = array();
    
    
     /**
     * 
     * Contains the values of the domain-properties from the current domain.
     * @var array
     * @static
     */
    public static $domainProperties = array();
        
    
     /**
     * 
     * Contains the values of the page-properties from the current page.
     * @var array
     * @static
     */
    public static $pageProperties = array();
        
    
    /**
     * 
     * Defines whether the frontend editor is enabled or not.
     * @var bool
     * @static
     * @internal
     */
    public static $kedit = false;
    
     /**
     * 
     * Defines whether force-ssl is enabled or not.
     * @var bool
     * @static
     * @internal
     */
    private static $ssl = false;
    
    /**
     * 
     * Contains the current port
     * @static
     * @var integer
     * @internal
     */
    public static $port = 0;
    
    
    /**
     * 
     * Defines whether the user has acces to the frontend-editor or not.
     * @var bool
     * @static
     * @private
     * @internal
     */
    private static $keditAccess = false;
    
    
    /**
     * 
     * Contains the current slot informations.
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
     * 
     * )
     * @var array
     * $static
     */
    public static $contents;
    
    /**
     * 
     * Defines whether we are at the startpage
     * @var bool
     * $static
     */
    public static $isStartpage;
    
    /**
     * 
     * Contains all config.json as object from all activated extension.
     * @var array
     * $static
     */
    public static $configs;
    
    
    /**
     * 
     * Contains the system config (inc/config.php).
     * @var array
     * $static
     */
    public static $config;
    
    /**
     *
     * Ref to kryn::$config for compatibility
     *
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
     * 
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
    public static $deactivateContentCheck = false;
    
    /**
     * Contains the krynCache object
     * $static
     */
    public static $cache;
    
     /**
     * Contains the krynCache object for file caching
     * See kryn::setFastCache for more informations.
     *
     * $static
     */
    public static $cacheFast;
    
    
    /**
     * Defines whether we are in the administration area or not.
     * Equal to getArgv(1)=='admin'
     *
     *$static
     */
    public static $admin = false;
    
    /**
     * 
     * Contains the ACLs as cache
     * @var array
     * @static
     * @private
     * @internal
     */
    private static $cachedPageAcls = array();
    
    /**
     * Cached object of the current domains's realUrls
     * 
     * @static
     * @var array
     */
    public static $realUrls;
    
    
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
     * 
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
     * 
     * Defines whether this version can compare or not.
     * @var bool
     * @internal
     */
    public static $canCompare = true;
    
    
    /**
     * 
     * If you want to add own Page-Hierarchy-Item use Kryn::addMenu( $pName, $pUrl );
     * This array is use for {navigation id="history|hierarchy"}
     * @param string $pName
     * @param string $pUrl
     * @static
     */
    public static function addMenu( $pName, $pUrl = "" ){

        kryn::$menus[ kryn::$page['rsn'] ][] = array("title" => $pName, "realUrl" => $pUrl);
        tAssignRef("menus", kryn::$menus);
    }

    /**
     * 
     * Adds a new css file to the <header>. Use relative paths from inc/template without a / as start
     * Absolute paths with http:// also possible.
     * @param string $pCss
     * @static
     */
    public static function addCss( $pCss ){

        if( is_array(kryn::$cssFiles) && array_search( $pCss, kryn::$cssFiles ) === false )
            kryn::$cssFiles[] = $pCss;
    }

    /**
     * 
     * Adds a new javascript file to the <header>. Use relative paths from inc/template without a / as start
     * @param string $pJs
     * @static
     */
    public static function addJs( $pJs ){

        if( is_array(kryn::$jsFiles) && array_search( $pJs, kryn::$jsFiles ) === false )
            kryn::$jsFiles[] = $pJs;
    }
    
    /**
     * 
     * Resets all javascript files.
     */
    public static function resetJs(){
        kryn::$jsFiles = array();
    }
    
     /**
     * 
     * Resets all css files.
     */
    public static function resetCss(){
        kryn::$cssFiles = array('css/kryn_defaults.css');
    }
    
    
    /**
     * 
     * Adds additional headers.
     * @param string $pHeader
     * @static
     */
    public static function addHeader( $pHeader ){
        
        if( array_search( $pHeader, kryn::$header ) === false )
            kryn::$header[] = $pHeader;
    }
    
    /**
     * Sets the doctype in the krynHtml class
     *
     * Possible doctypes are:
     * 'html 4.01 strict', 'html 4.01 transitional', 'html 4.01 frameset',
     * 'xhtml 1.0 strict', 'xhtml 1.0 transitional', 'xhtml 1.0 frameset',
     * 'xhtml 1.1 dtd', 'html5'
     * 
     * If you want to add a own doctype, you have to extend the static var:
     *     krynHtml::$docTypeMap['<id>'] = '<fullDocType>';
     *
     * The default is 'xhtml 1.0 transitional'
     *
     * Can also be called through the smarty function {setDocType value='html 4.01 strict'}
     *
     * @param string $pDocType
     */
    public static function setDocType( $pDocType ){
        krynHtml::$docType = $pDocType;
    }
    
    /**
    * Returns the current defined doctype
    * @return string Doctype
    */
    public static function getDocType( ){
        return krynHtml::$docType;
    }

    /**
     * 
     * Returns generated HTML of $pPageRsn.
     * $pIncludedRes: If true, Kryn includes also the proper css/js files: inc/template/css/_pages/<pPageRsn>.css and
     * inc/template/js/_pages/<pPageRsn>.js
     * @param integer $pPageRsn
     * @param bool $pIncludeRes Defines whether the proper css and javascript files should also be included
     * @return string
     * @static
     */
    public static function getPageContent( $pPageRsn, $pIncludeRes = false ){
        global $tpl;
        kryn::$forceKrynContent = true;
        $time = time();
        
        if( $pPageRsn+0 == 0 ) return;

        $pointer_page = dbExfetch( "SELECT * FROM %pfx%system_pages WHERE
            access_denied = '0'
            AND ( access_from = 0 OR ( access_from > 0 AND access_from <= $time ) )
            AND ( access_to = 0 OR ( access_to > 0 AND access_to >= $time ) )
            AND rsn = $pPageRsn" );
        
        
        $pointer_page = kryn::checkPageAccess( $pointer_page );

        if( !$pointer_page || !$pointer_page['rsn'] > 0 )
            return '';

        $pointer_page['properties'] = @json_decode($pointer_page['properties'],true);
        
        kryn::$pageProperties = $pointer_page['properties'];
            
        $oldPage = kryn::$current_page;
        
        if( $pIncludeRes ){
            if( file_exists( 'inc/template/js/_pages/'.$pPageRsn.'.js' ))
                kryn::addJs( 'js/_pages/'.$pPageRsn.'.js' );
            kryn::addCss( 'css/_pages/'.$pPageRsn.'.css' );
        }

        kryn::$current_page = $pointer_page;
        if( $pointer_page['type'] == 3 )
            $_content = tFetch( 'kryn/blankLayout.tpl' );
        else
            $_content = tFetch( $pointer_page['layout'] );

        kryn::$current_page = $oldPage;
        kryn::$forceKrynContent = false;
        return $_content;
    }

    /**
     * 
     * Loads all activated extension configs and tables
     * @internal
     */
    public static function loadModules(){
        global $modules, $kdb, $cfg;

        $tables = array();
        
        $system = array('kryn', 'users', 'admin');
        
        foreach( $system as $mymod ){
            kryn::$configs[ $mymod ] = kryn::getModuleConfig( $mymod );
        }
        
        $kdbmods =& kryn::getCache('active_modules');
        if( !$kdbmods ){
            $kdbmods = dbTableFetch('system_modules', -1, "activated = 1 AND name != 'admin' AND name != 'users'");
            kryn::setCache( 'active_modules', $kdbmods );
        }
        
        foreach( $kdbmods as $mymod ){
            kryn::$configs[ $mymod['name'] ] = kryn::getModuleConfig( $mymod['name'] );
        }

        foreach( kryn::$configs as &$config ){
            if( is_array($config['extendConfig']) ){
                foreach( $config['extendConfig'] as $extendModule => &$extendConfig ){
                    if( kryn::$configs[$extendModule] ){
                        kryn::$configs[$extendModule] = 
                            array_merge_recursive_distinct(kryn::$configs[$extendModule], $extendConfig);
                    }
                }
            }
        }
    }
    
    /**
     * 
     * Load and initialise all activated extension classes.
     * @internal
     */
    public static function initModules(){
        global $modules;
        
        include_once("inc/modules/users/users.class.php");
        $modules['users'] = new users();

        foreach( kryn::$configs as $mod => $config ){
            $classFile = 'inc/modules/'.$mod.'/'.$mod.'.class.php';
            if( $mod != 'admin' && $mod != 'users' ){
                if( file_exists($classFile) ){
                    include_once( $classFile );
                    $modules[$mod] = new $mod();
                }
            }
        }

        self::autoCoreUpdater();
    }
    
    /**
     * 
     * AutoCoreUpdater to update core in older systems
     * @internal
     * @static
     */
    public static function autoCoreUpdater(){
        
        return;
        // ALL under 0.7.0 RELEASES
        if( $GLOBALS['krynInstaller'] != true ){
            if( kryn::$canCompare == true ){
                
                if( kryn::compareVersion('users', '<', '0.7.0') ){
                    require_once("inc/modules/admin/adminModule.class.php");
                    require_once("inc/modules/admin/adminDb.class.php");
                    adminModule::installModule('users', true);
                    $die = true;
                }
                    
                if( kryn::compareVersion('admin', '<', '0.7.0') ){
                    require_once("inc/modules/admin/adminModule.class.php");
                    require_once("inc/modules/admin/adminDb.class.php");
                    adminModule::installModule('admin', true);
                    $die = true;
                }
                
            } else {
                
                //we have to check manually if admin or kryn is not 0.7.0
                if( kryn::$configs['users']['version'] != '0.7.0' ){
                    require_once("inc/modules/admin/adminModule.class.php");
                    require_once("inc/modules/admin/adminDb.class.php");
                    adminModule::installModule('users', true);
                    $die = true;
                }
                
                if( kryn::$configs['admin']['version'] != '0.7.0' ){
                    require_once("inc/modules/admin/adminModule.class.php");
                    require_once("inc/modules/admin/adminDb.class.php");
                    adminModule::installModule('admin', true);
                    $die = true;
                }
            }
            if( $die == true )
                die("System cores updated - Please reloead.");
        }
    }

    /**
     * 
     * Sends a E-Mail in UTF-8
     * @param string $pTo
     * @param string $pSubject
     * @param string $pBody
     * @param string $pFrom If not set, the Email of the current domain is used. If both is not defined the scheme is info@<currentDomain>
     * @static
     */
    public static function sendMail( $pTo, $pSubject, $pBody, $pFrom = false ){
        $pTo = str_replace( "\n", "", $pTo );
        if(! $pFrom ){
            $pFrom = kryn::$domain['email'];
            if( $pFrom == '' )
                $pFrom = 'info@'.kryn::$domain['domain'];
        }
        #$pTo = mb_encode_mimeheader( $pTo, 'utf-8', 'Q' );
        #$pSubject = mb_encode_mimeheader( $pSubject, 'utf-8', 'Q' );
        #$pFrom = mb_encode_mimeheader( $pFrom, 'utf-8', 'Q' );
        @mail( $pTo, '=?UTF-8?B?'.base64_encode($pSubject).'?=', $pBody, 'From: '. $pFrom."\r\n".'Content-Type: text/plain; charset=utf-8' );
    }

    /**
     * 
     * Convert a string to a mod-rewrite compatible string.
     * @param string $pString
     * @return string
     * @static
     */
    public static function toModRewrite($pString){
        $res = @str_replace('ä', "ae", strtolower($pString));
        $res = @str_replace('ö', "oe", $res);
        $res = @str_replace('ü', "ue", $res);
        $res = @str_replace('ß', "ss", $res);
        $res = @preg_replace('/[^a-zA-Z0-9]/', "-", $res);
        $res = @preg_replace('/--+/', '-', $res);
        return $res;
    }

    
    /**
     * 
     * Function to compate two versions with a operator.
        * Max Version: 999.999.999
        * Min Version: 0.0.1
     * @param string $pModuleVersion extension key or a version
     * @param string $pOp <,<=,>,>=,=
     * @param string $pVersion
     * @return bool
     * @static
     */
    public static function compareVersion( $pModuleVersion, $pOp, $pVersion ){

        if( kryn::$configs[$pModuleVersion] )
            $pModuleVersion =  kryn::$configs[$pModuleVersion]['version'];
            
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
        $tversion = $tmajor  * 1000 * 1000;
        $tversion += $tminor * 1000;
        $tversion += $tpatch;
        
        if( $pOp == '<' && $bversion < $tversion )
            return true;
            
        if( $pOp == '<=' && $bversion <= $tversion )
            return true;
            
        if( $pOp == '=<' && $bversion <= $tversion )
            return true;
            
        if( $pOp == '=' && $bversion == $tversion )
            return true;
            
        if( $pOp == '>=' && $bversion >= $tversion )
            return true;
            
        if( $pOp == '=>' && $bversion >= $tversion )
            return true;
            
        if( $pOp == '>' && $bversion > $tversion )
            return true;
        
        return false;
    }
    
    /**
     * 
     * Replaces all page links within the builded HTML to their full URL.
     * @param string $pContent
     * @static
     * @internal
     */
    public static function replacePageIds( &$pContent ){
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
     * 
     * Translates all string which are surrounded with [[ and ]].
     *
     * @param string &$pContent
     * @static
     * @internal
     *
     */
    public static function translate( $pContent ){
        return preg_replace_callback(
            '/([^\\\\]?)\[\[([^\]]*)\]\]/',
            create_function(
                '$pP',
                '
                return $pP[1]._l( $pP[2] );
                '
            ),
            $pContent
        );
    }

    /**
     * 
     * Redirect the user to specified URL within the system.
     * Relative to the baseUrl.
     * @param string $pUrl
     * @static
     */
    public static function redirect( $pUrl = '' ){
        global $cfg;
        
        
        if( strpos($pUrl, 'http') === false ){
            
            if( kryn::$domain['master'] != 1 )
                $pUrl = kryn::$domain['lang'].'/'.$pUrl;
            
            $domain = kryn::$domain['domain'];
            $path = kryn::$domain['path'];
            
            if(substr($domain, 0, -1) != '/')
                $domain .= '/';
            if($path != '' && substr($path, 0, 1) == '/')
                $path = substr($path, 1);
            if($path != '' && substr($path, 0, -1) == '/')
                $path .= '/';
            if($pUrl != '' && substr($path, 0, 1) == '/')
                $pUrl = substr($pUrl, 1);
            
            if( $pUrl == '/' )
                $pUrl = '';

            $pUrl = 'http://'.$domain.$path.$pUrl;
        }
        
        
        header( "HTTP/1.1 301 Moved Permanently" );
        header('Location: ' . $pUrl);
        exit;
    }

    /**
     * 
     * Checks the access to specified /admin pUrl.
     * @param string $pUrl
     * @param array $pUser If you want to use a other user object.
     * @return bool
     * @static
     */
    public static function checkUrlAccess( $pUrl, $pClient = false ){
        
        if( !$pClient )
            global $client;
        else
            $client = $pClient;
            
            
        if( substr($pUrl, 0, 6) != 'admin/' ){
            $pUrl = 'admin/'.$pUrl;
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
        if( !$inGroups ) $inGroups = 0;

        $code = esc($pUrl);
        if( substr( $code, -1 ) != '/' )
            $code .= '/';

        $userRsn = $client->user_rsn;
        
        $acls = dbExfetch( "
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
        
        if( count($acls) > 0 ){
            $firstCode = $acls[0]['code'];
            $count = 1;
            foreach($acls as $acl){
                if( $count == 1 && $acl['access'] == 1 ){
                    //first acl granted access
                    return true;
                }
                if( $count > 1 && $firstCode == $acl['code'] && $acl['access'] == 1 ){
                    //same code as first (same prio) but grant access
                    return true;
                }
                $count++;
            }
            //kein access
            return false;
        } else {
            //unbekannte konstellationen dürfen nix
            return false;
        }
    }

    /**
     * 
     * Checks the access to the administration URLs and redirect to administration login if no access. 
     * @internal
     * @static
     */
    public static function checkAccess(){
        global $client;

        $bypass = array('loadJs', 'loadCss'); 
        if(in_array(getArgv(2), $bypass))
            return true;

        $url = kryn::getRequestPageUrl();
        if( getArgv(1) == 'admin' && !kryn::checkUrlAccess( $url )){
            
            if( getArgv('getLanguage') != '' )
                admin::printLanguage();
            if( getArgv('getPossibleLangs') == '1' )
                admin::printPossibleLangs();
            
            //klog("authentication", "checkAccess: ".$_REQUEST['_kurl']." ACCESS DENIED");
            if( !getArgv(2) ){
                admin::showLogin();
                exit;
            } else {
                json(array('error'=>'access_denied'));
            }
        }
    }
    
    
    /**
     * 
     * Escape ' to \\' to use string in queries which uses ' as string delimiter.
     * @param string $pString
     * @return string Filtered string
     * @deprecated Use the global esc() instead
     * @static
     */
    public static function esc($pString){
        
        $search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
        $replace  = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
        return str_replace($search, $replace, $pString);
    }
    
    /**
     * 
     * Initialize config. Establish connections.
     * @internal
     */
    public static function initConfig(){
        global $cfg;
        
        $cfg['templatepath'] = $cfg['path']."inc/template";

        if( !$cfg['sessiontime'] && !$cfg['session_timeout'] )
            $cfg['session_timeout'] = 3600;

        if( $cfg['sessiontime'] && !$cfg['session_timeout'] )
            $cfg['session_timeout'] = $cfg['sessiontime'];
        
        if( !$cfg['session_tokenid'] ){
            $cfg['session_tokenid'] = 'krynsessionid_ba';
        }
        
        if( !$cfg['auth_class'] )
            $cfg['auth_class'] = 'kryn';
            
        if( !$cfg['show_banner'] )
            $cfg['show_banner'] = 1;
            
        if( !$cfg['session_storage'] )
            $cfg['session_storage'] = 'database';
            
        if( !$cfg['cache_type'] )
            $cfg['cache_type'] = 'files';
        
        tAssignRef('path', $cfg['path']);
        tAssignRef("cfg", $cfg);
        
        if( !$cfg['cronjob_key'] ){
            $cfg['cronjob_key'] = dechex(time()/mt_rand(100, 500));
            kryn::fileWrite('inc/config.php', "<?php \n\$cfg = ".var_export($cfg,true)."\n?>");
        }

        if( $cfg['cache_type'] == 'files' ){
        
            if( !$cfg['cache_params'] || $cfg['cache_params']['files_path'] == '' ){
                $cfg['cache_params']['files_path'] = 'inc/cache/';
            }
        }

        kryn::$cache = new krynCache( $cfg['cache_type'], $cfg['cache_params'] );

        kryn::$cacheFast = new krynCache( 'files', array('files_path' => 'inc/cache/') );
        
        if( !$cfg['template_cache'] )
            $cfg['template_cache'] = 'inc/tcache/';

        if( !is_dir($cfg['template_cache']) ){
            if( !@mkdir($cfg['template_cache']) )
               die('Can not access to or create folder for template caching: '.$cfg['template_cache']);
        }
        
        kryn::$config =& $cfg;
        kryn::$cfg =& $cfg;
    }
    
    public static function initAuth(){
        global $cfg, $user, $client, $adminClient;
                                 
        if( !$cfg['auth_class'] || $cfg['auth_class'] == 'kryn' ){
        
            $adminClient = new krynAuth( $cfg );
            
        } else {
            $ex = explode( '/', $cfg['auth_class'] );
            $class = "inc/modules/".$ex[0]."/".$ex[1].".class.php";
            if( file_exists($class) ){
                require_once( $class );
                $authClass = $ex[1];
                $adminClient = new $authClass( $cfg );

            }
        }

        $adminClient->start();
        $client = $adminClient;
    
        if( getArgv(1) != 'admin' ){

            $sessionDefinition = kryn::$domain['session'];
            $sessionDefinition['session_tokenid'] =
                ($sessionDefinition['session_tokenid'])?$sessionDefinition['session_tokenid']:'krynsessionid';
            
            if( !$sessionDefinition || $sessionDefinition['auth_class'] == 'kryn' ){
                $client = new krynAuth( $sessionDefinition );
            } else {
                $ex = explode( '/', $sessionDefinition['auth_class'] );
                $class = "inc/modules/".$ex[0]."/".$ex[1].".class.php";
                if( file_exists($class) ){
                    require_once( $class );
                    $authClass = $ex[1];
                    $client = new $authClass( $sessionDefinition );
                }
            }
            
            $client->start();
        }
        
        //compatibility with older extension
        $user = $client;
        if( $client->user['rsn'] != 0 ){
            $user->user_logged_in = true;
        }
        
        
    }
    
    /**
     * 
     * Returns the full human-readable path to given pageRsn delimited with » or return the full path as array.
     * @param int $pPageRsn
     * @param bool $pAsArray
     */
    public static function getPagePath( $pPageRsn, $pAsArray = false ){
        global $kcache;
        
        
        $domainRsn = self::getDomainOfPage( $pPageRsn );
        $domain = self::getDomain( $domainRsn );
        $page = self::getPage($pPageRsn, true);
        
        
        
        if( !$kcache['paths'] ){
            $kcache['paths'] = kryn::getCache('menus_'.$domainRsn);
        }
        
        if( !$kcache['paths'][$pPageRsn] ){
            //maybe the cache is old or back-compatibility
            require_once("inc/modules/admin/adminPages.class.php");
            $kcache['paths'] = adminPages::updateMenuCache($domainRsn);
        }
        
        
        if( $pAsArray == true )
            return $kcache['paths'][$pPageRsn];
        
        $path = '';
        if( $domain['master'] != 1 )
            $path = '['.$domain['lang'].'] ';
        $path .= $domain['domain'];
        foreach( $kcache['paths'][$pPageRsn] as $p ){
            $path .= ' » '.$p['title'];
        }
        
        $path .= ' » '.$page['title'];
            
        return $path;
            
    }


    /**
    * Returns whether the given page has childs or not
    * $param integer $pRsn
    * @return bool
    * @static
    */    
    public static function pageHasChilds( $pRsn ){
        $pRsn += 0;

        $domain = self::getDomainOfPage( $pRsn );
    
        $page =& self::getPage( $pRsn, $domain );
        
        return $page['hasChilds'] ? true : false;
    }

    /**
     * 
     * Returns the URL of the specified page
     * @param integer $pRsn
     * @param bool $pDomainRsn With domain or without
     * @param boool $pWithoutHttp With HTTP/S or without
     * @return string
     * @static
     */
    public static function &pageUrl( $pRsn = 0, $pDomainRsn = false, $pWithoutHttp = false ){

        if(! $pRsn > 0 )
            $pRsn = kryn::$page['rsn'];
        
        if( $pDomainRsn )
            kryn::$domain['rsn'] = $pDomainRsn;
            
        if( kryn::$domain['startpage_rsn'] == $pRsn )
            return './'; 
            
        $cachedUrls =& kryn::readCache( 'urls' );
        
        $url = $cachedUrls['rsn'][ 'rsn='.$pRsn];
        
        if( $url == '' || $pDomainRsn != false ){
            require_once('inc/modules/admin/adminPages.class.php');
            
            $r2d = kryn::getPhpCache('r2d');
            if( !is_array($r2d) ) {
                $r2d = adminPages::updatePage2DomainCache();
            }
           
            //find domain of this page
            foreach( $r2d as $domain => $pages ){
                if( strpos(','.$pages, ','.$pRsn.',') !== FALSE ){
                    $target_domain = $domain;
                }
            }
            
            if( !$taget_domain ){
                //backwards compatibility
                $r2d = adminPages::updatePage2DomainCache();
           
                //find domain of this page
                foreach( $r2d as $domain => $pages ){
                    if( strpos(','.$pages, ','.$pRsn.',') !== FALSE ){
                        $target_domain = $domain;
                    }
                }
            }
            
            //'casue its a different domain, we need to tell load this domain
            $domains =& kryn::getPhpCache('domains');
            if( !$domains['r2d'] )
               $domains = adminPages::updateDomainCache();
            
            $domainBackup = kryn::$domain;
            kryn::$domain = $domains['r2d']['rsn='.$target_domain];

            $cachedUrls =& kryn::readCache( 'urls' );
            $url = $cachedUrls['rsn'][ 'rsn='.$pRsn];
            
            if( $pWithoutHttp ){
                
             if( kryn::$domain['master'] != 1 ){
                    $url = kryn::$domain['domain'].kryn::$domain['path'].kryn::$domain['lang'].'/'.$url;
                } else {
                    $url = kryn::$domain['domain'].kryn::$domain['path'].$url;
                }
                
            } else {
            
                if( kryn::$domain['master'] != 1 ){
                    $url = 'http://'.kryn::$domain['domain'].kryn::$domain['path'].kryn::$domain['lang'].'/'.$url;
                } else {
                    $url = 'http://'.kryn::$domain['domain'].kryn::$domain['path'].$url;
                }
            }
            
            kryn::$domain = $domainBackup;
                
            if( substr($url, -1) == '/' )
                $url = substr($url, 0, -1);
                
            if( $url == '/' )
                $url = '/./';
                
            return $url;
            
        }
        
        if( substr($url, -1) == '/' )
            $url = substr($url, 0, -1);
        
        if( $url == '/' )
            $url = '/./';
            
        return $url;
    }

    /**
     * 
     * Redirect the user to specified page
     * @param integer $pRsn
     * @static
     */
    public static function redirectToPage( $pRsn ){
        self::redirect( self::pageUrl( $pRsn ));
    }

    /**
     * 
     * Die if the domain cannot found.
     * @param string $pDomain
     * @internal
     */
    public static function domainNotFound( $pDomain ){
        die( "Domain <i>$pDomain</i> not found." );
    }
    
    /**
    * returns the path of the current request without parameters
    */
    public static function getRequestPath(){
        return self::getRequestPageUrl();
    }

    /**
     * 
     * Returns the requested URL without parameter
     * @static
     * @return string
     * @internal
     */
    public static function getRequestPageUrl( $pWithAdditionalParameter = false ){
        global $_AGET;
        
        $kurl = $_REQUEST['_kurl'];

        $t = explode( '/', $kurl );
        foreach( $t as $s ) {
            if(! strpos( $s, '=' ) > 0 && !strpos( $s, ':' ) && $s != '')
                $url .= $s.'/';
        }


        #if( substr( $url, -2 ) == "//" )
        #    $url = substr( $url, 0, -2 );

        if( substr( $url, -1 ) == "/" )
            $url = substr( $url, 0, -1 );

        if( $pWithAdditionalParameter ){
            if( count($_AGET) > 0 ){
                $url .= "?";
                foreach( $_AGET as $key => $val ){
                    $url .= $key.":".$val;
                }
            }
        }
            
        return $url;
    }

    /**
     * 
     * Reads all parameter out of the URL and insert them to $_REQUEST
     * @internal
     */
    public static function prepareUrl(){
        global $_AGET;

        $url = $_REQUEST['_kurl'];

        kryn::$url = '';
        
        $t = explode("/", $url);
        $c = 1;
        
        foreach($t as $i){
            if(strpos($i, "=")){
                $param = explode("=", $i);
                $_REQUEST[$param[0]] = $param[1];
                $_AGET[$param[0]] = $param[1];
                kryn::$url .= '/'.urlencode($param[0]).'='.urlencode($param[1]);
            } elseif( strpos($i, ":")){
                $param = explode(":", $i);
                $_REQUEST[$param[0]] = $param[1];
                $_AGET[$param[0]] = $param[1];
                kryn::$url .= '/'.urlencode($param[0]).'='.urlencode($param[1]);
            } else {
                $_REQUEST['param'.$c] = $i;
                $_REQUEST[$c] = $i;
                kryn::$url .= '/'.urlencode($i);
                $c++;
            }
        }
        
        kryn::$urlWithGet = kryn::$url;
        $f = false;
        foreach( $_GET as $k => &$v ){
            if( $k == '_kurl' ) continue;
            kryn::$urlWithGet .= (!$f?'?':'&').urlencode($k).'='.urlencode($v);
            if( $f == false ) $f = true;
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
        
        tAssignRef( 'request', $_REQUEST);
    }

    /**
     * 
     * Check whether specified pLang is a valid language
     * @param string $pLang
     * @return bool
     * @internal
     */
    public static function validLanguage( $pLang ){
        if( strlen($pLang) != 2 ) return false;

        $languages = kryn::getCache('systemLanguages');
        
        if( !$languages ) {
            $languages = dbTableFetch('system_langs', -1, 'visible = 1');
            kryn::setCache('systemLanguages', $languages);
        }

        foreach($languages as $l){
            if( $l['code'] == $pLang ){
                return true;
            }
        }
        return false;
    }

    /**
     * 
     * Clears the language chaces
     * @param string $pLang
     * @internal
     */
    public static function clearLanguageCache( $pLang = false ){
        if( $pLang == false ){

            $langs = dbTableFetch( 'system_langs', DB_FETCH_ALL, 'visible = 1' );
            foreach( $langs as $lang ){
                kryn::clearLanguageCache( $lang['code'] );
            }
            return false;
        }
        $code = 'cacheLang_'.$pLang;
        kryn::setPhpCache($code, false);
        @unlink('inc/cache/lang_'.$pLang.'.json');
    }

    /**
     * 
     * Returns all translation of the specified language
     * @param string $pLang de, en, ...
     * @return string
     * @static
     * @internal
     */
    public static function &getAllLanguage($pLang=false){
        global $language;

        if(! $pLang ) $pLang = $language;
        if(! $pLang || is_array($pLang) ) $pLang = 'en';

        $code = 'cacheLang_'.$pLang;
        $lang =& kryn::getCache($code);
        
        if( (!$lang || count($lang) == 0 ) ){
        
            $mods = kryn::$configs;
            $mods['kryn'] = 'kryn';
            $lang = array();
            foreach( $mods as $key => $mod ){
                if( $key != 'kryn' )
                    $json = kryn::fileRead( 'inc/modules/'.$key.'/lang/'.$pLang.'.json' );
                else
                    $json = kryn::fileRead( 'inc/kryn/lang/'.$pLang.'.json' );
                $ar = json_decode($json,true);

                if( is_array($ar) )
                    $lang = array_merge( $lang, $ar );
            }
            kryn::setCache( $code, $lang );
            return kryn::getCache( $code );

        }
        
        return $lang;
    }
    
    /**
     * 
     * Returns domain informations of the specified domain
     * @param unknown_type $pDomainRsn
     * @return array
     * @static
     */
    public static function getDomain( $pDomainRsn ){
     
        $domains = kryn::getPhPCache('domains');

        if( !$domains['r2d'] )
           $domains = adminPages::updateDomainCache();

        return $domains['r2d']['rsn='.$pDomainRsn];
    }

    /**
     * 
     * Reads the requested URL and try to extract the requested language.
     * @return string Empty string if nothing found.
     * @internal
     */
    public static function getPossibleLanguage(){

        if( strpos($_REQUEST['_kurl'], '/') > 0 )
            $first = substr( $_REQUEST['_kurl'], 0, strpos($_REQUEST['_kurl'],'/'));
        else 
            $first = $_REQUEST['_kurl'];

        if( self::validLanguage($first) ){
            $_REQUEST['_kurl'] = substr( $_REQUEST['_kurl'], strlen($first)+1 ); //cut langcode 
            return $first;
        }

        /*if( self::validLanguage($_COOKIE['lang'])){
            return $_COOKIE['lang'];
        }*/

        return "";
    }

    
    /**
     * 
     * Loads the current domain based in the requested URL 
     * @internal
     */
    public static function searchDomain(){
        global $cfg, $languages, $lang, $language;

        $languages =& kryn::getCache('systemLanguages');
        $language = $languages[0];
        tAssignRef("languages", $languages);

        $http = 'http://';
        if( $_SERVER['HTTPS'] == '1'  || strtolower($_SERVER['HTTPS']) == 'on' ){
            $http = 'https://';
            kryn::$ssl = true;
        }

        $port = '';
        if( (
                ($_SERVER['SERVER_PORT'] != 80 && $http == 'http://') || 
                ($_SERVER['SERVER_PORT'] != 443 && $http == 'https://') 
            ) && $_SERVER['SERVER_PORT']+0 > 0
          ){
            $port = ':'.$_SERVER['SERVER_PORT'];
        }
        kryn::$port = $port;

        $domainName = $_SERVER['SERVER_NAME'];
        
        if( getArgv('kryn_domain') && getArgv('enableSearchIndexMode') ){
            $domainName = getArgv('kryn_domain', 1);
        }
        
        if( getArgv(1) != 'admin' ){
            $possibleLanguage = self::getPossibleLanguage();

            $domains =& kryn::getFastCache('domains');

            if( !$domains ){
                require_once('inc/modules/admin/adminPages.class.php');
                adminPages::updateDomainCache();
                $domains =& kryn::getFastCache('domains');
            }

            if( $redirect = $domains['n2r'][$domainName] ){
                header( "HTTP/1.1 301 Moved Permanently" ); 
                header('Location: '.$http.$redirect['domain'].$redirect['path']);
                exit;
            }

            if( $possibleLanguage == ''){ //keine sprache gefunden, nehme die domain ohne lokalosierungsprefix
                //$domain = dbTableFetch('system_domains', 1, "domain = '$domainName' and master = 1");
                $domain = $domains['n2d'][$domainName];
                $possibleLanguage = $domain['lang'];
            } else {
                //$domain = dbTableFetch('system_domains', 1, "domain = '$domainName' and lang = '$possibleLanguage' and master != 1");
                $domain = $domains['n2d'][$domainName.'_'.$possibleLanguage];
                kryn::$baseUrl = $http.$domainName.$port.$cfg['path'].$possibleLanguage.'/';
                                
                if( !$domain ){                
                    $domain = $domains['n2d'][$domainName];
                    $possibleLanguage = $domain['lang'];
                }
            }
            
            if(! $domain['rsn'] > 0 ){
                klog("system", "Domain <i>$domainName</i> not found. Language: $possibleLanguage");
                die("Domain <i>$domainName</i> not found.".$_REQUEST['_kurl']);
            }
            $language = $possibleLanguage;
            kryn::$domain = $domain;
        }
        
        if( $domain['phplocale'] ){
            setlocale( LC_ALL, $domain['phplocale'] );
        }
        
        if( $domain['publicproperties'] && !is_array($domain['publicproperties']) ){
            kryn::$themeProperties = @json_decode($domain['publicproperties'], true);
        }
        
        if( $domain['themeproperties'] && !is_array($domain['themeproperties']) ){
            kryn::$themeProperties = @json_decode($domain['themeproperties'], true);
        }
        
        if( $domain['session'] && !is_array($domain['session']) ){
            $domain['session'] = @json_decode($domain['session'], true);
        }
    
        if( $domain['extproperties'] && !is_array($domain['extproperties']) ){
            $domain['extensionProperties'] = @json_decode($domain['extproperties'], true);
        }

        $domain['extproperties'] = &$domain['extensionProperties'];
        kryn::$domainProperties = &$domain['extensionProperties'];
            
        #setCookie("lang", $language, time()+3600*24*300, "/"); # 300 Days
        if( getArgv(1) == 'admin' ){
            $domain['path'] = str_replace( 'index.php', '', $_SERVER['SCRIPT_NAME'] );
        } 
        
        if( $domain['path'] != '' ){
            tAssignRef( 'path', $domain['path']);
            $cfg['path'] = $domain['path'];
            $cfg['templatepath'] = $domain['path'].'inc/template';
            tAssignRef( 'cfg', $cfg );
            tAssignRef( '_path', $domain['path']);
        }
        
        $domain['_languagePrefix'] = $possibleLanguage;

        kryn::$baseUrl = $http.$domainName.$port.$cfg['path'];
        if( $domain['master'] != 1 && getArgv(1) != 'admin'){
            kryn::$baseUrl = $http.$domainName.$port.$cfg['path'].$possibleLanguage.'/';
        }

        tAssignRef("language", $language);
        
        if( getArgv(1) == 'robots.txt' && $domain['robots'] != "" ){
            header('Content-Type: text/plain');
            print $domain['robots'];
            exit();
        }
        
        if( $domain['favicon'] != "" ){
            kryn::addHeader('<link rel="shortcut icon" href="'.kryn::$baseUrl.$domain['favicon'].'" />');
        }

        tAssignRef('baseUrl', kryn::$baseUrl);
        kryn::$lang = kryn::getAllLanguage( $language );
        kryn::$language =& $language;
        kryn::$domain = $domain;
        tAssignRef( 'domain', $domain );
        tAssignRef( '_domain', $domain ); //compatibility
        tAssignRef("lang", $lang);
        
        
        $tUrl = explode("?", $_REQUEST["_kurl"]);
        if( getArgv(1) != 'admin' && substr($tUrl[0], -1) == '/' ){
            $get = array();
            foreach( $_GET as $k => $v )
                if( $k != '_kurl')
                    $get[] = $k."=".$v;
            
            $toUrl = substr($tUrl[0], 0, -1);
            if( count($get) > 0 )
                $toUrl .= '?'.implode("&", $get);
                
            if( count($_POST) == 0 )//only when the browser don't send data
                kryn::redirect( $toUrl );
        }
        
    }
    
    
    
    /**
     * 
     * Checks the specified page.
     * Internal function.
     * @param array $page
     * @param bool $pWithRedirect
     * @return array|bool False if no access
     * @internal
     */
    public static function checkPageAccess( $page, $pWithRedirect = true ){
        global $client, $adminClient;
        
        $oriPage = $page;
        if( $page['access_from'] > 0 && ($page['access_from'] > time() ))
            $page = false;

        if( $page['access_to'] > 0 && ($page['access_to'] < time() ))
            $page = false;
            
        if( $page['access_from_groups'] != '' ){
            
            $access = false;
            $groups = ','.$page['access_from_groups'].","; //eg ,2,4,5,
            
            $cgroups = null;
            if( $page['access_need_via'] == 0 ){
                $cgroups =& $client->user['groups'];
            } else {
                $htuser = $client->login( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );
                
                if( $htuser > 0 && $htuser['passwd'] == $p ){ 
                    $cgroups =& $htuser['groups'];
                }
            }
            
            if( $cgroups ){
                foreach( $cgroups as $group ){
                    if( strpos($groups, ",".$group['group_rsn'].",") !== false ){
                        $access = true;
                    }
                }
            }
            
            if(!$access) {
                foreach($adminClient->user['groups'] as $group) {
                    if( strpos($groups, ",".$group.",") !== false) {
                        $access = true;
                        break;
                    }
                }
            }
            
            if( !$access ){
                $page = false;
            }
        }
    
        
        if( !$page && $pWithRedirect && $oriPage['access_need_via'] == 0 ){
        
            if( $oriPage['access_redirectto']+0 > 0 )
                kryn::redirectToPage( $oriPage['access_redirectto'] );
        }
        
        if( !$page && $pWithRedirect && $oriPage['access_need_via'] == 1 ){
            header('WWW-Authenticate: Basic realm="'._l('Access denied. Maybe you are not logged in or have no access.').'"');
            header('HTTP/1.0 401 Unauthorized');
                
            exit;
        }
        return $page;
    }
    
    
    /**
     * 
     * Returns all system_acls for current user
     * @return array
     * @internal
     */
    public static function getPageAcls(){
        global $client;
        if( kryn::$cachedPageAcls ) return kryn::$cachedPageAcls;
        
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
     * 
     * Checks whether the specified code has specified action
     * @param array $pCodes
     * @param string $pAction
     * @return array Specified pCode if not found or the parent if found there.
     * @internal
     */
    public static function checkPageAccessHasCode( $pCodes, $pAction ){
        $access = in_array( $pAction, $pCodes );
        
        if( !$access ){
        
            $acl_tab = false;
            
            
            foreach( kryn::$pageAcls as $keyAll => $subAll ){
                
                foreach( $subAll as $tabKey => $tabSubs ){
                    if( in_array( $pAction, $tabSubs ) && $tabKey != 'tree' ){
                         $acl_tab = $tabKey;
                    }
                }
            }
            
            if( $acl_tab ){
                $access = in_array( $acl_tab, $pCodes );
            }
        }
        return $access;
    }
    
    /** 
     * Checks the access of a domain
     *
     * @param integer $pRsn
     * @param string $pAction
     * @return bool
     */
    public static function checkDomainAccess( $pRsn, $pAction ){
        return self::checkPageAcl( $pRsn, $pAction, 'd' );
    }
    
     /** 
     * Checks the access of a node (page)
     *
     * @param integer $pRsn
     * @param string $pAction
     * @return bool
     *
     */
    public static function checkNodeAccess( $pRsn, $pAction ){
        return self::checkPageAcl( $pRsn, $pAction, 'p' );
    }
    
    /**
     * Checks the access of a page or domain
     *
     * @param integer $pRsn
     * @param string $pAction
     * @param string $pType p,d
     * @return bool
     * @internal
     */
    public static function checkPageAcl( $pRsn, $pAction, $pType = 'p' ){
        $acls = kryn::getPageAcls();

        if( count($acls) == 0 ) return true;
        
        $access = false;
        
        $current_rsn = $pRsn;
        $current_type = $pType;
            
        $not_found = true;
        $parent_acl = false;
        
        $codes = array();
        
        while( $not_found ){
        
            $acl = kryn::_getAcl( $current_rsn, $current_type );
            if( $acl && $acl['code'] ){
                
                $code = str_replace(']', '', $acl['code']);
                $t = explode('[', $code);
                $codes = explode(",", $t[1]);
                
                if( self::checkPageAccessHasCode( $codes, $pAction ) ){
                    if ( 
                            ($parent_acl == false) ||
                            ($parent_acl == true && strpos($acl['code'], '%') !== false)
                        ){
                        $access = ($acl['access'] == 1) ? true : false;
                        $not_found = false;
                    }
                }
            }
            
            if( $not_found == true && $current_type == 'd' ){
                //no parent acl on domain-level
                $not_found = false;
            }
            
            if( $not_found == true && $current_type == 'p' ){
                //search and set parent
                $parent = kryn::getPageAclParent( $current_rsn );
                if( $parent['domain'] ){
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
     * 
     * Returns the parent of the acl page
     * @param integer $pRsn
     * @return array
     * @internal
     */
    public static function getPageAclParent( $pRsn ){
           $domain_rsn = kryn::getDomainOfPage( $pRsn );
    
           $menus = kryn::getPhpCache( 'menus_'.$domain_rsn );
        $page_tree = $menus[$pRsn]; //$H(ka.settings['menus_'+domain_rsn]).get( pRsn );
        $result = array('prsn' => 0, 'domain_rsn' => $domain_rsn, 'domain' => true);
        
        if( !$page_tree ) return $result;
        
        $page = $page_tree[ count($page_tree)-1 ];
        
        if( count($page_tree) >= 1 && $page ){
            $result = $page;
            $result['domain_rsn'] = $domain_rsn;
        }
        
        return $result;
        
    }
    
    
    /**
     * 
     * Returns the domain of the specified page
     * @param integer $pRsn
     * return integer
     */
    public static function getDomainOfPage( $pRsn ){
        $rsn = false;
        $r2d =& kryn::getCache('r2d');
        if( !is_array($r2d) ) {
            require_once('inc/modules/admin/adminPages.class.php');
            $r2d = adminPages::updatePage2DomainCache();
        }
        
        $pRsn = ','.$pRsn.',';
        foreach( $r2d as $domain_rsn => $pages ){
            $pages = ','.$pages.',';
            if( strpos($pages, $pRsn) !== false ){
                $rsn = $domain_rsn;
            }
        }
        return $rsn;
    }
    
    
    /**
     * 
     * Returns the acl infos for the specified id
     * @param integer $pRsn
     * @param string $pType
     * @return array
     * @internal
     */
    public static function _getAcl( $pRsn, $pType ){
        $acl = false;
        
        $acls = kryn::getPageAcls();
        foreach( $acls as $item ){
            
            $type = substr($item['code'], 0, 1);
            if( $pType == $type ){
                
                $code = str_replace('%', '', $item['code']);
                $code = substr($code, 1);
                $t = explode('[', $code);
                $rsn = $t[0];
                if( $rsn == $pRsn ){
                    $acl = $item;
                }
            } 
        }
        return $acl;
    }
    
    
    /**
     * 
     * Search the current page or the startpage, loads all informations and checks the access.
     * Internal function
     * @internal
     */
    public static function searchPage(){
        global $path, $cfg, $_start, $client, $kcache;

        if( getArgv(1) == 'admin' ) return;

        $url = kryn::getRequestPageUrl();

        tAssign( 'url', $url );
        
        $domain = kryn::$domain['rsn'];
        $kcache['realUrl'] = kryn::readCache( 'urls' );

        if( !is_array($kcache['realUrl']) ){
            require_once( 'inc/modules/admin/adminPages.class.php' );
            $kcache['realUrl'] = adminPages::updateUrlCache( $domain );
            $kcache['paths'] = adminPages::updateMenuCache( $domain );
            //adminPages::updatePageCaches( $domain );
        }
        
        //extract extra url attributes
        $found = $end = false;
        $possibleUrl = $next = $url;
        $oriUrl = $possibleUrl;
        
        do {
            
            $rsn = $kcache['realUrl']['url'][ 'url='.$possibleUrl ];
            
            if( $rsn > 0 || $possibleUrl == '' ){
                $found = true;
            } else if ( !$found ){
                $rsn = $kcache['realUrl']['alias'][ $possibleUrl ];
                if( $rsn > 0 ){
                    $found = true;
                    //we found a alias
                    kryn::redirectToPage($rsn);
                } else {
                    $possibleUrl = $next;
                }
            }
            
            if( $next == false ){
                $end = true;
            } else {
                //maybe we found a alias in the parens with have a alias with "withsub"
                $aliasRsn = $kcache['realUrl']['alias'][ $next.'/%' ];
                
                if( $aliasRsn ){
                
                    //links5003/test => links5003_5/test
                    
                    $aliasPageUrl = $kcache['realUrl']['rsn'][ 'rsn='.$aliasRsn ];
                    
                    $urlAddition = str_replace( $next, $aliasPageUrl, $url);

                    $toUrl = $urlAddition;
                    
                    //go out, and redirect the user to this url
                    kryn::redirect($urlAddition);
                    $end = true;
                }
            }
            
            $pos = strrpos($next,'/');
            if( $pos !== false )
                $next = substr( $next, 0, $pos);
            else
                $next = false;
            
        } while( !$end );
        
        $diff = substr( $url, strlen( $possibleUrl ), strlen( $url ));

        if( substr($diff, 0,1) != '/' )
            $diff = '/'.$diff;
            
        $extras = explode( "/", $diff );
        if( count($extras) > 0 ){
            foreach( $extras as $nr => $extra ){
                $_REQUEST['e'.$nr] = $extra;
            }
        }
        $url = $possibleUrl;
        tAssignRef('request', $_REQUEST);
        
        //if the url is a file request we throw a 404 because files have to check via checkFile.php
        if( strpos($oriUrl, ".") !== FALSE ){
             $page = array();
             $url = "404";
             $rsn = 0;
        }

        kryn::$isStartpage = false;
        
        $pageRsn = 0;
        
        if( $url == '' ){
            $page = dbExfetch(
               "SELECT p.*
                FROM %pfx%system_pages p, %pfx%system_domains d
                WHERE 
                    p.domain_rsn = $domain
                    AND access_denied = '0'
                    AND p.rsn = d.startpage_rsn" );
            
            kryn::$isStartpage = true;
            
            if(! $page['rsn'] > 0 )
                die('There is no startpage for domain '.kryn::$domain['domain']);
                
            $pageRsn = $page['rsn'];
            
        } else {
            
            $pageRsn = $rsn;
            /*
            if( $rsn > 0 ) {
                $sql = "SELECT p.*
                    FROM %pfx%system_pages p
                    WHERE rsn = $rsn AND access_denied = '0'"; 
                $page = dbExfetch( $sql );
                
            }*/
        }
        
        return $pageRsn;
    }

    
    /**
     * 
     * Publish the kryn::$menus to template.
     * @internal
     */
    public static function loadMenus(){
        kryn::$menus =& kryn::getCache( 'menus_'.kryn::$domain['rsn'] );
        if( count(kryn::$menus) == 0 ){
            require_once( "inc/modules/admin/adminPages.class.php" );
            adminPages::updateMenuCache( kryn::$domain['rsn'] );
        }
        tAssignRef( 'menus', kryn::$menus );
    }
    
    
    public static function &getPage( $pPageRsn = false , $pReloadCache = false ){
        
        $pPageRsn += 0;
        
        if( !$pPageRsn ){
            $pPageRsn = kryn::searchPage();
        }
        
        $page = kryn::getCache('page_'.$pPageRsn);
        
        if( !$page || $pReloadCache == true ){

            $page = dbTableFetch('system_pages', 1, "rsn = $pPageRsn");
            $curVersion = dbTableFetch('system_pagesversions', 1, "page_rsn = $pPageRsn AND active = 1");

            $page['extensionProperties'] = json_decode($page['properties'],true);
            $page['properties'] = $page['extensionProperties'];
            $page['realUrl'] = $kcache['realUrl']['rsn'][ 'rsn='.$page['rsn'] ];
            $page['active_version_rsn'] = $curVersion['rsn'];

            $row = dbExfetch('SELECT rsn FROM %pfx%system_pages WHERE prsn = '.$pPageRsn.' LIMIT 1', 1);
            if( $row['rsn']+0 > 0 )
                $page['hasChilds'] = true;
            else
                $page['hasChilds'] = false;
            
            kryn::setCache('page_'.$pPageRsn, $page);
            $page = kryn::getCache('page_'.$pPageRsn);
        }
            
        
        return $page;
        
    }

    
    /**
     * 
     * Returns all contents of the slot of the specified page.
     * @param integer $pRsn
     * @param integer $pBoxId
     * @return array
     * @static
     */
    public static function &getPageContents( $pRsn, $pBoxId = false, $pWithoutCache = false ){
        global $time, $client, $kcache;
        
        $pRsn = $pRsn+0;
        
        $time = time();
        $page = kryn::getPage( $pRsn );
        
        if( $page['access_from']+0 > 0 && $page['access_from'] <= $time )
            return array();
            
        if( $page['access_to']+0 > 0 && $page['access_to'] >= $time )
            return array();
            
        if( $page['access_denied'] == 1 )
            return array();

        $result =& kryn::getCache('page_contents_'.$pRsn);
        if( $result && !$pWithoutCache ) return $result;

        $versionRsn = $page['active_version_rsn'];
        
        //todo read acl from table
        $aclCanViewOtherVersions = true;

        if( kryn::$page['rsn'] == $pRsn && getArgv('kVersionId')+0 > 0 && $aclCanViewOtherVersions ){
            $versionRsn = getArgv('kVersionId')+0;
        }
        
        $box = '';
        if( $pBoxId ){
            $box = "AND box_id = $pBoxId";
        }
        
        if( $versionRsn > 0 ){

            $res = dbExec( "
            SELECT c.*
            FROM
                %pfx%system_contents c,
                %pfx%system_pagesversions v
            WHERE 
                v.rsn = $versionRsn 
                AND v.page_rsn = $pRsn
                AND c.version_rsn = v.rsn
                $box
                AND c.hide != 1
                AND ( c.cdate > 0 AND c.cdate IS NOT NULL )
            ORDER BY c.sort" );
            
            while( $page = dbFetch($res) ){
                if(kryn::checkPageAccess($page, false) !== false)
                   $result[$page['box_id']][] = $page;
            }
                
        } else {
        
            //compatibility o old kryns <=0.7
            $result = array();
            $res = dbExec( "SELECT * FROM %pfx%system_contents
                WHERE page_rsn = $pRsn 
                $box 
                AND version_rsn = 1 
                AND hide != 1
                ORDER BY sort" );
            while( $page = dbFetch($res) ){
                $result[$page['box_id']][] = $page;
            }   
        }
        kryn::setCache('page_contents_'.$pRsn, $result);
        return kryn::getCache('page_contents_'.$pRsn);
    }

    public static function notFound(){
        
        if( kryn::$domain['page404interface'] != '' ){
            if( strpos( kryn::$domain['page404interface'], "inc/template" ) !== FALSE ){
                include(kryn::$domain['page404interface']);
            } else {
                include('inc/template'.kryn::$domain['page404interface']);
            }
        } else if( kryn::$domain['page404_rsn'] > 0 ){
            kryn::redirectToPage( kryn::$domain['page404_rsn'] );
        } else {
            header("HTTP/1.0 404 Not Found");
            print tFetch( 'kryn/404-page.tpl' );
        }
        die();
    }
    
    
    /**
     * 
     * Loads the layout from the current page and generate header and body HTML. Send to client.
     * @param bool $pReturn Return instead of exit()
     * @internal
     */
    public static function display( $pReturn = false ){
        //mi add
        global $_start, $modules, $_AGET;

        kryn::$pageUrl = '/'.kryn::getRequestPageUrl(true); //kryn::$baseUrl.$possibleUrl;
        
        # search page for requested URL
        $page = kryn::getPage();
        
        $page = self::checkPageAccess( $page );
        if( !$page || !$page['rsn'] > 0 ){ //no access
            return false;
        }
        
        tAssignRef( 'realUrls', $kcache['realUrl'] );

        if( kryn::$domain['startpage_rsn'] == $page['rsn'] && kryn::$isStartpage ){
            $page['realUrl'] = '';
        }
    
        if( kryn::$domain['startpage_rsn'] == $page['rsn'] && !kryn::$isStartpage ){
            krynSearch::toBlacklist();
        
            kryn::redirect( kryn::$baseUrl );
        }
        
        if( $page['unsearchable'] == 1 ){
            krynSearch::toBlacklist();
        }
        
        if( $page['type'] == 1 ){//is link
            $to = $page['link'];
         
            krynSearch::toBlacklist();
            if( $page['link']+0 > 0 ){
                kryn::redirectToPage( $page['link'] );
            } else {
                header( "HTTP/1.1 301 Moved Permanently" ); 
                header("Location: $to");
            }
            exit;
        }
        
        if( $page['type'] == 0 ){//is page
            if( $page['access_forcessl'] == 1 && kryn::$ssl == false ){
                header('Location: '.str_replace('http://', 'https://',kryn::$baseUrl).$page['realUrl']);
                exit;
            }
    

            foreach( kryn::$configs as $extKey => &$mod ){
                if( $mod['themes'] ){
                    foreach( $mod['themes'] as $tKey => &$theme ) {
                        if( $theme['layouts'] ) {
                            foreach( $theme['layouts'] as $lKey => &$layout ){
                                if( $layout == $page['layout'] ){
                                    if( is_array(kryn::$themeProperties) ){
                                        kryn::$themeProperties = kryn::$themeProperties[$extKey][$tKey];
                                        kryn::$publicProperties =& kryn::$themeProperties;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            tAssignRef( 'themeProperties', kryn::$themeProperties );
            tAssignRef( 'publicProperties', kryn::$themeProperties );
        }

        //prepage for ajax
        if( getArgv('kGetPage', 1) != '' ){
            if( getArgv('kGetPage')+0 > 0 )
                $page = dbTableFetch( "system_pages", 1, "rsn = ".(getArgv('kGetPage',1)+0));
            else {
                $url = getArgv('kGetPage', 1);
                if( substr($url, -1 ) == '/' )
                    $url = substr($url, 0, -1);
                $rsn = $kcache['realUrl']['url'][ 'url='.$url ];
                $page = dbTableFetch( "system_pages", 1, "rsn = ".($rsn));
                
                $page = self::checkPageAccess( $page );
                
            }
            $domainRsn = $page['domain_rsn'];
            $domain = dbTableFetch('system_domains', 1, "rsn = $domainRsn");
            //todo check ACL
        }
        
        kryn::$page =& $page;
        tAssignRef( 'page', kryn::$page );
        
        kryn::$canonical = kryn::$baseUrl.kryn::getRequestPageUrl(true);

        if( kryn::$page['cache'] == "1" && $pReturn == false ){ //only in frontend and not in searchindex mode 
            print kryn::fileRead( "inc/cache/_pages/".kryn::$page['rsn'].".html" );
            exit;
        }

        kryn::loadMenus();
        kryn::$menus[ kryn::$page['rsn'] ][] = kryn::$page;
        kryn::initModules();


        if( kryn::$keditAccess ){
            kryn::addJs( 'admin/js/ka.kedit.js' );
            kryn::addCss( 'admin/css/ka.kedit.css' );
            kryn::addJs( 'admin/js/ka.Button.js' );
            kryn::addCss( 'admin/css/ka.Button.css' );
        }

        if( kryn::$kedit == true ){
            kryn::addJs( 'kryn/mootools-core.js' );
            kryn::addJs( 'kryn/mootools-more.js' );
            kryn::addJs( 'admin/kEditLayout.js' );
            kryn::addJs( 'js=global.js/' );
            kryn::addCss( 'admin/kedit.css' );
            kryn::addCss( 'admin/kEditLayout.css' );
            //$content = kryn::fileRead( 'inc/template/kryn/layouts/' . kryn::$page['layout'] . '.tpl' );
        }

        if( !file_exists( "inc/template/".kryn::$page['layout']) )
            die("Cannot found layout: ".kryn::$page['layout']." in page <b>". kryn::$page['rsn']."</b><br />May you remove/deactivate a theme extension." );


        if( kryn::$page['layout'] == "" ){
            $content = _l("Error: No layout choosen for this page.");
        } else {
            $content = kryn::renderPageContents(); //tFetch( kryn::$page['layout'] );
        }

        $content = str_replace('\[[', '[[', $content);
        kryn::replacePageIds( $content );

        
        //htmlspecialchars(urldecode(kryn::$url));
        $content = preg_replace('/href="#(.*)"/', 'href="'.kryn::$url.'#$1"', $content);
        kryn::$pageHtml =& $content;
        
        foreach( $modules as $key => &$mod ){
            $modules[ $key ] = NULL;
        }
               
        $pageTitle = ( kryn::$page['page_title'] ) ? kryn::$page['page_title'] : kryn::$page['title'];
        $title = str_replace(
            array('%title', '%domain'),
            array(
                $pageTitle,
                $_SERVER['SERVER_NAME']),
            $domain['title_format']);
        
        kryn::$page['title_full'] = $title;
        

        //output for json eg.
        if( getArgv('kGetPage') != ''){
            if( getArgv('json') == 1 ){
                $page['rsn'] = kryn::$page['rsn'];
                $page['title'] = kryn::$page['title'];
                $page['title_full'] = kryn::$page['title_full'];
                $page['url'] = kryn::$page['url'];
                json( array('content' => kryn::$pageHtml, 'page' => $page) );
            } else {
                die( kryn::$pageHtml );
            }
        }
        
        if( !getArgv('enableSearchIndexMode') )
                krynSearch::$returnCodes = true;
        
        $resCode = krynSearch::createPageIndex(kryn::$pageHtml);
        
        if( $resCode == 2 ){
            kryn::redirect(krynSearch::$redirectTo);
        }
        
        kryn::$pageHtml = self::removeSearchBlocks( kryn::$pageHtml );
        
        kryn::$pageHtml = krynHtml::buildPage( kryn::$pageHtml ); 

        //replace all href="#anchor" with valid ones //workaround for <base>
        
        if( $pReturn )
            return kryn::$pageHtml;

        print kryn::$pageHtml;
        exit;
    }
    
    public static function removeSearchBlocks( $pHtml ){
        $res = str_replace('<!--unsearchable-begin-->', '', $pHtml);
        return str_replace('<!--unsearchable-end-->', '', $res);
    }

    /**
     * 
     * Deactivates the 404 content check
     */
    public static function deactivateContentCheck(){
  
        self::$deactivateContentCheck = true;
        
    }

    /**
     * 
     * Compress given string
     * @param string $pString
     * @return string
     * @static
     * @internal
     */
    public static function compress($pString){
        $res = $pString;
        $res = preg_replace('/\s\s+/', ' ', $res);
        $res = preg_replace('/\t/', '', $res);
        $res = preg_replace('/\n\n+/', "\n", $res);
        return $res;
    }

    /**
     * 
     * Return the content of a template file.
     * @param string $pPath Path relative to inc/template/
     * @return string
     * @static
     */
    public static function readTempFile($pPath){
        global $cfg;
        $pPath = str_replace("..", "", $pPath);
        $file = "inc/template/".$pPath;
        if( !file_exists($file) ) return '';
        $handle = @fopen($file,"r");
        $fs = @filesize($file);
        if( $fs > 0 )
            $n = @fread($handle, $fs);
        @fclose($handle);
        return $n;
    }

    /**
     * 
     * Return the content of a file
     * @param string $pPath Relative to installation dir
     * @return string
     * @static
     */
    public static function fileRead( $pPath ){
        $file = $pPath;
        if( !file_exists($file) ) return '';
        $handle = @fopen($file, "r");
        $fs = @filesize($file);
        if( $fs > 0 )
            $n = @fread($handle, $fs);
        @fclose($handle);
        return $n;
    } 

    /**
     * 
     * Returns the config hash of the specified extension.
     * @param string $pModule
     * @return array All config values from the config.json
     * @static
     */
    public static function getModuleConfig( $pModule, $pLang = false ){
        $pModule = str_replace( ".","", $pModule );

        if( $pModule == 'kryn' )
            $config = "inc/kryn/config.json";
        else
            $config = "inc/modules/$pModule/config.json";

        if( !file_exists($config) ){
            return false;
        }

        $ltime = filemtime($config);
        $lang = $pLang?$pLang:kryn::$domain['lang'];
        
        $cacheCode = "moduleConfig_$pModule"."_$lang"."_$ltime";
        
        $configObj = kryn::getFastCache( $cacheCode );
        if( !$configObj ){
            
            //delete all config caches from this module
            $delfiles = glob("inc/cache/moduleConfig_$pModule"."_$lang"."_*.php");
            
            if( count($delfiles) > 0 )
                foreach( $delfiles as $delfile )
                    @unlink( $delfile );

            $json = kryn::translate( kryn::fileRead( $config ) );
            $configObj = json_decode( $json, 1 );
            kryn::setFastCache( $cacheCode, $configObj );
        }
        
        return $configObj;
    }

    /**
     * 
     * Writes content to a template file
     * @param string $pPath
     * @param string $pContent
     * @static
     */
    public static function writeTempFile($pPath, $pContent){
        global $cfg;
        $pPath = str_replace("..", "", $pPath);
        $file = "inc/template/$pPath";
        kryn::fileWrite( $file, $pContent );
    }

     /**
     * 
     * Writes content to a file
     * @param string $pPath
     * @param string $pContent
     * @static
     */ 
    public static function fileWrite( $pPath, $pContent ){
        
        $h = fopen( $pPath, "w+");
        if( $h  ){
            fwrite($h, $pContent);
            fclose($h);
        }
    }

    
    /**
     * 
     * Removes the content of the specified cache-key
     * @param string $pCode
     * @static
     * @deprecated Use removeCache instead.
     */
    public static function removePhpCache( $pCode ){
        return self::removeCache( $pCode );
        $pCode = str_replace('..', '', $pCode);
        @unlink('inc/cache/'.$pCode.'.php');
    }

    /**
     * 
     * Sets a content to the specified cache-key
     * @param string $pCode
     * @param string $pValue
     * @static
     * @deprecated Use setCache instead.
     */
    public static function setPhpCache( $pCode, $pValue ){
        return self::setCache( $pCode, $pValue );
        $pCode = str_replace('..', '', $pCode);
        $varname = '$kcache[\'krynPhpCache_'.$pCode.'\'] ';
        $phpCode = "<"."?php \n$varname = ".var_export($pValue,true).";\n ?".">";
        kryn::fileWrite('inc/cache/'.$pCode.'.php', $phpCode);
    }

    
    /**
     * 
     * Gets the content of the specified cache-key
     * @param string $pCode
     * @return string
     * @static
     * @deprecated Use getCache instead.
     */
    public static function &getPhpCache( $pCode ){
        return self::getCache( $pCode );
        global $kcache;
        $pCode = str_replace('..', '', $pCode);
        $varname = 'krynPhpCache_'.$pCode;
        if( file_exists( 'inc/cache/'.$pCode.'.php' )){
            include_once( 'inc/cache/'.$pCode.'.php' );
            return $kcache[$varname];
        }
        return false;
    }

    /**
     * 
     * Removes a value for the specified cache-key
     * @param string $pCode
     */
    public static function deleteCache( $pCode ){
        if( kryn::$cache )
            kryn::$cache->delete( $pCode );
    }
    
    /**
     * 
     * Sets a content to the specified cache-key.
     * 
     * @param string $pCode
     * @param string $pValue
     * @param integer $pTimeout Unix Timestamp. Default is one hour + time()
     * @deprecated Use kryn::setCache() instead.
     * @static
     */
    public static function setCache( $pCode, $pValue, $pTimeout ){
        if( kryn::$cache )
            return kryn::$cache->set( $pCode, $pValue, $pTimeout );
        return false;
    }
    
    /**
     * Marks a code as invalidate until $pTime
     *
     * @param string $pCode
     * @param integer $pTime Timestamp. Default is time()
    */
    
    public static function invalidateCache( $pCode, $pTime = false ){
        if( kryn::$cache )
            return kryn::$cache->invalidate( $pCode, $pTime );
        return false;
    }
    
    /**
     * 
     * Returns the content of the specified cache-key
     *
     * @param string $pCode
     * @deprecated Use kryn::getCache() instead.
     * @return string
     * @static
     */
    public static function &getCache( $pCode ){
        if( kryn::$cache )
            return kryn::$cache->get( $pCode );
        return false;
    }
    
    /**
     * 
     * Sets a content to the specified cache-key.
     *
     * This function saves the value in a generated php file.
     * The idea behind this: If the server has active apc or
     * other optcode caching, then this method is way
     * faster then tcp caching-server.
     *
     * Please be sure, that you really want to use that: This
     * is not compatible with load balanced kryn installations
     * and should only be used, if you are really sure, that 
     * the other machine in a load balanced scenario does not
     * need informations about this cache.
     * 
     * A good purpose for this is for example caching converted
     * json files (like the installed extension configs).
     *
     * @param string $pCode
     * @param string $pValue
     * @deprecated Use kryn::setCache() instead.
     * @static
     */
    public static function setFastCache( $pCode, $pValue ){
        kryn::$cacheFast->set( $pCode, $pValue );
    }
    
    /**
     * 
     * Returns the content of the specified cache-key.
     * See kryn::setFastCache for more informations.
     *
     * @param string $pCode
     * @deprecated Use kryn::getCache() instead.
     * @return string
     * @static
     */
    public static function &getFastCache( $pCode ){
        return kryn::$cacheFast->get( $pCode );
    }

    /**
     * 
     * Internal function for returning cache system-informations.
     * @param string $pCode
     * @static
     * @internal
     */
    public static function &readCache( $pCode ){
        $rsn = kryn::$domain['rsn'];
        $pCode = str_replace('..', '', $pCode);
        return kryn::getCache( $pCode.'_'.$rsn );
    }

    /* 
     * Resize a image to a fix resolution or to max dimension.
     *
     * @param pResolution Defined the resolution of the target image. e.g 1024x700, 1500x100, 500x500 
     * @param $pFix If you want to resize the image to fix resolution (thumpnails) 
     * @static
    */
    public static function resizeImage( $pPath, $pTarget, $pResolution, $pFix = false ){
    
        list( $oriWidth, $oriHeight, $type ) = getimagesize( $pPath );
        switch( $type ){
            case 1:
                $imagecreate = 'imagecreatefromgif';
                $imagesave = 'imagegif';
                break;
            case 2:
                $imagecreate = 'imagecreatefromjpeg';
                $imagesave = 'imagejpeg';
                break;
            case 3:
                $imagecreate = 'imagecreatefrompng';
                $imagesave = 'imagepng';
                break;
        }
        
        if(! $imagecreate )
            return;
           
        $img = $imagecreate( $pPath );
        
        //$cacheThumpFile = self::$cacheDir.'thump.'.$pFile;
        //$cacheFile = self::$cacheDir . filemtime( $file ) . '.' . $pFile;
        
        //list( $thumpWidth, $thumpHeight ) = explode( 'x', $pConf['thumpSize'] );
        
        list( $newWidth, $newHeight ) = explode( 'x', $pResolution );
        $thumpWidth = $newWidth;
        $thumpHeight = $newHeight;
        
       
        //
        // render Thump
        //
        if( $pFix ){
               $thumpImage = imagecreatetruecolor( $thumpWidth, $thumpHeight );
            imagealphablending( $thumpImage, false );
    
            if( $oriWidth > $oriHeight ){
        
                //resize mit hoehe = $tempheight, width = auto;
                
                $ratio = $thumpHeight / ( $oriHeight / 100 );
                $_width = ceil($oriWidth * $ratio / 100);
        
                $top = 0;
                if( $_width < $thumpWidth) { 
                    $ratio = $_width / ($thumpWidth/100);
                    $nHeight = $thumpHeight * $ratio / 100;
                    $top =  ($thumpHeight - $nHeight)/2;
                    $_width = $thumpWidth;
                }
        
                $tempImg = imagecreatetruecolor( $_width, $thumpHeight );
                imagealphablending( $tempImg, false );
                imagecopyresampled( $tempImg, $img, 0, 0, 0, 0, $_width, $thumpHeight, $oriWidth, $oriHeight);
                $_left = ($_width/2) - ($thumpWidth/2);
        
                imagecopyresampled( $thumpImage, $tempImg, 0, 0, $_left, 0, $thumpWidth, $thumpHeight, $thumpWidth, $thumpHeight );
        
            } else {
                $ratio = $thumpWidth / ( $oriWidth / 100 );
                $_height = ceil($oriHeight * $ratio / 100);
                $tempImg = imagecreatetruecolor( $thumpWidth, $_height );
                imagealphablending( $tempImg, false );
                imagecopyresampled( $tempImg, $img, 0, 0, 0, 0, $thumpWidth, $_height, $oriWidth, $oriHeight );
                $_top = ($_height/2) - ($thumpHeight/2);
                imagecopyresampled( $thumpImage, $tempImg, 0, 0, 0, $_top, $thumpWidth, $thumpHeight, $thumpWidth, $thumpHeight );
            }
            
            if( $type == 3 ){
                
                imagealphablending( $thumpImage, false );
                imagesavealpha( $thumpImage, true );
            }
            
            $imagesave( $thumpImage, $pTarget );
        
       } else {
            
            //render image(big)
            if( $oriHeight > $oriWidth ){
                $ratio = $newHeight / ( $oriHeight / 100 );
                $_width = ceil($oriWidth * $ratio / 100);
                $newImage = imagecreatetruecolor( $_width, $newHeight );
                imagealphablending( $newImage, false );
                
                imagecopyresampled( $newImage, $img, 0, 0, 0, 0, $_width, $newHeight, $oriWidth, $oriHeight);
            } else {
                $ratio = $newWidth / ( $oriWidth / 100 );
                $_height = ceil($oriHeight * $ratio / 100);
                $newImage = imagecreatetruecolor( $newWidth, $_height );
                imagealphablending( $newImage, false );
                
                imagecopyresampled( $newImage, $img, 0, 0, 0, 0, $newWidth, $_height, $oriWidth, $oriHeight);
            }
            if( $type == 3 ){
                
                imagealphablending( $newImage, false );
                imagesavealpha( $newImage, true );
            }
            
            $imagesave( $newImage, $pTarget );

        }
        
    } 
    
    
    /**
     * 
     * Reads all files of the specified folders.
     * @param string $pPath
     * @param bool $pWithExt Return file extensions or not
     * @return array
     * @static
     */
    public static function readFolder( $pPath, $pWithExt = false ) {
        $h = @opendir( $pPath );
        if(! $h ){
            return false;
        }
        while( $file = readdir($h) ){
            if( $file != '.' && $file != '..' && $file != '.svn' ){
                if(! $pWithExt ){
                    //delete extension
                    $file = substr( $file, 0, (strpos($file, '.') > 0) ? strrpos( $file, '.' ) : strlen($file) );
                }
                $files[] = $file;
            }
        }
        return $files;
    }
    
    /**
     * 
     * Build the HTML for given page. If pPageRsn is a deposit, it returns with kryn/blankLayout.tpl as layout, otherwise
     * it returns the layouts with all it contents.
     * @param int $pPageRsn
     * @param int $pSlotId
     * @param array $pProperties
     * @internal
     */
    public static function renderPageContents( $pPageRsn = false, $pSlotId = false, $pProperties = false ){
        
        if( kryn::$contents ){
            $oldContents = kryn::$contents;
        }
        
        kryn::$forceKrynContent = true;
        
        $start = microtime(true);
        if( $pPageRsn == kryn::$page['rsn'] ){
            //endless loop
            die(_l('You produced a endless loop. Please check your latest changed pages.'));
        }
        
        if( !$pPageRsn ){
        
            $pPageRsn = kryn::$page['rsn'];
        } else if( $pPageRsn != kryn::$page['rsn'] ){
        
            $oldPage = kryn::$page;
            kryn::$page = kryn::getPage( $pPageRsn, true );
            $newStage = true;
        }
        
        kryn::addCss('css/_pages/'.$pPageRsn.'.css');
        kryn::addJs('js/_pages/'.$pPageRsn.'.js');
        
        kryn::$contents = kryn::getPageContents( $pPageRsn );
        
        if( kryn::$page['type'] == 3 ){ //deposit
            kryn::$page['layout'] = 'kryn/blankLayout.tpl';
        }
        
        if( $pSlotId ){
            $html = kryn::renderContents(kryn::$contents[$pSlotId], $pProperties);
        } else {
            $html = tFetch( kryn::$page['layout'] );
        }
        
        if( $oldContents ){
            kryn::$contents = $oldContents;
        }
        if( $oldPage ){
            kryn::$page = $oldPage;
        }
        kryn::$forceKrynContent = false;
        
        return $html;
    }
    
    /**
     * 
     * Build HTML for given contents.
     * @param array $pContents
     * @param array $pSlotProperties
     * @internal
     */
    public static function renderContents( &$pContents, $pSlotProperties ){
        global $tpl, $client, $adminClient;
        
        $access = true;
        $contents = array();

        foreach( $pContents as $key => &$content ){
            
            
            $access = true;

            if( 
                ($content['access_from']+0 > 0 && $content['access_from'] > time() ) ||
                ($content['access_to']+0 > 0 && $content['access_to'] < time() )
            ){
                $access = false;
            }
            
            if( $content['hide'] === 0 ){
                $access = false;
            }
            
            if( $access && $content['access_from_groups'] ){
    
                $access = false;
                $groups = ','.$content['access_from_groups'].',';
            
                foreach( $client->user['groups'] as $group ){
                    if( strpos( $groups, ','.$group.',' ) !== false ){
                        $access = true;
                        break;
                    }
                }
                
                if( !$access ){
                    foreach( $adminClient->user['groups'] as $group ){
                        if( strpos( $groups, ','.$group.',' ) !== false ){
                            $access = true;
                            break;
                        }
                    }
                    if( $access ){
                        //have acces through the admin login
                    }
                }
            }
                
            if( $access ){
                $contents[$key] = $content;
            }
        }

        $count = count($contents);
        tAssign('layoutContentsMax', $count);
        tAssign('layoutContentsIsFirst', true);
        tAssign('layoutContentsIsLast', false);
        tAssign('layoutContentsId', $params['id']);
        tAssign('layoutContentsName', $params['name']);
        
        
        $slot = $pSlotProperties;
        $slot['maxItems'] = $count;
        $slot['isFirst'] = true;
        $slot['isLast'] = false;
        
        $i = 0;
        
        $oldContent = $tpl->getTemplateVars('content');

        if( $count > 0 ){
            foreach( $contents as &$content ){
                if( $i == $count ) {
                    tAssign('layoutContentsIsLast', true);
                    $slot['isLast'] = true;
                }
                if( $i > 0 ){
                    tAssign('layoutContentsIsFirst', false);
                    $slot['isFirst'] = false;
                }
                
                $i++;
                tAssign('layoutContentsIndex', $i);
                $slot['index'] = $i;
                 
                tAssignRef('slot', $slot);
                kryn::$slot = $slot;
                
                $html .= kryn::renderContent( $content, $slot );

            }
        }
        
        if( $pSlotProperties['assign'] != "" ){
            tAssignRef($pSlotProperties['assign'], $html);
            return;
        }
        
        return $html;
        
        
    }
    
    /**
     * 
     * Build HTML for given content.
     * @param array $pContent
     * @param array $pProperties
     * @internal
     */
    public static function renderContent( $pContent, $pProperties ){
        global $modules, $tpl, $client, $adminClient;
        
        $content =& $pContent;
        
        $_content = &$content['content'];
        
        tAssignRef( 'content', $content );
        tAssign( 'css', ($content['css']) ? $content['css'] : false );
        
        switch( strtolower($content['type']) ){
            case 'text':
                //replace all [[ with a workaround, so that multilanguage will not fetch.
                $_content = str_replace('[[', '[<!-- -->[', $_content);
                
                break;
            case 'html':
                $_content = str_replace('[[', '\[[', $_content);
                
                break;
            case 'navigation':
                $temp = json_decode( $content['content'], 1 );
                $temp['id'] = $temp['entryPoint'];
                $_content = krynNavigation::plugin( $temp );
                
                break;
            case 'picture':

                $temp = explode( '::', $_content );
                
                if( $temp[0] != '' && $temp[0] != 'none' ){
                    $opts = json_decode( $temp[0], true );
                    $align = ( $opts['align'] ) ? $opts['align'] : 'left';
                    $alt = ( $opts['alt'] ) ? $opts['alt'] : '';
                    $title = ( $opts['title'] ) ? $opts['title'] : '';
                    
                    $imagelink = $temp[1];
                    
                    if( $opts['width'] && $opts['height']){
                        $imagelink = resizeImageCached( $imagelink, $opts['width'].'x'.$opts['height'] );
                    } elseif ( $pProperties['picturedimension'] && $opts['forcedimension'] != "1" ){
                        $imagelink = resizeImageCached( $imagelink, $pProperties['picturedimension'] );
                    }
                    
                    $link = '';
                    if( $opts['link']+0 > 0 ){
                        $link = kryn::pageUrl( $opts['link'] );
                    } else if( $opts['link'] != '' ){
                        $link = $opts['link'];
                    }

                    if( $link == '' ){
                        $_content = '<div style="text-align: '.$align.';"><img src="' . $imagelink . '" alt="'.$alt.'" title="'.$title.'" /></div>';
                    } else {
                        $_content = '<div style="text-align: '.$align.';"><a href="'.$link.'" ><img src="' . $imagelink . '" alt="'.$alt.'" title="'.$title.'" /></a></div>';
                    }

                } else {
                    $_content = '<img src="' . $temp[1] . '" />';
                }
                
                break;
            case 'template':
                
                if( substr($_content, 0,1) == '/' )
                    $_content = substr($_content, 1);

                $file = str_replace('..', '', $_content);
                if( file_exists( "inc/template/".$file ) ){
                    $_content = tFetch( $file );
                }
                break;
            case 'pointer':
                
                if( $_content+0 > 0 && $_content+0 != kryn::$page['rsn'] )
                    $_content = kryn::renderPageContents( $_content+0, 1, $pProperties );
                
                break;
            case 'layoutelement':
                
                $oldContents = kryn::$contents;
                
                $layoutcontent = json_decode($_content, true);
                kryn::$contents = $layoutcontent['contents'];
                $_content = tFetch( $layoutcontent['layout'] );
                
                kryn::$contents = $oldContents;
                
                break;
            case 'plugin':
                
                $t = explode( '::', $_content );
                $config = $_content;
                
                $_content = 'Plugin not found.';                    
                
                if( $modules[ $t[0] ] ){
                
                    $config = substr(  $config, strlen($t[0])+2+strlen($t[1])+2 );
                    $config = json_decode( $config, true );
                    
                    if( method_exists( $modules[ $t[0] ], $t[1]) )
                        $_content = $modules[ $t[0] ]->$t[1]( $config );
                        
                    // if in seachindex mode and plugin is configured unsearchable the kill plugin output
                    if(isset(kryn::$configs[$t[0]]['plugins'][$t[1]][3]) && kryn::$configs[$t[0]]['plugins'][$t[1]][3] == true)
                        $_content = kryn::$unsearchableBegin.$_content.kryn::$unsearchableEnd;                          
                    
                }
                
                break;
            case 'php':
                $temp = ob_get_contents();
                ob_end_clean();
                ob_start();
                eval( $_content );
                $_content = ob_get_contents();
                ob_end_clean();
                ob_start();
                print $temp;
                break;
        }
    
        
        $unsearchable = false;
        if( (!is_array($content['access_from_groups']) && $content['access_from_groups'] != '') ||
            (is_array($content['access_from_groups']) && count($content['access_from_groups']) > 0) ||
            ($content['access_from']+0 > 0 && $content['access_from'] > time() ) ||
            ($content['access_to']+0 > 0 && $content['access_to'] < time() ) ||
            $content['unsearchable'] == 1
            ){
            $unsearchable = true;
        }

        if( $content['template'] == '' || $content['template'] == '-' ){
            if( $unsearchable )
                return '<!--unsearchable-begin-->'.$_content.'<!--unsearchable-end-->';
            else
                return $_content;
        } else {
            tAssignRef( 'content', $content );
            $template = $content['template'];
            if( $unsearchable )
                return '<!--unsearchable-begin-->'.tFetch( $template ).'<!--unsearchable-end-->';
            else
                return tFetch( $template );
        }
    }

}

?>

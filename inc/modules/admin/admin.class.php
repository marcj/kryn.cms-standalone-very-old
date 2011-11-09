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

class admin {
    
    function __construct(){
        //require_once("inc/modules/admin/update.php");
    }
    
    public function activateLL(){
        global $modules;
        if(! $this ){
            $modules->modules['admin']->activateLL();
            return ;
        }
        tAssign( 'activateLL', true );

        if( getArgv('setActivateLLlang') != '' ){
            setcookie('activateLLlang', getArgv('setActivateLLlang'), time()+(60*60*24*7*265), '/');
            $_COOKIE['activateLLlang'] = getArgv('setActivateLLlang');
        }

        //hier cookie check
        if( $_COOKIE['activateLLlang'] != '' )
            $this->lang = $_COOKIE['activateLLlang'];

        if( $this->lang == '' )
            $this->lang = 'de';

        $_REQUEST['lang'] = $this->lang;
        tAssign( 'activateLLlang', $this->lang );
    }

    public function content(){
        global $tpl, $navigation, $modules, $cfg, $client;


        if( getArgv('getLanguage') != '' )
            self::printLanguage();

        if( getArgv('getPossibleLangs') == '1' )
            self::printPossibleLangs();

        header( 'Expires:' );

        require( 'inc/modules/admin/adminModule.class.php' );
        require( 'inc/modules/admin/adminDb.class.php' );
        require( 'inc/modules/admin/adminLayout.class.php' );
        require( 'inc/modules/admin/adminPages.class.php' );
        require( 'inc/modules/admin/adminSettings.class.php' );
        require( 'inc/modules/admin/adminFilemanager.class.php' );
        require( 'inc/modules/admin/adminSearchIndexer.class.php' );  
        require( 'inc/modules/admin/adminStore.class.php' );   
        require( 'inc/modules/admin/adminBackup.class.php' );  

        tAssign("admin", true);
                    
        kryn::initModules();
                    
        $code = kryn::getRequestPath();
        $info = self::getPathItem( $code );

        if( !$info ){
            $info = self::getPathItem( substr($code,6) );
        }
                
        if( $info ){
            if( $info['type'] == 'store' ){
                if( !$info['class'] ){
                    $obj = new adminStore();
                } else {
                    require_once('inc/modules/'.$info['_module'].'/'.$info['class'].'.class.php');
                    $class = $info['class'];
                    $obj = new $class();
                }
                json($obj->handle($info));
            } else {
                $adminWindows = array('edit', 'list', 'add', 'combine');
                require( 'inc/modules/admin/adminWindow.class.php' );
                $obj = new adminWindow();

                if( getArgv('cmd') == 'getInfo' ){
                    json( $info );
                } else if( in_array( $info['type'], $adminWindows ) ){
                    json($obj->handle($info));
                }
            }
        }

        
        if( $modules[ getArgv(2) ] && getArgv(2) != 'admin' ){

            $content = $modules[ getArgv(2) ]->admin();

            tAssign( "content", $content );
            die( tFetch('admin/iframe.tpl') );

        } else {
            $content = null;
            switch( getArgv(2) ){
                case 'mini-search':
                    return self::miniSearch( getArgv('q', 1) );
                case 'loadCss':
                    return self::loadCss();
                case 'widgets':
                    require("inc/modules/admin/adminWidgets.class.php");
                    return adminWidgets::init();
                case 'pages':
                    json( adminPages::init() );
                    break;
                case 'help':
                    switch( getArgv(3) ){
                        case 'load':
                            return self::loadHelp();
                            
                        case 'loadTree':
                            return json( self::loadHelpTree(getArgv('lang')) );
                        default:
                            json('empty-param-3');
                    }
                    break;
                case 'backend':
                    switch( getArgv(3) ){
                        case 'nothing':
                            die("");
                        case 'clearCache':
                            json( admin::clearCache() );
                        case 'loadJs':
                            return self::loadJs();
                        case 'loadCustomJs':
                            return self::loadCustomJs();
                        case 'loadLayoutElementFile':
                            return self::loadLayoutElementFile( getArgv('template') );
                        case 'getContentTemplate':
                            return self::loadContentLayout();
                        case 'fixDb':
                            return self::fixDb();
                        case 'saveDesktop':
                            self::saveDesktop(getArgv('icons'));
                        case 'getDesktop':
                            self::getDesktop();
                        case 'saveWidgets':
                            self::saveWidgets(getArgv('widgets'));
                        case 'getWidgets':
                            self::getWidgets();
                        case 'getMenus':
                            return admin::getMenus();
                        case 'getSettings': json(self::getSettings()); break;
                        case 'saveUserSettings': $content = self::saveUserSettings(); break;
                        case 'getDefaultImages': self::getDefaultImages(); break;

                        case 'imageThump':
                            return adminFilemanager::imageThump( getArgv('file') );

                        case 'stream': $content = self::stream(); break;
                        case 'navigationPreview':
                            return admin::navigationPreview( getArgv('content') );
                        case 'pointerPreview':
                            return admin::pointerPreview( getArgv('content') );
                        case 'plugins':
                            require("inc/modules/admin/adminPlugins.class.php");
                            return adminPlugins::init();
                        case 'searchIndexer' :                         
                            adminSearchIndexer::init();
                            break;
                    }
                    break;
                case 'files':
                    $content = adminFilemanager::init();
                    break;
                case 'filebrowser':
                    require( 'inc/modules/admin/filebrowser.class.php' );
                    $content = filebrowser::init();
                    break;
                case 'system':
                    switch( getArgv(3) ){
                        case 'tools':
                            switch( getArgv(4) ){
                                case 'database':
                                    return self::database();
                                case 'logs':
                                    return json(self::getLogs());
                            }
                            break;
                        case 'module': $content = adminModule::init(); break;
                        case 'settings': $content = adminSettings::init(); break;
                        case 'backup': $content = adminBackup::init(); break;
                        case 'languages':
                            require("inc/modules/admin/adminLanguages.class.php");
                            $content = adminLanguages::init();
                            break;
                        case 'layout':
                            adminLayout::init();
                            break;
                        default: $content = self::systemInfo(); break;
                    }
                    break;
            }
            if( $content !== null )
                json( $content );
        }

        if( !getArgv(2) )
            admin::showLogin();

        json(array('error'=>'param_failed'));
    }
    
    /**
    * Gets the item from the 'admin' entry points defined in the config.json, by the given code
    * @param string $pCode Example publication/news/list returns the hash of 'list'
    */
    
    public static function getPathItem( $pCode ){
        
        $codes = explode( '/', $pCode );
        
        if( kryn::$configs[ 'admin' ]['admin'][ $codes[1] ] ){
            //inside admin extension
            $adminInfo = kryn::$configs[ 'admin' ]['admin'];
            $start = 1;
            $module = 'admin';
            $code = substr( $pCode, 6 );
        } else if( kryn::$configs[ $codes[1] ]['admin'] ){
            //inside other extension
            $adminInfo = kryn::$configs[ $codes[1] ]['admin'];
            $start = 2;
            $module = $codes[1];
            $code = substr( $pCode, 6+strlen($codes[1])+1 );
        }

        $_info = $adminInfo[$codes[$start]];
        $path = array();
        $path[] = $_info['title'];

        $count = count($codes);
        for($i=$start+1;$i<=$count;$i++){
            if( $codes[$i] != "" ){
                $_info = $_info['childs'][$codes[$i]];
                $path[] = $_info['title'];
            }
        }

        unset( $path[ count($path)-1 ] );
        unset( $_info['childs'] );
        
        if( !$_info ){
            return false;
        }
        
        $_info['_path'] = $path;
        $_info['_module'] = $module;
        $_info['_code'] = $code;
        
        $cssPath = str_replace( '/', '_', $code ); //this.code.replace(/\//g,'_');;
        if( $pModule == 'admin' ){
            $cssPath = 'inc/template/admin/css/'.$cssPath.'.css';
        } else {
            $cssPath = 'inc/template/'.$module.'/admin/css/'.$cssPath.'.css';
        }
        if( file_exists( $cssPath ) )
            $_info['cssmdate'] = filemtime( $cssPath );
            
        
        return $_info;
    }
    
    public static function loadContentLayout(){

        $content = array();

        $vars = array('title', 'type', 'template');
        
        foreach( $vars as $p ){
            $content[$p] = $_GET[$p];
        }
        
        tAssign( 'content', $content );
        
        $content['template'] = str_replace('..', '', $content['template']);
        $tpl = kryn::fileRead( 'inc/template/'.$content['template'] );
        
        $tpl = str_replace('{$content.title}', '<span class="ka-layoutelement-content-title">{$content.title}</span>', $tpl);
        $tpl = str_replace('{$content.content}', '<div class="ka-layoutelement-content-content"></div>', $tpl);

        json( tFetch('string:'.$tpl) );
    }

    public static function loadCustomJs(){
    
        $module = getArgv('module');
        $code = getArgv('code');
        
        $module = preg_replace('/[^a-zA-Z-\\/_]/', '', $module);
        $code = preg_replace('/[^a-zA-Z-\\/_]/', '', $code);
        
        if( $module == 'admin' )
           $file = "inc/template/admin/js/$code.js";
        else
            $file = "inc/template/$module/admin/js/$code.js";

        header('Content-Type: text/javascript');
        if(! file_exists($file) ){
            print "contentCantLoaded_".getArgv('onLoad')."('$file');\n";
        } else {
            readFile( $file );
            print "\n";
            print "contentLoaded_".getArgv('onLoad').'();'."\n";
        }
        die();
    }
    
    public static function loadLayoutElementFile( $pFile ){
        
        $pFile = str_replace('..', '', $pFile);
        
        $found = false;
        foreach( kryn::$configs as $config ){
            if( $config['themes'] ){
                foreach( $config['themes'] as $themeTitle => $layouts ){
                    if( $layouts['layoutElement'] ){
                        foreach( $layouts['layoutElement'] as $layoutTiel => $layoutFile ){
                            if( $pFile == $layoutFile )
                                $found = true;
                        }
                    }
                }
            }
        }
        
        $res = false;
        if( $found ){
            $res['layout'] = tFetch($pFile);
        }
        json($res);
    }
    
    public static function logs(){
        
        
    }
    
    public static function database(){
        global $kdb;
        
        $res = array('fetchtime' => 0);
        
        $sql = getArgv('sql');
        
        $startExec = microtime(true);
        $execRes = dbExec( $sql );
        $res['exectime'] = microtime(true)-$startExec;
        
        if( !$execRes ){
            $res['error'] = database::lastError();
        } else {
            $startFetch = microtime(true);
            $res['items'] = dbFetch( $execRes, -1);
            $res['fetchtime'] = microtime(true)-$startFetch;
        }
        
        json($res);
    }
    
    public static function miniSearch( $pQ ){
        global $modules;
        
        $res = array();
        foreach( $modules as &$mod ){
            if( method_exists( $mod, 'searchAdmin') ){
                $res = array_merge( $res, $mod->searchAdmin( $pQ ));
            }
        }
        
        json( $res );
    
    }
    
    public static function getLogs(){
        
        if( getArgv(5) == 'clear' ){
            dbDelete('system_log');
            json(1);
        }
        
        $page = 1;
        if( getArgv('page')+0 > 1 ){
            $page = getArgv('page')+0;
        }
        
                
        $perPage = 40;        
        $where = "WHERE ";
        
        switch( getArgv('area') ){
        
            case '404':
                $where .= "code = '404'"; break;
                
            case 'database':
                $where .= "code = 'database'"; break;
                
            case 'authentication':
                $where .= "code = 'authentication'"; break;
                
            case 'system':
                $where .= "code = 2 OR code = 2048"; break;
                
            case 'all':
            default:
                $where = "";
        
        }


        $from = ($perPage*$page ) - $perPage;
        $count = $perPage;
        
        $title = _l($widget['title']);
        
        $return = array('items', 'count', 'title' => $title );
        
        $sql = "SELECT date, ip, username, code, message FROM %pfx%system_log $where";
        
        if( $sql == "" ) return $return;
        
        $limit = ' ORDER BY date DESC LIMIT '.$count.' OFFSET '.$from;
        $res = dbExec( $sql.$limit, -1 );
        
        $count = dbExfetch( preg_replace('/SELECT(.*)FROM/mi', 'SELECT count(rsn) as ctn FROM', str_replace("\n", " ", $sql ) ));
        $return['count'] = $count['ctn'];
        
        $maxPages = 1;
        if( $return['count'] > 0 ){
            $maxPages = ceil($return['count'] / $perPage);
        }
        $return['maxPages'] = $maxPages;
        $return['items'] = dbExfetch( $sql.$limit, -1);

        foreach( $return['items'] as &$item ){
            $item[0] = date('d M H:i:s', $item['date']);
            $item[1] = $item['ip'];
            $item[2] = $item['username'];
            $item[3] = $item['code'];
            $item[4] = $item['message'];
        }
        
        return $return;
    }
    
    public static function clearCache(){
        global $cfg;
        
        clearfolder( $cfg['files_path'] );
        clearfolder( $cfg['template_cache'] );
    
        return true;
    }
    
    public function searchAdmin( $pQuery ){
        
        $res = array();
        
        $lang = getArgv('lang');
        
        //pages
        $pages = dbExfetch("SELECT p.rsn, p.title, d.lang
            FROM %pfx%system_pages p, %pfx%system_domains d
            WHERE d.rsn = p.domain_rsn AND p.title LIKE '%$pQuery%' LIMIT 10 OFFSET 0", -1);
            
        if( count($pages) > 0 ){
            foreach( $pages as $page )
                $respages[] = array($page['title'], 'admin/pages/', array('rsn' => $page['rsn'], 'lang' => $page['lang']));
            $res[ _l('Pages') ] = $respages;
        }
        
        //help
        $helps = array();
        foreach( kryn::$configs as $key => $mod ){
            $helpFile = "inc/modules/$key/lang/help_$lang.json";
            if( !file_exists( $helpFile ) ) continue;
            if( count($helps) > 10 ) continue;

            $json = json_decode( kryn::fileRead( $helpFile ), 1);
            if( is_array($json) && count($json) > 0 ){
                foreach( $json as $help ){
                
                    if( count($helps) > 10 ) continue;
                    $found = false;
                    
                    if( preg_match( "/$pQuery/i", $help['title'] ) )
                        $found = true;
                        
                    if( preg_match( "/$pQuery/i", $help['tags'] ) )
                        $found = true;
                        
                    if( preg_match( "/$pQuery/i", $help['help'] ) )
                        $found = true;
                        
                    if( $found )
                        $helps[] = array($help['title'], 'admin/help', array('id' => $key.'/'.$help['id']));
                }
            }
        }
        if( count($helps) > 0 ){
            $res[ _l('Help') ] = $helps;
        }
        
        return $res;
    }

    public static function loadHelp(){
        $id = getArgv('id');
        $temp = explode("/", $id);
        $module = $temp[0];
        $helpId = $temp[1];
        $lang = getArgv('lang');

        $helpFile = "inc/modules/$module/lang/help_$lang.json";
        $json = kryn::fileRead( $helpFile );
        $langs = json_decode( $json, 1);
        $res = false;
        foreach( $langs as &$help ){
            if( $help['id'] == $helpId )
                $res = $help;
        }
        if( !$res && $lang != 'en' ) {
            $_REQUEST['lang'] = 'en';
            self::loadHelp();

        } else {
            if( !$res )
                json(array('title'=>'Not found'));
            else    
                json( $res );
        }
    } 
    
    
    public static function loadHelpTree( $pLang = 'en'){
        
        $res = array();
        foreach( kryn::$configs as $modCode => &$config ){
            
            $langFile = "inc/modules/$modCode/lang/help_$lang.json";
            if( !file_exists($langFile) )
                $langFile = "inc/modules/$modCode/lang/help_en.json";
            if( !file_exists($langFile) )
                continue;
            
            $modTitle = $config['title'][$pLang] ? $config['title'][$pLang] : $config['title']['en'];
        
            $help = kryn::fileRead( $langFile );
            $help = json_decode( $help, true );
            
            if( count($help) > 0 ){
                foreach( $help as &$item ){
                    
                    $item['open'] = $modCode.'/'.$item['id'];
                    $res[ $modTitle ][] = $item;
                    
                }
            }
        
        }
        
        return $res;
    
    }

    public static function fixDb(){

         

    }

    public static function showLogin(){
        global $client;
        
        $language  = $client->user['settings']['adminLanguage']?$client->user['settings']['adminLanguage']:'en';
        
        if( getArgv('setLang') != '' )
            $language = getArgv('setLang',2);

        if( $client->user_rsn > 0 ){
            $access = kryn::checkUrlAccess('admin/backend');
            tAssign( 'hasBackendAccess', $access+0 );
        }
        
        tAssign('adminLanguage', $language);

        print tFetch('admin/index.tpl');
        exit;
    }

    public static function printPossibleLangs(){
        $files = kryn::readFolder( 'inc/modules/admin/lang/', false );
        $where = "code = 'en' ";
        foreach( $files as $file )
            $where .= " OR code = '$file'";
        $langs = dbTableFetch('system_langs', -1, $where);
        
        $json = json_encode( $langs );
        header('Content-Type: text/javascript');
        print "if( typeof(ka)=='undefined') window.ka = {}; ka.possibleLangs = ".$json;
        exit;
    }

    public static function printLanguage(){
        global $client;
        
        $lang = getArgv('getLanguage',2);

        $json = kryn::fileRead('inc/cache/lang_'.$lang.'.json');
        
        if( $json == '' || $json == '[]' ){
            $json = kryn::getAllLanguage($lang);   
            $json = json_encode($json);
            kryn::fileWrite('inc/cache/lang_'.$lang.'.json', $json);
        }
        $client->setLang( $lang );
        
        
        kryn::$lang = kryn::getAllLanguage( $lang );
        
        if( getArgv('js') == 1 ){
            header('Content-Type: text/javascript');
            print "if( typeof(ka)=='undefined') window.ka = {}; ka.lang = ".$json;
            if( !$json ){
                print "\nLocale.define('en-US', 'Date', ".tFetch('admin/mootools-locale.tpl').");";
            }
        } else {
            $json = json_decode( $json, true );
            $json['mootools'] = json_decode(tFetch('admin/mootools-locale.tpl'),true);
            json($json);
        }
            
        //print mootools date translation
        
        exit;
    }

    public static function loginLang(){
        $code = getArgv('loginLang');
        $json = json_encode($lang[$code]);
        print "if( typeof(ka)=='undefined') window.ka = {}; ka.lang = ".$json;
        exit;
    }

    public static function saveDesktop( $pIcons ){
        global $client;
        if( $client->user_rsn > 0 )
            dbUpdate( 'system_user', array('rsn' => $client->user_rsn), array('desktop' => $pIcons));
        json( true );
    }
    
    public static function saveWidgets( $pWidgets ){
        global $client;
        if( $client->user_rsn > 0 )
            dbUpdate( 'system_user', array('rsn' => $client->user_rsn), array('widgets' => $pWidgets));
        json( true );
    }
    
    public static function getWidgets(){
        global $client;
        if( $client->user_rsn > 0 ){
            $row = dbTableFetch( 'system_user', 1, "rsn = ".$client->user_rsn );
            json( $row['widgets'] );
        }
        json( false );
    }

    public static function getDesktop(){
        global $client;
        if( $client->user_rsn > 0 ){
            $row = dbTableFetch( 'system_user', 1, "rsn = ".$client->user_rsn );
            json( $row['desktop'] );
        }
        json( false );
    }

    public static function getDefaultImages(){

        $res = kryn::readFolder( 'inc/template/admin/images/userBgs/defaultImages', true );

        json( $res );
    }

    public static function saveUserSettings(){
        global $client;

        $settings = json_decode(getArgv('settings'),true);
        
        if( $settings['adminLanguage'] == '' )
            $settings['adminLanguage'] = $client->user['settings']['adminLanguage'];

        $settings = serialize( $settings );
        dbUpdate( 'system_user', array('rsn' => $client->user_rsn), array('settings' => $settings) );
        $client->getUser( $client->user_rsn, true ); //reload from cache

        json(1);
    }

    public static function getSettings(){
        global $modules, $client, $cfg;

        $res = array();
        
        $res['modules'] = array();
        foreach( kryn::$configs as $key => $mod ){
            $res['modules'][] = $key;
            if( $mod )
                $res['configs'][$key] = $mod;
        }

        $res['layouts'] = array();
        $res['contents'] = array();
        $res['navigations'] = array();
        
        $res['navigations'] = array();
        foreach( kryn::$configs as $key => $config ){
            if( $config['themes'] ){
                foreach( $config['themes'] as $themeTitle => $theme ){
                    if( $theme['layouts'] ){
                        $res['layouts'][ $themeTitle ] = $theme['layouts'];
                    }
                    if( $theme['navigations'] ){
                        $res['navigations'][ $themeTitle ] = $theme['navigations'];
                    }
                    if( $theme['contents'] ){
                        $res['contents'][ $themeTitle ] = $theme['contents'];
                    }
                    if( $theme['publicProperties'] && count($theme['publicProperties']) > 0 ){
                        $res['publicProperties'][$key][ $themeTitle ] = $theme['publicProperties'];
                    }
                }
            }
        }
        
        $res['groups'] = dbTableFetch( 'system_groups', DB_FETCH_ALL);
        $res['user'] = $client->user['settings'];

        $res['system'] = $cfg;
        $res['system']['db_name'] = '';
        $res['system']['db_user'] = '';
        $res['system']['db_passwd'] = '';

        $inGroups = $client->user['inGroups'];
        

        $code = esc($pUrl);
        if( substr( $code, -1 ) != '/' )
            $code .= '/';
        
        $res['ingroups'] = $inGroups;
        $res['r2d'] =& kryn::getCache("r2d");
        
        if( !$res['r2d'] ){
               $res['r2d'] = array();
        }
        
        $res['domains'] = array();
        $qr = dbExec('SELECT * FROM %pfx%system_domains ORDER BY domain');
        while($row = dbFetch($qr)){
            if( kryn::checkPageAcl( $row['rsn'], 'showDomain', 'd') ){
                $res['domains'][] = $row;
            }
        }

        $userRsn = $client->user_rsn;
            
        $res['acl_pages'] = dbExfetch("
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
            
        $res['pageAcls'] = kryn::$pageAcls;
        
        
        $resAcls = dbExec("
                SELECT code, access, type, target_rsn, target_type FROM %pfx%system_acl
                WHERE
                type > 2 AND
                (
                    ( target_type = 1 AND target_rsn IN ($inGroups))
                    OR
                    ( target_type = 2 AND target_rsn IN ($userRsn))
                )
                ORDER BY code DESC
        ");
        $res['acls'] = array();
        
        if( $resAcls ){
            while( $row = dbFetch($resAcls) ){
                $res['acls'][$row['type']][] = $row;
            }
        }
        
        
        $tlangs = dbTableFetch( 'system_langs', DB_FETCH_ALL, 'visible = 1' );
        $langs = dbToKeyIndex( $tlangs, 'code' );
        $res['langs'] = $langs;

        return $res;
    }

    public static function stream(){
    	global $modules, $client;
    	
        $res['time'] = date('H:i');
        $res['last'] = time();
        
        $sessionCount = dbExfetch('SELECT count(*) as mcount FROM %pfx%system_sessions', 1);
        $res['sessions_count'] = $sessionCount['mcount'];
        
        $res['hasCrawlPermission'] = adminSearchIndexer::hasPermission();
        
        foreach( kryn::$configs as $key => $conf ){
        	$stream = $conf['stream'];
        	
        	if( $stream && method_exists($modules[$key], $stream) ){
        	
        		$res[$key] = $modules[$key]->$stream();
        	}
        }
        
        
        json( $res );
    }

    public static function systemInfo(){
        
        $res['version'] = kryn::$configs['kryn']['version'];
        
        json($res);
    }
    
    public static function loadJs(){
        
        header('Content-Type: application/x-javascript');

        $md5Hash = '';
        foreach( kryn::$configs as &$config ){
            if( $config['adminJavascript'] && is_array( $config['adminJavascript'] )  ) {
                foreach( $config['adminJavascript'] as $jsFile ){
                    if( file_exists('inc/template/'.$jsFile) ){
                        $md5Hash .= '.'.filemtime('inc/template/'.$jsFile).'.';
                    }
                }
            }
        }
        
        $md5Hash = md5($md5Hash);
        
        print "/* Kryn.cms combined admin javascript file: $md5Hash */\n\n";
        if( file_exists('inc/cache/cachedAdminJs_'.$md5Hash.'.js') ){
            readFile( 'inc/cache/cachedAdminJs_'.$md5Hash.'.js' );
        } else {
            $content = '';
            foreach( kryn::$configs as &$config ){
                if( $config['adminJavascript'] && is_array( $config['adminJavascript'] )  ) {
                    
                    foreach( $config['adminJavascript'] as $jsFile ){
                        if( file_exists('inc/template/'.$jsFile) ){
                            $content .= "\n\n/* file: $jsFile */\n\n";
                            $content .= kryn::fileRead( 'inc/template/'.$jsFile );
                        }
                    }
                }
            }
            foreach( glob('inc/cache/cachedAdminJs_*.js') as $cache ){
                @unlink($cache);
            }
            kryn::fileWrite('inc/cache/cachedAdminJs_'.$md5Hash.'.js', $content);
            print $content;
        }
        print "\n".'ka.ai.loaderDone('.getArgv('id').');'."\n";
        exit;
    }

    public static function loadCss(){
        
        header('Content-Type: text/css');
        
        $from = array(
            "-moz-border-radius-topleft",
            "-moz-border-radius-topright",
            "-moz-border-radius-bottomleft",
            "-moz-border-radius-bottomright",
            "-moz-border-radius",
        );
        
        $toSafari = array(
            "-webkit-border-top-left-radius",
            "-webkit-border-top-right-radius",
            "-webkit-border-bottom-left-radius",
            "-webkit-border-bottom-right-radius",
            "-webkit-border-radius",
        );
        $toCss3 = array(
            "border-top-left-radius",
            "border-top-right-radius",
            "border-bottom-left-radius",
            "border-bottom-right-radius",
            "border-radius",
        );
        
        $md5Hash = '';
        foreach( kryn::$configs as &$config ){
            if( $config['adminCss'] && is_array( $config['adminCss'] )  ) {
                foreach( $config['adminCss'] as $cssFile ){
                    if( file_exists('inc/template/'.$cssFile) ){
                        $md5Hash .= '.'.filemtime('inc/template/'.$cssFile).'.';
                    }
                }
            }
        }
        
        $md5Hash = md5($md5Hash);
        
        print "/* Kryn.cms combined admin css file: $md5Hash */\n\n";
        if( file_exists('inc/cache/cachedAdminCss_'.$md5Hash.'.css') ){
            readFile( 'inc/cache/cachedAdminCss_'.$md5Hash.'.css' );
        } else {
            $content = '';
            foreach( kryn::$configs as &$config ){
                if( $config['adminCss'] && is_array( $config['adminCss'] )  ) {
                    
                    foreach( $config['adminCss'] as $cssFile ){
                        if( file_exists('inc/template/'.$cssFile) ){
                            $content .= "\n\n/* file: $cssFile */\n\n";
                            
                            $h = fopen( 'inc/template/'.$cssFile, "r" );
                            if($h){
                                while(!feof($h) && $h){
                                    $buffer = fgets($h, 4096);
                                    $content .= $buffer;
                                    $newLine = str_replace( $from, $toSafari, $buffer );
                                    if( $newLine != $buffer )
                                        $content .= $newLine;
                                    $newLine = str_replace( $from, $toCss3, $buffer );
                                    if( $newLine != $buffer )
                                        $content .= $newLine;
                                }
                                fclose($h);
                            }
                           
                            
                            //$content .= kryn::fileRead( 'inc/template/'.$cssFile );
                        }
                    }
                }
            }
            foreach( glob('inc/cache/cachedAdminCss_*.css') as $cache ){
                @unlink($cache);
            }
            kryn::fileWrite('inc/cache/cachedAdminCss_'.$md5Hash.'.css', $content);
            print $content;
        }
        exit;
    }
    
    public static function getMenus(){
        
        $links = array();
        
        foreach( kryn::$configs as $extCode => $config ){
            
            if( $config['admin'] ){
                foreach( $config['admin'] as $key => $value ){
                    
                    if( $value['childs'] ){    
                        
                        $childs = self::getChildMenus( "$extCode/$key", $value );
                        
                        if( count($childs) == 0 ){
                            if( kryn::checkUrlAccess( "$extCode/$key" ) ){
                                unset($value['childs']);
                                $links[$extCode][$key] = $value;
                            }
                        } else {
                            $value['childs'] = $childs;
                            $links[$extCode][$key] = $value;
                        }
                        
                    } else {
                        if( kryn::checkUrlAccess( "$extCode/$key" ) ){
                            $links[$extCode][$key] = $value;
                        }
                    }
                    
                    if( (!$links[$extCode][$key]['type'] && !$links[$extCode][$key]['childs']) || $links[$extCode][$key]['isLink'] === false ){
                        unset($links[$extCode][$key]);
                    }
                    
                }
            }
        }
        
        json($links);
    }
    
    public static function getChildMenus( $pCode, $pValue ){
    	
    	$links = array();
    	foreach( $pValue['childs'] as $key => $value ){
    	
    		if( $value['childs'] ){	
	    				
				$childs = self::getChildMenus( $pCode."/$key", $value );
				if( count($childs) == 0 ){
	    			if( kryn::checkUrlAccess( $pCode."/$key" ) ){
	    				unset($value['childs']);
	    				$links[$key] = $value;
	    			}
				} else {
					$value['childs'] = $childs;
					$links[$key] = $value;
				}
				
    		} else {
    			if( kryn::checkUrlAccess( $pCode."/$key" ) ){
    				$links[$key] = $value;
    			}
    		}
    	     if( (!$links[$key]['type'] && !$links[$key]['childs']) || $links[$key]['isLink'] === false ){
                unset($links[$extCode][$key]);
            }
    		
    	}
    	return $links;
    }

    public static function pointerPreview( $pContent ){

        $page = dbExfetch( 'SELECT * FROM %pfx%system_pages WHERE rsn = ' . ($pContent+0) );
        kryn::$domain['rsn'] = $page['domain_rsn'];
        kryn::$realUrls =& kryn::readCache( 'urls' );

        $_content = "$pContent: <strong>".$page['title']."</strong> (".kryn::$realUrls['rsn']["rsn=".$pContent].")";
        
        json( $_content );
    }

    public static function navigationPreview( $pContent ){

        $page = adminPages::getPageByRsn( $pContent );

        kryn::$domain['rsn'] = $page['domain_rsn'];
        kryn::$realUrls =& kryn::readCache( 'urls' );

        $_content = "<strong>".$page['title']."</strong> (".kryn::$realUrls['rsn']["rsn=".$pContent].")";
        json($_content);
        /*
        $options[ 'id' ] = $temp[0];
        $options[ 'template' ] = $temp[1];
        $navi = navigation::plugin( $options );
        json( $navi );*/
    }
    public static function getPageDetails( $pRsn ){
        global $cfg;
        $res =  adminPages::getPageByRsn( $pRsn );
        $path = $cfg['path'];
        $content = kryn::readTempFile("pages/".$pRsn.".tpl");
        $res['content'] = preg_replace('/{krynplugin plugin="(.*)?"}/U', "<img src=\"${path}admin/menu=pluginIcon/plugin=$1/\" class='krynPluginIcon' />", $content);
        json( $res );
    }

    public static function get_dir_array($myDir = ".", $template){
        if(substr($myDir,-1) == "/")
            $myDir = substr($myDir,0,-1);

        $dir = opendir($myDir);
        #$result[] = array();

        while($file = readdir($dir)){
            $newDir = $myDir . "/" . $file;
            if (substr($newDir, 0, strlen("inc/template/".$template)) == "inc/template/".$template){
                if($file == "." || $file == ".." || $file == "CVS" )
                    continue;

                $filename = substr($newDir, 13, strlen($newDir));
                $t = explode("/", $newDir);

                if(is_dir($newDir)){
                    #echo $filename."\n";
                    $result[$filename] = $this->get_dir_array($newDir . "/", $template);
                } else {
                    #echo "FILE: ".$newDir."\n";
                    $t = explode("/", $newDir);
                    $result[$newDir] = $t[count($t)-1];
                }
            }
        }
        return $result;
    }

    public static function linkCheckForm(){        
        $result = true;
        if($_REQUEST['linkname'] == "") $result = false;
        if($_REQUEST['linkurl'] == "") $result = false;
        if($_REQUEST['linkuser'] == "") $result = false;
        return $result;
    }

    public function install(){

        dbDelete('system_pages');
        dbDelete('system_contents');
        
        
        $sqls = explode( ";\n", kryn::fileRead('inc/modules/admin/defaultData.sql') );
        foreach( $sqls as &$sql )
            dbExec( $sql );

        dbDelete('system_langs');
        $h = fopen('inc/modules/admin/ISO_639-1_codes.csv', 'r');
        if( $h ){
            while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
                dbInsert('system_langs', array('code' => $data[0], 'title' => $data[1], 'langtitle' => $data[2]));
            }
        }
        
        dbUpdate('system_langs', array('code' => 'en'), array('visible' => 1));
        
    }

    
    public static function addVersion( $pTable, $pPrimary ){
    
        foreach( $pPrimary as $fieldName => $fieldValue ){
            if( $fieldValue+0 > 0 )
                $sql = " AND $fieldName = $fieldValue";
            else
                $sql = " AND $fieldName = '". esc($fieldValue)."'";
        }
        
        $row = dbTableFetch($pTable, "1=1 $sql", 1);
        
        return self::addVersionRow( $pTable, $pPrimary, $row );
    }
    
    public static function addVersionRow( $pTable, $pPrimary, $pRow ){
        global $client;
        
        $code = $pTable;
        foreach( $pPrimary as $fieldName => $fieldValue ){
            $code .= '_'.$fieldName.'='.$fieldValue;
        }
        
        $content = json_encode( $pRow );
                
        $currentVersion = dbTableFetch('system_frameworkversion', "code = '$code' ORDER BY version DESC", 1);
        
        $version = $currentVersion['version']+1;
        $new = array(
            'code' => $code,
            'content' => $content,
            'version' => $version,
            'cdate' => time(),
            'user_rsn' => $client->user_rsn
        );
        
        dbInsert('system_frameworkversion', $new);
        return $version;
    }
    
    
    public static function getVersion( $pTable, $pPrimary, $pVersion ){
    
        $code = $pTable;
        foreach( $pPrimary as $fieldName => $fieldValue ){
            $code .= '_'.$fieldName.'='.$fieldValue;
        }
        $version = $pVersion+0;
        
        $version = dbTableFetch('system_frameworkversion', "code = '$code' AND version = $version", 1);
    
        return json_decode( $version['content'], true );
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /*
     * 
     * WIDGET STUFF
     * 
     */
    
    
    
    
    
    public function widgetLastLogins( $pConf ){
        $res['title'] = "Letzte Sessions";

        $sessions = dbExFetch('SELECT s.*, u.username
                    FROM '.pfx.'system_sessions s, '.pfx.'system_user u
                    WHERE s.user_rsn = u.rsn AND u.rsn > 0
                    ORDER BY time DESC
                    LIMIT 10 OFFSET 0', DB_FETCH_ALL);
        tAssign('sessions', $sessions);
        $res['content'] = tFetch( 'admin/overview.widget.sessions.tpl' );

        return $res;
    }

    public function widgetVersion(){

        $res['title'] = 'Kryn '.kryn::$configs['kryn']['version'];
        $res['content'] = '
            <span style="color: green;">Sie benutzen die aktuellste Version.</span>    
        '; 

        return $res;

    }

    public function widgetWaitingContent( $pConf ){
       
        $pages = dbExFetch('SELECT u.username, p.*, v.modified
            FROM %pfx%system_user u, %pfx%system_pages p, %pfx%system_pagesversions v
            WHERE draft_exist = 1
            AND v.page_rsn = p.rsn
            AND u.rsn = v.owner_rsn
            AND v.active = 1
            AND ( p.type = 0 OR p.type = 3)
            ', -1);
        
        $res['title'] = _l('Unpulished contents').' ('.count($pages).')';

        $html = '<table width="100%">';
        if( count($pages) > 0 ){
            foreach($pages as $page){
                $html .= '<tr><td width="90">'.date("d. M H:i:s", $page['modified']).'</td>';
                $html .= '<td>'.$page['username'].'</td>';
                $html .= '<td>'.$page['title'].'</td>';
                $html .= '<td width="20"><a href="javascript:;" onclick="ka.wm.open(\'admin/pages\', {rsn: '.$page['rsn'].'});"><img src="'.kryn::$domain['path'].'admin/images/icons/bullet_go.png" /></a></td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        $res['content'] = $html;

        return $res;

    }
    
    public function manipulateUnpublishedContentsRow( $pRow ){
        $domain = kryn::getDomain( $pRow[4] );
        $pRow[2] = '<a href="javascript:;" onclick="ka.wm.open(\'admin/pages\', {rsn: '.$pRow[2].'});">'.kryn::getPagePath( $pRow[2]+0 ).'</a>';
        return $pRow;
    }
    
    public function manipulateLastChangesRow( $pRow ){
        //$domain = kryn::getDomain( $pRow[4] );
        $pRow[2] = '<a href="javascript:;" onclick="ka.wm.open(\'admin/pages\', {rsn: '.$pRow[2].'});">'.kryn::getPagePath( $pRow[2]+0 ).'</a>';
        return $pRow;
    }

    public function widgetSessions( $pConf ){

        $res['title'] = _l('Current Sessions');
     
        
        $sessions = dbExFetch('SELECT s.*, u.username
                    FROM '.pfx.'system_sessions s, '.pfx.'system_user u
                    WHERE s.user_rsn = u.rsn 
                    ORDER BY time DESC
                    ', DB_FETCH_ALL);

        $sessionsCount = dbExFetch('SELECT count(rsn) as anzahl 
                    FROM '.pfx.'system_sessions s
                    ', 1);

        $res['title'] .= ' ('.$sessionsCount['anzahl'].')';

        $html = '<table width="100%">';
        foreach($sessions as $session){
            $html .= '<tr><td width="90">'.date("d. M H:i:s", $session['time']).'</td>';
            $html .= '<td>'.$session['username'].'</td><td width="90">'.$session['ip'].'</td>';
            $html .= '<td>'.$session['page'].'</td></tr>';
        }
        $html .= '</table>';
        $res['content'] = $html;
        return $res;
    }
}

?>

<?php

$time = time();
$_start = microtime(true);

define('PATH', realpath(dirname(__FILE__).'/../') . '/');
define('PATH_CORE', 'core/');
define('PATH_MODULE', 'module/');
define('PATH_MEDIA', 'media/');

@set_include_path( '.' . PATH_SEPARATOR . PATH . 'lib/pear/' . PATH_SEPARATOR . get_include_path());

/**
* Define globals
*
* @globals
*/
$cfg = array();
$languages = array();
$kcache = array();
$_AGET = array();
$tpl = false;
@ini_set('error_reporting', E_ERROR | E_WARNING | E_PARSE);

# install
if (!file_exists('config.php')) {
    header("Location: install.php");
    exit;
}

include('config.php');

/*if (!array_key_exists('display_errors', $cfg))
    $cfg['display_errors'] = 0;


if ($cfg['display_errors'] == 0) {
    @ini_set('display_errors', 0);
} else {
    @ini_set('display_errors', 1);
}*/

include(PATH_CORE.'misc.global.php');
include(PATH_CORE.'database.global.php');
include(PATH_CORE.'template.global.php');
include(PATH_CORE.'internal.global.php');
include(PATH_CORE.'framework.global.php');

# Load important classes
include(PATH_CORE.'database.class.php');
include(PATH_CORE.'kryn.class.php');
include(PATH_CORE.'krynEvent.class.php');

include(PATH_CORE.'krynModule.class.php');
include(PATH_CORE.'krynObject/krynObjectAbstract.class.php');
include(PATH_CORE.'krynCache.class.php');
include(PATH_CORE.'krynAcl.class.php');
include(PATH_CORE.'krynObject.class.php');
include(PATH_CORE.'krynObjects.class.php');
include(PATH_CORE.'krynNavigation.class.php');
include(PATH_CORE.'krynHtml.class.php');
include(PATH_CORE.'krynAuth.class.php');
include(PATH_CORE.'krynFile.class.php');
include(PATH_CORE.'krynLanguage.class.php');
include(PATH_CORE.'krynSearch.class.php');

date_default_timezone_set($cfg['timezone']);

if (!empty($cfg['locale']))
    setlocale(LC_ALL, $cfg['locale']);

define('pfx', $cfg['db_prefix']);

//some compatibility fixes
if ($_SERVER['REDIRECT_PORT'] + 0 > 0)
    $_SERVER['SERVER_PORT'] = $_SERVER['REDIRECT_PORT'];
if ($_SERVER['SERVER_PORT'] != 80) {
    $cfg['port'] = $_SERVER['SERVER_PORT'];
}

//get lang has more priority
$_REQUEST['lang'] = ($_GET['lang']) ? $_GET['lang'] : $_POST['lang'];


//read out the url so that we can use getArgv()
kryn::prepareUrl();


kryn::$admin = (getArgv(1) == 'admin');
tAssign('admin', kryn::$admin);

//special file /krynJavascriptGlobalPath.js
if (getArgv(1) == 'krynJavascriptGlobalPath.js') {
    $cfg['path'] = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    header("Content-type: text/javascript");
    die("var path = '" . $cfg['path'] . "'; var _path = '" . $cfg['path'] . "'; var _baseUrl = 'http://" .
        $_SERVER['SERVER_NAME'] . ($cfg['port'] ? ':' . $cfg['port'] : '') . $cfg['path'] . "'");
}

/*
 * Initialize the inc/config.php values. Make some vars compatible to older versions etc.
 */
kryn::initConfig();


/*
 * Load list of active modules
 */
kryn::loadActiveModules();


/*
 * Load current language
 */
kryn::loadLanguage();


/*
 * Load themes, db scheme and object definitions from configs
 */
kryn::loadModuleConfigs();


if (getArgv(1) == 'admin') {
    /*
    * Load the whole config of all modules
    */
    kryn::loadConfigs();
}

?>
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
error_reporting(E_ALL ^ E_NOTICE);


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
include('lib/propel/runtime/lib/Propel.php');
include(PATH_CORE.'kryn.class.php');

kryn::$config = $cfg;

function krynAutoLoad($class) {

    if (file_exists(PATH_CORE . $class . '.class.php')){
        include PATH_CORE . $class . '.class.php';
        return true;
    } else if (file_exists(PATH_CORE . '/entities/' . $class . '.class.php')){
        include PATH_CORE . '/entities/' . $class . '.class.php';
        return true;
    } else if (file_exists('lib/Smarty/' . $class . '.class.php')){
        include 'lib/Smarty/' . $class . '.class.php';
        return true;
    } else if (kryn::$propelClassMap[$class.'.php']){
        include kryn::$propelClassMap[$class.'.php'];
        return true;
    } else {
        foreach (kryn::$extensions as $extension){
            if (file_exists($file = PATH_MODULE . $extension.'/'.$class.'.class.php')){
                include $file;
                return true;
            }
        }
    }
};

spl_autoload_register('krynAutoLoad');
kryn::loadActiveModules();


if (!file_exists($file = 'propel-config.php')){
    propelHelper::init();
}

Propel::init($file);
set_include_path("propel/build/classes" . PATH_SEPARATOR . get_include_path());

$propelConfig = include($file);
kryn::$propelClassMap = $propelConfig['classmap'];

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
 * Load current language
 */
kryn::loadLanguage();


/*
 * Load themes, db scheme and object definitions from configs
 */
kryn::loadModuleConfigs();


kryn::initModules();

if (getArgv(1) == 'admin') {
    /*
    * Load the whole config of all modules
    */
    kryn::loadConfigs();
}

?>
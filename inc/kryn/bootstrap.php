<?php

$time = time();
$_start = microtime(true);

define('PATH', realpath(dirname(__FILE__).'/../../') . '/');
define('PATH_MODULE', PATH . 'inc/module/');
define('PATH_TEMPLATE', PATH . 'inc/template/');

@set_include_path('./inc/lib/pear/' . PATH_SEPARATOR . get_include_path());

/**
* Define globals
*
* @globals
*/
$cfg = array();
$modules = array();
$languages = array();
$kcache = array();
$_AGET = array();
$tpl = false;

# install
if (!file_exists('inc/config.php')) {
    header("Location: install.php");
    exit;
}

include('inc/config.php');

if (!array_key_exists('display_errors', $cfg))
    $cfg['display_errors'] = 0;

@ini_set('error_reporting', E_ALL & ~E_NOTICE);

if ($cfg['display_errors'] == 0) {
    @ini_set('display_errors', 0);
} else {
    @ini_set('display_errors', 1);
}

include('inc/kryn/misc.global.php');
include('inc/kryn/database.global.php');
include('inc/kryn/template.global.php');
include('inc/kryn/internal.global.php');
include('inc/kryn/framework.global.php');


# Load important classes
include('inc/kryn/database.class.php');
include('inc/kryn/krynModule.class.php');

include('inc/kryn/kryn.class.php');
include('inc/kryn/krynObject/krynObjectAbstract.class.php');
include('inc/kryn/krynCache.class.php');
include('inc/kryn/krynAcl.class.php');
include('inc/kryn/krynObject.class.php');
include('inc/kryn/krynNavigation.class.php');
include('inc/kryn/krynHtml.class.php');
include('inc/kryn/krynAuth.class.php');
include('inc/kryn/krynFile.class.php');
include('inc/kryn/krynLanguage.class.php');
include('inc/kryn/krynSearch.class.php');

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

//special file /krynJavascriptGlobalPath.js
if (getArgv(1) == 'krynJavascriptGlobalPath.js') {
    $cfg['path'] = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    header("Content-type: text/javascript");
    die("var path = '" . $cfg['path'] . "'; var _path = '" . $cfg['path'] . "'; var _baseUrl = 'http://" .
        $_SERVER['SERVER_NAME'] . ($cfg['port'] ? ':' . $cfg['port'] : '') . $cfg['path'] . "'");
}

?>
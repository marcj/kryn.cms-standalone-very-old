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
 * Index.php
 *
 * @author MArc Schmidt <marc@kryn.org>

 */

header("Content-Type: text/html; charset=utf-8");

$time = time();
$_start = microtime(true);
define('PATH', dirname(__FILE__) . '/');
define('PATH_MODULE', dirname(__FILE__) . '/inc/module/');
define('PATH_TEMPLATE', dirname(__FILE__) . '/inc/template/');

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

# install
if (!file_exists('inc/config.php')) {
    header("Location: install.php");
    exit;
}
;
include('inc/config.php');

if (!array_key_exists('display_errors', $cfg))
    $cfg['display_errors'] = 0;


@ini_set('error_reporting', E_ALL & ~E_NOTICE);

if ($cfg['display_errors'] == 0) {
    @ini_set('display_errors', 0);
} else {
    @ini_set('display_errors', 1);
}

include('inc/kryn/checkFile.php');

include('inc/kryn/misc.global.php');
include('inc/kryn/database.global.php');
include('inc/kryn/template.global.php');
include('inc/kryn/internal.global.php');
include('inc/kryn/framework.global.php');


# Load important classes
include('inc/lib/smarty/Smarty.class.php');
include('inc/kryn/database.class.php');
include('inc/kryn/krynModule.class.php');

include('inc/kryn/kryn.class.php');
include('inc/kryn/krynCache.class.php');
include('inc/kryn/krynAcl.class.php');
include('inc/kryn/krynNavigation.class.php');
include('inc/kryn/krynHtml.class.php');
include('inc/kryn/krynAuth.class.php');
include('inc/kryn/krynFile.class.php');
include('inc/kryn/krynLanguage.class.php');
include('inc/kryn/krynSearch.class.php');


# Init classes and globals
$tpl = new Smarty();
$tpl->template_dir = 'inc/template/';
$tpl->compile_dir = 'cache/smarty_compile/';

tAssign('time', $time);

date_default_timezone_set($cfg['timezone']);

if (!empty($cfg['locale']))
    setlocale(LC_ALL, $cfg['locale']);

define('pfx', $cfg['db_prefix']);

if (!file_exists($cfg['tpl_cpl']))
    @mkdir($cfg['tpl_cpl']);

if ($_SERVER['REDIRECT_PORT'] + 0 > 0)
    $_SERVER['SERVER_PORT'] = $_SERVER['REDIRECT_PORT'];

if ($_SERVER['SERVER_PORT'] != 80) {
    $cfg['port'] = $_SERVER['SERVER_PORT'];
}

$_REQUEST['lang'] = ($_GET['lang']) ? $_GET['lang'] : $_POST['lang'];

kryn::prepareUrl();
# Javascript
if ($_REQUEST['js'] == 'global.js') {
    $cfg['path'] = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    header("Content-type: text/javascript");
    $http = 'http://';
    if ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on')
        $http = 'https://';
    die("var path = '" . $cfg['path'] . "'; var _path = '" . $cfg['path'] . "'; var _baseUrl = '" .
        $http . $_SERVER['SERVER_NAME'] . ($cfg['port'] ? ':' . $cfg['port'] : '') . $cfg['path'] . "'");
}

/*
 * Initialize the inc/config.php values. Make some vars compatible to older versions etc.
 */
kryn::initConfig();

/*
 * Load list of active modules
 */
kryn::loadActiveModules();

if (getArgv(1) != 'admin') {
    kryn::searchDomain();
}

/*
 * Load themes, db scheme and object definitions from configs
 */
kryn::loadModuleConfigs();

/*
 * Initialize the krynAuth objects.
 * Handle users-logout/users-login
 */
kryn::initAuth();

/*
 * Load current language
 */
kryn::loadLanguage();


tAssignRef("request", $_REQUEST); #compatibility
tAssignRef("user", $user->user); #compatibility


if (getArgv(1) == 'admin') {

    /*
     * Load the whole config of all modules
     */
    kryn::loadConfigs();

    /*
     * initial administration controller
     */
    require('inc/kryn/adminForm.class.php');
    require(PATH_MODULE . 'admin/admin.class.php');
    $modules['admin'] = new admin();

    /*
     * Check url access
     */
    kryn::checkAccess();
} else {

    /*
     * Initialize some search params
     */
    krynSearch::initSearch();

}


register_shutdown_function('kryn_shutdown');

kryn::$admin = false;
tAssign('admin', false);

if (getArgv(1) == 'admin') {
    tAssign('admin', true);
    kryn::$admin = true;

    /*
     * Start administration controller
     */
    $modules['admin']->content();

} else {

    /*
     * Start normal frontend generation
     */
    kryn::display();
}

?>
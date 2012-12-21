<?php

mb_internal_encoding("UTF-8");


$_time = time();
$_start = microtime(true);

error_reporting(E_CORE_ERROR|E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR|E_USER_ERROR|E_PARSE);

//fix PATH_INFO
if (!$_SERVER['PATH_INFO']){
    $pathInfo = explode('&', $_SERVER['QUERY_STRING']);
    $_SERVER['PATH_INFO'] = $pathInfo[0];
    array_shift($_GET);
}


if (!defined('PATH')){
    define('PATH', realpath(dirname(__FILE__).'/../') . '/');
    define('PATH_CORE', 'core/');
    define('PATH_MODULE', 'module/');
    define('PATH_MEDIA', 'media/');
    define('PATH_MEDIA_CACHE', 'media/cache/');
}
$cwd = getcwd();
chdir(PATH);
@set_include_path( '.' . PATH_SEPARATOR . PATH . 'lib/pear/' . PATH_SEPARATOR . get_include_path());

/**
 * Check and loading config.php or redirect to install.php
 */
if (!isset($cfg)){
    $cfg = array();
    if (!file_exists(PATH.'config.php') || !is_array($cfg = include(PATH.'config.php'))){
        header("Location: install.php");
        exit;
    }
}

if (substr($_SERVER['PATH_INFO'], 0, 1) == '/')
    $_SERVER['PATH_INFO'] = substr($_SERVER['PATH_INFO'], 1);

/**
 * Define global functions.
 */
include_once(PATH_CORE.'global/misc.global.php');
include_once(PATH_CORE.'global/database.global.php');
include_once(PATH_CORE.'global/template.global.php');
include_once(PATH_CORE.'global/internal.global.php');
include_once(PATH_CORE.'global/framework.global.php');
include_once(PATH_CORE.'global/exceptions.global.php');

# Load very important classes.
include_once(PATH_CORE.'Kryn.class.php');
include_once(PATH_CORE.'Utils.class.php');
include_once(PATH.'lib/propel/runtime/lib/Propel.php');

Core\Kryn::$config = $cfg;
Core\Kryn::setBaseUrl(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));


/**
 * Load active modules into Kryn::$extensions.
 */
Core\Kryn::loadActiveModules();

include_once(PATH.'core/bootstrap.autoloading.php');


if ($cfg['timezone'])
    date_default_timezone_set($cfg['timezone']);

if ($cfg['locale'])
    setlocale(LC_ALL, $cfg['locale']);

define('pfx', $cfg['database']['prefix']);

chdir($cwd);
?>
<?php
global $_start;
global $_time;

$_time = time();
$_start = microtime(true);
chdir(__DIR__ . '/../../');

error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('Europe/Berlin');

if (!defined('PATH')) {
    define('PATH', realpath(__DIR__ . '/../../') . '/');
    define('PATH_CORE', 'src/Core/');
    define('PATH_MODULE', 'module/');
    define('PATH_WEB', 'web/');
    define('PATH_WEB_CACHE', 'web/cache/');
}

if (!function_exists('mb_internal_encoding')) {
    die('FATAL ERROR: PHP module mbstring is not loaded. Aborted. Run the installer again.');
}

mb_internal_encoding("UTF-8");

if (substr($_SERVER['PATH_INFO'], 0, 1) == '/') {
    $_SERVER['PATH_INFO'] = substr($_SERVER['PATH_INFO'], 1);
}

/**
 * Define global functions.
 */
include_once(PATH . PATH_CORE . 'global/misc.global.php');
include_once(PATH . PATH_CORE . 'global/database.global.php');
include_once(PATH . PATH_CORE . 'global/internal.global.php');
include_once(PATH . PATH_CORE . 'global/framework.global.php');
include_once(PATH . PATH_CORE . 'global/exceptions.global.php');
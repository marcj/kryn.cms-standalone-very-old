<?php

namespace Core;

$_time = time();
$_start = microtime(true);

define('PATH', realpath(dirname(__FILE__).'/../') . '/');
define('PATH_CORE', 'core/');
define('PATH_MODULE', 'module/');
define('PATH_MEDIA', 'media/');
define('PATH_MEDIA_CACHE', 'media/cache/');

@set_include_path( '.' . PATH_SEPARATOR . PATH . 'lib/pear/' . PATH_SEPARATOR . get_include_path());

/**
 * Check and loading config.php or redirect to install.php
 */
if (!file_exists('config.php')) {
    header("Location: install.php");
    exit;
}

$cfg = include('config.php');

error_reporting(E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR|E_USER_ERROR);

@ini_set('display_errors', 0);

if (!$_GET['__url'] && $_SERVER['PATH_INFO'])
    $_GET['__url'] = substr($_SERVER['PATH_INFO'], 1);

/**
 * Define global functions.
 */
include(PATH_CORE.'global/misc.global.php');
include(PATH_CORE.'global/database.global.php');
include(PATH_CORE.'global/template.global.php');
include(PATH_CORE.'global/internal.global.php');
include(PATH_CORE.'global/framework.global.php');
include(PATH_CORE.'global/exceptions.global.php');

# Load very important classes.
include(PATH_CORE . 'Kryn.class.php');
include(PATH_CORE . 'Utils.class.php');
include('lib/propel/runtime/lib/Propel.php');

if ($cfg['displayErrors']){
    set_error_handler("coreUtilsErrorHandler", E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR|E_USER_ERROR|E_PARSE);
    set_exception_handler("coreUtilsExceptionHandler");
    register_shutdown_function('coreUtilsShutdownHandler');
}

Kryn::$config = $cfg;
Kryn::setBaseUrl(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));

include('core/bootstrap.autoloading.php');

/**
 * Propel orm initialisation.
 */

if (!file_exists($file = 'propel-config.php')){
    Core\PropelHelper::init();
}

if (!is_readable($file)){
    die("./propel-config.php exists, but is not readable. Please fix the permissions.\n");
}
\Propel::init($file);

$propelConfig = include($file);

Kryn::$propelClassMap = $propelConfig['classmap'];

if ($cfg['timezone'])
    date_default_timezone_set($cfg['timezone']);

if ($cfg['locale'])
    setlocale(LC_ALL, $cfg['locale']);

define('pfx', $cfg['database']['prefix']);

//read out the url so that we can use getArgv()
Kryn::prepareUrl();

Kryn::$admin = (getArgv(1) == 'admin');
/*
 * Initialize the config.php values. Make some vars compatible to older versions etc.
 */
Kryn::initConfig();

/*
 * Load current language
 */
Kryn::loadLanguage();

/*
 * Load themes, db scheme and object definitions from configs
 */
Kryn::loadModuleConfigs();


Kryn::initModules();

if (Kryn::$admin) {
    /*
    * Load the whole config of all modules
    */

    Kryn::loadConfigs();

}

?>
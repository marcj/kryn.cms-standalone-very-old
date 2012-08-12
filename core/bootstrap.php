<?php

namespace Core;

$_time = time();
$_start = microtime(true);

define('PATH', realpath(dirname(__FILE__).'/../') . '/');
define('PATH_CORE', 'core/');
define('PATH_MODULE', 'module/');
define('PATH_MEDIA', 'media/');
define('PATH_PUBLIC_CACHE', 'media/cache/');

@set_include_path( '.' . PATH_SEPARATOR . PATH . 'lib/pear/' . PATH_SEPARATOR . get_include_path());

/**
 * Check and loading config.php or redirect to install.php
 */
if (!file_exists('config.php')) {
    header("Location: install.php");
    exit;
}

$cfg = include('config.php');

error_reporting(E_ALL ^ E_NOTICE);

if (!array_key_exists('displayErrors', $cfg))
    $cfg['displayErrors'] = 0;

if ($cfg['displayErrors'] == 0) {
    @ini_set('display_errors', 0);
} else {
    @ini_set('display_errors', 1);
}

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
include('lib/propel/runtime/lib/Propel.php');

Kryn::$config = $cfg;

include('core/bootstrap.autoloading.php');

/**
 * Propel orm initialisation.
 */

if (!file_exists($file = 'propel-config.php')){
    \propelHelper::init();
}

if (!is_readable($file)){
    die("./propel-config.php exists, but is not readable. Please fix the permissions.\n");
}
\Propel::init($file);

/*$config = \Propel::getConfiguration(\PropelConfiguration::TYPE_OBJECT);
$config->setParameter('debugpdo.logging.details.method.enabled', true);
$config->setParameter('debugpdo.logging.details.time.enabled', true);
$config->setParameter('debugpdo.logging.details.mem.enabled', true);

class propelLogger implements \BasicLogger{
    public function alert($message)
    {
        $this->log($message, 'alert');
    }

    public function crit($message)
    {
        $this->log($message, 'crit');
    }

    public function err($message)
    {
        $this->log($message, 'err');
    }

    public function warning($message)
    {
        $this->log($message, 'warning');
    }

    public function notice($message)
    {
        $this->log($message, 'notice');
    }
    public function info($message)
    {
        $this->log($message, 'info');
    }

    public function debug($message)
    {
        $this->log($message, 'debug');
    }

    public function log($message, $severity = null){
        error_log('['.$severity.'] '.$message);
    }
}

\Propel::setLogger(new propelLogger());
 */

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

//special file /krynJavascriptGlobalPath.js
if (getArgv(1) == 'krynJavascriptGlobalPath.js') {
    $cfg['path'] = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
    header("Content-type: text/javascript");
    die("var path = '" . $cfg['path'] . "'; var _path = '" . $cfg['path'] . "'; var _baseUrl = 'http://" .
        $_SERVER['SERVER_NAME'] . ($cfg['port'] ? ':' . $cfg['port'] : '') . $cfg['path'] . "'");
}

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
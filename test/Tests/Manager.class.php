<?php

namespace Tests;

class Manager
{
    public static $config;

    public static $configFile = 'default.json';

    /**
     * @param null $pConfigFile Default is config/default.mysql.json
     */
    public static function setupConfig($pConfigFile = null)
    {
        $configFile = $pConfigFile ?: 'test/config/'.(getenv('CONFIG_FILE')?getenv('CONFIG_FILE'):self::$configFile);

        if (!file_exists($configFile)) {
            die("Config file not found: $configFile\n");
        }
        self::$config = json_decode(file_get_contents($configFile), true);

        if (getenv('DOMAIN'))
            self::$config['domain'] = getenv('DOMAIN');

        if (getenv('PORT'))
            self::$config['port'] = getenv('PORT');

        if (getenv('DB_NAME'))
            self::$config['config']['database']['name'] = getenv('DB_NAME');

        if (getenv('DB_USER'))
            self::$config['config']['database']['user'] = getenv('DB_USER');

        if (getenv('DB_PW'))
            self::$config['config']['database']['password'] = getenv('DB_PW');

        if (getenv('DB_SERVER'))
            self::$config['config']['database']['server'] = getenv('DB_SERVER');

        if (getenv('DB_TYPE'))
            self::$config['config']['database']['type'] = getenv('DB_TYPE');

    }

    /**
     * @param null $pConfigFile Default is config/default.mysql.json
     */
    public static function freshInstallation($pConfigFile = null)
    {
        self::setupConfig($pConfigFile);
        $cfg = self::$config['config'];
        $cfg['displayErrors'] = true;

        if (file_exists('config.php'))
            self::uninstall();

        self::install($cfg);

    }

    public static function getJson($pPath = '/', $pMethod = 'GET', $pPostData = null)
    {
        $info = self::get($pPath, $pMethod, $pPostData);
        $data = json_decode($info['content'], true);

        return !json_last_error() ? $data : false;
    }

    public static function get($pPath = '/', $pMethod = 'GET', $pPostData = null)
    {
        if (!self::$config) {
            self::setupConfig();
        }

        if (!extension_loaded('curl')) {
            return null;
        }

        $domain = self::$config['domain'];
        if (self::$config['port'] && self::$config['port'] != 80)
            $domain .= ':'.self::$config['port'];

        $ch = curl_init();

        if (strtoupper($pMethod) != 'GET') {
            $pPath .= (strpos($pPath, '?') === false ? '?' : '&') . '_method='.strtolower($pMethod);
        }

        $url = $domain.$pPath;

        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $cookieFile = \Core\Kryn::getTempFolder().'cookies.txt';
        touch($cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest"));

        if (strtoupper($pMethod) == 'POST' || strtoupper($pMethod) == 'PUT') {
            curl_setopt($ch, CURLOPT_POST, true);
        }

        if ($pPostData && count($pPostData) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $pPostData);
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $info['content'] = $response;

        return $info;
    }

    public static function clearCookies()
    {
        file_put_contents(\Core\Kryn::getTempFolder().'/cookies.txt', '');
    }

    public static function uninstall()
    {
        $trace = debug_backtrace();
        foreach ($trace as $t) {
            $string[] = basename($t['file']).':'.$t['line'];
        }

        if (file_exists('config.php')) {
            $config = include 'config.php';
        } else {
            die("Kryn.cms not installed. =>".implode(', ', $string)." \n");
        }

        $config['displayBeautyErrors'] = 0; //0 otherwise the exceptionHandler of kryn is used, that breaks the PHPUnit.

        require 'core/bootstrap.php';
        require 'core/bootstrap.startup.php';

        \Core\Kryn::loadModuleConfigs(true);

        $manager = new \Admin\Module\Manager;

        foreach ($config['activeModules'] as $module) {
            $manager->uninstall($module, false, true);
        }

        $manager->uninstall('users', false, true);
        $manager->uninstall('admin', false, true);
        $manager->uninstall('core', false, true);

        \Core\PropelHelper::updateSchema();

        \Core\SystemFile::remove('config.php');

        self::cleanup();

        \Core\PropelHelper::cleanup();

        \Admin\Utils::clearCache();

    }

    public static function install($pConfig)
    {
        $cfg = $pConfig;
        $cfg['displayBeautyErrors'] = 0; //0 otherwise the exceptionHandler of kryn is used, what breaks the PHPUnit.

        if (!file_put_contents('config.php', "<?php\n return ".var_export($cfg, true).'; '))
            throw new \FileNotWritableException('Can not install Kryn.cms. config.php not writeable.');

        require 'core/bootstrap.php';

        \Core\TempFile::createFolder('./');
        \Core\WebFile::createFolder('cache/');

        require 'core/bootstrap.startup.php';
        @ini_set('display_errors', 1);
        \Core\Kryn::loadModuleConfigs();

        $manager = new \Admin\Module\Manager;

        $_GET['domain'] = self::$config['domain'];

        \Core\TempFile::remove('propel');

        if (!\Propel::isInit()) {
            \Propel::initialize();
        }

        \Propel::setConfiguration(\Core\PropelHelper::getConfig());

        $manager->install('core', true);
        $manager->install('admin', true);
        $manager->install('users', true);

        foreach ($pConfig['activeModules'] as $module)
            $manager->install($module, true);

        \Core\PropelHelper::updateSchema();
        \Core\PropelHelper::generateClasses();

        $manager->installDatabase('core');
        $manager->installDatabase('admin');
        $manager->installDatabase('users');

        foreach ($pConfig['activeModules'] as $module)
            $manager->installDatabase($module);

        include 'core/bootstrap.startup.php';

        \Core\PropelHelper::cleanup();

        //load all configs
        \Core\Kryn::loadModuleConfigs();

        \Admin\Utils::clearCache();

    }

    public static function bootupCore()
    {
        if (file_exists('config.php')) {
            $cfg = include 'config.php';
        } else throw new \Exception('Kryn.cms not installed. (config.php not found)');

        $cfg = include 'config.php';
        $cfg['displayErrors'] = false;

        $_SERVER['PATH_INFO'] = '/';
        $_SERVER['SERVER_NAME'] = self::$config['domain'];

        require 'core/bootstrap.php';
        require 'core/bootstrap.startup.php';

        ini_set('display_errors', 1);

    }

    public static function cleanup()
    {
        //load all configs
        \Core\Kryn::loadModuleConfigs();

        \Core\Object::cleanup();

        \Admin\Utils::clearCache();

        \Core\Kryn::cleanup();

    }

}

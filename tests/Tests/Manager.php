<?php

namespace Tests;

use Core\Config\SystemConfig;

class Manager
{
    /**
     * @var array
     */
    public static $config = array();

    /**
     * @var string
     */
    public static $configFile = 'default.xml';

    /**
     * @param array $pConfigFile
     *
     * @throws \Exception
     */
    public static function setupConfig(array $pConfigFile = null)
    {
        $configFile = $pConfigFile ? : 'tests/config/' . (getenv('CONFIG_FILE') ? : self::$configFile);

        if (!file_exists($configFile)) {
            throw new \Exception("Config file not found: $configFile\n");
        }
        self::$config['config'] = new SystemConfig(file_get_contents($configFile));

        self::$config['host']  = getenv('HOST') ?: '127.0.0.1:8000';

        if (false !== getenv('DB_NAME')) {
            self::$config['config']->getDatabase()->getMainConnection()->setName(getenv('DB_NAME'));
        }

        if (false !== getenv('DB_USER')) {
            self::$config['config']->getDatabase()->getMainConnection()->setUsername(getenv('DB_USER'));
        }

        if (false !== getenv('DB_PW')) {
            self::$config['config']->getDatabase()->getMainConnection()->setPassword(getenv('DB_PW'));
        }

        if (false !== getenv('DB_SERVER')) {
            self::$config['config']->getDatabase()->getMainConnection()->setServer(getenv('DB_SERVER'));
        }

        if (false !== getenv('DB_TYPE')) {
            self::$config['config']->getDatabase()->getMainConnection()->setType(getenv('DB_TYPE'));
        }


    }

    /**
     * @param string $pConfigFile Default is config/default.json
     */
    public static function freshInstallation($pConfigFile = null)
    {
        self::setupConfig($pConfigFile);
        $cfg = self::$config['config'];

        if (file_exists('app/config/config.xml')) {
            self::uninstall();
        }

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
            die('Curl extension not loaded.');
        }

        $host = self::$config['host'];
        $ch = curl_init();

        if (strtoupper($pMethod) != 'GET') {
            $pPath .= (strpos($pPath, '?') === false ? '?' : '&') . '_method=' . strtolower($pMethod);
        }

        $url = $host . $pPath;

        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $cookieFile = \Core\Kryn::getTempFolder() . 'cookies.txt';
        touch($cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest"));

        if (strtoupper($pMethod) == 'POST' || strtoupper($pMethod) == 'PUT') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
        }

        if ($pPostData && count($pPostData) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($pPostData));
        }

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $info['content'] = $response;

        return $info;
    }

    public static function clearCookies()
    {
        file_put_contents(\Core\Kryn::getTempFolder() . '/cookies.txt', '');
    }

    public static function uninstall()
    {
        if (file_exists('app/config/config.xml')) {
            throw new \Exception("Kryn.cms is not installed. We can't uninstall it.");
        }

        \Core\Kryn::bootstrap();
        \Core\Kryn::loadModuleConfigs(true);

        $manager = new \Admin\Module\Manager;

        foreach (\Core\Kryn::getSystemConfig()->getBundleName() as $bundleName) {
            $manager->uninstall($bundleName, false);
        }

        $manager->uninstall('Users\\UsersBundle', false);
        $manager->uninstall('Admin\\AdminBundle', false);
        $manager->uninstall('Core\\CoreBundle', false);

        \Core\PropelHelper::updateSchema();

        \Core\SystemFile::remove('app/config/config.xml');
        self::cleanup();
    }

    public static function install(SystemConfig $pConfig)
    {
        $pConfig->save('app/config/config.xml');

        \Core\Kryn::bootstrap();

        \Core\TempFile::remove('propel');
        \Core\TempFile::remove('propel-classes');
        \Core\TempFile::createFolder('./');
        \Core\WebFile::createFolder('cache/');

        @ini_set('display_errors', 1);
        \Core\Kryn::loadModuleConfigs();

        $manager = new \Admin\Module\Manager;

        $_GET['domain'] = self::$config['host'];

        echo "\nInstallation\n";
        debugPrint('Installation start');
        $manager->install('Core\\CoreBundle');
        $manager->install('Admin\\AdminBundle');
        $manager->install('Users\\UsersBundle');
        debugPrint('Installed system bundles');

        foreach ($pConfig->getBundles() as $module) {
            $manager->install($module);
        }
        debugPrint('Installed extra bundles');

        \Core\PropelHelper::updateSchema();
        debugPrint('Updated schema');

        \Core\PropelHelper::generateClasses();
        debugPrint('Updated model classes');

        $manager->installDatabase('Core\\CoreBundle');
        $manager->installDatabase('Admin\\AdminBundle');
        $manager->installDatabase('Users\\UsersBundle');
        debugPrint('Installed system database entries');

        foreach ($pConfig->getBundles() as $module) {
            $manager->installDatabase($module);
        }
        debugPrint('Installed extra database entries');

        \Core\PropelHelper::cleanup();

        \Core\Kryn::bootstrap();
        debugPrint('Installed and bootstrapped');
        echo "Installation done.\n";

        \Admin\Utils::clearCache();
    }

    public static function bootupCore()
    {
        if (!file_exists('app/config/config.xml')) {
            throw new \Exception('Kryn.cms not installed. (app/config/config.xml not found)');
        }

        $_SERVER['PATH_INFO'] = '/';
        $_SERVER['SERVER_NAME'] = self::$config['domain'];

        \Core\Kryn::bootstrap();

        ini_set('display_errors', 1);
    }

    public static function cleanup()
    {
        //load all configs
        \Core\Kryn::loadModuleConfigs();
        \Core\Object::cleanup();
        \Admin\Utils::clearCache();
        \Core\PropelHelper::cleanup();
        \Core\Kryn::cleanup();

    }

}

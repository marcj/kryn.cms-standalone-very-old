<?php

namespace Tests;

class Manager
{
    /**
     * @var array
     */
    public static $config = array();

    /**
     * @var string
     */
    public static $configFile = 'default.json';

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
        self::$config = json_decode(file_get_contents($configFile), true);

        if (getenv('DOMAIN')) {
            self::$config['domain'] = getenv('DOMAIN');
        }

        if (getenv('PORT')) {
            self::$config['port'] = getenv('PORT');
        }

        if (getenv('DB_NAME')) {
            self::$config['config']['database']['name'] = getenv('DB_NAME');
        }

        if (getenv('DB_USER')) {
            self::$config['config']['database']['user'] = getenv('DB_USER');
        }

        if (getenv('DB_PW')) {
            self::$config['config']['database']['password'] = getenv('DB_PW');
        }

        if (getenv('DB_SERVER')) {
            self::$config['config']['database']['server'] = getenv('DB_SERVER');
        }

        if (getenv('DB_TYPE')) {
            self::$config['config']['database']['type'] = getenv('DB_TYPE');
        }

    }

    /**
     * @param string $pConfigFile Default is config/default.json
     */
    public static function freshInstallation($pConfigFile = null)
    {
        self::setupConfig($pConfigFile);
        $cfg = self::$config['config'];

        if (file_exists('config.php')) {
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
            return null;
        }

        $domain = self::$config['domain'];
        if (self::$config['port'] && self::$config['port'] != 80) {
            $domain .= ':' . self::$config['port'];
        }

        $ch = curl_init();

        if (strtoupper($pMethod) != 'GET') {
            $pPath .= (strpos($pPath, '?') === false ? '?' : '&') . '_method=' . strtolower($pMethod);
        }

        $url = $domain . $pPath;

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
        file_put_contents(\Core\Kryn::getTempFolder() . '/cookies.txt', '');
    }

    public static function uninstall()
    {
        $trace = debug_backtrace();
        foreach ($trace as $t) {
            $string[] = basename($t['file']) . ':' . $t['line'];
        }

        if (file_exists('config.php')) {
            $config = include 'config.php';
        } else {
            throw new \Exception("Kryn.cms not installed. =>" . implode(', ', $string) . " \n");
        }

        $config['displayBeautyErrors'] = 0; //0 otherwise the exceptionHandler of kryn is used, that breaks the PHPUnit.

        \Core\Kryn::bootstrap();
        \Core\Kryn::loadModuleConfigs(true);

        $manager = new \Admin\Module\Manager;

        foreach ($config['bundles'] as $bundleName) {
            $manager->uninstall($bundleName, false);
        }

        $manager->uninstall('Users\\UsersBundle', false);
        $manager->uninstall('Admin\\AdminBundle', false);
        $manager->uninstall('Core\\CoreBundle', false);

        \Core\PropelHelper::updateSchema();

        \Core\SystemFile::remove('config.php');
        self::cleanup();
    }

    public static function install($pConfig)
    {
        $cfg = $pConfig;
        $cfg['displayBeautyErrors'] = 0; //0 otherwise the exceptionHandler of kryn is used, what breaks the PHPUnit.

        if (!file_put_contents('config.php', "<?php\n return " . var_export($cfg, true) . '; ')) {
            throw new \FileNotWritableException('Can not install Kryn.cms. config.php not writeable.');
        }

        require 'src/Core/bootstrap.php';
        \Core\Kryn::bootstrap();

        \Core\TempFile::remove('propel');
        \Core\TempFile::remove('propel-classes');
        \Core\TempFile::createFolder('./');
        \Core\WebFile::createFolder('cache/');

        @ini_set('display_errors', 1);
        \Core\Kryn::loadModuleConfigs();

        $manager = new \Admin\Module\Manager;

        $_GET['domain'] = self::$config['domain'];

        echo "\nInstallation\n";
        debugPrint('Installation start');
        $manager->install('Core\\CoreBundle', true);
        $manager->install('Admin\\AdminBundle', true);
        $manager->install('Users\\UsersBundle', true);
        debugPrint('Installed system bundles');

        foreach ($pConfig['bundles'] as $module) {
            $manager->install($module, true);
        }
        debugPrint('Installed extra bundles');

        \Core\PropelHelper::updateSchema();
        debugPrint('Updated schema');

        \Core\PropelHelper::generateClasses();
        debugPrint('Updated model classes');

        $manager->installDatabase('Core\\CoreBundle');
        $manager->installDatabase('Admin\\AdminBundle');
        $manager->installDatabase('Users\\UsersBundle');
        debugPrint('Install system database entries');

        foreach ($pConfig['bundles'] as $module) {
            $manager->installDatabase($module);
        }
        debugPrint('Install extra database entries');

        \Core\PropelHelper::cleanup();

        \Core\Kryn::bootstrap();
        debugPrint('Installed and bootstrapped');
        echo "Installation done.\n";

        \Admin\Utils::clearCache();
    }

    public static function bootupCore()
    {
        if (!file_exists('config.php')) {
            throw new \Exception('Kryn.cms not installed. (config.php not found)');
        }

        $cfg = include 'config.php';
        $cfg['displayErrors'] = false;

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

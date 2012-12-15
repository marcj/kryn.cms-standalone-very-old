<?php

namespace Tests;


class Manager {

    public static $config;

    public static $configFile = 'default.mysql.json';

    /**
     * @param null $pConfigFile Default is config/default.mysql.json
     */
    public static function freshInstallation($pConfigFile = null){

        $configFile = $pConfigFile ?: 'test/config/'.(getenv('CONFIG_FILE')?getenv('CONFIG_FILE'):self::$configFile);

        if (!file_exists($configFile)){
            die("Config file not found: $configFile\n");
        }
        self::$config = json_decode(file_get_contents($configFile), true);

        if (getenv('DOMAIN'))
            self::$config['domain'] = getenv('DOMAIN');

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

        $cfg = self::$config['config'];
        $cfg['displayErrors'] = false;

        if (file_exists('config.php'))
            self::uninstall();

        self::install($cfg);

    }

    public static function get($pPath = '/', $pPostData = null){

        $domain = self::$config['domain'];
        if (self::$config['port'] && self::$config['port'] != 80)
            $domain .= ':'.self::$config['port'];

        $content = wget('http://'.$domain.$pPath, null, $pPostData);

        return $content;
    }

    public static function uninstall(){


        $trace = debug_backtrace();
        foreach ($trace as $t){
            $string[] = basename($t['file']).':'.$t['line'];
        }

        if (file_exists('config.php')){
            $config = include('config.php');
        } else {
            die("Kryn.cms not installed. =>".implode(', ', $string)." \n");
        }

        $config['displayBeautyErrors'] = 0; //0 otherwise the exceptionHandler of kryn is used, what breaks the PHPUnit.
        $cfg = $config;

        $doit = true;

        require('core/bootstrap.php');

        require('core/bootstrap.startup.php');
        \Core\Kryn::loadConfigs();

        $manager = new \Admin\Module\Manager;

            foreach ($config['activeModules'] as $module){
                $manager->uninstall($module, false, true);
            }

            $manager->uninstall('users', false, true);
            $manager->uninstall('admin', false, true);
            $manager->uninstall('core', false, true);

            \Core\PropelHelper::updateSchema();



        \Core\SystemFile::remove('config.php');

        self::cleanup();

        \Core\PropelHelper::cleanup();

        //load all configs
        \Core\Kryn::loadConfigs();

        \Admin\Utils::clearCache();

    }


    public static function install($pConfig){

        $cfg = $pConfig;
        $cfg['displayBeautyErrors'] = 0; //0 otherwise the exceptionHandler of kryn is used, what breaks the PHPUnit.

        if (!file_put_contents('config.php', "<?php\n return ".var_export($cfg, true).'; '))
            throw new \FileNotWritableException('Can not install Kryn.cms. config.php not writeable.');

        require('core/bootstrap.php');

        \Core\TempFile::createFolder('./');
        \Core\MediaFile::createFolder('cache/');

        require('core/bootstrap.startup.php');
        @ini_set('display_errors', 1);
        \Core\Kryn::loadConfigs();

        $manager = new \Admin\Module\Manager;

        $_GET['domain'] = self::$config['domain'];

        \Core\TempFile::remove('propel');

        if (!\Propel::isInit())
            \Propel::init(\Core\PropelHelper::getConfig());
        else
            \Propel::configure(\Core\PropelHelper::getConfig());

        try {

            foreach ($pConfig['activeModules'] as $module)
                $manager->install($module, true);

            \Core\PropelHelper::updateSchema();
            \Core\PropelHelper::generateClasses();

            $doit = true;
            include('core/bootstrap.startup.php');
        } catch (\Exception $ex){
            die($ex);
        }

        \Core\PropelHelper::cleanup();

        //load all configs
        \Core\Kryn::loadConfigs();

        \Admin\Utils::clearCache();

    }

    public static function bootupCore(){

        if (file_exists('config.php')){
            $cfg = include('config.php');
        } else throw new \Exception('Kryn.cms not installed.');

        $cfg = include('config.php');
        $cfg['displayErrors'] = false;

        //todo, make it configable
        $_SERVER['PATH_INFO'] = '/';
        $_SERVER['SERVER_NAME'] = 'localhost';

        require('core/bootstrap.php');
        require('core/bootstrap.startup.php');

        ini_set('display_errors', 1);

    }

    public static function cleanup(){

        //load all configs
        \Core\Kryn::loadConfigs();

        \Core\Object::cleanup();

        \Admin\Utils::clearCache();

        \Core\Kryn::cleanup();

    }

}
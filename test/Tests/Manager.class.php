<?php

namespace Tests;

class Manager {

    public static $config;

    public static $configFile = 'default.postgresql.json';

    /**
     * @param null $pConfigFile Default is config/default.mysql.json
     */
    public static function freshInstallation($pConfigFile = null){

        $configFile = $pConfigFile ?: 'config/'.self::$configFile;

        self::$config = json_decode(file_get_contents($configFile), true);

        $cfg = self::$config['config'];
        $cfg['displayErrors'] = false;

        if (file_exists('../config.php'))
            self::uninstall();

        self::install($cfg);

    }

    public static function uninstall(){

        $origin = getcwd();

        $trace = debug_backtrace();
        foreach ($trace as $t){
            $string[] = basename($t['file']).':'.$t['line'];
        }

        if (file_exists('../config.php')){
            $config = include('../config.php');
        } else {
            die("Kryn.cms not installed. =>".implode(', ', $string)." \n");
        }

        $config['displayErrors'] = 0; //0 otherwise the exceptionHandler of kryn is used, what breaks the PHPUnit.
        $cfg = $config;

        $doit = true;

        require('../core/bootstrap.php');

        require('../core/bootstrap.startup.php');
        @ini_set('display_errors', 1);

        chdir(PATH);

        $manager = new \Admin\Module\Manager;

        foreach ($config['activeModules'] as $module){
            $manager->uninstall($module, false, true);
        }

        $manager->uninstall('users', false, true);
        $manager->uninstall('admin', false, true);

        \Core\PropelHelper::updateSchema();

        \Core\SystemFile::remove('config.php');

        self::cleanup();

        \Core\PropelHelper::cleanup();

        //load all configs
        \Core\Kryn::loadConfigs();

        \Admin\Utils::clearCache();

        chdir($origin);
    }


    public static function install($pConfig){

        $origin = getcwd();

        $cfg = $pConfig;
        $cfg['displayErrors'] = 0; //0 otherwise the exceptionHandler of kryn is used, what breaks the PHPUnit.

        if (!file_put_contents('../config.php', "<?php\n return ".var_export($cfg, true).'; '))
            throw new \FileNotWritableException('Can not install Kryn.cms. config.php not writeable.');

        require('../core/bootstrap.php');
        @ini_set('display_errors', 1);

        chdir(PATH);

        $manager = new \Admin\Module\Manager;

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

        chdir($origin);
    }

    public static function bootupCore(){

        if (file_exists('../config.php')){
            $cfg = include('../config.php');
        } else throw new \Exception('Kryn.cms not installed.');

        $cfg = include('../config.php');
        $cfg['displayErrors'] = false;

        //todo, make it configable
        $_SERVER['PATH_INFO'] = '/';
        $_SERVER['SERVER_NAME'] = 'localhost';

        require('../core/bootstrap.php');
        require('../core/bootstrap.startup.php');

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
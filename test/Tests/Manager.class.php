<?php

namespace Tests;

class Manager {

    /**
     * @param null $pConfigFile Default is config/default.mysql.json
     */
    public static function freshInstallation($pConfigFile = null){

        $configFile = $pConfigFile ?: 'config/default.mysql.json';

        $config = json_decode(file_get_contents($configFile), true);
        $config['displayErrors'] = false;

        $cfg = $config;

        require('../core/bootstrap.php');
        @ini_set('display_errors', 1);

        $cwd = getcwd();
        chdir(PATH);

        $manager = new \Admin\Module\Manager;

        foreach ($config['activeModules'] as $module)
            $manager->installPre($module);

        \Core\TempFile::delete('propel');

        \Core\PropelHelper::writeXmlConfig();
        \Core\PropelHelper::writeBuildPorperties();
        \Core\PropelHelper::collectSchemas();

        \Core\PropelHelper::generateClasses();

        \Core\PropelHelper::updateSchema();

        foreach ($config['activeModules'] as $module)
            $manager->installDatabase($module);

        foreach ($config['activeModules'] as $module)
            $manager->installPost($module);

        \Core\PropelHelper::cleanup();

        //load all configs
        \Core\Kryn::loadConfigs();

        \Admin\Utils::clearCache();

        chdir($cwd);
    }

    public static function bootupCore(){

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
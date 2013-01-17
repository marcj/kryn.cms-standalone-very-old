<?php

$cwd = getcwd();
chdir(PATH);

@ini_set('display_errors', 0);

if (\Core\Kryn::$config['displayErrors']){
    @ini_set('display_errors', 1);
}

if (\Core\Kryn::$config['displayBeautyErrors']){
    set_error_handler("coreUtilsErrorHandler", E_CORE_ERROR|E_COMPILE_ERROR|E_RECOVERABLE_ERROR|E_ERROR|E_CORE_ERROR|E_USER_ERROR|E_PARSE);
    set_exception_handler("coreUtilsExceptionHandler");
}
register_shutdown_function('coreUtilsShutdownHandler');

/*
 * Propel orm initialisation.
 */
if (!is_dir(\Core\Kryn::getTempFolder().'propel-classes')){
    \Core\PropelHelper::init();
}

\Propel::setConfiguration(Core\PropelHelper::getConfig());

if (!\Propel::isInit())
    \Propel::initialize();


//read out the url so that we can use getArgv()
Core\Kryn::prepareUrl();

Core\Kryn::$admin = (getArgv(1) == 'admin');


/*
 * Initialize the config.php values. Make some vars compatible to older versions etc.
 */
Core\Kryn::initConfig();

Core\Kryn::initCache();

/*
 * Load current language
 */
Core\Kryn::loadLanguage();

/*
 * Load themes, db scheme and object definitions from configs
 */
Core\Kryn::loadModuleConfigs();


Core\Kryn::initModules();

if (Core\Kryn::$admin) {
    /*
    * Load the whole config of all modules
    */

    Core\Kryn::loadConfigs();

}

chdir($cwd);
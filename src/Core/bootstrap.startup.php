<?php

@ini_set('display_errors', 0);

if (Core\Kryn::$config['displayErrors']) {
    @ini_set('display_errors', 1);
}

if (Core\Kryn::$config['displayErrors']) {
    set_exception_handler("coreUtilsExceptionHandler");
    set_error_handler(
        "coreUtilsErrorHandler",
        E_CORE_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR | E_ERROR | E_CORE_ERROR | E_USER_ERROR | E_PARSE
    );
}
register_shutdown_function('coreUtilsShutdownHandler');

/*
 * Propel orm initialisation.
 */
if (!\Core\PropelHelper::loadConfig()) {
    Core\PropelHelper::init();
    \Core\PropelHelper::loadConfig();
}

/*
 * Initialize the config.php values. Make some vars compatible to older versions etc.
 */
Core\Kryn::initConfig();

/**
 * Initialize caching controllers
 */
Core\Kryn::initCache();

/*
 * Load themes and configs
 */
Core\Kryn::loadModuleConfigs();

/*
 * Load current language
 */
Core\Kryn::loadLanguage();

Core\Kryn::getLogger()->addDebug('Bootstrap loaded.');

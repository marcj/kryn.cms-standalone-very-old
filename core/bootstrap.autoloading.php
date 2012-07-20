<?php

namespace Core;

/**
 * Load active modules into Kryn::$extensions.
 */
Kryn::loadActiveModules();


/**
 * Register auto loader.
 *
 */
//init auto-loader for module folder.
foreach (Kryn::$extensions as $extension){
    spl_autoload_register(function($pClass) use($extension){
        if (file_exists($file = (($extension == 'kryn')?PATH_CORE:PATH_MODULE . $extension).'/'.
            str_replace('\\', '/', substr($pClass, (($pos=strpos($pClass,'\\'))?$pos+1:0)))
            .'.class.php')){

            require_once($file);
            return true;
        }
    });
}

//init auto-loader for propel libs.
spl_autoload_register(function ($pClass) {
    if (file_exists(PATH_CORE . '/entities/' . $pClass . '.class.php')){
        include PATH_CORE . '/entities/' . $pClass . '.class.php';
        return true;
    } else if (file_exists('lib/Smarty/' . $pClass . '.class.php')){
        include 'lib/Smarty/' . $pClass . '.class.php';
        return true;
    } else if (Kryn::$propelClassMap[$pClass.'.php']){
        include Kryn::$propelClassMap[$pClass.'.php'];
        return true;
    }
});

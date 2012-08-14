<?php

namespace Core;

/**
 * Load active modules into Kryn::$extensions.
 */
Kryn::loadActiveModules();



//init auto-loader for propel libs.
spl_autoload_register(function ($pClass) {
    if (Kryn::$propelClassMap[$pClass.'.php']){
        include Kryn::$propelClassMap[$pClass.'.php'];
        return true;
    }
    if ($pClass == 'Smarty'){
        include 'lib/Smarty/Smarty.class.php';
        return true;
    }
});

/**
 * Register auto loader.
 *
 */
//init auto-loader for module folder.
foreach (Kryn::$extensions as $extension){
    spl_autoload_register(function($pClass) use($extension){

        $clazz = str_replace('\\', '/', substr($pClass, (($pos=strpos($pClass,'\\'))?$pos+1:0))).'.class.php';

        if (file_exists($file = (($extension == 'kryn')?PATH_CORE:PATH_MODULE . $extension).'/controller/'.$clazz)){
            include($file);
            return true;
        }

        if (file_exists($file = (($extension == 'kryn')?PATH_CORE:PATH_MODULE . $extension).'/lib/'.$clazz)){
            include($file);
            return true;
        }

        if (file_exists($file = (($extension == 'kryn')?PATH_CORE:PATH_MODULE . $extension).'/'.$clazz)){
            include($file);
            return true;
        }
    });
}

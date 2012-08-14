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
spl_autoload_register(function($pClass){

    $extension = strtolower(substr($pClass, 0, strpos($pClass, '\\')));
    $fullClazz = str_replace('\\', '/', $pClass).'.class.php';

    $clazz = substr($fullClazz, strlen($extension)+1);

    if (file_exists($file = (($extension == 'core')?PATH_CORE:PATH_MODULE . $extension).'/controller/'.$clazz)){
        include($file);
        return true;
    }

    if (file_exists($file = (($extension == 'core')?PATH_CORE:PATH_MODULE . $extension).'/lib/'.$clazz)){
        include($file);
        return true;
    }

    if (file_exists($file = (($extension == 'core')?PATH_CORE:PATH_MODULE . $extension).'/'.$clazz)){
        include($file);
        return true;
    }

    if (file_exists($file = 'lib/'.$fullClazz)){
        include($file);
        return true;
    }


});

<?php

namespace Core;

/**
 * Load active modules into Kryn::$extensions.
 */
Kryn::loadActiveModules();


$propelClasses = Kryn::getTempFolder().'propel-classes/';
//init auto-loader for propel libs.
spl_autoload_register(function($pClass) use ($propelClasses) {

    if (substr($pClass, 0, 1) == '\\')
        $pClass = substr($pClass, 1);

    if (file_exists($propelClasses.$pClass.'.php')){
        include $propelClasses.$pClass.'.php';
        return true;
    }

    if ($pClass == 'Smarty'){
        include 'lib/Smarty/Smarty.class.php';
        return true;
    }
});

//init auto-loader for propel module models.
foreach (Kryn::$extensions as $extension){

    if (substr($pClass, 0, 1) == '\\')
        $pClass = substr($pClass, 1);

    spl_autoload_register(function ($pClass) use ($extension) {
        if (file_exists($clazz = 'module/'.$extension.'/model/'.$pClass.'.php')){
            include $clazz;
            return true;
        }
    });
}

/**
 * Register auto loader.
 *
 */
//init auto-loader for module folder.
spl_autoload_register(function($pClass){

    if (substr($pClass, 0, 1) == '\\')
        $pClass = substr($pClass, 1);

    $extension = strtolower(substr($pClass, 0, strpos($pClass, '\\')));
    $fullClazz = str_replace('\\', '/', $pClass).'.class.php';
    $fullClazzWC = str_replace('\\', '/', $pClass).'.php';

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

    if (file_exists($file = 'lib/'.$fullClazzWC)){
        include($file);
        return true;
    }


});


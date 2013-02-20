<?php

namespace Core;

/**
 * Register auto loader.
 *
 */

//composer's vendor autoload
include 'vendor/autoload.php';

//init auto-loader for module folder.
spl_autoload_register(function($pClass){

    if (substr($pClass, 0, 1) == '\\')
        $pClass = substr($pClass, 1);

    $extension = strtolower(substr($pClass, 0, strpos($pClass, '\\')));
    $fullClazz = str_replace('\\', '/', $pClass).'.class.php';
    $fullClazzWC = str_replace('\\', '/', $pClass).'.php';

    $clazz = substr($fullClazzWC, strlen($extension)+1);

    if (file_exists($file = PATH.(($extension == 'core')?PATH_CORE:PATH_MODULE . $extension).'/controller/'.$clazz)) {
        include($file);

        return true;
    }

    if (file_exists($file = PATH.(($extension == 'core')?PATH_CORE:PATH_MODULE . $extension).'/lib/'.$clazz)) {
        include($file);

        return true;
    }

    if (file_exists($file = PATH.(($extension == 'core')?PATH_CORE:PATH_MODULE . $extension).'/'.$clazz)) {
        include($file);

        return true;
    }

    if (file_exists($file = PATH.'lib/'.$fullClazz)) {
        include($file);

        return true;
    }

    if (file_exists($file = PATH.'lib/'.$fullClazzWC)) {
        include($file);

        return true;
    }


});

//init auto-loader for propel models.
spl_autoload_register(function ($pClass) {


    $ext = strtolower(substr($pClass, 0, $sPos = strpos($pClass, '\\')));
    $clazz = substr($pClass, $sPos+1);

    if (file_exists($clazz = PATH.Kryn::getModuleDir($ext).'model/'.$clazz.'.php')) {
        include $clazz;

        return true;
    }

});


//init auto-loader for propel model base libs.
spl_autoload_register(function($pClass) {

    $propelClasses = Kryn::getTempFolder().'propel-classes/';

    if (substr($pClass, 0, 1) == '\\')
        $pClass = substr($pClass, 1);
    $pClass = str_replace('\\', '/', $pClass);

    if (file_exists($propelClasses.$pClass.'.php')) {
        include $propelClasses.$pClass.'.php';

        return true;
    }
});

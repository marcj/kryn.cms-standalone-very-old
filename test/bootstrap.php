<?php


//setup auto-loader
spl_autoload_register(function ($pClass) {

    $pClass = substr($pClass, strpos($pClass, '\\')+1);
    $fullClazz = 'Tests/'.str_replace('\\', '/', $pClass).'.class.php';

    if (file_exists($fullClazz)){
        include $fullClazz;
        return true;
    }
});
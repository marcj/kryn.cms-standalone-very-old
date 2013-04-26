<?php

namespace Core;

/**
 * Register auto loader.
 *
 */

//composer's vendor autoload
$loader = include __DIR__ . '/../../vendor/autoload.php';

$loader->add('Core', __DIR__ . '/..');
$loader->add('Admin', __DIR__ . '/..');
$loader->add('Users', __DIR__ . '/..');
$loader->add('Publication', PATH . '/modules/kryn-publication-bundle');
$loader->add('', '/Users/marc/Kryn.cms/vendor/krynlabs/kryn-demotheme/');


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
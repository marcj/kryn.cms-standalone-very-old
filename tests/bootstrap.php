<?php

define('KRYN_TESTS', true);

$loader = include 'vendor/autoload.php';

$loader->add('', __DIR__ . '/');
$loader->add('', __DIR__ . '/bundles');

\Core\Kryn::setLoader($loader);

if (!getenv('NOINSTALL')) {
    @unlink('app/config/config.xml');
}

ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);



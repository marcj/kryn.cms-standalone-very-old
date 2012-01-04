<?php

if (php_sapi_name() !== 'cli') exit;

$testSet = $argv[1];
chdir('..');

require('inc/kryn/bootstrap.php');

require(PATH . 'testSets/lib/krynTestSets.global.php');

tAssign('time', $time);
date_default_timezone_set($cfg['timezone']);
if (!empty($cfg['locale']))
    setlocale(LC_ALL, $cfg['locale']);
define('pfx', $cfg['db_prefix']);

if ($testSet){
    include(PATH . 'testSets/' . $testSet . '.php');
}

?>
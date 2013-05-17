<?php

define('KRYN_TESTS', true);

$loader = include __DIR__ . '/../vendor/autoload.php';

/*$configFile = __DIR__ . '/config/' . (getenv('CONFIG_FILE') ?: 'default.json');

$json   = file_get_contents($configFile);
$config = json_decode($json, true);
if (!$config) {
    die('Config file is corrupt: ' . $configFile . PHP_EOL);
}

if ($config['require']) {
    $composerFile = 'composer.json';
    $json         = file_get_contents($composerFile);
    $composer     = json_decode($json, true);
    if (!$composer) {
        die('composer.json is corrupt.');
    }

    $config['require'] = $config['require'] ?: array();

    foreach ($config['require'] as $require => $version) {
        if (!isset($composer['require'][$require])) {
            $composer['require'][$require] = $version;
        }
    }

    file_put_contents($composerFile, json_encode($composer));

    //update composer
    $input = new \Symfony\Component\Console\Input\StringInput('');
    $input->setInteractive(false);
    $output = new \Symfony\Component\Console\Output\StreamOutput(fopen('php://output', 'rw'));

    $io       = new \Composer\IO\ConsoleIO($input, $output, new \Symfony\Component\Console\Helper\HelperSet(array()));
    $composer = \Composer\Factory::create($io);
    $install  = \Composer\Installer::create($io, $composer);
    $install
        //->setVerbose($input->getOption('verbose'))
        //->setPreferSource($input->getOption('prefer-source'))
        //->setPreferDist($input->getOption('prefer-dist'))
        //->setDevMode(true)
        ->setUpdate(true)
        ->setUpdateWhitelist(array_keys($config['require']));

    if (!$install->run()){
        die('composer install failed.');
    }

    $loader->unregister();
    $autoload = 'vendor/composer' . '/autoload_real.php';
    $content  = file_get_contents($autoload);
    preg_match('/class ([a-zA-Z0-9]+)/', $content, $match);
    $class = $match[1];
    include $autoload;
    $loader = $class::getLoader();

    unset($install);
    unset($composer);
    unset($io);
    unset($input);
    unset($config);
    unset($composer);
    unset($json);
}*/

$loader->add('', __DIR__ . '/');
$loader->add('', __DIR__ . '/bundles');

\Core\Kryn::setLoader($loader);

if (!getenv('NOINSTALL')) {
    @unlink(__DIR__ . '/../app/config/config.xml');
}

ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);



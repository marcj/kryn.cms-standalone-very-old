<?php

namespace Tests;

/**
 * This class provides in setUp an fresh installation and bootup Kryn.cms core, s
 * you can work in your tests as you would do in a Kryn.cms module.
 *
 * This uninstalls (removes config.php as well) in tearDown().
 *
 */
class TestCaseWithInstallation extends \PHPUnit_Framework_TestCase {

    protected function setUp(){
        if (!$this->bootUp++){
            Manager::freshInstallation();
            Manager::bootupCore();
        }
    }

    protected function tearDown(){
        if ($this->bootUp--)
            Manager::uninstall();
    }

}
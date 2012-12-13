<?php

namespace Tests\Module;

use Tests\Manager;

class BasicTest extends \PHPUnit_Framework_TestCase {


    public static function setUpBeforeClass(){
        Manager::freshInstallation();
        Manager::bootupCore();
    }

    public function testPrimaryKeys(){

        $active = \Core\Kryn::isActiveModule('test');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveModule('core');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveModule('users');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveModule('admin');
        $this->assertTrue($active);

    }

    public static function tearDownAfterClass(){
        Manager::cleanup();
    }


}
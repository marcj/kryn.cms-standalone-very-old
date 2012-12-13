<?php

namespace Tests\Object;

use Tests\Manager;

class GeneralTest extends \PHPUnit_Framework_TestCase {


    public static function setUpBeforeClass(){
        Manager::freshInstallation();
        Manager::bootupCore();
    }

    public function testObject(){

        $definition =& \Core\Kryn::$objects['test'];
        $this->assertNotEmpty($definition);

        $objectClass = \Core\Object::getClass('test');
        $this->assertNotEmpty($objectClass);

    }

    public static function tearDownAfterClass(){
        Manager::cleanup();
    }

}
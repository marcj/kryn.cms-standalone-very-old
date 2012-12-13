<?php

namespace Tests\Object;

use Tests\Manager;

class PrimaryKeysTest extends \PHPUnit_Framework_TestCase {


    public static function setUpBeforeClass(){
        Manager::freshInstallation();
        Manager::bootupCore();
    }

    public function testPrimaryKeys(){

        $primaryKeys = \Core\Object::getPrimaryList('test');
        $this->assertEquals(array('id'), $primaryKeys);

    }

    public static function tearDownAfterClass(){
        Manager::cleanup();
    }


}
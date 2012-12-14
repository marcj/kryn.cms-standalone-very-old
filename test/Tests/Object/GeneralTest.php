<?php

namespace Tests\Object;

use Tests\TestCaseWithInstallation;

class GeneralTest extends TestCaseWithInstallation {

    public function testObject(){

        $definition =& \Core\Kryn::$objects['test'];
        $this->assertNotEmpty($definition);

        $objectClass = \Core\Object::getClass('test');
        $this->assertNotEmpty($objectClass);

    }

}
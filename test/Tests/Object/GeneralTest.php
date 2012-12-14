<?php

namespace Tests\Object;

use Tests\TestCaseWithInstallation;

class GeneralTest extends TestCaseWithInstallation {

    public function testObject(){

        $definition =& \Core\Object::getDefinition('Test\\Test');
        $this->assertNotEmpty($definition);

        $objectClass = \Core\Object::getClass('Test\\Test');
        $this->assertNotEmpty($objectClass);

    }

}
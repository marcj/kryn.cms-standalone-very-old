<?php

namespace Tests\Object;

use Tests\TestCaseWithCore;

class GeneralTest extends TestCaseWithCore {

    public function testObject(){

        $definition =& \Core\Object::getDefinition('Test\\Test');
        $this->assertNotEmpty($definition);

        $objectClass = \Core\Object::getClass('Test\\Test');
        $this->assertNotEmpty($objectClass);

    }

}
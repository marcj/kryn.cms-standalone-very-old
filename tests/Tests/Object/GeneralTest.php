<?php

namespace Tests\Object;

use Tests\TestCaseWithCore;

class GeneralTest extends TestCaseWithCore
{
    public function testObject()
    {
        $definition = \Core\Object::getDefinition('Test\\Test');
        $this->assertNotEmpty($definition);
        $this->assertInstanceOf('Core\Config\Object', $definition);

        $this->assertEquals('Test', $definition->getId());
        $this->assertEquals('name', $definition->getLabel());

        $objectClass = \Core\Object::getClass('Test\\Test');
        $this->assertNotEmpty($objectClass);
        $this->assertInstanceOf('Core\ORM\ORMAbstract', $objectClass);
    }
}

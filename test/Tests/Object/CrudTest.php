<?php

namespace Tests\Object;

use Tests\TestCaseWithCore;

class CreateTest extends TestCaseWithCore {

    public function testObject(){

        \Core\Object::clear('Test\\Test');

        //check empty
        $count = \Core\Object::getCount('Test\\Test');
        $this->assertEquals(0, $count);

        //new object
        $values = array('name' => 'Hallo "\'Peter, ✔');
        $pk = \Core\Object::add('Test\\Test', $values);

        //check if inserted correctly
        $this->assertArrayHasKey('id', $pk);
        $this->assertGreaterThan(0, $pk['id']);

        //get through single value pk and check result
        $item = \Core\Object::get('Test\\Test', $pk['id']);
        $this->assertGreaterThan(0, $item['id']);
        $this->assertEquals($values['name'], $item['name']);

        //get through array pk and check result
        $item = \Core\Object::get('Test\\Test', $pk);
        $this->assertGreaterThan(0, $item['id']);
        $this->assertEquals($values['name'], $item['name']);

        //check count
        $count = \Core\Object::getCount('Test\\Test');
        $this->assertGreaterThan(0, $count);

        //remove
        \Core\Object::remove('Test\\Test', $pk);

        //check empty
        $count = \Core\Object::getCount('Test\\Test');
        $this->assertEquals(0, $count);

    }

}
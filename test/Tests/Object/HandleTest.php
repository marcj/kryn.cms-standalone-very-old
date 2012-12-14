<?php

namespace Tests\Object;

use Tests\Manager;
use Tests\TestCaseWithInstallation;

class CreateTest extends TestCaseWithInstallation {

    public function testObject(){

        \Core\Object::clear('test');

        //check empty
        $count = \Core\Object::getCount('test');
        $this->assertEquals(0, $count);

        //new object
        $values = array('name' => 'Hallo "\'Peter, ✔');
        $pk = \Core\Object::add('test', $values);

        //check if inserted correctly
        $this->assertArrayHasKey('id', $pk);
        $this->assertGreaterThan(0, $pk['id']);

        //get through single value pk and check result
        $item = \Core\Object::get('test', $pk['id']);
        $this->assertGreaterThan(0, $item['id']);
        $this->assertEquals($values['name'], $item['name']);

        //get through array pk and check result
        $item = \Core\Object::get('test', $pk);
        $this->assertGreaterThan(0, $item['id']);
        $this->assertEquals($values['name'], $item['name']);

        //check count
        $count = \Core\Object::getCount('test');
        $this->assertGreaterThan(0, $count);

        //remove
        \Core\Object::remove('test', $pk);

        //check empty
        $count = \Core\Object::getCount('test');
        $this->assertEquals(0, $count);

    }

}
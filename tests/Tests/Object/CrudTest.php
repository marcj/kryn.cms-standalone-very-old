<?php

namespace Tests\Object;

use Core\Object;
use Tests\TestCaseWithCore;

class CreateTest extends TestCaseWithCore
{
    public function testObject()
    {
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

    public function testAdd()
    {
        $values = array(
            'title' => 'News item',
            'intro' => 'Lorem ipsum',
            'newsDate' => strtotime(array_rand(['+', '-']) . rand(1, 30) . ' day', array_rand(['+', '-']) . rand(1, 24) . ' hours')
        );
        $pk = Object::add('Publication\\News', $values);

        $item = Object::get('publication:news', $pk);

        $this->assertEquals($values['title'], $item['title']);
        $this->assertEquals($values['intro'], $item['intro']);
        $this->assertEquals($values['newsDate'], $item['newsDate']);

        $this->assertTrue(Object::remove('publication:news', $pk));

        $this->assertNull(Object::get('publication:news', $pk));
    }

}

<?php

namespace Tests\Object;

use Tests\TestCaseWithCore;
use \Core\Object;
use \Core\WorkspaceManager;

class WorkspacesTest extends TestCaseWithCore
{
    public function testDifferentWorkspaces()
    {
        Object::clear('Publication\\News');

        WorkspaceManager::setCurrent(0);
        Object::add('Publication\\News', array(
            'title' => 'News 1 in workspace live'
        ));
        Object::add('Publication\\News', array(
            'title' => 'News 2 in workspace live'
        ));

        WorkspaceManager::setCurrent(1);
        Object::add('Publication\\News', array(
            'title' => 'News 1 in workspace one'
        ));
        Object::add('Publication\\News', array(
            'title' => 'News 2 in workspace one'
        ));
        Object::add('Publication\\News', array(
            'title' => 'News 3 in workspace one'
        ));

        //anything inserted and selecting works correctly?
        WorkspaceManager::setCurrent(0);
        $count = Object::getCount('Publication\\News');
        $this->assertEquals(2, $count);

        WorkspaceManager::setCurrent(1);
        $count = Object::getCount('Publication\\News');
        $this->assertEquals(3, $count);

        //anything inserted and selecting works correctly, also through propel directly?
        WorkspaceManager::setCurrent(0);
        $count = \Publication\NewsQuery::create()->count();
        $this->assertEquals(2, $count);

        WorkspaceManager::setCurrent(1);
        $count = \Publication\NewsQuery::create()->count();
        $this->assertEquals(3, $count);

    }

    public function testThroughCoreObjectWrapper()
    {
        Object::clear('Publication\\News');
        $count = Object::getCount('Publication\\News');
        $this->assertEquals(0, $count);

        $id11 = 0;

        for ($i=1; $i<=50;$i++) {
            $pk = Object::add('Publication\\News', array(
                'title' => 'News '.$i,
                'intro' => str_repeat('L', $i)
            ));
            if ($i == 11) $id11 = $pk['id'];
        }

        $count = Object::getCount('Publication\\News');
        $this->assertEquals(50, $count);

        $item = Object::get('Publication\\News', $id11);
        $this->assertEquals('News 11', $item['title']);

        Object::update('Publication\\News', $id11, array(
            'title' => 'New News 11'
        ));

        $item = Object::get('Publication\\News', $id11);
        $this->assertEquals('New News 11', $item['title']);

        Object::update('Publication\\News', $id11, array(
            'title' => 'New News 11 - 2'
        ));

        //check version counter - we have 2 updates, so we have 2 versions.
        $count = \Publication\NewsVersionQuery::create()->filterById($id11)->count();
        $this->assertEquals(2, $count);

        Object::remove('Publication\\News', $id11);

        //check version counter - we have 2 updates and 1 deletion (one deletion creates 2 new versions,
        //first regular version and second placeholder for deletion, so we have 4 versions now.
        $count = \Publication\NewsVersionQuery::create()->filterById($id11)->count();
        $this->assertEquals(4, $count);

        $item = Object::get('Publication\\News', $id11);
        $this->assertNull($item); //should be gone

        Object::clear('Publication\\News');

    }

    public function testThroughPropelObjects()
    {
        \Publication\NewsQuery::create()->deleteAll();
        \Publication\NewsVersionQuery::create()->deleteAll();

        $count = \Publication\NewsQuery::create()->count();
        $this->assertEquals(0, $count);

        $id = 0;

        for ($i=1; $i<=50;$i++) {

            $object = new \Publication\News;
            $object->setTitle('News '.$i);
            $object->setIntro(str_repeat('L', $i));
            $object->save();
            if ($i == 11)
                $id = $object->getId();

        }

        $count = \Publication\NewsQuery::create()->count();
        $this->assertEquals(50, $count);

        $item = \Publication\NewsQuery::create()->findOneById($id);
        $this->assertEquals('News 11', $item->getTitle());

        $item->setTitle('New News 11');
        $item->save();

        $item = \Publication\NewsQuery::create()->findOneById($id);
        $this->assertEquals('New News 11', $item->getTitle());

        //check version counter - we have 1 update, so we have 1 version.
        $count = \Publication\NewsVersionQuery::create()->filterById($id)->count();
        $this->assertEquals(1, $count);

        $item->delete();

        //check version counter - we have 1 update and 1 deletion (one deletion creates 2 new versions,
        //first regular version and second placeholder for deletion, so we have 3 versions now.
        $count = \Publication\NewsVersionQuery::create()->filterById($id)->count();
        $this->assertEquals(3, $count);

        $item = \Publication\NewsQuery::create()->findOneById($id);
        $this->assertNull($item); //should be gone

    }

}

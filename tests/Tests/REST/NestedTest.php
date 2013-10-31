<?php

namespace Tests\Permission;

use Core\Models\NodeQuery;
use Tests\Manager;
use Tests\RestTestCase;

class NestedTest extends RestTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->login();
    }

    public function testNestedMultipleAdd()
    {

        $blog = NodeQuery::create()->findOneByTitle('Blog');

        $post = array(
            'visible' => true,
            '_items' => [
                ['title' => 'Rest Nested Test', 'type' => 0, 'layout' => '@KrynDemoThemeBundle.krynDemoTheme/layout_default.tpl']
            ],
            '_multiple' => true,
            '_position' => 'next',
            '_pk' => ['id' => $blog->getId()],
            '_targetObjectKey' => 'core:node'
        );

        $response = $this->restCall('/kryn/admin/nodes/:multiple', 'POST', $post);

        $this->assertEquals(200, $response['status']);
        $this->assertGreaterThan(1, $response['data'][0]['id']);

        $id = $response['data'][0]['id'];

        $item = NodeQuery::create()->findPk($id);

        $this->assertGreaterThan(0, $item->getLft());
        $this->assertGreaterThan(0, $item->getRgt());
        $this->assertGreaterThan(0, $item->getLevel());

        $item->delete();
    }

    public function testNestedMultipleAddTwo()
    {

        $blog = NodeQuery::create()->findOneByTitle('Blog');

        $post = array(
            'visible' => true,
            '_items' => [
                ['title' => 'Rest Nested Test 1', 'type' => 0, 'layout' => '@KrynDemoThemeBundle.krynDemoTheme/layout_default.tpl'],
                ['title' => 'Rest Nested Test 2', 'type' => 0, 'layout' => '@KrynDemoThemeBundle.krynDemoTheme/layout_default.tpl']
            ],
            '_multiple' => true,
            '_position' => 'next',
            '_pk' => ['id' => $blog->getId()],
            '_targetObjectKey' => 'core:node'
        );

        $response = $this->restCall('/kryn/admin/nodes/:multiple', 'POST', $post);

        $this->assertEquals(200, $response['status']);
        $this->assertGreaterThan(1, $response['data'][0]['id']);
        $this->assertGreaterThan(1, $response['data'][1]['id']);

        $id = $response['data'][0]['id'];
        $id2 = $response['data'][1]['id'];

        $item = NodeQuery::create()->findPk($id);
        $item2 = NodeQuery::create()->findPk($id2);

        $this->assertGreaterThan(0, $item->getLft());
        $this->assertGreaterThan(0, $item->getRgt());
        $this->assertGreaterThan(0, $item->getLevel());

        $this->assertGreaterThan(0, $item2->getLft());
        $this->assertGreaterThan(0, $item2->getRgt());
        $this->assertGreaterThan(0, $item2->getLevel());

        $item->delete();
        $item2->delete();
    }

}
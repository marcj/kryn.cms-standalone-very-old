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

    /**
    * @group test
    */
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
        $this->assertGreaterThan(1, $response['data']['id']);

        //todo, get $id and check lft/rgt values
    }

}
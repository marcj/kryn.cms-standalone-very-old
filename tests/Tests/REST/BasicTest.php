<?php

namespace Tests\Permission;

use Test\Models\Item;
use Test\Models\ItemQuery;
use Tests\Manager;
use Tests\RestTestCase;

class BasicTest extends RestTestCase
{
    public function setUp()
    {
        parent::setUp();

        //login as admin
        $loggedIn = $this->restCall('/kryn/admin/logged-in');

        if (!$loggedIn || !$loggedIn['data']) {
            Manager::get('/kryn/admin/login?username=admin&password=admin');
        }
    }

    public function testBasics()
    {
        $loggedIn = $this->restCall('/kryn/admin/logged-in');
        $this->assertTrue($loggedIn['data'], 'we are logged in.');

        $response = Manager::get('/kryn');

        $this->assertNotEmpty($response['content']);

        $this->assertContains('Kryn.cms Administration', $response['content'], "we got the login view.");

        $this->assertContains('window._session = {"userId":1', $response['content'], "we're logged in.");
    }

    public function testSettings()
    {
        $result = $this->restCall('/kryn/admin/backend/settings?lang=en');
        $this->assertInternalType('array', $result);

        $this->assertEquals(200, $result['status']);
    }

    public function testListing()
    {
        $response = $this->restCall('/kryn/admin/object/Core:Node');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(14, count($response['data']), "we have 14 nodes from the installation script.");

        ItemQuery::create()->deleteAll();

        $response = $this->restCall('/kryn/admin/object/Test:Item');

        $this->assertEquals(200, $response['status']);
        $this->assertNull($response['data'], 'if we have no items, we should get NULL.');

        $item1 = new Item();
        $item1->setTitle('Item 1');
        $item1->save();

        $item2 = new Item();
        $item2->setTitle('Item 2');
        $item2->save();
        $id2 = $item2->getId();

        $response = $this->restCall('/kryn/admin/object/Test:Item');

        $this->assertEquals(200, $response['status']);
        $this->assertEquals(2, count($response['data']));

        $response = $this->restCall('/kryn/admin/object/Test:Item/' . $id2);

        $this->assertEquals(200, $response['status']);
        $this->assertEquals($id2, $response['data']['id']);

    }

    public function testUpdating()
    {
        ItemQuery::create()->deleteAll();

        $item1 = new Item();
        $item1->setTitle('Item 1');
        $item1->save();
        $id = $item1->getId();

        $response = $this->restCall('/kryn/admin/object/Test:Item/' . $id . '?fields=title');
        $this->assertEquals('Item 1', $response['data']['title']);

        $response = $this->restCall(
            '/kryn/admin/object/Test:Item/' . $id,
            'PUT',
            array(
                 'title' => 'Item 1 modified'
            )
        );

        $this->assertEquals(200, $response['status']);
        $this->assertTrue($response['data']);

        //did we really store the new value?
        $response = $this->restCall('/kryn/admin/object/Test:Item/' . $id);
        $this->assertEquals('Item 1 modified', $response['data']['title']);

    }

    public function testDelete()
    {
        ItemQuery::create()->deleteAll();

        $item1 = new Item();
        $item1->setTitle('Item 1');
        $item1->save();
        $id = $item1->getId();

        $response = $this->restCall('/kryn/admin/object/Test:Item/' . $id);
        $this->assertEquals('Item 1', $response['data']['title']);

        $response = $this->restCall('/kryn/admin/object/Test:Item/' . $id, 'DELETE');

        $this->assertEquals(200, $response['status']);
        $this->assertTrue($response['data']);

        //did we really delete it?
        $response = $this->restCall('/kryn/admin/object/Test:Item/' . $id);
        $this->assertNull($response['data']);
    }

    public function testAdd()
    {
        ItemQuery::create()->deleteAll();

        $item1 = new Item();
        $item1->setTitle('Item 1');
        $item1->save();
        $id = $item1->getId();

        $response = $this->restCall('/kryn/admin/object/Test:Item/' . $id);
        $this->assertEquals('Item 1', $response['data']['title']);

        $response = $this->restCall(
            '/kryn/admin/object/Test:Item',
            'POST',
            array(
                 'title' => 'Item 2'
            )
        );

        $this->assertEquals(200, $response['status']);
        $this->assertEquals($id + 1, $response['data']['id'] + 0);

        //did we really inserted it?
        $response = $this->restCall('/kryn/admin/object/Test:Item/' . $response['data']['id']);
        $this->assertEquals($id + 1, $response['data']['id'] + 0);

    }

}

<?php

namespace Tests\Permission;

use Tests\TestCaseWithCore;
use Test\Item;
use Test\ItemQuery;
use Test\Test;
use Test\TestQuery;
use Core\Permission;

use Users\Group;
use Users\User;

class ObjectTest extends TestCaseWithCore {

    public function testObject(){

        ItemQuery::create()->deleteAll();
        TestQuery::create()->deleteAll();
        Permission::removeObjectRules('Test\\Item');

        $user = new User();
        $user->setUsername('TestUser');
        $user->save();

        $group = new Group();
        $group->setName('ACL Test group');
        $group->addGroupMembershipUser($user);
        $group->save();

        $item1 = new Item();
        $item1->setTitle('Item 1');
        $item1->save();

        $item2 = new Item();
        $item2->setTitle('Item 2');
        $item2->save();

        $test1 = new Test();
        $test1->setName('Test 1');
        $test1->save();

        $this->assertFalse(Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'we have no rules, so everyone except admin user and admin group has no access.');

        $this->assertTrue(Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, 1),
            'we have no rules, so only group admin has access.');

        $this->assertTrue(Permission::checkList('Test\\Item', $item1->getId(), Permission::USER, 1),
            'we have no rules, so only user admin has access.');

        Permission::setObjectList('Test\\Item', Permission::GROUP, $group->getId(), true);
        $this->assertTrue(Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'testGroup got list access to all test\\item objects.');


        Permission::setObjectListExact('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId(), false);
        $this->assertFalse(Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'testGroup got list access-denied to item 1.');

        $this->assertTrue(Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup still have access to item2.');

        Permission::setObjectListExact('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId(), false);
        $this->assertFalse(Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup does not have access to item2 anymore.');


        $acl = Permission::setObjectListExact('Test\\Item', $item2->getId(), Permission::USER, $user->getId(), true);
        $this->assertFalse(Permission::checkList('Test\\Item', $item2->getId(), Permission::USER, $user->getId()),
            'testUser got access through a rule for only him.');


        $acl->setAccess(false);
        $acl->save();
        Permission::clearCache();
        $this->assertFalse(Permission::checkList('Test\\Item', $item2->getId(), Permission::USER, $user->getId()),
            'testUser got no-access through a rule for only him.');


        //access to every item
        $acl = Permission::setObjectList('Test\\Item', Permission::GROUP, $group->getId(), true);
        $this->assertTrue(Permission::checkList('Test\\Item', $item2->getId(), Permission::USER, $user->getId()),
            'testUser has now access to all items through his group.');
        $this->assertTrue(Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'testGroup has now access to all items.');
        $this->assertTrue(Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup has now access to all items.');


        //remove the acl item that gives access to anything.
        $acl->delete();
        Permission::clearCache();
        $this->assertFalse(Permission::checkList('Test\\Item', $item2->getId(), Permission::USER, $user->getId()),
            'testUser has no access anymore, since we deleted the access-for-all rule.');
        $this->assertFalse(Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'testGroup has no access anymore to all items (item1).');
        $this->assertFalse(Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup has no access anymore to all items (item2).');


        //check checkListCondition
        Permission::setObjectListCondition('Test\\Item', array(array('id', '>', $item1->getId())), Permission::GROUP, $group->getId(), true);
        $this->assertTrue(Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup has access to all items after item1');

        $this->assertFalse(Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'testGroup has access to all items after item1, but only > , so not item1 itself.');

        //revoke anything to object 'test\item'
        Permission::setObjectList('Test\\Item', Permission::GROUP, $group->getId(), false);
        $this->assertFalse(Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup has no access to all items after item1');

        //check against object test
        Permission::setObjectListExact('Test\\Test', $test1->getId(), Permission::GROUP, $group->getId(), true);
        $this->assertTrue(Permission::checkList('Test\\Test', $test1->getId(), Permission::GROUP, $group->getId()),
            'testGroup has access test1.');

        Permission::setObjectList('Test\\Test', Permission::GROUP, $group->getId(), false);
        $this->assertFalse(Permission::checkList('Test\\Test', $test1->getId(), Permission::GROUP, $group->getId()),
            'testGroup has no access test1.');



        //Permission::setObjectListExact('Test\\Item', $item1->getId(), Permission::GROUP, $user->getId(), true);
        //Permission::setObjectList('Test\\Item', $item2->getId(), Permission::GROUP, $user->getId(), false);

        /*
        Permission::setObjectUpdate('Test\\Item', $item1->getId(), Permission::GROUP, $user->getId(), true);
        Permission::setObjectUpdate('Test\\Item', $item2->getId(), Permission::GROUP, $user->getId(), false);

        Permission::setObjectAdd('Test\\Item', $item1->getId(), Permission::GROUP, $user->getId(), true,
            array(
                 'desc' => false
            )
        );


        Permission::setObjectDelete('Test\\Item', $item1->getId(), Permission::GROUP, $user->getId(), false);
        Permission::setObjectView('Test\\Item', $item1->getId(), Permission::GROUP, $user->getId(), false);
*/

        //check permissions

        //$this->assertTrue(Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $user->getId()));
        //$this->assertFalse(Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $user->getId()));

    }

}
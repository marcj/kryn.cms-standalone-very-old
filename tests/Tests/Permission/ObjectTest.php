<?php

namespace Tests\Permission;

use Core\Config\Condition;
use Core\Models\Acl;
use Core\Models\Base\DomainQuery;
use Core\Models\Base\NodeQuery;
use Core\Models\Node;
use Core\Object;
use Tests\TestCaseWithCore;
use Test\Models\Item;
use Test\Models\ItemQuery;
use Test\Models\Test;
use Test\Models\TestQuery;
use Test\Models\ItemCategory;
use Test\Models\ItemCategoryQuery;
use Core\Permission;

use Users\Models\Group;
use Users\Models\User;

class ObjectTest extends TestCaseWithCore
{
    public function testConditionToSql()
    {

        $condition = new Condition();

        $condition2 = new Condition();
        $condition2->addAnd([
            'title', '=', 'TestNode tree'
        ]);

        $condition->addAnd($condition2);
        $condition->addOr([
            '1', '=', '1'
        ]);

        $params = [];
        $sql = $condition->toSql($params, 'core:node');

        $expectedArray = [
            [
                ['title', '=', 'TestNode tree']
            ],
            'OR',
            [
                '1', '=', '1'
            ]
        ];
        $this->assertEquals($expectedArray, $condition->toArray());
        $this->assertEquals([':p1' => 'TestNode tree'], $params);
        $this->assertEquals(' kryn_system_node.title = :p1  OR 1= 1', $sql);
    }

    public function testNestedSubPermission()
    {
        Permission::setCaching(true);
        Permission::removeObjectRules('core:node');

        \Core\Kryn::getClient()->login('admin', 'admin');

        $user = \Core\Kryn::getClient()->getUser();

        $domain = DomainQuery::create()->findOne();

        $root = NodeQuery::create()->findRoot($domain->getId());

        $subNode = new Node();
        $subNode->setTitle('TestNode tree');
        $subNode->insertAsFirstChildOf($root);
        $subNode->save();

        $subNode2 = new Node();
        $subNode2->setTitle('TestNode sub');
        $subNode2->insertAsFirstChildOf($subNode);
        $subNode2->Save();

        //make access for all
        $rule = new Acl();
        $rule->setAccess(true);
        $rule->setObject('core:node');
        $rule->setTargetType(Permission::USER);
        $rule->setTargetId($user->getId());
        $rule->setMode(Permission::ALL);
        $rule->setConstraintType(Permission::CONSTRAINT_ALL);
        $rule->setPrio(0);
        $rule->save();

        //revoke access for all children of `TestNode tree`
        $rule2 = new Acl();
        $rule2->setAccess(false);
        $rule2->setObject('core:node');
        $rule2->setTargetType(Permission::USER);
        $rule2->setTargetId($user->getId());
        $rule2->setMode(Permission::ALL);
        $rule2->setConstraintType(Permission::CONSTRAINT_CONDITION);
        $rule2->setConstraintCode(json_encode([
            'title', '=', 'TestNode tree'
        ]));
        $rule2->setPrio(1);
        $rule2->setSub(true);
        $rule2->save();

        $items = Object::getBranch('core:node', $subNode->getId(), null, 1, null, [
            'permissionCheck' => true
        ]);
        $this->assertNull($items, 'rule2 revokes the access to all elements');


        $rule2->setSub(false);
        $rule2->save();
        $items = Object::getBranch('core:node', $subNode->getId(), null, 1, null, [
            'permissionCheck' => true
        ]);
        $this->assertEquals('TestNode sub', $items[0]['title'], 'We got TestNode sub');


        $rule2->setAccess(true);
        $rule2->save();
        $items = Object::getBranch('core:node', $subNode->getId(), null, 1, null, [
            'permissionCheck' => true
        ]);
        $this->assertEquals('TestNode sub', $items[0]['title'], 'We got TestNode sub');


        $subNode->delete();
        $rule->delete();
        $rule2->delete();
    }

    public function xtestSpeed()
    {
        $item = new Item();
        $item->setTitle('Item 1');
        $item->save();

        debugPrint('start');
        $objectItem = Object::get('test:item', ['id' => $item->getId()]);
        debugPrint('---');
        $objectItem = Object::get('test:item', ['id' => $item->getId()]);
        debugPrint('done');

        $item->delete();
    }

    public function testRuleCustom()
    {
        ItemCategoryQuery::create()->deleteAll();
        ItemQuery::create()->deleteAll();
        TestQuery::create()->deleteAll();
        Permission::setCaching(true);
        Permission::removeObjectRules('Test\\Item');

        $user = new User();
        $user->setUsername('testuser');
        $user->save();

        $item1 = new Item();
        $item1->setTitle('Item 1');
        $item1->save();

        $item2 = new Item();
        $item2->setTitle('Item test');
        $item2->save();

        $rule = new Acl();
        $rule->setAccess(true);
        $rule->setObject('test:item');
        $rule->setTargetType(Permission::USER);
        $rule->setTargetId($user->getId());
        $rule->setMode(Permission::ALL);
        $rule->setConstraintType(Permission::CONSTRAINT_ALL);
        $rule->setPrio(0);
        $rule->save();

        $rule = new Acl();
        $rule->setAccess(false);
        $rule->setObject('test:item');
        $rule->setTargetType(Permission::USER);
        $rule->setTargetId($user->getId());
        $rule->setMode(Permission::ALL);
        $rule->setConstraintType(Permission::CONSTRAINT_CONDITION);
        $rule->setConstraintCode(json_encode([
            ['title', 'LIKE', '%test']
        ]));
        $rule->setPrio(1);
        $rule->save();

        $access1 = Permission::checkListExact(
            'test:item',
            $item1->getId(),
            Permission::USER,
            $user->getId()
        );

        $access2 = Permission::checkListExact(
            'test:item',
            $item2->getId(),
            Permission::USER,
            $user->getId()
        );

        $this->assertTrue($access1, 'item1 has access as the second rule doesnt grab and first rule says all access=true');
        $this->assertFalse($access2, 'no access to item2 as we have defined access=false in second rule.');

        $user->delete();

        Permission::setCaching(true);
        Permission::removeObjectRules('Test\\Item');
    }

    public function testRulesWithFields()
    {
        ItemCategoryQuery::create()->deleteAll();
        ItemQuery::create()->deleteAll();
        TestQuery::create()->deleteAll();
        Permission::setCaching(false);
        Permission::removeObjectRules('Test\\Item');

        $user = new User();
        $user->setUsername('TestUser');
        $user->save();

        $group = new Group();
        $group->setName('ACL Test group');
        $group->addGroupMembershipUser($user);
        $group->save();

        $cat1 = new ItemCategory();
        $cat1->setName('Nein');

        $item1 = new Item();
        $item1->setTitle('Item 1');
        $item1->addItemCategory($cat1);
        $item1->save();

        $cat2 = new ItemCategory();
        $cat2->setName('Hiiiii');

        $item2 = new Item();
        $item2->setTitle('Item 2');
        $item2->addItemCategory($cat2);
        $item2->save();

        Permission::removeObjectRules('Test\\Item');
        $fields = array(
            'oneCategory' => array(
                array(
                    'access' => false,
                    'condition' => array(array('id', '>', $cat1->getId()))
                )
            )
        );
        Permission::setObjectUpdate('Test\\Item', Permission::USER, $user->getId(), true, $fields);

        $this->assertFalse(
            Permission::checkUpdate(
                'Test\\Item',
                array('oneCategory' => $cat2->getId()),
                Permission::USER,
                $user->getId()
            )
        );
        $this->assertTrue(
            Permission::checkUpdate(
                'Test\\Item',
                array('oneCategory' => $cat1->getId()),
                Permission::USER,
                $user->getId()
            )
        );

        Permission::removeObjectRules('Test\\Item');
        $fields = array(
            'oneCategory' => array(
                array(
                    'access' => false,
                    'condition' => array(array('name', '=', 'Nein'))
                )
            )
        );

        Permission::setObjectUpdate('Test\\Item', Permission::USER, $user->getId(), true, $fields);

        $this->assertTrue(
            Permission::checkUpdate(
                'Test\\Item',
                array('oneCategory' => $cat2->getId()),
                Permission::USER,
                $user->getId()
            )
        );
        $this->assertFalse(
            Permission::checkUpdate(
                'Test\\Item',
                array('oneCategory' => $cat1->getId()),
                Permission::USER,
                $user->getId()
            )
        );

        Permission::removeObjectRules('Test\\Item');

        $fields = array(
            'title' => array(
                array(
                    'access' => false,
                    'condition' => array(array('title', 'LIKE', 'peter %'))
                )
            )
        );
        Permission::setObjectUpdate('Test\\Item', Permission::USER, $user->getId(), true, $fields);

        $this->assertTrue(
            Permission::checkUpdate('Test\\Item', array('title' => 'Heidenau'), Permission::USER, $user->getId())
        );
        $this->assertTrue(
            Permission::checkUpdate('Test\\Item', array('title' => 'peter'), Permission::USER, $user->getId())
        );
        $this->assertFalse(
            Permission::checkUpdate('Test\\Item', array('title' => 'peter 2'), Permission::USER, $user->getId())
        );
        $this->assertFalse(
            Permission::checkUpdate('Test\\Item', array('title' => 'peter asdad'), Permission::USER, $user->getId())
        );

        Permission::removeObjectRules('Test\\Item');

        $fields = array('title' => array(array('access' => false, 'condition' => array(array('title', '=', 'peter')))));
        Permission::setObjectUpdate('Test\\Item', Permission::USER, $user->getId(), true, $fields);

        $this->assertTrue(
            Permission::checkUpdate('Test\\Item', array('title' => 'Heidenau'), Permission::USER, $user->getId())
        );
        $this->assertFalse(
            Permission::checkUpdate('Test\\Item', array('title' => 'peter'), Permission::USER, $user->getId())
        );
        $this->assertTrue(
            Permission::checkUpdate('Test\\Item', array('title' => 'peter2'), Permission::USER, $user->getId())
        );

        Permission::setCaching(true);
        Permission::removeObjectRules('Test\\Item');
    }

    public function texxstObjectGeneral()
    {
        ItemQuery::create()->deleteAll();
        TestQuery::create()->deleteAll();
        Permission::removeObjectRules('Test\\Item');
        Permission::setCaching(false);

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

        $this->assertFalse(
            Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'we have no rules, so everyone except admin user and admin group has no access.'
        );

        $this->assertTrue(
            Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, 1),
            'we have no rules, so only group admin has access.'
        );

        $this->assertTrue(
            Permission::checkList('Test\\Item', $item1->getId(), Permission::USER, 1),
            'we have no rules, so only user admin has access.'
        );

        Permission::setObjectList('Test\\Item', Permission::GROUP, $group->getId(), true);
        $this->assertTrue(
            Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'testGroup got list access to all test\\item objects.'
        );

        Permission::setObjectListExact('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId(), false);
        $this->assertFalse(
            Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'testGroup got list access-denied to item 1.'
        );

        $this->assertTrue(
            Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup still have access to item2.'
        );

        Permission::setObjectListExact('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId(), false);
        $this->assertFalse(
            Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup does not have access to item2 anymore.'
        );

        $acl = Permission::setObjectListExact('Test\\Item', $item2->getId(), Permission::USER, $user->getId(), true);
        $this->assertTrue(
            Permission::checkList('Test\\Item', $item2->getId(), Permission::USER, $user->getId()),
            'testUser got access through a rule for only him.'
        );

        $acl->setAccess(false);
        $acl->save();
        Permission::clearCache();
        $this->assertFalse(
            Permission::checkList('Test\\Item', $item2->getId(), Permission::USER, $user->getId()),
            'testUser got no-access through a rule for only him.'
        );

        //access to every item
        $acl = Permission::setObjectList('Test\\Item', Permission::GROUP, $group->getId(), true);
        $this->assertTrue(
            Permission::checkList('Test\\Item', $item2->getId(), Permission::USER, $user->getId()),
            'testUser has now access to all items through his group.'
        );
        $this->assertTrue(
            Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'testGroup has now access to all items.'
        );
        $this->assertTrue(
            Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup has now access to all items.'
        );

        //remove the acl item that gives access to anything.
        $acl->delete();
        Permission::clearCache();
        $this->assertFalse(
            Permission::checkList('Test\\Item', $item2->getId(), Permission::USER, $user->getId()),
            'testUser has no access anymore, since we deleted the access-for-all rule.'
        );
        $this->assertFalse(
            Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'testGroup has no access anymore to all items (item1).'
        );
        $this->assertFalse(
            Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup has no access anymore to all items (item2).'
        );

        //check checkListCondition
        Permission::setObjectListCondition(
            'Test\\Item',
            array(array('id', '>', $item1->getId())),
            Permission::GROUP,
            $group->getId(),
            true
        );
        $this->assertTrue(
            Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup has access to all items after item1'
        );

        $this->assertFalse(
            Permission::checkList('Test\\Item', $item1->getId(), Permission::GROUP, $group->getId()),
            'testGroup has access to all items after item1, but only > , so not item1 itself.'
        );

        //revoke anything to object 'test\item'
        Permission::setObjectList('Test\\Item', Permission::GROUP, $group->getId(), false);
        $this->assertFalse(
            Permission::checkList('Test\\Item', $item2->getId(), Permission::GROUP, $group->getId()),
            'testGroup has no access to all items after item1'
        );

        //check against object test
        Permission::setObjectListExact('Test\\Test', $test1->getId(), Permission::GROUP, $group->getId(), true);
        $this->assertTrue(
            Permission::checkList('Test\\Test', $test1->getId(), Permission::GROUP, $group->getId()),
            'testGroup has access test1.'
        );

        Permission::setObjectList('Test\\Test', Permission::GROUP, $group->getId(), false);
        $this->assertFalse(
            Permission::checkList('Test\\Test', $test1->getId(), Permission::GROUP, $group->getId()),
            'testGroup has no access test1.'
        );

        Permission::setCaching(true);
        Permission::removeObjectRules('Test\\Item');
    }

}

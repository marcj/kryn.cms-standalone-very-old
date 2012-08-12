<?php

if (!defined('KRYN_MANAGER')) return false;

SessionQuery::create()->deleteAll();
UserGroupQuery::create()->deleteAll();
UserQuery::create()->deleteAll();
GroupQuery::create()->deleteAll();
AclQuery::create()->deleteAll();

$groupGuest = new Group();
$groupGuest->setName('Guest');
$groupGuest->setDescription('All anonymous user');
$groupGuest->save();

$id = $groupGuest->getId(0);
dbUpdate('system_group', array('id' => $id), array('id' => 0));

$groupUsers = new Group();
$groupUsers->setName('Users');
$groupUsers->setDescription('Registered user');
$groupUsers->save();

$groupAdmin = new Group();
$groupAdmin->setName('Admin');
$groupAdmin->setDescription('Super user');
$groupAdmin->save();
$id = $groupAdmin->getId(0);
dbUpdate('system_group', array('id' => $id), array('id' => 1));
$groupAdmin->setId(1);


$admin = new User();
$admin->setUsername('admin');
$admin->setFirstName('Admini');
$admin->setLastName('strator');
$admin->setEmail('admin@localhost');
$admin->setActivate(1);
$admin->setPassword('admin');

$settings = new \Core\Properties(array(
    'userBg' => '/admin/images/userBgs/defaultImages/color-blue.jpg',
    'adminLanguage' => 'en'
));

$admin->setSettings($settings);
$admin->save();

$id = $admin->getId(0);
dbUpdate('system_user', array('id' => $id), array('id' => 1));
$admin->setId(1);

$admin->addGroupMembership($groupAdmin);
$admin->save();
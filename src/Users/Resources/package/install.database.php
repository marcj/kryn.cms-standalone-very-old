<?php

use Core\Models\AclQuery;
use Core\Models\WorkspaceQuery;
use Users\Models\Group;
use Users\Models\GroupQuery;
use Users\Models\SessionQuery;
use Users\Models\User;
use Users\Models\UserGroupQuery;
use Users\Models\UserQuery;

if (!defined('KRYN_MANAGER')) {
    return false;
}

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

$groupAdmin = new Group();
$groupAdmin->setName('Admin');
$groupAdmin->setDescription('Super user');
$groupAdmin->save();
$id = $groupAdmin->getId();
dbUpdate('system_group', array('id' => $id), array('id' => 1));
$groupAdmin->setId(1);

$groupUsers = new Group();
$groupUsers->setName('Users');
$groupUsers->setDescription('Registered user');
$groupUsers->save();

$admin = new User();
$admin->setUsername('admin');
$admin->setFirstName('Admini');
$admin->setLastName('strator');
$admin->setEmail('admin@localhost');
$admin->setActivate(1);
$admin->setPassword('admin');
$liveWorkspace = WorkspaceQuery::create()->findOneById(1);
$admin->addWorkspace($liveWorkspace);

$settings = new \Core\Properties(array(
                                      'userBg' => '/admin/images/userBgs/defaultImages/color-blue.jpg',
                                      'adminLanguage' => 'en'
                                 ));

$admin->setSettings($settings);
$admin->save();

$id = $admin->getId();
dbUpdate('system_user', array('id' => $id), array('id' => 1));
$admin->setId(1);

$admin->addGroupMembership($groupAdmin);
$admin->save();

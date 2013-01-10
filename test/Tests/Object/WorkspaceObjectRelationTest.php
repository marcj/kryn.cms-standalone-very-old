<?php

namespace Tests\Object;

use Tests\TestCaseWithInstallation;
use \Core\Object;
use \Core\WorkspaceManager;

use Test\Item;
use Test\ItemQuery;
use Test\ItemCategory;
use Test\ItemCategoryQuery;


class WorkspaceObjectRelationTest extends TestCaseWithInstallation {

    public function testThroughPropel(){

        WorkspaceManager::setCurrent(0);

        ItemQuery::create()->deleteAll();
        ItemCategoryQuery::create()->deleteAll();

        WorkspaceManager::setCurrent(0);

        //TODO

    }

}
<?php

namespace Tests\Object;

use Tests\TestCaseWithCore;
use \Core\Object;
use \Core\WorkspaceManager;

use Test\Models\ItemQuery;
use Test\Models\ItemCategoryQuery;

class WorkspaceObjectRelationTest extends TestCaseWithCore
{
    public function testThroughPropel()
    {
        WorkspaceManager::setCurrent(0);

        ItemQuery::create()->deleteAll();
        ItemCategoryQuery::create()->deleteAll();

        WorkspaceManager::setCurrent(0);

        //TODO

    }

}

<?php

namespace Tests\Module;

use Tests\Manager;
use Tests\TestCaseWithInstallation;

class BasicTest extends TestCaseWithInstallation {

    public function testPrimaryKeys(){

        $active = \Core\Kryn::isActiveModule('test');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveModule('core');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveModule('users');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveModule('admin');
        $this->assertTrue($active);

    }

}
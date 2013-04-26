<?php

namespace Tests\Module;

use Tests\TestCaseWithCore;

class BasicTest extends TestCaseWithCore
{
    public function testGeneral()
    {
        $active = \Core\Kryn::isActiveModule('core');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveModule('users');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveModule('admin');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveModule('test');
        $this->assertTrue($active);

        $this->assertTrue(is_dir('web/cache'));
        $this->assertTrue(is_writable('web/cache'));

        $this->assertTrue(is_writable(\Core\Kryn::getTempFolder()));

        $this->assertInstanceOf('Test\\Test', new \Test\Test());

    }

}

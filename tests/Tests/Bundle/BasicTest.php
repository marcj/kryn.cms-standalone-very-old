<?php

namespace Tests\Module;

use Tests\TestCaseWithCore;

class BasicTest extends TestCaseWithCore
{
    public function testGeneral()
    {
        $active = \Core\Kryn::isActiveBundle('core');

        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveBundle('users');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveBundle('admin');
        $this->assertTrue($active);

        $active = \Core\Kryn::isActiveBundle('test');
        $this->assertTrue($active, 'Module `test` is active.`');

        $this->assertTrue(is_dir('web/cache'));
        $this->assertTrue(is_writable('web/cache'));

        $this->assertTrue(is_writable(\Core\Kryn::getTempFolder()));

        $this->assertInstanceOf('Test\\Controller\\Test', new \Test\Controller\Test());
    }

}

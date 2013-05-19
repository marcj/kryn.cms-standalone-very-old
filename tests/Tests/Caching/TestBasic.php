<?php

namespace Tests\Caching;

use Tests\TestCaseWithCore;

use Core\Kryn;

class BasicTest extends TestCaseWithCore
{
    public function testGeneral()
    {
        //invalidation check
        $this->assertTrue(Kryn::setCache('core/test/2', 'Test Object number 2'));

        $this->assertTrue(Kryn::invalidateCache('core/test'));
        usleep(1000*50); //50ms

        $this->assertFalse(Kryn::getCache('core/test/2'));

        //without invalidation
        $this->assertTrue(Kryn::setCache('core/test/2', 'Test Object number 2'));
        $this->assertEquals('Test Object number 2', Kryn::getCache('core/test/2'));
    }

}

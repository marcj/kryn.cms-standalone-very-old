<?php

namespace Tests\Core;

use Core\Config\Cache;
use Core\Config\Client;
use Core\Config\Database;
use Core\Config\Errors;
use Core\Config\FilePermission;
use Core\Config\SessionStorage;
use Core\Config\SystemConfig;
use Core\Config\Connection;
use Core\Kryn;
use Tests\TestCaseWithCore;

class KrynTest extends TestCaseWithCore
{
    public function testBasics()
    {
        $this->assertEquals('kryn', Kryn::getAdminPrefix());
        $this->assertEquals('kryn-5d12', Kryn::getId());
        $this->assertInstanceOf('Core\HttpRequest', Kryn::getRequest());
        $this->assertInstanceOf('Core\PageResponse', Kryn::getResponse());
    }
}
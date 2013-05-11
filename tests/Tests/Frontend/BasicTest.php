<?php

namespace Tests\Frontend;

use Tests\Manager;
use Tests\TestCaseWithFreshInstallation;

class BasicTest extends TestCaseWithFreshInstallation
{
    public function setUp()
    {
        return;
        $response = Manager::get('/core/data.test');

        if (strpos($response['content'], 'OK') === false) {
            $this->markTestSkipped('Is looks like the DOMAIN or http server is not correctly configured. Skipped.');
        }

    }

    public function testGeneral()
    {
        return;
        $response = Manager::get('/');
        $this->assertTrue($response['http_code'] == 200);

    }

}

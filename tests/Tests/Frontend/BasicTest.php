<?php

namespace Tests\Frontend;

use Tests\Manager;
use Tests\TestCaseWithCore;

class BasicTest extends TestCaseWithCore
{
    public function setUp()
    {
        $response = Manager::get('/bundles/core/data.test');

        if (strpos($response['content'], 'OK') === false) {
            $this->markTestSkipped('Is looks like the DOMAIN or http server is not correctly configured. Skipped.');
        }

    }

    public function testGeneral()
    {
        $response = Manager::get('/');
        $this->assertTrue($response['http_code'] == 200);
    }

}

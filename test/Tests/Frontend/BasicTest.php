<?php

namespace Tests\Frontend;

use Tests\Manager;
use Tests\TestCaseWithFreshInstallation;

class BasicTest extends TestCaseWithFreshInstallation {

    public function setUp(){

        $response = Manager::get('/README.md');

        if (!strpos($response['content'], 'Kryn.cms')){
            $this->markTestSkipped('Is looks like the DOMAIN or http server is not correctly configured. Skipped.');
        }

    }

    public function testGeneral(){

        $response = Manager::get('/');
        $this->assertTrue($response['status'] == 200);


    }

}
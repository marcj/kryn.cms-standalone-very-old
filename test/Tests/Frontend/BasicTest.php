<?php

namespace Tests\Frontend;

use Tests\Manager;
use Tests\TestCaseWithInstallation;

class BasicTest extends TestCaseWithInstallation {

    public function testGeneral(){

        $response = Manager::get('/');
        $this->assertTrue($response['status'] == 200);


    }

}
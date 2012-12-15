<?php

namespace Tests\Frontend;

use Tests\Manager;
use Tests\TestCaseWithInstallation;

class BasicTest extends TestCaseWithInstallation {

    public function testGeneral(){

        $result = Manager::get('/');
        var_dump($result);
        $this->assertTrue($result['status'] == 200);

    }

}
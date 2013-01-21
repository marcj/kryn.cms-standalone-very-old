<?php

namespace Tests;

/**
 * This class provides in setUp an fresh installation and bootup Kryn.cms core, s
 * you can work in your tests as you would do in a Kryn.cms module.
 *
 * This uninstalls (removes config.php as well) in tearDown().
 *
 */
class RestTestCase extends TestCaseWithCore {

    public $currentDir = '';

    public function restCall($pPath = '/', $pMethod = 'GET', $pPostData = null){

        $info = Manager::get($pPath, $pMethod, $pPostData);
        $data = json_decode($info['content'], true);

        if ($data['error']){
            $this->fail(json_format(json_encode($data)));
        }

        return !json_last_error() ? $data : false;
    }

    public function setUp(){

        if (!function_exists('curl_init')){
            $this->markTestSkipped('PHP module curl is not installed.');
        }

        $response = Manager::get('/README.md');

        if (strpos($response['content'], 'Kryn.cms') === false){
            $this->markTestSkipped('Is looks like the DOMAIN or http server is not correctly configured. Skipped.');
        }

    }
    public function run(\PHPUnit_Framework_TestResult $result = NULL){

        $this->currentDir = getcwd();

        if ($result === NULL) {
            $result = $this->createResult();
        }
        if (!$this->bootUp++){
            try {

                if (!file_exists('config.php')){
                    Manager::freshInstallation();
                }
                Manager::bootupCore();
            } catch (\Exception $ex){
                $result->addError($this, $ex, 0);
                return $result;
            }
        }

        $result = parent::run($result);

        return $result;

    }

}
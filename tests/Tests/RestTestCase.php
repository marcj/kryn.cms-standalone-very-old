<?php

namespace Tests;

/**
 * This class provides in setUp an fresh installation and bootup Kryn.cms core, s
 * you can work in your tests as you would do in a Kryn.cms module.
 *
 * This uninstalls (removes config.php as well) in tearDown().
 *
 */
class RestTestCase extends TestCaseWithCore
{
    public $currentDir = '';

    public function restCall($pPath = '/', $pMethod = 'GET', $pPostData = null)
    {
        $info = Manager::get($pPath, $pMethod, $pPostData);
        $data = json_decode($info['content'], true);

        if ($data['error']) {
            $this->fail(json_format(json_encode($data)));
        }

        return !json_last_error() ? $data : $info['content'];
    }

    public function setUp()
    {
        if (!function_exists('curl_init')) {
            $this->markTestSkipped('PHP module curl is not installed.');
        }

        $response = Manager::get('/bundles/core/data.test');

        if (strpos($response['content'], 'OK') === false) {
            $this->markTestSkipped('Is looks like the DOMAIN or http server is not correctly configured. Skipped.');
        }

    }
}

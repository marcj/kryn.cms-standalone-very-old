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

    public function login()
    {
        //login as admin
        $loggedIn = $this->restCall('/kryn/admin/logged-in');

        if (!$loggedIn || !$loggedIn['data']) {
            Manager::get('/kryn/admin/login?username=admin&password=admin');
        }
    }

    public function restCall($path = '/', $method = 'GET', $postData = null, $failOnError = true)
    {
        $info = Manager::get($path, $method, $postData);
        $data = json_decode($info['content'], true);

        if ($failOnError && (!is_array($data) || $data['error'])) {
            $this->fail(
                "path $path, method: $method: (status code: {$info['http_code']}):\n".
                var_export($info, true)
            );
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
            $this->markTestSkipped('Is looks like the HOST or http server is not correctly configured. Skipped.');
        }

    }
}

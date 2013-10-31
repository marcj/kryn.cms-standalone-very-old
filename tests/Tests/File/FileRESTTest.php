<?php

namespace Tests\File;

use Core\Kryn;
use Core\TempFile;
use Tests\Manager;
use Tests\RestTestCase;
use Tests\TestCaseWithCore;

class FileRESTTest extends RestTestCase
{
    public function setUp()
    {
        parent::setUp();

        //login as admin
        $loggedIn = $this->restCall('/kryn/admin/logged-in');

        if (!$loggedIn || !$loggedIn['data']) {
            Manager::get('/kryn/admin/login?username=admin&password=admin');
        }
    }

    public function testListing()
    {
        $response = $this->restCall('/kryn/admin/file?path=/');
        $bundle = null;
        foreach ($response['data'] as $file) {
            if ('/bundles' === $file['path']) {
                $bundle = $file;
            }
        }

        $this->assertNotNull($bundle);
        $this->assertGreaterThan(0, $bundle['id']);
        $this->assertEquals('/bundles', $bundle['path']);
        $this->assertEquals('bundles', $bundle['name']);
        $this->assertEquals('/', $bundle['dir']);
        $this->assertEquals(true, $bundle['writeAccess']);
        $this->assertEquals('dir', $bundle['type']);
    }


    public function testListingSingle()
    {
        $response = $this->restCall('/kryn/admin/file/single?path=/');

        $file = $response['data'];

        $this->assertNotNull($file);
        $this->assertGreaterThan(0, $file['id']);
        $this->assertEquals('/', $file['path']);
        $this->assertEquals('/', $file['name']);
        $this->assertEquals('/', $file['dir']);
        $this->assertEquals(true, $file['writeAccess']);
        $this->assertEquals('dir', $file['type']);

        $response = $this->restCall('/kryn/admin/file/single?path=/bundles');

        $file = $response['data'];

        $this->assertNotNull($file);
        $this->assertGreaterThan(0, $file['id']);
        $this->assertEquals('/bundles', $file['path']);
        $this->assertEquals('bundles', $file['name']);
        $this->assertEquals('/', $file['dir']);
        $this->assertEquals(true, $file['writeAccess']);
        $this->assertEquals('dir', $file['type']);
    }

    public function testListingBundles()
    {
        $response = $this->restCall('/kryn/admin/file?path=/bundles');
        $files = [];
        foreach ($response['data'] as $file) {
            $files[$file['path']] = $file;
        }

        $admin = $files['/bundles/admin'];
        $this->assertNotNull($admin);
        $this->assertGreaterThan(0, $admin['id']);
        $this->assertEquals('/bundles/admin', $admin['path']);
        $this->assertEquals('admin', $admin['name']);
        $this->assertEquals('/bundles', $admin['dir']);
        $this->assertEquals(true, $admin['writeAccess']);
        $this->assertEquals('dir', $admin['type']);

        $users = $files['/bundles/users'];
        $this->assertNotNull($users);
        $this->assertGreaterThan(0, $users['id']);
        $this->assertEquals('/bundles/users', $users['path']);
        $this->assertEquals('users', $users['name']);
        $this->assertEquals('/bundles', $users['dir']);
        $this->assertEquals(true, $users['writeAccess']);
        $this->assertEquals('dir', $users['type']);
    }

    public function testCreateFolder()
    {
        $id = dechex(time() / mt_rand(100, 500));
        $testPath = '/test_' . $id;
        $response = $this->restCall('/kryn/admin/file/folder?path=' . $testPath, 'POST');
        $this->assertEquals(true, $response['data']);

        $response = $this->restCall('/kryn/admin/file/single?path=' . $testPath);
        $file = $response['data'];

        $this->assertNotNull($file);
        $this->assertGreaterThan(0, $file['id']);
        $this->assertEquals($testPath, $file['path']);
        $this->assertEquals(basename($testPath), $file['name']);
        $this->assertEquals(dirname($testPath), $file['dir']);
        $this->assertEquals(true, $file['writeAccess']);
        $this->assertEquals('dir', $file['type']);

        $response = $this->restCall('/kryn/admin/file?path=' . $testPath, 'DELETE');

        $this->assertEquals(true, $response['data']);
    }

    public function testCreateFile()
    {
        $id = dechex(time() / mt_rand(100, 500));
        $testPath = '/test_' . $id . '.txt';
        $response = $this->restCall('/kryn/admin/file?path=' . $testPath, 'POST');
        $this->assertEquals(true, $response['data']);

        $response = $this->restCall('/kryn/admin/file/single?path=' . $testPath);
        $file = $response['data'];

        $this->assertNotNull($file);
        $this->assertGreaterThan(0, $file['id']);
        $this->assertEquals($testPath, $file['path']);
        $this->assertEquals(basename($testPath), $file['name']);
        $this->assertEquals(dirname($testPath), $file['dir']);
        $this->assertEquals(true, $file['writeAccess']);
        $this->assertEquals('file', $file['type']);

        $response = $this->restCall('/kryn/admin/file?path=' . $testPath, 'DELETE');

        $this->assertEquals(true, $response['data']);
    }

    public function testMoveFile()
    {
        $id = dechex(time() / mt_rand(100, 500));
        $testPath = '/test_' . $id . '.txt';
        $response = $this->restCall('/kryn/admin/file?path=' . $testPath, 'POST');
        $this->assertEquals(true, $response['data']);

        $response = $this->restCall('/kryn/admin/file/single?path=' . $testPath);
        $file = $response['data'];

        $this->assertNotNull($file);
        $this->assertGreaterThan(0, $file['id']);
        $this->assertEquals($testPath, $file['path']);
        $this->assertEquals(basename($testPath), $file['name']);
        $this->assertEquals(dirname($testPath), $file['dir']);
        $this->assertEquals(true, $file['writeAccess']);
        $this->assertEquals('file', $file['type']);


        $id = dechex(time() / mt_rand(100, 500));
        $testDirPath = '/test_' . $id;
        $response = $this->restCall('/kryn/admin/file/folder?path=' . $testDirPath, 'POST');
        $this->assertEquals(true, $response['data']);

        $response = $this->restCall('/kryn/admin/file/paste?target=' . $testDirPath . '/', 'POST', [
            'files' => [$testPath],
            'move' => true
        ]);
        $this->assertEquals(true, $response['data']);

        $response = $this->restCall('/kryn/admin/file?path=' . $testDirPath);
        $this->assertcount(1, $response['data']);
        $file = $response['data'][0];
        $newPath = $testDirPath . '/' . basename($testPath);
        $this->assertEquals($newPath, $file['path']);
        $this->assertEquals(basename($testPath), $file['name']);
        $this->assertEquals($testDirPath, $file['dir']);

        $response = $this->restCall('/kryn/admin/file?path=' . $newPath, 'DELETE');
        $this->assertEquals(true, $response['data']);
        $response = $this->restCall('/kryn/admin/file?path=' . $testDirPath, 'DELETE');
        $this->assertEquals(true, $response['data']);
    }

    public function testCopyFile()
    {
        $id = dechex(time() / mt_rand(100, 500));
        $testPath = '/test_' . $id . '.txt';
        $response = $this->restCall('/kryn/admin/file?path=' . $testPath, 'POST');
        $this->assertEquals(true, $response['data']);

        $response = $this->restCall('/kryn/admin/file/single?path=' . $testPath);
        $file = $response['data'];

        $this->assertNotNull($file);
        $this->assertGreaterThan(0, $file['id']);
        $this->assertEquals($testPath, $file['path']);
        $this->assertEquals(basename($testPath), $file['name']);
        $this->assertEquals(dirname($testPath), $file['dir']);
        $this->assertEquals(true, $file['writeAccess']);
        $this->assertEquals('file', $file['type']);


        $id = dechex(time() / mt_rand(100, 500));
        $testDirPath = '/test_' . $id;
        $response = $this->restCall('/kryn/admin/file/folder?path=' . $testDirPath, 'POST');
        $this->assertEquals(true, $response['data']);

        $response = $this->restCall('/kryn/admin/file/paste?target=' . $testDirPath . '/', 'POST', [
            'files' => [$testPath],
            'move' => false
        ]);
        $this->assertEquals(true, $response['data']);

        $response = $this->restCall('/kryn/admin/file?path=' . $testDirPath);
        //copied
        $this->assertcount(1, $response['data']);
        $file = $response['data'][0];
        $newPath = $testDirPath . '/' . basename($testPath);
        $this->assertEquals($newPath, $file['path']);
        $this->assertEquals(basename($testPath), $file['name']);
        $this->assertEquals($testDirPath, $file['dir']);

        $response = $this->restCall('/kryn/admin/file/single?path=' . $testPath);
        //still there
        $file = $response['data'];
        $this->assertNotNull($file);
        $this->assertGreaterThan(0, $file['id']);
        $this->assertEquals($testPath, $file['path']);
        $this->assertEquals(basename($testPath), $file['name']);
        $this->assertEquals(dirname($testPath), $file['dir']);
        $this->assertEquals(true, $file['writeAccess']);
        $this->assertEquals('file', $file['type']);

        $response = $this->restCall('/kryn/admin/file?path=' . $testPath, 'DELETE');
        $this->assertEquals(true, $response['data']);
        $response = $this->restCall('/kryn/admin/file?path=' . $newPath, 'DELETE');
        $this->assertEquals(true, $response['data']);
        $response = $this->restCall('/kryn/admin/file?path=' . $testDirPath, 'DELETE');
        $this->assertEquals(true, $response['data']);
    }

}

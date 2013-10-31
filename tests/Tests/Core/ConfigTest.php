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

class ConfigTest extends TestCaseWithCore
{
    public function testBasics()
    {
        $this->assertCount(6, Kryn::getBundles());
        $this->assertCount(6, Kryn::getConfigs()->getConfigs());
        $this->assertCount(6, Kryn::getConfigs()->getConfigs());
    }

    public function testConfigs()
    {
        $config = Kryn::getConfigs();

        $this->assertCount(6, $config->getConfigs());

        foreach ($config->getConfigs() as $config) {
            $this->assertInstanceOf('Core\Config\Bundle', $config);
        }
    }

    public function testBundleConfigs()
    {
        foreach (Kryn::getBundles() as $bundle) {
            $bundleConfig = Kryn::getConfig($bundle);
            $this->assertInstanceOf('Core\Config\Bundle', $bundleConfig);
        }

        $bundleConfig = Kryn::getConfig('publication');
        $this->assertInstanceOf('Core\Config\Bundle', $bundleConfig);

        $this->assertEquals('PublicationBundle', $bundleConfig->getBundleName());
        $this->assertEquals('Publication', $bundleConfig->getName());
    }

    public function testBundle()
    {
        foreach (Kryn::getBundles() as $bundle) {
            $bundleConfig = Kryn::getBundle($bundle);
            $this->assertInstanceOf('Core\Bundle', $bundleConfig);
        }

        $bundleConfig = Kryn::getBundle('publication');
        $this->assertInstanceOf('Core\Bundle', $bundleConfig);

        $this->assertEquals('PublicationBundle', $bundleConfig->getName());
        $this->assertEquals('Publication', $bundleConfig->getName(true));
        $this->assertEquals('Publication\PublicationBundle', $bundleConfig->getClassName());
        $this->assertEquals('vendor/kryncms/publication-bundle/Publication/', $bundleConfig->getPath());
        $this->assertEquals('vendor/kryncms/publication-bundle/Publication/composer.json', $bundleConfig->getComposerPath());
        $this->assertEquals('Publication', $bundleConfig->getNamespace());
        $this->assertEquals('Publication', $bundleConfig->getRootNamespace());
        $this->assertInstanceOf('Core\Config\Bundle', $bundleConfig->getConfig());
    }
}
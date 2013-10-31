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

class SystemConfigTest extends TestCaseWithCore
{
    public function testSystemConfigTitle()
    {

        $xml = "<config>
  <!--The system title of this installation.-->
  <systemTitle>Peter's Kryn</systemTitle>
</config>";

        $config = new SystemConfig();
        $config->setSystemTitle('Peter\'s Kryn');
        $this->assertEquals($xml, $config->toXml());

        $reverse = new SystemConfig($xml);
        $this->assertEquals($xml, $reverse->toXml());

        $xmlAdditional = '<config asd="fgh">
  <!--The system title of this installation.-->
  <systemTitle>Peter\'s Kryn</systemTitle>
  <custom>fooobarr</custom>
  <otherValues>
    <item>peter</item>
    <item key="foo">hans</item>
  </otherValues>
</config>';

        $config = new SystemConfig($xmlAdditional);
        $config->setSystemTitle('Peter\'s Kryn');
        $this->assertEquals('fooobarr', $config->getAdditional('custom'));
        $this->assertEquals('fgh', $config->getAdditionalAttribute('asd'));
        $this->assertEquals(array('peter', 'foo' => 'hans'), $config->getAdditional('otherValues'));
        $this->assertEquals($xmlAdditional, $config->toXml());
    }

    public function testSystemConfigDb()
    {
        $xml = '<config>
  <database>
    <!--All tables will be prefixed with this string. Best practise is to suffix it with a underscore.
    Examples: dev_, domain_ or prod_-->
    <prefix>dev_</prefix>
    <connections>
      <!--
        type: mysql|pgsql|sqlite (the pdo driver name)
        persistent: true|false (if the connection should be persistent)
        slave: true|false (if the connection is a slave or not (readonly or not))
      -->
      <connection type="mysql">
        <!--The schema/database name-->
        <name>testdb</name>
        <username>peter</username>
      </connection>
    </connections>
  </database>
</config>';
        $config = new SystemConfig();

        $connection = new Connection();
        $connection->setUsername('peter');
        $connection->setType('mysql');
        $connection->setName('testdb');

        $database = new Database();
        $database->setPrefix('dev_');
        $config->setDatabase($database);
        $config->getDatabase()->addConnection($connection);

        $output = $config->toXml();
        $this->assertEquals($xml, $output);

        $reverse = new SystemConfig($xml);
        $this->assertInternalType('array', $reverse->getDatabase()->getConnections());
        $this->assertInstanceOf('\Core\Config\Connection', $reverse->getDatabase()->getConnections()[0]);
        $this->assertEquals('mysql', $reverse->getDatabase()->getConnections()[0]->getType());
        $this->assertEquals('peter', $reverse->getDatabase()->getConnections()[0]->getUsername());
        $this->assertEquals('testdb', $reverse->getDatabase()->getConnections()[0]->getName());
        $this->assertEquals($xml, $reverse->toXml());
    }

    public function testSystemConfigFile()
    {
        $xml = '<config>
  <!--
    Whenever Kryn creates files we try to set the correct permission and file owner.
    Attributes (default):
    groupPermission:    rw|r|empty (rw)
    everyonePermission: rw|r|empty (r)
    disableModeChange:  true|false (false)
    -->
  <file groupPermission="r" everyonePermission="">
    <!--The group owner name-->
    <groupOwner>ftp</groupOwner>
  </file>
</config>';
        $config3 = new SystemConfig();

        $filePermission = new FilePermission();
        $filePermission->setGroupPermission('r');
        $filePermission->setEveryonePermission('');
        $filePermission->setGroupOwner('ftp');
        $config3->setFile($filePermission);

        $this->assertEquals($xml, $config3->toXml());

        $reverse = new SystemConfig($xml);
        $this->assertFalse($reverse->getFile()->getDisableModeChange());
        $this->assertEquals('r', $reverse->getFile()->getGroupPermission());
        $this->assertEquals('', $reverse->getFile()->getEveryonePermission());
        $this->assertEquals('ftp', $reverse->getFile()->getGroupOwner());
        $this->assertEquals($xml, $reverse->toXml());

        $xml = '<config>
  <!--
    Whenever Kryn creates files we try to set the correct permission and file owner.
    Attributes (default):
    groupPermission:    rw|r|empty (rw)
    everyonePermission: rw|r|empty (r)
    disableModeChange:  true|false (false)
    -->
  <file disableModeChange="true"/>
</config>';
        $config4 = new SystemConfig();

        $filePermission = new FilePermission();
        $filePermission->setDisableModeChange(true);
        $config4->setFile($filePermission);

        $this->assertEquals($xml, $config4->toXml());

        $reverse = new SystemConfig($xml);
        $this->assertTrue($reverse->getFile()->getDisableModeChange());
        $this->assertEquals($xml, $reverse->toXml());
    }

    public function testSystemConfigCache()
    {
        $xml = '<config>
  <!--
  The cache layer we use for the distributed caching.
  (The `fast caching` is auto determined (Order: APC, XCache, Files))
  -->
  <cache>
    <!--The full classname of the storage. MUST have `Core\Cache\CacheInterface` as interface.-->
    <class>\Vendor\Other\CacheClass</class>
    <options>
      <option key="servers">
        <option>127.0.0.1</option>
        <option>192.168.0.1</option>
      </option>
      <option key="compression">true</option>
      <option key="foo">bar</option>
    </options>
  </cache>
</config>';
        $config5 = new SystemConfig();
        $cache = new Cache();
        $cache->setClass('\Vendor\Other\CacheClass');
        $cache->setOption('servers', array('127.0.0.1', '192.168.0.1'));
        $cache->setOption('compression', 'true');
        $cache->setOption('foo', 'bar');
        $config5->setCache($cache);

        $this->assertEquals(array('127.0.0.1', '192.168.0.1'), $config5->getCache()->getOption('servers'));

        $this->assertEquals($xml, $config5->toXml());

        $reverse = new SystemConfig($xml);
        $this->assertEquals(array('127.0.0.1', '192.168.0.1'), $reverse->getCache()->getOption('servers'));
        $this->assertEquals('true', $reverse->getCache()->getOption('compression'));
        $this->assertEquals($xml, $reverse->toXml());
    }

    public function testSystemConfigErrors()
    {
        $xml = '<config>
  <!--error handling-->
  <errors>
    <!--If the system should print error messages to the client. DEACTIVATE THIS IN PRODUCTIVE SYSTEMS!-->
    <display>true</display>
    <!--If the system should print error message from the RESTful JSON API to the client. DEACTIVATE THIS IN PRODUCTIVE SYSTEMS!-->
    <displayRest>true</displayRest>
    <!--
    If the system should print a prettified stackTrace with codeHighlighting in the error message.
    This included the stackTrace in the RESTful JSON API (displayRest).
    -->
    <stackTrace>true</stackTrace>
  </errors>
</config>';

        $config = new SystemConfig();
        $errors = new Errors();
        $config->setErrors($errors);
        $errors->setDisplay(true);
        $errors->setDisplayRest(true);
        $errors->setStackTrace(true);

        $this->assertEquals($xml, $config->toXml());

        $reverse = new SystemConfig($xml);
        $this->assertTrue($config->getErrors()->getDisplay());
        $this->assertTrue($config->getErrors()->getDisplayRest());
        $this->assertTrue($config->getErrors()->getStackTrace());
        $this->assertFalse($config->getErrors()->getLog());
        $this->assertEquals($xml, $reverse->toXml());
    }

    public function testSystemConfigBundles()
    {
        $xml = '<config>
  <!--
    A list of installed bundles. Enter here the PHP FQDN (Will be resolved through PSR-0 and then loaded)

    Example:
        <bundle>Publication\PublicationBundle</bundle>
    -->
  <bundles>
    <bundle>Publication\PublicationBundle</bundle>
    <bundle>KrynDemoThemeBundle</bundle>
  </bundles>
</config>';
        $config = new SystemConfig();
        $config->setBundles(array('Publication\PublicationBundle', 'KrynDemoThemeBundle'));
        $this->assertEquals($xml, $config->toXml());

        $reverse = new SystemConfig($xml);
        $this->assertEquals(array('Publication\PublicationBundle', 'KrynDemoThemeBundle'), $reverse->getBundles());
        $this->assertEquals($xml, $reverse->toXml());
    }

    public function testSystemConfigClient()
    {

        $xml = '<config>
  <!--The client session/authorisation/authentication handling.
  Attributes: (default)
    autoStart: true|false (false) If the systems starts always a session for each request and therefore sends for each
                                visitor/request a cookie (if none is delivered).
  -->
  <client>
    <class>Vendor\Custom\ClientHandling</class>
    <options>
      <option key="server">127.0.0.1</option>
      <option key="cert">false</option>
    </options>
    <!--A class that handles the actual data storage.-->
    <sessionStorage>
      <!--The full classname of the storage. MUST have `Core\Cache\CacheInterface` as interface.
      Define `database` for the database storage.-->
      <class>Vendor\MyOwn\Storage</class>
    </sessionStorage>
  </client>
</config>';

        $config = new SystemConfig();
        $client = new Client();
        $client->setClass('Vendor\Custom\ClientHandling');
        $client->setOption('server', '127.0.0.1');
        $client->setOption('cert', 'false');
        $config->setClient($client);
        $sessionStorage = new SessionStorage();
        $sessionStorage->setClass('Vendor\MyOwn\Storage');
        $client->setSessionStorage($sessionStorage);
        $this->assertEquals($xml, $config->toXml());

        $reverse = new SystemConfig($xml);
        $this->assertInstanceOf('Core\Config\Client', $reverse->getClient());
        $this->assertEquals('Vendor\Custom\ClientHandling', $reverse->getClient()->getClass());
        $this->assertEquals('127.0.0.1', $reverse->getClient()->getOption('server'));
        $this->assertEquals('false', $reverse->getClient()->getOption('cert'));
        $this->assertInstanceOf('Core\Config\SessionStorage', $reverse->getClient()->getSessionStorage());
        $this->assertEquals('Vendor\MyOwn\Storage', $reverse->getClient()->getSessionStorage()->getClass());
        $this->assertEquals($xml, $reverse->toXml());
    }

    public function testSystemConfigDefaultConfig()
    {
        $config = new SystemConfig();
        $config->setDebug(true);

        $database = new Database();
        $database->setPrefix('kryn_');
        $connection = new Connection();
        $connection->setType('mysql');
        $connection->setServer('127.0.0.1');
        $connection->setName('test');
        $connection->setUsername('root');
        $database->addConnection($connection);

        $file = new FilePermission();
        $file->setGroupPermission('rw');
        $file->setEveryonePermission('r');
        $file->setDisableModeChange(false);
        $file->setGroupOwner('www-data');

        $cache = new Cache();
        $cache->setClass('Core\Cache\Files');

        $client = new Client();
        $client->setClass('Core\Client\KrynUsers');
        $client->setOption('emailLogin', true);

        $sessionStorage = new SessionStorage();
        $sessionStorage->setClass('database');
        $client->setSessionStorage($sessionStorage);

        $errors = new Errors();
        $errors->setDisplay(true);
        $errors->setLog(true);
        $errors->setDisplayRest(true);
        $errors->setStackTrace(true);

        $config->setSystemTitle('Fresh Installation');
        $config->setTimezone('Europe/Berlin');
        $config->setBundles(array('Publication\PublicationBundle', 'KrynDemoThemeBundle'));

        $config->setDatabase($database);
        $config->setFile($file);
        $config->setErrors($errors);
        $config->setCache($cache);
        $config->setClient($client);

        $distConfig = file_get_contents('app/config/config.dist.xml');

        $this->assertEquals($distConfig, $config->toXml(true));

        $reverse = new SystemConfig($distConfig);

        $this->assertEquals('kryn_', $reverse->getDatabase()->getPrefix());
        $firstConnection = $reverse->getDatabase()->getConnections()[0];
        $this->assertInstanceOf('Core\Config\Connection', $firstConnection);
        $this->assertEquals('mysql', $firstConnection->getType());
        $this->assertEquals('root', $firstConnection->getUsername());
        $this->assertEquals('', $firstConnection->getPassword());
        $this->assertEquals('test', $firstConnection->getName());

        $this->assertEquals('rw', $reverse->getFile()->getGroupPermission());
        $this->assertEquals('r', $reverse->getFile()->getEveryonePermission());
        $this->assertEquals('www-data', $reverse->getFile()->getGroupOwner());
        $this->assertFalse($reverse->getFile()->getDisableModeChange());

        $this->assertEquals('Core\Cache\Files', $reverse->getCache()->getClass());

        $this->assertEquals('Core\Client\KrynUsers', $reverse->getClient()->getClass());
        $this->assertEquals('true', $reverse->getClient()->getOption('emailLogin'));

        $this->assertEquals('database', $reverse->getClient()->getSessionStorage()->getClass());

        $this->assertTrue($reverse->getErrors()->getDisplay());
        $this->assertTrue($reverse->getErrors()->getLog());
        $this->assertTrue($reverse->getErrors()->getDisplayRest());
        $this->assertTrue($reverse->getErrors()->getStackTrace());

        $this->assertEquals('Fresh Installation', $reverse->getSystemTitle());
        $this->assertEquals('Europe/Berlin', $reverse->getTimezone());
        $this->assertEquals(array('Publication\PublicationBundle', 'KrynDemoThemeBundle'), $reverse->getBundles());
    }

}
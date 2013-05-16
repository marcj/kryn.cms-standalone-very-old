<?php

namespace Tests\Core;

use Core\Config\Database;
use Core\Config\SystemConfig;
use Core\Config\Connection;
use Tests\TestCaseWithCore;

class ConfigTest extends TestCaseWithCore
{
    public function testSystemConfig()
    {

        $xml = "<config>
  <systemTitle>Peter's Kryn</systemTitle>
</config>";

        $config = new SystemConfig($xml);
        $output = $config->toXml();
        $this->assertEquals($xml, $output);


        $xmlAdditional = '<config asd="fgh">
  <systemTitle>Peter\'s Kryn</systemTitle>
  <custom>fooobarr</custom>
</config>';

        $config = new SystemConfig($xmlAdditional);
        $config->setSystemTitle('Peter\'s Kryn');
        $output = $config->toXml();
        $this->assertEquals($xmlAdditional, $output);

        $xml = '<config>
  <database>
    <!--All tables will be prefixed with this string. Best practise is to suffix it with a underscore.
    Examples: dev_, domain_ or prod_-->
    <prefix>dev_</prefix>
    <connections>
      <!--
        type: mysql|pgsql|sqlite (the pdo driver name)
        persistent: true|false (if the connection should be persistent)-->
      <connection type="mysql">
        <username>peter</username>
        <!--The schema/database name-->
        <name>testdb</name>
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

        $config2 = new SystemConfig($xml);
        $this->assertEquals($xml, $config2->toXml());

    }
}
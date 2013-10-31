<?php

namespace Tests\Core;

use Core\Config\Bundle;
use Core\Config\Cache;
use Core\Config\Client;
use Core\Config\Database;
use Core\Config\EntryPoint;
use Core\Config\Errors;
use Core\Config\Field;
use Core\Config\FilePermission;
use Core\Config\Object;
use Core\Config\Plugin;
use Core\Config\Route;
use Core\Config\RouteDefault;
use Core\Config\RouteRequirement;
use Core\Config\SessionStorage;
use Core\Config\SystemConfig;
use Core\Config\Connection;
use Core\Config\Theme;
use Core\Config\ThemeContent;
use Core\Config\ThemeLayout;
use Tests\TestCaseWithCore;

class PluginTest extends TestCaseWithCore
{
    private $xml = '<plugin id="listing">
  <label>News Listing</label>
  <class>Publication\Controller\Plugin\News</class>
  <method>listing</method>
  <routes>
    <route pattern="{page}">
      <default key="page">1</default>
      <requirement key="page">\d</requirement>
    </route>
    <route pattern="{slug}">
      <requirement key="page">[^/]+</requirement>
    </route>
  </routes>
  <options>
    <field id="template" type="view">
      <label>Template</label>
      <options>
        <option key="directory">@PublicationBundle/news/list/</option>
      </options>
    </field>
    <field id="itemsPerPage" type="number">
      <label>Items per page</label>
      <default>10</default>
    </field>
    <field id="detailPage" type="object">
      <label>Detail page</label>
      <object>Core\Node</object>
    </field>
  </options>
</plugin>';
    public function testPluginConfig()
    {
        $plugin = new Plugin($this->xml);
        $this->valueTest($plugin);
    }

    public function valueTest(Plugin $plugin)
    {
        $this->assertEquals('listing', $plugin->getId());
        $this->assertEquals('News Listing', $plugin->getLabel());
        $this->assertEquals('Publication\Controller\Plugin\News', $plugin->getClass());
        $this->assertEquals('listing', $plugin->getMethod());

        $this->assertInstanceOf('\Core\Config\Route', $plugin->getRoutes()[0]);
        $this->assertInstanceOf('\Core\Config\Route', $plugin->getRoutes()[1]);

        $this->assertEquals('1', $plugin->getRoutes()[0]->getDefaultValue('page'));

        $this->assertEquals('Items per page', $plugin->getOption('itemsPerPage')->getLabel());
        $this->assertEquals('@PublicationBundle/news/list/', $plugin->getOption('template')->getOption('directory'));

        $array = $plugin->toArray();

        $this->assertEquals('listing', $array['id']);
        $this->assertEquals('News Listing', $array['label']);
        $this->assertEquals('Publication\Controller\Plugin\News', $array['class']);
        $this->assertEquals('listing', $array['method']);

        $this->assertCount(2, $array['routes']);
        $this->assertCount(3, $array['options']);

        $this->assertEquals([
            'pattern' => '{page}',
            'defaults' => [
                'page' => 1
            ],
            'requirements' => [
                'page' => '\\d'
            ]
        ], $array['routes'][0]);

        $this->assertEquals('Items per page', $array['options']['itemsPerPage']['label']);

        $this->assertEquals($this->xml, $plugin->toXml());
    }

    public function testPluginConfigPhp()
    {
        $plugin = new Plugin();
        $plugin->setId('listing');
        $plugin->setLabel('News Listing');
        $plugin->setClass('Publication\Controller\Plugin\News');
        $plugin->setMethod('listing');

        $route1 = new Route();
        $route1->setPattern('{page}');

        $route1->addDefault(new RouteDefault(['key' => 'page', 'value' => 1]));
        $route1->addRequirement(new RouteRequirement(['key' => 'page', 'value' => '\\d']));

        $plugin->addRoute($route1);

        $route2 = new Route();
        $route2->setPattern('{slug}');
        $route2->addRequirement(new RouteRequirement(['key' => 'page', 'value' => '[^/]+']));

        $plugin->addRoute($route2);

        $field1 = new Field();
        $field1->setId('template');
        $field1->setType('view');
        $field1->setLabel('Template');
        $field1->setOption('directory', '@PublicationBundle/news/list/');

        $field2 = new Field();
        $field2->setId('itemsPerPage');
        $field2->setType('number');
        $field2->setLabel('Items per page');
        $field2->setDefault(10);

        $field3 = new Field();
        $field3->setId('detailPage');
        $field3->setType('object');
        $field3->setLabel('Detail page');
        $field3->setObject('Core\Node');

        $plugin->addOption($field1);
        $plugin->addOption($field2);
        $plugin->addOption($field3);

        $this->valueTest($plugin);
    }


    public function testPluginConfigArray()
    {
        $pluginArray = array (
            'id' => 'listing',
            'label' => 'News Listing',
            'class' => 'Publication\\Controller\\Plugin\\News',
            'method' => 'listing',
            'routes' => array (
                array (
                    'pattern' => '{page}',
                    'defaults' => array (
                        'page' => 1,
                    ),
                    'requirements' => array (
                        'page' => '\\d',
                    ),
                ),
                array (
                    'pattern' => '{slug}',
                    'requirements' => array (
                        'page' => '[^/]+',
                    ),
                ),
            ),
            'options' => array (
                'template' => array (
                    'id' => 'template',
                    'label' => 'Template',
                    'type' => 'view',
                    'options' => array (
                        'directory' => '@PublicationBundle/news/list/',
                    ),
                ),
                'itemsPerPage' => array (
                    'id' => 'itemsPerPage',
                    'label' => 'Items per page',
                    'type' => 'number',
                    'default' => 10,
                ),
                'detailPage' => array (
                    'id' => 'detailPage',
                    'label' => 'Detail page',
                    'type' => 'object',
                    'object' => 'Core\\Node',
                ),
            ),
        );

        $plugin = new Plugin($pluginArray);
        $this->assertEquals($pluginArray, $plugin->toArray());
        $this->valueTest($plugin);
    }
}
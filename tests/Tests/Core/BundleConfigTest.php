<?php

namespace Tests\Core;

use Core\Config\Bundle;
use Core\Config\BundleCache;
use Core\Config\Cache;
use Core\Config\Client;
use Core\Config\Configs;
use Core\Config\Database;
use Core\Config\EntryPoint;
use Core\Config\Errors;
use Core\Config\Event;
use Core\Config\Field;
use Core\Config\FilePermission;
use Core\Config\Object;
use Core\Config\Plugin;
use Core\Config\SessionStorage;
use Core\Config\SystemConfig;
use Core\Config\Connection;
use Core\Config\Theme;
use Core\Config\ThemeContent;
use Core\Config\ThemeLayout;
use Core\Config\TreeIconMapping;
use Tests\Core\FileImportTest\BundleTestBundle;
use Tests\TestCaseWithCore;

class BundleConfigTest extends TestCaseWithCore
{

    private static $krynXml = 'tests/Tests/Core/FileImportTest/Resources/config/kryn.xml';
    private static $krynObjectsXml = 'tests/Tests/Core/FileImportTest/Resources/config/kryn.objects.xml';

    public function setUp()
    {

        @unlink(static::$krynXml);
        @unlink(static::$krynObjectsXml);
    }

    public function testFileImport()
    {
        $configs = new Configs();
        $testBundle = new BundleTestBundle();

        copy(static::$krynXml. '.dist', static::$krynXml);
        copy(static::$krynObjectsXml. '.dist', static::$krynObjectsXml);

        $configStrings = $testBundle->getConfigs();
        $configObjects = $configs->parseConfig($configStrings);

        $testBundleConfig = $configObjects['BundleTestBundle'];
        $this->assertNotNull($testBundleConfig);
        $testBundleConfig->setBundleClass($testBundle);

        $this->assertEquals(static::$krynXml, $testBundleConfig->getPropertyFilePath('caches'));
        $this->assertEquals(static::$krynObjectsXml, $testBundleConfig->getPropertyFilePath('objects'));

        unlink(static::$krynXml);
        unlink(static::$krynObjectsXml);
    }

    public function testFileImportSaveObjects()
    {
        $configs = new Configs();
        $testBundle = new BundleTestBundle();

        copy(static::$krynXml. '.dist', static::$krynXml);
        copy(static::$krynObjectsXml. '.dist', static::$krynObjectsXml);

        $configStrings = $testBundle->getConfigs();
        $configObjects = $configs->parseConfig($configStrings);

        $testBundleConfig = $configObjects['BundleTestBundle'];
        $this->assertNotNull($testBundleConfig);
        $testBundleConfig->setBundleClass($testBundle);

        $export = $testBundleConfig->exportFileBased('objects');

        $this->assertStringEqualsFile(static::$krynObjectsXml, $export, 'no changes');

        $objects = $testBundleConfig->getObjects();
        $objects[0]->setId('Test2');
        $testBundleConfig->setObjects($objects);

        $testBundleConfig->saveFileBased('objects');

        $xml = '<config>
  <bundle>
    <objects>
      <object id="Test2">
        <label>Test</label>
        <class>Core\Models\Test</class>
        <dataModel>custom</dataModel>
        <fields>
          <field id="id" type="number" primaryKey="true">
            <label>ID</label>
          </field>
          <field id="name" type="text">
            <label>Name</label>
          </field>
        </fields>
      </object>
    </objects>
  </bundle>
</config>';

        $this->assertEquals(static::$krynObjectsXml, $testBundleConfig->getPropertyFilePath('objects'));
        $this->assertStringEqualsFile($testBundleConfig->getPropertyFilePath('objects'), $xml);

        unlink(static::$krynXml);
        unlink(static::$krynObjectsXml);
    }

    public function testFileImportSaveMixed()
    {
        $configs = new Configs();
        $testBundle = new BundleTestBundle();

        copy(static::$krynXml. '.dist', static::$krynXml);
        copy(static::$krynObjectsXml. '.dist', static::$krynObjectsXml);

        $configStrings = $testBundle->getConfigs();
        $configObjects = $configs->parseConfig($configStrings);

        $testBundleConfig = $configObjects['BundleTestBundle'];
        $this->assertNotNull($testBundleConfig);
        $testBundleConfig->setBundleClass($testBundle);

        $export = $testBundleConfig->exportFileBased('objects');
        $exportCaches = $testBundleConfig->exportFileBased('caches');

        $this->assertStringEqualsFile(static::$krynObjectsXml, $export, 'no changes');
        $this->assertStringEqualsFile(static::$krynXml, $exportCaches, 'no changes');

        $objects = $testBundleConfig->getObjects();
        $objects[0]->setId('Test2');
        $testBundleConfig->setObjects($objects);

        $caches = $testBundleConfig->getCaches();
        $caches[1]->setMethod('testMethod2');
        $testBundleConfig->setCaches($caches);

        $events = $testBundleConfig->getEvents();
        $events[1]->setKey('core/object/updateModified');
        $testBundleConfig->setEvents($events);

        $testBundleConfig->saveFileBased('objects');

        $xml = '<config>
  <bundle>
    <objects>
      <object id="Test2">
        <label>Test</label>
        <class>Core\Models\Test</class>
        <dataModel>custom</dataModel>
        <fields>
          <field id="id" type="number" primaryKey="true">
            <label>ID</label>
          </field>
          <field id="name" type="text">
            <label>Name</label>
          </field>
        </fields>
      </object>
    </objects>
  </bundle>
</config>';

        $this->assertEquals(static::$krynObjectsXml, $testBundleConfig->getPropertyFilePath('objects'));
        $this->assertStringEqualsFile($testBundleConfig->getPropertyFilePath('objects'), $xml);

        $this->assertEquals(static::$krynXml, $testBundleConfig->getPropertyFilePath('caches'));
        $this->assertEquals(static::$krynXml, $testBundleConfig->getPropertyFilePath('events'));

        $testBundleConfig->saveFileBased('caches');

        $xmlCaches = '<config>
  <bundle>
    <caches>
      <cache>core/contents</cache>
      <cache method="testMethod2">core/contents2</cache>
    </caches>
    <events>
      <event key="core/object/modify">
        <desc>Fires on every object modification (add/delete/update). Subject is the normalized object key.</desc>
      </event>
      <event key="core/object/update">
        <desc>Fires on every object update. Subject is the normalized object key.</desc>
      </event>
    </events>
    <listeners>
      <event key="core/object/modify" subject="core:domain">
        <clearCache>core/domains.created</clearCache>
        <clearCache>core/domains</clearCache>
      </event>
      <event key="core/object/modify" subject="core:content">
        <clearCache>core/contents</clearCache>
      </event>
      <event key="core/object/modify" subject="core:node">
        <clearCache>core/contents</clearCache>
      </event>
    </listeners>
  </bundle>
</config>';
        $this->assertStringEqualsFile($testBundleConfig->getPropertyFilePath('caches'), $xmlCaches);

        $testBundleConfig->saveFileBased('events');

        $xmlEvents = '<config>
  <bundle>
    <caches>
      <cache>core/contents</cache>
      <cache method="testMethod2">core/contents2</cache>
    </caches>
    <events>
      <event key="core/object/modify">
        <desc>Fires on every object modification (add/delete/update). Subject is the normalized object key.</desc>
      </event>
      <event key="core/object/updateModified">
        <desc>Fires on every object update. Subject is the normalized object key.</desc>
      </event>
    </events>
    <listeners>
      <event key="core/object/modify" subject="core:domain">
        <clearCache>core/domains.created</clearCache>
        <clearCache>core/domains</clearCache>
      </event>
      <event key="core/object/modify" subject="core:content">
        <clearCache>core/contents</clearCache>
      </event>
      <event key="core/object/modify" subject="core:node">
        <clearCache>core/contents</clearCache>
      </event>
    </listeners>
  </bundle>
</config>';

        $this->assertStringEqualsFile($testBundleConfig->getPropertyFilePath('events'), $xmlEvents);

        $configStrings = $testBundle->getConfigs();
        $configObjects = $configs->parseConfig($configStrings);

        $testBundleConfig = $configObjects['BundleTestBundle'];
        $this->assertNotNull($testBundleConfig);
        $testBundleConfig->setBundleClass($testBundle);

        $this->assertCount(1, $testBundleConfig->getObjects());
        $this->assertCount(2, $testBundleConfig->getCaches());
        $this->assertCount(2, $testBundleConfig->getEvents());

        $this->assertEquals('Test2', $testBundleConfig->getObjects()[0]->getId());
        $this->assertEquals('testMethod2', $testBundleConfig->getCaches()[1]->getMethod());
        $this->assertEquals('core/object/updateModified', $testBundleConfig->getEvents()[1]->getKey());

        unlink(static::$krynXml);
        unlink(static::$krynObjectsXml);
    }

    public function testBundle()
    {
        $config = new Bundle();

        $events = [
            ['key' => 'core/object/modify', 'desc' => 'foo'],
            ['key' => 'core/object/update', 'desc' => 'bar']
        ];

        foreach ($events as $item) {
            $items[] = new Event($item);
        }
        $config->setEvents($items);
        $config->setBundleName('FooBar');

        $caches = [
            new BundleCache([
                'key' => 'foo',
                'method' => 'bar'
            ]),
            new BundleCache([
                'key' => 'foo2'
            ])
        ];

        $config->setCaches($caches);

        $array = array (
            'name' => 'FooBar',
            'class' => 'Core\Config\Bundle',
            'caches' => array (
                array (
                    'key' => 'foo',
                    'method' => 'bar',
                ),
                array (
                    'key' => 'foo2',
                ),
            ),
            'events' => array (
                array (
                    'key' => 'core/object/modify',
                    'desc' => 'foo',
                ),
                array (
                    'key' => 'core/object/update',
                    'desc' => 'bar',
                ),
            ),
        );

        $xml = '<bundle>
  <caches>
    <cache method="bar">foo</cache>
    <cache>foo2</cache>
  </caches>
  <events>
    <event key="core/object/modify">
      <desc>foo</desc>
    </event>
    <event key="core/object/update">
      <desc>bar</desc>
    </event>
  </events>
</bundle>';

        $this->assertEquals($array, $config->toArray());
        $this->assertEquals($xml, $config->toXml());
    }


    public function testTheme()
    {
        $xml = '<theme id="krynDemoTheme">
  <label>Kryn.cms Demo Theme</label>
  <contents>
    <content>
      <label>Default</label>
      <file>@KrynDemoThemeBundle/content_default.tpl</file>
    </content>
    <content>
      <label>Sidebar Item</label>
      <file>@KrynDemoThemeBundle/content_sidebar.tpl</file>
    </content>
  </contents>
  <layouts>
    <layout>
      <label>Default</label>
      <file>@KrynDemoThemeBundle/layout_default.tpl</file>
    </layout>
  </layouts>
</theme>';

        $theme = new Theme($xml);
        $theme->setId('krynDemoTheme');
        $theme->setLabel('Kryn.cms Demo Theme');

        $content = new ThemeContent();
        $content->setFile('@KrynDemoThemeBundle/content_default.tpl');
        $content->setLabel('Default');
        $content2 = new ThemeContent();
        $content2->setFile('@KrynDemoThemeBundle/content_sidebar.tpl');
        $content2->setLabel('Sidebar Item');
        $theme->setContents(array($content, $content2));

        $layout = new ThemeLayout();
        $layout->setFile('@KrynDemoThemeBundle/layout_default.tpl');
        $layout->setLabel('Default');
        $theme->setLayouts(array($layout));

        $this->assertEquals($xml, $theme->toXml());

        $reverse = new Theme($xml);
        $this->assertEquals('krynDemoTheme', $reverse->getId());
        $this->assertEquals('Kryn.cms Demo Theme', $reverse->getLabel());

        $this->assertEquals('Default', $reverse->getContents()[0]->getLabel());
        $this->assertEquals('@KrynDemoThemeBundle/content_default.tpl', $reverse->getContents()[0]->getFile());

        $this->assertEquals('Default', $reverse->getLayouts()[0]->getLabel());
        $this->assertEquals('@KrynDemoThemeBundle/layout_default.tpl', $reverse->getLayouts()[0]->getFile());

        $this->assertEquals($xml, $reverse->toXml());
    }

    public function testTheme2()
    {
        $xml = '<theme id="krynDemoTheme">
  <label>Kryn.cms Demo Theme</label>
</theme>';

        $theme = new Theme($xml);
        $theme->setId('krynDemoTheme');
        $theme->setLabel('Kryn.cms Demo Theme');
        $this->assertEquals($xml, $theme->toXml());

        $reverse = new Theme($xml);
        $this->assertEquals('krynDemoTheme', $reverse->getId());
        $this->assertEquals('Kryn.cms Demo Theme', $reverse->getLabel());

        $this->assertEquals($xml, $reverse->toXml());
    }

    public function testObjectSmall()
    {
        $xml = '<object id="View">
  <label>Template View</label>
  <desc>Template views</desc>
  <class>\Admin\ObjectView</class>
  <labelField>name</labelField>
  <dataModel>custom</dataModel>
  <nested>true</nested>
  <treeIconMapping>
    <icon id="dir">#icon-folder-4</icon>
  </treeIconMapping>
  <fields>
    <field id="path" type="text" primaryKey="true">
      <label>Path</label>
    </field>
    <field id="name" type="text">
      <label>File name</label>
    </field>
  </fields>
</object>';

        $arrayObject = new Object(array (
            'id' => 'View',
            'label' => 'Template View',
            'desc' => 'Template views',
            'class' => '\\Admin\\ObjectView',
            'labelField' => 'name',
            'dataModel' => 'custom',
            'nested' => true,
            'treeIconMapping' => array (
                'dir' => '#icon-folder-4',
            ),
            'fields' => array (
                'path' => array (
                    'id' => 'path',
                    'label' => 'Path',
                    'type' => 'text',
                    'primaryKey' => true,
                ),
                'name' => array (
                    'id' => 'name',
                    'label' => 'File name',
                    'type' => 'text',
                ),
            ),
        ));

        $xmlObject = new Object($xml);

        $object = new Object();
        $object->setId('View');
        $object->setLabel('Template View');
        $object->setDesc('Template views');
        $object->setLabelField('name');
        $object->setDataModel('custom');
        $object->setNested(true);
        $object->setClass('\Admin\ObjectView');

        $treeIconMapping = new TreeIconMapping();
        $treeIconMapping->setOption('dir', '#icon-folder-4');
        $object->setTreeIconMapping($treeIconMapping);

        $field1 = new Field();
        $field1->setId('path');
        $field1->setPrimaryKey(true);
        $field1->setLabel('Path');
        $field1->setType('text');

        $field2 = new Field();
        $field2->setId('name');
        $field2->setLabel('File name');
        $field2->setType('text');

        $object->setFields(array($field1, $field2));


        $this->assertEquals($xml, $object->toXml());
        $this->assertEquals($xmlObject->toXml(), $object->toXml());
        $this->assertEquals($xml, $xmlObject->toXml());

        $this->assertEquals($xmlObject->toArray(), $object->toArray());
        $this->assertEquals($xmlObject->toArray(), $arrayObject->toArray());
        $this->assertEquals($xmlObject->toXml(), $arrayObject->toXml());
        $this->assertEquals($xml, $arrayObject->toXml());
    }

    public function testObjectBrowserColumns()
    {
        $xml = '<object id="View">
  <label>Template View</label>
  <desc>Template views</desc>
  <class>\Admin\ObjectView</class>
  <labelField>name</labelField>
  <dataModel>custom</dataModel>
  <nested>true</nested>
  <fields>
    <field id="path" type="text" primaryKey="true">
      <label>Path</label>
    </field>
    <field id="name" type="text">
      <label>File name</label>
    </field>
  </fields>
  <browserColumns>
    <field id="path" type="text">
      <label>Path</label>
    </field>
    <field id="name" type="text">
      <label>File name</label>
    </field>
  </browserColumns>
</object>';

        $object = new Object();
        $object->setId('View');
        $object->setLabel('Template View');
        $object->setDesc('Template views');
        $object->setLabelField('name');
        $object->setDataModel('custom');
        $object->setNested(true);
        $object->setClass('\Admin\ObjectView');

        $field1 = new Field();
        $field1->setId('path');
        $field1->setPrimaryKey(true);
        $field1->setLabel('Path');
        $field1->setType('text');

        $field2 = new Field();
        $field2->setId('name');
        $field2->setLabel('File name');
        $field2->setType('text');

        $object->setFields(array($field1, $field2));

        $field1 = new Field();
        $field1->setId('path');
        $field1->setLabel('Path');
        $field1->setType('text');

        $field2 = new Field();
        $field2->setId('name');
        $field2->setLabel('File name');
        $field2->setType('text');

        $object->setBrowserColumns(array($field1, $field2));

        $reverse = new Object($xml);

        $this->assertEquals($xml, $object->toXml());
        $this->assertEquals($xml, $reverse->toXml());
    }

    public function testObjectItemArray()
    {
        $xml ='
<object id="Item">
  <label>title</label>
  <table>test_item</table>
  <labelField>title</labelField>
  <nested>false</nested>
  <dataModel>propel</dataModel>
  <multiLanguage>false</multiLanguage>
  <workspace>true</workspace>
  <domainDepended>false</domainDepended>
  <treeFixedIcon>false</treeFixedIcon>
  <fields>
    <field id="id" type="number" primaryKey="true" autoIncrement="true">
    </field>
    <field id="title" type="text">
    </field>
    <field id="category" type="object">
      <object>Test\ItemCategory</object>
      <objectRelation>nToM</objectRelation>
    </field>
    <field id="oneCategory" type="object">
      <object>Test\ItemCategory</object>
      <objectRelation>nTo1</objectRelation>
    </field>
  </fields>
</object>';

        $object = new Object($xml);
        $array = $object->toArray();

        $this->assertEquals('Item', $object->getId());
        $this->assertEquals('title', $object->getLabel());
        $this->assertEquals('test_item', $object->getTable());
        $this->assertTrue($object->getWorkspace());
        $this->assertCount(4, $object->getFields());

        $this->assertEquals('Item', $array['id']);
        $this->assertEquals('title', $array['label']);
        $this->assertEquals('test_item', $array['table']);
        $this->assertTrue($array['workspace']);
        $this->assertCount(4, $array['fields']);
    }

    public function testObjectFromArray()
    {
        $GLOBALS['test'] = 1;

        $entryPointsArray = array (
            0 =>
            array (
                'path' => 'backend',
                'label' => 'Backend access',
                'children' =>
                array (
                    0 =>
                    array (
                        'path' => 'chooser',
                        'type' => 'custom',
                        'label' => 'Chooser',
                        'fullPath' => 'backend/chooser',
                        'title' => 'Chooser',
                        'id' => 'chooser',
                    ),
                    1 =>
                    array (
                        'path' => 'stores',
                        'label' => 'Stores',
                        'children' =>
                        array (
                            0 =>
                            array (
                                'path' => 'languages',
                                'type' => 'store',
                                'label' => 'Language',
                                'fullPath' => 'backend/stores/languages',
                                'title' => 'Language',
                                'id' => 'languages',
                            ),
                            1 =>
                            array (
                                'path' => 'extensions',
                                'type' => 'store',
                                'class' => 'adminStoreExtensions',
                                'label' => 'Extensions',
                                'fullPath' => 'backend/stores/extensions',
                                'title' => 'Extensions',
                                'id' => 'extensions',
                            ),
                        ),
                        'fullPath' => 'backend/stores',
                        'type' => 'acl',
                        'title' => 'Stores',
                        'id' => 'stores',
                    ),
                ),
                'fullPath' => 'backend',
                'type' => 'acl',
                'title' => 'Backend access',
                'id' => 'backend',
            ),
            1 =>
            array (
                'path' => 'dashboard',
                'type' => 'custom',
                'icon' => '#icon-chart-5',
                'link' => 'true',
                'label' => 'Dashboard',
                'fullPath' => 'dashboard',
                'title' => 'Dashboard',
                'id' => 'dashboard',
            ),
            2 =>
            array (
                'path' => 'nodes',
                'type' => 'combine',
                'class' => 'Admin\\Controller\\Windows\\NodeCrud',
                'icon' => '#icon-screen-2',
                'link' => 'true',
                'label' => 'Pages',
                'multi' => 'true',
                'children' =>
                array (
                    0 =>
                    array (
                        'path' => 'add',
                        'type' => 'custom',
                        'label' => 'Add pages',
                        'multi' => 'true',
                        'fullPath' => 'nodes/add',
                        'title' => 'Add pages',
                        'id' => 'add',
                    ),
                    1 =>
                    array (
                        'path' => 'addDomains',
                        'type' => 'custom',
                        'label' => 'Add domains',
                        'multi' => 'true',
                        'fullPath' => 'nodes/addDomains',
                        'title' => 'Add domains',
                        'id' => 'addDomains',
                    ),
                    2 =>
                    array (
                        'path' => 'root',
                        'type' => 'combine',
                        'class' => '\\Admin\\Controller\\Windows\\DomainCrud',
                        'label' => 'Domain',
                        'fullPath' => 'nodes/root',
                        'title' => 'Domain',
                        'id' => 'root',
                    ),
                    3 =>
                    array (
                        'path' => 'frontend',
                        'type' => 'custom',
                        'label' => 'Frontend',
                        'fullPath' => 'nodes/frontend',
                        'title' => 'Frontend',
                        'id' => 'frontend',
                    ),
                ),
                'fullPath' => 'nodes',
                'title' => 'Pages',
                'id' => 'nodes',
            )
        );

        $entryPoints = [];
        foreach ($entryPointsArray as $entryPointArray) {
            $entryPoint = new EntryPoint();
            $entryPoint->fromArray($entryPointArray);
            $entryPoints[] = $entryPoint;
        }

        $xmlBackend = '<entryPoint path="backend">
  <label>Backend access</label>
  <children>
    <entryPoint path="chooser" type="custom">
      <label>Chooser</label>
    </entryPoint>
    <entryPoint path="stores">
      <label>Stores</label>
      <children>
        <entryPoint path="languages" type="store">
          <label>Language</label>
        </entryPoint>
        <entryPoint path="extensions" type="store">
          <class>adminStoreExtensions</class>
          <label>Extensions</label>
        </entryPoint>
      </children>
    </entryPoint>
  </children>
</entryPoint>';

        $xmlDashboard = '<entryPoint path="dashboard" type="custom" icon="#icon-chart-5" link="true">
  <label>Dashboard</label>
</entryPoint>';

        $xmlNodes = '<entryPoint path="nodes" type="combine" icon="#icon-screen-2" link="true" multi="true">
  <class>Admin\Controller\Windows\NodeCrud</class>
  <label>Pages</label>
  <children>
    <entryPoint path="add" type="custom" multi="true">
      <label>Add pages</label>
    </entryPoint>
    <entryPoint path="addDomains" type="custom" multi="true">
      <label>Add domains</label>
    </entryPoint>
    <entryPoint path="root" type="combine">
      <class>\Admin\Controller\Windows\DomainCrud</class>
      <label>Domain</label>
    </entryPoint>
    <entryPoint path="frontend" type="custom">
      <label>Frontend</label>
    </entryPoint>
  </children>
</entryPoint>';

        $this->assertEquals($xmlBackend, $entryPoints[0]->toXml());
        $this->assertEquals($xmlDashboard, $entryPoints[1]->toXml());
        $this->assertEquals($xmlNodes, $entryPoints[2]->toXml());
    }

    public function testOptions()
    {

        $xml = '<object id="File">
  <label>File</label>
  <class>Admin\Models\ObjectFile</class>
  <dataModel>custom</dataModel>
  <table>system_file</table>
  <labelField>path</labelField>
  <nested>true</nested>
  <treeLabel>name</treeLabel>
  <treeIcon>type</treeIcon>
  <treeIconMapping>
    <icon id="dir">#icon-folder-4</icon>
  </treeIconMapping>
  <treeDefaultIcon>#icon-paper</treeDefaultIcon>
  <browserInterfaceClass>ka.Files</browserInterfaceClass>
  <browserInterface>custom</browserInterface>
</object>';

        $object = new Object($xml);

        $this->assertEquals(['dir' => '#icon-folder-4'], $object->toArray()['treeIconMapping']);
    }
}
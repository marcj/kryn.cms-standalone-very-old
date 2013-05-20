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
use Core\Config\SessionStorage;
use Core\Config\SystemConfig;
use Core\Config\Connection;
use Core\Config\Theme;
use Core\Config\ThemeContent;
use Core\Config\ThemeLayout;
use Tests\TestCaseWithCore;

class BundleConfigTest extends TestCaseWithCore
{
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
  <fields>
    <field id="path" primaryKey="true">
      <label>Path</label>
      <type>text</type>
    </field>
    <field id="name">
      <label>File name</label>
      <type>text</type>
    </field>
  </fields>
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

        $this->assertEquals($xml, $object->toXml());
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
    <field id="id" primaryKey="true" autoIncrement="true">
      <type>number</type>
    </field>
    <field id="title">
      <type>text</type>
    </field>
    <field id="category">
      <type>object</type>
      <object>Test\ItemCategory</object>
      <objectRelation>nToM</objectRelation>
    </field>
    <field id="oneCategory">
      <type>object</type>
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

        $xmlBackend = '<entryPoint path="backend" type="acl">
  <label>Backend access</label>
  <children>
    <entryPoint path="chooser" type="custom">
      <label>Chooser</label>
    </entryPoint>
    <entryPoint path="stores" type="acl">
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
}
<?php

namespace Tests\Core;

use Core\Config\Cache;
use Core\Config\Client;
use Core\Config\Database;
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
}
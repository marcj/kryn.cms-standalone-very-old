<?php

namespace Admin\Admin;
 
class NodeCrud extends \Admin\ObjectWindow {

    public $fields = array (
  '__General__' => 
  array (
    'label' => 'General',
    'type' => 'tab',
    'children' => 
    array (
      'title' => 
      array (
        'key' => 'title',
        'label' => 'Title',
        'type' => 'text',
        'required' => 'true',
      ),
      'type' => 
      array (
        'key' => 'type',
        'label' => 'Type',
        'type' => 'number',
        'required' => 'true',
      ),
      'url' => 
      array (
        'key' => 'url',
        'label' => 'URL',
        'type' => 'text',
        'required' => 'true',
      ),
      'link' => 
      array (
        'key' => 'link',
        'label' => 'Link',
        'combobox' => 'true',
        'type' => 'object',
        'required' => 'true',
        'needValue' => '1',
        'againstField' => 'type',
      ),
    ),
  ),
  '__Access__' => 
  array (
    'label' => 'Access',
    'type' => 'tab',
    'children' => 
    array (
      'visible' => 
      array (
        'type' => 'checkbox',
        'label' => 'Visible in navigation',
        'empty' => '1',
      ),
      'accessDenied' => 
      array (
        'type' => 'checkbox',
        'label' => 'Access denied',
        'desc' => 'For everyone. This remove the page from the navigation.',
        'empty' => '1',
      ),
      'forceHttps' => 
      array (
        'type' => 'checkbox',
        'label' => 'Force HTTPS',
        'empty' => '1',
      ),
    ),
  ),
  '__Content__' => 
  array (
    'label' => 'Content',
    'type' => 'tab',
    'children' => 
    array (
      'field_1' => 
      array (
        'key' => 'field_1',
        'label' => '#Todo',
        'type' => 'label',
      ),
    ),
  ),
);

    public $itemsPerPage = 10;

    public $asNested = true;

    public $order = array (
  'a' => 'a',
);

    public $addIcon = '#icon-plus-5';

    public $addLabel = 'Add node';

    public $add = true;

    public $editIcon = '#icon-pencil-8';

    public $edit = true;

    public $remove = false;

    public $nestedRootFieldTemplate = '{label}';

    public $nestedRootAddIcon = '#icon-plus-2';

    public $nestedRootAddEntrypoint = 'root/';

    public $nestedRootAdd = true;

    public $nestedRootEditEntrypoint = 'root/';

    public $nestedRootEdit = true;

    public $nestedRootRemoveEntrypoint = 'root/';

    public $nestedRootRemove = true;

    public $export = false;

    public $object = 'Core\\Node';

    public $preview = false;

    public $titleField = 'Node';

    public $workspace = true;

    public $multiLanguage = true;

    public $multiDomain = false;

    public $versioning = false;


}

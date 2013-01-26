<?php

namespace Admin\Admin;
 
class NodeCrud extends \Admin\ObjectWindow {

    public $fields = array (
  '__General__' => array (
    'label' => 'General',
    'type' => 'tab',
    'children' => array (
      'title' => array (
        'label' => 'Title',
        'type' => 'text',
        'required' => 'true',
      ),
      'type' => array (
        'label' => 'Type',
        'type' => 'number',
        'required' => 'true',
      ),
      'url' => array (
        'label' => 'URL',
        'type' => 'text',
        'required' => 'true',
      ),
      'link' => array (
        'label' => 'Link',
        'combobox' => 'true',
        'type' => 'object',
        'required' => 'true',
        'needValue' => '1',
        'againstField' => 'type',
      ),
    ),
  ),
  '__Access__' => array (
    'label' => 'Access',
    'type' => 'tab',
    'children' => array (
      'visible' => array (
        'label' => 'Visible in navigation',
        'type' => 'checkbox',
      ),
      'accessDenied' => array (
        'label' => 'Access denied',
        'type' => 'checkbox',
        'desc' => 'For everyone. This remove the page from the navigation.',
      ),
      'forceHttps' => array (
        'label' => 'Force HTTPS',
        'type' => 'checkbox',
      ),
    ),
    'key' => '__Access__',
  ),
  '__Content__' => array (
    'label' => 'Content',
    'type' => 'tab',
    'children' => array (
      'field_1' => array (
        'label' => '#Todo',
        'type' => 'label',
      ),
    ),
  ),
);

    public $itemsPerPage = 10;

    public $asNested = true;

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

    public $addMultipleFields = array (
  'title' => array (
    'label' => 'Title',
    'type' => 'text',
    'required' => 'true',
  ),
  'visible' => array (
    'label' => 'Visible',
    'type' => 'checkbox',
    'width' => '100',
  ),
);

    public $addMultiple = true;

    public $object = 'Core\\Node';

    public $preview = false;

    public $titleField = 'Node';

    public $workspace = true;

    public $multiLanguage = true;

    public $multiDomain = false;

    public $versioning = false;


}

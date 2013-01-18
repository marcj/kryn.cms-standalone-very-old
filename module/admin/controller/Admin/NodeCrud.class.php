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
        'type' => 'text',
        'label' => 'Title',
        'empty' => '1',
      ),
    ),
  ),
);

    public $itemLayout = '';

    public $itemsPerPage = 10;

    public $asNested = true;

    public $order = array (
  'a' => 'a',
);

    public $addIcon = '#icon-plus-5';

    public $addEntrypoint = '';

    public $add = true;

    public $editIcon = '#icon-pencil-8';

    public $editEntrypoint = '';

    public $edit = true;

    public $removeIcon = '#icon-minus-5';

    public $remove = true;

    public $export = false;

    public $object = 'Core\\Node';

    public $preview = false;

    public $titleField = 'Node';

    public $workspace = true;

    public $multiLanguage = true;

    public $multiDomain = false;

    public $versioning = false;


}

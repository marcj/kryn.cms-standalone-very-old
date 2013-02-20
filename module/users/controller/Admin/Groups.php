<?php

namespace Users\Admin;
 
class Groups extends \Admin\ObjectCrud {

    public $fields = array (
  '__General__' => 
  array (
    'label' => 'General',
    'type' => 'tab',
    'children' => 
    array (
      'name' => 
      array (
        'key' => 'name',
        'label' => 'Name',
        'type' => 'text',
      ),
      'description' =>
      array (
        'key' => 'desc',
        'label' => 'Description',
        'type' => 'text',
      ),
    ),
  ),
);

    public $columns = array (
  'name' => 
  array (
    'type' => 'text',
    'label' => 'Name',
  ),
  'description' => 
  array (
    'type' => 'text',
    'label' => '!!No title defined!!',
  ),
);

    public $itemLayout = '{name}
<div style="color: silver">{if description.length > 20}{description.substr(0,20)}...{else}{description}{/if}</div>';

    public $itemsPerPage = 10;

    public $order = array (
  'name' => 'asc',
);

    public $addIcon = '#icon-plus-5';

    public $addEntrypoint = 'add';

    public $add = true;

    public $editIcon = '#icon-pencil-8';

    public $editEntrypoint = 'edit';

    public $edit = true;

    public $removeIcon = '#icon-minus-5';

    public $remove = true;

    public $export = false;

    public $object = 'Users\\Group';

    public $preview = false;

    public $titleField = 'name';

    public $workspace = false;

    public $multiLanguage = false;

    public $multiDomain = false;

    public $versioning = false;


}

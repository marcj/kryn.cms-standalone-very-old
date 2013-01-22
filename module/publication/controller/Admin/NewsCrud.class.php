<?php

namespace Publication\Admin;
 
class NewsCrud extends \Admin\ObjectWindow {

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
        'label' => '[[Title]]',
        'type' => 'text',
        'required' => 'true',
      ),
    ),
  ),
);

    public $columns = array (
  'title' =>
  array (
    'type' => 'text',
  )
);

    public $itemsPerPage = 10;

    public $order = array (
  'title' => 'asc',
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

    public $object = 'Publication\\News';

    public $preview = false;

    public $workspace = true;

    public $multiLanguage = false;

    public $multiDomain = false;

    public $versioning = false;


}

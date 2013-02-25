<?php

namespace Publication\Admin;

class NewsCrud extends \Admin\ObjectCrud
{
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
      'categoryId' => array (
        'type' => 'object',
        'label' => 'Category',
        'object' => 'Publication\\NewsCategory',
        'objectLabel' => 'title',
        'objectRelation' => 'nTo1',
      ),
      'tags' => array (
        'type' => 'text',
        'label' => 'Tags',
      ),
      'releaseDate' => array (
        'type' => 'datetime',
        'label' => 'Actual news date',
      ),
      'releaseAt' => array (
        'type' => 'datetime',
        'label' => 'Release at',
        'desc' => '[[Let it empty to release it immediately.]]'
      ),
      'deactivate' => array (
        'type' => 'checkbox',
        'label' => 'Hide',
        'empty' => '1',
      ),
      'deactivatecomments' => array (
        'type' => 'checkbox',
        'label' => 'Deactivate comments',
        'empty' => '1',
      ),
    ),
  ),
  '__Intro__' => array (
    'label' => 'Intro',
    'type' => 'tab',
    'children' => array (
      'intro' => array (
        'label' => 'Intro',
        'type' => 'wysiwyg',
        'preset' => 'simple'
      ),
    ),
  ),
  '__Content__' => array (
    'label' => 'Content',
    'type' => 'tab',
    'children' => array (
      'content' => array (
        'label' => 'Content',
        'type' => 'wysiwyg'
      ),
    ),
  ),
);

    public $columns = array (
  'title' => array (
    'type' => 'text',
    'label' => 'Title',
  ),
  'releaseDate' => array (
    'type' => 'datetime',
    'label' => 'Date',
  ),
);

    public $itemLayout = '<h2>{title}</h2>
{if releaseDate}<div class="sub">{releaseDate}</div>{/if}';

    public $defaultLimit = 15;

    public $order = array (
  'title' => 'asc',
);

    public $addIcon = '#icon-plus-5';

    public $add = true;

    public $editIcon = '#icon-pencil-8';

    public $edit = true;

    public $removeIcon = '#icon-minus-5';

    public $remove = true;

    public $nestedRootAdd = false;

    public $nestedRootEdit = false;

    public $nestedRootRemove = false;

    public $export = false;

    public $object = 'Publication\\News';

    public $preview = false;

    public $titleField = 'Article';

    public $workspace = true;

    public $multiLanguage = false;

    public $multiDomain = false;

    public $versioning = false;

}

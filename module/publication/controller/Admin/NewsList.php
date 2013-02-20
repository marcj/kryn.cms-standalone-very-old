<?php

namespace Publication\Admin;

class NewsList extends \Admin\ObjectCrud
{
    public $object = 'news';

    public $order = array(
        'categoryId' => 'desc',
        'title' => 'asc'
    );

    public $filter = array('title', 'categoryId');

    public $add = true;
    public $edit = true;
    public $remove = true;

    public $fields = array(
        'title',
        'categoryId',
        'releasedate',
        'releaseat'
    );

}

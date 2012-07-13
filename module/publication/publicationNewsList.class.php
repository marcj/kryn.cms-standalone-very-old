<?php

class publicationNewsList extends adminWindowCombine {

    public $object = 'news';

    public $itemsPerPage = 5;
    public $orderBy = 'category_id';
    public $orderByDirection = 'DESC';

    public $secondOrderBy = 'title'; /* optional */
    public $secondOrderByDirection = 'ASC'; /* optional */

    public $iconAdd = 'add.png';
    public $iconDelete = 'cross.png';

    public $filter = array('title', 'category_id');

    public $add = true;
    public $edit = true;
    public $remove = true;

    public $multiLanguage = true;

    public $primary = array('id');

    public $columns = array(
        'title',
        'category_id' => array(
            'label' => 'Category',
            'type' => 'select',
            'table' => 'publication_news_category',
            'table_label' => 'title',
            'width' => 130,
            'table_key' => 'id'
        ),
        'releasedate' => array(
            'label' => 'Date',
            'width' => 110,
            'type' => 'datetime'
        ),
        'releaseat' => array(
            'label' => 'Release at',
            'width' => 110,
            'type' => 'datetime',
        )
    );

}

?>
<?php

class publicationNewsList extends windowList {

    public $table = 'publication_news';
    public $itemsPerPage = 5;
    public $orderBy = 'category_rsn';
    public $orderByDirection = 'DESC';

    public $iconAdd = 'add.png';
    public $iconDelete = 'cross.png';

    public $filter = array('title', 'category_rsn');

    public $add = true;
    public $edit = true;
    public $remove = true;
    
    public $multiLanguage = true;

    public $primary = array('rsn');


    public $itemActions = array(
        array('View comments', 'admin/images/icons/comment.png', 'publication/news/comments'),
    );

    public $columns = array(
        'title' => array(
            'label' => 'Title',
            'type' => 'text'
        ),
        'category_rsn' => array(
            'label' => 'Categorie',
            'type' => 'select',
            'table' => 'publication_news_category',
            'table_label' => 'title',
            'width' => 130,
            'table_key' => 'rsn'
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

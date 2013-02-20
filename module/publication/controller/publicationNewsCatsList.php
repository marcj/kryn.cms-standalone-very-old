<?php

class publicationNewsCatsList extends windowList
{
    public $table = 'publication_news_category';
    public $itemsPerPage = 20;
    public $orderBy = 'title';

    public $filter = array('title');

    public $add = true;
    public $edit = true;
    public $remove = true;

    public $multiLanguage = true;
    public $primary = array('id');

    public $columns = array(
        'title' => array(
            'label' => 'Titel',
            'type' => 'text'
        )
    );

}

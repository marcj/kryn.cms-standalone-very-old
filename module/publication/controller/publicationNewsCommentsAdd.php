<?php

class publicationNewsCommentsAdd extends windowAdd
{
    public $table = 'publication_comments';
    public $primary = array('id');

    public $fields = array(
        'owner_username' => array(
            'label' => 'Name',
            'type' => 'text'
        ),
        'email' => array(
            'label' => 'E-Mail',
            'type' => 'text'
        ),
        'parent_id' => array(
            'label' => 'News',
            'type' => 'select',
            'multiLanguage' => true,
            'empty' => false,
            'table' => 'publication_news',
            'table_label' => 'title',
            'table_key' => 'id'
        ),
        'subject' => array(
            'label' => 'Subject',
            'type' => 'text'
        ),
        'message' => array(
            'label' => 'Message',
            'type' => 'textarea'
        )
    );
}

<?php

class publicationNewsCommentsAdd extends windowAdd {

    public $table = 'publication_comments';
    public $primary = array('rsn');

    public $fields = array(
        'owner_username' => array(
            'label' => 'Name',
            'type' => 'text'
        ),
        'email' => array(
            'label' => 'E-Mail',
            'type' => 'text'
        ),
        'parent_rsn' => array(
            'label' => 'News',
            'type' => 'select',
            'multiLanguage' => true,
            'empty' => false,
            'table' => 'publication_news',
            'table_label' => 'title',
            'table_key' => 'rsn'
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

?>

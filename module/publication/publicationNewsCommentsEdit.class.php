<?php

class publicationNewsCommentsEdit extends adminWindowEdit {

    public $table = 'publication_comments';

    public $primary = 'id';

    public $multiLanguage = 0;

    public $multiDomain = 0;

    public $versioning = 0;


    public $fields = array (
        '__general__' => array (
          'label' => '[[General]]',
          'type' => 'tab',
          'depends' => array (
            'owner_username' => array (
              'label' => 'Name',
              'type' => 'text',
            ),
            'email' => array (
              'label' => 'E-Mail',
              'type' => 'text',
            ),
            'parent_id' => array (
              'label' => 'News',
              'multi' => 0,
              'object' => 'news',
              'type' => 'object',
              'empty' => 0,
              'tableitem' => 0,
            ),
            'subject' => array (
              'label' => 'Subject',
              'type' => 'text',
            ),
            'message' => array (
              'label' => 'Message',
              'type' => 'textarea',
            ),
          ),
        ),
      );

}
 ?>
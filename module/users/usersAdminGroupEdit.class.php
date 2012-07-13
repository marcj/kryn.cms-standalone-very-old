<?php

class usersAdminGroupEdit extends windowEdit {

    public $table = 'system_groups';
    public $primary = array('id');

    public $fields = array(
        'name' => array(
            'label' => 'Name',
            'type' => 'text',
            'empty' => false,
        ),
        'description' => array(
            'label' => 'Description',
            'type' => 'text',
        ),
    );

}

?>

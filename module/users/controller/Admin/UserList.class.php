<?php


namespace Users\Admin;


class UserList extends \Admin\ObjectWindow {

    public $object = 'user';

    public $itemsPerPage = 20;
    public $order = array('username' => 'asc');

    public $filter = array('lastName', 'firstName', 'username', 'email');

    public $add = true;
    public $edit = true;
    public $remove = true;
    
    public $itemLayout = '<b id="username"></b> (<span id="firstName"></span> <span id="lastName"></span>)<div style="color: silver;" id="email"></div><div style="color: silver;" id="groupMembership.name"></div>';

    public $fields = array(
        'lastName' => array(
            'label' => 'Last name'
        ),
        'firstName' => array(
            'label' => 'First name'
        ),
        'username' => array(
            'label' => 'Username',
            'width' => 100
        ),
        'email' => array(
            'label' => 'Email'
        ),
        'activate' => array(
            'label' => 'Active',
            'width' => '35',
            'type' => 'imagemap',
            'imageMap' => array(
                'null' => 'admin/images/icons/cancel.png',
                '0' => 'admin/images/icons/cancel.png',
                '1' => 'admin/images/icons/accept.png'
            )
        ),
        'groupMembership.name'
    );

    function getCustomCondition(){
        $condition[] = array('id', '>', 0);
        return $condition;  
    }
    
    function deleteItem(){

        parent::deleteItem();

        $sql = "DELETE FROM `%pfx%system_groupaccess` WHERE `user_id` = ".($_POST['item']['id']+0);
        dbExec( $sql );
        return true;
    }

    function acl( $pItem ){
        $res = parent::acl( $pItem );

        if( $pItem['id'] == '1' )
            $res['remove'] = false;

        if( $pItem['id'] == '0' ){
            $res['remove'] = false;
            $res['edit'] = false;
        }

        return $res;
    }

}
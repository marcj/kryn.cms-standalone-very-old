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
    
    //todo
    public $itemLayout = '
    <div title="#{item.id}">
        <b>{item.username}</b>
        {if item.firstName || item.lastName}
            (<span>{item.firstName}</span>{if item.lastName} <span>{item.lastName}</span>){/if}
        {/if}
        {if item.email}<div style="color: silver;">{item.email}</div>{/if}
        <div style="color: silver;">{item.groupMembership.name}</div>
    </div>';

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
        'groupMembership.name' => array(
            'label' => 'Group membership'
        )
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

    function prepareRow( $pItem ){
        $res = parent::prepareRow( $pItem );

        if ($pItem['id'] == '1')
            $res['remove'] = false;

        return $res;
    }

}
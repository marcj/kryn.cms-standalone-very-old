<?php

class usersAdminList extends windowList {

    public $object = 'user';

    public $itemsPerPage = 20;
    public $orderBy = 'username';

    public $iconAdd = 'user_add.png';
    public $iconEdit = 'user_edit.png';
    public $iconDelete = 'user_delete.png';

    public $filter = array('last_name', 'first_name', 'username', 'email');

    public $add = true;
    public $edit = true;
    public $remove = true;

    public $primary = array('rsn');
    
    public $itemLayout = '<b id="username"></b> (<span id="first_name"></span> <span id="last_name"></span>)<br/><span style="color: silver;" id="groups_name"></span>';

    public $columns = array(
        'last_name' => array(
            'label' => 'Last name',
            'type' => 'text'
        ),
        'first_name' => array(
            'label' => 'First name',
            'type' => 'text'
        ),
        'username' => array(
            'label' => 'Username',
            'type' => 'text',
            'width' => 100
        ),
        'email' => array(
            'label' => 'Email',
            'width' => '140',
            'type' => 'text'
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
        'groups',
    );

    function filterSql(){
    	$filter = parent::filterSql();
    	
    	$filter .= " AND %pfx%".$this->table.".rsn > 0";
    	
    	return $filter;    	
    }
    
    function deleteItem(){

        parent::deleteItem();

        $sql = "DELETE FROM `%pfx%system_groupaccess` WHERE `user_rsn` = ".($_POST['item']['rsn']+0);
        dbExec( $sql );
        return true;
    }

    function acl( $pItem ){
        $res = parent::acl( $pItem );

        if( $pItem['rsn'] == '1' )
            $res['remove'] = false;

        if( $pItem['rsn'] == '0' ){
            $res['remove'] = false;
            $res['edit'] = false;
        }

        return $res;
    }

}

?>

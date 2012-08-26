<?php


class usersAdminEdit extends windowEdit {

    public $object = 'user';
    public $primary = array('id');
    public $versioning = false;

    public $loadSettingsAfterSave = true;
    
    private static $cacheUser = false;
    
    public $titleField = 'username';

    public $tabLayouts = array(
        'General' => '<table width="100%"><tr>
        	<td width="110">
        		 <div style="height: 100px; margin:5px" id="picture"></div>
        	</td>
        	<td>
        		<div id="name"></div>
        		<div style="clear: both"></div>
        		<div id="lastname"></div>
        		<div style="clear: both"></div>
        	</td>
        </tr><tr>
        	<td colspan="2" style="padding: 10px;">
        		<table width="100%">
        			<tbody id="default"></tbody>
        		</table>
        	</td>
        </tr></table>'
    );

    public $tabFields = array(
        'General' => array(
            'picture' => array(
                'onlycontent' => 1,
                'target' => 'picture',
                'type' => 'custom',
                'class' => 'users_field_picture'
            ),
            'first_name' => array(
                'label' => 'First name',
                'target' => 'name',
                'small' => 1,
                'type' => 'text'
            ),
        	'last_name' => array(
                'label' => 'Last name',
                'target' => 'lastname',
                'small' => 1,
                'type' => 'text'
            ),
            'company' => array(
                'label' => 'Company',
                'tableitem' => true,
                'type' => 'text'
            ),
            'street' => array(
                'label' => 'Street',
                'tableitem' => true,
                'type' => 'text'
            ),
            'city' => array(
                'label' => 'City',
                'tableitem' => true,
                'type' => 'text'
            ),
            'zip' => array(
                'label' => 'Zipcode',
                'tableitem' => true,
                'type' => 'number',
                'length' => 10
            ),
            'country' => array(
                'label' => 'Country',
                'tableitem' => true,
                'type' => 'text'
            ),
            'phone' => array(
                'label' => 'Phone',
                'tableitem' => true,
                'type' => 'text'
            ),
            'fax' => array(
                'label' => 'Fax',
                'tableitem' => true,
                'type' => 'text'
            ),
            
        ),
        'Account' => array(
            'username' => array(
                'label' => 'Username',
                'desc' => '(and the administration login)',
                'type' => 'text',
                //'empty' => false // TODO: FE users don't need a username, just an email address [Ferdi]
            ),
            'passwd' => array(
                'label' => 'Password',
                'type' => 'password',
                'desc' => 'Leave empty to change nothing',
                'startempty' => true,
                'onlyIfFilled' => true,
                'customSave' => 'savePasswd'
            ),
            'email' => array(
                'label' => 'Email',
                'type' => 'text',
                'empty' => false
            ),
            'activate' => array(
                'label' => 'Active account',
                'type' => 'checkbox'
            ),
            'userBg' => array(
               'label' => 'Desktop background image',
                'type' => 'fileChooser',
                'customSave' => 'saveUserBg',
                'customValue' => 'userBgValue',
            ),
            'groups'

            /*'groups' => array(
                'label' => 'Groups',
                'type' => 'textlist',
                'store' => 'admin/backend/stores/groups',
                'relation' => 'n-n',
                'n-n' => array(
                    'right' => 'system_groups',
                    'right_key' => 'id',
                    'right_label' => 'name',
                    'middle' => 'system_groupaccess',
                    'middle_keyright' => 'system_group_id',
                    'middle_keyleft' => 'system_user_id',
                    'left_key' => 'id'
                )
            )*/
        ),
        'Administration' => array(
            'adminLanguage' => array(
                'label' => 'Admin Language',
                'type' => 'select',
                'sql' => 'SELECT * FROM %pfx%system_langs',
                'table_key' => 'code',
                'table_label' => 'title',
                'customSave' => 'saveLanguage',
                'customValue' => 'getLanguage',
            ),
            'userBg' => array(
               'label' => 'Desktop background image',
                'type' => 'fileChooser',
                'customSave' => 'saveUserBg',
                'customValue' => 'userBgValue',
            ),
            'css3Shadow' => array(
                'label' => 'Use CSS3 box-shadows',
                'desc' => 'Can affect performance in some browsers, but activates better window feeling',
                'type' => 'checkbox',
                'customSave' => 'saveCssShadow',
                'customValue' => 'getCssShadow',
            ),
            'autocrawler' => array(
                'label' => 'Activate autocrawler',
                'desc' => 'This activates the internal searchengine autocrawler, when you are working in the administration. Can affect performance, especially when you have low bandwith internet',
                'type' => 'checkbox',
                'depends' => array(
                    'autocrawler_minddelay' => array(
                        'needValue' => 1,
                        'label' => 'Min. delay (Milliseconds)',
                        'desc' => 'If you have problems with the speed, try to increase this delay.',
                        'type' => 'number',
                        'default' => 200,
                        'length' => 10,
                        'customSave' => 'saveAutocrawlerDelay',
                        'customValue' => 'getAutocrawlerDelay',
                    )
                ),
                'customSave' => 'saveAutocrawler',
                'customValue' => 'getAutocrawler',
            )
        )
    );
    

    private static function saveSetting( $pKey, $pVal ){
        
        $temp = dbTableFetch('system_user', 1, "id = ".(getArgv('id')+0));
        $settings = unserialize( $temp['settings'] );
        
        $settings[$pKey] = $pVal;
        $ssettings = serialize( $settings );
        
        dbUpdate( 'system_user', array('id' => getArgv('id')+0), array('settings' => $ssettings) );
    }

    private static function getSetting( $pKey ){
        
        $id = getArgv('id')+0;
        
        if( !self::$cacheUser )
            self::$cacheUser = dbTableFetch('system_user', 1, "id = $id");
            
        $settings = unserialize(self::$cacheUser['settings']);
        return $settings[$pKey];
    }
    
    
    /*
     * Saver
     * 
     */
    public function saveUserBg(){
        self::saveSetting('userBg', getArgv('userBg',1));
    }

    public function saveLanguage(){
        self::saveSetting('adminLanguage', getArgv('adminLanguage'));
    }

    public function saveAutocrawler(){
        self::saveSetting('css3Shadow', getArgv('css3Shadow'));
    }
    
    public function saveCssShadow(){
        self::saveSetting('autocrawler', getArgv('autocrawler'));
    }

    public function saveAutocrawlerDelay(){
        self::saveSetting('autocrawler_minddelay', getArgv('autocrawler_minddelay'));
    }
    
    
    /*
     * Getter
     * 
     */
    public function getLanguage(){
        return self::getSetting('adminLanguage');
    }

    public function userBgValue($pPrimary, $pItem){
        return self::getSetting('userBg');
    }
    
    public function getCssShadow(){
        return self::getSetting('css3Shadow');
    }
 
    public function getAutocrawler(){
        return self::getSetting('autocrawler');
    }
    
    public function getAutocrawlerDelay(){
        $val = self::getSetting('autocrawler_minddelay');
        if( !$val ) return 200;
        return $val;
    }    

    public function savePasswd( &$pRow ){
        
        $salt = krynAuth::getSalt();
        $passwd = krynAuth::getHashedPassword( getArgv('passwd'), $salt );
        $pRow['passwd'] = $passwd;
        $pRow['passwd_salt'] = $salt;

    }

}

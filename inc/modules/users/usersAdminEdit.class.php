<?php


class usersAdminEdit extends windowEdit {

    public $table = 'system_user';
    public $primary = array('rsn');
    public $versioning = false;

    public $loadSettingsAfterSave = true;
    
    private static $cacheUser = false;

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
                'startempty' => true,
                'onlyIfFilled' => true,
                'modifier' => 'toPasswd'
            ),
            'email' => array(
                'label' => 'Email',
                'type' => 'text',
                'empty' => false
            ),
            'activate' => array(
                'label' => 'Active account',
                'type' => 'select',
                'tableItems' => array(
                    array('name' => 'Yes', 'value' => 1),
                    array('name' => 'No', 'value' => 0)
                ),
                'table_label' => 'name',
                'table_id' => 'value',
            ),
            'userBg' => array(
               'label' => 'Desktop background image',
                'type' => 'fileChooser',
                'customSave' => 'saveUserBg',
                'customValue' => 'userBgValue',
            ),
            'groups' => array(
                'label' => 'Groups',
                'type' => 'select',
                'table' => 'system_groupaccess',
                'relation' => 'n-n',
                'n-n' => array(
                    'right' => 'system_groups',
                    'right_key' => 'rsn',
                    'right_label' => 'name',
                    'middle' => 'system_groupaccess',
                    'middle_keyright' => 'group_rsn',
                    'middle_keyleft' => 'user_rsn',
                    'left_key' => 'rsn'
                ),
                'size' => 6,
                'multiple' => 1
            )
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
        
        $temp = dbTableFetch('system_user', 1, "rsn = ".(getArgv('rsn')+0));
        $settings = unserialize( $temp['settings'] );
        
        $settings[$pKey] = $pVal;
        $ssettings = serialize( $settings );
        
        dbUpdate( 'system_user', array('rsn' => getArgv('rsn')+0), array('settings' => $ssettings) );
    }

    private static function getSetting( $pKey ){
        
        $rsn = getArgv('rsn')+0;
        
        if( !self::$cacheUser )
            self::$cacheUser = dbTableFetch('system_user', 1, "rsn = $rsn");
            
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
    

    public function toPasswd( $pPw ){
        return md5($pPw);
    }
    
    function __destruct(){
    	user::getUser( getArgv('rsn'), true ); //refresh cache
    }

}

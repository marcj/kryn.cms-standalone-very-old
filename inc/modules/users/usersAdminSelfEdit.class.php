<?php


class usersAdminSelfEdit extends windowEdit {

    public $table = 'system_user';
    public $primary = array('rsn');
    public $versioning = false;
    
    public $loadSettingsAfterSave = true;
    
    private static $cacheUser = false;

    function __construct(){
        global $user;
        $_REQUEST['rsn'] = $user->user_rsn;
    }

    public $tabFields = array(
        'General' => array(
	        'username' => array(
	            'label' => 'Username',
	            'desc' => 'Also the administration login',
	            'needAccess' => 'admin/users/users/editMe/username',
	            'type' => 'text',
	            'empty' => false
	        ),
	        'email' => array(
	            'label' => 'Email',
	            'type' => 'text',
	            'empty' => false
	        ),
	        'passwd' => array(
	            'label' => 'Password',
	            'desc' => 'Let it empty to change nothing',
	            'type' => 'password',
	            'startempty' => true,
	            'onlyIfFilled' => true,
	            'modifier' => 'toPasswd'
	        ),
            'groups' => array(
                'label' => 'Groups',
                'type' => 'textlist',
	            'needAccess' => 'admin/users/users/editMe/groups',
                'store' => 'admin/backend/stores/groups',
                'relation' => 'n-n',
                'n-n' => array(
                    'right' => 'system_groups',
                    'right_key' => 'rsn',
                    'right_label' => 'name',
                    'middle' => 'system_groupaccess',
                    'middle_keyright' => 'group_rsn',
                    'middle_keyleft' => 'user_rsn',
                    'left_key' => 'rsn'
                )
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
        global $client;
        $client->getUser( getArgv('rsn')+0, true ); //refresh cache
    }

}

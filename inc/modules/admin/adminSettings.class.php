<?php

class adminSettings {

    public static function init(){
        switch( getArgv(4) ){
            case 'saveSettings':
                return adminSettings::saveSettings();
            case 'loadSettings':
                return adminSettings::loadSettings();
            case 'saveCommunity':
                return adminSettings::saveCommunity();
            case 'preload':
                return adminSettings::preload();
        } 
    }

    public static function preload(){
        $res['langs'] = dbTableFetch( 'system_langs', DB_FETCH_ALL, "1=1 ORDER BY title" );
        $res['timezones'] = timezone_identifiers_list();
        json($res);
    }

    public static function loadSettings(){
        global $cfg;
        
        $settings = admin::getSettings();
        
        include('inc/config.php');
        
        $settings['system'] = array_merge($settings['system'], $cfg);

        json( $settings );
    }

    public static function saveSettings(){
        global $cfg;

        $settings = admin::getSettings();
        $res = array();

        if( $settings['system']['communityEmail'] != getArgv('communityEmail') 
            && getArgv('communityEmail') != '')
            $res['needPw'] = true;

        if( getArgv('communityEmail') == '' ){
            $_REQUEST['communityId'] = '';
        }
        
        if( !getArgv('sessiontime') )
            $_REQUEST['sessiontime'] = 3600;
        
        include('inc/config.php');
        
        $blacklist = array('communityEmail', 'communityId', 'languages');
        
        foreach( $_POST as $key => $value ){
            if( !in_array($key,$blacklist) ){
                $cfg[ $key ] = getArgv( $key );
            }
        }
        
        kryn::fileWrite('inc/config.php', "<?php \n\$cfg = ".var_export($cfg,true)."\n?>");

        dbUpdate('system_langs', array('visible'=>1), array('visible'=>0));
        $langs = getArgv('languages');
        foreach( $langs as $l )
            dbUpdate('system_langs', array('rsn'=>$l), array('visible'=>1));

        json( $res );
    }

    public static function saveCommunity(){
        global $cfg;
        
        $pw = md5(getArgv('passwd'));
        $email = getArgv('email',1);
        $json = wget("http://www.kryn.org/rpc?t=checkLogin&email=$email&pw=$pw");
        if( $json === false )
            json(2);
        $res = json_decode($json,true);
        if( $res['status'] >= 1 ){
            $cfg['communityEmail'] = $email;
            $cfg['communityId'] = $res['id'];
            kryn::fileWrite('inc/config.php', "<?php \n\$cfg = ".var_export($cfg,true)."\n?>");
            json(1);
        }

        json(0);
    }



}

?>

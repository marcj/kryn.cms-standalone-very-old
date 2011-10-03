<?php


class adminBackup {


    public static function init(){
        switch( getArgv(4) ){
            case 'list':
                return self::getItems();
            case 'save':
                if( getArgv('id') )
                    return self::saveItem( getArgv('id') );
                else
                    return self::addItem();
            default:
                return array('error'=>'param_failed');
        }
    }

    public static function saveItem( $pId ){
        global $cfg;
        
        if( !$cfg['backups'] )
            $cfg['backups'] = array();
        
        if( getArgv('method') != 'cronjob' ) return false;
        
        $cfg['backups'][$pId] = $_POST;
        
        kryn::fileWrite('inc/config.php', "<?php \n\$cfg = ".var_export($cfg,true)."\n?>");
        
        return true;
    }
    
    public static function addItem(){
        global $cfg;
        
        if( !$cfg['backups'] )
            $cfg['backups'] = array();
        
        if( getArgv('method') != 'cronjob' ) return false;
        
        do {
            $id = dechex(rand(0,3).time().count($cfg['backups']));
        } while( $cfg['backups'][$id] );
        
        $cfg['backups'][$id] = $_POST;
        
        kryn::fileWrite('inc/config.php', "<?php \n\$cfg = ".var_export($cfg,true)."\n?>");
        
        return true;
    }
    
    public static function getItems(){
        global $cfg;
        
        if( !$cfg['backups'] )
            $cfg['backups'] = array();
    
        return $cfg['backups'];
    }



}

?>
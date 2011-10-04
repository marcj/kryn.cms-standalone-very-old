<?php


class adminBackup {

    public static function init(){
        switch( getArgv(4) ){
            case 'list':
                return self::getItems();
            case 'remove':
                return self::removeItem( getArgv('id') );
            case 'save':
                if( getArgv('id') )
                    return self::saveItem( getArgv('id') );
                else
                    return self::addItem();
            case 'state':
                return self::state( getArgv('id') );
            case 'generate':
                return self::createBackup();
            default:
                return array('error'=>'param_failed');
        }
    }
    
    public static function state( $pBackupId ){
        global $cfg;

        if( !$cfg['backups'][$pBackupId] ) return 'not_found';        
        
        $path = self::getTempFolder().'kryn_backup_'.$pBackupId;
        $state = kryn::fileRead( $path.'/step' );

        return $state;        
    }

    public static function removeItem( $pId ){
        global $cfg;
        
        unset($cfg['backups'][$pId]);
        
        kryn::fileWrite('inc/config.php', "<?php \n\$cfg = ".var_export($cfg,true)."\n?>");
        
        return true;
    }
    
    
    public static function saveItem( $pId ){
        global $cfg;
        
        if( !$cfg['backups'] )
            $cfg['backups'] = array();
        
        $cfg['backups'][$pId] = $_POST;
        
        kryn::fileWrite('inc/config.php', "<?php \n\$cfg = ".var_export($cfg,true)."\n?>");
        
        return true;
    }
    
    public static function addItem(){
        global $cfg;
        
        if( !$cfg['backups'] )
            $cfg['backups'] = array();
        
        $path = '';
        do {
            $id = dechex(time()/mt_rand(100, 500));
            $path = self::getTempFolder().'kryn_backup_'.$id;
        } while( $cfg['backups'][$id] && file_exists($path) );

        
        mkdir($path);
        
        if( !file_exists( $path ) ){
            klog('backup', _('Add backup failed. Can not create folder:').' '.$path);
            return false;
        }
        
        $_POST['_path'] = $path;
        $cfg['backups'][$id] = $_POST;

        @mkdir($path);

        kryn::fileWrite('inc/config.php', "<?php \n\$cfg = ".var_export($cfg,true)."\n?>");

        if( getArgv('start') )
            self::startBackup( $id );
            
        return true;
    }
    
    public static function getItems(){
        global $cfg;
        
        if( !$cfg['backups'] )
            $cfg['backups'] = array();

        foreach( $cfg['backups'] as $key => &$backup ){
            $path = self::getTempFolder().'kryn_backup_'.$key;
            if( file_exists($path) ){
                $state = kryn::fileRead( $path.'/step' );
                if( $state && $state != '' && $state != 'done' )
                    $backup['working'] = true;
            }
        }
    
        return $cfg['backups'];
    }
    
    public static function getTempFolder(){
        
        $path = realpath( self::_getTempFolder() );
        if( substr($path, -1) != '/' )
            return $path.'/';

        return $path;
    }

    public static function _getTempFolder(){
        global $cfg;
        
        if( $cfg['backup_generation_path'] )
            return $cfg['backup_generation_path'];
        
        if( $_ENV['TMP'] ) return $_ENV['TMP'];
        if( $_ENV['TEMP'] ) return $_ENV['TEMP'];
        if( $_ENV['TMPDIR'] ) return $_ENV['TMPDIR'];
        if( $_ENV['TEMPDIR'] ) return $_ENV['TEMPDIR'];
    
        return sys_get_temp_dir();
    }

    public static function startBackup( $pBackupCode ){
        global $cfg;
        
        $definitions = $cfg['backups'][$pBackupCode];
        if( $definitions ){
            $path = self::getTempFolder().'kryn_backup_'.$pBackupCode;
        } else {
            return false;
        }

        kryn::fileWrite( $path.'/step', 'preparing' );
        chmod($path, 0777);
        chmod($path.'/step', 0666);
        
        if( function_exists('popen') ){
            $cmd = 'cronjob.php '.$cfg['cronjob_key'].' backup '.$pBackupCode;
            
            //TODO need windows equivalent
            pclose(popen('php '.$cmd.' &> /dev/null &', "r"));

            sleep(1);

            $state = kryn::fileRead( $path.'/step' );
            if( $state == 'preparing' ){
                klog('backup', _l('Can not start the backup process through popen() caused by a undefined error.'));
                kryn::fileWrite( $path.'/step', 'error' );
            }

        } else {
            kryn::fileWrite( $path.'/step', 'error' );
            klog('backup', _l('Can not start the backup process through popen() caused by php functions restriction.'));
        }

    }

    public static function doBackup( $pBackupCode ){
        global $cfg;

        $definitions = $cfg['backups'][ $pBackupCode ];
        
        $path = $definitions['_path'];
        kryn::fileWrite( $path.'/step', 'start' );

        sleep(15);

        kryn::fileWrite( $path.'/step', 'done' );

    }

}

?>
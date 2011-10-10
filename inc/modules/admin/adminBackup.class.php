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
        error_log("start backup $path");
        exec("logger start backup $path");
        kryn::fileWrite( $path.'/step', 'start' );
        
        @delDir($path.'/domains');
        @delDir($path.'/nodes');
        
        @mkdir($path.'/domains');
        @mkdir($path.'/nodes');

        //pages/sites
        if( $definitions['pages'] == 'all' ){
        
            $domains = dbTableFetch('system_domains', -1);
            foreach( $domains as $domain ){
                kryn::fileWrite( $path.'/step', 'domain:'.$domain['domain'] );
                self::exportWebsite( $path, $domain['rsn'] );
            }
                
        } else if( $definitions['pages'] == 'choose' ){
        
            foreach( $definitions['pages_domains'] as $domainRsn ){
                
                $domain = dbTableFetch('system_domains', 'rsn = '.$domainRsn, 1);
                kryn::fileWrite( $path.'/step', 'domain:'.$domain['domain'] );

                self::exportWebsite( $path, $domainRsn );
            }

            foreach( $definitions['pages_nodes'] as $nodeRsn ){
                $node = dbTableFetch('system_pages', ' rsn = '.$nodeRsn, 1);
                kryn::fileWrite( $path.'/step', 'node:'.$node['title'] );
                self::exportNode( $path, $nodeRsn );
            }

        }

        sleep(15);

        kryn::fileWrite( $path.'/step', 'done' );
    }
    
    public static function getNextFileId( $pPath, $pExt = '.json' ){
        
        $found = false;
        $curId = 0;
        do {
        
            $curId++;
            if( !file_exists( $pPath.'/'.$curId.$pExt) ){
                $found = true;
            }
        
        } while( !$found );
    
        return $curId;
    }
    
    public static function exportWebsite( $pPath, $pDomainRsn ){

        

        $pDomainRsn += 0;
        $nodes = self::exportTree( 0, $pDomainRsn );
        $domain = dbTableFetch('system_domains', 'rsn = '.$pDomainRsn, 1);
              
        unset($domain['rsn']);
                
        $export = array(
            'domain' => $domain,
            'nodes' => &$nodes
        );
        
        $id = self::getNextFileId( $pPath.'/domains' );

        kryn::fileWrite( $pPath.'/domains/'.$id.'.json', json_encode($export) );
        
    }

    public static function exportNode( $pPath, $pNodeRsn ){
        
        $pNodeRsn += 0;
        $nodes = self::exportTree( $pNodeRsn );
        
        $id = self::getNextFileId( $pPath.'/nodes' );
        kryn::fileWrite( $pPath.'/nodes/'.$id.'.json', json_encode($nodes) );

    }
    
    public static function exportTree( $pNodeRsn, $pDomainRsn = false, $pAndAllVersions = false ){
	
    	$pNodeRsn += 0;
        $pagesRes = dbExec("SELECT * FROM %pfx%system_pages WHERE prsn = $pNodeRsn ".($pDomainRsn?' AND domain_rsn = '.$pDomainRsn:''));
        
        $childs = array();
        while( $row = dbFetch($pagesRes) ){
    	     
        	$contentRes = dbExec("SELECT c.* FROM %pfx%system_contents c, %pfx%system_pagesversions v
                    WHERE 
                    c.page_rsn = ".$row['rsn']."
                    AND v.active = 1
                    AND c.version_rsn = v.rsn");
            
            while( $contentRow = dbFetch($contentRes) ){
                
                unset($contentRow['rsn']);
                unset($contentRow['page_rsn']);
                $row['contents'][] = $contentRow;
                
            }
            
            //TODO $pAndAllVersions
            
            
            $row['childs'] = self::exportTree($row['rsn'], $pDomainRsn, $pAndAllVersions);
            
            unset($row['rsn']);
            unset($row['domain_rsn']);
            unset($row['prsn']);
            
            $childs[] = $row;
        }
    
        return $childs;
    }

}

?>
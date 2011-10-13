<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */



class krynCache {
    
    public $type;
    
    public $config;
    
    function __construct( $pType, $pConfig ){
    
        $this->type = $pType;
        $this->config = $pConfig;
    
        switch( $this->type ){
            case 'memcached':
                $this->initMemcached();
                break;
            case 'redis':
                $this->initRedis();
                break;
            case 'files':            
                if( !is_dir($this->config['files_path']) ){
                    if( !mkdir($this->config['files_path']) ){
                        die('Can not create cache folder: '.$this->config['files_path']);
                    }
            }
        }

    }
    
    public function initRedis(){
        
        $this->redis = new Redis;
        foreach( $this->config['redis_servers'] as $server ){
            $this->redis->connect( $server['ip'], $server['port']+0 );
        }
    
    }
    
    public function initMemchached(){
    
        if( class_exists('Memcache') ){
            
            $this->memcache = new Memcache;
            foreach( $this->config['memcached_servers'] as $server ){
                $this->memcache->addServer( $server['ip'], $server['port']+0 );
            }

        } else {

            if( class_exists('Memcached') ){
                $this->memcached = new Memcached;
                foreach( $this->config['memcached_servers'] as $server ){
                    $this->memcached->addServer( $server['ip'], $server['port']+0 );
                }
            } else {
                $this->config['session_storage'] = 'database';
            }
        }
    
    }
    
    /**
     * 
     * Returns the content of the specified cache-key
     * @param string $pCode
     * @return string
     * @static
     */
    public function &get( $pCode ){
        global $kcache;

        switch( $this->type ){
            case 'memcached':
            
                if( $this->memcache ){
                    return $this->memcache->get( $pCode );
                } else if( $this->memcached ){
                    return $this->memcached->get( $pCode );
                }
            
            case 'redis':
                
                return $this->redis->get( $pCode );
                
            case 'files':
                if( $kcache[$cacheCode] ) return $kcache[$cacheCode];
                $cacheCode = 'krynPhpCache_'.$pCode;
                include( $this->config['files_path'].$pCode.'.php' );
                return $kcache[$cacheCode];
        }
        
    }
    
    /**
     * 
     * Sets a content to the specified cache-key.
     * Kryn uses MemCache or PHP-Caching
     * @param string $pCode
     * @param string $pValue
     * @static
     */
    public function set( $pCode, $pValue, $pTimeout = false ){
        global $kcache;

        switch( $this->type ){
            case 'memcached':
            
                if( $this->memcache ){
                    return $this->memcache->set( $pCode, $pValue, 0, $pTimeout?$pTimeout:null );
                } else if( $this->memcached ){
                    return $this->memcached->set( $pCode, $pValue, $pTimeout?$pTimeout:null);
                }
            
            case 'redis':
                
                if( $pTimeout )
                    $this->redis->setex( $pCode, $pTimeout, $pValue );
                else
                    return $this->redis->set( $pCode, $pValue );

            case 'files':

                $cacheCode = 'krynPhpCache_'.$pCode;
                $kcache[$cacheCode] = $pValue;
                $varname = '$kcache[\''.$cacheCode.'\'] ';
                $phpCode = "<"."?php \n$varname = ".var_export($pValue,true).";\n ?".">";
                kryn::fileWrite($this->config['files_path'].$pCode.'.php', $phpCode);
                return file_exists( $this->config['files_path'].$pCode.'.php' );
        }
    }
    
    /**
     *
     * Clears the content for specified cache-key.
     * @param string $pCode
     */
    public function clear( $pCode ){
        global $kcache;
        
        switch( $this->type ){
            case 'memcached':
            
                if( $this->memcache ){
                    return $this->memcache->delete( $pCode );
                } else if( $this->memcached ){
                    return $this->memcached->delete( $pCode );
                }
            
            case 'redis':
                
                return $this->redis->delete( $pCode, $pValue );
                
            case 'files':
            
                $cacheCode = 'krynPhpCache_'.$pCode;
                unset($kcache[$cacheCode]);
                @unlink($this->config['files_path'].$pCode.'.php');
        }
    }
    
    /**
     *
     * Clears the content for specified cache-key.
     * @param string $pCode
     */
    public function delete( $pCode ){
        return $this->clear( $pCode );
    }
}

?>
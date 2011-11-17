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

/**
*
* krynCache
*
* A caching class, that provides several ways to cache data
* with inheritance invalidation mechanism. 
*
*
* 
*
*/

class krynCache {
    
    /*
    * Contains the current type
    */
    public $type;

    /**
    * Contains the current config values
    */    
    public $config;
    
    /**
    * This activates the invalidate() mechanism
    *
    * If activated, each time get() is called, the function searched
    * for parents based on a exploded string by '_'. If a parent is
    * found is a invalidated cache, the call is ignored and false will be returned.
    *
    * Example: call get('workspace_tables_tableA')
    *          => checks 'workspace_tables' for invalidating (getInvalidate('workspace_tables'))
    *          => if 'workspace_tables' was flagged as invalidate (invalidate('workspace_tables')), return false
    *          => checks 'workspace' for invalidating (getInvalidate('workspace'))
    *          => if 'workspace' was flagged as invalidate (invalidate('workspace')), return false
    *
    * 
    * So you can invalidate multiple keys with just one call.
    *
    */
    public $withInvalidationChecks = true;
    

    /**
    * krynCache class constructor.
    * @param    string  $pType  can be memcached, redis, files, xcache or apc.
    * @param    string  $pConfig
    *                   memcached and redis: array(
    *                       'servers' => array(
    *                           array('ip' => '12.12.12.12', 'port' => 6379
    *                           array('ip' => '12.12.12.13', 'port' => 6379
    *                       )
    *                   )
    *                   files: array('files_path' => '<path to store the cached files')
    * @param    bool    $pWithInvalidationChecks activates the invalidating mechanism
    * @access public
    */
    function __construct( $pType = 'files', $pConfig = array(), $pWithInvalidationChecks = true ){
    
        $this->type = $pType;
        $this->config = $pConfig;
        $this->withInvalidationChecks = $pWithInvalidationChecks;

        switch( $this->type ){
            case 'memcached':
                if( !$this->initMemcached() ){
                    klog('cache', _l('Can not load the memcache(d) class. Fallback to file caching.'));
                    $this->type = 'files';
                    $this->config['files_path'] = 'inc/cache/';
                }
                break;
            case 'redis':
                if( !$this->initRedis() ){
                    klog('cache', _l('Can not load the Redis class. Fallback to file caching.'));
                    $this->type = 'files';
                    $this->config['files_path'] = 'inc/cache/';
                } 
                break;
            case 'apc':
                if( !function_exists('apc_store') ){
                    klog('cache', _l('The php apc module is not loaded. Fallback to file caching.'));
                    $this->type = 'files';
                    $this->config['files_path'] = 'inc/cache/';
                }
                break;
            case 'xcache':
                if( !function_exists('xcache_get') ){
                    klog('cache', _l('The php xcache module is not loaded. Fallback to file caching.'));
                    $this->type = 'files';
                    $this->config['files_path'] = 'inc/cache/';
                }
                break;
        }
        
        if( $this->type == 'files' ){

            if( $this->config['files_path'] == '' )
                $this->config['files_path'] = 'inc/cache/';
        
            if( !is_dir($this->config['files_path']) ){
                if( !mkdir($this->config['files_path']) ){
                    die('Can not create cache folder: '.$this->config['files_path']);
                }
            }
        }

    }

    /**
    *
    * Initialize the redis objects
    */
    public function initRedis(){
        
        if( !class_exists('Redis') ) return false;

        $this->redis = new Redis;

        foreach( $this->config['servers'] as $server ){
            $this->redis->connect( $server['ip'], $server['port']+0 );
        }
        
        $this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);
        
        return true;
    }

    /**
    *
    * Initialize the memcached objects
    */
    public function initMemcached(){
    
        if( class_exists('Memcache') ){
            $this->memcache = new Memcache;
            foreach( $this->config['servers'] as $server ){
                $this->memcache->addServer( $server['ip'], $server['port']+0 );
            }
        } else if( class_exists('Memcached') ){
            $this->memcached = new Memcached;
            foreach( $this->config['servers'] as $server ){
                $this->memcached->addServer( $server['ip'], $server['port']+0 );
            }
        } else {
            return false;
        }
    
        return true;
    }
    
    /**
     * 
     * Returns the content of the specified cache-key
     * @param string $pCode
     * @return string
     * @static
     */
    public function &get( $pCode, $pWithoutValidationCheck = false ){
        global $kcache;

        switch( $this->type ){
            case 'memcached':

                if( $this->memcache ){
                    $res = $this->memcache->get( $pCode );
                } else if( $this->memcached ){
                    $res = $this->memcached->get( $pCode );
                }
                break;

            case 'redis':
                
                $res = $this->redis->get( $pCode );
                break;

            case 'files':
                
                $cacheCode = 'krynPhpCache_'.$pCode;
                $path = $this->config['files_path'].$pCode.'.php';
                
                if( !file_exists( $path ) ) return false;
                include( $path );

                $res =& $kcache[$cacheCode];

                break;

            case 'apc':
            
                $res = apc_fetch( $pCode );
                break;
                
            case 'xcache':
            
                $res = xcache_get( $pCode );
        }
        
        if( !$res ){
            return false;
        }

        if( $this->withInvalidationChecks ){
        
            
            if( $pWithoutValidationCheck == true ){
                if( !$res['value'] || !$res['time'] || $res['timeout'] < time() ){
                    return false;
                }
            }
        
            //valid cache
            //search if a parent has been flagged as invalid
            if( strpos( $pCode, '_' ) !== false ){
    
                $parents = explode( '_', $pCode );
                $code = '';
                if( is_array($parents) ){
                    foreach( $parents as $parent ){
                        $code .= $parent;
                        $invalidateTime = $this->getInvalidate($code);
                        if( $invalidateTime && $invalidateTime > $res['time'] ){
                            return false;
                        }
                        $code .= '_';
                    }
                }
            }
        }

        if( $this->withInvalidationChecks )
            return $res['value'];
        else
            return $res;
        
    }
    
    /**
     * Returns the invalidation time
     *
     * @param string $pCode
    */
    public function getInvalidate( $pCode ){
        $res = $this->get( $pCode.'_i', true );
        return $res;
    }

    
    /**
     * Marks a code as invalidate until $pTime
     *
     * @param string $pCode
     * @param integer $pTime Timestamp. Default is time()
    */
    public function invalidate( $pCode, $pTime = false ){
    
        $this->set( $pCode.'_i', $pTime?$pTime:time(), time()+(3600*24*20), false );
    
    }
    
    /**
     * 
     * Sets a content to the specified cache-key.
     * Kryn uses MemCache or PHP-Caching
     *
     * pTimeout is only in files mode available when activating the $withInvalidationChecks=true
     *
     * @param string $pCode
     * @param mixed $pValue
     * @param integer $pTimeout In seconds. Default is one hour
     */
    public function set( $pCode, $pValue, $pTimeout = false ){
        
        if( !$pTimeout )
            $pTimeout = time()+3600;
        else
            $pTimeout += time();
        
        if( $this->withInvalidationChecks ){
            $pValue = array(
                'timeout' => $pTimeout,
                'time' => time(),
                'value' => $pValue
            );
        }
            
        switch( $this->type ){
            case 'memcached':

                if( $this->memcache ){
                    return $this->memcache->set( $pCode, $pValue, 0, $pTimeout?$pTimeout:null );
                } else if( $this->memcached ){
                    return $this->memcached->set( $pCode, $pValue, $pTimeout?$pTimeout:null);
                }
            
            case 'redis':
            
                return $this->redis->setex( $pCode, $pTimeout, $pValue );

            case 'files':

                $cacheCode = 'krynPhpCache_'.$pCode;
                $varname = '$kcache[\''.$cacheCode.'\'] ';

                $phpCode = "<"."?php \n$varname = ".var_export($pValue,true).";\n ?".">";

                if( class_exists('kryn') )
                    kryn::fileWrite($this->config['files_path'].$pCode.'.php', $phpCode);
                else
                    file_put_contents( $this->config['files_path'].$pCode.'.php', $phpCode );

                return file_exists( $this->config['files_path'].$pCode.'.php' );
                
            case 'apc':

                return apc_store( $pCode, $pValue, time()-$pTimeout );
                
            case 'xcache':
            
                return xcache_set( $pCode, $pValue, time()-$pTimeout );
        }
    }
    
    /**
     * Deletes the cache for specified cache-key.
     *
     * @param string $pCode
     */
    public function delete( $pCode ){
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
                @unlink($this->config['files_path'].$pCode.'.php');
                break;
                
            case 'apc':
            
                return apc_delete( $pCode );
                
            case 'xcache':
            
                return xcache_unset( $pCode );
        }
    }
    
    /**
     * Deletes the cache for specified cache-key.
     *
     * @param string $pCode
     */
    public function clear( $pCode ){
        return $this->delete( $pCode );
    }
}

?>
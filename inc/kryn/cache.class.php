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



class cache {
    
    /**
     * 
     * Returns the content of the specified cache-key
     * @param string $pCode
     * @return string
     * @static
     */
    public static function &get( $pCode ){
        global $cfg, $kcache, $kryn, $modules;

        $mem = $cfg['memcachedEstablished'];
        $pCode = str_replace('..', '', $pCode);
        
        $ref = false;
        
	    $cacheCode = 'krynPhpCache_'.$pCode;
        
        if( !$kcache[$cacheCode] ){
	        if( $mem ){
	            $kcache[$cacheCode] = memcache_get( $cfg['memcachedHandle'], $cacheCode );;
	        } else {
	            if( file_exists( $cfg['files_path'].$pCode.'.php' )){
	                include_once( $cfg['files_path'].$pCode.'.php' );
	            } else {
	                return false;
	            }
	        }
        }

        return $kcache[$cacheCode] ;
    }
    
    /**
     * 
     * Sets a content to the specified cache-key.
     * Kryn uses MemCache or PHP-Caching
     * @param string $pCode
     * @param string $pValue
     * @static
     */
    public static function set( $pCode, $pValue ){
        global $cfg, $kcache;

        $mem = $cfg['memcachedEstablished'];
        $pCode = str_replace('..', '', $pCode);

        if( $mem ){
            memcache_set( $cfg['memcachedHandle'], $pCode, $pValue );
            $kcache[$pCode] = $pValue;
        } else {
            //PHP 
            $kcache['krynPhpCache_'.$pCode] = $pValue;
            $varname = '$kcache[\'krynPhpCache_'.$pCode.'\'] ';
            $phpCode = "<"."?php \n$varname = ".var_export($pValue,true).";\n ?".">";
            kryn::fileWrite($cfg['files_path'].$pCode.'.php', $phpCode);
        }
        
    }
    
    /**
     *
     * Clears the content for specified cache-key.
     * @param string $pCode
     */
    public static function clear( $pCode ){
         global $cfg, $kcache;

        $mem = $cfg['memcachedEstablished'];
        $pCode = str_replace('..', '', $pCode);

        if( $mem ){
            memcache_delete($memcache_obj, 'key_to_delete');
        } else {
            //PHP
            unset($kcache['krynPhpCache_'.$pCode]);
            @unlink($cfg['files_path'].$pCode.'.php');
        }
    }
    
}

?>
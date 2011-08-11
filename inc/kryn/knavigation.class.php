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
 * Navigation class
 * 
 * Layer between Layouts and navigation (pages)
 * 
 * @package Kryn
 * @internal
 * @subpackage Layout
 * @author Kryn.labs <info@krynlabs.com>
 */


class knavigation {
    public $navigations;

    function getAdminLinks( $pParam, $pIsDomain = false ){

        if( $pIsDomain )
            $sql = "SELECT * FROM %pfx%system_pages WHERE domain_rsn= ".$pParam['rsn']." AND prsn = 0 ORDER BY sort";
        else
            $sql = "SELECT * FROM %pfx%system_pages WHERE prsn = ".$pParam['rsn']." ORDER BY sort";
        return dbExfetch( $sql, DB_FETCH_ALL );
    }

    public static function getLinks( $pRsn, $pWithFolders = false, $pDomain = false ){
        global $kryn, $user, $time;

        if(! is_numeric($pRsn) )
            return array();
        
        $code = $pRsn;
        if( $pDomain )
            $code .= '_'.kryn::$domain['rsn'];
        
        $links =& cache::get( 'navigations' );
        
        if( !$links[$code] && !is_array($links[$code]) ){

            $links[$code] = dbExfetch("
            SELECT 
                rsn, prsn, domain_rsn, title, url, type, page_title, layout, sort, visible, access_denied,
                access_from, access_to, access_nohidenavi, access_from_groups
            FROM
                %pfx%system_pages
            WHERE
                prsn = $pRsn
                AND ( type = 0 OR type = 1 OR type = 2)
                
                AND ( 
                    ( type = 2 )
                    OR
                    (
                        type != 2  AND visible = 1
                    )
                )
                AND access_denied != '1'
            ORDER BY sort", -1);

            cache::set('navigations', $links);
        }
        
        foreach( $links[$code] as &$page ){
        
            if( !$pWithFolders && $page['type'] == 2 ) continue;
            if( $pRsn == 0 && $pDomain && ($page['prsn'] != 0 || $page['domain_rsn'] != kryn::$domain['rsn'] ) ) continue;
        
            //permission check
        	if( $page['access_nohidenavi'] != 1 )
        	    $page = kryn::checkPageAccess( $page, false );
            
	        if( $page ){
	            $page[ 'links' ] = self::getLinks( $page['rsn'] );
	            $pages[] = $page;
	        }
        }
        return $pages;
    }


    public static function activePage( $pRsn ){
        global $kryn;
        $isActive = self::_activePage( $kryn->menus[ $pRsn ], $pRsn );
    }

    public static function _activePage( $pages, $pRsn ){
        if(! count($pages) > 0 ) return false;
        if( $page['rsn'] == $pRsn )
            return true;
        else
            return self::_activePage( $page[0], $pRsn );
    }
    
    public static function arrayLevel( $pArray, $pLevel ){
        $page = $pArray;
        return $pArray[ $pLevel-2 ];
    }

    public static function plugin( $pOptions ){
        global $kryn, $user, $cfg;

        $pTemplate = $pOptions['template'];
        $pWithFolders = ($pOptions['folders']==1)?true:false;
        
        $navi = false;
        if( $pOptions['id']+0 > 0 ){;
            $navi = kryn::getPage( $pOptions['id']+0 );
            $start = microtime(true);
            $navi['links'] = self::getLinks( $navi['rsn'], $pWithFolders );
        }

        if( $pOptions['level'] > 1 ){

            $currentLevel = count( $kryn->menus[kryn::$page['rsn']] )+1;

            $page = self::arrayLevel( $kryn->menus[kryn::$page['rsn']], $pOptions['level'] );

            if( $page['rsn'] > 0 )
                $navi = kryn::getPage( $page['rsn'] );
            elseif( $pOptions['level'] == $currentLevel+1 )
                $navi = kryn::$page;

            $navi['links'] = self::getLinks( $navi['rsn'], $pWithFolders, kryn::$domain['rsn'] );
        }

        if( $pOptions['level'] == 1 ){
            $navi['links'] = self::getLinks( 0, $pWithFolders, kryn::$domain['rsn'] );
        }
         
        if( $navi !== false ){
            tAssign("navi", $navi);
            
            return tFetch($pTemplate);
        }

        switch( $pOptions['id'] ){
            case 'history':
                $tpl = (!$pTemplate) ? 'main' : $pTemplate;
                tAssign( 'menus', kryn::readCache('menus') );
                if( file_exists( "inc/template/$tpl" ))
                    return tFetch( $tpl );
                return tFetch("kryn/history/$tpl.tpl");
                break;
        }
    }

}

?>

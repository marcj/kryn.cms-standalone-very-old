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
 * Layer between Layouts and navigation (pages)
 * @author MArc Schmidt <marc@kryn.org>
 */


class krynNavigation {
    public $navigations;

    public static function getLinks($pRsn, $pWithFolders = false, $pDepth = 0, $pDomain = false, $pWithoutCache = false) {

        if (!is_numeric($pRsn))
            return array();

        if (!$pDomain) {
            $pDomain = kryn::$domain['rsn'];
        }

        if ($pWithoutCache == false) {

            $code = $pDomain;
            $code .= '-'.$pRsn;
            $code .= '-'.kryn::$client->id;

            $navigation =& kryn::getCache('navigation-' . $code);
        }

        if ($pWithoutCache == true || !is_array($navigation)) {

            $condition = array(
                array('visible', '=', 1)
            );

            if ($pRsn == 0) {
                $condition[] = 'AND';
                $condition[] = array('domain_rsn', '=', $pDomain);
            }

            if (!$pWithFolders){
                $condition[] = 'AND';
                $condition[] = array('type', 'IN', '0,1');
            }

            if ($pRsn){
                $condition[] = 'OR';
                $condition[] = array('rsn', '=', $pRsn);
            }

            $nodes = krynObjects::getTree('node', $pRsn, $condition, $pDepth, $pDomain, array(
                'fields' => '*',
                'permissionCheck' => true
            ));

            if (count($nodes) > 0){
                foreach ($nodes as &$node) {
                    if ($node['properties']) {
                        $node['properties'] = json_decode($node['properties'], true);
                    }
                }

                if (!$pWithoutCache) {
                    kryn::setCache('navigation-' . $code, $nodes, 60);
                }
            }

        } else {
            $nodes =& $navigation;
        }

        return $nodes;
    }

    public static function arrayLevel($pArray, $pLevel) {
        $page = $pArray;
        return $pArray[$pLevel - 2];
    }

    public static function get($pOptions) {

        $pTemplate = $pOptions['template'];
        $pWithFolders = ($pOptions['folders'] == 1) ? true : false;

        if (!$pTemplate){
            return t('Navigation: No template selected.');
        }

        if(!$mtime = tModTime($pTemplate)){
            return t('Navigation: Template does not exist:').' '.$pTemplate;
        }

        $navigation = false;

        if ($pOptions['id'] + 0 > 0) {
            $navigation =& kryn::getPage($pOptions['id'] + 0);

            if (!$pOptions['noCache'] && kryn::$domainProperties['kryn']['cacheNavigations'] !== 0) {
                $cacheKey =
                    'systemNavigations-' . $navigation['domain_rsn'] . '_' . $navigation['rsn'] . '-' . md5(kryn::$canonical.$mtime);
                $cache =& kryn::getCache($cacheKey);
                if ($cache) return $cache;
            }

            $navigation = self::getLinks($navigation['rsn'], $pWithFolders, $navigation['domain_rsn']);
        }

        if ($pOptions['level'] > 1) {

            $currentLevel = count(kryn::$breadcrumbs) + 1;

            $page = self::arrayLevel(kryn::$breadcrumbs, $pOptions['level']);

            if ($page['rsn'] > 0)
                $navi =& kryn::getPage($page['rsn']);
            elseif ($pOptions['level'] == $currentLevel + 1)
                $navi = kryn::$page;

            if (!$pOptions['noCache'] && kryn::$domainProperties['kryn']['cacheNavigations'] !== 0) {
                $cacheKey =
                    'systemNavigations-' . $navi['domain_rsn'] . '_' . $navi['rsn'] . '-' . md5(kryn::$canonical.$mtime);
                $cache =& kryn::getCache($cacheKey);
                if ($cache) return $cache;
            }

            $navigation = self::getLinks($navi['rsn'], $pWithFolders, kryn::$domain['rsn']);
        }

        if ($pOptions['level'] == 1) {

            if (!$pOptions['noCache'] && kryn::$domainProperties['kryn']['cacheNavigations'] !== 0) {
                $cacheKey = 'systemNavigations-' . kryn::$page['domain_rsn'] . '_0-' . md5(kryn::$canonical.$mtime);
                $cache =& kryn::getCache($cacheKey);
                if (false && $cache) return $cache;
            }

            $navigation = array('title' => 'Root');
            $navigation['_children'] = self::getLinks(0, $pWithFolders, kryn::$domain['rsn']);
        }

        if ($navi !== false) {

            tAssign("navi", $navigation);
            tAssign("navigation", $navigation);

            if (kryn::$domainProperties['kryn']['cacheNavigations'] !== 0) {
                $res = tFetch($pTemplate);
                kryn::setCache($cacheKey, $res, 10);
                return $res;
            } else {
                return tFetch($pTemplate);
            }
        }

        switch ($pOptions['id']) {

            case 'history':
            case 'hierarchy':
            case 'breadcrumb':
                return tFetch($pTemplate);
        }
    }

}

?>

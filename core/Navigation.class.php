<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@Kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

namespace Core;

/**
 * Navigation class
 * Layer between Layouts and navigation (pages)
 * @author MArc Schmidt <marc@Kryn.org>
 */


class Navigation {

    public static function arrayLevel($pArray, $pLevel) {
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
            $navigation =& Kryn::getPage($pOptions['id'] + 0);

            if (!$navigation) return 'page_not_found';


//            if (!$pOptions['noCache'] && Kryn::$domainProperties['Kryn']['cacheNavigations'] !== 0) {
//                $cacheKey =
//                    'systemNavigations-' . $navigation->getDomainId() . '_' . $navigation->getId() . '-'
//                        . md5(Kryn::$canonical.$mtime);
//                $cache =& Kryn::getCache($cacheKey);
//                if ($cache) return $cache;
//            }

            //$navigation = self::getLinks($navigation->getId(), $pWithFolders, $navigation->getDomainId());
        }

        if ($pOptions['level'] > 1) {

            $currentLevel = count(Kryn::$breadcrumbs) + 1;
            $page = self::arrayLevel(Kryn::$breadcrumbs, $pOptions['level']);

            if ($page && $page->getId() > 0)
                $navigation = Kryn::getPage($page->getId());
            elseif ($pOptions['level'] == $currentLevel + 1)
                $navigation = Kryn::$page;
//
//            if (!$pOptions['noCache'] && Kryn::$domainProperties['Kryn']['cacheNavigations'] !== 0) {
//                $cacheKey =
//                    'systemNavigations-' . $navigation->getDomainId() . '_' . $navigation->getId() . '-' .
//                        md5(Kryn::$canonical.$mtime);
//                $cache =& Kryn::getCache($cacheKey);
//                if ($cache) return $cache;
//            }
        }

        if ($pOptions['level'] == 1) {

            /*if (!$pOptions['noCache'] && Kryn::$domainProperties['Kryn']['cacheNavigations'] !== 0) {
                $cacheKey = 'systemNavigations-' . Kryn::$page->getDomainId() . '_0-' . md5(Kryn::$canonical.$mtime);
                $cache =& Kryn::getCache($cacheKey);
                if (false && $cache) return $cache;
            }*/

            $navigation = NodeQuery::create()->findRoot(Kryn::$domain->getId());
        }

        if ($navigation !== false) {

            tAssign('navigation', $navigation);

            if (Kryn::$domainProperties['kryn']['cacheNavigations'] !== 0) {
                $res = tFetch($pTemplate);
                //Kryn::setCache($cacheKey, $res, 10);
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

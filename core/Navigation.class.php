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

        $cacheKey = 'core/navigation/' . Kryn::$page->getDomainId().'.'.Kryn::$page->getId() . '_'.md5(json_encode($pOptions));

        if (!$pTemplate){
            return t('Navigation: No template selected.');
        }

        if(!$mtime = tModTime($pTemplate)){
            return t('Navigation: Template does not exist:').' '.$pTemplate;
        }

        $navigation = false;
        $fromCache = false;

        if (!$pOptions['noCache'] && Kryn::$domainProperties['Kryn']['cacheNavigations'] !== 0){

            $navigation = Kryn::getFastCache($cacheKey);
            if ($navigation && is_array($navigation) && $navigation['mtime'] == $mtime)
                return $navigation['data'];

        } else {
            $navigation = Kryn::getFastCache($cacheKey);
            if ($navigation && $navigation['mtime'] == $mtime){
                $navigation = unserialize($navigation['data']);
                if ($navigation)
                    $fromCache = true;
            }
        }

        if (!$navigation){
            if ($pOptions['id'] + 0 > 0) {
                $navigation =& Kryn::getPage($pOptions['id'] + 0);

                if (!$navigation) return null;
            }

            if ($pOptions['level'] > 1) {

                $currentLevel = count(Kryn::$breadcrumbs) + 1;
                $page = self::arrayLevel(Kryn::$breadcrumbs, $pOptions['level']);

                if ($page && $page->getId() > 0)
                    $navigation = Kryn::getPage($page->getId());
                elseif ($pOptions['level'] == $currentLevel + 1)
                    $navigation = Kryn::$page;
            }

            if ($pOptions['level'] == 1) {
                $navigation = NodeQuery::create()->findRoot(Kryn::$domain->getId());
            }
        }


        if ($navigation !== false) {

            tAssign('navigation', $navigation);

            if (Kryn::$domainProperties['kryn']['cacheNavigations'] !== 0) {
                $res = tFetch($pTemplate);
                //Kryn::setCache($cacheKey, $res, 10);
                $html = $res;
            } else {
                $html = tFetch($pTemplate);
            }

            if (!$pOptions['noCache'] && Kryn::$domainProperties['Kryn']['cacheNavigations'] !== 0){
                Kryn::setFastCache($cacheKey, array('mtime' => $mtime, 'data' => $html));
            } else if (!$fromCache){
                Kryn::setFastCache($cacheKey, array('mtime' => $mtime, 'data' => serialize($navigation)));
            }

            return $html;
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

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

use Core\Models\NodeQuery;

/**
 * Navigation class
 * Layer between Layouts and navigation (pages)
 * @author MArc Schmidt <marc@Kryn.org>
 */

class Navigation
{
    public static function arrayLevel($pArray, $pLevel)
    {
        return $pArray[$pLevel - 2];
    }

    public static function get($pOptions)
    {
        $view = $pOptions['template'] ?: $pOptions['view'];
        $withFolders = ($pOptions['folders'] == 1) ? true : false;

        $cacheKey = 'core/navigation/' . Kryn::$page->getDomainId().'.'.Kryn::$page->getId() . '_'.md5(json_encode($pOptions));

        $navigation = false;
        $fromCache = false;

        $viewPath = Kryn::resolvePath($view, 'Views/');

        if (!file_exists($viewPath)) {
            throw new \FileNotFoundException(sprintf('File `%s` not found.', $viewPath));
        } else {
            $mtime = filemtime($viewPath);
        }

        if (!$pOptions['noCache'] && Kryn::$domainProperties['core']['cacheNavigations'] !== 0) {

            $cache = Kryn::getDistributedCache($cacheKey);
            if ($cache && is_array($cache) && $cache['html'] !== null && $cache['mtime'] == $mtime)
                return $cache['html'];

        }

        $cache = Kryn::getDistributedCache($cacheKey);

        if ($cache && $cache['object'] && $cache['mtime'] == $mtime) {
            $navigation = unserialize($cache['object']);
            $fromCache = true;
        }

        if (!$navigation && $pOptions['id'] != 'breadcrumb' && ($pOptions['id'] || $pOptions['level'])) {

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

            $data['navigation'] = $navigation;
            $html = Kryn::getInstance()->renderView($view, $data);

            if (!$pOptions['noCache'] && Kryn::$domainProperties['core']['cacheNavigations'] !== 0) {
                Kryn::setDistributedCache($cacheKey, array('mtime' => $mtime, 'html' => $html));
            } elseif (!$fromCache) {
                Kryn::setDistributedCache($cacheKey, array('mtime' => $mtime, 'object' => serialize($navigation)));
            }

            return $html;
        }

        //no navigation found, probably the template just uses the breadcrumb
        return Kryn::getInstance()->renderView($view);
    }

}

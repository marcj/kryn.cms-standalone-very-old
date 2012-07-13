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

    /**
     * @static
     * @param $pId
     * @param bool $pWithFolders
     * @param int $pDepth
     * @param SystemDomain $pDomain
     * @param bool $pWithoutCache
     * @return array|string
     */
    public static function getLinks($pId, $pWithFolders = false, $pDepth = 0, $pDomain = null, $pWithoutCache = false) {

        if (!is_numeric($pId))
            return array();

        if (!$pDomain) {
            $pDomain = kryn::$domain->getId();
        }


        $s1 = new SystemPage();
        $s1->setDomainId(3);
        $s1->setTitle('Root 1');
        $s1->save();

        $s2 = new SystemPage();
        $s2->setDomainId(3);
        $s2->setTitle('Root 2');

        $sub = new SystemPage();
        $sub->setTitle('Sub #1 of Root 2');
        $sub->insertAsFirstChildOf($s2);

        $sub = new SystemPage();
        $sub->setTitle('Sub #2 of Root 2');
        $sub->insertAsFirstChildOf($s2);

        $s2->save();

        $s3 = new SystemPage();
        $s3->setDomainId(3);
        $s3->setTitle('Root 3');

        $s3->save();

        $root = SystemPageQuery::create()->findRoot(3);
        var_dump($root);
        exit;
        foreach ($root->getIterator() as $node) {
            echo str_repeat(' ', $node->getLevel()) . $node->getTitle() . "<br/>";
        }
        exit;
        $blog = SystemPageQuery::create()->findPk(1);

        $nodes = SystemPageQuery::create()->findTree(1);

        //print $nodes->toJSON();
        var_dump($nodes);
        exit;

        return $nodes;

        if ($pWithoutCache == false) {

            $code = $pDomain;
            $code .= '-'.$pId;
            $code .= '-'.kryn::$client->id;

            $navigation =& kryn::getCache('navigation-' . $code);
        }

        if ($pWithoutCache == true || !is_array($navigation)) {

            $condition = array(
                array('visible', '=', 1)
            );

            if ($pId == 0) {
                $condition[] = 'AND';
                $condition[] = array('domain_id', '=', $pDomain);
            }

            if (!$pWithFolders){
                $condition[] = 'AND';
                $condition[] = array('type', 'IN', '0,1');
            }

            if ($pId){
                $condition[] = 'OR';
                $condition[] = array('id', '=', $pId);
            }

            $nodes = SystemPageQuery::create()->findRoot($pId);

            /*
            $nodes = krynObjects::getTree('node', $pId, $condition, $pDepth, $pDomain, array(
                'fields' => '*',
                'permissionCheck' => true
            ));*/

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
                    'systemNavigations-' . $navigation['domain_id'] . '_' . $navigation['id'] . '-' . md5(kryn::$canonical.$mtime);
                $cache =& kryn::getCache($cacheKey);
                if ($cache) return $cache;
            }

            $navigation = self::getLinks($navigation['id'], $pWithFolders, $navigation['domain_id']);
        }

        if ($pOptions['level'] > 1) {

            $currentLevel = count(kryn::$breadcrumbs) + 1;

            $page = self::arrayLevel(kryn::$breadcrumbs, $pOptions['level']);

            if ($page['id'] > 0)
                $navi =& kryn::getPage($page['id']);
            elseif ($pOptions['level'] == $currentLevel + 1)
                $navi = kryn::$page;

            if (!$pOptions['noCache'] && kryn::$domainProperties['kryn']['cacheNavigations'] !== 0) {
                $cacheKey =
                    'systemNavigations-' . $navi->getDomainId() . '_' . $navi->getId() . '-' . md5(kryn::$canonical.$mtime);
                $cache =& kryn::getCache($cacheKey);
                if ($cache) return $cache;
            }

            $navigation = self::getLinks($navi->getId(), $pWithFolders, kryn::$domain->getId());
        }

        if ($pOptions['level'] == 1) {

            if (!$pOptions['noCache'] && kryn::$domainProperties['kryn']['cacheNavigations'] !== 0) {
                $cacheKey = 'systemNavigations-' . kryn::$page->getDomainId() . '_0-' . md5(kryn::$canonical.$mtime);
                $cache =& kryn::getCache($cacheKey);
                if (false && $cache) return $cache;
            }

            $navigation = array('title' => 'Root');
            $navigation['_children'] = self::getLinks(0, $pWithFolders, kryn::$domain->getId());
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

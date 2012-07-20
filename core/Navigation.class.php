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
    public $navigations;

    /**
     * @static
     * @param \Page $pParentPage
     * @param bool $pWithFolders
     * @param \mDomain $pDomain
     * @param bool $pWithoutCache
     * @return mixed
     */
    public static function getLinks($pParentPage = null, $pWithFolders = false, $pDomain = null, $pWithoutCache = false) {

        if (!$pDomain) {
            $pDomain = Kryn::$domain->getId();
        }

        $query = \PageQuery::create()
            ->inTree($pDomain);

        if (!$pWithFolders){
            $query->filterByType(array(1,2));
        }

        //$query->filterByLvl(array('min' => 0));

        $items = $query->find();

        //var_dump($root);

        exit;

        return;

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
        foreach ($root->get() as $node) {
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
            $code .= '-'.Kryn::$client->id;

            $navigation =& Kryn::getCache('navigation-' . $code);
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
                    Kryn::setCache('navigation-' . $code, $nodes, 60);
                }
            }

        } else {
            $nodes =& $navigation;
        }

        return $nodes;
    }

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
                $navigation =& Kryn::getPage($page->getId());
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

            $navigation = \PageQuery::create()->findRoot(Kryn::$domain->getId());
        }

        if ($navigation !== false) {

            tAssign('navigation', $navigation);

            if (Kryn::$domainProperties['kryn']['cacheNavigations'] !== 0) {
                $res = tFetch($pTemplate);
                Kryn::setCache($cacheKey, $res, 10);
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

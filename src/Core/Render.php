<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@Kryn.org>
 *
 * To get the full copyright and license information, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

namespace Core;

use Core\Models\Content;
use Core\Render\TypeNotFoundException;
use Propel\Runtime\Map\TableMap;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Html render class
 *
 * @author MArc Schmidt <marc@Kryn.org>
 *
 * @events onRenderSlot
 *
 */

class Render
{
    /**
     * Cache of the current contents stage.
     *
     * @var array
     */
    public static $contents;

    /**
     *
     * Build the HTML for given page. If pPageId is a deposit, it returns with Kryn/blankLayout.tpl as layout, otherwise
     * it returns the layouts with all it contents.
     *
     * @static
     *
     * @param  bool         $pageId
     * @param  bool         $slotId
     * @param  bool         $properties
     *
     * @return mixed|string
     */
    public static function renderPage($pageId = false, $slotId = false, $properties = false)
    {
        if (self::$contents) {
            $oldContents = self::$contents;
        }

        Kryn::$forceKrynContent = true;
        Kryn::getLogger()->addDebug('renderPage(' . $pageId . ', ' . $slotId . ')');

        if ($pageId == Kryn::$page->getId()) {
            //endless loop
            return 'You produced a endless loop. Please check your latest changed pages.';
        }

        if (!$pageId) {

            $pageId = Kryn::$page->getId();

        } elseif ($pageId != Kryn::$page->getId()) {

            $oldPage = Kryn::$page;
            Kryn::$page = Kryn::getPage($pageId, true);

            if (!Kryn::$page) {
                Kryn::$page = $oldPage;

                return 'page_not_found';
            }

            Kryn::$nestedLevels[] = Kryn::$page;
        }

        Kryn::getEventDispatcher()->dispatch('core/render/contents/pre');

        if (file_exists($file = 'css/_pages/' . $pageId . '.css')) {
            Kryn::getResponse()->addCssFile($file);
        }

        if (file_exists($file = 'js/_pages/' . $pageId . '.js')) {
            Kryn::getResponse()->addJsFile($file);
        }

        self::$contents[$slotId] =& PageController::getSlotContents($slotId, $slotId);

        if (Kryn::$page->getType() == 3) { //deposit
            Kryn::$page->setLayout('core/blankLayout.tpl');
        }

        $arguments = array($pageId, $slotId, &self::$contents[$slotId]);
        Kryn::getEventDispatcher()->dispatch('core/render/contents', new GenericEvent($arguments));

        if ($slotId) {
            $html = self::renderContents(self::$contents[$slotId], $properties);
        } else {
            $html = tFetch(Kryn::$page->getLayout());
        }

        if ($oldContents) {
            self::$contents = $oldContents;
        }
        if ($oldPage) {
            Kryn::$page = $oldPage;
            array_pop(Kryn::$nestedLevels);
        }
        Kryn::$forceKrynContent = false;

        $arguments = array($pageId, $slotId, &$html);
        Kryn::getEventDispatcher()->dispatch('core/render/contents/post', new GenericEvent($arguments));

        return $html;
    }

    /**
     * Build HTML for given contents.
     *
     * @param array $contents
     * @param array $slotProperties
     *
     * @return string
     * @internal
     */
    public static function renderContents(&$contents, $slotProperties)
    {
        $contents2 = array();

        if (!($contents instanceof \Traversable)) {
            return;
        }

        foreach ($contents as $content) {

            $access = true;

            if (
                ($content->getAccessFrom() + 0 > 0 && $content->getAccessFrom() > time()) ||
                ($content->getAccessTo() + 0 > 0 && $content->getAccessTo() < time())
            ) {
                $access = false;
            }

            if ($content->getHide()) {
                $access = false;
            }

            if ($access && $content->getAccessFromGroups()) {

                $access = false;
                $groups = ',' . $content->getAccessFromGroups() . ',';

                $userGroups = Kryn::getClient()->getUser()->getUserGroups();

                foreach ($userGroups as $group) {
                    if (strpos($groups, ',' . $group->getGroupId() . ',') !== false) {
                        $access = true;
                        break;
                    }
                }

                if (!$access) {
                    $adminGroups = Kryn::getClient()->getUser()->getUserGroups();
                    foreach ($adminGroups as $group) {
                        if (strpos($groups, ',' . $group->getGroupId() . ',') !== false) {
                            $access = true;
                            break;
                        }
                    }
                }
            }

            if ($access) {
                $contents2[] = $content;
            }
        }

        $count = count($contents2);
        /*
         * Compatibility
         */
        $data['layoutContentsMax'] = $count;
        $data['layoutContentsIsFirst'] = true;
        $data['layoutContentsIsLast'] = false;
        $data['layoutContentsId'] = $slotProperties['id'];
        $data['layoutContentsName'] = $slotProperties['name'];

        $i = 0;

        //$oldContent = $tpl->getTemplateVars('content');
        Kryn::getEventDispatcher()->dispatch('core/render/slot/pre', new GenericEvent($data));

        $html = '';

        if ($count > 0) {
            foreach ($contents2 as &$content) {
                if ($i == $count) {
                    $data['layoutContentsIsLast'] = true;
                }

                if ($i > 0) {
                    $data['layoutContentsIsFirst'] = false;
                }

                $i++;
                $data['layoutContentsIndex'] = $i;

                $html .= self::renderContent($content, $data);

            }
        }

        $argument = array($data, &$html);
        Kryn::getEventDispatcher()->dispatch('core/render/slot', new GenericEvent($argument));
        Event::fire('onRenderSlot', $argument);

        if ($slotProperties['assign'] != "") {
            Kryn::getInstance()->assign($slotProperties['assign'], $html);
            return '';
        }

        return $html;

    }

    /**
     * Build HTML for given content.
     *
     * @param Content $content
     * @param array   $parameters
     *
     * @return string
     * @throws Render\TypeNotFoundException
     */
    public static function renderContent(Content $content, $parameters = array())
    {
        $type = $content->getType();
        $class = 'Core\\Render\\Type' . ucfirst($type);

        if (class_exists($class)) {
            /** @var \Core\Render\TypeInterface $typeRenderer */
            $typeRenderer = new $class($content, $parameters);
            $html = $typeRenderer->render();
        } else {
            throw new TypeNotFoundException(sprintf(
                'Type renderer for `%s` not found. [%s]',
                $content->getType(),
                json_encode($content)
            ));
        }

        $data['content'] = $content->toArray(TableMap::TYPE_STUDLYPHPNAME);
        $data['parameter'] = $parameters;
        $data['html'] = $html;

        Kryn::getEventDispatcher()->dispatch('core/render/content/pre', new GenericEvent($data));

        $unsearchable = false;
        if ((!is_array($content->getAccessFromGroups()) && $content->getAccessFromGroups() != '') ||
            (is_array($content->getAccessFromGroups()) && count($content->getAccessFromGroups()) > 0) ||
            ($content->getAccessFrom() > 0 && $content->getAccessFrom() > time()) ||
            ($content->getAccessTo() > 0 && $content->getAccessTo() < time()) ||
            $content->getUnsearchable()
        ) {
            $unsearchable = true;
        }

        Event::fire('onRenderContent', $argument);

        if ($content->getTemplate() == '' || $content->getTemplate() == '-') {
            if ($unsearchable) {
                $result = '<!--unsearchable-begin-->' . $data['html'] . '<!--unsearchable-end-->';
            }
        } else {
            $result = Kryn::getInstance()->renderView($content->getTemplate(), $data);

            if ($unsearchable) {
                $result = '<!--unsearchable-begin-->' . $result . '<!--unsearchable-end-->';
            }
        }

        $argument = array(&$result, $data);
        Kryn::getEventDispatcher()->dispatch('core/render/content', new GenericEvent($argument));

        return $result;
    }

    public static function updateDomainCache()
    {
        $res = dbQuery('SELECT * FROM ' . pfx . 'system_domain');
        $domains = array();

        while ($domain = dbFetch($res, 1)) {

            $code = $domain['domain'];
            $lang = "";
            if ($domain['master'] != 1) {
                $lang = '_' . $domain['lang'];
                $code .= $lang;
            }

            $domains[$code] = $domain['id'];

            $alias = explode(",", $domain['alias']);
            if (count($alias) > 0) {
                foreach ($alias as $ad) {
                    $domainName = str_replace(' ', '', $ad);
                    if ($domainName != '') {
                        $domains[$domainName . $lang] = $domain['id'];
                    }
                }
            }

            $redirects = explode(",", $domain['redirect']);
            if (count($redirects) > 0) {
                foreach ($redirects as $redirect) {
                    $domainName = str_replace(' ', '', $redirect);
                    if ($domainName != '') {
                        $domains['_redirects'][$domainName] = $domain['id'];
                    }
                }
            }

            Kryn::deleteCache('systemDomain-' . $domain['id']);
        }
        Kryn::setCache('systemDomains', $domains);
        dbFree($res);

        return $domains;
    }

    public static function updateMenuCache($domainRsn)
    {
        $resu = dbQuery(
            "SELECT id, title, url, pid FROM " . pfx . "system_node WHERE
                         domain_id = $domainRsn AND (type = 0 OR type = 1 OR type = 4)"
        );
        $res = array();
        while ($page = dbFetch($resu, 1)) {

            if ($page['type'] == 0) {
                $res[$page['id']] = self::getParentMenus($page);
            } else {
                $res[$page['id']] = self::getParentMenus($page, true);
            }

        }

        Kryn::setCache("menus-$domainRsn", $res);
        Kryn::invalidateCache('navigation_' . $domainRsn);

        dbFree($resu);

        return $res;
    }

    public static function getParentMenus($page, $allParents = false)
    {
        $pid = $page['parent_id'];
        $res = array();
        while ($pid != 0) {
            $parent_page =
                dbExfetch("SELECT id, title, url, pid, type FROM " . pfx . "system_node WHERE id = " . $pid, 1);
            if ($parent_page['type'] == 0 || $parent_page['type'] == 1 || $parent_page['type'] == 4) {
                //page or link or page-mount
                array_unshift($res, $parent_page);
            } elseif ($allParents) {
                array_unshift($res, $parent_page);
            }
            $pid = $parent_page['parent_id'];
        }

        return $res;
    }

    public static function updateUrlCache($domainRsn)
    {
        $domainRsn = $domainRsn + 0;

        $resu = dbQuery(
            "SELECT id, title, url, type, link FROM " . pfx . "system_node WHERE domain_id = $domainRsn AND parent_id IS NULL"
        );
        $res = array('url' => array(), 'id' => array());

        $domain = Kryn::getDomain($domainRsn);

        while ($page = dbFetch($resu)) {

            $page = self::__pageModify($page, array('realurl' => ''));
            $newRes = self::updateUrlCacheChildren($page, $domain);
            $res['url'] = array_merge($res['url'], $newRes['url']);
            $res['id'] = array_merge($res['id'], $newRes['id']);
        }

        $aliasRes = dbQuery('SELECT node_id, url FROM ' . pfx . 'system_node_alias WHERE domain_id = ' . $domainRsn);
        while ($row = dbFetch($aliasRes)) {
            $res['alias'][$row['url']] = $row['node_id'];
        }

        self::updatePage2DomainCache();
        Kryn::setCache("core/node-ids-to-url-$domainRsn", $res);
        dbFree($aliasRes);
        dbFree($resu);

        return $res;
    }

    public static function updatePage2DomainCache()
    {
        $r2d = array();
        $res = dbQuery('SELECT id, domain_id FROM ' . pfx . 'system_node ');

        while ($row = dbFetch($res)) {
            $r2d[$row['domain_id']] .= $row['id'] . ',';
        }
        dbFree(res);

        Kryn::setDistributedCache('core/node/toDomains', $r2d);

        return $r2d;
    }

    public static function updateUrlCacheChildren($page, $domain = false)
    {
        $res = array('url' => array(), 'id' => array(), 'r2d' => array());

        if ($page['type'] < 2) { //page or link or folder
            if ($page['realurl'] != '') {
                $res['url']['url=' . $page['realurl']] = $page['id'];
                $res['id'] = array('id=' . $page['id'] => $page['realurl']);
            } else {
                $res['id'] = array('id=' . $page['id'] => $page['url']);
            }
        }

        $page2s = dbExfetchAll(
            "SELECT id, title, url, type, link
                                         FROM " . pfx . "system_node
                             WHERE parent_id = " . $page['id']
        );

        if (is_array($page2s)) {
            foreach ($page2s as $page2) {

                Kryn::deleteCache('page_' . $page2['id']);

                $page2 = self::__pageModify($page2, $page);
                $newRes = self::updateUrlCacheChildren($page2);

                $res['url'] = array_merge($res['url'], $newRes['url']);
                $res['id'] = array_merge($res['id'], $newRes['id']);
                $res['r2d'] = array_merge($res['r2d'], $newRes['r2d']);

            }
        }

        return $res;
    }

    public static function __pageModify($page2, $page)
    {
        if ($page2['type'] == 0) {
            $del = '';
            if ($page['realurl'] != '') {
                $del = $page['realurl'] . '/';
            }
            $page2['realurl'] = $del . $page2['url'];

        } elseif ($page2['type'] == 1) { //link
            if ($page2['url'] == '') { //if empty, use parent-url else use url-hiarchy
                $page2['realurl'] = $page['realurl'];
            } else {
                $del = '';
                if ($page['realurl'] != '') {
                    $del = $page['realurl'] . '/';
                }
                $page2['realurl'] = $del . $page2['url'];
            }

            $page2['prealurl'] = $page2['link'];
        } elseif ($page2['type'] != 3) { //no deposit
            //ignore the hiarchie-item
            $page2['realurl'] = $page['realurl'];
        }

        return $page2;
    }

}

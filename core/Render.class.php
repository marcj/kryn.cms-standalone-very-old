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

use \Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Html render class
 * @author MArc Schmidt <marc@Kryn.org>
 *
 * @events onRenderSlot
 *
 */


class Render {


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
     * @param bool $pPageId
     * @param bool $pSlotId
     * @param bool $pProperties
     * @return mixed|string
     */
    public static function renderPage($pPageId = false, $pSlotId = false, $pProperties = false) {

        if (self::$contents) {
            $oldContents = self::$contents;
        }

        Kryn::$forceKrynContent = true;
        Kryn::getLogger()->addDebug('renderPage('.$pPageId.', '.$pSlotId.')');

        if ($pPageId == Kryn::$page->getId()) {
            //endless loop
            return 'You produced a endless loop. Please check your latest changed pages.';
        }

        if (!$pPageId) {

            $pPageId = Kryn::$page->getId();

        } else if ($pPageId != Kryn::$page->getId()) {

            $oldPage = Kryn::$page;
            Kryn::$page = Kryn::getPage($pPageId, true);

            if (!Kryn::$page){
                Kryn::$page = $oldPage;
                return 'page_not_found';
            }

            Kryn::$nestedLevels[] = Kryn::$page;
        }

        Kryn::getEventDispatcher()->dispatch('core.render-contents-pre');

        if (file_exists($file = 'css/_pages/' . $pPageId . '.css'))
            Kryn::getResponse()->addCssFile($file);

        if (file_exists($file = 'js/_pages/' . $pPageId . '.js'))
            Kryn::getResponse()->addJsFile($file);

        self::$contents[$pSlotId] =& PageController::getSlotContents($pSlotId, $pSlotId);

        if (Kryn::$page->getType() == 3) { //deposit
            Kryn::$page->setLayout('core/blankLayout.tpl');
        }

        $arguments = array($pPageId, $pSlotId, &self::$contents[$pSlotId]);
        Kryn::getEventDispatcher()->dispatch('core.render-contents', new GenericEvent($arguments));

        if ($pSlotId) {
            $html = self::renderContents(self::$contents[$pSlotId], $pProperties);
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


        $arguments = array($pPageId, $pSlotId, &$html);
        Kryn::getEventDispatcher()->dispatch('core.render-contents-post', new GenericEvent($arguments));

        return $html;
    }

    /**
     * Build HTML for given contents.
     *
     * @param array $pContents
     * @param array $pSlotProperties
     *
     * @return string
     * @internal
     */
    public static function renderContents(&$pContents, $pSlotProperties) {
        global $client, $adminClient;

        $contents = array();
        Kryn::getEventDispatcher()->dispatch('core.render-contents', new GenericEvent($pContents));

        foreach ($pContents as $content) {

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

                foreach ($userGroups as $group){
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
                $contents[] = $content;
            }
        }


        $count = count($contents);
        /*
         * Compatiblity
         */
        tAssign('layoutContentsMax', $count);
        tAssign('layoutContentsIsFirst', true);
        tAssign('layoutContentsIsLast', false);
        tAssign('layoutContentsId', $pSlotProperties['id']);
        tAssign('layoutContentsName', $pSlotProperties['name']);


        $slot = $pSlotProperties;
        $slot['maxItems'] = $count;
        $slot['isFirst'] = true;
        $slot['isLast'] = false;

        $i = 0;

        //$oldContent = $tpl->getTemplateVars('content');
        $argument = array($slot);
        Event::fire('onBeforeRenderSlot', $argument);

        $html = '';

        if ($count > 0) {
            foreach ($contents as &$content) {
                if ($i == $count) {
                    tAssign('layoutContentsIsLast', true);
                    $slot['isLast'] = true;
                }

                if ($i > 0) {
                    tAssign('layoutContentsIsFirst', false);
                    $slot['isFirst'] = false;
                }

                $i++;
                tAssign('layoutContentsIndex', $i);
                $slot['index'] = $i;

                tAssignRef('slot', $slot);
                Kryn::$slot = $slot;
                $html .= self::renderContent($content, $slot);

            }
        }

        $argument = array($slot, &$html);
        Event::fire('onRenderSlot', $argument);

        if ($pSlotProperties['assign'] != "") {
            tAssignRef($pSlotProperties['assign'], $html);
            return;
        }

        return $html;

    }

    /**
     * Build HTML for given content.
     *
     * @static
     * @param $pContent
     * @param $pProperties
     * @return mixed|string
     */
    public static function renderContent(Content $pContent, $pProperties) {

        $content =& $pContent;

        tAssignRef('content', $content);

        $argument = array(&$content, $pProperties);

        Kryn::getEventDispatcher()->dispatch('core.render-content', new GenericEvent($pContent));
        Event::fire('preRenderContent', $argument);

        /*

        switch (strtolower($content->getType())) {
            case 'text':
                //replace all [[ with a workaround, so that multilanguage will not fetch.
                //$content->setContent(str_replace('[[', '[<!-- -->[', $content->getContent()));


                break;
            case 'html':
                $content->setContent(str_replace('[[', '\[[', $content->getContent()));

                break;
            case 'navigation':

                if ($content->getContent()){
                    $temp = json_decode($content->getContent(), 1);
                    $temp['id'] = $temp['entryPoint']+0;
                    unset($temp['entryPoint']);

                    $content->setContent(krynNavigation::get($temp));
                }

                break;
            case 'picture':

                $temp = explode('::', $content['content']);

                if ($temp[0] != '' && $temp[0] != 'none') {
                    $opts = json_decode($temp[0], true);
                    $align = ($opts['align']) ? $opts['align'] : 'left';
                    $alt = ($opts['alt']) ? $opts['alt'] : '';
                    $title = ($opts['title']) ? $opts['title'] : '';

                    $imagelink = $temp[1];

                    if ($opts['width'] && $opts['height']) {
                        $imagelink = resizeImageCached($imagelink, $opts['width'] . 'x' . $opts['height']);
                    } elseif ($pProperties['picturedimension'] && $opts['forcedimension'] != "1") {
                        $imagelink = resizeImageCached($imagelink, $pProperties['picturedimension']);
                    }

                    $link = '';
                    if ($opts['link'] + 0 > 0) {
                        $link = Kryn::pageUrl($opts['link']);
                    } else if ($opts['link'] != '') {
                        $link = $opts['link'];
                    }

                    if ($link == '') {
                        $content['content'] =
                            '<div style="text-align: ' . $align . ';"><img src="' . $imagelink . '" alt="' . $alt .
                            '" title="' . $title . '" /></div>';
                    } else {
                        $content['content'] =
                            '<div style="text-align: ' . $align . ';"><a href="' . $link . '" ><img src="' .
                            $imagelink . '" alt="' . $alt . '" title="' . $title . '" /></a></div>';
                    }

                } else {
                    $content['content'] = '<img src="' . $temp[1] . '" />';
                }

                break;
            case 'template':

                if (substr($content['content'], 0, 1) == '/')
                    $content['content'] = substr($content['content'], 1);

                $file = str_replace('..', '', $content['content']);
                if (file_exists(PATH . PATH_MEDIA . $file)) {
                    $content['content'] = tFetch($file);
                }
                break;
            case 'pointer':

                if ($content['content'] + 0 > 0 && $content['content'] + 0 != Kryn::$page['id'])
                    $content['content'] = self::renderContents($content['content'] + 0, 1, $pProperties);

                break;
            case 'layoutelement':

                $oldContents = self::$contents;

                $layoutcontent = json_decode($content['content'], true);
                self::$contents = $layoutcontent['contents'];
                $content['content'] = tFetch($layoutcontent['layout']);

                self::$contents = $oldContents;

                break;
            case 'plugin':


                $t = explode('::', $content['content']);
                $config = $content['content'];

                $content['content'] = '<div>Plugin not found.</div>';

                if (Kryn::$modules[$t[0]]) {

                    $config = substr($config, strlen($t[0]) + 2 + strlen($t[1]) + 2);
                    $config = json_decode($config, true);

                    if (method_exists(Kryn::$modules[$t[0]], $t[1]))
                        $content['content'] = Kryn::$modules[$t[0]]->$t[1]($config);

                    // if in seachindex mode and plugin is configured unsearchable the kill plugin output
                    if (isset(Kryn::$configs[$t[0]]['plugins'][$t[1]][3]) &&
                        Kryn::$configs[$t[0]]['plugins'][$t[1]][3] == true
                    )
                        $content['content'] = Kryn::$unsearchableBegin . $content['content'] . Kryn::$unsearchableEnd;

                }

                break;
            case 'php':
                $temp = ob_get_contents();
                ob_end_clean();
                ob_start();
                eval($content['content']);
                $content['content'] = ob_get_contents();
                ob_end_clean();
                ob_start();
                print $temp;
                break;
        }
        */

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

        $html = '';
        if ($content->getTemplate() == '' || $content->getTemplate() == '-') {
            if ($unsearchable)
                $html = '<!--unsearchable-begin-->' . $content->getContent() . '<!--unsearchable-end-->';
            else
                $html = $content->getContent();
        } else {

            tAssign('content', tFetch('core/content.tpl'));
            $template = $content->getTemplate();
            if ($unsearchable)
                $html = '<!--unsearchable-begin-->' . tFetch($template) . '<!--unsearchable-end-->';
            else
                $html = tFetch($template);
        }


        $argument = array($pContent, $pProperties, &$html);
        Event::fire('onAfterRenderContent', $argument);

        return $html;
    }



    public static function updateDomainCache() {
        $res = dbQuery('SELECT * FROM '.pfx.'system_domain');
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
                    if ($domainName != '')
                        $domains['_redirects'][$domainName] = $domain['id'];
                }
            }

            Kryn::deleteCache('systemDomain-' . $domain['id']);
        }
        Kryn::setCache('systemDomains', $domains);
        dbFree($res);
        return $domains;
    }



    public static function updateMenuCache($pDomainRsn) {
        $resu = dbQuery("SELECT id, title, url, pid FROM ".pfx."system_node WHERE
        				 domain_id = $pDomainRsn AND (type = 0 OR type = 1 OR type = 4)");
        $res = array();
        while ($page = dbFetch($resu, 1)) {

            if ($page['type'] == 0)
                $res[$page['id']] = self::getParentMenus($page);
            else
                $res[$page['id']] = self::getParentMenus($page, true);

        }

        Kryn::setCache("menus-$pDomainRsn", $res);
        Kryn::invalidateCache('navigation_' . $pDomainRsn);

        dbFree($resu);
        return $res;
    }

    public static function getParentMenus($pPage, $pAllParents = false) {
        $pid = $pPage['parent_id'];
        $res = array();
        while ($pid != 0) {
            $parent_page =
                dbExfetch("SELECT id, title, url, pid, type FROM ".pfx."system_node WHERE id = " . $pid, 1);
            if ($parent_page['type'] == 0 || $parent_page['type'] == 1 || $parent_page['type'] == 4) {
                //page or link or page-mount
                array_unshift($res, $parent_page);
            } else if ($pAllParents) {
                array_unshift($res, $parent_page);
            }
            $pid = $parent_page['parent_id'];
        }
        return $res;
    }

    public static function updateUrlCache($pDomainRsn) {

        $pDomainRsn = $pDomainRsn + 0;

        $resu = dbQuery("SELECT id, title, url, type, link FROM ".pfx."system_node WHERE domain_id = $pDomainRsn AND parent_id IS NULL");
        $res = array('url' => array(), 'id' => array());

        $domain = Kryn::getDomain($pDomainRsn);

        while ($page = dbFetch($resu)) {

            $page = self::__pageModify($page, array('realurl' => ''));
            $newRes = self::updateUrlCacheChildren($page, $domain);
            $res['url'] = array_merge($res['url'], $newRes['url']);
            $res['id'] = array_merge($res['id'], $newRes['id']);
        }

        $aliasRes = dbQuery('SELECT node_id, url FROM '.pfx.'system_node_alias WHERE domain_id = ' . $pDomainRsn);
        while ($row = dbFetch($aliasRes)) {
            $res['alias'][$row['url']] = $row['node_id'];
        }

        self::updatePage2DomainCache();
        Kryn::setCache("core/node-ids-to-url-$pDomainRsn", $res);
        dbFree($aliasRes);
        dbFree($resu);
        return $res;
    }

    public static function updatePage2DomainCache() {

        $r2d = array();
        $res = dbQuery('SELECT id, domain_id FROM '.pfx.'system_node ');

        while ($row = dbFetch($res)) {
            $r2d[$row['domain_id']] .= $row['id'] . ',';
        }
        Kryn::setCache('core/domain-to-nodes', $r2d);
        dbFree(res);
        return $r2d;
    }

    public static function updateUrlCacheChildren($pPage, $pDomain = false) {
        $res = array('url' => array(), 'id' => array(), 'r2d' => array());

        if ($pPage['type'] < 2) { //page or link or folder
            if ($pPage['realurl'] != '') {
                $res['url']['url=' . $pPage['realurl']] = $pPage['id'];
                $res['id'] = array('id=' . $pPage['id'] => $pPage['realurl']);
            } else {
                $res['id'] = array('id=' . $pPage['id'] => $pPage['url']);
            }
        }

        $pages = dbExfetchAll("SELECT id, title, url, type, link
                             FROM ".pfx."system_node
                             WHERE parent_id = " . $pPage['id']);

        if (is_array($pages)) {
            foreach ($pages as $page) {


                Kryn::deleteCache('page_' . $page['id']);

                $page = self::__pageModify($page, $pPage);
                $newRes = self::updateUrlCacheChildren($page);

                $res['url'] = array_merge($res['url'], $newRes['url']);
                $res['id'] = array_merge($res['id'], $newRes['id']);
                $res['r2d'] = array_merge($res['r2d'], $newRes['r2d']);

            }
        }
        return $res;
    }

    public static function __pageModify($page, $pPage) {
        if ($page['type'] == 0) {
            $del = '';
            if ($pPage['realurl'] != '')
                $del = $pPage['realurl'] . '/';
            $page['realurl'] = $del . $page['url'];

        } elseif ($page['type'] == 1) { //link
            if ($page['url'] == '') { //if empty, use parent-url else use url-hiarchy
                $page['realurl'] = $pPage['realurl'];
            } else {
                $del = '';
                if ($pPage['realurl'] != '')
                    $del = $pPage['realurl'] . '/';
                $page['realurl'] = $del . $page['url'];
            }

            $page['prealurl'] = $page['link'];
        } else if ($page['type'] != 3) { //no deposit
            //ignore the hiarchie-item
            $page['realurl'] = $pPage['realurl'];
        }
        return $page;
    }

}

?>
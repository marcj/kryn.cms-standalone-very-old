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


namespace Core\Render;


use Core\Kryn;

/**
 * Html render class
 * @author MArc Schmidt <marc@Kryn.org>
 *
 * @events onRenderSlot
 *
 */


class Utils {

    public static $docType = 'html5';


    public static $docTypeMap = array(

        'html 4.01 transitional' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
        'html 4.01 strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
        'html 4.01 frameset' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',

        'xhtml 1.0 transitional' =>
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
        'xhtml 1.0 strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        'xhtml 1.0 frameset' =>
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
        'xhtml 1.1 dtd' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',

        'html5' => '<!DOCTYPE html>'
    );

    public static function plugin($pMethod) {

        switch ($pMethod) {
            case 'head':
                return self::buildHead();
        }

    }

    public static function setDocType($docType) {
        self::$docType = $docType;
    }

    public static function getDocType() {
        return self::$docType;
    }

    public static function getPage(&$pContent = '') {
        global $_start;

        $res = self::$docTypeMap[strtolower(self::$docType)];
        $res .= "\n<head>" . Kryn::$htmlHeadTop;
        $res .= self::buildHead(true);
        error_log('Kryn.cms - page generation tooks '.(microtime(true)-$_start).' seconds.');

        $res .= Kryn::$htmlHeadEnd . '</head><body>' . Kryn::$htmlBodyTop . $pContent . "\n\n" . Kryn::$htmlBodyEnd .
                '</body></html>';

        return $res;
    }

    public static function printPage(&$pContent = '') {

        $html = self::getPage($pContent);
        $html = str_replace('inc/template/', 'media/', $html); //compatibility

        print $html;
    }

    public static function buildHead($pContinue = false) {
        $tagEnd = (strpos(strtolower(self::$docType), 'xhtml') !== false) ? '/>' : '>';

        if ($pContinue == false && Kryn::$admin == false) {
            return '{*Kryn-header*}';
        }

    $html = '';
        $html .= "<base href=\"" . Kryn::getBaseUrl() . "\" $tagEnd\n";
        $html .= '<meta name="DC.language" content="' . Kryn::$domain->getLang(). '" ' . $tagEnd . "\n";

        $html .= '<link rel="canonical" href="' . Kryn::$canonical . '" ' . $tagEnd . "\n";


/*      $metas = @json_decode($page['meta'], 1);
        if (count($metas) > 0)
            foreach ($metas as $meta)
                if ($meta['value'] != '')
                    $html .= '<meta name="' . str_replace('"', '\"', $meta['name']) . '" content="' .
                             str_replace('"', '\"', $meta['value']) . '" ' . $tagEnd . "\n";*/


        if (Kryn::$config['showBanner'] == 1) {
            $html .= '<meta name="generator" content="Kryn.cms" ' . $tagEnd . "\n";
        }


        $myJsFiles = array();


        /*
        * CSS FILES
        *
        */

        foreach (Kryn::$cssFiles as $css) {
            $myCssFiles[] = $css;
        }



        /*
        * JS FILES
        *
        */

        foreach (Kryn::$jsFiles as $js) {
            $myJsFiles[] = $js;
        }

        if (Kryn::$domain->getResourcecompression() != '1') {
            foreach ($myJsFiles as $js) {
                if (strpos($js, "http://") !== false) {
                    $html .= '<script type="text/javascript" src="' . $js . '" ></script>' . "\n";
                } else {
                    $mtime = @filemtime(PATH . (substr($js,0,1) != '/' ? PATH_MEDIA : ''). $js);
                    $html .= '<script type="text/javascript" src="'
                        . ((substr($js,0,1) != '/') ? PATH_MEDIA . $js . '?c=' : substr($js, 1))
                        . $mtime . '" ></script>' . "\n";
                }
            }
        } else {
            foreach ($myJsFiles as $js) {
                if (strpos($js, "http://") !== false) {
                    $html .= '<script type="text/javascript" src="' . $js . '" ></script>' . "\n";
                } else {
                    //local
                    $file = PATH_MEDIA . $js;
                    if (file_exists(PATH . $file) && $mtime = @filemtime(PATH . $file)) {
                        $jsCode .= $file . '_' . $mtime;
                    }
                }
            }
            $jsmd5 = md5($jsCode);
            $jsCachedFile = PATH_MEDIA_CACHE . 'cachedJs_' . $jsmd5 . '.js';
            $jsContent = '';

            if (!file_exists(PATH . $jsCachedFile)) {

                foreach ($myJsFiles as $js) {
                    $file = PATH_MEDIA . $js;
                    if (file_exists( $file)) {
                        $jsContent .= "/* $file: */\n\n";
                        $jsContent .= Kryn::fileRead($file) . "\n\n\n";
                    }
                }
                Kryn::fileWrite($jsCachedFile, $jsContent);
            }

            $html .= '<script type="text/javascript" src="' . $jsCachedFile . '" ></script>' . "\n";
        }


        /*
        *
        * HEADER
        */

        foreach (Kryn::$header as $head)
            $html .= "$head\n";

        //customized metas
        /*$metas = json_decode($page['meta'], true);
        if ($page['meta_fromParent'] == 1) {
            $ppage = Kryn::getParentPage($page['id']);
            $pmetas = json_decode($ppage['meta'], true);
            $metas = array_merge($ppage, $pmetas);
        }*/

        return $html;
    }

    /**
     * Returns all contents of the slot of the specified page.
     *
     * @static
     * @param $pId
     * @param bool $pBoxId
     * @param bool $pWithoutCache
     * @return array|string
     */
    public static function &getContents($pId, $pBoxId = false, $pWithoutCache = false) {

        $pId = $pId + 0;

        $time = time();
        $page = Kryn::getPage($pId);

        if (!$page) return 'page_not_found';

        $page = Kryn::checkPageAccess($page, false);
        if (!$page)
            return array();

        $result =& Kryn::getCache('contents-' . $pId);
        if (false && $result && !$pWithoutCache) return $result;

        $result = array();

        $versionId = $page->getActiveVersionId();

        //todo read acl from table
        $aclCanViewOtherVersions = true;

        if (Kryn::$page->getId() == $pId && getArgv('kVersionId') + 0 > 0 && $aclCanViewOtherVersions) {
            $versionId = getArgv('kVersionId') + 0;
        }

        $box = '';
        if ($pBoxId) {
            $box = "AND box_id = $pBoxId";
        }

        if ($versionId > 0) {

            $res = dbQuery("
            SELECT c.*
            FROM
                ".pfx."system_content c,
                ".pfx."system_page_version v
            WHERE 
                v.id = $versionId
                AND v.page_id = $pId
                AND c.version_id = v.id
                $box
                AND c.hide != 1
                AND ( c.cdate > 0 AND c.cdate IS NOT NULL )
            ORDER BY c.sort");

            while ($content = dbFetch($res)) {
                if (Kryn::checkPageAccess($content, false) !== false){
                    $result[$content['box_id']][] = $content;
                }
            }

            dbFree($res);

        } else {

            //compatibility o old kryns <=0.7
            $result = array();

            $res = dbQuery("SELECT * FROM ".pfx."system_content
                WHERE node_id = $pId
                $box
                AND (hide != 1 OR hide IS NULL)
                ORDER BY sortable_rank");

            while ($page = dbFetch($res)) {
                $result[$page['box_id']][] = $page;
            }
            dbFree($res);
        }

        Kryn::setCache('contents-' . $pId, $result);

        return Kryn::getCache('contents-' . $pId);
    }

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


        if (Kryn::$contents) {
            $oldContents = Kryn::$contents;
        }

        Kryn::$forceKrynContent = true;

        $start = microtime(true);
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

        $args = array($pPageId, $pSlotId);
        Event::fire('preRenderContents', $args);

        Kryn::addCss('css/_pages/' . $pPageId . '.css');
        Kryn::addJs('js/_pages/' . $pPageId . '.js');

        Kryn::$contents =& self::getContents($pPageId);

        if (Kryn::$page->getType() == 3) { //deposit
            Kryn::$page->setLayout('core/blankLayout.tpl');
        }

        if ($pSlotId) {
            $html = self::renderContents(Kryn::$contents[$pSlotId], $pProperties);
        } else {
            $html = tFetch(Kryn::$page->getLayout());
        }

        if ($oldContents) {
            Kryn::$contents = $oldContents;
        }
        if ($oldPage) {
            Kryn::$page = $oldPage;
            array_pop(Kryn::$nestedLevels);
        }
        Kryn::$forceKrynContent = false;


        $arguments = array($pPageId, $pSlotId, &$html);
        Event::fire('onRenderContents', $arguments);

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

        $access = true;
        $contents = array();

        if (!is_array($pContents)) return '';


        foreach ($pContents as $key => &$content) {

            $access = true;

            if (
                ($content['access_from'] + 0 > 0 && $content['access_from'] > time()) ||
                ($content['access_to'] + 0 > 0 && $content['access_to'] < time())
            ) {
                $access = false;
            }

            if ($content['hide'] === 1) {
                $access = false;
            }

            if ($access && $content['access_from_groups']) {

                $access = false;
                $groups = ',' . $content['access_from_groups'] . ',';

                foreach ($client->user['groups'] as $group) {
                    if (strpos($groups, ',' . $group . ',') !== false) {
                        $access = true;
                        break;
                    }
                }

                if (!$access) {
                    foreach ($adminClient->user['groups'] as $group) {
                        if (strpos($groups, ',' . $group . ',') !== false) {
                            $access = true;
                            break;
                        }
                    }
                    if ($access) {
                        //have acces through the admin login
                    }
                }
            }

            if ($access) {
                $contents[$key] = $content;
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
    public static function renderContent($pContent, $pProperties) {

        $content =& $pContent;

        tAssignRef('content', $content);
        tAssign('css', ($content['css']) ? $content['css'] : false);


        $argument = array(&$content, $pProperties);
        Event::fire('preRenderContent', $argument);


        switch (strtolower($content['type'])) {
            case 'text':
                //replace all [[ with a workaround, so that multilanguage will not fetch.
                $content['content'] = str_replace('[[', '[<!-- -->[', $content['content']);


                break;
            case 'html':
                $content['content'] = str_replace('[[', '\[[', $content['content']);

                break;
            case 'navigation':

                if ($content['content']){
                    $temp = json_decode($content['content'], 1);
                    $temp['id'] = $temp['entryPoint']+0;
                    unset($temp['entryPoint']);

                    $content['content'] = krynNavigation::get($temp);
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

                $oldContents = Kryn::$contents;

                $layoutcontent = json_decode($content['content'], true);
                Kryn::$contents = $layoutcontent['contents'];
                $content['content'] = tFetch($layoutcontent['layout']);

                Kryn::$contents = $oldContents;

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

        $unsearchable = false;
        if ((!is_array($content['access_from_groups']) && $content['access_from_groups'] != '') ||
            (is_array($content['access_from_groups']) && count($content['access_from_groups']) > 0) ||
            ($content['access_from'] + 0 > 0 && $content['access_from'] > time()) ||
            ($content['access_to'] + 0 > 0 && $content['access_to'] < time()) ||
            $content['unsearchable'] == 1
        ) {
            $unsearchable = true;
        }

        Event::fire('onRenderContent', $argument);

        $html = '';
        if ($content['template'] == '' || $content['template'] == '-') {
            if ($unsearchable)
                $html = '<!--unsearchable-begin-->' . $content['content'] . '<!--unsearchable-end-->';
            else
                $html = $content['content'];
        } else {

            tAssign('content', $content);
            $template = $content['template'];
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
        Kryn::setCache("systemUrls-$pDomainRsn", $res);
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
        Kryn::setCache('systemPages2Domain', $r2d);
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
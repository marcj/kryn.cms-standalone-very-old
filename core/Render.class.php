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

/**
 * Html render class
 * @author MArc Schmidt <marc@Kryn.org>
 *
 * @events onRenderSlot
 *
 */

class Render {

    public static $docType = 'xhtml 1.0 transitional';


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
        global $cfg;

        $tagEnd = (strpos(strtolower(self::$docType), 'xhtml') !== false) ? ' />' : ' >';

        if ($pContinue == false && Kryn::$admin == false) {
            return '{*Kryn-header*}';
        }
        $page = Kryn::$page;
        $domain = Kryn::$domain;

        $html = '<title>' . Kryn::$domain->getTitle() . '</title>' . "\n";

        $html .= "<base href=\"" . Kryn::$baseUrl . "\" $tagEnd\n";
        $html .= '<meta name="DC.language" content="' . Kryn::$domain->getLang(). '" ' . $tagEnd . "\n";

        $html .= '<link rel="canonical" href="' . Kryn::$baseUrl . substr(Kryn::$url, 1) . '" ' . $tagEnd . "\n";


/*        $metas = @json_decode($page['meta'], 1);
        if (count($metas) > 0)
            foreach ($metas as $meta)
                if ($meta['value'] != '')
                    $html .= '<meta name="' . str_replace('"', '\"', $meta['name']) . '" content="' .
                             str_replace('"', '\"', $meta['value']) . '" ' . $tagEnd . "\n";*/


        if (Kryn::$config['show_banner'] == 1) {
            $html .= '<meta name="generator" content="Kryn.cms" ' . $tagEnd . "\n";
        }


        $myCssFiles = array();
        $myJsFiles = array();


        if (Kryn::$kedit == true) {
            $html .= '<script type="text/javascript">var kEditPageId = ' . Kryn::$page['id'] . ';</script>' . "\n";
        }


        /*
        * CSS FILES
        *
        */

        foreach (Kryn::$cssFiles as $css) {
            $myCssFiles[] = $css;
        }

        # clearstatcache();

        if (Kryn::$domain->getResourcecompression() != '1') {
            foreach ($myCssFiles as $css) {
                if (strpos($css, "http://") !== false) {
                    $html .= '<link rel="stylesheet" type="text/css" href="' . $css . '" ' . $tagEnd . "\n";
                } else if (file_exists(PATH . PATH_MEDIA . $css) &&
                           $mtime = @filemtime(PATH . PATH_MEDIA . $css)
                ) {
                    $css .= '?c=' . $mtime;
                    $html .=
                        '<link rel="stylesheet" type="text/css" href="' . $cfg['path'] . PATH_MEDIA . $css . '" ' .
                        $tagEnd . "\n";
                }
            }
        } else {
            $cssCode = '';
            foreach ($myCssFiles as $css) {
                if (strpos($css, "http://") !== false) {
                    $html .= '<script type="text/javascript" src="' . $css . '" ></script>' . "\n";
                } else {
                    //local
                    $file = PATH_MEDIA . $css;
                    if (file_exists(PATH . $file) && $mtime = @filemtime(PATH . $file)) {
                        $cssCode .= $file . '_' . $mtime;
                    }
                }
            }

            $cssmd5 = md5($cssCode);

            $cssCachedFile = $cfg['media_cache'] . 'cachedCss_' . $cssmd5 . '.css';


            $cssContent = '';

            if (!file_exists(PATH . $cssCachedFile)) {
                foreach ($myCssFiles as $css) {
                    $file = PATH_MEDIA . $css;
                    if (file_exists($file)) {
                        $cssContent .= "/* $file: */\n\n";
                        $temp = Kryn::fileRead($file) . "\n\n\n";
                        //$cssContent .= Kryn::fileRead( $file )."\n\n\n";

                        //replace relative urls to absolute
                        $mypath = $cfg['path'] . dirname($file);
                        $temp = preg_replace('/url\(/', 'url(' . $mypath . '/', $temp);

                        $cssContent .= $temp;
                    }
                }
                Kryn::fileWrite($cssCachedFile, $cssContent);
            }
            $html .=
                '<link rel="stylesheet" type="text/css" href="' . $cfg['path'] . $cssCachedFile . '" ' . $tagEnd . "\n";

            $jsCode = '';
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
                if (strpos($js, "http://") !== FALSE) {
                    $html .= '<script type="text/javascript" src="' . $js . '" ></script>' . "\n";
                } else {
                    if ($mtime = @filemtime(PATH . PATH_MEDIA . $js) || $js == '/krynJavascriptGlobalPath.js') {
                        $html .= '<script type="text/javascript" src="' . $cfg['path']
                            . ((substr($js,0,1)=='/') ? PATH_MEDIA . $js . '?c=' : substr($js, 1))
                            . $mtime . '" ></script>' . "\n";
                    }
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
            $jsCachedFile = $cfg['media_cache'] . 'cachedJs_' . $jsmd5 . '.js';
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

            $html .= '<script type="text/javascript" src="' . $cfg['path'] . $jsCachedFile . '" ></script>' . "\n";
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
    public static function &getPageContents($pId, $pBoxId = false, $pWithoutCache = false) {

        $pId = $pId + 0;

        $time = time();
        $page = Kryn::getPage($pId);


        $page = Kryn::checkPageAccess($page, false);
        if (!$page)
            return array();

        $result =& Kryn::getCache('pageContents-' . $pId);
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

            $res = dbExec("
            SELECT c.*
            FROM
                %pfx%system_page_content c,
                %pfx%system_page_version v
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

        } else {

            //compatibility o old kryns <=0.7
            $result = array();
            $res = dbExec("SELECT * FROM %pfx%system_page_content
                WHERE page_id = $pId
                $box 
                AND version_id = 1
                AND hide != 1
                ORDER BY sort");
            while ($page = dbFetch($res)) {
                $result[$page['box_id']][] = $page;
            }
        }

        Kryn::setCache('pageContents-' . $pId, $result);

        return Kryn::getCache('pageContents-' . $pId);
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
    public static function renderPageContents($pPageId = false, $pSlotId = false, $pProperties = false) {


        if (Kryn::$contents) {
            $oldContents = Kryn::$contents;
        }
        Kryn::$forceKrynContent = true;

        $start = microtime(true);
        if ($pPageId == Kryn::$page->getId()) {
            //endless loop
            die(t('You produced a endless loop. Please check your latest changed pages.'));
        }

        if (!$pPageId) {

            $pPageId = Kryn::$page->getId();

        } else if ($pPageId != Kryn::$page->getId()) {

            $oldPage = Kryn::$page;
            Kryn::$page = Kryn::getPage($pPageId, true);
            Kryn::$nestedLevels[] = Kryn::$page;
        }

        $args = array($pPageId, $pSlotId);
        Event::fire('onBeforeRenderPageContents', $args);

        Kryn::addCss('css/_pages/' . $pPageId . '.css');
        Kryn::addJs('js/_pages/' . $pPageId . '.js');

        Kryn::$contents =& self::getPageContents($pPageId);

        if (Kryn::$page->getType() == 3) { //deposit
            Kryn::$page->setLayout('Kryn/blankLayout.tpl');
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
        Event::fire('onRenderPageContents', $arguments);

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
        krynEvent::fire('onBeforeRenderSlot', $argument);

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
        krynEvent::fire('onRenderSlot', $argument);

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


        $argument = array($pContent, $pProperties);
        krynEvent::fire('onRenderContent', $argument);


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
                    $content['content'] = self::renderPageContents($content['content'] + 0, 1, $pProperties);

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
        krynEvent::fire('onAfterRenderContent', $argument);

        return $html;
    }


}

?>
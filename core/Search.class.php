<?php

/**
 * krynSearch class
 * @internal
 * @author Kryn.labs <info@krynlabs.com>
 */

namespace Core;

/**
 * Search class
 */

class Search {

    public static $forceSearchIndex = false;

    public static $returnCodes = false;

    static $jsonFoundPages = array();

    static $pageUrl;

    public static $minWaitTimeTillNextCrawl = 260; //for 'keep index up 2 date' adminSearchIndexer::getIndex();

    public static $redirectTo = '';

    public static $autoCrawlPermissionLifetime = 60; //sec

    //create a new search index for this page
    public static function createPageIndex(&$pContent) {

        //indexing forced no matter if already indexed
        if (isset($_REQUEST['forceSearchIndex']) && $_REQUEST['forceSearchIndex']) {

            //force could only be enabled with correct search_index_key for this domain
            $validation = dbExFetch("SELECT id FROM %pfx%system_domains WHERE id = " . Kryn::$domain->getId() .
                " AND search_index_key = '" . esc($_REQUEST['forceSearchIndex']) . "'", 1);

            if (!empty($validation) && $validation['id'] == Kryn::$domain->getId())
                self::$forceSearchIndex = $_REQUEST['forceSearchIndex'];

        }

        if (getArgv(1) == 'admin' || Kryn::$page->getId() + 0 == 0) return;

        if (Kryn::$page->getUnsearchable() == 1) return;

        if (getArgv('kVersionId') || getArgv('kryn_framework_version_id')) {
            return 6;
        }


        $indexedContent = self::stripContent($pContent);
        $contentMd5 = md5($indexedContent);

        $cashkey = 'krynSearch_' . Kryn::$page->getId() . '_' . $contentMd5;

        $cache = Kryn::getCache($cashkey);

        $a = '/' . Kryn::getRequestPageUrl(true);
        $b = $cache['url'];

        if ($cache && $b === "")
            $b = '/';

        self::$pageUrl = $a;

        if ($cache && $b == $a && self::$forceSearchIndex === false) {
            return 3; //'Url with this content is already indexed!', 3);
        }

        //check if we have additional arguments which doesnt change the content
        if ($cache && strlen($b) < strlen($a)
            && (strpos($b, '/' . Kryn::$page->getUrl()) === 0 || Kryn::$isStartpage)
            && self::$forceSearchIndex === false
        ) {

            self::$redirectTo = $b;
            return 2; //'Given arguments does not change the content!', 2);

        }

        //check if content is empty
        if (strlen(trim($indexedContent)) < 1) {
            return 7; //'No content found. Site was not indexed!', 7);
        }

        $index = \SearchQuery::create()->findPk(array($a, Kryn::$domain->getId()));

        if (!$index){
            $index = new \Search();
        }

        //we now ready to index this content
        $index->setUrl(self::$pageUrl)
              ->setTitle(Kryn::$domain->getTitle())
              ->setMd5($contentMd5)
              ->setMdate(time())
              ->setPageId(Kryn::$page->getId())
              ->setDomainId(Kryn::$domain->getId())
              ->setPagecontent($indexedContent);

        $index->save();

        Kryn::setCache($cashkey, array(
            'url' => $a
        ));

        self::getLinksInContent($pContent);

        return 1; //'Indexing successfully completed!', 1);             

    }

    public static function stripContent($pContent) {

        $arSearch = array('@<script[^>]*>.*</script>@Uis', // javascript
            '@<style[^>]*>.*</style>@Uis', //  style tags
            '@<\!--unsearchable-begin-->.*<\!--unsearchable-end-->@Uis', //unsearchable html comment
            '@<!--.*-->@Uis', // comments
            '@style="(.*)"@Uis', // css inline styling
            '@class="(.*)"@Uis', //css class
            '@id="(.*)"@Uis',

        );
        $pContent = preg_replace($arSearch, '', $pContent);
        return Kryn::compress(strip_tags($pContent, '<p><br><br /><h1><h2><h3><h4><h5><h6>'));
    }

    //search for links in parsed html content
    public static function getLinksInContent($pContent) {
        global $cfg;

        Kryn::replacePageIds($pContent);
        $searchPattern = '#<a[^>]+href[^>]*=[^>]*\"([^\"]+)\"[^>]*>(.*)<\/a>#Uis';
        preg_match_all($searchPattern, $pContent, $matches, PREG_SET_ORDER);

        $arInserted = array();
        foreach ($matches as $value) {

            $linkBackup = $value[1];
            $value[1] = strtolower($value[1]);
            //check if link is valid
            //kick all anchors, javascript btns, admin and downloadcenter links
            if (strlen($value[1]) < 2 || strpos($value[1], '.') !== false || strpos($value[1], '#') !== false ||
                strpos($value[1], 'mailto:') !== false || strpos($value[1], 'action_select') !== false
                || strpos($value[1], 'javascript:') === 0 || strpos($value[1], 'downloadfile') !== false
                || strpos($value[1], '/admin') === 0 || strpos($value[1], 'admin') === 0 ||
                strpos($value[1], 'users-logout:') !== false
                || (strpos($value[1], 'http://' . Kryn::$domain->getRealDomain()) === false &&
                    (strpos($value[1], 'http://') === 0) || strpos($value[1], 'https://') === 0)
                || strpos($value[1], 'user:logout') !== false
            )
                continue;

            //restore case-sensitivity
            $value[1] = $linkBackup;

            if (strpos($value[1], Kryn::$domain->getPath()) === 0) {
                $value[1] = substr($value[1], strlen(Kryn::$domain->getPath()));
            }

            if ($value[1] == '')
                $value[1] = '/';

            //add slash
            if (strpos($value[1], 'http://') !== 0 && strpos($value[1], 'https://') !== 0 &&
                strpos($value[1], '/') !== 0
            )
                $value[1] = '/' . $value[1];

            //remove last slash
            if (strrpos($value[1], '/') == strlen($value[1]) - 1)
                $value[1] = substr($value[1], 0, strlen($value[1]) - 1);

            //if absolute link transform to relative
            if (strpos($value[1], 'http://') === 0 || strpos($value[1], 'https://') === 0) {
                $value[1] = substr($value[1], stripos($value[1], Kryn::$domain->getDomain() . $cfg['path']) +
                                              strlen(Kryn::$domain->getDomain() . $cfg['path']) - 1);
            }

            $value[1] = str_replace('//', '/', $value[1]);
            $value[1] = str_replace('//', '/', $value[1]);

            if (substr($value[1], -1) == '/')
                $value[1] = substr($value[1], 0, -1);

            if (!isset($arInserted[Kryn::$domain->getId() . '_' . $value[1]]) &&
                !isset($arInserted[Kryn::$domain->getId() . '_' . $value[1]]) && strlen($value[1]) > 0
            ) {

                $arInserted[Kryn::$domain->getId() . '_' . $value[1]] = true;

                self::disposePageForIndex($value[1], 'LINK ' . esc($value[1]), Kryn::$domain->getId());

                self::$jsonFoundPages[] = $value[1];
            }
        }

    }

    public static function getSearchIndexOverview($pPageId) {
        $indexes = dbExFetch("
            SELECT url, title , mdate, md5
            FROM %pfx%system_search
            WHERE page_id =" . esc($pPageId) . " AND mdate > 0 ORDER BY url, mdate DESC", -1);

        $arIndexes = array();
        foreach ($indexes as $page) {
            $arIndexes[] = array($page['url'], $page['title'], date('d.m.Y H:i', $page['mdate']), $page['md5']);
        }

        return $arIndexes;
    }


    //insert a page into the search table for further indexing
    public static function disposePageForIndex($pUrl, $pTitle, $pDomainId, $pPageId = '0') {

        $index = \SearchQuery::create()->findPk(array($pUrl, $pDomainId));

        if (!$index){
            $index = new \Search();
        }

        //we now ready to index this content
        $index->setUrl($pUrl)
            ->setTitle($pTitle)
            ->setMdate(0)
            ->setPageId($pPageId)
            ->setDomainId($pDomainId);

        $index->save();

    }

    //clear complete search index
    public static function clearSearchIndex() {

        dbDelete('system_search');
        Kryn::invalidateCache('krynSearch');

        return array('state' => true);
    }
}

?>

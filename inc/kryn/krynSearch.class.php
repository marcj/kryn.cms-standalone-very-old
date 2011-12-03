<?php

/**
 * krynSearch class
 * @internal
 * @author Kryn.labs <info@krynlabs.com>
 */


class krynSearch extends baseModule {

    public static $forceSearchIndex = false;

    public static $returnCodes = false;

    static $jsonFoundPages = array();

    static $pageUrl;

    public static $minWaitTimeTillNextCrawl = 260; //for 'keep index up 2 date' adminSearchIndexer::getIndex();

    public static $redirectTo = '';


    public static $autoCrawlPermissionLifetime = 60; //sec

    public static function initSearch() {

        //indexing forced no matter if already indexed
        if (isset($_REQUEST['forceSearchIndex']) && $_REQUEST['forceSearchIndex']) {

            //force could only be enabled with correct search_index_key for this domain
            $validation = dbExFetch("SELECT rsn FROM %pfx%system_domains WHERE rsn = " . kryn::$domain['rsn'] .
                                    " AND search_index_key = '" . esc($_REQUEST['forceSearchIndex']) . "'", 1);

            if (!empty($validation) && $validation['rsn'] == kryn::$domain['rsn'])
                self::$forceSearchIndex = $_REQUEST['forceSearchIndex'];

        }

    }


    //create a new search index for this page
    public static function createPageIndex(&$pContent) {
        global $cfg;

        if (getArgv(1) == 'admin' || kryn::$page['rsn'] + 0 == 0) return;

        if (kryn::$page['unsearchable'] == 1) return;

        if (getArgv('kVersionId') || getArgv('kryn_framework_version_id')) {
            return 6;
        }


        $indexedContent = self::stripContent($pContent);
        $contentMd5 = md5($indexedContent);

        $cashkey = 'krynSearch_' . kryn::$page['rsn'] . '_' . $contentMd5;

        $cache = kryn::getCache($cashkey);

        $a = '/' . kryn::getRequestPageUrl(true);
        $b = $cache['url'];

        if ($cache && $b === "")
            $b = '/';

        self::$pageUrl = $a;

        if ($cache && $b == $a && self::$forceSearchIndex === false) {
            return 3; //'Url with this content is already indexed!', 3);
        }

        //check if we have additional arguments which doesnt change the content
        if ($cache && strlen($b) < strlen($a)
            && (strpos($b, '/' . kryn::$page['url']) === 0 || kryn::$isStartpage)
            && self::$forceSearchIndex === false
        ) {

            self::$redirectTo = $b;
            return 2; //'Given arguments does not change the content!', 2);

        }

        //check if content is empty
        if (strlen(trim($indexedContent)) < 1) {
            return 7; //'No content found. Site was not indexed!', 7);
        }

        //we now ready to index this content
        $values = array(
            'url' => self::$pageUrl,
            'title' => kryn::$page['title'],
            'md5' => $contentMd5,
            'mdate' => time(),
            'page_rsn' => kryn::$page['rsn'],
            'domain_rsn' => kryn::$domain['rsn'],
            'page_content' => $indexedContent
        );

        $where = array('url' => $a, 'domain_rsn' => kryn::$domain['rsn']);

        if (!$cache['rsn']) {

            $row = dbExfetch("SELECT url FROM %pfx%system_search WHERE url='" . esc($a) . "' AND domain_rsn = " .
                             kryn::$domain['rsn'], 1);

            if ($row['url']) {
                dbUpdate('system_search', $where, $values);
            } else {
                $rsn = dbInsert('system_search', $values);
            }
        } else {
            dbUpdate('system_search', $where, $values);
        }

        kryn::setCache($cashkey, array(
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
        return kryn::compress(strip_tags($pContent, '<p><br><br /><h1><h2><h3><h4><h5><h6>'));
    }

    //search for links in parsed html content
    public static function getLinksInContent($pContent) {
        global $cfg;

        kryn::replacePageIds($pContent);
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
                || (strpos($value[1], 'http://' . kryn::$domain['domain']) === false &&
                    (strpos($value[1], 'http://') === 0) || strpos($value[1], 'https://') === 0)
                || strpos($value[1], 'user:logout') !== false
            )
                continue;

            //restore case-sensitivity
            $value[1] = $linkBackup;

            if (strpos($value[1], kryn::$domain['path']) === 0) {
                $value[1] = substr($value[1], strlen(kryn::$domain['path']));
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
                $value[1] = substr($value[1], stripos($value[1], kryn::$domain['domain'] . $cfg['path']) +
                                              strlen(kryn::$domain['domain'] . $cfg['path']) - 1);
            }

            $value[1] = str_replace('//', '/', $value[1]);
            $value[1] = str_replace('//', '/', $value[1]);

            if (substr($value[1], -1) == '/')
                $value[1] = substr($value[1], 0, -1);

            if (!isset($arInserted[kryn::$domain['rsn'] . '_' . $value[1]]) &&
                !isset($arInserted[kryn::$domain['rsn'] . '_' . $value[1]]) && strlen($value[1]) > 0
            ) {

                $arInserted[kryn::$domain['rsn'] . '_' . $value[1]] = true;

                self::disposePageForIndex($value[1], 'LINK ' . esc($value[1]), kryn::$domain['rsn']);

                self::$jsonFoundPages[] = $value[1];
            }
        }

    }

    public static function getSearchIndexOverview($pPageRsn) {
        $indexes = dbExFetch("
            SELECT url, title , mdate, md5
            FROM %pfx%system_search
            WHERE page_rsn =" . esc($pPageRsn) . " AND mdate > 0 ORDER BY url, mdate DESC", -1);

        $arIndexes = array();
        foreach ($indexes as $page) {
            $arIndexes[] = array($page['url'], $page['title'], date('d.m.Y H:i', $page['mdate']), $page['md5']);
        }

        return $arIndexes;
    }


    //insert a page into the searchtable for further indexing
    public static function disposePageForIndex($pUrl, $pTitle, $pDomainRsn, $pPageRsn = '0') {

        $url = esc($pUrl);
        $row =
            dbExfetch("SELECT rsn FROM %pfx%system_search WHERE url = '$url' AND domain_rsn = " . $pDomainRsn + 0, 1);

        $values = array(
            'url' => $pUrl,
            'title' => $pTitle,
            'mdate' => 0,
            'domain_rsn' => $pDomainRsn,
            'page_rsn' => $pPageRsn
        );

        if ($row)
            dbUpdate('system_search', array('rsn' => $row['rsn']), $values);
        else
            dbInsert('system_search', $values);

    }

    //clear complete search index
    public static function clearSearchIndex() {

        dbDelete('system_search');
        kryn::invalidateCache('krynSearch');

        return array('state' => true);
    }
}

?>

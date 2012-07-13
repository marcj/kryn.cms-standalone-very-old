<?php
class adminSearchIndexer {

    public static function init() {

        require_once('core/krynSearch.class.php');

        switch (getArgv(4)) {

            case 'hasPermission':
                json(array('access' => self::hasPermission()));

            case 'getWaitlist':
                json(self::getWaitlist());

            case 'getIndex':
                json(self::getIndex());

            case 'getIndexedPages4AllDomains':
                json(self::getIndexedPages4AllDomains());


            case 'clearIndex' :
                json(krynSearch::clearSearchIndex());
                break;

            case 'getNewUnindexedPages':
                json(self::getNewUnindexedPages());

            case 'getSearchIndexOverview' :
                json(krynSearch::getSearchIndexOverview(getArgv('page_id') + 0));
                break;
            /*    
            case 'getFullSiteIndexUrls' :
               //krynSearch::initSearchFromBackend($_REQUEST['domain_id']+0);
                json(krynSearch::getFullSiteIndexUrls($_REQUEST['domain_id']+0));
            break;
            
            case 'getUnindexSitePercent' :
                //krynSearch::initSearchFromBackend($_REQUEST['domain_id']+0);
                json(krynSearch::getUnindexSitePercent($_REQUEST['domain_id']+0));
            break;    
                
            
            
            case 'getWaitlist' :
                    json(krynSearch::getWaitlist());
            break;
            case 'getUpdatelist':
                json(krynSearch::getUpdatelist());
            break;
            case 'pushPageTree':
                json(krynSearch::pushPageTree());
            break;






            case 'hasPermissionCheck':
                json(array('hasPermission' => krynSearch::checkAutoCrawlPermission()));
            break;
            */
            default:
                json(getArgv(4));
                break;
        }

        exit();
    }

    public static function getIndexedPages4AllDomains() {

        $items = dbExfetch('
        	SELECT max(d.domain) as domain, max(d.lang) as lang, count(s.domain_id)+0 as indexedcount
        	FROM %pfx%system_domains d
        	LEFT OUTER JOIN %pfx%system_search s ON (s.domain_id = d.id AND s.mdate > 0 AND (blacklist IS NULL OR blacklist = 0) )
        	
        	GROUP BY d.id
        ', -1);

        return $items;
    }

    public static function getNewUnindexedPages() {

        $res['access'] = self::hasPermission();
        if ($res['access'] == false) return $res;

        $dres = dbExec('
        SELECT p.id, p.title, p.domain_id FROM %pfx%system_page p
        WHERE p.type = 0 AND p.id NOT IN( SELECT page_id FROM %pfx%system_search )
        AND (p.unsearchable != 1 OR p.unsearchable IS NULL)
        ');

        $res['pages'] = array();


        require_once(PATH_MODULE . 'admin/adminPages.class.php');

        while ($row = dbFetch($dres)) {

            $res['pages'][] = $row;


            $urls = kryn::getCache('systemUrls-' . $row['domain_id']);
            if (!$urls) {
                $urls = adminPages::updateUrlCache($row['domain_id']);
            }

            $row['url'] = $urls['id']['id=' . $row['id']];
            krynSearch::disposePageForIndex('/' . $row['url'], $row['title'], $row['domain_id'], $row['id']);

        }

        return $res;
    }

    public static function getWaitlist() {
        $res['access'] = self::hasPermission();
        if ($res['access'] == false) return $res;

        $blacklistTimeout = time() - krynSearch::$blacklistTimeout;

        $res['pages'] = dbExfetch('
        	SELECT s.url, d.domain, d.master, d.lang, d.path FROM %pfx%system_search s, %pfx%system_domains d WHERE
        	d.id = s.domain_id AND s.mdate = 0 AND (s.blacklist IS NULL OR  s.blacklist < ' . $blacklistTimeout . ' )'
            , -1);
        return $res;
    }

    public static function getIndex() {
        $res['access'] = self::hasPermission();
        if ($res['access'] == false) return $res;

        $blacklistTimeout = time() - krynSearch::$blacklistTimeout;
        $nextCheckTimeout = time() - krynSearch::$minWaitTimeTillNextCrawl;

        $res['pages'] = dbExfetch('
        	SELECT s.url, d.domain, d.master, d.lang, d.path FROM %pfx%system_search s, %pfx%system_domains d WHERE
        	d.id = s.domain_id AND s.mdate < ' . $nextCheckTimeout . '  AND (s.blacklist IS NULL OR  s.blacklist < ' .
                                  $blacklistTimeout . ' )'
            , -1);

        return $res;
    }

    /**
     * checks the permission whether we have crawler access or not.
     * Set us as new crawler when old crawler is expired and/or update the crawler-time.
     */

    public static function hasPermission() {
        global $currentCrawler;

        $timeout = 2 * 60;

        $id = getArgv('crawlerId', 1);
        $crawler = PATH_MODULE . "admin/crawler.php";
        if (!file_exists($crawler)) false;
        include($crawler);

        if (!$currentCrawler || $currentCrawler['id'] == $id || time() - $currentCrawler['time'] > $timeout) {
            //we the new one
            $crawler['id'] = $id;
            $crawler['time'] = time();
            $php = "<?php \n" . '$currentCrawler = ' . var_export($crawler, true) . "; \n?>";
            kryn::fileWrite(PATH_MODULE . "admin/crawler.php", $php);
            return true;
        }

        return false;
    }
}

?>
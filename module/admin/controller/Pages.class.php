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

namespace Admin;

use Core\Kryn;


class Pages {

    public static function init() {

        switch (getArgv(3)) {
            case 'domain':
                return self::domain();
            case 'save':
                return self::save();
            case 'getLayout':
                return adminLayout::get(getArgv('name'), getArgv('plain'));
            case 'move':
                return self::move();
            case 'add':
                return self::add();
//            return self::save( true );
            case 'getPage':
                return self::getPage(getArgv('id') + 0, true);
            case 'getPageInfo':
                return self::getPageInfo(getArgv('id') + 0, true);
            case 'deletePage':
                return self::deletePage(getArgv('id') + 0);
            case 'getNotices':
                return self::getNotices(getArgv('id') + 0);
            case 'addNotice':
                return self::addNotice(getArgv('id') + 0);
            case 'getIcons':
                return json(self::getIcons(getArgv('id')));
            case 'getDomains':
                return self::getDomains(getArgv('language'));
            case 'getTree':
                return self::getTree(getArgv('page_id') + 0);
            case 'getTreeDomain':
                return self::getTreeDomain(getArgv('domain_id') + 0);
            case 'getTemplate':
                return self::getTemplate(getArgv('template'));
            case 'getVersions':
                return self::getVersions();
            case 'getUrl':
                return self::getUrl(getArgv('id'));
            case 'getPageVersions':
                json(self::getPageVersion(getArgv('id')));
            case 'getVersion':
                $id = getArgv('id') + 0;
                $version = getArgv('version') + 0;
                return json(self::getVersion($id, $version));
            /*case 'addVersion':
        return self::addVersion( getArgv('id')+0, getArgv('name',true) );*/
            case 'setLive':
                return json(self::setLive(getArgv('version')));

            case 'paste':
                return json(self::paste());

            case 'setHide':
                return json(self::setHide(getArgv('id'), getArgv('visible')));

            case 'deleteAlias':
                return self::deleteAlias(getArgv('id') + 0);
            case 'getAliases':
                return self::getAliases(getArgv('page_id') + 0);

            default:
                return self::itemList();
        }
    }
/*
    public static function setHide($pRsn, $pVisible) {
        $pRsn += 0;
        $pVisible += 0;

        if (Kryn::checkPageAcl($pRsn, 'visible'))
            dbUpdate('system_page', 'id = ' . $pRsn, array('visible' => $pVisible));
    }

    public static function getPageInfo($pRsn) {

        $pRsn += 0;
        $page = dbTableFetch('system_page', "id = $pRsn", 1);
        $page['_parents'] = Kryn::getPageParents($pRsn);

        if (!$page['_parents'])
            $page['_parents'] = array();

        return $page;

    }

    public static function getAliases($pRsn) {
        $pRsn = $pRsn + 0;

        $items = dbTableFetch('system_page_alias', 'to_page_id = ' . $pRsn, -1);
        json($items);
    }

    public static function deleteAlias($pRsn) {
        $pRsn = $pRsn + 0;

        dbDelete('system_page_alias', 'id = ' . $pRsn);
    }

    public static function setLive($pVersion) {

        $pVersion = $pVersion + 0;
        $version = dbTableFetch('system_page_version', 1, 'id = ' . $pVersion);

        if ($version['id'] > 0) {
            $newstVersion = dbTableFetch('system_page_version', 1,
                'page_id = ' . $version['page_id'] . ' ORDER BY created DESC');

            if ($newstVersion['id'] == $pVersion)
                dbUpdate('system_page', array('id' => $version['page_id']), array('draft_exist' => 0));
            else
                dbUpdate('system_page', array('id' => $version['page_id']), array('draft_exist' => 1));

            dbUpdate('system_page_version', array('page_id' => $version['page_id']), array('active' => 0));
            dbUpdate('system_page_version', array('id' => $version['id']), array('active' => 1));
            return 1;
        }
        return 0;

    }

    public static function paste() {

        $domain = getArgv('to_domain') == 1 ? true : false;
        if (getArgv('type') == 'pageCopy') {
            self::copyPage(getArgv('page'), getArgv('to'), $domain, getArgv('pos'));
        }
        if (getArgv('type') == 'pageCopyWithSubpages') {
            self::copyPage(getArgv('page'), getArgv('to'), $domain, getArgv('pos'), true);
        }

        $pageTo = dbTableFetch('system_page', 1, 'id = ' . (getArgv('to') + 0));
        self::cleanSort($pageTo['domain_id'], $pageTo['parent_id']);
        self::updateUrlCache($pageTo['domain_id']);
        self::updateMenuCache($pageTo['domain_id']);

        $page = dbTableFetch('system_page', 1, 'id = ' . (getArgv('page') + 0));
        self::cleanSort($page['domain_id'], $page['parent_id']);
        if ($page['domain_id'] != $pageTo['domain_id']) {
            self::updateUrlCache($page['domain_id']);
            self::updateMenuCache($page['domain_id']);
        }

        self::updatePage2DomainCache();
        Kryn::deleteCache('Kryn_pluginrelations');

        return true;

    }

    public static function copyPage($pFrom, $pTo, $pToDomain, $pPos, $pWithSubpages = false, $pWithoutThisPage = false) {
        global $user;

        $pFrom += 0;
        $pTo += 0;
        $pWithoutThisPage += 0;

        $fromPage = dbTableFetch('system_page', 1, 'id = ' . $pFrom);
        $newPage = $fromPage;

        if (!$pToDomain) {
            $toPage = dbTableFetch('system_page', 1, 'id = ' . $pTo);
            $siblingWhere = "pid = " . $toPage['parent_id'];
            $newPage['domain_id'] = $toPage['domain_id'];
        }

        if ($pPos == 'down' || $pPos == 'up') {
            $newPage['sort'] = $toPage['sort'];
            $newPage['parent_id'] = $toPage['parent_id'];
            $newPage['sort_mode'] = $pPos;
            if ($pToDomain) {
                return false;
            }
        } else {
            $newPage['sort'] = 1;
            $newPage['sort_mode'] = 'up';
            if (!$pToDomain) {
                $siblingWhere = "pid = " . $toPage['id'];
                $newPage['parent_id'] = $toPage['id'];
            } else {
                $newPage['parent_id'] = 0;
                $newPage['domain_id'] = $pTo;
                $siblingWhere = "pid = 0 AND domain_id = " . $pTo;
            }
        }
        $newPage['draft_exist'] = 1;
        unset($newPage['id']);
        $newPage['visible'] = 0;

        if ($pWithSubpages) {
            $withoutPage = '';
            if ($pWithoutThisPage) {
                $withoutPage = ' AND id != ' . $pWithoutThisPage;
            }

            $childs = dbTableFetch('system_page', -1, 'pid = ' . $pFrom . $withoutPage . ' ORDER BY sort ');
        }

        //ceck url & titles
        $siblings = dbTableFetch('system_page', -1, $siblingWhere);

        if (count($siblings) > 0) {

            $newCount = 0;
            $t = $newPage['title'];
            $needlePos = strpos($t, ' #') + 2;
            $needleLast = substr($t, $needlePos);

            foreach ($siblings as &$sibling) {

                //check title
                if (
                    $needleLast + 0 == 0 && $newPage['title'] == substr($sibling['title'], 0, strlen($newPage['title']))
                ) {
                    //same start, if last now a number ?
                    $end = substr($sibling['title'], strlen($newPage['title']) + 2);
                    if ($end + 0 > 0) {
                        if ($newCount < $end + 1)
                            $newCount = $end + 1; //$newPage['title'] .= ' #'.($end+1);
                    } else if ($end == '') { //equal title
                        if ($newCount == 0)
                            $newCount = 1; //$newPage['title'] .= ' #1';
                    }
                } else {

                    $ts = $sibling['title'];
                    $needleSPos = strpos($ts, ' #') + 2;
                    $needleSLast = substr($ts, $needleSPos);

                    if ($needleLast + 0 > 0 && $needleSLast + 0 > 0) {
                        //both seems to be increased
                        if ($newCount < $needleSLast + 1)
                            $newCount = $needleSLast + 1;
                    }

                }

                if ($newPage['url'] == substr($sibling['url'], 0, strlen($newPage['url']))) {
                    //same start, if last now a number ?
                    $end = substr($sibling['url'], strlen($newPage['url']));
                    if ($end + 0 > 0) {
                        $newPage['url'] .= '_' . ($end + 1);
                    } else if ($end == '') { //equal title
                        $newPage['url'] .= '_1';
                    }
                }
            }

            if ($newCount > 0) {
                if ($needlePos > 2)
                    $newPage['title'] = substr($t, 0, $needlePos - 2) . ' #' . $newCount;
                else
                    $newPage['title'] .= ' #' . $newCount;

            }
        }

        if ($newPage['parent_id'] == 0) {
            if (!Kryn::checkPageAcl($newPage['domain_id'], 'addPages', 'd'))
                json(array('error' => 'access_denied'));
            ;
        } else {
            if (!Kryn::checkPageAcl($newPage['parent_id'], 'addPages'))
                json(array('error' => 'access_denied'));
            ;
        }

        unset($newPage['id']);
        $lastId = dbInsert('system_page', $newPage);

        if (!$pWithoutThisPage)
            $pWithoutThisPage = $lastId;

        if ($newPage['parent_id'] == 0) {
            if (!Kryn::checkPageAcl($newPage['domain_id'], 'canPublish', 'd'))
                json(array('error' => 'access_denied'));
            ;
        } else {
            if (!Kryn::checkPageAcl($newPage['parent_id'], 'canPublish'))
                json(array('error' => 'access_denied'));
            ;
        }

        //copy contents
        $curVersion = dbTableFetch('system_page_version', 1, 'active = 1 AND page_id = ' . $pFrom);
        $contents = dbTableFetch('system_contents', -1, 'version_id = ' . $curVersion['id']);

        if (count($contents) > 0) {
            $newVersion = dbInsert('system_page_version', array(
                'page_id' => $lastId,
                'owner_id' => $user->user_id,
                'created' => time(),
                'modified' => time(),
                'active' => 0
            ));

            foreach ($contents as &$content) {
                $content['page_id'] = $lastId;
                unset($content['id']);
                $content['mdate'] = time();
                $content['cdate'] = time();
                $content['version_id'] = $newVersion;
                dbInsert('system_contents', $content);
            }
        }


        //copy subpages
        if ($pWithSubpages) {
            if (count($childs) > 0) {
                foreach ($childs as &$child) {
                    self::copyPage($child['id'], $lastId, 'into', true, $pWithoutThisPage);
                }
            }
        }

        return $lastId;
    }

    public static function domain() {
        switch (getArgv(4)) {
            case 'add':
                return self::addDomain();
            case 'delete':
                return self::delDomain();
            case 'getMaster':
                return self::getDomainMaster();
            case 'get':
                return self::getDomain();
            case 'save':
                return self::saveDomain();
        }
    }

    public static function getDomainMaster() {
        $id = getArgv('id') + 0;
        if (!Kryn::checkPageAcl($id, 'domainLanguageMaster', 'd')) {
            json(array('error' => 'access_denied'));
            ;
        }
        $cur = dbTableFetch('system_domain', 1, "id = $id");
        $res = dbTableFetch('system_domain', 1, "domain = '" . $cur['domain'] . "' AND master = 1");
        json($res);
    }

    public static function saveDomain() {
        $id = getArgv('id') + 0;

        $dbUpdate = array();
        $canChangeMaster = false;


        if (Kryn::checkPageAcl($id, 'domainName', 'd')) {
            $dbUpdate[] = 'domain';
        }

        if (Kryn::checkPageAcl($id, 'domainTitle', 'd')) {
            $dbUpdate[] = 'title_format';
        }

        if (Kryn::checkPageAcl($id, 'domainStartpage', 'd')) {
            $dbUpdate[] = 'startpage_id';
        }

        if (Kryn::checkPageAcl($id, 'domainPath', 'd')) {
            $dbUpdate[] = 'path';
        }
        if (Kryn::checkPageAcl($id, 'domainFavicon', 'd')) {
            $dbUpdate[] = 'favicon';
        }
        if (Kryn::checkPageAcl($id, 'domainLanguage', 'd')) {
            $dbUpdate[] = 'lang';
        }
        if (Kryn::checkPageAcl($id, 'domainLanguageMaster', 'd')) {
            $canChangeMaster = true;
            $dbUpdate[] = 'master';
        }
        if (Kryn::checkPageAcl($id, 'domainEmail', 'd')) {
            $dbUpdate[] = 'email';
        }


        if (Kryn::checkPageAcl($id, 'themeProperties', 'd')) {
            $dbUpdate[] = 'themeproperties';
        }
        if (Kryn::checkPageAcl($id, 'limitLayouts', 'd')) {
            $dbUpdate[] = 'layouts';
        }
        if (Kryn::checkPageAcl($id, 'domainProperties', 'd')) {
            $dbUpdate[] = 'extproperties';
        }
        if (Kryn::checkPageAcl($id, 'aliasRedirect', 'd')) {
            $dbUpdate[] = 'alias';
            $dbUpdate[] = 'redirect';
        }


        if (Kryn::checkPageAcl($id, 'phpLocale', 'd')) {
            $dbUpdate[] = 'phplocale';
        }
        if (Kryn::checkPageAcl($id, 'robotRules', 'd')) {
            $dbUpdate[] = 'robots';
        }
        if (Kryn::checkPageAcl($id, '404', 'd')) {
            $dbUpdate[] = 'page404interface';
            $dbUpdate[] = 'page404_id';
        }

        if (Kryn::checkPageAcl($id, 'domainOther', 'd')) {
            $dbUpdate[] = 'resourcecompression';
        }

        //todo need a acl for that
        $dbUpdate['session'] = json_encode(getArgv('session'));

        $domain = getArgv('domain', 1);
        if ($canChangeMaster) {
            if (getArgv('master') == 1) {
                dbUpdate('system_domain', "domain = '$domain'", array('master' => 0));
            }
        }

        Kryn::deleteCache('systemDomains-'.$id);
        dbUpdate('system_domain', array('id' => $id), $dbUpdate);
        self::updateDomainCache();

        json($domain);
    }

    public static function getDomain() {


        $id = getArgv('id') + 0;

        if (!Kryn::checkPageAcl($id, 'showDomain', 'd')) {
            json(array('error' => 'access_denied'));
        }

        $res['domain'] = dbExfetch("SELECT * FROM ".pfx."system_domain WHERE id = $id");
        json($res);
    }

    public static function delDomain() {
        $domain = getArgv('id') + 0;


        if (!Kryn::checkPageAcl($domain, 'deleteDomain', 'd')) {
            json(array('error' => 'access_denied'));
            ;
        }

        dbDelete('system_page', "domain_id = $domain");
        dbDelete('system_domain', "id = $domain");
        json(true);
    }

    public static function addDomain() {

        if (!Kryn::checkUrlAccess('admin/pages/addDomains'))
            json(array('error' => 'access_denied'));
        ;

        dbInsert('system_domain', array('domain', 'lang', 'master' => 0,
            'search_index_key' => md5(getArgv('domain') . '-' . mktime() . '-' . rand())));
        json(true);
    }
*/

    /*
     *
     *  Pages
     */

    /*public static function getPageVersion($pRsn) {
        $pRsn = $pRsn + 0;

        $res = array();
        if (!Kryn::checkPageAcl($pRsn, 'versions')) {
            json(array('error' => 'access_denied'));
            ;
        }

        //$res['live'] = dbTableFetch( 'system_page', 1, "id = $pRsn" );
        $res['versions'] = dbExFetch("SELECT v.*, u.username FROM ".pfx."system_user u, ".pfx."system_page_version v
            WHERE page_id = $pRsn AND u.id = v.owner_id ORDER BY created DESC", -1);

        return $res;
    }

    public static function getUrl($pRsn) {
        $pRsn = $pRsn + 0;

        json(Kryn::getPagePath($pRsn));
    }

    public static function deletePage($pPage, $pNoCacheRefresh = false) {

        $pPage = $pPage + 0;

        if (!Kryn::checkPageAcl($pPage, 'deletePages')) {
            json(array('error' => 'access_denied'));
            ;
        }

        Kryn::deleteCache('page-' . $pPage);

        $page = dbExfetch("SELECT * FROM ".pfx."system_page WHERE id = $pPage", 1);

        $subpages = dbTableFetch('system_page', 'pid = ' . $pPage, -1);
        if (count($subpages) > 0) {
            foreach ($subpages as $page) {
                self::deletePage($page['id'], true);
                dbExec("DELETE FROM ".pfx."system_page WHERE id = $pPage");
            }
        }

        dbExec("DELETE FROM ".pfx."system_page WHERE id = $pPage");

        if (!$pNoCacheRefresh) {
            self::cleanSort($page['domain_id'], $page['parent_id']);
            self::updateUrlCache($page['domain_id']);
            self::updateMenuCache($page['domain_id']);
        }
    }

    public static function getDomains($pLanguage) {
        $where = " 1=1 ";
        if ($pLanguage != "")
            $where = "lang = '$pLanguage'";

        $res = dbTableFetch('system_domain', DB_FETCH_ALL, "$where ORDER BY domain ASC");
        if (count($res) > 0) {
            foreach ($res as $domain) {

                if (Kryn::checkPageAcl($domain['id'], 'showDomain', 'd')) {
                    $result[] = $domain;
                }
            }
        }
        json($result);
    }


    public static function getTemplate($pTemplate) {
        global $cfg;

        Kryn::resetJs();
        Kryn::resetCss();

        $domain = urlencode(getArgv('domain'));

        $domainPath = str_replace('\\', '/', str_replace('\\\\\\\\', '\\', urldecode(getArgv('path'))));
        //        $url = 'http://'.getArgv('domain').str_replace('\\','/',str_replace('\\\\\\\\','\\',urldecode(getArgv('path'))));
        $path = 'http://' . $domain . $domainPath . PATH_MEDIA;

        Kryn::addJs($path . 'Kryn/mootools-core.js');
        Kryn::addJs($path . 'Kryn/mootools-more.js');
        Kryn::addJs($path . 'admin/js/ka.js');
        Kryn::addJs('http://' . $domain . $domainPath . 'KrynJavascriptGlobalPath.js');
        Kryn::addCss($path . 'admin/css/ka.layoutBox.css');
        Kryn::addCss($path . 'admin/css/inpage.css');
        Kryn::addCss($path . 'admin/css/ka.Field.css');
        Kryn::addCss($path . 'admin/css/ka.Button.css');
        Kryn::addCss($path . 'admin/css/ka.Select.css');
        Kryn::addCss($path . 'admin/css/ka.pluginChooser.css');
        Kryn::addCss($path . 'admin/css/inpage.css');

        Kryn::addCss($path . 'admin/css/ka.layoutBox.css');
        Kryn::addCss($path . 'admin/css/ka.layoutContent.css');

        //Kryn::addHeader( '<script type="text/javascript" src="'.'http://'.getArgv('domain').$domainPath.'inc/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>');

        $js = array(
            'MooEditable.js',
            'MooEditable.UI.MenuList.js',
            'MooEditable.Extras.js',
            'MooEditable.Image.js',
            'MooEditable.Table.js'
        );

        $css = array(
            'MooEditable.css',
            'MooEditable.Extras.css',
            'MooEditable.SilkTheme.css',
            'MooEditable.Image.css',
            'MooEditable.Table.css'
        );

        //foreach( $js as $t ){
        //    Kryn::addHeader( '<script type="text/javascript" src="'.'http://'.getArgv('domain').$domainPath.
        //        'inc/lib/mooeditable/Source/MooEditable/'.$t.'"></script>');
        //}

        foreach ($css as $t) {
            Kryn::addHeader(
                '<link rel="stylesheet" type="text/css" href="' . 'http://' . getArgv('domain') . $domainPath .
                'inc/lib/mooeditable/Assets/MooEditable/' . $t . '" />');
        }


        $id = getArgv('id') + 0;
        $page = dbTableFetch('system_page', 1, "id = $id");
        //$domain = dbTableFetch('system_domain', 1, "domain = '".getArgv('domain',1)."'");
        $domain = dbTableFetch('system_domain', 1, "id = '" . $page['domain_id'] . "'"); //.getArgv('domain',1)."'");

        $domainName = $domain['domain'];

        $http = 'http://';
        if ($_SERVER['HTTPS'] == '1' || strtolower($_SERVER['HTTPS']) == 'on')
            $http = 'https://';

        $port = '';
        if (($_SERVER['SERVER_PORT'] != 80 && $http == 'http://') ||
            ($_SERVER['SERVER_PORT'] != 443 && $http == 'https://')
        ) {
            $port = ':' . $_SERVER['SERVER_PORT'];
        }

        if (getArgv(1) == 'admin') {
            $domain['path'] = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        }

        if ($domain['path'] != '') {
            tAssign('path', $domain['path']);
            $cfg['path'] = $domain['path'];
            $cfg['templatepath'] = $domain['path'] . PATH_MEDIA;
            tAssign('cfg', $cfg);
            tAssign('_path', $domain['path']);
        }

        Kryn::$baseUrl = $http . $domainName . $port . $cfg['path'];
        if ($domain['master'] != 1) {
            Kryn::$baseUrl = $http . $domainName . $port . $cfg['path'] . $possibleLanguage . '/';
        }

        Kryn::$current_page = $page;
        Kryn::$page = $page;

        $page = KrynHtml::printPage();
        exit;
    }

    public static function getVersion($pPageRsn, $pVersion) {

        $pPageRsn = $pPageRsn + 0;

        if (!Kryn::checkPageAcl($pPageRsn, 'versions')) {
            json(array('error' => 'access_denied'));
            ;
        }
        $conts = array();
        if ($pVersion > 0) {
            $conts = dbTableFetch('system_contents', DB_FETCH_ALL, "page_id = $pPageRsn AND version_id = $pVersion
            AND (cdate > 0 AND cdate IS NOT NULL)  ORDER BY sort");
        }

        if (count($conts) > 0) {
            foreach ($conts as $cont) {
                $contents[$cont['box_id']][] = $cont;
            }
        }
        return $contents;
    }

    public static function getVersions() {
        $id = getArgv('id') + 0;


        if (!Kryn::checkPageAcl($id, 'versions')) {
            json(array('error' => 'access_denied'));
            ;
        }

        $res = dbExfetch("SELECT v.*, u.username FROM ".pfx."system_page_version v, ".pfx."system_user u
            WHERE u.id = v.owner_id AND page_id = $id ORDER BY created DESC", -1);
        json($res);
    }

    public static function addNotice($pRsn) {
        global $user;
        dbInsert('system_page_notices', array('page_id' => $pRsn, 'user_id' => $user->user_id, 'content',
            'created' => time()));
        json(true);
    }

    public static function getNotices($pRsn) {
        $res['notices'] = dbExfetch('SELECT n.*, u.username
            FROM '.pfx.'system_page_notices n, '.pfx.'system_user u
            WHERE u.id = n.user_id AND page_id = ' . $pRsn . ' ORDER BY id', DB_FETCH_ALL);
        $res['count'] = count($res['notices']);
        json($res);
    }

    public static function getTreeDomain($pDomainRsn) {
        $pDomainRsn = $pDomainRsn + 0;

        $viewAllPages = (getArgv('viewAllPages') == 1) ? true : false;
        if ($viewAllPages && !Kryn::checkUrlAccess('users/users/acl'))
            $viewAllPages = false;

        if (!$viewAllPages && !Kryn::checkPageAcl($pDomainRsn, 'showDomain', 'd')) {
            json(array('error' => 'access_denied'));
        }

        $domain = dbTableFetch('system_domain', 1, "id = $pDomainRsn");
        $domain['type'] = -1;

        $childs = dbTableFetch('system_page', DB_FETCH_ALL, "domain_id = $pDomainRsn AND pid = 0 ORDER BY sort");
        $domain['childs'] = array();

        $cachedUrls =& Kryn::getCache('systemUrls-' . $pDomainRsn);

        foreach ($childs as &$page) {
            if ($viewAllPages || Kryn::checkPageAcl($page['id'], 'showPage') == true) {
                $page['realUrl'] = $cachedUrls['id']['id=' . $page['id']];
                $page['hasChilds'] = Kryn::pageHasChilds($page['id']);
                $domain['childs'][] = $page;
            }
        }

        json($domain);
    }

    public static function getTree($pPageRsn) {
        $pPageRsn += 0;

        if ($pPageRsn == 0) return array();

        $viewAllPages = (getArgv('viewAllPages') == 1) ? true : false;
        if ($viewAllPages && !Kryn::checkUrlAccess('users/users/acl'))
            $viewAllPages = false;

        $page = dbExfetch('SELECT pid, domain_id FROM '.pfx.'system_page WHERE id = ' . $pPageRsn);

        if (!$viewAllPages && !Kryn::checkPageAcl($page['domain_id'], 'showDomain', 'd')) {
            json(array('error' => 'access_denied'));
            ;
        }

        if (!$viewAllPages && !Kryn::checkPageAcl($page['id'], 'showPage')) {
            json(array('error' => 'access_denied'));
            ;
        }

        $items = dbTableFetch('system_page', DB_FETCH_ALL, "pid = $pPageRsn ORDER BY sort");

        $cachedUrls =& Kryn::getCache('systemUrls-' . $page['domain_id']);

        if (count($items) > 0) {
            foreach ($items as &$item) {
                if ($viewAllPages || Kryn::checkPageAcl($item['id'], 'showPage')) {
                    $item['realUrl'] = $cachedUrls['id']['id=' . $item['id']];
                    $item['hasChilds'] = Kryn::pageHasChilds($item['id']);
                } else {
                    unset($item);
                }
            }
            return $items;

        } else {
            return array();
        }
    }

    public static function fixPageDomainRsn($pPageRsn, $pDomainRsn) {
        $pPageRsn += 0;

        dbUpdate('system_page', 'pid = ' . $pPageRsn, array('domain_id' => $pDomainRsn));

        $res = dbQuery('SELECT id FROM '.pfx.'system_page WHERE pid = ' . $pPageRsn);
        while ($row = dbFetch($res)) {
            self::fixPageDomainRsn($row['id'], $pDomainRsn);
        }
        dbFree($res);
    }
*/


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

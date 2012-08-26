<?php


/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@Core\Kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */


class adminPages {

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

    public static function setHide($pRsn, $pVisible) {
        $pRsn += 0;
        $pVisible += 0;

        if (Core\Kryn::checkPageAcl($pRsn, 'visible'))
            dbUpdate('system_page', 'id = ' . $pRsn, array('visible' => $pVisible));
    }

    public static function getPageInfo($pRsn) {

        $pRsn += 0;
        $page = dbTableFetch('system_page', "id = $pRsn", 1);
        $page['_parents'] = Core\Kryn::getPageParents($pRsn);

        if (!$page['_parents'])
            $page['_parents'] = array();

        return $page;

    }

    public static function getAliases($pRsn) {
        $pRsn = $pRsn + 0;

        $items = dbTableFetch('system_urlalias', 'to_page_id = ' . $pRsn, -1);
        json($items);
    }

    public static function deleteAlias($pRsn) {
        $pRsn = $pRsn + 0;

        dbDelete('system_urlalias', 'id = ' . $pRsn);
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
        Core\Kryn::deleteCache('Core\Kryn_pluginrelations');

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
            if (!Core\Kryn::checkPageAcl($newPage['domain_id'], 'addPages', 'd'))
                json(array('error' => 'access_denied'));
            ;
        } else {
            if (!Core\Kryn::checkPageAcl($newPage['parent_id'], 'addPages'))
                json(array('error' => 'access_denied'));
            ;
        }

        unset($newPage['id']);
        $lastId = dbInsert('system_page', $newPage);

        if (!$pWithoutThisPage)
            $pWithoutThisPage = $lastId;

        if ($newPage['parent_id'] == 0) {
            if (!Core\Kryn::checkPageAcl($newPage['domain_id'], 'canPublish', 'd'))
                json(array('error' => 'access_denied'));
            ;
        } else {
            if (!Core\Kryn::checkPageAcl($newPage['parent_id'], 'canPublish'))
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
        if (!Core\Kryn::checkPageAcl($id, 'domainLanguageMaster', 'd')) {
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


        if (Core\Kryn::checkPageAcl($id, 'domainName', 'd')) {
            $dbUpdate[] = 'domain';
        }

        if (Core\Kryn::checkPageAcl($id, 'domainTitle', 'd')) {
            $dbUpdate[] = 'title_format';
        }

        if (Core\Kryn::checkPageAcl($id, 'domainStartpage', 'd')) {
            $dbUpdate[] = 'startpage_id';
        }

        if (Core\Kryn::checkPageAcl($id, 'domainPath', 'd')) {
            $dbUpdate[] = 'path';
        }
        if (Core\Kryn::checkPageAcl($id, 'domainFavicon', 'd')) {
            $dbUpdate[] = 'favicon';
        }
        if (Core\Kryn::checkPageAcl($id, 'domainLanguage', 'd')) {
            $dbUpdate[] = 'lang';
        }
        if (Core\Kryn::checkPageAcl($id, 'domainLanguageMaster', 'd')) {
            $canChangeMaster = true;
            $dbUpdate[] = 'master';
        }
        if (Core\Kryn::checkPageAcl($id, 'domainEmail', 'd')) {
            $dbUpdate[] = 'email';
        }


        if (Core\Kryn::checkPageAcl($id, 'themeProperties', 'd')) {
            $dbUpdate[] = 'themeproperties';
        }
        if (Core\Kryn::checkPageAcl($id, 'limitLayouts', 'd')) {
            $dbUpdate[] = 'layouts';
        }
        if (Core\Kryn::checkPageAcl($id, 'domainProperties', 'd')) {
            $dbUpdate[] = 'extproperties';
        }
        if (Core\Kryn::checkPageAcl($id, 'aliasRedirect', 'd')) {
            $dbUpdate[] = 'alias';
            $dbUpdate[] = 'redirect';
        }


        if (Core\Kryn::checkPageAcl($id, 'phpLocale', 'd')) {
            $dbUpdate[] = 'phplocale';
        }
        if (Core\Kryn::checkPageAcl($id, 'robotRules', 'd')) {
            $dbUpdate[] = 'robots';
        }
        if (Core\Kryn::checkPageAcl($id, '404', 'd')) {
            $dbUpdate[] = 'page404interface';
            $dbUpdate[] = 'page404_id';
        }

        if (Core\Kryn::checkPageAcl($id, 'domainOther', 'd')) {
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

        Core\Kryn::deleteCache('systemDomains-'.$id);
        dbUpdate('system_domain', array('id' => $id), $dbUpdate);
        self::updateDomainCache();

        json($domain);
    }

    public static function getDomain() {


        $id = getArgv('id') + 0;

        if (!Core\Kryn::checkPageAcl($id, 'showDomain', 'd')) {
            json(array('error' => 'access_denied'));
        }

        $res['domain'] = dbExfetch("SELECT * FROM %pfx%system_domain WHERE id = $id");
        json($res);
    }

    public static function delDomain() {
        $domain = getArgv('id') + 0;


        if (!Core\Kryn::checkPageAcl($domain, 'deleteDomain', 'd')) {
            json(array('error' => 'access_denied'));
            ;
        }

        dbDelete('system_page', "domain_id = $domain");
        dbDelete('system_domain', "id = $domain");
        json(true);
    }

    public static function updateDomainCache() {
        $res = dbExec('SELECT * FROM '.pfx.'system_domain');
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

            Core\Kryn::deleteCache('systemDomain-' . $domain['id']);
        }
        Core\Kryn::setCache('systemDomains', $domains);
        return $domains;
    }

    public static function addDomain() {

        if (!Core\Kryn::checkUrlAccess('admin/pages/addDomains'))
            json(array('error' => 'access_denied'));
        ;

        dbInsert('system_domain', array('domain', 'lang', 'master' => 0,
            'search_index_key' => md5(getArgv('domain') . '-' . mktime() . '-' . rand())));
        json(true);
    }


    /*
     *
     *  Pages
     */

    public static function getPageVersion($pRsn) {
        $pRsn = $pRsn + 0;

        $res = array();
        if (!Core\Kryn::checkPageAcl($pRsn, 'versions')) {
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

        json(Core\Kryn::getPagePath($pRsn));
    }

    public static function deletePage($pPage, $pNoCacheRefresh = false) {

        $pPage = $pPage + 0;

        if (!Core\Kryn::checkPageAcl($pPage, 'deletePages')) {
            json(array('error' => 'access_denied'));
            ;
        }

        Core\Kryn::deleteCache('page-' . $pPage);

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

                if (Core\Kryn::checkPageAcl($domain['id'], 'showDomain', 'd')) {
                    $result[] = $domain;
                }
            }
        }
        json($result);
    }


    public static function getTemplate($pTemplate) {
        global $cfg;

        Core\Kryn::resetJs();
        Core\Kryn::resetCss();

        $domain = urlencode(getArgv('domain'));

        $domainPath = str_replace('\\', '/', str_replace('\\\\\\\\', '\\', urldecode(getArgv('path'))));
        //        $url = 'http://'.getArgv('domain').str_replace('\\','/',str_replace('\\\\\\\\','\\',urldecode(getArgv('path'))));
        $path = 'http://' . $domain . $domainPath . PATH_MEDIA;

        Core\Kryn::addJs($path . 'Core\Kryn/mootools-core.js');
        Core\Kryn::addJs($path . 'Core\Kryn/mootools-more.js');
        Core\Kryn::addJs($path . 'admin/js/ka.js');
        Core\Kryn::addJs('http://' . $domain . $domainPath . 'Core\KrynJavascriptGlobalPath.js');
        Core\Kryn::addCss($path . 'admin/css/ka.layoutBox.css');
        Core\Kryn::addCss($path . 'admin/css/inpage.css');
        Core\Kryn::addCss($path . 'admin/css/ka.Field.css');
        Core\Kryn::addCss($path . 'admin/css/ka.Button.css');
        Core\Kryn::addCss($path . 'admin/css/ka.Select.css');
        Core\Kryn::addCss($path . 'admin/css/ka.pluginChooser.css');
        Core\Kryn::addCss($path . 'admin/css/inpage.css');

        Core\Kryn::addCss($path . 'admin/css/ka.layoutBox.css');
        Core\Kryn::addCss($path . 'admin/css/ka.layoutContent.css');

        //Core\Kryn::addHeader( '<script type="text/javascript" src="'.'http://'.getArgv('domain').$domainPath.'inc/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>');

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

        /*foreach( $js as $t ){
            Core\Kryn::addHeader( '<script type="text/javascript" src="'.'http://'.getArgv('domain').$domainPath.
                'inc/lib/mooeditable/Source/MooEditable/'.$t.'"></script>');
        }*/

        foreach ($css as $t) {
            Core\Kryn::addHeader(
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

        Core\Kryn::$baseUrl = $http . $domainName . $port . $cfg['path'];
        if ($domain['master'] != 1) {
            Core\Kryn::$baseUrl = $http . $domainName . $port . $cfg['path'] . $possibleLanguage . '/';
        }

        Core\Kryn::$current_page = $page;
        Core\Kryn::$page = $page;

        $page = Core\KrynHtml::printPage();
        exit;
    }

    public static function getVersion($pPageRsn, $pVersion) {

        $pPageRsn = $pPageRsn + 0;

        if (!Core\Kryn::checkPageAcl($pPageRsn, 'versions')) {
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


        if (!Core\Kryn::checkPageAcl($id, 'versions')) {
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
        if ($viewAllPages && !Core\Kryn::checkUrlAccess('users/users/acl'))
            $viewAllPages = false;

        if (!$viewAllPages && !Core\Kryn::checkPageAcl($pDomainRsn, 'showDomain', 'd')) {
            json(array('error' => 'access_denied'));
        }

        $domain = dbTableFetch('system_domain', 1, "id = $pDomainRsn");
        $domain['type'] = -1;

        $childs = dbTableFetch('system_page', DB_FETCH_ALL, "domain_id = $pDomainRsn AND pid = 0 ORDER BY sort");
        $domain['childs'] = array();

        $cachedUrls =& Core\Kryn::getCache('systemUrls-' . $pDomainRsn);

        foreach ($childs as &$page) {
            if ($viewAllPages || Core\Kryn::checkPageAcl($page['id'], 'showPage') == true) {
                $page['realUrl'] = $cachedUrls['id']['id=' . $page['id']];
                $page['hasChilds'] = Core\Kryn::pageHasChilds($page['id']);
                $domain['childs'][] = $page;
            }
        }

        json($domain);
    }

    public static function getTree($pPageRsn) {
        $pPageRsn += 0;

        if ($pPageRsn == 0) return array();

        $viewAllPages = (getArgv('viewAllPages') == 1) ? true : false;
        if ($viewAllPages && !Core\Kryn::checkUrlAccess('users/users/acl'))
            $viewAllPages = false;

        $page = dbExfetch('SELECT pid, domain_id FROM '.pfx.'system_page WHERE id = ' . $pPageRsn);

        if (!$viewAllPages && !Core\Kryn::checkPageAcl($page['domain_id'], 'showDomain', 'd')) {
            json(array('error' => 'access_denied'));
            ;
        }

        if (!$viewAllPages && !Core\Kryn::checkPageAcl($page['id'], 'showPage')) {
            json(array('error' => 'access_denied'));
            ;
        }

        $items = dbTableFetch('system_page', DB_FETCH_ALL, "pid = $pPageRsn ORDER BY sort");

        $cachedUrls =& Core\Kryn::getCache('systemUrls-' . $page['domain_id']);

        if (count($items) > 0) {
            foreach ($items as &$item) {
                if ($viewAllPages || Core\Kryn::checkPageAcl($item['id'], 'showPage')) {
                    $item['realUrl'] = $cachedUrls['id']['id=' . $item['id']];
                    $item['hasChilds'] = Core\Kryn::pageHasChilds($item['id']);
                } else {
                    unset($item);
                }
            }
            return $items;

        } else {
            return array();
        }
    }

    public static function getIcons($pRsn) {
        global $cfg;

        $page = self::getPageByRsn($pRsn);

        if ($page['visible'] == '0' && $page['type'] != '2')
            $pngs[] = 'bullet_white';

        if ($page['access_denied'] == '1')
            $pngs[] = 'bullet_delete';

        if ($page['type'] == '1')
            $pngs[] = 'bullet_go';


        if (count($pngs) > 0)
            foreach ($pngs as $png) {
                $res .= '<img src="' . $cfg['path'] . PATH_MEDIA . '/admin/images/icons/' . $png . '_.png" />';
            }
        return $res;
    }

    public static function move() {
        $whoId = $_REQUEST['id'] + 0;
        $targetId = $_REQUEST['toid'] + 0;
        $mode = getArgv('mode', 1);


        $who = self::getPageByRsn($whoId);
        $target = self::getPageByRsn($targetId);


        //check if $who is parent of $target, then cancel
        $whoIsParent = false;
        $menus =& Core\Kryn::getCache('menus-' . $who['domain_id']);
        if (is_array($menus[$targetId])) {
            foreach ($menus[$targetId] as $parent) {
                if ($parent['id'] == $whoId) {
                    $whoIsParent = true;
                }
            }
        }

        if ($whoIsParent) {
            return false;
        }

        if (getArgv('toDomain') == 1) {
            $target['domain_id'] = $targetId;
            $targetId = 0;
            $mode = 'into';
        }


        if ($who['domain_id'] != $target['domain_id']) {
            $domainChanged = true;
        }

        if (!Core\Kryn::checkPageAcl($target['domain_id'], 'addPages', 'd')) {
            json(array('error' => 'access_denied'));
            ;
        }

        $oldRealUrl = Core\Kryn::pageUrl($whoId, $who['domain_id']);

        //handle mode
        switch ($mode) {
            case 'into':
                if ($targetId != 0 && !Core\Kryn::checkPageAcl($targetId, 'addPages')) {
                    json(array('error' => 'access_denied'));
                    ;
                }
                dbExec("UPDATE ".pfx."system_page SET pid = $targetId, domain_id = '" . $target['domain_id'] .
                       "', sort = 1, sort_mode = 'up' WHERE id = $whoId");
                break;

            case 'down':
                if ($target['parent_id'] == 0) {
                    if (!Core\Kryn::checkPageAcl($target['domain_id'], 'addPages', 'd')) {
                        json(array('error' => 'access_denied'));
                        ;
                    }
                } else {
                    if (!Core\Kryn::checkPageAcl($target['parent_id'], 'addPages')) {
                        json(array('error' => 'access_denied'));
                        ;
                    }
                }

                dbExec("UPDATE ".pfx."system_page SET pid = " . $target['parent_id'] . ", sort = " . $target['sort'] . ",
            sort_mode = 'down', domain_id = '" . $target['domain_id'] . "'  WHERE id = $whoId");
                break;
            case 'up':
                if ($target['parent_id'] == 0) {
                    if (!Core\Kryn::checkPageAcl($target['domain_id'], 'addPages', 'd')) {
                        json(array('error' => 'access_denied'));
                        ;
                    }
                } else {
                    if (!Core\Kryn::checkPageAcl($target['parent_id'], 'addPages')) {
                        json(array('error' => 'access_denied'));
                        ;
                    }
                }
                dbExec("UPDATE ".pfx."system_page SET pid = " . $target['parent_id'] . ", sort = " . $target['sort'] . ",
            sort_mode = 'up', domain_id = '" . $target['domain_id'] . "' WHERE id = $whoId");
                break;
        }

        if (getArgv('toDomain') || $domainChanged) {
            self::fixPageDomainRsn($whoId, $target['domain_id']);
        }

        Core\Kryn::deleteCache('page-' . $whoId);
        Core\Kryn::deleteCache('page-' . $target);


        $parents = Core\Kryn::getPageParents($whoId);
        foreach ($parents as &$parent) {
            Core\Kryn::deleteCache('page-' . $parent['id']);
        }

        $parents = Core\Kryn::getPageParents($target);
        foreach ($parents as &$parent) {
            Core\Kryn::deleteCache('page-' . $parent['id']);
        }

        self::cleanSort($target['domain_id'], 0);
        self::updateUrlCache($target['domain_id']);
        self::updateMenuCache($target['domain_id']);
        Core\Kryn::invalidateCache('navigation-' . $target['domain_id']);
        Core\Kryn::invalidateCache('systemNavigations-' . $target['domain_id']);
        Core\Kryn::invalidateCache('systemWholePage-' . $target['domain_id']);

        Core\Kryn::deleteCache('Core\Kryn_pluginrelations');

        if ($target['domain_id'] != $who['domain_id']) {
            self::cleanSort($who['domain_id'], 0);
            self::updateUrlCache($who['domain_id']);
            self::updateMenuCache($who['domain_id']);
            Core\Kryn::invalidateCache('navigation-' . $who['domain_id']);
            Core\Kryn::invalidateCache('systemNavigations-' . $who['domain_id']);
            Core\Kryn::invalidateCache('systemWholePage-' . $who['domain_id']);
        }

        return true;
    }

    public static function fixPageDomainRsn($pPageRsn, $pDomainRsn) {
        $pPageRsn += 0;

        dbUpdate('system_page', 'pid = ' . $pPageRsn, array('domain_id' => $pDomainRsn));

        $res = dbExec('SELECT id FROM '.pfx.'system_page WHERE pid = ' . $pPageRsn);
        while ($row = dbFetch($res)) {
            self::fixPageDomainRsn($row['id'], $pDomainRsn);
        }
    }

    public static function add() {

        $found = (getArgv('field_1') != '') ? true : false;
        $c = 1;
        $id = getArgv('id') + 0;
        $pos = getArgv('pos');
        $type = getArgv('type') + 0;

        $layout = getArgv('layout', 1);
        $visible = getArgv('visible');

        if (!getArgv('parentId'))
            jsonError('no_parent_id');

        if (!getArgv('parentObjectKey'))
            jsonError('no_parent_object_key');

        $targetItem = Core\KrynObjects::get(getArgv('parentObjectKey'), getArgv('parentId'));

        if (getArgv('parentObjectKey') == 'node'){
            $domain_id = $targetItem['domain_id'];
        } else {
            $domain_id = $targetItem['id'];
        }

        //3print_r($targetItem); exit;

        //if ($id > 0)
        //    $page = dbTableFetch('system_page', 1, "id = $id");

        //$domain_id = ($id > 0) ? $page['domain_id'] : getArgv('domain_id');
        //$pid = ($id > 0) ? $page['parent_id'] : 0;

        //todo, check ACL

        /*
        if ($pid == 0) {
            if (!Core\Kryn::checkPageAcl($domain_id, 'addPages', 'd')) {
                json(array('error' => 'access_denied'));
                ;
            }
        } else {
            if (!Core\Kryn::checkPageAcl($pid, 'addPages')) {
                json(array('error' => 'access_denied'));
                ;
            }
        }*/

        while ($found) {
            $val = getArgv('field_' . $c);
            if ($val == '') {
                $found = false;
                continue;
            }

            $row = array(
                'title' => $val,
                'access_denied' => 0,
                'cdate' => time(),
                'mdate' => time(),
                'cache' => 0,
                'access_from' => 0,
                'access_to' => 0,
                'url' => Core\Kryn::toModRewrite($val),
                'layout' => $layout,
                'visible' => $visible,
                'domain_id' => $domain_id,
                'type' => $type
            );

            Core\KrynObjects::add('node', $row, getArgv('parentId'), $pos, getArgv('parentObjectKey'));

            $c++;
        }

        self::updateUrlCache($domain_id);
        self::updateMenuCache($domain_id);

        if (getArgv('parentObjectKey') == 'node'){
            Core\Kryn::deleteCache('page-' . $id);
            $parents = Core\Kryn::getPageParents($id);
            foreach ($parents as &$parent) {
                Core\Kryn::deleteCache('page-' . $parent['id']);
            }
        }

        Core\Kryn::invalidateCache('navigation-' . $domain_id);
        Core\Kryn::invalidateCache('systemNavigations-' . $domain_id);
        Core\Kryn::invalidateCache('systemWholePage-' . $domain_id);

        self::updatePage2DomainCache();

        Core\Kryn::deleteCache('Core\Kryn_pluginrelations');
        json(true);
    }

    public static function save() {
        global $user, $kcache;

        $id = getArgv('id') + 0;

        Core\Kryn::deleteCache('page-' . $id);
        Core\Kryn::deleteCache('pageContents-' . $id);

        $domain_id = getArgv('domain_id') + 0;

        $aclCanPublish = false;
        $canSaveContents = false;

        if (Core\Kryn::checkPageAcl($id, 'contents') && Core\Kryn::checkPageAcl($id, 'canPublish')) {
            $aclCanPublish = true;
        }

        $groups = '';
        if (is_array(getArgv('access_from_groups')))
            $groups = esc(implode(",", getArgv('access_from_groups')));


        $active = 0;
        $publishPage = false;
        if (getArgv('andPublish') == 1 && $aclCanPublish) {
            $publishPage = true;
        }


        $updateArray = array();

        if (Core\Kryn::checkPageAcl($id, 'general')) {

            if (Core\Kryn::checkPageAcl($id, 'title'))
                $updateArray[] = 'title';

            if (Core\Kryn::checkPageAcl($id, 'page_title'))
                $updateArray[] = 'page_title';

            if (Core\Kryn::checkPageAcl($id, 'type'))
                $updateArray[] = 'type';

            if (Core\Kryn::checkPageAcl($id, 'url'))
                $updateArray[] = 'url';

            if (Core\Kryn::checkPageAcl($id, 'meta'))
                $updateArray[] = 'meta';

            $updateArray[] = 'target';
            $updateArray[] = 'link';
        }


        if (Core\Kryn::checkPageAcl($id, 'access')) {

            if (Core\Kryn::checkPageAcl($id, 'visible'))
                $updateArray[] = 'visible';

            if (Core\Kryn::checkPageAcl($id, 'access_denied'))
                $updateArray[] = 'access_denied';

            if (Core\Kryn::checkPageAcl($id, 'force_https'))
                $updateArray[] = 'force_https';

            if (Core\Kryn::checkPageAcl($id, 'releaseDates')) {
                $updateArray[] = 'access_from';
                $updateArray[] = 'access_to';
            }

            if (Core\Kryn::checkPageAcl($id, 'limitation')) {
                $updateArray['access_from_groups'] = $groups;
                $updateArray[] = 'access_need_via';
                $updateArray[] = 'access_nohidenavi';
                $updateArray[] = 'access_redirectto';
            }
        }

        if (Core\Kryn::checkPageAcl($id, 'contents')) {

            $canSaveContents = true;

            if (Core\Kryn::checkPageAcl($id, 'canChangeLayout'))
                $updateArray[] = 'layout';

        }

        if (Core\Kryn::checkPageAcl($id, 'properties')) {
            $updateArray[] = 'properties';
        }

        if (Core\Kryn::checkPageAcl($id, 'search')) {

            if (Core\Kryn::checkPageAcl($id, 'exludeSearch')) {
                $updateArray[] = 'unsearchable';

                if (getArgv('unsearchable', 1) + 0 > 0)
                    dbExec(
                        "DELETE FROM ".pfx."system_search WHERE page_id = '" . $id . "' AND domain_id=" . $domain_id);
            }

            if (Core\Kryn::checkPageAcl($id, 'searchKeys'))
                $updateArray[] = 'search_words';

        }

        $updateArray['draft_exist'] = ($publishPage) ? 0 : 1;
        $updateArray['mdate'] = time();


        $oldPage = dbTableFetch("system_page", "id = " . ($id + 0), 1);

        Core\Kryn::invalidateCache('systemWholePage-' . $oldPage['domain_id']);
        Core\Kryn::invalidateCache('systemNavigations-' . $oldPage['domain_id']);
        Core\Kryn::invalidateCache('navigation-' . $oldPage['domain_id']);

        $kcache['realUrl'] =& Core\Kryn::getCache('systemUrls-' . $oldPage['domain_id']);
        $oldRealUrl = $kcache['realUrl']['id']['id=' . $id];

        if (in_array('url', $updateArray) && $oldPage['url'] != getArgv('url') && getArgv('newAlias')) {


            if (getArgv('newAliasWithSub')) {
                $oldRealUrl .= '/%';
            }

            $existRow = dbExfetch(
                "SELECT id FROM ".pfx."system_urlalias WHERE to_page_id=" . $page . " AND url = '" . $oldRealUrl .
                "'", 1);

            if ($existRow['id'] + 0 == 0)
                dbInsert('system_urlalias', array('domain_id' => $oldPage['domain_id'], 'url' => $oldRealUrl,
                    'to_page_id' => $id));

        }

        if ($oldPage['url'] != getArgv('url')) {
            $indexedPages =& Core\Kryn::getCache('systemSearchIndexedPages');

            $need = $id . '_';
            foreach ($indexedPages as $key => &$index) {
                if (substr($key, 0, strlen($need)) == $need) {
                    unset($indexedPages[$key]);
                }
            }

            dbDelete('system_search', 'page_id	= ' . $id);
            Core\Kryn::invalidateCache('Core\KrynSearch_' . $oldPage['id']);
        }

        dbUpdate('system_page', array('id' => $id), $updateArray);

        //if page marked as unsearchable the delete it from index

        if ($canSaveContents && !(getArgv('dontSaveContents') == 1) && (getArgv('type') == 0 || getArgv('type') == 3)) {
            $contents = json_decode($_POST['contents'], true);

            $active = 0;
            if (getArgv('andPublish') == 1 && $aclCanPublish) {
                $active = 1;
                dbUpdate('system_page_version', array('page_id' => $id), array('active' => 0));
            }

            $time = time();

            $version_id = dbInsert('system_page_version', array(
                'page_id' => $id, 'owner_id' => $user->user_id, 'created' => $time, 'modified' => $time,
                'active' => $active
            ));

            if (count($contents) > 0) {
                foreach ($contents as $boxId => &$box) {

                    $sort = 1;

                    foreach ($box as &$content) {
                        $contentGroups = '';
                        if (is_array($content['access_from_groups']))
                            $contentGroups = esc(implode(",", $content['access_from_groups']));

                        if (Core\Kryn::checkPageAcl($id, 'content-' . $content['type'])) {

                            $contentRsn = dbInsert('system_contents', array(
                                'page_id' => $id,
                                'box_id' => $boxId,
                                'title' => $content['title'],
                                'content' => $content['content'],
                                'template' => $content['template'],
                                'type' => $content['type'],
                                'mdate' => $time,
                                'cdate' => $time,
                                'hide' => $content['hide'],
                                'sort' => $sort,
                                'version_id' => $version_id,
                                'unsearchable' => $content['unsearchable'],
                                'access_from' => $content['access_from'],
                                'access_to' => $content['access_to'],
                                'access_from_groups' => $contentGroups
                            ));

                            $sort++;
                        } else {

                            $oldContent = dbTableFetch('system_contents',
                                'id = ' . ($content['id'] + 0) . ' AND page_id = ' . $id . ' AND box_id = ' .
                                $boxId, 1);
                            if ($oldContent['id'] + 0 > 0 && $oldContent['type'] == $content['type']) {

                                $oldContent['version_id'] = $version_id;
                                unset($oldContent['id']);
                                dbInsert('system_contents', $oldContent);
                                $sort++;

                            }
                        }
                    }
                }
            }
        }


        if (Core\Kryn::checkPageAcl($id, 'resources')) {
            if (getArgv('getType') == 0 || getArgv('getType') == 3) { //page or deposit

                if (!file_exists(PATH_MEDIA.'css/_pages/')) {
                    klog('autofix', PATH_MEDIA.'css/_pages/ doesnt exists, create it.');
                    @mkdir(PATH_MEDIA.'css/_pages');
                }

                if (!file_exists(PATH_MEDIA.'js/_pages/')) {
                    klog('autofix', PATH_MEDIA.'js/_pages/ doesnt exists, create it.');
                    @mkdir(PATH_MEDIA.'js/_pages');
                }

                if (Core\Kryn::checkPageAcl($id, 'css')) {
                    if (getArgv('resourcesCss') != '')
                        Core\Kryn::fileWrite(PATH_MEDIA."css/_pages/$id.css", getArgv('resourcesCss'));
                    else if (file_exists(PATH_MEDIA."css/_pages/$id.css"))
                        @unlink(PATH_MEDIA."css/_pages/$id.css");
                }


                if (Core\Kryn::checkPageAcl($id, 'js')) {
                    if (getArgv('resourcesJs') != '')
                        Core\Kryn::fileWrite(PATH_MEDIA."js/_pages/$id.js", getArgv('resourcesJs'));
                    else
                        @unlink(PATH_MEDIA."js/_pages/$id.js");
                }
            }
        }

        self::updateUrlCache($domain_id);
        self::updateMenuCache($domain_id);

        Core\Kryn::deleteCache('Core\Kryn_pluginrelations');

        $res = self::getPage($id);
        $res['version_id'] = $version_id;
        json($res);
    }

    public static function updateMenuCache($pDomainRsn) {
        $resu = dbExec("SELECT id, title, url, pid FROM ".pfx."system_page WHERE
        				 domain_id = $pDomainRsn AND (type = 0 OR type = 1 OR type = 4)");
        $res = array();
        while ($page = dbFetch($resu, 1)) {

            if ($page['type'] == 0)
                $res[$page['id']] = self::getParentMenus($page);
            else
                $res[$page['id']] = self::getParentMenus($page, true);

        }

        Core\Kryn::setCache("menus-$pDomainRsn", $res);
        Core\Kryn::invalidateCache('navigation_' . $pDomainRsn);

        return $res;
    }

    public static function getParentMenus($pPage, $pAllParents = false) {
        $pid = $pPage['parent_id'];
        $res = array();
        while ($pid != 0) {
            $parent_page =
                dbExfetch("SELECT id, title, url, pid, type FROM ".pfx."system_page WHERE id = " . $pid, 1);
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

        $resu =
            dbExec("SELECT id, title, url, type, link FROM ".pfx."system_page WHERE domain_id = $pDomainRsn AND parent_id IS NULL");
        $res = array('url' => array(), 'id' => array());

        $domain = Core\Kryn::getDomain($pDomainRsn);

        while ($page = dbFetch($resu)) {

            $page = self::__pageModify($page, array('realurl' => ''));
            $newRes = self::updateUrlCacheChilds($page, $domain);
            $res['url'] = array_merge($res['url'], $newRes['url']);
            $res['id'] = array_merge($res['id'], $newRes['id']);
        }

        $aliasRes = dbExec('SELECT to_page_id, url FROM '.pfx.'system_urlalias WHERE domain_id = ' . $pDomainRsn);
        while ($row = dbFetch($aliasRes)) {
            $res['alias'][$row['url']] = $row['to_page_id'];
        }

        self::updatePage2DomainCache();
        Core\Kryn::setCache("systemUrls-$pDomainRsn", $res);
        return $res;
    }

    public static function updatePage2DomainCache() {

        $r2d = array();
        $res = dbExec('SELECT id, domain_id FROM '.pfx.'system_page ');

        while ($row = dbFetch($res)) {
            $r2d[$row['domain_id']] .= $row['id'] . ',';
        }
        Core\Kryn::setCache('systemPages2Domain', $r2d);
        return $r2d;
    }

    public static function updateUrlCacheChilds($pPage, $pDomain = false) {
        $res = array('url' => array(), 'id' => array(), 'r2d' => array());

        error_log('+ '.print_r($pPage, true));

        if ($pPage['type'] < 2) { //page or link or folder
            if ($pPage['realurl'] != '') {
                $res['url']['url=' . $pPage['realurl']] = $pPage['id'];
                $res['id'] = array('id=' . $pPage['id'] => $pPage['realurl']);
            } else {
                $res['id'] = array('id=' . $pPage['id'] => $pPage['url']);
            }
        }

        $pages = dbExfetchAll("SELECT id, title, url, type, link
                             FROM ".pfx."system_page
                             WHERE parent_id = " . $pPage['id']);

        if (is_array($pages)) {
            foreach ($pages as $page) {


                Core\Kryn::deleteCache('page_' . $page['id']);

                $page = self::__pageModify($page, $pPage);
                $newRes = self::updateUrlCacheChilds($page);

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

    public static function getPage($pRsn, $pLock = false) {

        $pRsn = $pRsn + 0;

        $res = self::getPageByRsn($pRsn);
        //$res['resourcesCss'] = Core\Kryn::readFile(PATH_MEDIA."css/_pages/$pRsn.css");
        //$res['resourcesJs'] = Core\Kryn::readFile(PATH_MEDIA."js/_pages/$pRsn.js");

        $curVersion = dbTableFetch('system_page_version', 1, "page_id = $pRsn AND active = 1");
        if (!$curVersion['id'] > 0) {
            $curVersion = dbTableFetch('system_page_version', 1, "page_id = $pRsn ORDER BY id DESC");
        }

        $contents = self::getVersion($pRsn, $curVersion['id']);
        $res['_activeVersion'] = $curVersion['id'];

        $res['alias'] = dbExfetch('SELECT * FROM '.pfx.'system_urlalias WHERE to_page_id=' . $pRsn, -1);

        $domain =
            dbExfetch("SELECT d.id FROM ".pfx."system_domain d, ".pfx."system_page p WHERE p.domain_id = d.id AND p.id = $pRsn");
        Core\Kryn::$domain = $domain;

        $cachedUrls =& Core\Kryn::readCache('systemUrls');
        $res['realUrl'] = $cachedUrls['id']['id=' . $pRsn];
        $res['contents'] = json_encode($contents);

        $res['versions'] =
            dbExfetch("SELECT version_id, MAX(mdate) FROM ".pfx."system_contents WHERE page_id = $pRsn GROUP BY version_id", DB_FETCH_ALL);

        json($res);
    }
}

?>

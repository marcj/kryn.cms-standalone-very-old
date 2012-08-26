<?php



    public function olDadmin(){

            $content = null;
            switch (getArgv(2)) {
                case 'mini-search':
                    return self::miniSearch(getArgv('q', 1));
                case 'loadCss':
                    return self::loadCss();
                case 'widgets':
                    require(PATH_MODULE . "admin/adminWidgets.class.php");
                    return adminWidgets::init();
                case 'pages':
                    json(adminPages::init());
                    break;
                case 'backend':
                    switch (getArgv(3)) {
                        case 'help':
                            switch (getArgv(4)) {
                                case 'load':
                                    return self::loadHelp();
                                case 'loadTree':
                                    return json(self::loadHelpTree(getArgv('lang')));
                            }
                            break;
                        case 'nothing':
                            die("");


                        case 'objectGetLabel':
                            $content = self::objectGetLabel(getArgv('object'));
                            break;
                        case 'objectGetItems':
                            $content = self::objectGetItems(getArgv('object'));
                            break;
                        case 'objectTree':
                            $content = self::getObjectTree(getArgv('object'), getArgv('depth')+0);
                            break;
                        case 'objectTreeRoot':
                            $content = self::getObjectTreeRoot(getArgv('object'), getArgv('rootId'));
                            break;

                        case 'objectParents':
                            $content = self::getObjectParents(getArgv('object'));
                            break;
                        case 'moveObject':
                            $content = self::moveObject(getArgv('source'), getArgv('target'), getArgv('mode', 2));
                            break;

                        case 'autoChooser':
                            $content = self::autoChooser(getArgv('object', 2), getArgv('page'));
                            break;
                        case 'getPluginElements':
                            $content = self::getPluginElements(getArgv('object', 2));
                            break;


                        case 'clearCache':
                            json(self::clearCache());
                        case 'loadJs':
                            return self::loadJs();
                        case 'loadCustomJs':
                            return self::loadCustomJs();
                        case 'loadLayoutElementFile':
                            return self::loadLayoutElementFile(getArgv('template'));
                        case 'getContentTemplate':
                            return self::loadContentLayout();
                        case 'fixDb':
                            return self::fixDb();
                        case 'saveDesktop':
                            self::saveDesktop(getArgv('icons'));

                        case 'getMenus':
                            return self::getMenus();
                        case 'saveUserSettings':
                            $content = self::saveUserSettings();
                            break;
                        case 'getDefaultImages':
                            self::getDefaultImages();
                            break;
                        case 'objects':
                            self::sendObjectStore();

                        case 'imageThumb':
                            $content = adminFilemanager::imageThumb(getArgv('path'),getArgv('width'),getArgv('height'));
                            break;
                        case 'showImage':
                            $content = adminFilemanager::showImage(getArgv('path'));
                            break;

                        case 'stream':
                            $content = self::stream();
                            break;
                        case 'navigationPreview':
                            return self::navigationPreview(getArgv('content'));
                        case 'pointerPreview':
                            return self::pointerPreview(getArgv('content'));
                        case 'plugins':
                            require(PATH_MODULE . "admin/adminPlugins.class.php");
                            return adminPlugins::init();
                        case 'window':
                            if (getArgv(4) == 'sessionbasedFileUpload') {
                                require(PATH_MODULE . 'admin/adminWindow.class.php');
                                $_REQUEST['cmd'] = 'sessionbasedFileUpload';
                                $content = adminWindow::handle();
                            }
                            break;
                        case 'searchIndexer' :
                            adminSearchIndexer::init();
                            break;
                    }
                    break;
                case 'files':
                    $content = adminFilemanager::init();
                    break;
                case 'filebrowser':
                    require(PATH_MODULE . 'admin/filebrowser.class.php');
                    $content = filebrowser::init();
                    break;
                case 'system':


                    switch (getArgv(3)) {
                        case 'tools':
                            switch (getArgv(4)) {
                                case 'database':
                                    return self::database();
                                case 'logs':
                                    return json(self::getLogs());
                            }
                            break;
                        case 'module':

                            RestServer::create('admin/system/module', 'adminModule')
                            ->collectRoutes()
                            ->run();

                        case 'settings':
                            $content = adminSettings::init();
                            break;
                        case 'backup':
                            $content = adminBackup::init();
                            break;
                        case 'languages':
                            require(PATH_MODULE . "admin/adminLanguages.class.php");
                            $content = adminLanguages::init();
                            break;
                        case 'layout':
                            adminLayout::init();
                            break;
                        default:
                            $content = self::systemInfo();
                            break;
                    }
                    break;
            }
            if ($content !== null)
                json($content);
    }



    public static function getObjectParents($pObjectUrl){
        return krynObjects::getParentsFromUri($pObjectUrl);
    }

    public static function getObjectTree($pObjectUrl, $pDepth = 0){
        return krynObjects::getTreeFromUri($pObjectUrl, $pDepth);
    }

    public static function getObjectTreeRoot($pObjectUrl, $pRootId){
        return krynObjects::getTreeRoot($pObjectUrl, $pRootId);
    }

    public static function moveObject($pSourceObjectUrl, $pTargetObjectUrl, $pMode){
        return krynObjects::move($pSourceObjectUrl, $pTargetObjectUrl, $pMode);
    }


    /**
     * Returns all plugin elements for specified object
     *
     * @static
     * @param $pObjectKey
     * @return array
     */
    public static function getPluginElements($pObjectKey){

        if (!Kryn::$objects[$pObjectKey]) return array('error' => 'object_not_found');

        $definition = Kryn::$objects[$pObjectKey];

        $cachedPluginRelations =& Kryn::getCache('kryn_pluginrelations');
        if (true || !$cachedPluginRelations || count($cachedPluginRelations) == 0) {
            self::cachePluginsRelations();
            $cachedPluginRelations =& Kryn::getCache('kryn_pluginrelations');
        }

        $module = $definition['_extension'];

        $previewPluginPages = array();

        if (!$definition['plugins']) return array('error' => 'no_plugins_defined');
        $plugins = explode(',', str_replace(' ', '', $definition['plugins']));

        foreach ($plugins as $plugin) {

            $moduleToUse = $module;
            $pluginToUse = $plugin;

            if (strpos($plugin, '/') !== false) {
                $ex = explode('/', $plugin);
                $moduleToUse = $ex[0];
                $pluginToUse = $ex[1];
            }

            $pages =& $cachedPluginRelations[$moduleToUse][$pluginToUse];
            if (count($pages) > 0) {
                foreach ($pages as &$page) {
                    $previewPluginPages[$moduleToUse . '/' . $pluginToUse][$page['domain_id']][$page['id']] =
                        array(
                            'title' => $page['title'],
                            'path' => Kryn::getPagePath($page['id'])
                        );
                }
            }
        }

        return $previewPluginPages;
    }




    /**
     * Loads all plugins from system_contents to a indexed cached array
     */
    private static function cachePluginsRelations() {

        $res = dbExec('
        SELECT p.domain_id, p.id, c.content, p.title
        FROM
            %pfx%system_contents c,
            %pfx%system_page_version v,
            %pfx%system_page p
        WHERE 1=1
            AND c.type = \'plugin\'
            AND c.hide = 0
            AND v.id = c.version_id
            AND p.id = v.page_id
            AND (p.access_denied = \'0\' OR p.access_denied IS NULL)
            AND v.active = 1
        ');

        if (!$res) {
            Kryn::setCache('kryn_pluginrelations', array());
            return;
        }

        $pluginRelations = array();

        while ($row = dbFetch($res)) {

            preg_match('/([a-zA-Z0-9_-]*)::([a-zA-Z0-9_-]*)::(.*)/', $row['content'], $matches);
            $pluginRelations[$matches[1]][$matches[2]][] = $row;

        }
        Kryn::setCache('kryn_pluginrelations', $pluginRelations);
    }


    public static function objectGetItems($pUrl){

        if (is_numeric($pUrl)){
            //compatibility
            $object_key = '';
        } else {
            list($object_key, $object_ids, $params) = krynObjects::parseUri($pUrl);
        }

        $definition = Kryn::$objects[$object_key];
        if (!$definition) return array('error' => 'object_not_found');

        //todo check here access

        if ($definition['chooserFieldDataModel'] == 'custom'){

            $class = $definition['chooserFieldDataModel'];
            $classFile = PATH_MODULE.'/'.$definition['_extension'].'/'.$class.'.class.php';
            if (!file_exists($classFile)) return array('error' => 'classfile_not_found');

            require_once($classFile);
            $dataModel = new $class($object_key);

            $items = $dataModel->getItems($object_ids);

        } else {

            $primaryKeys = krynObjects::getPrimaries($object_key);

            $fields = array_keys($primaryKeys);

            foreach ($definition['chooserFieldDataModelFields'] as $key => $val){
                $fields[] = $key;
            }

            $items = krynObjects::getList($object_key, $object_ids, array(
                'fields' => $fields,
                'condition' => $definition['chooserFieldDataModelCondition']
            ));
        }

        $res = array();
        if (is_array($items)){
            foreach ($items as &$item){

                $keys = array();
                foreach($primaryKeys as $key => &$field){
                    $keys[] = rawurlencode($item[$key]);
                }
                $res[ implode(',', $keys) ] = $item;
            }
        }

        return $res;
    }

    public static function objectGetLabel($pUrl){

        if (is_numeric($pUrl)){
            //compatibility
            $object_key = '';
        } else {
            list($object_key, $object_id, $params) = krynObjects::parseUri($pUrl);
        }

        $definition = Kryn::$objects[$object_key];
        if (!$definition) return array('error' => 'object_not_found');

        //todo check here access

        if ($definition['chooserFieldDataModel'] == 'custom'){

            $class = $definition['chooserFieldDataModelClass'];
            $classFile = PATH_MODULE.'/'.$definition['_extension'].'/'.$class.'.class.php';
            if (!file_exists($classFile)) return array('error' => 'classfile_not_found');

            require_once($classFile);
            $dataModel = new $class($object_key);

            $item = $dataModel->getItem($object_id[0]);
            return array(
                'object' => $object_key,
                'values' => $item
            );

        } else {

            $fields = array();
            foreach ($definition['fields'] as $key => $field){
                if ($field['primaryKey'])
                    $fields[] = $key;
            }

            $fields[] = $definition['chooserFieldDataModelField'];

            $item = krynObjects::get($object_key, $object_id[0], array(
                'fields' => $fields,
                'condition' => $definition['chooserFieldDataModelCondition']
            ));

            return array(
                'object' => $object_key,
                'values' => $item
            );

        }
    }

    /**
     * @static
     * @param $pObjectKey
     * @param int $pPage
     * @return array
     */
    public static function autoChooser($pObjectKey, $pPage = 1){

        //todo, check permissions

        $definition = Kryn::$objects[$pObjectKey];

        if ($definition['chooserBrowserDataModel'] == 'none')
            return;

        $order = false; //todo

        if ($definition['chooserBrowserDataModel'] == 'custom' && $definition['chooserBrowserDataModelClass']){

            $class = $definition['chooserBrowserDataModelClass'];
            $classFile = PATH_MODULE.'/'.$definition['_extension'].'/'.$class.'.class.php';
            if (!file_exists($classFile)) return array('error' => 'classfile_not_found');

            require_once($classFile);
            $dataModel = new $class($pObjectKey);

            $itemsCount = $dataModel->getCount();
            if (is_array($itemsCount) && $itemsCount['error'])
                return $itemsCount;

            $itemsPerPage = 15;
            $start = ($itemsPerPage*$pPage)-$itemsPerPage;
            $pages = ceil($itemsCount/$itemsPerPage);

            $items = $dataModel->getItems(
                $definition['chooserBrowserDataModelCondition'], $start, $itemsPerPage, null, $order
            );

            return array(
                'items' => count($items)>0?$items:false,
                'count' => $itemsCount,
                'pages' => $pages
            );
        }

        $fields = array();

        foreach ($definition['fields'] as $key => $field){
            if ($field['primaryKey'])
                $fields[] = $key;
        }

        if ($definition['chooserBrowserAutoColumns']){
            foreach ($definition['chooserBrowserAutoColumns'] as $key => $column){
                $fields[] = $key;
            }
        } else {
            if ($definition['chooserBrowserDataModelFields']){
                $tempFields = explode(',', str_replace(' ', '', $definition['chooserBrowserDataModelFields']));
                if (is_array($tempFields)){
                    foreach ($tempFields as $field){
                        $fields[] = $field;
                    }
                }
            }
        }

        $itemsCount = krynObjects::getCount($pObjectKey, $definition['chooserCondition']);
        if (is_array($itemsCount) && $itemsCount['error'])
            return $itemsCount;

        $itemsPerPage = 15;
        $start = ($itemsPerPage*$pPage)-$itemsPerPage;
        $pages = ceil($itemsCount/$itemsPerPage);

        $items = krynObjects::getList($pObjectKey, false, array(
            'fields' => implode(',', $fields),
            'limit'  => $itemsPerPage,
            'offset' => $start,
            'condition' => $definition['chooserBrowserDataModelCondition']
        ));

        return array(
            'items' => count($items)>0?$items:false,
            'count' => $itemsCount,
            'pages' => $pages
        );

    }


    public static function loadContentLayout() {

        $content = array();

        $vars = array('title', 'type', 'template');

        foreach ($vars as $p) {
            $content[$p] = $_GET[$p];
        }

        tAssign('content', $content);

        $content['template'] = str_replace('..', '', $content['template']);
        $tpl = Kryn::fileRead(PATH_MEDIA . $content['template']);

        $tpl =
            str_replace('{$content.title}', '<span class="ka-layoutelement-content-title">{$content.title}</span>', $tpl);
        $tpl = str_replace('{$content.content}', '<div class="ka-layoutelement-content-content"></div>', $tpl);

        json(tFetch('string:' . $tpl));
    }

    public static function loadLayoutElementFile($pFile) {

        $pFile = str_replace('..', '', $pFile);

        $found = false;
        foreach (Kryn::$configs as $config) {
            if ($config['themes']) {
                foreach ($config['themes'] as $themeTitle => $layouts) {
                    if ($layouts['layoutElement']) {
                        foreach ($layouts['layoutElement'] as $layoutTiel => $layoutFile) {
                            if ($pFile == $layoutFile)
                                $found = true;
                        }
                    }
                }
            }
        }

        $res = false;
        if ($found) {
            $res['layout'] = tFetch($pFile);
        }
        json($res);
    }

    public static function logs() {


    }

    public static function database() {

        $res = array('fetchtime' => 0);

        $sql = getArgv('sql');

        $startExec = microtime(true);
        $execRes = dbExec($sql);
        $res['exectime'] = microtime(true) - $startExec;

        if (!$execRes) {
            $res['error'] = dbError();
        } else {
            $startFetch = microtime(true);
            $res['items'] = dbFetch($execRes, -1);
            $res['fetchtime'] = microtime(true) - $startFetch;
        }

        json($res);
    }

    public static function miniSearch($pQ) {

        $res = array();
        foreach (Kryn::$modules as &$mod) {
            if (method_exists($mod, 'searchAdmin')) {
                $res = array_merge($res, $mod->searchAdmin($pQ));
            }
        }

        json($res);

    }

    public static function getLogs() {

        if (getArgv(5) == 'clear') {
            dbDelete('system_log');
            json(1);
        }

        $page = 1;
        if (getArgv('page') + 0 > 1) {
            $page = getArgv('page') + 0;
        }


        $perPage = 40;
        $where = "WHERE ";

        switch (getArgv('area')) {

            case '404':
                $where .= "code = '404'";
                break;

            case 'database':
                $where .= "code = 'database'";
                break;

            case 'authentication':
                $where .= "code = 'authentication'";
                break;

            case 'system':
                $where .= "code = 2 OR code = 2048";
                break;

            case 'all':
            default:
                $where = "";

        }


        $from = ($perPage * $page) - $perPage;
        $count = $perPage;

        $return = array('items', 'count');

        $sql = "SELECT date, ip, username, code, message FROM %pfx%system_log $where";

        if ($sql == "") return $return;

        $limit = ' ORDER BY date DESC LIMIT ' . $count . ' OFFSET ' . $from;
        $res = dbExec($sql . $limit, -1);

        $count =
            dbExfetch(preg_replace('/SELECT(.*)FROM/mi', 'SELECT count(id) as ctn FROM', str_replace("\n", " ", $sql)));
        $return['count'] = $count['ctn'];

        $maxPages = 1;
        if ($return['count'] > 0) {
            $maxPages = ceil($return['count'] / $perPage);
        }
        $return['maxPages'] = $maxPages;
        $return['items'] = dbExfetch($sql . $limit, -1);

        foreach ($return['items'] as &$item) {
            $item[0] = date('d M H:i:s', $item['date']);
            $item[1] = $item['ip'];
            $item[2] = $item['username'];
            $item[3] = $item['code'];
            $item[4] = $item['message'];
        }

        return $return;
    }

    public function searchAdmin($pQuery) {

        $res = array();

        $lang = getArgv('lang');

        //pages
        $pages = dbExfetch("SELECT p.id, p.title, d.lang
            FROM %pfx%system_page p, %pfx%system_domains d
            WHERE d.id = p.domain_id AND p.title LIKE '%$pQuery%' LIMIT 10 OFFSET 0", -1);

        if (count($pages) > 0) {
            foreach ($pages as $page)
                $respages[] =
                    array($page['title'], 'admin/pages/', array('id' => $page['id'], 'lang' => $page['lang']));
            $res[_l('Pages')] = $respages;
        }

        //help
        $helps = array();
        foreach (Kryn::$configs as $key => $mod) {
            $helpFile = PATH_MODULE . "$key/lang/help_$lang.json";
            if (!file_exists($helpFile)) continue;
            if (count($helps) > 10) continue;

            $json = json_decode(Kryn::fileRead($helpFile), 1);
            if (is_array($json) && count($json) > 0) {
                foreach ($json as $help) {

                    if (count($helps) > 10) continue;
                    $found = false;

                    if (preg_match("/$pQuery/i", $help['title']))
                        $found = true;

                    if (preg_match("/$pQuery/i", $help['tags']))
                        $found = true;

                    if (preg_match("/$pQuery/i", $help['help']))
                        $found = true;

                    if ($found)
                        $helps[] = array($help['title'], 'admin/help', array('id' => $key . '/' . $help['id']));
                }
            }
        }
        if (count($helps) > 0) {
            $res[_l('Help')] = $helps;
        }

        return $res;
    }

    public static function loadHelp() {
        $id = getArgv('id');

        $temp = explode("/", $id);
        $module = $temp[0];
        $helpId = $temp[1];
        $lang = getArgv('lang');

        $helpFile = PATH_MODULE . "$module/lang/help_$lang.json";
        $json = Kryn::fileRead($helpFile);
        $langs = json_decode($json, 1);
        $res = false;
        foreach ($langs as &$help) {
            if ($help['id'] == $helpId)
                $res = $help;
        }
        if (!$res && $lang != 'en') {
            $_REQUEST['lang'] = 'en';
            self::loadHelp();

        } else {
            if (!$res)
                json(array('title' => 'Not found'));
            else
                json($res);
        }
    }


    public static function loadHelpTree($pLang = 'en') {

        $res = array();
        foreach (Kryn::$configs as $modCode => &$config) {

            $langFile = PATH_MODULE . "$modCode/lang/help_$pLang.json";
            if (!file_exists($langFile))
                $langFile = PATH_MODULE . "$modCode/lang/help_en.json";
            if (!file_exists($langFile))
                continue;

            $modTitle = $config['title'][$pLang] ? $config['title'][$pLang] : $config['title']['en'];

            $help = Kryn::fileRead($langFile);
            $help = json_decode($help, true);

            if (count($help) > 0) {
                foreach ($help as &$item) {

                    $item['open'] = $modCode . '/' . $item['id'];
                    $res[$modTitle][] = $item;

                }
            }

        }

        return $res;

    }

    public static function fixDb() {


    }


    public static function stream() {

        $res['time'] = date('H:i');
        $res['last'] = time();

        $sessionCount = dbExfetch('SELECT count(*) as mcount FROM %pfx%system_sessions', 1);
        $res['sessions_count'] = $sessionCount['mcount'];

        $res['hasCrawlPermission'] = adminSearchIndexer::hasPermission();

        foreach (Kryn::$configs as $key => $conf) {

            if ($conf['_corruptConfig']) {

                $res['corruptJson'][] = $key;
            }
            $stream = $conf['stream'];

            if ($stream && method_exists(Kryn::$modules[$key], $stream)) {

                $res[$key] = Kryn::$modules[$key]->$stream();
            }
        }


        json($res);
    }

    public static function systemInfo() {

        $res['version'] = Kryn::$configs['kryn']['version'];

        json($res);
    }



    public static function addVersion($pTable, $pPrimary) {

        foreach ($pPrimary as $fieldName => $fieldValue) {
            if ($fieldValue+0 > 0)
                $sql = " AND $fieldName = ".($fieldValue+0);
            else
                $sql = " AND $fieldName = '" . esc($fieldValue) . "'";
        }

        $row = dbTableFetch($pTable, "1=1 $sql", 1);

        return self::addVersionRow($pTable, $pPrimary, $row);
    }

    public static function addVersionRow($pTable, $pPrimary, $pRow) {

        $code = $pTable;
        foreach ($pPrimary as $fieldName => $fieldValue) {
            $code .= '_' . $fieldName . '=' . $fieldValue;
        }

        $content = json_encode($pRow);

        $currentVersion =
            dbTableFetch('system_frameworkversion', "code = '" . esc($code) . "' ORDER BY version DESC", 1);

        $version = $currentVersion['version'] + 1;
        $new = array(
            'code' => $code,
            'content' => $content,
            'version' => $version,
            'cdate' => time(),
            'user_id' => Kryn::$adminClient->user_id
        );

        dbInsert('system_frameworkversion', $new);
        return $version;
    }


    public static function getVersion($pTable, $pPrimary, $pVersion) {

        $code = $pTable;
        foreach ($pPrimary as $fieldName => $fieldValue) {
            $code .= '_' . $fieldName . '=' . $fieldValue;
        }
        $version = $pVersion + 0;

        $version = dbTableFetch('system_frameworkversion', "code = '$code' AND version = $version", 1);

        return json_decode($version['content'], true);
    }


    /*
    *
    * WIDGET STUFF
    *
    */

    public function widgetLastLogins($pConf) {
        $res['title'] = "Letzte Sessions";

        $sessions = dbExFetch('SELECT s.*, u.username
                    FROM ' . pfx . 'system_sessions s, ' . pfx . 'system_user u
                    WHERE s.user_id = u.id AND u.id > 0
                    ORDER BY time DESC
                    LIMIT 10 OFFSET 0', DB_FETCH_ALL);
        tAssign('sessions', $sessions);
        $res['content'] = tFetch('admin/overview.widget.sessions.tpl');

        return $res;
    }

    public function widgetVersion() {

        $res['title'] = 'Kryn ' . Kryn::$configs['kryn']['version'];
        $res['content'] = '
            <span style="color: green;">Sie benutzen die aktuellste Version.</span>    
        ';

        return $res;

    }

    public function widgetWaitingContent($pConf) {

        $pages = dbExFetch('SELECT u.username, p.*, v.modified
            FROM %pfx%system_user u, %pfx%system_page p, %pfx%system_page_version v
            WHERE draft_exist = 1
            AND v.page_id = p.id
            AND u.id = v.owner_id
            AND v.active = 1
            AND ( p.type = 0 OR p.type = 3)
            ', -1);

        $res['title'] = _l('Unpulished contents') . ' (' . count($pages) . ')';

        $html = '<table width="100%">';
        if (count($pages) > 0) {
            foreach ($pages as $page) {
                $html .= '<tr><td width="90">' . date("d. M H:i:s", $page['modified']) . '</td>';
                $html .= '<td>' . $page['username'] . '</td>';
                $html .= '<td>' . $page['title'] . '</td>';
                $html .=
                    '<td width="20"><a href="javascript:;" onclick="ka.wm.open(\'admin/pages\', {id: ' . $page['id'] .
                    '});"><img src="' . Kryn::$domain['path'] . 'admin/images/icons/bullet_go.png" /></a></td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        $res['content'] = $html;

        return $res;

    }

    public function manipulateUnpublishedContentsRow($pRow) {
        $domain = Kryn::getDomain($pRow[4]);
        $pRow[2] = '<a href="javascript:;" onclick="ka.wm.open(\'admin/pages\', {id: ' . $pRow[2] . '});">' .
                   Kryn::getPagePath($pRow[2] + 0) . '</a>';
        return $pRow;
    }

    public function manipulateLastChangesRow($pRow) {
        //$domain = Kryn::getDomain( $pRow[4] );
        $pRow[3] = '<a href="javascript:;" onclick="ka.wm.open(\'admin/pages\', {id: ' . $pRow[3] . '});">' .
                   Kryn::getPagePath($pRow[3] + 0) . '</a>';
        return $pRow;
    }

    public function cacheDeleteSystemUrls(){

        $domains = krynObjects::getList('domain');
        foreach ($domains as $domain)
            Kryn::deleteCache('systemUrls-'.$domain['id']);

    }

    public function cacheDeleteDomain(){

        $domains = krynObjects::getList('domain');
        foreach ($domains as $domain)
            Kryn::deleteCache('systemDomain-'.$domain['id']);
    }
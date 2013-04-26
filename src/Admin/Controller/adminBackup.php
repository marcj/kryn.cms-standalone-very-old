<?php

class adminBackup
{
    public static $infos = array();

    public static function init()
    {
        global $config_backups;

        if (file_exists('inc/config_backups.php'))
            include 'inc/config_backups.php';

        switch (getArgv(4)) {
            case 'list':
                return self::getItems();
            case 'remove':
                return self::removeItem(getArgv('id'));
            case 'save':
                if (getArgv('id'))
                    return self::saveItem(getArgv('id'));
                else
                    return self::addItem();
            case 'state':
                return self::state(getArgv('id'));
            case 'start':
                return self::doBackup(getArgv('id'));
            case 'generate':
                return self::createBackup();
            case 'download':
                return self::sendBackup(getArgv('id'), getArgv('file'));
            case 'extractInfos':
                return self::extractInfos(getArgv('file'));
            default:
                return array('error' => 'param_failed');
        }
    }

    public static function extractInfos($pFile)
    {
        $zip = str_replace('..', '', $pFile);

        if (!file_exists($zip)) return array('error' => 'file_does_not_exists');

        include_once 'File/Archive.php';
        File_Archive::setOption('zipCompressionLevel', 9);

        $source = File_Archive::read($zip . '/', null, -1);

        $infos = false;
        while ($source->next()) {
            if (substr($source->getFilename(), -10) == 'infos.json') {
                $folder = substr($source->getFilename(), 0, -11);
                $infos = $source->getData();
                break;
            }
        }

        $source->close();
        unset($source);
        if (!$infos) {
            return array('error' => 'no_infos_file');
        }
        $infos = json_decode($infos, true);

        $infos['countOfAllFiles'] = 0;
        $infos['sizeOfAllFiles'] = 0;

        $source = File_Archive::read($zip . '/' . $folder . '/files.zip/', '', 1);
        if ($source) {
            while ($source->next()) {
                $folder = $source->getFilename();
                $folder = substr($folder, 6);
                $pos = strpos($folder, '/');
                $stat = $source->getStat();
                if ($pos) {
                    $folder = substr($folder, 0, $pos);
                    $infos['files'][$folder][] = substr($source->getFilename(), 6);
                    $infos['countOfFiles'][$folder]++;
                    $infos['sizeOfFiles'][$folder] += intval($stat['size']);
                }
                $infos['countOfAllFiles']++;
                $infos['sizeOfAllFiles'] += intval($stat['size']);
            }
        }

        json($infos);
    }

    public static function sendBackup($pId, $pFile)
    {
        global $config_backups;

        $path = '';
        foreach ($config_backups[$pId]['done'] as $done) {
            if ($done['name'] == $pFile) {
                $path = $done['path'];
                break;
            }
        }

        if ($path && file_exists($path)) {

            $fsize = filesize($path);
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Content-Type: application/zip");
            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . $fsize);
            flush();
            readFile($path);
            exit;
        }

        return false;

    }

    public static function state($pBackupId)
    {
        global $config_backups;

        if (!$config_backups[$pBackupId]) return 'not_found';

        $path = self::getTempFolder() . 'kryn_backup_' . $pBackupId;
        $state = kryn::fileRead($path . '_step');

        return $state;
    }

    public static function removeItem($pId)
    {
        global $config_backups;

        $path = $config_backups[$pId]['_path'];
        delDir($path . '_zips');
        unlink($path . '_step');
        delDir($path . '/');

        unset($config_backups[$pId]);

        kryn::fileWrite('inc/config_backups.php',
            "<?php \n\$config_backups = " . var_export($config_backups, true) . "\n?>");

        return true;
    }

    public static function saveItem($pId)
    {
        global $config_backups;

        if (!$config_backups)
            $config_backups = array();

        if (is_array($config_backups[$pId]))
            $config_backups[$pId] = array_merge($config_backups[$pId], $_POST);
        else
            $config_backups[$pId] = $_POST;

        if (getArgv('andStart') && !function_exists('popen')) {
            $config_backups[$pId]['startThroughAdministration'] = true;
            $config_backups[$pId]['working'] = true;
            self::startBackup($pId, true);
        }

        kryn::fileWrite('inc/config_backups.php',
            "<?php \n\$config_backups = " . var_export($config_backups, true) . "\n?>");

        if (getArgv('andStart')) {
            if (function_exists('popen'))
                self::startBackup($pId);
            else
                return array('startThroughAdministration' => $pId);
        }

        return true;
    }

    public static function addItem()
    {
        global $config_backups;

        if (!$config_backups)
            $config_backups = array();

        $path = '';
        do {
            $id = dechex(time() / mt_rand(100, 500));
            $path = self::getTempFolder() . 'kryn_backup_' . $id;
        } while ($config_backups[$id] && file_exists($path));

        mkdir($path);

        if (!file_exists($path)) {
            klog('backup', _('Add backup failed. Can not create folder:') . ' ' . $path);

            return false;
        }

        $_POST['_path'] = $path;
        $config_backups[$id] = $_POST;

        if (getArgv('andStart') && !function_exists('popen')) {
            $config_backups[$id]['startThroughAdministration'] = true;
            $config_backups[$id]['working'] = true;
            self::startBackup($id, true);
        }

        @mkdir($path);

        kryn::fileWrite('inc/config_backups.php',
            "<?php \n\$config_backups = " . var_export($config_backups, true) . "\n?>");

        if (getArgv('andStart')) {
            if (function_exists('popen'))
                self::startBackup($id);
            else
                return array('startThroughAdministration' => $id);
        }

        return true;
    }

    public static function getItems()
    {
        global $config_backups;

        if (!$config_backups)
            $config_backups = array();

        if (!function_exists('popen')) {
            $config_backups['__noPopenAvailable'] = true;
        }

        foreach ($config_backups as $key => &$backup) {
            $path = self::getTempFolder() . 'kryn_backup_' . $key;
            if (file_exists($path)) {
                $state = kryn::fileRead($path . '_step');
                if ($state && $state != '' && $state != 'done')
                    $backup['working'] = true;
            }
        }

        return $config_backups;
    }

    public static function getTempFolder()
    {
        $path = realpath(self::_getTempFolder());
        if (substr($path, -1) != '/')
            return $path . '/';

        return $path;
    }

    public static function _getTempFolder()
    {
        global $cfg;

        if ($cfg['backup_generation_path'])
            return $cfg['backup_generation_path'];

        return kryn::getTempFolder();
    }

    public static function startBackup($pBackupCode, $pNoAsync = false)
    {
        global $config_backups, $cfg;

        $definitions = $config_backups[$pBackupCode];
        if ($definitions) {
            $path = self::getTempFolder() . 'kryn_backup_' . $pBackupCode;
        } else {
            return false;
        }

        @mkdir($path);
        kryn::fileWrite($path . '_step', 'preparing');
        chmod($path, 0777);
        chmod($path . '_step', 0666);

        if ($pNoAsync) return true;

        if (function_exists('popen')) {
            $cmd = 'cronjob.php ' . $cfg['cronjob_key'] . ' backup ' . $pBackupCode;

            //TODO need windows equivalent
            pclose(popen('php ' . $cmd . ' &> /dev/null &', "r"));

            sleep(1);

            $state = kryn::fileRead($path . '_step');
            if ($state == 'preparing') {
                klog('backup', _l('Can not start the backup process through popen() caused by a undefined error.'));
                kryn::fileWrite($path . '_step', 'error');

                return false;
            }

            return true;

        } else {
            kryn::fileWrite($path . '_step', 'error');
            klog('backup', _l('Can not start the backup process through popen() caused by php functions restriction.'));

            return false;
        }

    }

    public static function doBackup($pBackupCode)
    {
        global $config_backups;

        if (file_exists('inc/config_backups.php'))
            require_once 'inc/config_backups.php';

        $definitions = $config_backups[$pBackupCode];

        $path = $definitions['_path'];

        //print_r($definitions);
        //print 'start: ' . $pBackupCode . ': ' . $path . "\n";
        $start = microtime(true);

        mkdirr($path);

        if (!is_dir($path)) {
            klog('core', $path.' is not writeable. Can not save backup archive.');

            return;
        }
        kryn::fileWrite($path . '_step', 'start');

        klog('backup', 'Start backup ' . $pBackupCode . ' (' . $path . ')');

        //kryn::fileWrite( $path.'/buildOn.json', json_encode(kryn::getDebugInformation()) );

        @delDir($path . '/domains');
        @delDir($path . '/nodes');

        @mkdir($path . '/domains');
        @mkdir($path . '/nodes');

        include_once 'File/Archive.php';
        File_Archive::setOption('zipCompressionLevel', 9);

        adminBackup::$infos = array();

        //pages/sites
        if ($definitions['pages'] == 'all') {

            $domains = dbTableFetch('system_domains', -1);
            foreach ($domains as $domain) {
                kryn::fileWrite($path . '_step', 'domain:' . $domain['domain']);
                self::exportWebsite($path, $domain['id']);
            }

        } elseif ($definitions['pages'] == 'choose') {

            foreach ($definitions['pages_domains'] as $domainRsn) {

                $domain = dbTableFetch('system_domains', 'id = ' . $domainRsn, 1);
                kryn::fileWrite($path . '_step', 'domain:' . $domain['domain']);

                self::exportWebsite($path, $domainRsn);
            }

            foreach ($definitions['pages_nodes'] as $node) {
                $node = dbTableFetch('system_page', ' id = ' . $node['id'], 1);
                if ($node) {
                    kryn::fileWrite($path . '_step', 'node:' . $node['title']);
                    self::exportNode($path, $node['id']);
                }
            }

        }

        //files
        $fileReads = array();
        if ($definitions['files'] == 'all') {

            $blacklist = array(
                PATH_WEB.'admin/', PATH_WEB.'js/', PATH_WEB.'css/', PATH_WEB.'kryn/',
                PATH_WEB.'users/'
            );
            foreach (kryn::$extensions as $extension)
                $blacklist[] = PATH_WEB . $extension . '/';

            kryn::fileWrite($path . '_step', 'gatherFiles');

            $files = find('media/*', false);
            foreach ($files as $file) {
                if (!in_array($file . '/', $blacklist)) {
                    if (is_dir($file)) {
                        $subfiles = find($file . '/*');
                        foreach ($subfiles as $subfile) {
                            $fileReads[] =
                                File_Archive::read($subfile, str_replace(PATH_WEB, 'files/', $subfile));
                        }
                    } else {
                        $fileReads[] = File_Archive::read($file, str_replace(PATH_WEB, 'files/', $file));
                    }
                }
            }

        } elseif ($definitions['files'] == 'choose') {

            kryn::fileWrite($path . '_step', 'gatherFiles');

            foreach ($definitions['files_choose'] as $item) {

                $myPath = $item['folder'];

                if ($myPath == '/')
                    $myPath = '';
                else if (is_dir(PATH_WEB . $myPath) && substr($myPath, -1) != '/')
                    $myPath .= '/';

                if (!is_dir(PATH_WEB . $myPath)) {
                    $fileReads[] = File_Archive::read(PATH_WEB . $myPath, 'files/' . PATH_WEB . $myPath);
                } else {
                    $files = find(PATH_WEB . $myPath . '*');
                    foreach ($files as $file) {
                        $fileReads[] = File_Archive::read($file, str_replace(PATH_WEB, 'files/', $file));
                    }
                }
            }
        }

        if (count($fileReads) > 0) {

            $filesSource = File_Archive::readMulti($fileReads);
            $filesZip = $path . '/files.zip';

            kryn::fileWrite($path . '_step', 'zippingFiles');
            File_Archive::extract(
                $filesSource,
                $filesZip
            );

            if ($definitions['files_versions']) {

            }
        }

        //extensions
        require_once(PATH_MODULE . 'admin/adminModule.class.php');
        if ($definitions['extensions'] == 'all') {
            @mkdir($path . '/extensions/');

            $blacklist = array('admin', 'kryn', 'users');
            foreach (kryn::$extensions as $extension) {
                if (in_array($extension, $blacklist)) continue;

                $file = adminModule::createArchive($extension);
                rename($file, $path . '/extensions/' . basename($file));

            }
        } elseif ($definitions['extensions'] == 'choose') {
            @mkdir($path . '/extensions/');

            foreach ($definitions['extensions_choose'] as $extension) {
                $file = adminModule::createArchive($extension);
                rename($file, $path . '/extensions/' . basename($file));
            }
        }

        //extensions tables
        if ($definitions['extensions_data'] == 'all') {
            @mkdir($path . '/extensions_data/');
            $blacklist = array('admin', 'kryn', 'users');

            foreach (kryn::$extensions as $extension) {
                if (in_array($extension, $blacklist)) continue;

                if (class_exists($extension) && method_exists($extension, 'exportBackupData')) {

                    $file = $path . '/extensions_data/' . $extension . '.data';
                    kryn::fileWrite($file, call_user_func(array($extension, 'exportBackupData')));

                } else {
                    @mkdir($path . '/extensions_data/' . $extension . '/');
                    $config = kryn::getModuleConfig($extension);

                    if ($config['db']) {
                        foreach ($config['db'] as $table => $columns) {
                            $file = $path . '/extensions_data/' . $extension . '/' . $table . '.json';
                            $contents = dbTableFetch($table);
                            if ($contents) {
                                kryn::fileWrite($file, json_encode($contents));
                            }
                        }
                    }
                }

            }

        } elseif ($definitions['extensions_data'] == 'choose') {
            @mkdir($path . '/extensions_data/');

        }

        //system
        /* part of kryn.cms 1.1
        if ($definitions['system'] == '1') {

            $files = array('index.php', '.htaccess', 'cronjob.php', 'LICENSE', 'README');
            $dirs = array(
                'inc/kryn/', 'inc/lib/codemirror/', 'inc/lib/mooeditable/', 'inc/lib/pear/', 'inc/lib/smarty/',
                'inc/template/admin/', 'inc/template/kryn/', 'inc/template/users', 'inc/template/css/', 'inc/template/js',
                PATH_MODULE.'admin/', PATH_MODULE.'users/'
            );

            //copy files
            foreach ($files as $file) {
                copy( $file, $path.'/'.$file );
            }

            //copy folders
            foreach ($dirs as $dir) {
                copyr( $dir, $path.'/'.$dir );
            }

            @mkdir( $path.'/fileversions' );
            copy( 'fileversions/.htaccess', $path.'/fileversions/.htaccess' );

            //find and copy installer.
            $rootFiles = find('*', false);
            foreach ($rootFiles as $file) {
                $content = kryn::fileRead( $file );
                if ( strpos( $content, '<title>Kryn.cms installation</title>') !== false ) {
                    copy( $file, $path.'/install.php' );
                }
            }

            //export tables
            $exts = array('admin', 'users');
            $tables = array(
                'system_user', 'system_modules', 'system_langs', 'system_groups', 'system_groupaccess',
                'system_acl'
            );

            foreach ($tables as $table) {
                $file = $path.'/system_data/'.$table.'.json';
                $contents = dbTableFetch( $table );
                if ($contents) {
                    kryn::fileWrite( $file, json_encode($contents) );
                }
            }
        }
        */

        adminBackup::$infos['time'] = time();

        kryn::fileWrite($path . '_step', 'gatherDone');
        kryn::fileWrite($path . '/infos.json', json_encode(adminBackup::$infos));

        $subfolder = 'Kryn_Backup_' . $pBackupCode . '_' . date('Ymd_h-i-s');
        $zipFile = $subfolder . '.zip';

        /*$files = find($path.'/*');

        foreach ($files as $file) {
            $reads[] = File_Archive::read($file, str_replace($path, $subfolder, $file));
        }

        $source = File_Archive::readMulti($reads);
        */

        @mkdir($path . '_zips');
        $zipPath = $path . '_zips/' . $zipFile;

        if (!@touch($zipPath)) {
            klog('core', $path.'_zips is not writeable. Can not save backup archive.');

            return;
        }

        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            $zip->open($zipPath, ZIPARCHIVE::CREATE);
            $files = find($path.'/*');
            foreach ($files as $file)
                if (!is_dir($file)) {
                    $zip->addFile($file, str_replace($path, '', $file));
                }
            $zip->close();
        } else {
            //note: if memory_limit is low, than we only can generate small zip files
            File_Archive::extract(
                File_Archive::read($path, $subfolder . '/'),
                File_Archive::toArchive(
                    $zipPath,
                    File_Archive::toFiles()
                )
            );
        }

        $zipSize = filesize($zipPath);

        $timeDiff = microtime(true) - $start;
        $config_backups[$pBackupCode]['done'][] = array(
            'took_time' => $timeDiff,
            'name' => $zipFile,
            'path' => $zipPath,
            'size' => $zipSize
        );
        $config_backups[$pBackupCode]['working'] = false;
        $config_backups[$pBackupCode]['generated']++;

        kryn::fileWrite('inc/config_backups.php',
            "<?php \n\$config_backups = " . var_export($config_backups, true) . "\n?>");

        //delete all files
        //delDir($path);

        kryn::fileWrite($path . '_step', 'done');
        klog('backup', 'Backup generated. '.$pBackupCode.' => '.$zipPath);

        return true;
    }

    public static function getNextFileId($pPath, $pExt = '.json')
    {
        $found = false;
        $curId = 0;
        do {

            $curId++;
            if (!file_exists($pPath . '/' . $curId . $pExt)) {
                $found = true;
            }

        } while (!$found);

        return $curId;
    }

    public static function exportWebsite($pPath, $pDomainRsn)
    {
        $pDomainRsn += 0;
        $domain = dbTableFetch('system_domains', 'id = ' . $pDomainRsn, 1);

        unset($domain['id']);

        $pageCounter = 0;

        $export = array(
            'domain' => $domain,
            'nodes' => self::exportTree(0, $pDomainRsn, null, $pageCounter)
        );

        $id = self::getNextFileId($pPath . '/domains');

        adminBackup::$infos['domains'][] = array(
            'domain' => $domain['domain'],
            'lang' => $domain['lang'],
            'page_count' => $pageCounter
        );

        kryn::fileWrite($pPath . '/domains/' . $id . '.json', json_encode($export));

    }

    public static function exportNode($pPath, $pNodeRsn)
    {
        $pNodeRsn = $pNodeRsn + 0;
        $node = dbTableFetch('system_page', ' id = ' . $pNodeRsn, 1);

        $pageCounter = 0;

        $node['childs'] = self::exportTree($pNodeRsn, null, null, $pageCounter);

        adminBackup::$infos['nodes'][] = array(
            'title' => $node['title'],
            'page_count' => $pageCounter
        );
        $id = self::getNextFileId($pPath . '/nodes');

        kryn::fileWrite($pPath . '/nodes/' . $id . '.json', json_encode($node));

    }

    public static function exportTree($pNodeRsn, $pDomainRsn = false, $pAndAllVersions = false, &$pPageCounter)
    {
        $pNodeRsn += 0;
        $pagesRes = dbExec("SELECT * FROM %pfx%system_page WHERE pid = $pNodeRsn " .
                           ($pDomainRsn ? ' AND domain_id = ' . $pDomainRsn : ''));

        $childs = array();
        while ($row = dbFetch($pagesRes)) {

            $pPageCounter++;

            $contentRes = dbExec("SELECT c.* FROM %pfx%system_contents c, %pfx%system_page_version v
                    WHERE
                    c.page_id = " . $row['id'] . "
                    AND v.active = 1
                    AND c.version_id = v.id");

            while ($contentRow = dbFetch($contentRes)) {

                unset($contentRow['id']);
                unset($contentRow['page_id']);
                $row['contents'][] = $contentRow;

            }

            //TODO $pAndAllVersions

            $row['childs'] = self::exportTree($row['id'], $pDomainRsn, $pAndAllVersions, $pPageCounter);

            unset($row['id']);
            unset($row['domain_id']);
            unset($row['pid']);

            $childs[] = $row;
        }

        return $childs;
    }

}

<?php


/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */


class adminFilemanager {

    public static $fs;
    public static $fsObjects = array();

    public function getFs($pPath){

        $class = 'adminFs';
        $file = false;

        if ($pPath != '/') {

            $sPos = strpos(substr($pPath, 1), '/');
            if( $sPos === false )
                $firstFolder = substr($pPath,1);
            else
                $firstFolder = substr($pPath, 1, $sPos);

            //if firstFolder a magic folder?
            if( $fs = kryn::$config['magic_folder'][$firstFolder] ){
                $class = $fs['class'];
                $file = $fs['file'];
                $params = $fs['params'];
                $prefix = '/'.$firstFolder;
            }
        }

        //error_log("getFs($pPath) returns $class (".rand().")");

        if(self::$fsObjects[$class]) return self::$fsObjects[$class];

        if ($file)
            require_once($file);

        self::$fsObjects[$class] = new $class($params);
        self::$fsObjects[$class]->magicFolderName = $prefix;

        return self::$fsObjects[$class];
    }

    public static function init() {


        $path = str_replace('..', '', getArgv('path'));
        $path = str_replace('//', '/', $path);
        if ($path != '/' && substr($path,-1) == '/') $path = substr($path,0,-1);

        if (!krynAcl::checkAccess(3, $path, 'read', true))
            return array('error'=>'access_denied');

        self::$fs = self::getFs($path);

        switch (getArgv(3)) {

            case 'getFiles':
                return self::getFiles($path);
            case 'getImages':
                return self::getImages($path);
            case 'getContent':
                return self::getContent($path);

            case 'getFile':
                return self::getFile($path);
            case 'getSize':
                return self::getSize($path);

            case 'redirect':
                return self::redirectToPublicUrl($path);


            case 'getVersions':
                return self::getVersions($path);
            case 'addVersion':
                return self::addVersion($path);
            case 'recoverVersion':
                return self::recoverVersion(getArgv('rsn'));

            case 'setAccess':
                return self::setAccess($path, getArgv('access'));
            case 'setInternalAcl':
                return self::setInternalAcl($path, getArgv('rules'));


            case 'createFile':
                return self::createFile($path);
            case 'createFolder':
                return self::createFolder($path);


            case 'setContent':
                return self::setContent($path, getArgv('content'));
            case 'moveFile':
                return self::moveFile($path, getArgv('newPath'), getArgv('overwrite')==1?true:false);
            case 'duplicateFile':
                return self::duplicateFile($path, getArgv('newName'), getArgv('overwrite')==1?true:false);


            case 'deleteFile':
                return self::deleteFile($path);
            case 'recover':
                return self::recover(getArgv('rsn'));


            case 'prepareUpload':
                return self::prepareUpload($path);
            case 'upload':
                return self::uploadFile($path);

            //todo, next 3 methods
            case 'rotate':
                return self::rotateFile(getArgv('file'), getArgv('position'));
            case 'resize':
                return self::resize(getArgv('file'), getArgv('width') + 0, getArgv('height') + 0);
            case 'diffFiles':
                return self::diffFiles(getArgv('from'), getArgv('to'));

            case 'paste':
                return self::paste($path);
            case 'search':
                return self::search($path, getArgv('q'));
        }
    }

    public static function redirectToPublicUrl($pPath){
        $path = self::$fs->getPublicUrl(self::normalizePath($pPath));
        kryn::redirect($path);
    }

    public static function diffFiles($pFrom, $pTo) {
        require_once(PATH_MODULE . 'admin/FineDiff.class.php');

        $textFrom = self::readFile($pFrom);
        $textTo = self::readFile($pTo);

        $textFrom = str_replace("\r\n", "\n", $textFrom);
        $textTo = str_replace("\r\n", "\n", $textTo);

        $diff = FineDiff::getDiffOpcodes($textFrom, $textTo);

        //$htmlOutput = $diff->renderDiffToHTML();

        // Fix newlines and spaces
        //$htmlOutput = nl2br($htmlOutput);
        //$htmlOutput = str_replace(" ", "&nbsp;", $htmlOutput);

        return $diff;
    }

    public static function getContent($pPath) {
        json(self::$fs->getContent(self::normalizePath($pPath)));
    }

    public static function getFile($pPath) {

        $file = self::$fs->getFile(self::normalizePath($pPath));
        if ($file)
            $file['writeaccess'] = krynAcl::checkAccess(3, $pPath, 'write', true);

        json($file);
    }

    public static function getSize($pPath) {
        json(self::$fs->getSize(self::normalizePath($pPath)));
    }



    public static function setInternalAcl($pFilePath, $pRules) {
        global $cfg;

        $pFilePath = esc('/' . $pFilePath);
        if ($pFilePath == '//')
            $pFilePath = '/';

        $wc = '\%';

        if ($cfg['db_type'] == 'postgresql')
            $wc = '\\%';

        dbDelete('system_acl', "type = 3 AND code LIKE '$pFilePath" . "[%'");
        dbDelete('system_acl', "type = 3 AND code LIKE '$pFilePath" . $wc . "[%'");
        // /\%

        $row = dbExfetch('SELECT MAX(prio) as maxium FROM %pfx%system_acl');
        $prio = $row['maxium'];
        if (is_array($pRules)) {
            foreach ($pRules as $rule) {
                $prio++;
                $rule['prio'] = $prio;
                $rule['type'] = 3;
                $rule['code'] = str_replace('//', '/', $rule['code']);
                dbInsert('system_acl', $rule);

            }
        }

    }

    public static function setFilesystem($pPath, $pChmod, $pOwner = false, $pGroup = false, $pWithSub = false) {


        $pPath = str_replace("..", '', $pPath);
        chmod($pPath, octdec('0' . $pChmod));
        chown($pPath, $pOwner);
        chgrp($pPath, $pGroup);

        if ($pWithSub) {

            if (is_dir($pPath)) {
                $h = opendir($pPath);
                if ($h) {
                    while (($file = readdir($h)) !== false) {
                        if ($file == '.' || $file == '..' || $file == '.svn') continue;
                        self::setFilesystem($pPath . '/' . $file, $pChmod, $pOwner, $pGroup, $pWithSub);
                    }
                }
            }
        }
        return true;
    }

    public static function setAccess($pPath, $pAccess) {

        if (is_dir($pPath))
            $dir = substr($pPath, 0, -1);
        else
            $dir = dirname($pPath);

        if ($pAccess != 'allow' && $pAccess != 'deny' && $pAccess != '')
            return false;

        if (!file_exists($dir)) {
            return false;
        }

        $htaccess = $dir . '/.htaccess';
        if (!file_exists($htaccess) && !touch($htaccess)) {
            klog('files', _('Can not set the file access, because the system can not create the .htaccess file'));
            return false;
        }

        $content = kryn::fileRead($htaccess);

        if (!is_dir($pPath)) {
            $filename = '"' . basename($pPath) . '"';
            $filenameesc = $filename;
        } else {
            $filename = "*";
            $filenameesc = '\*';
        }

        $content = preg_replace('/<Files ' . $filenameesc . '>\W*(\w*) from all[^<]*<\/Files>/i', '', $content);

        if ($pAccess != '') {

            $content .= "
<Files $filename>
$pAccess from all
</Files>";
        }

        kryn::fileWrite($htaccess, $content);

        return true;

    }

    public static function recoverVersion($pRsn) {

        $pRsn = $pRsn + 0;
        $version = dbTableFetch("system_files_versions", "rsn = " . $pRsn, 1);

        if (!file_exists($version['versionpath'])) {
            klog('files', str_replace('%s', $version['versionpath'], _l('Can not recover the version for file %s')));
            return false;
        }

        self::addVersion($version['path']);

        copy($version['versionpath'], $version['path']);

        return true;

    }

    public static function getVersions($pPath) {
        $pPath = str_replace("..", ".", esc($pPath));
        $pPath = str_replace("//", "/", $pPath);

        $versions = dbExfetch("
        	SELECT v.*, u.username
        	FROM %pfx%system_files_versions v, %pfx%system_user u
        	WHERE
        		u.rsn = v.user_rsn AND 
        		path = '" . $pPath . "'
        	ORDER BY v.rsn DESC
        ", -1);

        foreach ($versions as &$version) {
            $version['size'] = filesize($version['versionpath']);
        }

        return $versions;
    }

    /**
     * Adds a new version in the files_versions table for given path
     */
    public static function addVersion($pPath) {
        global $user;

        $pPath = str_replace("..", ".", $pPath);
        $pPath = str_replace("//", "/", $pPath);

        if (!file_exists($pPath)) return false;

        if (!file_exists('data/fileversions/')) {
            if (!mkdir('data/fileversions/')) {
                klog('files', _l('Can not create the file versions folder data/fileversions/, so the system can not create file versions.'));
                return;
            }
        }

        $versionpath = kryn::toModRewrite($pPath);

        $rand = md5(filemtime($pPath) . mt_rand(1, 100) . mt_rand(1, 12200) . time());

        $versionpath = 'data/fileversions/' . $rand . '.' . $versionpath . '.ver';

        copy($pPath, $versionpath);

        $insert = array(
            'user_rsn' => $user->user_rsn,
            'path' => $pPath,
            'created' => time(),
            'mtime' => filemtime($pPath),
            'versionpath' => $versionpath
        );

        dbInsert('system_files_versions', $insert);

        return true;
    }


    public static function resize($pFile, $pWidth, $pHeight) {
        $pFile = 'inc/template/' . str_replace('..', '', $pFile);

        list($oriWidth, $oriHeight, $type) = getimagesize($pFile);
        switch ($type) {
            case 1:
                $imagecreate = 'imagecreatefromgif';
                $imagesave = 'imagegif';
                break;
            case 2:
                $imagecreate = 'imagecreatefromjpeg';
                $imagesave = 'imagejpeg';
                break;
            case 3:
                $imagecreate = 'imagecreatefrompng';
                $imagesave = 'imagepng';
                break;
        }


        $imageNew = imagecreatetruecolor($pWidth, $pHeight);
        $image = $imagecreate($pFile);

        imagecopyresampled($imageNew, $image, 0, 0, 0, 0, $pWidth, $pHeight, $oriWidth, $oriHeight);

        self::addVersion($pFile);
        $imagesave($imageNew, $pFile);

        return filemtime($pFile);
    }


    public static function rotateImage($image) {
        $width = imagesx($image);
        $height = imagesy($image);
        $newImage = imagecreatetruecolor($height, $width);
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        for ($w = 0; $w < $width; $w++)
            for ($h = 0; $h < $height; $h++) {
                $ref = imagecolorat($image, $w, $h);
                imagesetpixel($newImage, $h, ($width - 1) - $w, $ref);
            }
        return $newImage;
    }


    public static function rotateFile($pFile, $pPosition) {
        global $user;

        $pFile = 'inc/template/' . str_replace('..', '', $pFile);

        list($oriWidth, $oriHeight, $type) = getimagesize($pFile);
        switch ($type) {
            case 1:
                $imagecreate = 'imagecreatefromgif';
                $imagesave = 'imagegif';
                break;
            case 2:
                $imagecreate = 'imagecreatefromjpeg';
                $imagesave = 'imagejpeg';
                break;
            case 3:
                $imagecreate = 'imagecreatefrompng';
                $imagesave = 'imagepng';
                break;
        }

        $source = $imagecreate($pFile);

        $degrees = 90;
        if ($pPosition == 'left')
            $degrees *= -1;

        if (function_exists("imagerotate")) {
            $rotate = imagerotate($source, $degrees, 0);
        } else {
            if ($pPosition == 'left') {
                $rotate = self::rotateImage($source);
            } else {
                $rotate = self::rotateImage($source);
                $rotate = self::rotateImage($rotate);
                $rotate = self::rotateImage($rotate);
            }
        }


        self::addVersion($pFile);

        $imagesave($rotate, $pFile);

        return filemtime($pFile);
    }


    public static function getImages($pPath) {

        //todo, dont use it
        //return kryn::$fs->search(self::normalizePath($pPath), '*.jpg');

    }

    public static function imageThumb($pPath) {

        self::$fs = self::getFs($pPath);

        if(!krynAcl::checkAccess(3, $pPath, 'read', true)) return array('error'=>'access_denied');
        $file = self::$fs->getFile(self::normalizePath($pPath));
        header("Content-Type: image/png");

        $path = kryn::$config['media_cache'].'thumbnail-'.kryn::toModRewrite($pPath).'-'.$file['mtime'].'.png';
        if (file_exists($path))
            die(readFile($path));

        $fileContent = self::$fs->getContent(self::normalizePath($pPath));
        kryn::fileWrite($path, $fileContent);
        resizeImage($path, $path, '120x70', true);

        die(readFile($path));
    }

    public static function prepareUpload($pPath) {
        global $adminClient;

        $oriName = getArgv('name');
        $name = self::normalizeName(getArgv('name'));
        $newPath = ($pPath == '/')?'/'.$name:$pPath.'/'.$name;

        $res = array();

        if(!krynAcl::checkAccess(3, $newPath, 'write', true) ) return array('error'=>'access_denied');

        if ($name != $oriName) {
            $res['renamed'] = true;
            $res['name'] = $name;
        }

        $exist = self::$fs->fileExists(self::normalizePath($newPath));
        if ($exist && getArgv('overwrite') != 1) {
            $res['exist'] = true;
        } else {
            self::$fs->createFile(self::normalizePath($newPath), 'kryn_fileupload_blocked' . "\n" . $adminClient->token);
        }

        return $res;
    }


    public static function uploadFile($pPath) {
        global $adminClient;

        $name = $_FILES['file']['name'];
        if (getArgv('name')) {
            $name = getArgv('name');
        }

        if ($_FILES["file"]['error']) {

            switch ($_FILES['file']['error']) {
                case 1:
                    $error = t('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
                    break;
                case 2:
                    $error =
                        t('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
                    break;
                case 3:
                    $error = t('The uploaded file was only partially uploaded.');
                    break;
                case 7:
                    $error = t('Failed to write file to disk.');
                    break;
                case 6:
                    $error = t('Missing a temporary folder.');
                    break;
                case 4:
                    $error = t('No file was uploaded.');
                    break;
                case 8:
                    $error =
                        t('A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.');
                    break;
            }

            klog('file', sprintf(t('Failed to upload the file %s to %s. Error: %s'), $name, $pPath, $error));
            return;
        }

        $name = self::normalizeName($name);

        $newPath = ($pPath == '/')?'/'.$name:$pPath.'/'.$name;

        if (self::$fs->fileExists(self::normalizePath($newPath))) {

            if (getArgv('overwrite') != '1') {

                $content = self::$fs->getContent(self::normalizePath($newPath));

                if ($content != 'kryn_fileupload_blocked' . "\n" . $adminClient->token) {
                    //not our file, so cancel
                    return false;
                }
            }
        }

        if (!krynAcl::checkAccess(3, $newPath, 'write', true)) return array('error' => 'access_denied');

        $content = kryn::fileRead($_FILES['file']['tmp_name']);
        error_log($_FILES['file']['tmp_name'].': '.$content);
        self::$fs->setContent(self::normalizePath($newPath), $content);
        @unlink($_FILES["file"]["tmp_name"]);

        return $newPath;
    }

    public static function createFile($pPath) {

        $access = krynAcl::checkAccess(3, $pPath, 'write', true);

        if (!$access) return array('error'=>'access_denied');
        if(self::$fs->fileExists(self::normalizePath($pPath)))
            return false;

        return self::$fs->createFile(self::normalizePath($pPath));
    }

    public static function createFolder($pPath) {

        $access = krynAcl::checkAccess(3, $pPath, 'write', true);

        if (!$access) return array('error'=>'access_denied');
        if(self::$fs->fileExists(self::normalizePath($pPath)))
            return false;

        return self::$fs->createFolder(self::normalizePath($pPath));
    }

    /**
     * Removes the magicFolderName in the path, remove .. and // => /
     *
     * @param $pPath
     */
    public function normalizePath($pPath){

        $fs = self::getFs($pPath);
        $pPath = substr($pPath, strlen($fs->magicFolderName));

        $pPath = str_replace('..', '', $pPath);
        $pPath = str_replace('//', '/', $pPath);

        if (substr($pPath, 0, 1) != '/')
            $pPath = '/'.$pPath;

        return $pPath;
    }

    public function normalizeName($pName){

        $name = @str_replace('ä', "ae", strtolower($pName));
        $name = str_replace('..', '', $name);
        $name = @str_replace('ö', "oe", $name);
        $name = @str_replace('ü', "ue", $name);
        $name = @str_replace('ß', "ss", $name);
        $name = @preg_replace('/[^a-zA-Z0-9\.\_\(\)]/', "-", $name);
        $name = @preg_replace('/--+/', '-', $name);

        return $name;
    }

    public static function moveFile($pPath, $pNewPath, $pOverwrite = false) {

        if (!krynAcl::checkAccess(3, $pPath, 'write', true)) return array('array'=>'access_denied');
        if (!krynAcl::checkAccess(3, $pNewPath, 'write', true)) return array('array'=>'access_denied');

        $otherFs = self::getFs($pNewPath);

        if(!$pOverwrite && $otherFs->fileExists(self::normalizePath($pNewPath)))
            return array('file_exists'=>true);

        if($otherFs == self::$fs){
            self::$fs->move(self::normalizePath($pPath), self::normalizePath($pNewPath));
        } else {
            $content = self::$fs->getContent(self::normalizePath($pPath));
            if ($otherFs->newFile(self::normalizePath($pNewPath), $content)) {
                self::$fs->remove(self::normalizePath($pPath));
            }
        }

        //todo, self::renameVersion($pPath, $pNewPath);
        //todo, self::renameAcls($pPath, $pNewPath);

        return true;
    }

    public static function duplicateFile($pPath, $pNewName, $pOverwrite = false) {

        $pNewPath = dirname($pPath).'/'.self::normalizeName($pNewName);

        if (!krynAcl::checkAccess(3, $pNewPath, 'write', true)) return array('array'=>'access_denied');

        if(!$pOverwrite && self::$fs->fileExists(self::normalizePath($pNewPath)))
            return array('file_exists'=>true);

        return self::$fs->copy(self::normalizePath($pPath), self::normalizePath($pNewPath));
    }


    public static function recover($pRsn) {
        //todo
        $item = dbTableFetch('system_files_log', 1, "rsn = " . ($pRsn + 0));
        if ($item['rsn'] > 0) {

            $nPath = str_replace('inc/template', '', $item['path']);
            $toDir = dirname($nPath);

            $access = krynAcl::checkAccess(3, '/' . $toDir, 'write', true);
            if (!$access) json('no-access');

            $access = krynAcl::checkAccess(3, '/' . $nPath, 'write', true);
            if (!$access) json('no-access');

            if (file_exists($item['path'])) {
                self::addVersion($item['path']);
            }

            rename("inc/template/trash/" . $item['rsn'], $item['path']);

            dbDelete('system_files_log', "rsn = " . $item['rsn']);
        }
        return true;

    }

    public static function deleteFile($pPath) {

        if (!krynAcl::checkAccess(3, $pPath, 'write', true)) return array('error'=>'access_denied');
        $path = 'inc/template'.$pPath;

        if (substr($pPath,0,7) == '/trash/') {

            $trashItem = dbTableFetch('system_files_log', 1, "rsn = ".basename($path)+0);
            dbDelete('system_files_log', "rsn = " . $trashItem['rsn']);

            if (is_dir($path)) {
                delDir($path);
            } else {
                unlink($path);
            }

        } else {
            self::$fs->deleteFile(self::normalizePath($pPath));
        }

        return true;
    }

    public static function paste($pToPath) {

        if (!krynAcl::checkAccess(3, $pToPath, 'write', true)) return array('array'=>'access_denied');

        $files = getArgv('files');
        $move = (getArgv('move') == 1) ? true : false;

        if (is_array($files)) {

            $exist = false;
            foreach ($files as $file) {

                $newPath = $pToPath.'/'.basename($file);
                $fs = self::getFs($newPath);

                if ($fs->fileExists(self::normalizePath($newPath))) {
                    $exist = true;
                    break;
                }
            }

            if (getArgv('overwrite') != "true" && $exist) {
                return json(array('exist' => true));
            }


            foreach ($files as $file) {

                $file = str_replace('..', '', $file);
                $file = str_replace(chr(0), '', $file);

                if (!krynAcl::checkAccess(3, $file, 'read', true)) continue;

                if ($move)
                    if (!krynAcl::checkAccess(3, $file, 'write', true)) continue;

                $oldFs = self::getFs($file);
                $newPath = $pToPath.'/'.basename($file);
                if (!krynAcl::checkAccess(3, $newPath, 'write', true)) continue;

                $newFs = self::getFs($newPath);

                error_log('file: '.$file. ' newPath: '.$newPath);
                error_log('old: '.$oldFs->magicFolderName. ' new: '.$newFs->magicFolderName);

                if ($newFs === $oldFs) {
                    error_log('same');
                    if ($move)
                        $newFs->move(self::normalizePath($file), self::normalizePath($newPath));
                    else
                        $newFs->copy(self::normalizePath($file), self::normalizePath($newPath));
                } else {
                    $content = $oldFs->getContent(self::normalizePath($file));
                    $newFs->setContent(self::normalizePath($newPath), $content);
                    if ($move)
                        $oldFs->deteleFile(self::normalizePath($file));
                }

            }
        }

        return true;
    }

    public static function search($pPath, $pQuery) {

        return self::$fs->search($pQuery);

    }

    public static function getPublicAccess($pPath){
        return self::$fs->getPublicAccess($pPath);
    }

    public static function getInternalAccess($pPath){
        return dbTableFetch('system_acl', "type = 3 AND (code LIKE '$pPath\\\%%' OR code LIKE '$pPath\[%')", -1);
    }

    public static function getTrashFiles(){


        $res['type'] = 'dir';
        $res['path'] = '/trash';
        $res['name'] = 'Trash';
        $res['ctime'] = filectime('inc/template/trash');
        $res['mtime'] = filemtime('inc/tempalte/trash');


        $files = array();
        $h = opendir('inc/template/trash/');

        while ($file = readdir($h)) {
            if ($file == '.svn' || $file == '.' || $file == '..') continue;
            $files[] = $file;
        }

        natcasesort($files);

        foreach ($files as $file) {
            if ($file == '.htaccess') continue;
            $path = '/trash/' . $file;

            $dbItem = dbTableFetch('system_files_log', 1, 'rsn = ' . ($file+0));

            $item['name'] = basename($dbItem['path']).'-v'.$file;
            $item['path'] = str_replace('inc/template', '', $path);
            $item['original_rsn'] = $dbItem['rsn'];
            $item['original_path'] = $dbItem['path'];
            $item['lastModified'] = $dbItem['modified'];
            $item['mtime'] = $dbItem['modified'];
            $item['type'] = ($dbItem['type'] == 1) ? 'dir' : 'file';

            $res['items'][] = $item;

        }

        return $res;
    }

    public static function getFiles($pPath) {

        $access = krynAcl::checkAccess(3, $pPath, 'read', true);
        if (!$access) return false;

        if ($pPath == '/trash'){
            return self::getTrashFiles();
        }

        $items = self::$fs->getFiles(self::normalizePath($pPath));
        if (!is_array($items)) return $items;

        if (self::$fs->magicFolderName)
            foreach ($items as &$file)
                $file['path'] = self::$fs->magicFolderName.$file['path'];

        if($pPath == '/'){
            if (is_array(kryn::$config['magic_folder'])) {
                foreach (kryn::$config['magic_folder'] as $folder => &$config ){
                    $magic = array(
                        'path'  => '/'.$folder,
                        'magic' => true,
                        'name'  => $config['name'],
                        'icon'  => $config['icon'],
                        'ctime' => 0,
                        'mtime' => 0,
                        'type' => 'dir'
                    );
                    $items[$magic['name']] = $magic;
                }
            }
        }

        //$item['writeaccess'] = krynAcl::checkAccess(3, $item['path'], 'write', true);

        uksort($items, "strnatcasecmp");

        foreach($items as &$file){
            $file['writeaccess'] = krynAcl::checkAccess(3, $file['path'], 'write', true);
        }

        return $items;
    }


}

?>
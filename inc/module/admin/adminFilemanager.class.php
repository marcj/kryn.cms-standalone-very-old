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
            case 'preview':
                return self::preview($path);

            case 'redirect':
                return self::redirectToPublicUrl($path);


            case 'getVersions':
                return self::getVersions($path);
            case 'addVersion':
                return self::addVersion($path);
            case 'recoverVersion':
                return self::recoverVersion(getArgv('rsn'));

            case 'setPublicAccess':
                return self::setPublicAccess($path, getArgv('access'));
            case 'setInternalAcl':
                return self::setInternalAcl($path, getArgv('rules'));

            //both, public and internal
            case 'getAccess':
                return self::getAccess($path);


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

    public static function preview($pPath){
        $expires = 3600;
        header("Pragma: public");
        header("Cache-Control: maxage=".$expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
        header('Content-Type: image/png');

        $content = self::$fs->getContent(self::normalizePath($pPath));
        $im = imagecreatefromstring($content);
        imagepng($im);
        imagedestroy($im);
        exit;
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

        if (self::normalizePath($pPath) == '/'){
            //its the magic folder itself
            $key = substr($pPath, 1);
            $folder = kryn::$config['magic_folders'][$key];
            return array(
                'path'  => $pPath,
                'magic' => true,
                'name'  => $folder['name'],
                'ctime' => 0,
                'mtime' => 0,
                'type' => 'dir'
            );
        }
        $file = self::$fs->getFile(self::normalizePath($pPath));

        if ($file)
            $file['writeaccess'] = krynAcl::checkAccess(3, $pPath, 'write', true);

        if (self::$fs->magicFolderName)
            $file['path'] = self::$fs->magicFolderName.''.$file['path'];

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

    public static function setPublicAccess($pPath, $pAccess) {

        if ($pAccess == '') $pAccess = -1;
        if (strtolower($pAccess) == 'allow') $pAccess = true;
        if (strtolower($pAccess) == 'deny') $pAccess = false;

        return self::$fs->setPublicAccess(self::normalizePath($pPath), $pAccess);

    }

    public static function getAccess($pPath) {

        $res['writeaccess'] = krynAcl::checkAccess(3, $pPath, 'write', true);

        $res['public'] = self::$fs->getPublicAccess(self::normalizePath($pPath));

        $filepath = esc($pPath);
        $res['internalAcls'] =
                dbTableFetch('system_acl', "type = 3 AND (code LIKE '$filepath\\\%%' OR code LIKE '$filepath\[%')", -1);

        return $res;
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

        if(!krynAcl::checkAccess(3, $pPath, 'read', true)) return array('error'=>'access_denied');
        $expires = 3600;

        self::$fs = self::getFs($pPath);
        $content = self::$fs->getContent(self::normalizePath($pPath));
        if (!$content) return array('error'=>'file_empty');

        header("Pragma: public");
        header("Cache-Control: maxage=".$expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
        header("Content-Type: image/png");

        $image = imagecreatefromstring($content);
        $resizedImage = resizeImage($image, true, '120x70', true);

        imagepng($resizedImage);
        imagedestroy($resizedImage);
        exit;
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
                    $error = t('A PHP extension stopped the file upload.');
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
     * @return string
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

                $oldFile = str_replace('..', '', $file);
                $oldFile = str_replace(chr(0), '', $oldFile);

                if (!krynAcl::checkAccess(3, $oldFile, 'read', true)) continue;

                if ($move)
                    if (!krynAcl::checkAccess(3, $oldFile, 'write', true)) continue;

                $oldFs = self::getFs($oldFile);
                $newPath = $pToPath.'/'.basename($file);
                if (!krynAcl::checkAccess(3, $newPath, 'write', true)) continue;

                $newFs = self::getFs($newPath);

                if ($newFs === $oldFs) {
                    if ($move)
                        $newFs->move(self::normalizePath($oldFile), self::normalizePath($newPath));
                    else
                        $newFs->copy(self::normalizePath($oldFile), self::normalizePath($newPath));
                } else {

                    $file = $oldFs->getFile(self::normalizePath($oldFile));

                    if ($file['type'] == 'file'){
                        $content = $oldFs->getContent(self::normalizePath($oldFile));
                        $newFs->setContent(self::normalizePath($newPath), $content);
                    } else {
                        //we need to move a folder from one file layer to another.
                        if ($oldFs->magicFolderName == '') {
                            //just directly upload the stuff
                            self::copyFolder('inc/template'.$oldFile, $newPath);
                        } else {
                            //we need to copy all files down to our local hdd temporarily
                            $temp = kryn::createTempFolder();
                            self::downloadFolder($oldFile, $temp);
                            self::copyFolder($temp, $newPath);
                            delDir($temp);
                        }
                    }
                    if ($move)
                        $oldFs->deleteFile(self::normalizePath($oldFile));
                }

            }
        }

        return true;
    }

    public static function downloadFolder($pPath, $pTo){

        $fs = self::getFs($pPath);
        $files = $fs->getFiles(self::normalizePath($pPath));

        if (!is_dir($pTo)) mkdirr($pTo);

        if (is_array($files)){
            foreach ($files as $file){
                if ($file['type'] == 'file'){

                    $content = $fs->getContent(self::normalizePath($pPath.'/'.$file['name']));
                    kryn::fileWrite($pTo . '/' . $file['name'], $content);

                } else {
                    self::downloadFolder($pPath.'/'.$file['name'], $pTo.'/'.$file['name']);
                }
            }
        }

    }

    public static function copyFolder($pFrom, $pTo){

        $fs = self::getFs($pTo);
        $fs->createFolder(self::normalizePath($pTo));

        $normalizedPath = self::normalizePath($pTo);

        $files = find($pFrom.'/*');

        foreach ($files as $file){
            $newName = $normalizedPath.'/'.substr($file, strlen($pFrom)+1);

            if (is_dir($file))
                $fs->createFolder(self::normalizePath($newName));
            else
                $fs->createFile(self::normalizePath($newName), kryn::fileRead($file));
        }

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
        $res['mtime'] = filemtime('inc/template/trash');


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
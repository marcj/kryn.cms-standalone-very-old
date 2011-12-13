<?php


class adminFS {

    public $magicFolderName = '';

    /**
     * @param $pPath
     */
    public function createFile($pPath, $pContent = false) {
        if (!file_exists('inc/template'.$pPath)){
            if (!$pContent)
                return touch('inc/template'.$pPath);
            else
                return kryn::fileWrite('inc/template'.$pPath, $pContent);
        }

        return false;
    }

    /**
     * @param $pPath
     */
    public function createFolder($pPath) {
        if (!file_exists('inc/template'.$pPath))
            return mkdir('inc/template'.$pPath, null, true);
        return false;
    }

    /**
     * @param $pPath
     * @param $pContent
     * @return bool
     */
    public function setContent($pPath, $pContent) {

        if (!file_exists($pPath) )
            $this->createFile($pPath);

        $fh = fopen('inc/template'.$pPath, 'w');
        $res = fwrite($fh, $pContent);
        fclose($fh);

        return $res===false?false:true;
    }


    /**
     * list directory contents
     *
     * Should return the item at $pPath with the informations:
     *  array(
     *  path => path to this file for usage in the administration and modules. Not the full http path. No trailing slash!
     *  name => basename(path)
     *  ctime => as unix timestamps
     *  mtime => as unix timestamps
     *  size => filesize in bytes (not for folders)
     *  type => 'dir' or 'file'
     *  items => if it's a directory then here should be all files inside it, with the same infos above (except items)
     *  )
     * @param $pPath
     * @return array|int|bool Returns false if not exists, return 2 if its not a directory, return 3 if the webserver
     * does not have access to this path or returns the items as array
     */
    public function getFiles($pPath){

        if (substr($pPath,0,1) != '/')
            $pPath = '/'.$pPath;

        $pPath = 'inc/template' . $pPath;
        $pPath = str_replace('..', '', $pPath);

        if (!file_exists($pPath))
            return false;

        if (!is_dir($pPath)) return 2;

        if (substr($pPath,-1) != '/')
            $pPath .= '/';

        $h = @opendir($pPath);
        if (file_exists($pPath) && !$h) return 3;

        $items = array();
        while ($file = readdir($h)) {
            if ($file == '.' || $file == '..') continue;
            $path = $pPath . $file;

            $item['path'] = str_replace('inc/template', '', $pPath) . $file;
            $item['name'] = $file;
            $item['type'] = (is_dir($path)) ? 'dir' : 'file';
            $item['size'] = filesize($path);
            $item['ctime'] = filectime($path);
            $item['mtime'] = filemtime($path);
            $items[$item['name']] = $item;
        }

        return $items;
    }

    /**
     * @param $pPath
     * @return int|bool|array Return false if the file doenst exist, return 2 if the webserver does not have access
     * or return array if anything is OK.
     */
    public function getFile($pPath){

        $pPath = 'inc/template'.$pPath;

        if(!file_exists($pPath))
            return false;

        if (!is_readable($pPath)) return 2;

        $type = (is_dir($pPath))?'dir':'file';

        $name = basename($pPath);
        if($pPath == 'inc/template/')
            $name = '/';

        $ctime = filectime($pPath);
        $mtime = filemtime($pPath);

        return array(
            'path' => str_replace('inc/template', '', $pPath),
            'name' => $name,
            'type' => $type,
            'ctime' => $ctime,
            'mtime' => $mtime
        );
    }

    /**
     * disk usage
     *
     * @param $pPath
     */
    public function getSize($pPath){

        $size = 0;
        $fileCount = 0;
        $folderCount = 0;

        $path = 'inc/template'.$pPath;

        if ($h = opendir($path)) {
            while (false !== ($file = readdir($h))) {
                $nextPath = $path . '/' . $file;
                if ($file != '.' && $file != '..' && !is_link($nextPath)) {
                    if (is_dir($nextPath)) {
                        $folderCount++;
                        $result = self::getSize($nextPath);
                        $size += $result['size'];
                        $fileCount += $result['fileCount'];
                        $folderCount += $result['folderCount'];
                    } else if (is_file($nextPath)) {
                        $size += filesize($nextPath);
                        $fileCount++;
                    }
                }
            }
        }
        closedir($h);
        return array(
            'size' => $size,
            'fileCount' => $fileCount,
            'folderCount' => $folderCount
        );
    }

    /**
     * @param $pPath
     */
    public function fileExists($pPath){

        return file_exists('inc/template'.$pPath);
    }

    /**
     *
     * @param $pPathSource
     * @param $pPathTarget
     */
    public function copy($pPathSource, $pPathTarget){

        error_log("copy: $pPathSource, $pPathTarget ");
        if (!file_exists('inc/template'.$pPathSource)) return false;
        return copyr('inc/template'.$pPathSource, 'inc/template'.$pPathTarget);
    }

    /**
     *
     * @param $pPathSource
     * @param $pPathTarget
     */
    public function move($pPathSource, $pPathTarget){

        return rename('inc/template'.$pPathSource, 'inc/template'.$pPathTarget);
    }

    /**
     *
     *
     * @param $pPath
     * @return bool|string
     */
    public function getContent($pPath){

        $pPath = 'inc/template'.$pPath;

        if (!file_exists($pPath)) return false;

        $handle = @fopen($pPath, "r");
        $fs = @filesize($pPath);

        if ($fs > 0)
            $content = @fread($handle, $fs);

        @fclose($handle);

        return $content;

    }

    public function search($pPath, $pPattern, $pDepth = -1){



    }

    public function getPublicUrl($pPath){
        return '/inc/template'.$pPath;
    }

    /**
     *
     *
     * @param $pPath
     * @return bool|int
     */
    public function deleteFile($pPath){

        //this filesystem layer moves the files to trash instead of real removing
        //the class above 'adminFilemanager' handles the deletions in the trash folder
        $path = 'inc/template'.$pPath;
        if (!file_exists($path)) return false;

        $newTrashId = dbInsert('system_files_log', array(
            'path' => $path,
            'modified' => filemtime($path),
            'created' => time(),
            'type' => (is_dir($path)) ? 1 : 0
        ));

        $target = 'inc/template/trash/'.$newTrashId;

        if (is_dir($path)) {
            copyr($path, $target);
            delDir($path);
        } else {
            copy($path, $target);
            unlink($path);
        }

    }

    public function getPublicAccess($pPath){

        $res = $this->getFile($pPath);

        $path = 'inc/template'.$pPath;

        if ($res['type'] == 'file') {
            $htaccess = dirname($path) . '/' . '.htaccess';
        } else {
            $htaccess = $path . '/' . '.htaccess';
        }

        if (@file_exists($htaccess)) {

            $content = kryn::fileRead($htaccess);
            @preg_match_all('/<Files ([^>]*)>\W*(\w*) from all[^<]*<\/Files>/smi', $content, $matches, PREG_SET_ORDER);
            if (count($matches) > 0) {
                foreach ($matches as $match) {
                    $match[1] = str_replace('"', '', $match[1]);
                    if ($res['type'] == 'dir') {
                        $res['htaccess'][] = array(
                            'file' => $match[1],
                            'access' => $match[2]
                        );
                    }

                    if ($res['name'] == $match[1] || ($res['type'] == 'dir' && $match[1] == "*")) {
                        $res['thishtaccess'] = array(
                            'file' => $match[1],
                            'access' => $match[2]
                        );
                    }
                }
            }
        }

    }

}


?>
<?php

namespace Core\FAL;

use Core\Kryn;


/**
 * Local file layer for the local file system.
 * 
 */
class Local extends FALAbstract {

    /**
     * Current root folder.
     * @var string
     */
    private $root = PATH_MEDIA;


    /**
     * Default permission modes for directories.
     * @var integer
     */
    public $dirMode  = 0700;


    /**
     * Default permission modes for files.
     * @var integer
     */
    public $fileMode = 0600;

    /**
     * Default group owner name.
     *
     * @var string
     */
    public $groupName = '';

    public function getFullPath($pPath){
        $root = $this->getRoot();

        if (substr($root, -1) != '/')
            $root .= '/';

        if (substr($pPath, 0, 1) == '/')
            $pPath = substr($pPath, 1);

        return $root . $pPath;
    }


    /**
     * Sets file permissions on file/folder recursively.
     *
     * @param  string $pPath
     * @throws \FileOperationPermittedException
     * @return bool
     */
    public function setPermission($pPath){

        $path = $this->getFullPath($pPath);

        if (!file_exists($path)) return false;

        if ($this->groupName)
            if (!chgrp($path, $this->groupName)){
                throw new \FileOperationPermittedException(tf('Operation to chgrp the file %s to %s is permitted.', $path, $this->groupName));
            }

        if (is_dir($path)){

            if (!chmod($path, $this->dirMode))
                throw new \FileOperationPermittedException(tf('Operation to chmod the folder %s to %o is permitted.', $path, $this->dirMode));

            $sub = find($path.'/*', false);
            if (is_array($sub)){
                foreach ($sub as $path){
                    $this->setPermission(substr($path, 0, strlen($this->getRoot())));
                }
            }
        } else if (is_file($path)){
            if (!chmod($path, $this->fileMode))
                throw new \FileOperationPermittedException(tf('Operation to chmod the file %s to %o is permitted.', $path, $this->fileMode));
        }

        return true;

    }


    /**
     * Loads and converts the configuration in Core\Kryn::$config (./config.php)
     * to appropriate modes.
     *
     */
    public function loadConfig(){

        $this->fileMode = 600;
        $this->dirMode  = 700;

        if (Kryn::$config['fileGroupPermission'] == 'rw'){
            $this->fileMode += 60;
            $this->dirMode  += 70;
        } else if (Kryn::$config['fileGroupPermission'] == 'r'){
            $this->fileMode += 40;
            $this->dirMode  += 50;
        }

        if (Kryn::$config['fileEveryonePermission'] == 'rw'){
            $this->fileMode += 6;
            $this->dirMode  += 7;
        } else if (Kryn::$config['fileEveryonePermission'] == 'r'){
            $this->fileMode += 4;
            $this->dirMode  += 5;
        }

        $this->fileMode = octdec($this->fileMode);
        $this->dirMode  = octdec($this->dirMode);
        $this->groupName = Kryn::$config['fileGroupName'];
    }


    /**
     * {@inheritDoc} 
     */
    public function __construct($pMountPoint, $pParams = null){

        parent::__construct($pMountPoint, $pParams);
        if ($pParams && $pParams['root']) $this->setRoot($pParams['root']);

        $this->loadConfig();
    }

    /**
     * Gets current root folder for this local layer.
     * @param string $pRoot
     */
    public function setRoot($pRoot){
        $this->root = $pRoot;
    }

    /**
     * Sets current root folder for this local layer.
     * @return string
     */
    public function getRoot(){
        return $this->root;
    }

    /**
     * {@inheritDoc} 
     */
    public function createFile($pPath, $pContent = false) {

        $path = $this->getFullPath($pPath);

        if (!file_exists(dirname($path)))
            $this->createFolder(dirname($pPath));

        if (!file_exists($path)){
            if (($res = file_put_contents($path, $pContent) === false))
                if (is_writable(dirname($path)))
                    throw new \FileIOException(tf('Can not create file %s', $path));
                else
                    throw new \FileNotWritableException(tf('Can not create the file %s in %s, since it is not writeable.', $path, dirname($path)));
        }

        $this->setPermission($pPath);

        return $res === false ? false : true;
    }

    /**
     * @param string $pPath The full absolute path
     * @return bool
     * @throws \FileOperationPermittedException
     * @throws \FileIOException
     */
    private function _createFolder($pPath){

        is_dir(dirname($pPath)) or $this->_createFolder(dirname($pPath));

        if (!is_dir($pPath)){
            if (!@mkdir($pPath))
                throw new \FileIOException(tf('Can not create folder %s.', $pPath));
        }

        if ($this->groupName)
            if (!@chgrp($pPath, $this->groupName))
                throw new \FileOperationPermittedException(tf('Operation to chgrp the folder %s to %s is permitted.', $pPath, $this->groupName));

        if (!chmod($pPath, $this->dirMode))
            throw new \FileOperationPermittedException(tf('Operation to chmod the folder %s to %o is permitted.', $pPath, $this->dirMode));

        return is_dir($pPath);
    }

    /**
     * {@inheritDoc} 
     */
    public function createFolder($pPath) {

        if (!file_exists($path = $this->getFullPath($pPath)))
            return $this->_createFolder($path);

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function setContent($pPath, $pContent) {

        $path = $this->getFullPath($pPath);

        if (!file_exists($path) )
            $this->createFile($pPath);

        else if (!is_writable($path))
            throw new \FileNotWritableException(tf('File %s is not writable.', $path));


        $res = file_put_contents($path, $pContent);
        $this->setPermission($pPath);

        return $res === false?false:true;
    }


    /**
     * {@inheritDoc} 
     */
    public function getFiles($pPath){

        if (substr($pPath,0,1) != '/')
            $pPath = '/'.$pPath;

        $pPath = $this->getRoot() . $pPath;
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

            $item['path'] = str_replace($this->getRoot(), '', $pPath) . $file;
            $item['name'] = $file;

            $item['type'] = (is_dir($path)) ? 'dir' : 'file';
            if ($item['type'] == 'file'){
                $info = pathinfo($file);
                $item['extension'] = $info['extension'];
            } else {
                $item['extension'] = 'directory';
            }

            $item['size'] = filesize($path);
            $item['ctime'] = filectime($path);
            $item['mtime'] = filemtime($path);
            $items[] = $item;
        }

        return $items;
    }

    /**
     * {@inheritDoc} 
     */
    public function getFile($pPath){

        $pPath = $this->getRoot().$pPath;
        if(!file_exists($pPath))
            return false;

        if (!is_readable($pPath)) return -1;

        $type = (is_dir($pPath))?'dir':'file';

        $name = basename($pPath);
        if($pPath == $this->getRoot())
            $name = '/';

        $ctime = filectime($pPath);
        $mtime = filemtime($pPath);

        $item['type'] = (is_dir($pPath)) ? 'dir' : 'file';
        if ($item['type'] == 'file'){
            $info = pathinfo($pPath);
            $extension = $info['extension'];
        } else {
            $extension = 'directory';
        }

        return array(
            'path'  => str_replace($this->getRoot(), '', $pPath),
            'name'  => $name,
            'type'  => $type,
            'ctime' => $ctime,
            'extension' => $extension,
            'mtime' => $mtime,
            'size'  => filesize($pPath)
        );
    }

    /**
     * {@inheritDoc} 
     */
    public function getSize($pPath){

        $size = 0;
        $fileCount = 0;
        $folderCount = 0;

        $path = $this->getRoot().$pPath;

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
     * {@inheritDoc}
     */
    public function fileExists($pPath){
        return file_exists($this->getRoot().$pPath);
    }

    /**
     * {@inheritDoc}
     */
    public function getCount($pFolderPath){
        return count(glob($this->getRoot().$pFolderPath.'/*'));
    }

    /**
     * {@inheritDoc} 
     */
    public function copy($pPathSource, $pPathTarget){
        if (!file_exists($this->getRoot().$pPathSource)) return false;
        return copyr($this->getRoot().$pPathSource, $this->getRoot().$pPathTarget);
    }

    /**
     * {@inheritDoc} 
     */
    public function move($pPathSource, $pPathTarget){

        return rename($this->getRoot().$pPathSource, $this->getRoot().$pPathTarget);
    }

    /**
     * {@inheritDoc}
     */
    public function getMd5($pPath){
        return md5_file($this->getRoot().$pPath);
    }

    /**
     * {@inheritDoc} 
     */
    public function getContent($pPath){

        $pPath = $this->getRoot().$pPath;

        if (!file_exists($pPath)) return false;

        $handle = @fopen($pPath, "r");
        $fs = @filesize($pPath);

        if ($fs > 0)
            $content = @fread($handle, $fs);

        @fclose($handle);

        return $content;

    }

    /**
     * {@inheritDoc} 
     */
    public function search($pPath, $pPattern, $pDepth = -1, $pCurrentDepth = 1){

        $result = array();
        $files = $this->getFiles($pPath);

        $q = str_replace('/', '\/', $pPattern);

        foreach ($files as $file){
            if (preg_match('/^'.$q.'/i', $file['name'], $match) !== 0){
                $result[] = $file;
            }
            if ($file['type'] == 'dir' && ($pDepth == -1 || $pCurrentDepth < $pDepth)){
                $newPath = $pPath . ($pPath=='/'?'':'/') . $file['name'];
                $more = $this->search($newPath, $pPattern, $pDepth, $pCurrentDepth+1);
                if (is_array($more))
                    $result = array_merge($result, $more);
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc} 
     */
    public function getPublicUrl($pPath){
        return '/'.$this->getRoot().$pPath;
    }

    /**
     * {@inheritDoc} 
     */
    public function remove($pPath){

        $path = $this->getRoot().$pPath;

        if (is_dir($path)) {
            delDir($path);
        } else if (is_file($path)){
            unlink($path);
        }

    }

    /**
     * {@inheritDoc} 
     */
    public function getPublicAccess($pPath){

        $path = $this->getRoot().$pPath;

        if (!file_exists($path)) return false;

        if (!is_dir($path)) {
            $htaccess = dirname($path) . '/' . '.htaccess';
        } else {
            $htaccess = $path . '/' . '.htaccess';
        }
        $name = basename($pPath);

        if (@file_exists($htaccess)) {

            $content = kryn::fileRead($htaccess);
            @preg_match_all('/<Files ([^>]*)>\W*(\w*) from all[^<]*<\/Files>/smi', $content, $matches, PREG_SET_ORDER);
            if (count($matches) > 0) {
                foreach ($matches as $match) {

                    $match[1] = str_replace('"', '', $match[1]);
                    $match[1] = str_replace('\'', '', $match[1]);

                    //TODO, what is $res?
                    if ($name == $match[1] || ($res['type'] == 'dir' && $match[1] == "*")) {
                        return strtolower($match[2])=='allow'?true:false;
                    }
                }
            }
        }
        return -1;
    }

    /**
     * {@inheritDoc} 
     */
    public function setPublicAccess($pPath, $pAccess = false){

        $path = $this->getRoot().$pPath;

        if (!is_dir($path) == 'file') {
            $htaccess = dirname($path) . '/' . '.htaccess';
        } else {
            $htaccess = $path . '/' . '.htaccess';
        }

        if (!file_exists($htaccess) && !touch($htaccess)) {
            klog('files', t('Can not set the file access, because the system can not create the .htaccess file'));
            return false;
        }

        $content = kryn::fileRead($htaccess);

        if (!is_dir($pPath)) {
            $filename = '"' . basename($pPath) . '"';
            $filenameesc = preg_quote($filename, '/');
        } else {
            $filename = "*";
            $filenameesc = '\*';
        }

        $content = preg_replace('/<Files ' . $filenameesc . '>\W*(\w*) from all[^<]*<\/Files>/i', '', $content);

        if ($pAccess !== -1) {
            $access = $pAccess==true?'Allow':'Deny';
            $content .= "\n<Files $filename>\n\t$access from all\n</Files>";
        }

        kryn::fileWrite($htaccess, $content);

        return true;
    }
}

?>
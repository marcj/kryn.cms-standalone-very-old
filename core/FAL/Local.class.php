<?php

namespace Core\FAL;

use Core\File;

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
     * {@inheritDoc} 
     */
    public function __construct($pEntryPoint, $pParams = null){

        parent::__construct($pEntryPoint, $pParams);
        if ($pParams && $pParams['root']) $this->setRoot($pParams['root']);

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

        if (!file_exists(dirname($this->getRoot().$pPath)))
            $this->createFolder(dirname($pPath));


        if (!file_exists($this->getRoot().$pPath)){
            if (!$pContent)
                $res = touch($this->getRoot().$pPath);
            else
                $res = file_put_contents($this->getRoot().$pPath, $pContent);
        }

        File::setPermission($this->getRoot().$pPath);

        return $res;
    }

    private function _createFolder($pPath){
        is_dir(dirname($pPath)) || $this->_createFolder(dirname($pPath));
        if (!@mkdir($pPath, File::$dirMode))
            throw new \FileIOException(tf('Can not create folder %s', $pPath));
        return is_dir($pPath);
    }

    /**
     * {@inheritDoc} 
     */
    public function createFolder($pPath) {
        
        if (!file_exists($path = $this->getRoot().$pPath))
            return $this->_createFolder($path);
        
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function setContent($pPath, $pContent) {

        if (!file_exists($this->getRoot().$pPath) )
            $this->createFile($pPath);
 
        if (!is_writable($this->getRoot().$pPath))
            throw new \FileNotWritableException(tf('File %s is not writable.', $this->getRoot().$pPath));

        $res = file_put_contents($this->getRoot().$pPath, $pContent);
        File::setPermission($this->getRoot().$pPath);

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

        return array(
            'path'  => str_replace($this->getRoot(), '', $pPath),
            'name'  => $name,
            'type'  => $type,
            'ctime' => $ctime,
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
    public function delete($pPath){

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
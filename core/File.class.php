<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@Kryn.org>
 *
 * To get the full copyright and license information, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

namespace Core;

/**
 * File
 *
 * Class to proxy the file functions to the appropriate file layer.
 * Use this class, if you want to modify files inside media/.
 *
 * This class resolves all mount points inside media/.
 *
 */
class File {


    /**
     * Caches all objects of active file layers (magic folders)
     *
     * @var array
     */
    public static $fsObjects = array();


    /**
     * Default permission modes for directories.
     * @var integer
     */
    public static $dirMode  = 0700;


    /**
     * Default permission modes for files.
     * @var integer
     */
    public static $fileMode = 0600;


    /**
     * Sets file permissions on file/folder recursivly.
     * 
     * @param  string $pPath the path
     */
    public static function setPermission($pPath){

        if (is_dir($pPath)){
            
            @chmod($pPath, self::$dirMode);

            $sub = find($pPath.'/*', false);
            if (is_array($sub)){
                foreach ($sub as $path){
                    self::fixFiles($path);
                }
            }
        } else if (is_file($pPath)){
            @chmod($pPath, self::$fileMode);
        }

    }


    /**
     * Loads and converts the configuration in Core\Kryn::$config (./config.php)
     * to appropriate modes.
     * 
     */
    public static function loadConfig(){

        self::$fileMode = 600;
        self::$dirMode  = 700;

        if (kryn::$config['fileGroupPermission'] == 'rw'){
            self::$fileMode += 60;
            self::$dirMode  += 70;
        } else if (kryn::$config['fileGroupPermission'] == 'r'){
            self::$fileMode += 40;
            self::$dirMode  += 50;
        }

        if (kryn::$config['fileEveryonePermission'] == 'rw'){
            self::$fileMode += 6;
            self::$dirMode  += 7;
        } else if (kryn::$config['fileEveryonePermission'] == 'r'){
            self::$fileMode += 4;
            self::$dirMode  += 5;
        }

        self::$fileMode = octdec(self::$fileMode);
        self::$dirMode  = octdec(self::$dirMode);
    }


    /**
     *
     * Returns the instance of the file layer object for the given path.
     *
     * @static
     * @param  string $pPath
     * @return object
     */
    public static function getLayer($pPath){

        $class = '\Core\FAL\Local';
        $params['root'] = PATH_MEDIA;
        $entryPoint = '';

        if ($pPath != '/') {

            $sPos = strpos(substr($pPath, 1), '/');
            if ($sPos === false)
                $firstFolder = substr($pPath,1);
            else
                $firstFolder = substr($pPath, 1, $sPos);

            //if firstFolder a mounted folder?
            if ($fs = Kryn::$config['mounts'][$firstFolder]) {
                $class = $fs['class'];
                $params = $fs['params'];
                $entryPoint = $firstFolder;
            }
        }

        if (self::$fsObjects[$class]) return self::$fsObjects[$class];
        if (class_exists($class))
            self::$fsObjects[$class] = new $class($entryPoint, $params);
        else
            return false;

        return self::$fsObjects[$class];

    }


    /**
     * Removes the name of the mount point from the proper layer.
     * Also removes '..' and replaces '//' => '/'
     *
     * This is needed because the file layer gets the relative path under his own root.
     *
     * @param  string $pPath
     * @return string
     */
    public static function normalizePath($pPath){

        $fs = self::getLayer($pPath);
        $pPath = substr($pPath, strlen($fs->getMountPoint()));

        $pPath = str_replace('..', '', $pPath);
        $pPath = str_replace('//', '/', $pPath);

        if (substr($pPath, 0, 1) == '/')
            $pPath = substr($pPath, 1);

        return $pPath;
    }


    /**
     * Removes unusual chars in file names.
     *
     * @static
     * @param  string $pName
     * @return string
     */
    public static function normalizeName($pName){
        $s = array('ä', 'ö', 'ü', 'ß');
        $r = array('ae', 'oe', 'ue', 'ss');
        $name = @str_replace($s, $r, $pName);
        $name = @preg_replace('/[^a-zA-Z0-9\.\_\(\)]/', "-", $name);
        $name = @preg_replace('/--+/', '-', $name);
        return $name;
    }


    /**
     * Gets the content of a file.
     *
     * @static
     * @param string $pPath
     * @return bool|string
     */
    public static function getContent($pPath){

        $fs = self::getLayer($pPath);
        return $fs->getContent(self::normalizePath($pPath));

    }


    /**
     * Sets the content of a file.
     *
     * Creates the file if not exist. Created also the full folder path if
     * the they doesnt exist.
     * 
     * @static
     * @param string $pPath
     * @param string $pContent
     * @return bool
     */
    public static function setContent($pPath, $pContent){

        $fs = self::getLayer($pPath);
        return $fs->setContent(self::normalizePath($pPath), $pContent);

    }


    /**
     * Checks if a file exists.
     *
     * @static
     * @param string $pPath
     * @return bool
     */
    public static function exists($pPath){

        $fs = self::getLayer($pPath);
        return $fs->fileExists(self::normalizePath($pPath));

    }


    /**
     * Creates a file with default permissions.
     * Creates also the full folder path if the they doesnt exist.
     * 
     * @static
     * @param string $pPath
     * @return bool
     */
    public static function createFile($pPath, $pContent){

        $fs = self::getLayer($pPath);
        return $fs->createFile(self::normalizePath($pPath), $pContent);

    }


    /**
     * Creates a folder with default permissions.
     * Creates also the full folder path if the they doesnt exist.
     * 
     * @static
     * @param string $pPath
     * @return bool
     */
    public static function createFolder($pPath){

        $fs = self::getLayer($pPath);
        return $fs->createFolder(self::normalizePath($pPath));

    }

    /**
     * Deletes a file/folder.
     * 
     * @static
     * @param string $pPath
     * 
     * @return bool
     */
    public static function delete($pPath){

        $fs = self::getLayer($pPath);
        return $fs->delete(self::normalizePath($pPath));

    }

 
    /**
     * Return information for a file/folder.
     *
     * The result contains following information:
     *  [path(relative), name, type(dir|file), ctime(unixtimestamp), mtime(unixtimestamp), size(bytes)]
     *  
     *  array(
     *    path => path to this file/folder for usage in the administration and modules. Not the full http path. No trailing slash!
     *    name => basename(path)
     *    ctime => as unix timestamps
     *    mtime => as unix timestamps
     *    size => filesize in bytes (not for folders)
     *    type => 'dir' or 'file'
     *  )
     *
     * @static
     * @param string $pPath
     * 
     * @return int|bool|array Return false if the file doenst exist,
     *                        return 2 if the webserver does not have access
     *                        or return array with the information.
     */
    public static function getFile($pPath){

        $fs = self::getLayer($pPath);
        return $fs->getFile(self::normalizePath($pPath));

    }


    /**
     * List directory contents.
     * 
     * Same as in getFile() but in a list.
     * 
     *  array(
     *    array(
     *      path => path to the file/folder for usage in the administration and modules. Not the full http path. No trailing slash!
     *      name => basename(path)
     *      ctime => as unix timestamps
     *      mtime => as unix timestamps
     *      size => filesize in bytes (not for folders)
     *      type => 'file' | 'dir'
     *      mount => boolean (if the folder is a mount point)
     *    )
     *  )
     *
     * @static
     * @param string $pPath
     * 
     * @return int|bool|array Return false if the file doenst exist,
     *                        return 2 if the webserver does not have access
     *                        or return array with the information.
     */
    public static function getFiles($pPath){


        //$access = krynAcl::check(3, $pPath, 'read', true);
        //if (!$access) return false;

        $fs = krynFile::getLayer($pPath);

        if ($pPath == '/trash'){
            return self::getTrashFiles();
        }

        $items = $fs->getFiles(self::normalizePath($pPath));
        if (!is_array($items)) return $items;

        if (count($items) == 0) return array();

        if ($fs->getEntryPoint())
            foreach ($items as &$file)
                $file['path'] = $fs->getEntryPoint().$file['path'];

        if($pPath == '/'){
            if (is_array(Kryn::$config['magic_folder'])) {
                foreach (Kryn::$config['magic_folder'] as $folder => &$config ){
                    $magic = array(
                        'path'  => '/'.$folder,
                        'mount' => true,
                        'name'  => $folder,
                        'icon'  => $config['icon'],
                        'ctime' => 0,
                        'mtime' => 0,
                        'type' => 'dir'
                    );
                    $items[] = $magic;
                }
            }
        }

        uksort($items, "strnatcasecmp");

        $where = array();
        $vals  = array();
        foreach($items as &$file){
            $vals[]  = $file['path'];
            $where[] = 'path = ?';
        }
        $sql = 'SELECT id, path FROM %pfx%system_files WHERE 1=0 OR '.implode(' OR ', $where);

        $res = dbExec($sql, $vals);
        $path2id = array();

        while ($row = dbFetch($res)){
            $path2id[$row['path']] = $row['id'];
        }

        foreach($items as &$file){

            //todo, create new option 'show hidden files' in user settings and depend in that
            //we'll show files with a dot at the beginning.

            //$file['object_id'] = Object
            if (!$path2id[$file['path']]){
                $id = dbInsert('system_files', array('path' => $file['path']));
                $file['id'] = $id;
            } else {
                $file['id'] = $path2id[$file['path']];
            }
            $file['writeaccess'] = krynAcl::checkUpdate('file', $file['path']);
        }

        return $items;
    }


    /**
     * Copies a file to a destination.
     * If the source is a folder, it copies recursivly.
     *
     * @static
     * @param string $pPathSource
     * @param string $pPathTarget
     * @return bool
     */
    public static function copy($pFrom, $pTo){
        //TODO, move the code from adminFilemanager::paste() to here

    }


    /**
     * Moves a file to new destinaton.
     *
     * @static
     * @param string $pPathSource
     * @param string $pPathTarget
     * @return bool
     */
    public static function move($pFrom, $pTo){
        //TODO, move the code from adminFilemanager::paste() to here

    }


    /**
     * Searchs files in a path by a regex pattern.
     * 
     * @static
     * @param  string  $pPath
     * @param  string  $pPattern      Preg regex
     * @param  integer $pDepth        Maximum depth. -1 for unlimited.
     * @param  integer $pCurrentDepth Internal
     * @return array                  Files array
     */
    public static function search($pFrom, $pTo){
        //TODO, move the code from adminFilemanager::search() to here

    }


    /**
     *
     * Returns the public URL of the file $pPath
     * With HTTP or HTTPs, depends on Core\Kryn::$ssl.
     *
     * @static
     * @param  string $pPath
     * @return string
     */
    public static function getUrl($pPath){
        $fs = self::getLayer($pPath);
        $url = $fs->getPublicUrl(self::normalizePath($pPath));

        //TODO, check if $url contains http(s)://, and then decide if we need to add it
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0)
            return $url;

        return 'http' . (Core\Kryn::$ssl?'s':'') . '://'.$url;
    }


    /**
     * Translates the internal id to the real path.
     * Example: getPath(45) => '/myImageFolder/Picture1.png'
     *
     * @static
     * @param  integer|string $pId String for backward compatibility
     * @return string
     */
    public static function getPath($pId){

        if (!is_numeric($pId))
            return PATH_MEDIA.$pId;

        //page bases caching here
        $sql = 'SELECT id, path FROM %pfx%system_files WHERE id = '.($pId+0);
        $item = dbExfetch($sql);

        return $item['path'];

    }


    /**
     * List trash contents.
     * 
     * 
     *  array(
     *    array(
     *      path => path to the file/folder
     *      name => basename(path)
     *      ctime => as unix timestamps
     *      mtime => as unix timestamps
     *      size => filesize in bytes (not for folders)
     *      type => 'file' | 'dir'
     *      original_id => trash id
     *      original_path => original path before the deletion
     *    )
     *  )
     * 
     * @static
     * @param string $pPath
     * 
     * @return int|bool|array Return false if the file doenst exist,
     *                        return 2 if the webserver does not have access
     *                        or return array with the information.
     */
    public static function getTrashFiles(){

        $files = array();
        $h = opendir(PATH_MEDIA.'trash/');

        while ($file = readdir($h)) {
            if ($file == '.svn' || $file == '.' || $file == '..') continue;
            $files[] = $file;
        }

        natcasesort($files);

        $res = array();
        foreach ($files as $file) {

            if ($file == '.htaccess') continue;

            $path = '/trash/' . $file;

            $dbItem = dbTableFetch('system_files_log', 1, 'id = ' . ($file+0));

            $item['name'] = basename($dbItem['path']).'-v'.$file;
            $item['path'] = str_replace(PATH_MEDIA, '', $path);
            $item['original_id'] = $dbItem['id'];
            $item['original_path'] = $dbItem['path'];
            $item['mtime'] = $dbItem['modified'];
            $item['ctime'] = $dbItem['created'];
            $item['type'] = ($dbItem['type'] == 1) ? 'dir' : 'file';

            $res[] = $item;

        }

        return $res;
    }


}

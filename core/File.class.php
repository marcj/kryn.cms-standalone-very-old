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
 * krynFile - file abstraction layer
 *
 */

class File {

    /**
     * Caches all objects of active file layers (magic folders)
     *
     * @var array
     */
    public static $fsObjects = array();


    public static $dirMask = 0775;

    public static $fileMask = 0660;

    public static function fixMask($pPath){

        if (is_dir($pPath)){
            $sub = find($pPath.'/*', false);
            if (is_array($sub)){
                foreach ($sub as $path){
                    self::fixMask($path);
                }
            }
        } else if (is_file($pPath)){
            @chmod($pPath, self::$fileMask);
        }

    }

    /**
     *
     * Returns the instance of the file layer class for the given path
     *
     * @static
     * @param  string $pPath
     * @return object
     */
    public static function getLayer($pPath){

        $class = 'adminFs';
        $file = false;

        if ($pPath != '/') {

            $sPos = strpos(substr($pPath, 1), '/');
            if ($sPos === false)
                $firstFolder = substr($pPath,1);
            else
                $firstFolder = substr($pPath, 1, $sPos);

            //if firstFolder a magic folder?
            if ($fs = Kryn::$config['magic_folder'][$firstFolder]) {
                $class = $fs['class'];
                $file = $fs['file'];
                $params = $fs['params'];
                $prefix = '/'.$firstFolder;
            }
        }

        if (self::$fsObjects[$class]) return self::$fsObjects[$class];

        if ($file)
            require_once($file);

        if (class_exists($class))
            self::$fsObjects[$class] = new $class($params);
        else
            return false;

        self::$fsObjects[$class]->magicFolderName = $prefix;

        return self::$fsObjects[$class];

    }

    /**
     * Removes the magicFolderName from the file layer instance in the path,
     * also remove '..' and replace '//' => '/'
     *
     * This is needed because the file layer gets the path under his own root.
     *
     * @param  string $pPath
     * @return string
     */
    public static function normalizePath($pPath){

        $fs = self::getLayer($pPath);
        $pPath = substr($pPath, strlen($fs->magicFolderName));

        $pPath = str_replace('..', '', $pPath);
        $pPath = str_replace('//', '/', $pPath);

        if (substr($pPath, 0, 1) != '/')
            $pPath = '/'.$pPath;

        return $pPath;
    }

    /**
     * Removes unusual chars in file names, also lowercase it
     *
     * @static
     * @param  string $pName
     * @return string
     */
    public static function normalizeName($pName){
        $name = @str_replace('ä', "ae", strtolower($pName));
        $name = str_replace('..', '', $name);
        $name = @str_replace('ö', "oe", $name);
        $name = @str_replace('ü', "ue", $name);
        $name = @str_replace('ß', "ss", $name);
        $name = @preg_replace('/[^a-zA-Z0-9\.\_\(\)]/', "-", $name);
        $name = @preg_replace('/--+/', '-', $name);
        return $name;
    }

    /**
     * Reads entire file into a string
     *
     * @static
     * @param  string $pPath
     *
     * @return string
     */
    public static function getContent($pPath){

        $fs = self::getLayer($pPath);
        return $fs->getContent(self::normalizePath($pPath));

    }


    /**
     * Write a string to a file
     *
     * @static
     * @param string $pPath
     * @param string $pContent
     *
     * @return bool
     */
    public static function setContent($pPath, $pContent){

        $fs = self::getLayer($pPath);
        return $fs->setContent(self::normalizePath($pPath), $pContent);

    }

    /**
     * Checks whether a file or directory exists
     *
     * @static
     * @param  string $pPath
     * @return bool
     */
    public static function exists($pPath){

        $fs = self::getLayer($pPath);
        return $fs->fileExists(self::normalizePath($pPath));

    }

    /**
     * Creates a file
     *
     * @static
     * @param  string $pPath
     * @return bool
     */
    public static function createFile($pPath, $pContent){

        $fs = self::getLayer($pPath);
        return $fs->createFile(self::normalizePath($pPath), $pContent);

    }

    /**
     * Creates a folder
     *
     * @static
     * @param  string $pPath
     * @return bool
     */
    public static function createFolder($pPath){

        $fs = self::getLayer($pPath);
        return $fs->createFolder(self::normalizePath($pPath));

    }

    /**
     * Gets the basic information of the file
     *
     * @static
     * @param  string $pPath
     * @return array
     */
    public static function getFile($pPath){

        $fs = self::getLayer($pPath);
        return $fs->getFile(self::normalizePath($pPath));

    }

    /**
     * Gets the basic information of all files inside the folder
     *
     * @static
     * @param  string $pPath
     * @return array
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

        if ($fs->magicFolderName)
            foreach ($items as &$file)
                $file['path'] = $fs->magicFolderName.$file['path'];

        if($pPath == '/'){
            if (is_array(Kryn::$config['magic_folder'])) {
                foreach (Kryn::$config['magic_folder'] as $folder => &$config ){
                    $magic = array(
                        'path'  => '/'.$folder,
                        'magic' => true,
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
        foreach($items as &$file){
            $where[] = 'path = \''.esc($file['path']).'\'';
        }
        $sql = 'SELECT id, path FROM %pfx%system_files WHERE 1=0 OR '.implode(' OR ', $where);

        $res = dbExec($sql);
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

    public static function copy($pFrom, $pTo){
        //TODO, move the code from adminFilemanager::paste() to here

    }

    public static function move($pFrom, $pTo){
        //TODO, move the code from adminFilemanager::paste() to here

    }

    public static function search($pFrom, $pTo){
        //TODO, move the code from adminFilemanager::search() to here

    }

    /**
     *
     * Returns the public URL of the file $pPath
     * With HTTP or HTTPs, depends on Kryn::$ssl.
     *
     * @static
     * @param  string $pPath
     * @return string
     */
    public static function getUrl($pPath){
        $fs = self::getLayer($pPath);
        $url = $fs->getPublicUrl(self::normalizePath($pPath));

        //TODO, check if $url contains http(s)://, and then decide if we need to add it

        return $url;
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
            $item['lastModified'] = $dbItem['modified'];
            $item['mtime'] = $dbItem['modified'];
            $item['type'] = ($dbItem['type'] == 1) ? 'dir' : 'file';

            $res[] = $item;

        }

        return $res;
    }


}

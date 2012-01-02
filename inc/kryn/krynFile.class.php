<?php

/**
 * krynFile - file abstraction layer
 *
 *
 *
 */
class krynFile {

    /**
     * Caches all objects of active file layers (magic folders)
     *
     * @var array
     */
    public static $fsObjects = array();


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
        $name = str_replace('ä', "ae", $pName);
        $name = str_replace('..', '', $name);
        $name = str_replace('ö', "oe", $name);
        $name = str_replace('ü', "ue", $name);
        $name = str_replace('ß', "ss", $name);
        $name = preg_replace('/[^a-zA-Z0-9\.\_\(\)]/', "-", $name);
        $name = preg_replace('/--+/', '-', $name);
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

        $fs = self::getLayer($pPath);
        return $fs->getFile(self::normalizePath($pPath));

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
     * With HTTP or HTTPs, depends on kryn::$ssl.
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

        if (is_string($pId))
            return 'inc/template/'.$pId;


        //TODO, fetch the path from the id. table: system_files
        //TODO, not done here

    }


}


?>
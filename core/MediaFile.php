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
class MediaFile
{
    /**
     * Caches all objects of active file layers (magic folders)
     *
     * @var array
     */
    public static $fsObjects = array();

    /**
     * Whether this class checks against the object 'file' and appends
     * the 'id'. Checks also against permission/acl table.
     *
     * @var bool
     */
    public static $checkObject = true;

    /**
     * Whether this class checks for file mount.
     *
     * @var bool
     */
    public static $checkMounts = true;

    /**
     *
     * Returns the instance of the file layer object for the given path.
     *
     * @static
     * @param  string $pPath
     * @return object
     */
    public static function getLayer($pPath)
    {
        $class = 'Core\FAL\Local';
        $params['root'] = PATH.PATH_MEDIA;
        $mountName = '';

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
                $mountName = $firstFolder;
            }
        }

        if (static::$fsObjects[$class]) return static::$fsObjects[$class];

        if (class_exists($class))
            static::$fsObjects[$class] = new $class($mountName, $params);
        else
            throw new \ClassNotFoundException(tf('Class %s not found.', $class));

        return static::$fsObjects[$class];

    }

    /**
     * Removes the name of the mount point from the proper layer.
     * Also removes '..' and replaces '//' => '/'
     *
     * This is needed because the file layer gets the relative path under his own root.
     * Forces a / at the beginning, removes the trailing / if exists.
     *
     * @param  string $pPath
     * @return string
     */
    public static function normalizePath($pPath)
    {
        $fs = static::getLayer($pPath);
        $pPath = substr($pPath, strlen($fs->getMountPoint()));

        $pPath = str_replace('..', '', $pPath);
        $pPath = str_replace('//', '/', $pPath);

        if (substr($pPath, 0, 1) != '/')
            $pPath = '/'.$pPath;

        if (substr($pPath, -1) == '/')
            $pPath = substr($pPath, 0, -1);

        return $pPath;
    }

    /**
     * Removes unusual chars in file names.
     *
     * @static
     * @param  string $pName
     * @return string
     */
    public static function normalizeName($pName)
    {
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
     * @param  string      $pPath
     * @return bool|string
     */
    public static function getContent($pPath)
    {
        $fs = static::getLayer($pPath);

        return $fs->getContent(static::normalizePath($pPath));

    }

    /**
     * Sets the content of a file.
     *
     * Creates the file if not exist. Created also the full folder path if
     * the they doesnt exist.
     *
     * @static
     * @param  string $pPath
     * @param  string $pContent
     * @return bool
     */
    public static function setContent($pPath, $pContent)
    {
        $fs = static::getLayer($pPath);

        return $fs->setContent(static::normalizePath($pPath), $pContent);

    }

    /**
     * Checks if a file exists.
     *
     * @static
     * @param  string $pPath
     * @return bool
     */
    public static function exists($pPath)
    {
        $fs = static::getLayer($pPath);

        return $fs->fileExists(static::normalizePath($pPath));

    }

    /**
     * Creates a file with default permissions.
     * Creates also the full folder path if the they doesnt exist.
     *
     * @static
     * @param  string $pPath
     * @return bool
     */
    public static function createFile($pPath, $pContent)
    {
        $fs = static::getLayer($pPath);

        return $fs->createFile(static::normalizePath($pPath), $pContent);

    }

    /**
     * Creates a folder with default permissions.
     * Creates also the full folder path if the they doesnt exist.
     *
     * @static
     * @param  string $pPath
     * @return bool
     */
    public static function createFolder($pPath)
    {
        $fs = static::getLayer($pPath);

        return $fs->createFolder(static::normalizePath($pPath));

    }

    /**
     * Removes a file/folder.
     *
     * @static
     * @param string $pPath
     *
     * @return bool
     */
    public static function remove($pPath)
    {
        $fs = static::getLayer($pPath);

        return $fs->remove(static::normalizePath($pPath));

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
    public static function getFile($pPath)
    {
        $fs = static::getLayer($pPath);
        $path = static::normalizePath($pPath);

        $file = $fs->getFile($path);
        if (!$file) return null;

        if (static::$checkObject) {
            $item = FileQuery::create()->filterByPath($pPath)->orderById()->findOne();
            if (!$item) {
                //insert
                $item = new File();
                $item->setPath($pPath);
                $item->setHash($fs->getMd5($path));
                $item->save();
                $id = $item->getId();
            } else {
                $id = $item->getId();
            }

            $file['id'] = $id+0;
        }

        return $file;
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
     *                        return -1 if the webserver does not have access
     *                        or return array with the information.
     */
    public static function getFiles($pPath)
    {
        //$access = krynAcl::check(3, $pPath, 'read', true);
        //if (!$access) return false;

        $fs = static::getLayer($pPath);
        $path = static::normalizePath($pPath);

        if ($pPath == '/trash') {
            return static::getTrashFiles();
        }

        $items = $fs->getFiles(static::normalizePath($pPath));
        if (!is_array($items)) return $items;

        if (count($items) == 0) return array();

        if (static::$checkMounts) {
            if ($fs->getMountPoint())
                foreach ($items as &$file)
                    $file['path'] = $fs->getMountPoint().$file['path'];

            if ($pPath == '/') {
                if (is_array(Kryn::$config['mounts'])) {
                    foreach (Kryn::$config['mounts'] as $folder => &$config) {
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
        }

        uksort($items, "strnatcasecmp");

        if (static::$checkObject) {
            $where = array();
            $vals  = array();
            foreach ($items as &$file) {
                $vals[]  = $file['path'];
                $where[] = 'path = ?';
            }
            $sql = 'SELECT id, path FROM '.pfx.'system_file WHERE 1=0 OR '.implode(' OR ', $where);

            $res = dbQuery($sql, $vals);
            $path2id = array();

            while ($row = dbFetch($res)) {
                $path2id[$row['path']] = $row['id'];
            }

            foreach ($items as &$file) {

                //todo, create new option 'show hidden files' in user settings and depend on that
                //we'll show files with a dot at the beginning.

                //$file['object_id'] = Object
                if (!$path2id[$file['path']]) {
                    $id = dbInsert('system_file', array('path' => $file['path'], 'hash' => $fs->getMd5($path)));
                    $file['id'] = $id;
                } else {
                    $file['id'] = $path2id[$file['path']];
                }
                $file['writeaccess'] = Permission::checkUpdate('file', $file['path']);
            }
            dbFree($res);
        }

        return $items;
    }

    /**
     * Returns the file count inside $pFolderPath
     *
     * @static
     * @param  string $pFolderPath
     * @return mixed
     */
    public static function getCount($pFolderPath)
    {
        $fs = static::getLayer($pFolderPath);

        return $fs->getCount(static::normalizePath($pFolderPath));
    }


    /**
     * Copies a file to a destination.
     * If the source is a folder, it copies recursivly.
     *
     * @static
     * @param  string $pPathSource
     * @param  string $pPathTarget
     * @return bool
     */
    public static function copy($pFrom, $pTo)
    {
        //TODO, move the code from adminFilemanager::paste() to here

    }


    /**
     * Moves a file to new destinaton.
     *
     * @static
     * @param  string $pPathSource
     * @param  string $pPathTarget
     * @return bool
     */
    public static function move($pFrom, $pTo)
    {
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
     * @return array   Files array
     */
    public static function search($pFrom, $pTo)
    {
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
    public static function getUrl($pPath)
    {
        $fs = static::getLayer($pPath);
        $url = $fs->getPublicUrl(static::normalizePath($pPath));

        //TODO, check if $url contains http(s)://, and then decide if we need to add it
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0)
            return $url;

        return 'http' . (\Core\Kryn::$ssl?'s':'') . '://'.$url;
    }


    /**
     * Translates the internal id to the real path.
     * Example: getPath(45) => '/myImageFolder/Picture1.png'
     *
     * @static
     * @param  integer|string $pId String for backward compatibility
     * @return string
     */
    public static function getPath($pId)
    {
        if (!is_numeric($pId))
            return PATH_MEDIA.$pId;

        //page bases caching here
        $sql = 'SELECT id, path FROM '.pfx.'system_file WHERE id = '.($pId+0);
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
     *
     * @return int|bool|array Return false if the file doenst exist,
     *                        return 2 if the webserver does not have access
     *                        or return array with the information.
     */
    public static function getTrashFiles()
    {
        if (!static::$checkObject) {
            return false;
        }

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

            $dbItem = dbTableFetch('system_file_log', 1, 'id = ' . ($file+0));

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


    /**
     * @param      $pPath
     * @param      $pResolution
     * @param bool $pQuadratic
     */
    public static function getThumbnail($pPath, $pResolution, $pQuadratic = false)
    {
        $fs = static::getLayer($pPath);

        $content = $fs->getContent(static::normalizePath($pPath));
        $image = imagecreatefromstring($content);

        list($newWidth, $newHeight) = explode('x', $pResolution);
        $thumbWidth = $newWidth;
        $thumbHeight = $newHeight;

        $oriWidth = imagesx($image);
        $oriHeight = imagesy($image);
        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagealphablending($thumbImage, false);

        if ($thumbWidth >= $oriWidth && $thumbHeight > $oriHeight) return $image;

        if ($oriWidth > $oriHeight) {

            $ratio = $thumbHeight / ($oriHeight / 100);
            $_width = ceil($oriWidth * $ratio / 100);

            $top = 0;
            if ($_width < $thumbWidth) {
                $ratio = $_width / ($thumbWidth / 100);
                $nHeight = $thumbHeight * $ratio / 100;
                $top = ($thumbHeight - $nHeight) / 2;
                $_width = $thumbWidth;
            }

            $tempImg = imagecreatetruecolor($_width, $thumbHeight);
            imagealphablending($tempImg, false);
            imagecopyresampled($tempImg, $image, 0, 0, 0, 0, $_width, $thumbHeight, $oriWidth, $oriHeight);
            $_left = ($_width / 2) - ($thumbWidth / 2);

            imagecopyresampled($thumbImage, $tempImg, 0, 0, $_left, 0, $thumbWidth, $thumbHeight, $thumbWidth, $thumbHeight);

        } else {
            $ratio = $thumbWidth / ($oriWidth / 100);
            $_height = ceil($oriHeight * $ratio / 100);
            $tempImg = imagecreatetruecolor($thumbWidth, $_height);
            imagealphablending($tempImg, false);
            imagecopyresampled($tempImg, $image, 0, 0, 0, 0, $thumbWidth, $_height, $oriWidth, $oriHeight);
            $_top = ($_height / 2) - ($thumbHeight / 2);
            imagecopyresampled($thumbImage, $tempImg, 0, 0, 0, $_top, $thumbWidth, $thumbHeight, $thumbWidth, $thumbHeight);
        }

        imagealphablending($thumbImage, false);
        imagesavealpha($thumbImage, true);

        return $thumbImage;

    }

}

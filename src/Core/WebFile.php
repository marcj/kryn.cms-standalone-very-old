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

use Core\File\FileInfo;
use Core\Models\File;
use Core\Models\FileQuery;
use Core\File\FileInfoInterface;

/**
 * File
 *
 * Class to proxy the file functions to the appropriate file layer.
 * Use this class, if you want to modify files inside media/.
 *
 * This class resolves all mount points inside media/.
 *
 */
class WebFile
{
    /**
     * Caches all objects of active file layers (magic folders)
     *
     * @var array
     */
    public static $fsObjects = array();

    /**
     * Whether this class checks against the object 'Core\Model\File' and appends
     * the 'id'.  /* Checks also against permission/acl table. * /
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
     *
     * @param  string $pPath
     *
     * @return \Core\FAL\FALAbstract
     * @throws \ClassNotFoundException
     */
    public static function getLayer($pPath)
    {
        $class = 'Core\FAL\Local';
        $params['root'] = PATH . PATH_WEB;
        $mountName = '';

        if ($pPath != '/') {

            $sPos = strpos(substr($pPath, 1), '/');
            if ($sPos === false) {
                $firstFolder = substr($pPath, 1);
            } else {
                $firstFolder = substr($pPath, 1, $sPos);
            }

            //if firstFolder a mounted folder?
            if (isset(Kryn::$config['mounts']) && $fs = Kryn::$config['mounts'][$firstFolder]) {
                $class = $fs['class'];
                $params = $fs['params'];
                $mountName = $firstFolder;
            }
        }

        if (isset(static::$fsObjects[$class])) {
            return static::$fsObjects[$class];
        }

        if (class_exists($class)) {
            static::$fsObjects[$class] = new $class($mountName, $params);
        } else {
            throw new \ClassNotFoundException(tf('Class %s not found.', $class));
        }

        return static::$fsObjects[$class];

    }

    /**
     * Sets internal `id` of the `File` model.
     *
     * @param FileInfoInterface|FileInfoInterface[] $fileInfo
     *
     * @return FileInfoInterface|FileInfoInterface[]
     */
    public static function wrap($fileInfo)
    {
        return File::wrap($fileInfo);
    }

    /**
     * Removes the name of the mount point from the proper layer.
     * Also removes '..' and replaces '//' => '/'
     *
     * This is needed because the file layer gets the relative path under his own root.
     * Forces a / at the beginning, removes the trailing / if exists.
     *
     * @param  string|array $path
     *
     * @return string
     */
    public static function normalizePath($path)
    {
        if (is_array($path)) {
            $result = [];
            foreach ($path as $p) {
                $result[] = static::normalizePath($p);
            }
            return $result;
        } else {
            $fs = static::getLayer($path);
            $path = substr($path, strlen($fs->getMountPoint()));

            if (strpos($path, '@') === 0) {
                $path = Kryn::resolvePath($path);
            }

            if ('/' !== substr($path, 0, 1)) {
                $path = '/' . $path;
            }
            if ('/' === substr($path, -1)) {
                $path = substr($path, 0, -1);
            }
            $path = str_replace('..', '', $path);
            $path = str_replace('//', '/', $path);

            return $path;
        }
    }

    /**
     * Removes unusual chars in file names.
     *
     * @static
     *
     * @param  string $pName
     *
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
     *
     * @param  string $pPath
     *
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
     *
     * @param  string $pPath
     * @param  string $pContent
     *
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
     *
     * @param  string $pPath
     *
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
     *
     * @param  string $pPath
     *
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
     *
     * @param  string $pPath
     *
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
     *
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
     *
     * @param string $pPath
     *
     * @return \Core\File\FileInfoInterface
     */
    public static function getFile($pPath)
    {
        $fs = static::getLayer($pPath);
        $path = static::normalizePath($pPath);

        $file = $fs->getFile($path);
        return static::wrap($file);
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
     *
     * @param string $pPath
     *
     * @return \Core\File\FileInfoInterface[]
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
        if (!is_array($items)) {
            return $items;
        }

        if (count($items) == 0) {
            return array();
        }

        $items = static::wrap($items);

        if (static::$checkMounts) {

            if ($fs->getMountPoint()) {
                foreach ($items as &$file) {
                    $file->setMountPoint($fs->getMountPoint());
                }
            }

            if ('/' === $pPath) {
                foreach (Kryn::getSystemConfig()->getMountPoints() as $mountPoint) {
                    $fileInfo = new FileInfo();
                    $fileInfo->setPath('/' . $mountPoint->getPath());
                    $fileInfo->setIcon($mountPoint->getIcon());
                    $fileInfo->setType(FileInfo::DIR);
                    $fileInfo->setMountPoint(true);
                    $items[] = $fileInfo;
                }
            }
        }

        usort($items, function($a, $b){
            return strnatcasecmp($a ? $a->getPath() : '', $b ? $b->getPath() : '');
        });

        return $items;
    }

    /**
     * Returns the file count inside $pFolderPath
     *
     * @static
     *
     * @param  string $pFolderPath
     *
     * @return mixed
     */
    public static function getCount($pFolderPath)
    {
        $fs = static::getLayer($pFolderPath);

        return $fs->getCount(static::normalizePath($pFolderPath));
    }

    /**
     * Returns the md5 of the content.
     *
     * @param $path
     *
     * @return string
     */
    public static function getHash($path){
        $fs = static::getLayer($path);
        return $fs->getHash(static::normalizePath($path));
    }

    /**
     * Copies or moves files to a destination.
     * If the source is a folder, it copies recursively.
     *
     * @static
     *
     * @param  array|string $source
     * @param  string       $target
     * @param  string       $action move|copy
     *
     * @return bool
     */
    public static function paste($source, $target, $action = 'move')
    {
        $files = (array)$source;
        $action = strtolower($action);

        foreach ($files as $file) {
            $oldFile = str_replace('..', '', $file);
            $oldFile = str_replace(chr(0), '', $oldFile);

            $oldFs = static::getLayer($oldFile);
            //if the $target is a folder with trailing slash, we move/copy the files _into_ it otherwise we replace.
            $newPath = '/' === substr($target, -1) ? $target . basename($file) : $target;

            $newFs = static::getLayer($newPath);

            if ($newFs === $oldFs) {
                $result = $newFs->$action(static::normalizePath($oldFile), static::normalizePath($newPath));
            } else {
                //we need to move a folder from one file layer to another.
                $file = $oldFs->getFile(static::normalizePath($oldFile));

                if ($file['type'] == 'file') {
                    $content = $oldFs->getContent(static::normalizePath($oldFile));
                    $newFs->setContent(static::normalizePath($newPath), $content);
                } else {
                    if ('' === $oldFs->getMountPoint()) {
                        //just directly upload the stuff
                        static::copyFolder(PATH_WEB . $oldFile, $newPath);
                    } else {
                        //we need to copy all files down to our local hdd temporarily
                        //and upload then
                        $folder = static::downloadFolder($oldFile);
                        static::copyFolder($folder, $newPath);
                        TempFile::remove($folder);
                    }
                }
                if ('move' === $action) {
                    $oldFs->deleteFile(static::normalizePath($oldFile));
                }
            }
        }

        return $result;
    }

    public static function downloadFolder($path, $to = null)
    {
        $fs = self::getFs($path);
        $files = $fs->getFiles(self::normalizePath($path));

        $to = $to ? : Kryn::createTempFolder('', false);

        if (is_array($files)) {
            foreach ($files as $file) {
                if ('file' === $file['type']) {
                    $content = $fs->getContent(self::normalizePath($path . '/' . $file['name']));
                    TempFile::setContent($to . '/' . $file['name'], $content);
                } else {
                    self::downloadFolder($path . '/' . $file['name'], $to . '/' . $file['name']);
                }
            }
        }

        return $to;
    }

    public static function copyFolder($pFrom, $pTo)
    {
        $fs = self::getFs($pTo);
        $fs->createFolder(self::normalizePath($pTo));

        $normalizedPath = self::normalizePath($pTo);

        $files = find($pFrom . '/*');

        $result = true;

        foreach ($files as $file) {
            $newName = $normalizedPath . '/' . substr($file, strlen($pFrom) + 1);

            if (is_dir($file)) {
                $result &= $fs->createFolder(self::normalizePath($newName));
            } else {
                $result &= $fs->createFile(self::normalizePath($newName), kryn::fileRead($file));
            }
        }

        return $result;
    }

    /**
     * Moves a file or files to new destinaton.
     *
     * @static
     *
     * @param  array|string $source
     * @param  string       $target
     *
     * @return bool
     */
    public static function move($source, $target)
    {
        return static::paste($source, $target, 'move');
    }

    /**
     * Copies a file or files to new destinaton.
     *
     * @static
     *
     * @param  array|string $source
     * @param  string       $target
     *
     * @return bool
     */
    public static function copy($source, $target)
    {
        return static::paste($source, $target, 'copy');
    }

    /**
     * Searchs files in a path by a regex pattern.
     *
     * @static
     *
     * @param  string  $path
     * @param  string  $query
     * @param  integer $depth
     *
     * @return FileInfo[]   Files array
     */
    public static function search($path, $query, $depth = 1)
    {
        $fs = static::getLayer($path);
        $files = $fs->search(static::normalizePath($path), $query, $depth);
        return $files;
    }

    /**
     *
     * Returns the public URL of the file $pPath
     * With HTTP or HTTPs, depends on Core\Kryn::$ssl.
     *
     * @static
     *
     * @param  string $pPath
     *
     * @return string
     */
    public static function getUrl($pPath)
    {
        $fs = static::getLayer($pPath);
        $url = $fs->getPublicUrl(static::normalizePath($pPath));

        //TODO, check if $url contains http(s)://, and then decide if we need to add it
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            return $url;
        }

        return 'http' . (\Core\Kryn::$ssl ? 's' : '') . '://' . $url;
    }

    /**
     * Translates the internal id to the real path.
     * Example: getPath(45) => '/myImageFolder/Picture1.png'
     *
     * @static
     *
     * @param  integer|string $pId String for backward compatibility
     *
     * @return string
     */
    public static function getPath($pId)
    {
        if (!is_numeric($pId)) {
            return PATH_WEB . $pId;
        }

        //page bases caching here
        $sql = 'SELECT id, path FROM ' . pfx . 'system_file WHERE id = ' . ($pId + 0);
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
        $h = opendir(PATH_WEB . 'trash/');

        while ($file = readdir($h)) {
            if ($file == '.svn' || $file == '.' || $file == '..') {
                continue;
            }
            $files[] = $file;
        }

        natcasesort($files);

        $res = array();
        foreach ($files as $file) {

            if ($file == '.htaccess') {
                continue;
            }

            $path = '/trash/' . $file;

            $dbItem = dbTableFetch('system_file_log', 1, 'id = ' . ($file + 0));

            $item['name'] = basename($dbItem['path']) . '-v' . $file;
            $item['path'] = str_replace(PATH_WEB, '', $path);
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
     * Resize a image and returns it's object.
     *
     * @param string  $pPath
     * @param integer $pWidth
     * @param int     $pHeight
     *
     * @return \PHPImageWorkshop\Core\ImageWorkshopLayer
     */
    public static function getResizeMax($pPath, $pWidth, $pHeight)
    {

        $content = \Core\WebFile::getContent($pPath);
        if (!$content) {
            return null;
        }

        $image = \PHPImageWorkshop\ImageWorkshop::initFromString($content);

        $width = $image->getWidth();
        $height = $image->getHeight();

        if ($width > $height) {
            $newWidth = $pWidth;
        } else {
            $newHeight = $pHeight;
        }

        $image->resizeInPixel($newWidth, $newHeight, true);

        if ($image->getHeight() > $pHeight) {
            $image->resizeInPixel(null, $pHeight, true);
        }
        if ($image->getWidth() > $pWidth) {
            $image->resizeInPixel($pWidth, null, true);
        }

        return $image;
    }

    /**
     *
     *
     * @param string $pPath
     * @param string $pResolution <width>x<height>
     * @param bool   $pResize
     *
     * @return resource
     */
    public static function getThumbnail($pPath, $pResolution, $pResize = false)
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

        imagealphablending($image, false);
        imagesavealpha($image, true);

        if (!$pResize && $thumbWidth >= $oriWidth && $thumbHeight > $oriHeight) {
            return $image;
        }

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

            imagecopyresampled(
                $thumbImage,
                $tempImg,
                0,
                0,
                $_left,
                0,
                $thumbWidth,
                $thumbHeight,
                $thumbWidth,
                $thumbHeight
            );

        } else {
            $ratio = $thumbWidth / ($oriWidth / 100);
            $_height = ceil($oriHeight * $ratio / 100);
            $tempImg = imagecreatetruecolor($thumbWidth, $_height);
            imagealphablending($tempImg, false);
            imagecopyresampled($tempImg, $image, 0, 0, 0, 0, $thumbWidth, $_height, $oriWidth, $oriHeight);
            $_top = ($_height / 2) - ($thumbHeight / 2);
            imagecopyresampled(
                $thumbImage,
                $tempImg,
                0,
                0,
                0,
                $_top,
                $thumbWidth,
                $thumbHeight,
                $thumbWidth,
                $thumbHeight
            );
        }

        imagealphablending($thumbImage, false);
        imagesavealpha($thumbImage, true);

        return $thumbImage;

    }

}

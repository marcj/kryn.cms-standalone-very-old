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
     * @param  string $path
     *
     * @return \Core\FAL\FALAbstract
     * @throws \ClassNotFoundException
     */
    public static function getLayer($path)
    {
        $class = 'Core\FAL\Local';
        $params['root'] = PATH . PATH_WEB;
        $mountName = '';

        if ($path != '/') {

            $sPos = strpos(substr($path, 1), '/');
            if ($sPos === false) {
                $firstFolder = substr($path, 1);
            } else {
                $firstFolder = substr($path, 1, $sPos);
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
     * @param  string $name
     *
     * @return string
     */
    public static function normalizeName($name)
    {
        $s = array('ä', 'ö', 'ü', 'ß');
        $r = array('ae', 'oe', 'ue', 'ss');
        $name2 = @str_replace($s, $r, $name);
        $name2 = @preg_replace('/[^a-zA-Z0-9\.\_\(\)]/', "-", $name2);
        $name2 = @preg_replace('/--+/', '-', $name2);

        return $name2;
    }

    /**
     * Gets the content of a file.
     *
     * @static
     *
     * @param  string $path
     *
     * @return bool|string
     */
    public static function getContent($path)
    {
        $fs = static::getLayer($path);

        return $fs->getContent(static::normalizePath($path));

    }

    /**
     * Sets the content of a file.
     *
     * Creates the file if not exist. Created also the full folder path if
     * the they doesnt exist.
     *
     * @static
     *
     * @param  string $path
     * @param  string $content
     *
     * @return bool
     */
    public static function setContent($path, $content)
    {
        $fs = static::getLayer($path);

        return $fs->setContent(static::normalizePath($path), $content);

    }

    /**
     * Checks if a file exists.
     *
     * @static
     *
     * @param  string $path
     *
     * @return bool
     */
    public static function exists($path)
    {
        $fs = static::getLayer($path);

        return $fs->fileExists(static::normalizePath($path));

    }

    /**
     * Creates a file with default permissions.
     * Creates also the full folder path if the they doesnt exist.
     *
     * @static
     *
     * @param  string $path
     *
     * @return bool
     */
    public static function createFile($path, $content)
    {
        $fs = static::getLayer($path);

        return $fs->createFile(static::normalizePath($path), $content);

    }

    /**
     * Creates a folder with default permissions.
     * Creates also the full folder path if the they doesnt exist.
     *
     * @static
     *
     * @param  string $path
     *
     * @return bool
     */
    public static function createFolder($path)
    {
        $fs = static::getLayer($path);

        return $fs->createFolder(static::normalizePath($path));

    }

    /**
     * Removes a file/folder.
     *
     * @static
     *
     * @param string $path
     *
     * @return bool
     */
    public static function remove($path)
    {
        $fs = static::getLayer($path);

        return $fs->remove(static::normalizePath($path));

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
     * @param string $path
     *
     * @return \Core\File\FileInfoInterface
     */
    public static function getFile($path)
    {
        $fs = static::getLayer($path);
        $path2 = static::normalizePath($path);

        $file = $fs->getFile($path2);
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
     * @param string $path
     *
     * @return \Core\File\FileInfoInterface[]
     */
    public static function getFiles($path)
    {
        //$access = krynAcl::check(3, $path, 'read', true);
        //if (!$access) return false;
        $fs = static::getLayer($path);
        $path2 = static::normalizePath($path);

        if ($path == '/trash') {
            return static::getTrashFiles();
        }

        $items = $fs->getFiles(static::normalizePath($path));
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

            if ('/' === $path) {
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
     * Returns the file count inside $folderPath
     *
     * @static
     *
     * @param  string $folderPath
     *
     * @return mixed
     */
    public static function getCount($folderPath)
    {
        $fs = static::getLayer($folderPath);

        return $fs->getCount(static::normalizePath($folderPath));
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

    public static function copyFolder($from, $to)
    {
        $fs = self::getFs($to);
        $fs->createFolder(self::normalizePath($to));

        $normalizedPath = self::normalizePath($to);

        $files = find($from . '/*');

        $result = true;

        foreach ($files as $file) {
            $newName = $normalizedPath . '/' . substr($file, strlen($from) + 1);

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
     * Returns the public URL of the file $path
     * With HTTP or HTTPs, depends on Core\Kryn::$ssl.
     *
     * @static
     *
     * @param  string $path
     *
     * @return string
     */
    public static function getUrl($path)
    {
        $fs = static::getLayer($path);
        $url = $fs->getPublicUrl(static::normalizePath($path));

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
     * @param  integer|string $id String for backward compatibility
     *
     * @return string
     */
    public static function getPath($id)
    {
        if (!is_numeric($id)) {
            return PATH_WEB . $id;
        }

        //page bases caching here
        $sql = 'SELECT id, path FROM ' . pfx . 'system_file WHERE id = ' . ($id + 0);
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
     * @param string  $path
     * @param integer $width
     * @param int     $height
     *
     * @return \PHPImageWorkshop\Core\ImageWorkshopLayer
     */
    public static function getResizeMax($path, $width, $height)
    {

        $content = \Core\WebFile::getContent($path);
        if (!$content) {
            return null;
        }

        $image = \PHPImageWorkshop\ImageWorkshop::initFromString($content);

        $width2 = $image->getWidth();
        $height2 = $image->getHeight();

        if ($width2 > $height2) {
            $newWidth = $width;
        } else {
            $newHeight = $height;
        }

        $image->resizeInPixel($newWidth, $newHeight, true);

        if ($image->getHeight() > $height) {
            $image->resizeInPixel(null, $height, true);
        }
        if ($image->getWidth() > $width) {
            $image->resizeInPixel($width, null, true);
        }

        return $image;
    }

    /**
     *
     *
     * @param string $path
     * @param string $resolution <width>x<height>
     * @param bool   $resize
     *
     * @return resource
     */
    public static function getThumbnail($path, $resolution, $resize = false)
    {
        $fs = static::getLayer($path);

        $content = $fs->getContent(static::normalizePath($path));
        $image = imagecreatefromstring($content);

        list($newWidth, $newHeight) = explode('x', $resolution);
        $thumbWidth = $newWidth;
        $thumbHeight = $newHeight;

        $oriWidth = imagesx($image);
        $oriHeight = imagesy($image);
        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        imagealphablending($thumbImage, false);

        imagealphablending($image, false);
        imagesavealpha($image, true);

        if (!$resize && $thumbWidth >= $oriWidth && $thumbHeight > $oriHeight) {
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

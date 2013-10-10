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
use Core\File\FileInfoInterface;
use Core\Models\File;
use Core\File\FileInfo;

/**
 * SystemFile
 *
 * Class to proxy the file functions to the local file layer on temp folder.
 * Use this class, if you want to modify files of the webserver's temp folder.
 *
 * Does not support external mount points.
 *
 */
class TempFile extends WebFile
{
    public static $fsObjects = array();

    //not permission check, since file object is only for media/ folder, so for File:: class.
    public static $checkObject = false;


    //we do not support mounts outside of web/.
    public static $checkMounts = false;

    /**
     *
     * Returns the instance of the local file layer.
     *
     * @static
     *
     * @param  string $path
     *
     * @return object
     */
    public static function getLayer($path = null)
    {
        $class = '\Core\FAL\Local';
        $params['root'] = Kryn::getTempFolder();

        if (isset(static::$fsObjects[$class])) {
            return static::$fsObjects[$class];
        }

        static::$fsObjects[$class] = new $class('', $params);

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
        return $fileInfo;
    }

    /**
     * @param string $path
     * @return FileInfoInterface
     */
    public static function getFile($path)
    {
        return parent::getFile($path);
    }

    /**
     * @param string $path
     * @return FileInfoInterface[]
     */
    public static function getFiles($path)
    {
        return parent::getFiles($path);
    }


    public static function getPath($path)
    {
        throw new \Exception(t('getPath on TempFile is not possible. Use Core\WebFile::getPath'));
    }

    public static function getUrl($path)
    {
        throw new \Exception(t('getUrl on TempFile is not possible. Use Core\WebFile::getUrl'));
    }

    public static function getTrashFiles()
    {
        throw new \Exception(t('getTrashFiles on TempFile is not possible. Use Core\WebFile::getTrashFiles'));
    }

}

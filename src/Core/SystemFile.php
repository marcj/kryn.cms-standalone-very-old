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
 * SystemFile
 *
 * Class to proxy the file functions to the local file layer on root.
 * Use this class, if you want to modify files outside of media/.
 *
 * Does not support external mount points.
 *
 */
class SystemFile extends WebFile
{
    public static $fsObjects = array();

    //not permission check, since file object is only for media/ folder, so for WebFile:: class.
    public static $checkObject = false;

    //we do not support mounts outside of media/.
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

        $params['root'] = './';

        if (static::$fsObjects[$class]) {
            return static::$fsObjects[$class];
        }

        static::$fsObjects[$class] = new $class('', $params);

        return static::$fsObjects[$class];
    }

    public static function setPermission($path)
    {
        self::getLayer()->setPermission($path);
    }

    public static function wrap($fileInfo)
    {
        return $fileInfo;
    }

    public static function getPath($path)
    {
        throw new \Exception(t('getPath on SystemFile is not possible. Use Core\File::getPath'));
    }

    public static function getUrl($path)
    {
        throw new \Exception(t('getUrl on SystemFile is not possible. Use Core\File::getUrl'));
    }

    public static function getTrashFiles()
    {
        throw new \Exception(t('getTrashFiles on SystemFile is not possible. Use Core\File::getTrashFiles'));
    }

}

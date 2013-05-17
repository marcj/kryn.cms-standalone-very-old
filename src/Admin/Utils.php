<?php

namespace Admin;

use Core\Config\EntryPoint;
use Core\Kryn;

class Utils
{
    public static function clearCache()
    {
        \Core\TempFile::remove('cache-object');
        \Core\TempFile::remove('smarty-compile');

        \Core\WebFile::remove('cache');
        \Core\WebFile::createFolder('cache');

        foreach (Kryn::$bundles as $bundle) {
            self::clearModuleCache($bundle);
        }

        return true;
    }

    public static function clearModuleCache($bundleName)
    {
        $config = Kryn::getBundle($bundleName);

        if ($config) {
            Kryn::invalidateCache(strtolower($config->getName(true)));
        }
    }

    /**
     * Gets the item from the administration entry points defined in the config.json, by the given code.
     *
     * @param string  $code <bundleName>/news/foo/bar/edit
     * @param boolean $withChildren
     *
     * @return EntryPoint
     */
    public static function getEntryPoint($code, $withChildren = false)
    {
        if (substr($code, 0, 1) == '/') {
            $code = substr($code, 1);
        }

        $bundleName = $code;
        if (false !== ($pos = strpos($code, '/'))) {
            $bundleName = substr($code, 0, strpos($code, '/'));
            $path = substr($code, strpos($code, '/') + 1);
        }

        $bundleName = ucfirst($bundleName) . 'Bundle';
        $config = Kryn::getConfig($bundleName);

        if ($config) {

            while (!($entryPoint = $config->getEntryPoint($path))) {
                if (false === strpos($path, '/')) {
                    break;
                }
                $path = substr($path, 0, strrpos($path, '/'));
            };
        }

        return $entryPoint;
    }
}

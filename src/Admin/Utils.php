<?php

namespace Admin;

use Core\Kryn;

use Core\Config\EntryPoint;

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

    public static function clearModuleCache($pName)
    {
        $config = Kryn::getConfig($pName);

        Kryn::invalidateCache($pName);
        if (!$config) return false;

        //TODO,
//        if ($config->getCaches()) {
//            foreach ($config->getCaches() as $cache) {
//                if ($m = $cache['method']) {
//                    if (method_exists(Kryn::$modules[$pName], $m))
//                        Kryn::$modules[$pName]->$m();
//                } else {
//                    Kryn::deleteCache($cache['key']);
//                }
//            }
//        }
//        if ($config->getCacheInvalidation()) {
//            foreach ($config['cacheInvalidation'] as $cache) {
//                Kryn::invalidateCache($cache['key']);
//            }
//        }

        return true;
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
            $path       = substr($code, strpos($code, '/') + 1);
        }

        $bundleName = ucfirst($bundleName) . 'Bundle';
        $config     = Kryn::getConfig($bundleName);

        if ($config) {
            $entryPoint = $config->getEntryPoint($path);
        }

        return $entryPoint;
    }
}

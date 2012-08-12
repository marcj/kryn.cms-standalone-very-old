<?php

namespace Admin;

use Core\Kryn;

class Utils {

	public static function clearCache(){

        clearfolder('cache/object/');
        clearfolder(PATH_PUBLIC_CACHE);

        foreach (Kryn::$configs as $extKey => $config){
            if ($config['caches']){
                foreach ($config['caches'] as $cache){
                    if ($m = $cache['method']){
                        if (method_exists(Kryn::$modules[$extKey], $m))
                            try {
                                Kryn::$modules[$extKey]->$m();
                            } catch (Exception $e){
                                klog('admin', 'Error during the clearCache function: '.$e);
                            }
                    } else {
                        Kryn::deleteCache($cache['key']);
                    }
                }
            }
            if ($config['cacheInvalidation']){
                foreach ($config['cacheInvalidation'] as $cache){
                    Kryn::invalidateCache($cache['key']);
                }
            }
        }


        return true;
	}
}
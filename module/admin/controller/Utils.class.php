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

    
    /**
     *
     * Gets the item from the 'admin' entry points defined in the config.json, by the given code
     *
     * @static
     * @param $pCode <extKey>/news/foo/bar/edit
     * @return array|bool
     */
    public static function getPathItem($pCode) {

        $codes = explode('/', $pCode);

        if (Kryn::$configs['admin']['admin'][$codes[1]]) {
            //inside admin extension
            $adminInfo = Kryn::$configs['admin']['admin'];
            $start = 1;
            $module = 'admin';
            $code = substr($pCode, 6);
        } else if (Kryn::$configs[$codes[1]]['admin']) {
            //inside other extension
            $adminInfo = Kryn::$configs[$codes[1]]['admin'];
            $start = 2;
            $module = $codes[1];
            $code = substr($pCode, 6 + strlen($codes[1]) + 1);
        }

        $_info = $adminInfo[$codes[$start]];
        $path = array();
        $path[] = $_info['title'];

        $count = count($codes);
        for ($i = $start + 1; $i <= $count; $i++) {
            if ($codes[$i] != "") {
                $_info = $_info['childs'][$codes[$i]];
                $path[] = $_info['title'];
            }
        }

        unset($path[count($path) - 1]);
        unset($_info['childs']);

        if (!$_info) {
            return false;
        }

        $_info['_path'] = $path;
        $_info['_module'] = $module;
        $_info['_code'] = $code;

        if ($code) {
            $css = PATH . PATH_MEDIA . $module . '/' . (($module != 'admin') ? 'admin/' : '') . 'css/' .
                   str_replace('/', '_', $code) . '.css';
            if (file_exists($css) && $mtime = filemtime($css)) {
                $_info['cssmdate'] = $mtime;
            }
        }


        return $_info;
    }
}
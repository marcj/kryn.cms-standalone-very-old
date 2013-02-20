<?php

namespace Admin;

use Core\Kryn;

class Utils
{
    public static function clearCache()
    {
        \Core\TempFile::remove('cache-object');
        \Core\TempFile::remove('smarty-compile');

        \Core\MediaFile::remove('cache');
        \Core\MediaFile::createFolder('cache');

        foreach (Kryn::$configs as $extKey => $config) {
            self::clearModuleCache($extKey);
        }

        return true;
    }

    public static function clearModuleCache($pName)
    {
        $config = Kryn::$configs[$pName];

        Kryn::invalidateCache($pName);
        if (!$config) return false;

        if ($config['caches']) {
            foreach ($config['caches'] as $cache) {
                if ($m = $cache['method']) {
                    if (method_exists(Kryn::$modules[$pName], $m))
                        Kryn::$modules[$pName]->$m();
                } else {
                    Kryn::deleteCache($cache['key']);
                }
            }
        }
        if ($config['cacheInvalidation']) {
            foreach ($config['cacheInvalidation'] as $cache) {
                Kryn::invalidateCache($cache['key']);
            }
        }

        return true;
    }

    public static function loadCss()
    {
        header('Content-Type: text/css');

        $from = array(
            "-moz-border-radius-topleft",
            "-moz-border-radius-topright",
            "-moz-border-radius-bottomleft",
            "-moz-border-radius-bottomright",
            "-moz-border-radius",
        );

        $toSafari = array(
            "-webkit-border-top-left-radius",
            "-webkit-border-top-right-radius",
            "-webkit-border-bottom-left-radius",
            "-webkit-border-bottom-right-radius",
            "-webkit-border-radius",
        );
        $toCss3 = array(
            "border-top-left-radius",
            "border-top-right-radius",
            "border-bottom-left-radius",
            "border-bottom-right-radius",
            "border-radius",
        );

        $md5Hash = '';
        $cssFiles = array();

        foreach (Kryn::$configs as &$config) {
            if ($config['adminCss'])
                self::collectFiles($config['adminCss'], $cssFiles);
        }

        foreach ($cssFiles as $cssFile)
            $md5Hash .= filemtime($cssFile) . '.';

        $md5Hash = md5($md5Hash);

        print "/* Kryn.cms combined admin css file: $md5Hash */\n\n";

        if (file_exists('cache/media/cachedAdminCss_' . $md5Hash . '.css')) {
            readFile('cache/media/cachedAdminCss_' . $md5Hash . '.css');
        } else {
            $content = '';
            foreach ($cssFiles as $cssFile) {
                $content .= "\n\n/* file: $cssFile */\n\n";

                $dir = '../../'.dirname($cssFile).'/';
                $h = fopen($cssFile, "r");
                if ($h) {
                    while (!feof($h) && $h) {
                        $buffer = fgets($h, 4096);

                        $buffer = preg_replace('/url\(\'([^\/].*)\'\)/', 'url(\''.$dir.'$1\')', $buffer);
                        $buffer = preg_replace('/url\(([^\/\'].*)\)/', 'url('.$dir.'$1)', $buffer);

                        $content .= $buffer;
                        $newLine = str_replace($from, $toSafari, $buffer);
                        if ($newLine != $buffer)
                            $content .= $newLine;
                        $newLine = str_replace($from, $toCss3, $buffer);
                        if ($newLine != $buffer)
                            $content .= $newLine;
                    }
                    fclose($h);
                }
            }

            foreach (glob('cache/media/cachedAdminCss_*.css') as $cache)
                @unlink($cache);

            file_put_contents('cache/media/cachedAdminCss_' . $md5Hash . '.css', $content);
            print $content;
        }
        exit;
    }

    public static function collectFiles($pArray, &$pFiles)
    {
        foreach ($pArray as $jsFile) {
            if (strpos($jsFile, '*') !== -1) {
                $folderFiles = find(PATH_MEDIA . $jsFile, false);
                foreach ($folderFiles as $file) {
                    if (!array_search($file, $pFiles))
                        $pFiles[] = $file;
                }
            } else {
                if (file_exists(PATH_MEDIA . $jsFile))
                    $pFiles[] = PATH_MEDIA . $jsFile;
            }
        }

    }

    /**
     *
     * Gets the item from the administration entry points defined in the config.json, by the given code.
     *
     *
     * @static
     * @param $pCode <extKey>/news/foo/bar/edit
     * @return array|bool
     */
    public static function getEntryPoint($pCode, $pWithChildren = false)
    {
        $codes = explode('/', $pCode);
        $actualCode = array();

        if (Kryn::$configs['admin']['entryPoints'][$codes[0]]) {
            //inside admin extension
            $entryPoint = Kryn::$configs['admin']['entryPoints'][$codes[0]];
            $module = 'admin';
            $actualCode[] = $module;
            $actualCode[] = $codes[0];

        } elseif (Kryn::$configs[$codes[0]]) {

            //inside other extension
            $adminInfo = Kryn::$configs[$codes[0]]['entryPoints'];
            $code = substr($pCode, strlen($codes[0]) + 1);
            $module = array_shift($codes);
            $actualCode[] = $module;

            $entryPoint = array('type' => -1, 'title' => Kryn::$configs[$module]['title'],
                                'children' => Kryn::$configs[$module]['entryPoints']);
        }

        $path = array();
        $path[] = $entryPoint['title'];

        if ($entryPoint && $entryPoint['children']) {

            foreach ($codes as $c) {
                if ($entryPoint['children'][$c]) {
                    $actualCode[] = $c;
                    $entryPoint = $entryPoint['children'][$c];
                    $path[] = $entryPoint['title'];
                }
            }
        }

        unset($path[count($path) - 1]);

        if (!$pWithChildren)
            unset($entryPoint['children']);

        if (!$entryPoint) {
            return false;
        }

        $entryPoint['_path'] = $path;
        $entryPoint['_module'] = $module;
        $entryPoint['_code'] = $code;
        $entryPoint['_url'] = implode('/', $actualCode);

        if ($code) {
            $css = PATH . PATH_MEDIA . $module . '/' . (($module != 'admin') ? 'admin/' : '') . 'css/' .
                   str_replace('/', '_', $code) . '.css';
            if (file_exists($css) && $mtime = filemtime($css)) {
                $_info['cssmdate'] = $mtime;
            }
        }

        return $entryPoint;
    }
}

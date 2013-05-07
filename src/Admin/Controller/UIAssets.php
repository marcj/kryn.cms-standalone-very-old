<?php

namespace Admin\Controller;

use Core\Kryn;
use Core\Lang;
use Core\SystemFile;

use Core\Models\LanguageQuery;

class UIAssets
{
    public function getPossibleLangs()
    {
        $languages = LanguageQuery::create()
            ->select(array('code', 'title', 'langtitle'))
            ->filterByVisible(true)
            ->orderByCode()
            ->find()
            ->toArray('code');

        if (0 === count($languages)) {
            $json = '{"en":{"code":"en","title":"English","langtitle":"English"}}';
        } else {
            $json = json_encode($languages);
        }

        header('Content-Type: text/javascript');
        print "window.ka = window.ka || {}; ka.possibleLangs = " . $json;
        exit;
    }

    public function getLanguagePluralForm($lang)
    {
        $lang = preg_replace('/[^a-z]/', '', $lang);
        $file = Lang::getPluralJsFunctionFile($lang); //just make sure the file has been created
        header('Content-Type: text/javascript');
        echo file_get_contents(PATH_WEB . $file);
        exit;
    }

    public function getLanguage($pLang)
    {
        $lang = esc($pLang, 2);

        if (!Kryn::isValidLanguage($lang))
            $lang = 'en';

        Kryn::getAdminClient()->getSession()->setLanguage($lang);
        Kryn::getAdminClient()->syncStore();

        Kryn::loadLanguage($lang);

        if (getArgv('javascript') == 1) {
            header('Content-Type: text/javascript');
            print "if( typeof(ka)=='undefined') window.ka = {}; ka.lang = " . json_encode(Kryn::$lang);
            print "\nLocale.define('en-US', 'Date', " . Kryn::getInstance()->renderView('@AdminBundle/mootools-locale.tpl') . ");";
            exit;
        } else {
            Kryn::$lang['mootools'] = json_decode(Kryn::getInstance()->renderView('@AdminBundle/mootools-locale.tpl'), true);

            return Kryn::$lang;
        }
    }

    public static function loadCss()
    {
        header('Content-Type: text/css');

        $toGecko = array(
            "-moz-border-radius-topleft",
            "-moz-border-radius-topright",
            "-moz-border-radius-bottomleft",
            "-moz-border-radius-bottomright",
            "-moz-border-radius",
        );

        $toWebkit = array(
            "-webkit-border-top-left-radius",
            "-webkit-border-top-right-radius",
            "-webkit-border-bottom-left-radius",
            "-webkit-border-bottom-right-radius",
            "-webkit-border-radius",
        );
        $from = array(
            "border-top-left-radius",
            "border-top-right-radius",
            "border-bottom-left-radius",
            "border-bottom-right-radius",
            "border-radius",
        );

        $md5Hash = '';
        $cssFiles = array();

        foreach (Kryn::$configs as $bundleConfig) {
            foreach ($bundleConfig->getAdminAssetsPaths(false, '.*\.css', true) as $assetPath) {
                $cssFiles[$assetPath] = Kryn::resolvePath($assetPath, 'Resources/public');
            }
        }

        foreach ($cssFiles as $cssFile)
            $md5Hash .= filemtime($cssFile) . '.';

        $md5Hash = md5($md5Hash);

        print "/* Kryn.cms combined admin css file: $md5Hash */\n\n";

        $cacheDir  = PATH_WEB_CACHE . '';
        $cacheFile = $cacheDir.'admin.cachedCss_' . $md5Hash . '.css';

        if (false && file_exists($cacheFile)) {
            readFile($cacheFile);
        } else {
            $content = '';
            foreach ($cssFiles as $assetPath => $cssFile) {
                $content .= "\n\n/* file: $assetPath */\n\n";

                $dir = '../../'.substr(dirname($cssFile),4).'/';
                $h = fopen($cssFile, "r");
                if ($h) {
                    while (!feof($h) && $h) {
                        $buffer = fgets($h, 4096);

                        $buffer = preg_replace('/url\(\'([^\/].*)\'\)/', 'url(\''.$dir.'$1\')', $buffer);
                        $buffer = preg_replace('/url\((?!data:image)([^\/\'].*)\)/', 'url('.$dir.'$1)', $buffer);

                        $content .= $buffer;
                        $newLine = str_replace($from, $toWebkit, $buffer);
                        if ($newLine != $buffer)
                            $content .= $newLine;
                        $newLine = str_replace($from, $toGecko, $buffer);
                        if ($newLine != $buffer)
                            $content .= $newLine;
                    }
                    fclose($h);
                }
            }

            foreach (glob($cacheDir . 'admin.cachedCss_*.css') as $cache)
                @unlink($cache);

            file_put_contents($cacheFile, $content);
            print $content;
        }
        exit;
    }

    public static function collectFiles($pArray, &$pFiles)
    {
        foreach ($pArray as $jsFile) {
            if (strpos($jsFile, '*') !== -1) {
                $folderFiles = find(PATH_WEB . $jsFile, false);
                foreach ($folderFiles as $file) {
                    if (!array_search($file, $pFiles))
                        $pFiles[] = $file;
                }
            } else {
                if (file_exists($jsFile))
                    $pFiles[] = $jsFile;
            }
        }

    }
}

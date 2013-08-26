<?php

namespace Admin\Controller;

use Core\Kryn;
use Core\Lang;
use Core\Models\LanguageQuery;
use Propel\Runtime\Map\TableMap;

class UIAssets
{
    public function getPossibleLangs()
    {
        $languages = LanguageQuery::create()
            ->filterByVisible(true)
            ->orderByCode()
            ->find()
            ->toArray('Code', null, TableMap::TYPE_STUDLYPHPNAME);

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

    public function getLanguage($lang)
    {
        $lang2 = esc($lang, 2);

        if (!Kryn::isValidLanguage($lang2)) {
            $lang2 = 'en';
        }

        Kryn::getAdminClient()->getSession()->setLanguage($lang2);
        Kryn::getAdminClient()->syncStore();

        Kryn::loadLanguage($lang2);

        if (getArgv('javascript') == 1) {
            header('Content-Type: text/javascript');
            print "if( typeof(ka)=='undefined') window.ka = {}; ka.lang = " . json_encode(Kryn::$lang2);
            print "\nLocale.define('en-US', 'Date', " . Kryn::getInstance()->renderView(
                '@AdminBundle/mootools-locale.tpl'
            ) . ");";
            exit;
        } else {
            Kryn::$lang2['mootools'] = json_decode(
                Kryn::getInstance()->renderView('@AdminBundle/mootools-locale.tpl'),
                true
            );

            return Kryn::$lang2;
        }
    }

    public static function collectFiles($array, &$files)
    {
        foreach ($array as $jsFile) {
            if (strpos($jsFile, '*') !== -1) {
                $folderFiles = find(PATH_WEB . $jsFile, false);
                foreach ($folderFiles as $file) {
                    if (!array_search($file, $files)) {
                        $files[] = $file;
                    }
                }
            } else {
                if (file_exists($jsFile)) {
                    $files[] = $jsFile;
                }
            }
        }

    }
}

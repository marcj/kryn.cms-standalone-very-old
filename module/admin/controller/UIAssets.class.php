<?php

namespace Admin;

use Core\Kryn;

class UIAssets {


    public function getPossibleLangs() {

        $files = Kryn::readFolder(PATH_MODULE . 'admin/lang/', false);
        $where = "code = 'en' ";
        foreach ($files as $file)
            $where .= " OR code = '$file'";
        $langs = dbExFetchAll("SELECT * FROM ".pfx."system_langs WHERE $where");

        $json = json_encode($langs);
        header('Content-Type: text/javascript');
        print "if( typeof(ka)=='undefined') window.ka = {}; ka.possibleLangs = " . $json;
        exit;
    }


    public function getLanguagePluralForm($pLang){

        $lang = esc($pLang, 2);
        header('Content-Type: text/javascript');
        print "/* Kryn plural function */\n";
        readFile(PATH_MEDIA_CACHE.'gettext_plural_fn_'.$lang.'.js');
        print "\n";
        exit;
    }

    public function getLanguage($pLang) {

        $lang = esc($pLang, 2);

        if (!Kryn::isValidLanguage($lang))
            $lang = 'en';

        Kryn::$adminClient->getSession()->setLanguage($lang);
        Kryn::$adminClient->syncStore();

        Kryn::loadLanguage($lang);

        if (getArgv('javascript') == 1) {
            header('Content-Type: text/javascript');
            print "if( typeof(ka)=='undefined') window.ka = {}; ka.lang = " . json_encode(Kryn::$lang);
            if (!$json) {
                print "\nLocale.define('en-US', 'Date', " . tFetch('admin/mootools-locale.tpl') . ");";
            }
            exit;
        } else {
            Kryn::$lang['mootools'] = json_decode(tFetch('admin/mootools-locale.tpl'), true);
            return Kryn::$lang;
        }
    }
}
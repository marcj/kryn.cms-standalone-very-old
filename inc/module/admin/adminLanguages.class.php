<?php

class adminLanguages {


    public function init() {
        switch (getArgv(4)) {
            case 'getAllLanguages':
                json(self::getLanguageOverview(getArgv('lang')));
            case 'overviewExtract':
                json(self::getOverviewExtract(getArgv('module'), getArgv('lang')));
            case 'saveAllLanguages':
                json(self::saveAllLanguages());
        }
    }

    public function getOverviewExtract( $pModule, $pLang ){

        if( !$pModule || !$pLang) return array();

        $extract = krynLanguage::extractLanguage( $pModule );
        $translated = krynLanguage::getLanguage( $pModule, $pLang );

        $p100 = count($extract);
        $cTranslated = 0;

        foreach( $extract as $id => $translation ){
            if( $translated['translations'][$id] && $translated['translations'][$id] != '' ) $cTranslated++;
        }

        return array(
            'count' => $p100,
            'countTranslated' => $cTranslated
        );

    }

    public function saveAllLanguages() {
        $lang = getArgv('lang', 2);

        $langs = json_decode(getArgv('langs'), true);
        foreach ($langs as $key => &$mylangs) {
            if (count($mylangs) > 0) {
                adminModule::saveLanguage($key, $lang, $mylangs);
            }
        }
    }

    public function getAllLanguages($pLang = 'en') {

        if ($pLang == '') $pLang = 'en';

        $res = array();
        foreach (kryn::$configs as $key => $mod) {

            $res[$key]['config'] = $mod;
            $res[$key]['lang'] = krynLanguage::extractLanguage($key);

            if (count($res[$key]['lang']) > 0) {
                $translate = krynLanguage::getLanguage($key, $pLang);
                foreach ($res[$key]['lang'] as $key => &$lang) {
                    if ($translate[$key] != '')
                        $lang = $translate[$key];
                    else
                        $lang = '';
                }
            }
        }

        return $res;

    }

}

?>

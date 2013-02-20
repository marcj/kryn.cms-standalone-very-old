<?php

namespace Admin;

class Languages {

    public function __construct($pRestServer){

        $pRestServer
            //->addGetRoute('all-languages', 'getLanguageOverview')
            ->addGetRoute('overview', 'getOverviewExtract');

    }

    public function getOverviewExtract($pModule, $pLang){

        if( !$pModule || !$pLang) return array();

        $extract = \Core\Lang::extractLanguage( $pModule );
        $translated = \Core\Lang::getLanguage( $pModule, $pLang );

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

    public function getAllLanguages($pLang = 'en') {

        if ($pLang == '') $pLang = 'en';

        $res = array();
        foreach (kryn::$configs as $key => $mod) {

            $res[$key]['config'] = $mod;
            $res[$key]['lang'] = \Core\Lang::extractLanguage($key);

            if (count($res[$key]['lang']) > 0) {
                $translate = \Core\Lang::getLanguage($key, $pLang);
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

<?php

namespace Admin\Controller;

class Languages
{
    public function __construct($restServer)
    {
        $restServer
            //->addGetRoute('all-languages', 'getLanguageOverview')
            ->addGetRoute('overview', 'getOverviewExtract');

    }

    public function getOverviewExtract($module, $lang)
    {
        if (!$module || !$lang) {
            return array();
        }

        $extract = \Core\Lang::extractLanguage($module);
        $translated = \Core\Lang::getLanguage($module, $lang);

        $p100 = count($extract);
        $cTranslated = 0;

        foreach ($extract as $id => $translation) {
            if ($translated['translations'][$id] && $translated['translations'][$id] != '') {
                $cTranslated++;
            }
        }

        return array(
            'count' => $p100,
            'countTranslated' => $cTranslated
        );

    }

    public function getAllLanguages($lang = 'en')
    {
        if ($lang == '') {
            $lang = 'en';
        }

        $res = array();
        foreach (kryn::$configs as $key => $mod) {

            $res[$key]['config'] = $mod;
            $res[$key]['lang'] = \Core\Lang::extractLanguage($key);

            if (count($res[$key]['lang']) > 0) {
                $translate = \Core\Lang::getLanguage($key, $lang);
                foreach ($res[$key]['lang'] as $key => &$lang2) {
                    if ($translate[$key] != '') {
                        $lang2 = $translate[$key];
                    } else {
                        $lang2 = '';
                    }
                }
            }
        }

        return $res;

    }

}

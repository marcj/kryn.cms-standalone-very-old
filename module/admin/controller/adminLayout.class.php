<?php

class adminLayout {

    function init() {
        switch (getArgv(4)) {
            case 'get':
                return self::get(getArgv('name'), getArgv('plain'));

            case 'load':
                return json(self::load(getArgv('type')));

            case 'save':
                return json(self::save(getArgv('file')));

            case 'loadFile':
                return json(self::loadFile(getArgv('file')));
        }
    }

    public static function save($pFile) {
        $file = str_replace("..", "", $pFile);
        kryn::fileWrite(PATH_MEDIA.$file, getArgv('content'));
        return true;
    }

    public static function loadFile($pFile) {

        $res = array();
        foreach (kryn::$configs as $config) {
            if ($config['themes']) {
                foreach ($config['themes'] as $themeTitle => $theme) {
                    foreach ($theme as $typeId => $typeItems) {
                        foreach ($typeItems as $title => $layout) {
                            if ($layout == $pFile) {
                                $res['title'] = $title;
                                $res['path'] = $layout;
                                $res['content'] = kryn::fileRead(PATH_MEDIA . $layout);
                            }
                        }
                    }
                }
            }
        }
        return $res;
    }


    public static function load($pType) {

        $res = array();
        foreach (kryn::$configs as $config) {
            if ($config['themes']) {
                foreach ($config['themes'] as $themeTitle => $theme) {
                    if ($theme[$pType]) {
                        $res[$themeTitle] = $theme[$pType];
                    }
                }
            }
        }
        return $res;
    }

    public static function get($pFile, $pPlain = false) {

        $id = getArgv('id') + 0;
        $page = dbTableFetch('system_page', 1, "id = $id");
        kryn::$current_page = $page;
        kryn::$page = $page;
        tAssign('page', $page);
        $domain = dbTableFetch('system_domains', 1, "id = " . $page['domain_id']);
        kryn::$domain = $domain;
        kryn::loadBreadcrumb();

        if ($domain['publicproperties'] && !is_array($domain['publicproperties'])) {
            kryn::$themeProperties = @json_decode($domain['publicproperties'], true);
        }

        if ($domain['themeproperties'] && !is_array($domain['themeproperties'])) {
            kryn::$themeProperties = @json_decode($domain['themeproperties'], true);
        }

        foreach (kryn::$configs as $extKey => &$mod) {
            if ($mod['themes']) {
                foreach ($mod['themes'] as $tKey => &$theme) {
                    if ($theme['layouts']) {
                        foreach ($theme['layouts'] as $lKey => &$layout) {
                            if ($layout == $page['layout']) {
                                if (is_array(kryn::$themeProperties)) {
                                    kryn::$themeProperties = kryn::$themeProperties[$extKey][$tKey];
                                    kryn::$publicProperties =& kryn::$themeProperties;
                                }
                            }
                        }
                    }
                }
            }
        }

        tAssign('themeProperties', kryn::$themeProperties);
        tAssign('publicProperties', kryn::$themeProperties);

        $pFile = str_replace("..", "", $pFile);
        if ($pFile != '')
            $res['tpl'] = tFetch($pFile);

        if ($res['tpl']) {
            $res['tpl'] = preg_replace('/\{krynContent ([^\}]*)\}/', '<div>{krynContent $1}</div>', $res['tpl']);
            $res['tpl'] = preg_replace('/\{slot ([^\}]*)\}/', '<div>{slot $1}</div>', $res['tpl']);

        }

        $css = array();
        foreach (kryn::$cssFiles as $file => $v) {
            if (file_exists(PATH . PATH_MEDIA . $file) && $mtime = @filemtime(PATH . PATH_MEDIA . $file))
                $css[] = $file . '?modified=' . $mtime;
        }
        $res['css'] = $css;
        json($res);
    }

}

?>

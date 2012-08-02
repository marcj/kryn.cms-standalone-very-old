<?php


namespace Admin;

use \Core\Kryn;

class Backend {


    public static function getDesktop() {

        if ($desktop = Kryn::$adminClient->getUser()->getDesktop())
            return $desktop->toArray();
        else return false;
    }

    public static function saveDesktop($pIcons) {

        $properties = new \Core\Properties($pIcons);
        Kryn::$adminClient->getUser()->setDesktop($properties);
        Kryn::$adminClient->getUser()->save();

        return true;
    }

    public static function getWidgets() {

        if ($widgets = Kryn::$adminClient->getUser()->getWidgets())
            return $widgets->toArray();
        else return false;

    }

    public static function saveWidgets($pWidgets) {

        $properties = new \Core\Properties($pWidgets);
        Kryn::$adminClient->getUser()->setWidgets($properties);
        Kryn::$adminClient->getUser()->save();

        return true;
    }


    public static function saveUserSettings($pSettings) {

        $properties = new \Core\Properties($pSettings);

        Kryn::$adminClient->getUser()->setSettings($properties);
        Kryn::$adminClient->getUser()->save();

        return true;
    }


    public function getCustomJs() {

        $module = getArgv('module', 2);
        $code = getArgv('code', 2);

        if ($module == 'admin')
            $file = "media/admin/js/$code.js";
        else
            $file = "media/$module/admin/js/$code.js";

        header('Content-Type: text/javascript');

        if (!file_exists($file)) {
            $content = "contentCantLoaded_" . getArgv('onLoad', 2) . "('$file');\n";
        } else {
            $content  = file_get_contents($file);
            $content .= "\n";
            $content .= "contentLoaded_" . getArgv('onLoad', 2) . '();' . "\n";
        }

        die($content);
    }



    public function getSettings() {
        global $cfg;

        $loadKeys = false;
        if (getArgv('keys')){
            $loadKeys = getArgv('keys');
        }

        $res = array();

        if ($loadKeys == false || in_array('modules', $loadKeys))
            $res['modules'] = Kryn::$extensions;

        if ($loadKeys == false || in_array('configs', $loadKeys))
            $res['configs'] = Kryn::$configs;



        if ($loadKeys == false || in_array('layouts', $loadKeys))
            $res['layouts'] = array();

        if ($loadKeys == false || in_array('contents', $loadKeys))
            $res['contents'] = array();

        if ($loadKeys == false || in_array('navigations', $loadKeys))
            $res['navigations'] = array();

        if ($loadKeys == false || in_array('themeProperties', $loadKeys))
            $res['themeProperties'] = array();


        if (
            $loadKeys == false ||
            (in_array('modules', $loadKeys) || in_array('contents', $loadKeys) || in_array('navigations', $loadKeys))
        ){
            foreach (Kryn::$configs as $key => $config) {
                if ($config['themes']) {
                    foreach ($config['themes'] as $themeTitle => $theme) {

                        if ($loadKeys == false || in_array('layouts', $loadKeys)){
                            if ($theme['layouts']) {
                                $res['layouts'][$themeTitle] = $theme['layouts'];
                            }
                        }


                        if ($loadKeys == false || in_array('navigations', $loadKeys)){
                            if ($theme['navigations']) {
                                $res['navigations'][$themeTitle] = $theme['navigations'];
                            }
                        }

                        if ($loadKeys == false || in_array('contents', $loadKeys)){
                            if ($theme['contents']) {
                                $res['contents'][$themeTitle] = $theme['contents'];
                            }
                        }

                        if ($loadKeys == false || in_array('themeProperties', $loadKeys)){
                            //publicProperties is deprecated. themeProperties is the new key. for compatibility is it here.
                            if ($theme['publicProperties'] && count($theme['publicProperties']) > 0) {
                                $res['themeProperties'][$key][$themeTitle] = $theme['publicProperties'];
                            }

                            if ($theme['themeProperties'] && count($theme['themeProperties']) > 0) {
                                $res['themeProperties'][$key][$themeTitle] = $theme['themeProperties'];
                            }
                        }
                    }
                }
            }
        }

        if ($loadKeys == false || in_array('upload_max_filesize', $loadKeys)){
            $v = ini_get('upload_max_filesize');
            $v2 = ini_get('post_max_size');
            $b = $this->return_bytes(($v < $v2) ? $v : $v2);
            $res['upload_max_filesize'] = $b;
        }

        if ($loadKeys == false || in_array('groups', $loadKeys))
            $res['groups'] = dbTableFetch('system_group', DB_FETCH_ALL);


        if ($loadKeys == false || in_array('user', $loadKeys)){
            if ($settings = Kryn::$adminClient->getSession()->getUser()->getSettings())
                $res['user'] = $settings->toArray();

            if (!$res['user'])
                $res['user'] = array();
        }


        if ($loadKeys == false || in_array('system', $loadKeys)){
            $res['system'] = $cfg;
            $res['system']['db_name'] = '';
            $res['system']['db_user'] = '';
            $res['system']['db_passwd'] = '';
        }


        if ($loadKeys == false || in_array('r2d', $loadKeys)){
            $res['r2d'] =& Kryn::getCache("systemPages2Domain");

            if (!$res['r2d']){
                $res['r2d'] = adminPages::updatePage2DomainCache();
            }

            if (!$res['r2d'])
                $res['r2d'] = array();
        }

        if ($loadKeys == false || in_array('domains', $loadKeys)){
            $res['domains'] = array();
            $qr = dbExec('SELECT * FROM %pfx%system_domain ORDER BY domain');
            while ($row = dbFetch($qr)) {
                //todo
                //if (Core\Kryn::checkPageAcl($row['id'], 'showDomain', 'd')) {
                //    $res['domains'][] = $row;
                //}
            }
        }


        if ($loadKeys == false || in_array('modules', $loadKeys)){
            $tlangs = \LangsQuery::create()->filterByVisible(1)->find();
            $langs = dbToKeyIndex($tlangs->toArray(), 'code');
            $res['langs'] = $langs;
        }

        return $res;
    }


    public function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }


    public static function loadJs() {

        header('Content-Type: application/x-javascript');

        $md5Hash = '';
        $jsFiles = array();

        foreach (Kryn::$configs as &$config) {
            if ($config['adminJavascript'])
                \Admin::collectFiles($config['adminJavascript'], $jsFiles);
        }

        foreach ($jsFiles as $jsFile)
            $md5Hash .= filemtime($jsFile) . '.';

        $md5Hash = md5($md5Hash);

        print "/* Kryn.cms combined admin javascript file: $md5Hash */\n\n";

        if (file_exists('cache/media/cachedAdminJs_' . $md5Hash . '.js')) {
            readFile('cache/media/cachedAdminJs_' . $md5Hash . '.js');
        } else {

            $content = '';
            foreach ($jsFiles as $jsFile) {
                $content .= "\n\n/* file: $jsFile */\n\n";
                $content .= Kryn::fileRead($jsFile);
            }

            //delete old cached files
            foreach (glob('cache/media/cachedAdminJs_*.js') as $cache)
                @unlink($cache);

            Kryn::fileWrite('cache/media/cachedAdminJs_' . $md5Hash . '.js', $content);
            print $content;
        }

        print "\n" . 'ka.ai.loaderDone(' . getArgv('id') . ');' . "\n";
        exit;
    }



    public function getMenus() {

        $links = array();

        foreach (Kryn::$configs as $extCode => $config) {

            if ($config['admin']) {
                foreach ($config['admin'] as $key => $value) {

                    if ($value['childs']) {

                        $childs = $this->getChildMenus("$extCode/$key", $value);

                        if (count($childs) == 0) {
                            if (Kryn::checkUrlAccess("$extCode/$key")) {
                                unset($value['childs']);
                                $links[$extCode][$key] = $value;
                            }
                        } else {
                            $value['childs'] = $childs;
                            $links[$extCode][$key] = $value;
                        }

                    } else {
                        if (Kryn::checkUrlAccess("$extCode/$key")) {
                            $links[$extCode][$key] = $value;
                        }
                    }

                    if ((!$links[$extCode][$key]['type'] && !$links[$extCode][$key]['childs']) ||
                        $links[$extCode][$key]['isLink'] === false
                    ) {
                        unset($links[$extCode][$key]);
                    }

                }
            }
        }

        return $links;
    }


    public function getChildMenus($pCode, $pValue) {

        $links = array();
        foreach ($pValue['childs'] as $key => $value) {

            if ($value['childs']) {

                $childs = $this->getChildMenus($pCode . "/$key", $value);
                if (count($childs) == 0) {
                    if (Kryn::checkUrlAccess($pCode . "/$key")) {
                        unset($value['childs']);
                        $links[$key] = $value;
                    }
                } else {
                    $value['childs'] = $childs;
                    $links[$key] = $value;
                }

            } else {
                if (Kryn::checkUrlAccess($pCode . "/$key")) {
                    $links[$key] = $value;
                }
            }
            if ((!$links[$key]['type'] && !$links[$key]['childs']) || $links[$key]['isLink'] === false) {
                unset($links[$key][$key]);
            }

        }
        return $links;
    }

}
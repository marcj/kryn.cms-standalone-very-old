<?php


namespace Admin;

use \Core\Kryn;

class Backend {

    public function clearCache(){
        \Admin\Utils::clearCache();
        return true;
    }

    public function getDesktop() {

        if ($desktop = Kryn::$adminClient->getUser()->getDesktop())
            return $desktop->toArray();
        else return false;
    }

    public function saveDesktop($pIcons) {

        $properties = new \Core\Properties($pIcons);
        Kryn::$adminClient->getUser()->setDesktop($properties);
        Kryn::$adminClient->getUser()->save();

        return true;
    }

    public function getSearch($pQ, $pLang = null) {

        $res = array();
        foreach (Kryn::$modules as &$mod) {
            if (method_exists($mod, 'searchAdmin')) {
                $res = array_merge($res, $mod->searchAdmin($pQ));
            }
        }
        return $res;
    }


    public function getWidgets() {

        if ($widgets = Kryn::$adminClient->getUser()->getWidgets())
            return $widgets->toArray();
        else return false;

    }

    public function saveWidgets($pWidgets) {

        $properties = new \Core\Properties($pWidgets);
        Kryn::$adminClient->getUser()->setWidgets($properties);
        Kryn::$adminClient->getUser()->save();

        return true;
    }


    public function saveUserSettings($pSettings) {

        $properties = new \Core\Properties($pSettings);

        if (Kryn::getAdminClient()->getUser()->getId() > 0){
            Kryn::getAdminClient()->getUser()->setSettings($properties);
            Kryn::getAdminClient()->getUser()->save();
        }

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

    /**
     * Returns a huge array with settings.
     *
     * items: 
     *
     *  modules
     *  configs
     *  layouts
     *  contents
     *  navigations
     *  themes
     *  themeProperties
     *  user
     *  groups
     *  langs
     *
     *  Example: settings?keys[]=modules&keys[]=layouts
     * 
     * @param  array $keys Limits the result.
     * @return array
     */
    public function getSettings($keys = array()){

        $loadKeys = $keys;
        if (!$loadKeys){
            $loadKeys = false;
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
            $res['groups'] = dbTableFetchAll('system_group');


        if ($loadKeys == false || in_array('user', $loadKeys)){
            if ($settings = Kryn::$adminClient->getUser()->getSettings()){
                if ($settings instanceof \Core\Properties)
                    $res['user'] = $settings->toArray();
            }

            if (!$res['user'])
                $res['user'] = array();
        }


        if ($loadKeys == false || in_array('system', $loadKeys)){
            $res['system'] = Kryn::$config;
            $res['system']['db_name'] = '';
            $res['system']['db_user'] = '';
            $res['system']['db_passwd'] = '';
        }

        if ($loadKeys == false || in_array('r2d', $loadKeys)){
            $res['r2d'] =& Kryn::getCache("systemPages2Domain");

            if (!$res['r2d']){
                $res['r2d'] = \Admin\Pages::updatePage2DomainCache();
            }

            if (!$res['r2d'])
                $res['r2d'] = array();
        }

        if ($loadKeys == false || in_array('domains', $loadKeys)){
            $res['domains'] = \Core\Object::getList('Core\Domain', null, array('permissionCheck' => true));
        }


        if ($loadKeys == false || in_array('langs', $loadKeys)){
            $tlangs = \Core\LanguageQuery::create()->filterByVisible(true)->find()->toArray(null, null, \BasePeer::TYPE_STUDLYPHPNAME);

            $langs = dbToKeyIndex($tlangs, 'code');
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
                Utils::collectFiles($config['adminJavascript'], $jsFiles);
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

        print "\n" . 'ka.adminInterface.loaderDone(' . getArgv('id') . ');' . "\n";
        exit;
    }



    public function getMenus() {

        $links = array();

        foreach (Kryn::$configs as $extCode => $config) {

            if ($config['entryPoints']) {
                foreach ($config['entryPoints'] as $key => $value) {

                    if ($value['children']) {

                        $childs = $this->getChildMenus("$extCode/$key", $value);

                        if (count($childs) == 0) {
                            //todo, check against Permission::
                            //if (Kryn::checkUrlAccess("$extCode/$key")) {
                                unset($value['children']);
                                $links[$extCode][$key] = $value;
                            //}
                        } else {
                            $value['children'] = $childs;
                            $links[$extCode][$key] = $value;
                        }

                    } else {
                        //todo, check against Permission::
                        //if (Kryn::checkUrlAccess("$extCode/$key")) {
                            $links[$extCode][$key] = $value;
                        //}
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
        foreach ($pValue['children'] as $key => $value) {

            if ($value['children']) {

                $childs = $this->getChildMenus($pCode . "/$key", $value);
                if (count($childs) == 0) {
                    //if (Kryn::checkUrlAccess($pCode . "/$key")) {
                        unset($value['children']);
                        $links[$key] = $value;
                    //}
                } else {
                    $value['children'] = $childs;
                    $links[$key] = $value;
                }

            } else {
                //if (Kryn::checkUrlAccess($pCode . "/$key")) {
                    $links[$key] = $value;
                //}
            }
            if ((!$links[$key]['type'] && !$links[$key]['children']) || $links[$key]['isLink'] === false) {
                unset($links[$key][$key]);
            }

        }
        return $links;
    }

}
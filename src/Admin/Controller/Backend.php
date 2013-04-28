<?php

namespace Admin\Controller;

use Core\Kryn;

use Core\Config\Asset;
use Core\Config\Assets;
use Core\Permission;

class Backend
{
    public function clearCache()
    {
        \Admin\Utils::clearCache();

        return true;
    }

    public function getDesktop()
    {
        if ($desktop = Kryn::getAdminClient()->getUser()->getDesktop())
            return $desktop->toArray();
        else return false;
    }

    public function saveDesktop($pIcons)
    {
        $properties = new \Core\Properties($pIcons);

        Kryn::getAdminClient()->getUser()->setDesktop($properties);

        return Kryn::getAdminClient()->getUser()->save() > 0;
    }

    public function getSearch($pQ, $pLang = null)
    {
        $res = array();
        foreach (Kryn::$modules as &$mod) {
            if (method_exists($mod, 'searchAdmin')) {
                $res = array_merge($res, $mod->searchAdmin($pQ));
            }
        }

        return $res;
    }

    public function getWidgets()
    {
        if ($widgets = Kryn::getAdminClient()->getUser()->getWidgets())
            return $widgets->toArray();
        else return false;

    }

    public function saveWidgets($pWidgets)
    {
        $properties = new \Core\Properties($pWidgets);
        Kryn::getAdminClient()->getUser()->setWidgets($properties);
        Kryn::getAdminClient()->getUser()->save();

        return true;
    }

    public function saveUserSettings($pSettings)
    {
        $properties = new \Core\Properties($pSettings);

        if (Kryn::getAdminClient()->getUser()->getId() > 0) {
            Kryn::getAdminClient()->getUser()->setSettings($properties);
            Kryn::getAdminClient()->getUser()->save();
        }

        return true;
    }

    public function getCustomJs()
    {
        $module = getArgv('module', 2);
        $code = getArgv('code', 2);

        if ($module == 'admin')
            $file = "web/bundles/admin/js/$code.js";
        else
            $file = "web/bundles/$module/admin/js/$code.js";

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
    public function getSettings($keys = array())
    {
        $loadKeys = $keys;
        if (!$loadKeys) {
            $loadKeys = false;
        }

        $res = array();

        if ($loadKeys == false || in_array('modules', $loadKeys)){
            foreach (Kryn::$configs as $config) {
                $res['bundles'][] = $config->getName();
            }
        }

        if ($loadKeys == false || in_array('configs', $loadKeys))
            $res['configs'] = Kryn::getConfigs()->toArray();

        if (
            $loadKeys == false || in_array('themes', $loadKeys)
        ){
            foreach (Kryn::getConfigs() as $key => $config) {
                if ($config->getThemes()) {
                    foreach ($config->getThemes() as $themeTitle => $theme) {
                        /** @var $theme \Core\Config\Theme */
                        $res['themes'][$theme->getId()] = $theme->toArray();
                    }
                }
            }
        }

        if ($loadKeys == false || in_array('upload_max_filesize', $loadKeys)) {
            $v = ini_get('upload_max_filesize');
            $v2 = ini_get('post_max_size');
            $b = $this->return_bytes(($v < $v2) ? $v : $v2);
            $res['upload_max_filesize'] = $b;
        }

        if ($loadKeys == false || in_array('groups', $loadKeys))
            $res['groups'] = dbTableFetchAll('system_group');

        if ($loadKeys == false || in_array('user', $loadKeys)) {
            if ($settings = Kryn::getAdminClient()->getUser()->getSettings()) {
                if ($settings instanceof \Core\Properties)
                    $res['user'] = $settings->toArray();
            }

            if (!$res['user'])
                $res['user'] = array();
        }

        if ($loadKeys == false || in_array('system', $loadKeys)) {
            $res['system'] = Kryn::$config;
            unset($res['system']['database']);
            unset($res['system']['passwordHashKey']);
            unset($res['system']['passwordHashCompat']);
        }

        if ($loadKeys == false || in_array('r2d', $loadKeys)) {
            $res['r2d'] =& Kryn::getCache("systemPages2Domain");

            if (!$res['r2d']) {
                $res['r2d'] = \Core\Render::updatePage2DomainCache();
            }

            if (!$res['r2d'])
                $res['r2d'] = array();
        }

        if ($loadKeys == false || in_array('domains', $loadKeys)) {
            $res['domains'] = \Core\Object::getList('Core\Domain', null, array('permissionCheck' => true));
        }

        if ($loadKeys == false || in_array('langs', $loadKeys)) {
            $tlangs = \Core\Models\LanguageQuery::create()->filterByVisible(true)->find()->toArray(null, null, \BasePeer::TYPE_STUDLYPHPNAME);

            $langs = dbToKeyIndex($tlangs, 'code');
            $res['langs'] = $langs;
        }

        return $res;
    }

    public function return_bytes($val)
    {
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

    public static function loadJs()
    {
        //header('Content-Type: application/x-javascript');

        $md5Hash = '';
        $jsFiles = array();

        foreach (Kryn::$configs as $bundleConfig) {
            foreach ($bundleConfig->getAdminAssetsPaths(false, '.*\.js', true) as $assetPath) {
                $jsFiles[$assetPath] = Kryn::resolvePath($assetPath, 'Resources/public');
            }
        }

        foreach ($jsFiles as $jsFile) {
            $md5Hash .= filemtime($jsFile) . '.';
        }

        $md5Hash = md5($md5Hash);

        print "/* Kryn.cms combined admin javascript file: $md5Hash */\n\n";

        $cacheDir  = PATH_WEB_CACHE . '';
        $cacheFile = $cacheDir.'admin.cachedJs_' . $md5Hash . '.css';

        if (file_exists($cacheFile)) {
            readFile($cacheFile);
        } else {

            $content = '';
            foreach ($jsFiles as $jsFile => $localPath) {
                $content .= "\n\n/* file: $jsFile */\n\n";
                $content .= file_get_contents($localPath);
            }

            //delete old cached files
            foreach (glob($cacheDir . 'admin.cachedJs_*.js') as $cache)
                @unlink($cache);

            file_put_contents($cacheDir . 'admin.cachedJs_' . $md5Hash . '.js', $content);
            print $content;
        }

        print "\n" . 'ka.adminInterface.loaderDone(' . getArgv('id') . ');' . "\n";
        exit;
    }

    public function getMenus()
    {
        $entryPoints = array();

        foreach (Kryn::getConfigs() as $bundleName => $bundleConfig) {

            foreach ($bundleConfig->getAllEntryPoints() as $subEntryPoint) {
                $path = strtolower($subEntryPoint->getFullPath(true));

                if (substr_count($path, '/') <= 2) {
                    if ($subEntryPoint->isLink()) {
                        //todo, check permissions
                        $entryPoints[$path] = array(
                            'label' => $subEntryPoint->getLabel(),
                            'icon'  => $subEntryPoint->getIcon(),
                            'fullPath'  => $path,
                            'path'   => $subEntryPoint->getPath(),
                            'type'  => $subEntryPoint->getType(),
                            'level' => substr_count($path, '/')
                        );
                    }
                }
            }
        }

        return $entryPoints;
    }

    public function getChildMenus($pCode, $pValue)
    {
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

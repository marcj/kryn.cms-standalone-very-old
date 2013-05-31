<?php

namespace Admin\Module;

use Core\Exceptions\BundleNotFoundException;
use Core\Kryn;
use Core\SystemFile;

class Manager
{
    public function __construct()
    {
        define('KRYN_MANAGER', true);
    }

    public function __destruct()
    {
        define('KRYN_MANAGER', false);
    }

    /**
     * Filters any special char out of the name.
     *
     * @static
     *
     * @param $pName Reference
     */
    public static function prepareName(&$pName)
    {
        $pName = preg_replace('/[^a-zA-Z0-9-_\\\\]/', '', $pName);
    }

    /**
     * @param string $bundleName
     * @param bool   $pReloadConfig
     * @return int
     */
    public function deactivate($bundleName, $pReloadConfig = false)
    {
        Manager::prepareName($bundleName);

        Kryn::getSystemConfig()->removeBundle($bundleName);

        if ($pReloadConfig) {
            Kryn::loadModuleConfigs();
        }
        \Admin\Utils::clearModuleCache($bundleName);

        return Kryn::getSystemConfig()->save();
    }

    /**
     * @param $bundleName
     * @param bool $pReloadConfig
     * @return bool|int
     */
    public function activate($bundleName, $pReloadConfig = false)
    {
        Manager::prepareName($bundleName);

        Kryn::getSystemConfig()->addBundle($bundleName);

        if ($pReloadConfig) {
            Kryn::loadModuleConfigs();
        }
        \Admin\Utils::clearModuleCache($bundleName);

        return Kryn::getSystemConfig()->save();
    }

    public function getInfo($bundle)
    {
        $bundle = Kryn::getBundle($bundle);

        $info = $bundle->getComposer();
        $info['_installed'] = $bundle->getInstalledInfo();
        return $info;
    }

    public static function getInstalledInfo($name)
    {
        if (SystemFile::exists('composer.lock')) {
            $composerLock = SystemFile::getContent('composer.lock');
            if ($composerLock) {
                $composerLock = json_decode($composerLock, true);

                foreach ($composerLock['packages'] as $package) {
                    if (strtolower($package['name']) == strtolower($name)) {
                        return $package;
                    }
                }
            }
        }

        return [];
    }

    public function getInstalled()
    {
        if (SystemFile::exists('composer.json')) {
            $composer = SystemFile::getContent('composer.json');
            if ($composer) {
                $composer = json_decode($composer, true);

                $packages = [];
                $packages[] = $composer;

                foreach ((array)$composer['require'] as $name => $version) {
                    if ($package = static::getInstalledInfo($name)) {
                        $packages[] = $package;
                    }
                }
            }
        }

        return $packages ?: [];
    }

    private static function versionCompareToServer($local, $server)
    {
        list($major, $minor, $patch) = explode(".", $local);
        $lversion = $major * 1000 * 1000 + $minor * 1000 + $patch;

        list($major, $minor, $patch) = explode(".", $server);
        $sversion = $major * 1000 * 1000 + $minor * 1000 + $patch;

        if ($lversion == $sversion) {
            return '=';
        } // Same version
        else if ($lversion < $sversion) {
            return '<';
        } // Local older
        else {
            return '>';
        } // Local newer
    }

    public function getLocal()
    {
        $finder = new \Symfony\Component\Finder\Finder();
        $finder
            ->files()
            ->name('*Bundle.php')
            ->in('vendor')
            ->in('tests/bundles')
            ->in('src');
        return $this->getBundles($finder);
    }

    public function getBundles($finder)
    {
        $bundles = array();
        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {

            $file = $file->getRealPath();
            $content = file_get_contents($file);
            preg_match('/^\s*\t*class ([a-z0-9_]+)/mi', $content, $className);
            if (isset($className[1]) && $className[1]) {
                preg_match('/\s*\t*namespace ([a-zA-Z0-9_\\\\]+)/', $content, $namespace);
                $class = (count($namespace) > 1 ? $namespace[1] . '\\' : '') . $className[1];

                if ('Bundle' === $className[1] || false !== strpos($class, '\\Test\\') ||
                    false !== strpos($class, '\\Tests\\')
                ) {
                    continue;
                }

                $bundles[] = $class;
            }
        }
        $bundles = array_unique($bundles);

        foreach ($bundles as $bundleClass) {
            $bundle = new $bundleClass();
            if (!($bundle instanceof \Core\Bundle)) {
                continue;
            }

            if ($composer = $bundle->getComposer()) {
                $composer['_path'] = $bundle->getPath();
                $composer['_installed'] = $bundle->getInstalledInfo();
                $res[$bundle->getClassName()] = $composer;
                if (null === $res[$bundle->getClassName()]['activated']) {
                    $res[$bundle->getClassName()]['activated'] = array_search(
                        $bundle->getClassName(),
                        Kryn::$config['bundles']
                    ) !== false ? true : false;
                }
            }
        }
        return $res;
    }

    public static function getConfig($pName)
    {
        return self::loadInfo($pName);
    }

    public static function loadInfo($pModuleName, $pType = false, $pExtract = false)
    {
        /*
        * pType: false => load from local (dev) PATH_MODULE/$pModuleName
        * pType: path  => load from zip (module upload)
        * pType: true =>  load from inet
        */

        $pModuleName = str_replace(".", "", $pModuleName);
        $configFile = \Core\Kryn::getBundleDir($pModuleName) . "config.json";

        $extract = false;

        // inet
        if ($pType === true || $pType == 1) {

            //$res = wget(Kryn::$config['repoServer'] . "/?install=$pModuleName");
            if ($res === false) {
                return array('cannotConnect' => 1);
            }

            $info = json_decode($res, 1);

            if (!$info['id'] > 0) {
                return array('notExist' => 1);
            }

            if (!@file_exists('data/upload')) {
                if (!@mkdir('data/upload')) {
                    klog('core', t('FATAL ERROR: Can not create folder data/upload.'));
                }
            }

            if (!@file_exists('data/packages/modules')) {
                if (!@mkdir('data/packages/modules')) {
                    klog('core', _l('FATAL ERROR: Can not create folder data/packages/modules.'));
                }
            }

            $configFile = "data/packages/modules/$pModuleName.config.json";
            @unlink($configFile);
            //wget(Kryn::$config['repoServer'] . "/modules/$pModuleName/config.json", $configFile);
            if ($pExtract) {
                $extract = true;
                $zipFile = 'data/packages/modules/' . $info['filename'];
                //wget(Kryn::$config['repoServer'] . "/modules/$pModuleName/" . $info['filename'], $zipFile);
            }
        }

        //local zip
        if (($pType !== false && $pType != "0") && ($pType !== true && $pType != "1")) {
            if (file_exists(PATH_WEB . $pType)) {
                $pType = PATH_WEB . $pType;
            }
            $zipFile = $pType;
            $bname = basename($pType);
            $t = explode("-", $bname);
            $pModuleName = $t[0];
            $extract = true;
        }

        if ($extract) {
            @mkdir("data/packages/modules/$pModuleName");
            include_once 'File/Archive.php';
            $toDir = "data/packages/modules/$pModuleName/";
            $zipFile .= "/";
            $res = File_Archive::extract($zipFile, $toDir);
            $configFile = "data/packages/modules/$pModuleName/module/$pModuleName/config.json";
            if ($pModuleName == 'core') {
                $configFile = "data/packages/modules/kryn/core/config.json";
            }
        }

        if ($configFile) {
            if (!file_exists($configFile)) {
                return false;
            }
            $json = file_get_contents($configFile);
            $config = json_decode($json, true);
            unset($config['noConfig']);

            if (!$pExtract) {
                @rmDir("data/packages/modules/$pModuleName");
                @unlink($zipFile);
            }

            //if locale
            if ($pType == false) {
                if (is_dir(PATH_WEB . "$pModuleName/_screenshots")) {
                    $config['screenshots'] = Kryn::readFolder(PATH_WEB . "$pModuleName/_screenshots");
                }
            }

            $config['__path'] = dirname($configFile);
            if (is_array(Kryn::$configs) && array_key_exists($pModuleName, Kryn::$configs)) {
                $config['installed'] = true;
            }

            $config['extensionCode'] = $pModuleName;

            if (Kryn::$configs) {
                foreach (Kryn::$configs as $extender => &$modConfig) {
                    if (is_array($modConfig['extendConfig'])) {
                        foreach ($modConfig['extendConfig'] as $extendModule => $extendConfig) {
                            if ($extendModule == $pModuleName) {
                                $config['extendedFrom'][$extender] = $extendConfig;
                            }
                        }
                    }
                }
            }

            return $config;
        }

    }

    public function check4Updates()
    {
        $res['found'] = false;

        # add kryn-core

        foreach (Kryn::$configs as $key => $config) {
            $version = '0';
            $name = $key;
            //$version = wget(Kryn::$config['repoServer'] . "/?version=$name");
            if ($version && $version != '' && self::versionCompareToServer(
                    $config->getVersion(),
                    $version['content']
                ) == '<'
            ) {
                $res['found'] = true;
                $temp = array();
                $temp['newVersion'] = $version;
                $temp['name'] = $name;
                $res['modules'][] = $temp;
            }
        }

        json($res);

    }

    /**
     * Returns true if all dependencies are fine.
     *
     * @param $pName
     *
     * @return boolean
     */
    public function hasOpenDependencies($pName)
    {
    }

    /**
     * Returns a list of open dependencies.
     *
     * @param $pName
     */
    public function getOpenDependencies($pName)
    {
    }

    /**
     * Activates a module, fires his install/installDatabase package scripts
     * and updates the propel ORM, if the modules has a model.xml.
     *
     * If $pName points to a zip-file, we extract it in temp, fires the extract script and move it to our install root.
     *
     * @param  string $pName
     * @param  bool   $oOrmUpdate
     *
     * @return bool
     */
    public function install($pName, $oOrmUpdate = false)
    {
        Manager::prepareName($pName);

        $hasPropelModels = SystemFile::exists(Kryn::getBundleDir($pName) . 'Resources/config/models.xml');
        $this->fireScript($pName, 'install');

        //fire update propel orm
        if ($oOrmUpdate && $hasPropelModels) {
            //update propel
            \Core\PropelHelper::updateSchema();
            \Core\PropelHelper::cleanup();
        }

        $this->activate($pName, true);

        return true;
    }

    /**
     * Fires the database package script.
     *
     * @param  string $pName
     *
     * @return bool
     */
    public function installDatabase($pName)
    {
        $this->fireScript($pName, 'install.database');

        return true;
    }

    /**
     * Removes relevant data and object's data. Executes also the uninstall script.
     * Removes database values, some files etc.
     *
     * @param  string $pName
     * @param  bool   $pRemoveFiles
     * @param  bool   $oOrmUpdate
     *
     * @return bool
     */
    public function uninstall($pName, $pRemoveFiles = true, $pOrmUpdate = false)
    {
        Manager::prepareName($pName);
        $config = self::getConfig($pName);
        $hasPropelModels = SystemFile::exists(\Core\Kryn::resolvePath($pName, 'Resources/config') . 'model.xml');

        \Core\Event::fire('admin/module/manager/uninstall/pre', $pName);

        //remove object data
        if ($config['objects']) {
            foreach ($config['objects'] as $key => $object) {
                \Core\Object::clear(ucfirst($pName) . '\\' . $key);
            }
        }

        $this->fireScript($pName, 'uninstall');

        $bundle = \Core\Kryn::getBundle($pName);

        if (!$bundle) {
            throw new BundleNotFoundException(tf('Bundle `%s` not found.', $pName));
        }

        $webName = strtolower($bundle->getName(true));

        //remove files
        if ($pRemoveFiles) {
            if ($config['extraFiles']) {
                foreach ($config['extraFiles'] as $file) {
                    delDir($file);
                }
            }

            @unlink($webName);
        }

        \Core\Event::fire('admin/module/manager/uninstall/post', $pName);

        $this->deactivate($pName, true);

        //fire update propel orm
        if ($pOrmUpdate && $hasPropelModels) {
            //remove propel classes in temp
            \Core\TempFile::remove('propel-classes/' . $bundle->getRootNamespace());

            //update propel
            if ($pOrmUpdate) {
                \Core\PropelHelper::updateSchema();
                \Core\PropelHelper::cleanup();
            }
        }

        return true;

    }

    /**
     * Fires the script in module/$pModule/package/$pScript.php and its events.
     *
     * @event admin/module/manager/<$pScript>/pre
     * @event admin/module/manager/<$pScript>/failed
     * @event admin/module/manager/<$pScript>
     *
     * @param  string $pModule
     * @param  string $pScript
     *
     * @throws \SecurityException
     * @throws \Exception
     * @return bool
     */
    public function fireScript($pModule, $pScript)
    {
        \Core\Event::fire('admin/module/manager/' . $pScript . '/pre', $pModule);

        $file = $this->getScriptFile($pModule, $pScript);

        if (file_exists($file)) {

            $content = file_get_contents($file);
            if (strpos($content, 'KRYN_MANAGER') === false) {
                throw new \SecurityException('!It is not safe, if your script can be external executed!');
            }

            try {
                include($file);
            } catch (\Exception $ex) {
                //\Core\Event::fire('admin/module/manager/' . $pScript . '/failed', $arg = array($pModule, $ex));
                throw $ex;
            }

            \Core\Event::fire('admin/module/manager/' . $pScript, $pModule);
        }

        return true;
    }


    private function getScriptFile($pModule, $pName)
    {
        self::prepareName($pModule);

        try {
            return \Core\Kryn::getBundleDir($pModule) . 'Resources/package/' . $pName . '.php';
        } catch (\Core\Exceptions\ModuleDirNotFoundException $e) {
        }

    }
}

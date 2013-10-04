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
     * @param $name Reference
     */
    public static function prepareName(&$name)
    {
        $name = preg_replace('/[^a-zA-Z0-9-_\\\\]/', '', $name);
    }

    /**
     * Deactivates a bundle in the system config.
     *
     * @param string $bundle
     * @param bool   $reloadConfig
     * @return int
     */
    public function deactivate($bundle, $reloadConfig = false)
    {
        Manager::prepareName($bundle);

        Kryn::getSystemConfig()->removeBundle($bundle);

        if ($reloadConfig) {
            Kryn::loadModuleConfigs();
        }
        \Admin\Utils::clearModuleCache($bundle);

        return Kryn::getSystemConfig()->save();
    }

    /**
     * Activates a bundle in the system config.
     *
     * @param $bundle
     * @param bool $reloadConfig
     * @return bool|int
     */
    public function activate($bundle, $reloadConfig = false)
    {
        Manager::prepareName($bundle);

        Kryn::getSystemConfig()->addBundle($bundle);

        if ($reloadConfig) {
            Kryn::loadModuleConfigs();
        }
        \Admin\Utils::clearModuleCache($bundle);

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
        $packages = [];
        $bundles = [];
        if (SystemFile::exists('composer.json')) {
            $composer = SystemFile::getContent('composer.json');
            if ($composer) {
                $composer = json_decode($composer, true);

                $packages = [];

                foreach ((array)$composer['require'] as $name => $version) {
                    $package = [
                        'name' => $name,
                        'version' => $version
                    ];
                    $packages[] = $package;
                }
            }
        }

        $bundleClasses = array_merge(
            static::getBundlesFromPath('vendor'),
            static::getBundlesFromPath('src')
        );

        if ($bundleClasses) {
            foreach ($bundleClasses as $bundle) {
                $bundleObj = new $bundle;
                $path = $bundleObj->getPath();
                if (0 === strpos($path, 'vendor/')) {
                    $expl = explode('/', $path);
                    $package = $expl[1] . '/' . $expl[2];
                } else {
                    $package = 'local ./src/';
                }
                $bundleInfo = [
                    'class' => $bundle,
                    'package' => $package,
                    'active' => Kryn::isActiveBundle($bundle)
                ];
                $bundles[] = $bundleInfo;
            }
        }

        return [
            'packages' => $packages,
            'bundles' => $bundles
        ];
    }

    /**
     * @param string $path
     * @return array
     */
    public function getBundlesFromPath($path)
    {
        if (SystemFile::exists($path)) {
            $bundles = [];

            $finder = new \Symfony\Component\Finder\Finder();
            $finder
                ->files()
                ->name('*Bundle.php')
                ->notPath('/Tests/')
                ->notPath('/Test/')
                ->in($path);

            $bundles = array();
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            foreach ($finder as $file) {

                $file = $file->getRealPath();
                $content = file_get_contents($file);
                preg_match('/^\s*\t*class ([a-z0-9_]+)/mi', $content, $className);
                if (isset($className[1]) && $className[1]){
                    preg_match('/\s*\t*namespace ([a-zA-Z0-9_\\\\]+)/', $content, $namespace);
                    $class = (count($namespace) > 1 ? $namespace[1] . '\\' : '' ) . $className[1];

                    if ('Bundle' === $className[1]) {
                        continue;
                    }

                    $bundles[] = $class;
                }
            }

            return $bundles;
        }
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
     * @param $name
     *
     * @return boolean
     */
    public function hasOpenDependencies($name)
    {
    }

    /**
     * Returns a list of open dependencies.
     *
     * @param $name
     */
    public function getOpenDependencies($name)
    {
    }

    /**
     *
     * Installs a bundle.
     * Activates a bundle, fires his package scripts
     * and updates the propel ORM, if the bundle has a model.xml.
     *
     * @param  string $bundle
     * @param  bool   $ormUpdate
     *
     * @return bool
     */
    public function install($bundle, $ormUpdate = false)
    {
        Manager::prepareName($bundle);

        $hasPropelModels = SystemFile::exists(Kryn::getBundleDir($bundle) . 'Resources/config/models.xml');
        $this->fireScript($bundle, 'install');

        //fire update propel orm
        if ($ormUpdate && $hasPropelModels) {
            //update propel
            \Core\PropelHelper::updateSchema();
            \Core\PropelHelper::cleanup();
        }

        $this->activate($bundle, true);

        return true;
    }

    /**
     * Fires the database package script.
     *
     * @param  string $name
     *
     * @return bool
     */
    public function installDatabase($name)
    {
        $this->fireScript($name, 'install.database');

        return true;
    }

    /**
     * Removes relevant data and object's data. Executes also the uninstall script.
     * Removes database values, some files etc.
     *
     * @param $bundle
     * @param bool $removeFiles
     * @param bool $ormUpdate
     *
     * @return bool
     *
     * @throws \Core\Exceptions\BundleNotFoundException
     */
    public function uninstall($bundle, $removeFiles = true, $ormUpdate = false)
    {
        Manager::prepareName($bundle);

        $bundleObject = \Core\Kryn::getBundle($bundle);
        if (!$bundleObject) {
            throw new BundleNotFoundException(tf('Bundle `%s` not found.', $bundle));
        }

        $hasPropelModels = SystemFile::exists(\Core\Kryn::resolvePath($bundle, 'Resources/config') . 'model.xml');

        \Core\Event::fire('admin/module/manager/uninstall/pre', $bundle);

        $this->fireScript($bundle, 'uninstall');

        \Core\Event::fire('admin/module/manager/uninstall/post', $bundle);

        $this->deactivate($bundle, true);

        //fire update propel orm
        if ($ormUpdate && $hasPropelModels) {
            //remove propel classes in temp
            \Core\TempFile::remove('propel-classes/' . $bundleObject->getRootNamespace());

            //update propel
            if ($ormUpdate) {
                \Core\PropelHelper::updateSchema();
                \Core\PropelHelper::cleanup();
            }
        }

        //remove files
        if (filter_var($removeFiles, FILTER_VALIDATE_BOOLEAN)) {
            delDir($bundleObject->getPath());
        }

        return true;

    }

    /**
     * Fires the script in module/$module/package/$script.php and its events.
     *
     * @event admin/module/manager/<$script>/pre
     * @event admin/module/manager/<$script>/failed
     * @event admin/module/manager/<$script>
     *
     * @param  string $module
     * @param  string $script
     *
     * @throws \SecurityException
     * @throws \Exception
     * @return bool
     */
    public function fireScript($module, $script)
    {
        \Core\Event::fire('admin/module/manager/' . $script . '/pre', $module);

        $file = $this->getScriptFile($module, $script);

        if (file_exists($file)) {

            $content = file_get_contents($file);
            if (strpos($content, 'KRYN_MANAGER') === false) {
                throw new \SecurityException('!It is not safe, if your script can be external executed!');
            }

            try {
                include($file);
            } catch (\Exception $ex) {
                //\Core\Event::fire('admin/module/manager/' . $script . '/failed', $arg = array($module, $ex));
                throw $ex;
            }

            \Core\Event::fire('admin/module/manager/' . $script, $module);
        }

        return true;
    }


    private function getScriptFile($module, $name)
    {
        self::prepareName($module);

        try {
            return \Core\Kryn::getBundleDir($module) . 'Resources/package/' . $name . '.php';
        } catch (\Core\Exceptions\ModuleDirNotFoundException $e) {
        }

    }
}

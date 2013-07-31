<?php

namespace Admin\Module;

use Admin\Exceptions\BuildException;
use Admin\Module\Manager;
use Core\Config\Bundle;
use Core\Config\EntryPoint;
use Core\Config\Model;
use Core\Config\Object;
use Core\Config\Plugin;
use Core\Config\Theme;
use Core\Exceptions\BundleNotFoundException;
use Core\Kryn;
use Core\SystemFile;

class Editor
{
    public function getConfig($bundle)
    {
        $bundle = Kryn::getBundle($bundle);
        $config = $bundle->getComposer();
        $config['_path'] = $bundle->getPath();
        return $config;
    }

    public function getBasic($bundle)
    {
        $bundle = $this->getBundle($bundle);
        $config = $bundle->getConfig();

        $result['streams'] = $config->propertyToArray('streams');
//        $result['falDriver'] = $config->propertyToArray('falDriver');
//        $result['caches'] = $config->propertyToArray('caches');
//        $result['events'] = $config->propertyToArray('events');

        $adminAssets = $config->getAdminAssets();
        $assets = [];
        if ($adminAssets) {
            foreach ($adminAssets as $asset) {
                $asset = array_merge($asset->toArray(), ['type' => 'Core\Config\Asset' === get_class($asset) ? 'asset' : 'assets']);
                $assets[] = $asset;
            }
        }
        $result['adminAssets'] = $assets;

        return $result;
    }

    public function getLanguage($pName, $pLang = null)
    {
        Manager::prepareName($pName);

        return \Core\Lang::getLanguage($pName, $pLang);

    }

    public function saveLanguage($pName, $pLangs, $pLang = null)
    {
        Manager::prepareName($pName);

        return \Core\Lang::saveLanguage($pName, $pLang, $pLangs);

    }

    public function getExtractedLanguage($pName)
    {
        Manager::prepareName($pName);

        return \Core\Lang::extractLanguage($pName);

    }

    public function getWindows($bundle)
    {
        $bundle = $this->getBundle($bundle);

        $classes = find($bundle->getPath(), '*.php');
        $windows = array();
        $whiteList = array('\Admin\ObjectCrud');

        foreach ($classes as $class) {

            $content = SystemFile::getContent($class);

            if (preg_match(
                '/class[\s\t]+([a-zA-Z0-9_]+)[\s\t]+extends[\s\t]+([a-zA-Z0-9_\\\\]*)[\s\t\n]*{/',
                $content,
                $matches
            )
            ) {
                if (in_array($matches[2], $whiteList)) {

                    $clazz = $matches[1];

                    preg_match('/namespace ([a-zA-Z0-9_\\\\]*)/', $content, $namespace);
                    $namespace = $namespace[1];
                    if ($namespace) {
                        $clazz = $namespace . '\\' . $clazz;
                    }

                    $clazz = '\\' . $clazz;

                    $windows[$class] = $clazz;
                }
            }
        }

        return $windows;
    }

    public function getPlugins($bundle)
    {
        $bundle = $this->getBundle($bundle);
        $config = $bundle->getConfig();

        return $config->getPluginsArray();
    }

    public function getThemes($bundle)
    {
        $bundle = $this->getBundle($bundle);
        $config = $bundle->getConfig();

        return $config->getThemesArray();
    }

    public function saveThemes($bundle, $themes)
    {
        $bundle = $this->getBundle($bundle);

        $def = [];
        if (is_string($themes)) {
            $themes = json_decode($themes, 1);
        }

        foreach ($themes as $array) {
            $theme = new Theme();
            $theme->fromArray($array);
            $def[] = $theme;
        }

        $config = new Bundle();
        $config->setThemes($def);

        $file = $bundle->getPath() . 'Resources/config/kryn.themes.xml';
        return $config->saveConfig($file);
    }

    public function savePlugins($bundle, $plugins)
    {
        $bundle = $this->getBundle($bundle);

        $pluginsDef = [];
        if (is_string($plugins)) {
            $plugins = json_decode($plugins, 1);
        }

        foreach ($plugins as $pluginArray) {
            $plugin = new Plugin();
            $plugin->fromArray($pluginArray);
            $pluginsDef[] = $plugin;
        }

        $config = new Bundle();
        $config->setPlugins($pluginsDef);

        $file = $bundle->getPath() . 'Resources/config/kryn.plugins.xml';
        return $config->saveConfig($file);
    }

    public function getObjects($bundle)
    {
        $bundle = $this->getBundle($bundle);
        $config = $bundle->getConfig();

        return $config->getObjectsArray();
    }

    public function saveObjects($bundle, $objects)
    {
        $bundle = $this->getBundle($bundle);

        $objectsDef = [];
        if (is_string($objects)) {
            $objects = json_decode($objects, 1);
        }

        foreach ($objects as $objectArray) {
            $object = new Object();
            $object->fromArray($objectArray);
            $objectsDef[] = $object;
        }

        $config = new Bundle();
        $config->setObjects($objectsDef);

        $file = $bundle->getPath() . 'Resources/config/kryn.objects.xml';
        return $config->saveConfig($file);
    }

    public function getModel($bundle)
    {
        $bundleClass = $this->getBundle($bundle);
        $path = $bundleClass->getPath() . 'Resources/config/models.xml';

        return [
            'path' => $path,
            'content' => @file_get_contents($path)
        ];

    }

    public function saveModel($bundle, $model)
    {
        $bundleClass = $this->getBundle($bundle);
        $path = $bundleClass->getPath() . 'Resources/config/models.xml';

        if (!is_writable($path)) {
            throw new \FileNotWritableException(tf('The model file `%s` for `%s` is not writable.', $path, $bundle));
        }

        if (!@file_put_contents($path, $model)) {
            throw new \FileIOErrorException(tf('Can not write model file `%s` for `%s`.', $path, $bundle));
        }

        return true;
    }

    public function setModelFromObjects($bundle)
    {
        $bundle = $this->getBundle($bundle);
        $config = $bundle->getConfig();

        $path = $bundle->getPath() . 'Resources/config/models.xml';
        if (!file_exists($path) && !touch($path)) {
            throw new BuildException(tf('File `%s` is not writeable.', $path));
        }

        $result = array();
        foreach ($config->getObjects() as $object) {
            /** @var $object Object */
            try {
                $result[$object->getId()] = $this->setModelFromObject($bundle, $object);
            } catch (BuildException $e) {
                $result[$object->getId()] = $e;
            }
        }

        return $result;
    }

    public function setModelFromObject(\Core\Bundle $bundle, Object $object)
    {
        $clazz = 'Core\ORM\Sync\\' . ucfirst($object->getDataModel());
        if (class_exists($clazz)) {
            $sync = new $clazz();
            return $sync->syncObject($bundle, $object);
        }

        return false;
    }

    public function getBundle($bundleClass)
    {
        $bundle = Kryn::getBundle($bundleClass);
        if (!$bundle) {
            throw new BundleNotFoundException(tf('Bundle `%s` not found.', $bundleClass));
        }
        return $bundle;
    }

    public function saveGeneral($name)
    {
        $bundle = $this->getBundle($name);
        $config = $bundle->getComposer();

        $values = ['name', 'description', 'keywords', 'version', 'license', 'require', 'authors', 'homepage'];
        foreach ($values as $key) {
            if ('' !== $_POST[$key]) {
                $config[$key] = $_POST[$key];
            } else {
                unset($config[$key]);
            }
        }

        return $bundle->setComposer($config);
    }

    public function getEntryPoints($bundle)
    {
        $bundle = $this->getBundle($bundle);
        $config = $bundle->getConfig();

        $entryPoints = $config->getEntryPointsArray();
        return $entryPoints;
    }

    public function saveEntryPoints($bundle, $entryPoints)
    {
        $bundle = $this->getBundle($bundle);

        $entryPointsDef = [];
        foreach ($entryPoints as $entryPointArray) {
            $entryPoint = new EntryPoint();
            $entryPoint->fromArray($entryPointArray);
            $entryPointsDef[] = $entryPoint;
        }

        $config = new Bundle();
        $config->setEntryPoints($entryPointsDef);

        $file = $bundle->getPath() . 'Resources/config/kryn.entrypoints.xml';
        return $config->saveConfig($file);
    }

    public function saveWindowDefinition($pClass)
    {
        if (substr($pClass, 0, 1) != '\\') {
            $pClass = '\\' . $pClass;
        }

        $general = getArgv('general');
        $path = $general['file'];

        $sourcecode = "<?php\n\n";

        $lSlash = strrpos($pClass, '\\');
        $className = $lSlash !== -1 ? substr($pClass, $lSlash + 1) : $pClass;

        $parentClass = '\Admin\ObjectCrud';

        $namespace = substr(substr($pClass, 1), 0, $lSlash);
        if (substr($namespace, -1) == '\\') {
            $namespace = substr($namespace, 0, -1);
        }

        $sourcecode .= "namespace $namespace;\n \n";

        $sourcecode .= 'class ' . $className . ' extends ' . $parentClass . " {\n\n";

        if (count($fields = getArgv('fields')) > 0) {
            $this->addVar($sourcecode, 'fields', $fields);
        }

        $listing = getArgv('list');
        if (is_array($listing)) {
            foreach ($listing as $listVarName => $listVar) {
                $this->addVar($sourcecode, $listVarName, $listVar);
            }
        }

        $add = getArgv('add');
        if (is_array($add)) {
            foreach ($add as $varName => $var) {
                $this->addVar($sourcecode, $varName, $var);
            }
        }

        $general = getArgv('general');
        $blacklist = array('class', 'file');
        if (is_array($general)) {
            foreach ($general as $varName => $var) {
                if (array_search($varName, $blacklist) !== false) {
                    continue;
                }
                $this->addVar($sourcecode, $varName, $var);
            }
        }

        $methods = getArgv('methods');
        if (is_array($methods)) {
            foreach ($methods as $name => $source) {
                $this->addMethod($sourcecode, $source);
            }
        }

        $sourcecode .= "\n}\n";

        $sourcecode = str_replace("\r", '', $sourcecode);

        SystemFile::setContent($path, $sourcecode);

        return true;

    }

    public function addMethod(&$pSourceCode, $pSource)
    {
        $pSourceCode .= substr($pSource, 6, -4) . "\n";

    }

    public function addVar(&$pSourceCode, $pName, $pVar, $pVisibility = 'public', $pStatic = false)
    {
        $val = var_export(self::toVar($pVar), true);

        if (is_array($pVar)) {
            $val = preg_replace("/' => \n\s+array \(/", "' => array (", $val);
        }

        $pSourceCode .=
            "    "
            . $pVisibility . ($pStatic ? ' static' : '') . ' $' . $pName . ' = ' . $val
            . ";\n\n";

    }

    public function toVar($pValue)
    {
        if ($pValue == 'true') {
            return true;
        }
        if ($pValue == 'false') {
            return false;
        }
        if (is_numeric($pValue)) {
            return $pValue + 0;
        }
        return $pValue;
    }

    public function getWindowDefinition($pClass)
    {
        if (substr($pClass, 0, 1) != '\\') {
            $pClass = '\\' . $pClass;
        }

        if (!class_exists($pClass)) {
            throw new \ClassNotFoundException(tf('Class %s not found.', $pClass));
        }

        $reflection = new \ReflectionClass($pClass);
        $path = substr($reflection->getFileName(), strlen(PATH));

        $content = explode("\n", SystemFile::getContent($path));

        $classReflection = new \ReflectionClass($pClass);
        $actualPath = $classReflection->getFileName();

        $res = array(
            'class' => $pClass,
            'file' => $path,
            'actualFile' => $actualPath,
            'properties' => array(
                '__file__' => $path
            )
        );

        $obj = new $pClass(null, true);
        foreach ($obj as $k => $v) {
            $res['properties'][$k] = $v;
        }

        $parent = $reflection->getParentClass();
        $parentClass = $parent->name;

        $methods = $reflection->getMethods();

        foreach ($methods as $method) {
            if ($method->class == $pClass) {

                $code = '';
                if ($code) {
                    $code = "    $code\n";
                }
                for ($i = $method->getStartLine() - 1; $i < $method->getEndLine(); $i++) {
                    $code .= $content[$i] . "\n";
                }

                if ($doc = $method->getDocComment()) {
                    $code = "    $doc\n$code";
                }

                $res['methods'][$method->name] = str_replace("\r", '', $code);
            }
        }

        if (getArgv('parentClass')) {
            $parentClass = getArgv('parentClass', 2);
        }


        if ($res['properties']['fields']) {
            foreach ($res['properties']['fields'] as &$field) {
                if ($field instanceof Model) {
                    $field = $field->toArray();
                }
            }
        }

        self::extractParentClassInformation($parentClass, $res['parentMethods']);

        unset($res['properties']['_fields']);

        return $res;
    }

    /**
     * Extracts parent's class information.
     *
     * @internal
     *
     * @param $pParentClass
     * @param $pMethods
     *
     * @throws \ClassNotFoundException
     */
    public static function extractParentClassInformation($pParentClass, &$pMethods)
    {
        if (!class_exists($pParentClass)) {
            throw new \ClassNotFoundException();
        }

        $reflection = new \ReflectionClass($pParentClass);
        $parentPath = substr($reflection->getFileName(), strlen(PATH));

        $parentContent = explode("\n", SystemFile::getContent($parentPath));
        $parentReflection = new \ReflectionClass($pParentClass);

        $methods = $parentReflection->getMethods();
        foreach ($methods as $method) {
            if ($pMethods[$method->name]) {
                continue;
            }

            if ($method->class == $pParentClass) {

                $code = '';
                for ($i = $method->getStartLine() - 1; $i < $method->getEndLine(); $i++) {

                    $code .= $parentContent[$i] . "\n";
                    if (strpos($parentContent[$i], '{')) {
                        break;
                    }

                }

                if ($doc = $method->getDocComment()) {
                    $code = "    $doc\n$code";
                }

                $pMethods[$method->name] = str_replace("\r", '', $code);
            }
        }

        $parent = $parentReflection->getParentClass();

        if ($parent) {
            self::extractParentClassInformation($parent->name, $pMethods);
        }

    }


    /**
     * Creates a new CRUD object window.
     *
     * @param string $pClass
     * @param string $pModule Name of the module
     * @param bool $pForce
     *
     * @return bool
     * @throws \FileAlreadyExistException
     */
    public function newWindow($pClass, $pModule, $pForce = false)
    {
        if (substr($pClass, 0, 1) != '\\') {
            $pClass = '\\' . $pClass;
        }

        if (class_exists($pClass) && !$pForce) {
            $reflection = new \ReflectionClass($pClass);
            throw new \FileAlreadyExistException(tf('Class already exist in %s', $reflection->getFileName()));
        }

        $actualPath = str_replace('\\', '/', substr($pClass, 1)) . '.class.php';
        $actualPath = \Core\Kryn::getBundleDir($pModule) . 'controller/' . $actualPath;

        if (file_exists($actualPath) && !$pForce) {
            throw new \FileAlreadyExistException(tf('File already exist, %s', $actualPath));
        }

        $sourcecode = "<?php\n\n";

        $lSlash = strrpos($pClass, '\\');
        $className = $lSlash !== -1 ? substr($pClass, $lSlash + 1) : $pClass;

        $parentClass = '\Admin\ObjectCrud';

        $namespace = ucfirst($pModule) . substr($pClass, 0, $lSlash);
        if (substr($namespace, -1) == '\\') {
            $namespace = substr($namespace, 0, -1);
        }

        $sourcecode .= "namespace $namespace;\n \n";

        $sourcecode .= 'class ' . $className . ' extends ' . $parentClass . " {\n\n";

        $sourcecode .= "}\n";

        error_log($actualPath);

        return SystemFile::setContent($actualPath, $sourcecode);
    }

}

<?php

namespace Core;

class Bundle
{

    /**
     * @var \ReflectionClass
     */
    private $reflectionObject;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $composer;

    /**
     * Returns the path to this bundle.
     *
     * @return string path with trailing slash.
     */
    final public function getPath()
    {
        if (null === $this->reflectionObject) {
            $this->reflectionObject = new \ReflectionClass($this);
        }

        if (null === $this->path) {
            $this->path = dirname($this->reflectionObject->getFileName());

            if (strpos($this->path, PATH) !== false) {
                $this->path = substr($this->path, strlen(PATH));
            }
        }

        return $this->path . (substr($this->path, -1) === '/' ? '' : '/');
    }

    /**
     * @param string $path
     */
    final public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Gets the appropriate information from the composer.lock file.
     *
     * @return array
     */
    public function getInstalledInfo()
    {
        return \Admin\Module\Manager::getInstalledInfo($this->getComposer()['name']);
    }

    /**
     * Returns the full namespace.
     *
     * @return string
     */
    final public function getNamespace()
    {
        if (null === $this->reflectionObject) {
            $this->reflectionObject = new \ReflectionClass($this);
        }

        return $this->reflectionObject->getNamespaceName();
    }

    /**
     * Returns the first part of the namespace.
     *
     * @return string
     */
    final public function getRootNamespace()
    {
        $namespace = $this->getNamespace();
        return substr($namespace, 0, strpos($namespace, '\\') ? : strlen($namespace));
    }

    /**
     * @return string
     */
    final public function getClassName()
    {
        return get_called_class();
    }

    /**
     * Returns the name of this bundle.
     *
     * @param bool $withoutSuffix
     *
     * @return string
     */
    final public function getName($withoutSuffix = false)
    {
        if (null === $this->name) {
            $this->name = get_class($this);
            if (false !== ($pos = strrpos($this->name, '\\'))) {
                $this->name = substr($this->name, 1 + $pos);
            }
        }

        if ($withoutSuffix && substr($this->name, -6) == 'Bundle') {
            return substr($this->name, 0, -6);
        }

        return $this->name;
    }

    /**
     * Returns the path to the composer.json
     *
     * @return string
     */
    public function getComposerPath()
    {
        return $this->getPath() . 'composer.json';
    }

    /**
     * Returns the composer configuration as array.
     *
     * @return array
     */
    public function getComposer()
    {
        if (null === $this->composer) {
            $path = $this->getComposerPath();
            if (file_exists($file = $path)) {
                $this->composer = json_decode(file_get_contents($file), true);
            }
        }

        return $this->composer;
    }

    /**
     * @return Config\Bundle
     */
    public function getConfig()
    {
        return Kryn::getConfig($this->getClassName());
    }

    /**
     * Sets and saves the composer config.
     *
     * @param array $composer
     *
     * @return boolean
     */
    public function setComposer(array $composer)
    {
        $this->composer = $composer;
        return false !== file_put_contents($this->getComposerPath(), json_format($this->composer));
    }

    /**
     * @return array
     */
    public function getConfigFiles()
    {
        $folder = $this->getPath() . 'Resources/config/';
        $baseFile = $folder . 'kryn.xml';

        $files = [];
        if (file_exists($baseFile)) {
            $files = [$folder . 'kryn.xml'];
        }

        if (file_exists($folder)) {
            $files = array_merge($files, glob($folder . 'kryn.*.xml'));
        }

        return $files;
    }

    /**
     * Returns a md5 hash of all kryn config files (Resources/config/kryn.*.xml).
     *
     * @return string
     */
    public function getConfigHash()
    {
        $hash = [];
        foreach ($this->getConfigFiles() as $file) {
            $hash[] = filemtime($file);
        }
        return md5(implode('.', $hash));
    }

    /**
     * Returns a all unfiltered configuration vars. Not recommended to use it. Use instead Kryn::getConfig();
     *
     * @return array
     */
    public function getConfigs()
    {
        $configs = array();
        foreach ($this->getConfigFiles() as $file) {
            if (file_exists($file)) {
                $doc = new \DOMDocument();
                $doc->load($file);

                $bundles = $doc->getElementsByTagName('bundle');
                foreach ($bundles as $bundle) {
                    $bundleName = $bundle->attributes->getNamedItem('name')->nodeValue;
                    if (!$bundleName) {
                        $bundleName = $this->getName();
                    }
                    $priority = $bundle->attributes->getNamedItem('priority')->nodeValue ? : 0;

                    $configs[$bundleName][$priority][$file] = $bundle;
                }
            }
        }

        return $configs;
    }

}
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
     * @return string
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

        return $this->path . '/';
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
            if (false !== ($pos = strpos($this->name, '\\'))) {
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
     * @param array $composer
     */
    public function setComposer(array $composer)
    {
        $this->composer = $composer;
        file_put_contents($this->getComposerPath(), json_encode($this->composer));
    }

    /**
     * Returns a all unfiltered configuration vars. Not recommended to use it. Use instead Kryn::getConfig();
     *
     * @return array
     */
    public function getConfig()
    {
        $configs = array();

        $files = glob($this->getPath() . '/Resources/config/kryn.*.xml');

        foreach ($files as $file) {

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

                    $configs[$bundleName][$priority][] = $bundle;
                }
            }
        }

        return $configs;
    }

}
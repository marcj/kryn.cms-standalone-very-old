<?php

namespace Core;

class Bundle {

    private $reflectionObject;

    private $name;

    private $path;

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

    final public function getNamespace()
    {
        if (null === $this->reflectionObject) {
            $this->reflectionObject = new \ReflectionClass($this);
        }

        return $this->reflectionObject->getNamespaceName();
    }

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

    public function getConfig()
    {
        $configs = array();

        $files = glob($this->getPath(). '/Resources/config/kryn.*.xml');

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
                    $priority = $bundle->attributes->getNamedItem('priority')->nodeValue ?: 0;

                    $configs[$bundleName][$priority][] = $bundle;
                }
            }
        }

        return $configs;
    }

}
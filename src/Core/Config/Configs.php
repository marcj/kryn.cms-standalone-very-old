<?php

namespace Core\Config;

use Core\Kryn;

class Configs implements \IteratorAggregate
{
    /**
     * @var Bundle[]
     */
    private $configElements = array();

    /**
     * @param array $bundles
     */
    public function __construct(array $bundles = null)
    {
        if ($bundles) {
            foreach ($bundles as $bundleName) {
                $bundle = Kryn::getBundle($bundleName);
                if (!$bundle) {
                    continue;
                }

                $configs = $bundle->getConfigs();
                $this->configElements = array_merge($this->configElements, $configs);
            }

            $this->configElements = $this->parseConfig($this->configElements);
        }
    }

    /**
     * Handles the <modify> element.
     * Calls on each config object the setup method.
     */
    public function setup()
    {
        foreach ($this->configElements as $config) {

            //todo, handle modify tag.

            $config->setup($this);
        }
    }

    /**
     * $configs = $configs[$bundleName][$priority][] = $bundleDomElement;
     *
     * @param array $configs
     *
     * @return \Core\Config\Bundle[]
     */
    public function parseConfig(array $configs)
    {
        $bundleConfigs = array();
        foreach ($configs as $bundleName => $priorities) {
            ksort($priorities); //sort by priority

            foreach ($priorities as $configs) {
                foreach ($configs as $file => $bundleElement) {
                    if (!$bundleConfigs[$bundleName]) {
                        $bundleConfigs[$bundleName] = new Bundle($bundleName);
                    }

                    $bundleConfigs[$bundleName]->import($bundleElement, $file);
                }
            }

            if ($bundleConfigs[$bundleName]) {
                $bundleConfigs[$bundleName]->setupObject();
            }
        }
        return $bundleConfigs;
    }

    /**
     * @param string $bundleName
     *
     * @return Config
     */
    public function getConfig($bundleName)
    {
        $bundle = Kryn::getBundle($bundleName);
        if (!$bundle) {
            return;
        }

        $name = $bundle->getName();
        return $this->configElements[$name];
    }

    public function getConfigs()
    {
        return $this->configElements;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this->configElements as $config) {
            $value = $config->toArray();
            $bundle = $config->getBundleClass();
            $value['composer'] = $bundle->getComposer() ?: [];
            $result[strtolower($config->getName())] = $value;
        }
        return $result;
    }

    /**
     * @return Config[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->configElements);
    }
}

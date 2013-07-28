<?php

namespace Core\Config;

use Core\Kryn;

class Configs implements \IteratorAggregate
{
    /**
     * @var Bundle[]
     */
    private $configElements = array();

    public function __construct(array $bundles)
    {
        foreach ($bundles as $bundleName) {
            $bundle = Kryn::getBundle($bundleName);
            if (!$bundle) {
                continue;
            }

            $configs = $bundle->getConfigs();
            $this->configElements = array_merge_recursive_distinct($this->configElements, $configs);
        }

        $this->configElements = $this->parseConfig($this->configElements);
    }

    public function setup()
    {
        foreach ($this->configElements as $config) {
            $config->setup($this);
        }
    }

    /**
     * $configs = $configs[$bundleName][$priority][] = $bundleDomElement;
     *
     * @param array $configs
     *
     * @return array
     */
    public function parseConfig(array $configs)
    {
        $bundleConfigs = array();
        foreach ($configs as $bundleName => $priorities) {
            ksort($priorities); //sort by priority

            foreach ($priorities as $configs) {
                foreach ($configs as $bundleElement) {
                    if ($bundleConfigs[$bundleName]) {
                        $bundleConfigs[$bundleName]->import($bundleElement);
                    } else {
                        $bundleConfigs[$bundleName] = new Bundle($bundleName, $bundleElement);
                    }
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

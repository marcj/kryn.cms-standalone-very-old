<?php

namespace Core\Config;

use Core\Kryn;

class Configs implements \IteratorAggregate {

    /**
     * @var DOMElement[]
     */
    private $configElements = array();

    /**
     * @var Config[]
     */
    private $configs = array();

    public function __construct(array $bundles)
    {
        foreach ($bundles as $bundleName) {
            $bundle = Kryn::getBundle($bundleName);
            if (!$bundle) continue;

            $configs = $bundle->getConfig();

            $this->configElements = array_merge_recursive_distinct($this->configElements, $configs);
        }

        $this->configElements = $this->parseConfig($this->configElements);
    }

    /**
     * $configs = $configs[$bundleName][$priority][] = $bundleDomElement;
     *
     * @param array $configs
     *
     * @return array
     */
    public function parseConfig(array $configs){
        $bundleConfigs = array();

        foreach ($configs as $bundleName => $priorities) {
            ksort($priorities); //sort by priority

            foreach ($priorities as $configs) {
                foreach ($configs as $bundleElement) {
                    if ($bundleConfigs[$bundleName]) {
                        $bundleConfigs[$bundleName]->import($bundleElement);
                    } else {
                        $bundleConfigs[$bundleName] = new Config($bundleName, $bundleElement);
                    }
                }
            }
        }
        return $bundleConfigs;
    }

    public function dumpElement(\DOMElement $element)
    {
        $doc = new \DOMDocument();
        $doc->appendChild($doc->importNode($element->cloneNode(true), true));
        return $doc->saveXML();
    }

    /**
     * @param string $bundleName
     *
     * @return Config
     */
    public function getConfig($bundleName) {
        $bundle = Kryn::getBundle($bundleName);
        if (!$bundle){
            return;
        }

        $name = $bundle->getName();
        return $this->configElements[$name];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach ($this->configElements as $config)
        {
            $result[$config->getName()] = $config->toArray();
        }
        return $result;
    }

    /**
     * @return Config[]
     */
    public function getIterator() {
        return new \ArrayIterator($this->configElements);
    }
}

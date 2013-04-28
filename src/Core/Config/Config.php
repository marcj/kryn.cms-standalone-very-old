<?php

namespace Core\Config;

use Admin\Utils;
use Core\Kryn;

class Config extends Model
{

    /**
     * @var Plugin[]
     */
    protected $plugins;

    /**
     * @var Theme[]
     */
    protected $themes;

    /**
     * @var Object[]
     */
    protected $objects;

    /**
     * @var EntryPoint[]
     */
    protected $entryPoints;

    /**
     * @var Asset[]
     */
    protected $adminAssets;

    /**
     * @var string
     */
    protected $bundleName;

    /**
     * @var array
     */
    protected $instanceRelations = array();

    /**
     * @param \DOMElement $bundleName
     * @param \DOMElement $bundleDoc
     */
    public function __construct($bundleName, \DOMElement $bundleDoc = null)
    {
        $this->element = new \DOMDocument();
        $this->bundleName = $bundleName;

        if ($bundleDoc) {
            $this->element->appendChild($this->element->importNode($bundleDoc, true));
        }
    }

    /**
     * @param string $bundleName
     */
    public function setBundleName($bundleName)
    {
        $this->bundleName = $bundleName;
    }

    /**
     * @return string
     */
    public function getBundleName()
    {
        return $this->bundleName;
    }

    /**
     * @return \Core\Bundle
     */
    public function getBundleClass()
    {
        return Kryn::getBundle($this->getBundleName());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBundleClass()->getName(true);
    }

    public function setupObject()
    {
    }

    /**
     * Loads a DOM element into the current configuration.
     *
     * @param \DOMElement $element
     */
    public function load(\DOMElement $element)
    {
        $this->element->appendChild($this->element->importNode($element->cloneNode(true), true));
    }

    /**
     * @return Plugin[]
     */
    public function getPlugins()
    {
        if (null === $this->plugins) {
            $plugins = $this->element->getElementsByTagName('plugin');
            $this->plugins = array();
            foreach ($plugins as $plugin) {
                $this->plugins[] = $this->getModelInstance($plugin);
            }
        }

        return $this->plugins;
    }

    /**
     * @param \DOMNode $node
     */
    public function import(\DOMNode $node)
    {
        if ('bundle' === $node->nodeName) {
            foreach ($node->childNodes as $child) {
                $this->import($child);
            }
        } else if ('#text' !== $node->nodeName) {
            $actualElement = $this->getDirectChild($node->nodeName);
            if ($actualElement) {
                //todo, element(section) already there, so merge both children
            } else {
                //not there yet, just append it
                $this->element->firstChild->appendChild($this->element->importNode($node, true));
            }
        }
    }

    /**
     * @param string $id
     *
     * @return Plugin
     */
    public function getPlugin($id)
    {
        $this->plugins = $this->plugins ?: $this->getPlugins();
        foreach ($this->plugins as $plugin) {
            if ($plugin->getId() == $id){
                return $plugin;
            }
        }
    }

    /**
     * @param string $filter
     * @param bool   $regex
     *
     * @return Asset[]|Assets[]
     */
    public function getAdminAssets($filter = '', $regex = false)
    {
        if (null === $this->adminAssets) {
            $childrenElement   = $this->getDirectChild('admin');
            $this->adminAssets = array();
            if ($childrenElement) {
                $children = $childrenElement->childNodes;
                foreach ($children as $child) {
                    if ('asset' === $child->nodeName || 'assets' == $child->nodeName) {
                        $this->adminAssets[] = $this->getModelInstance($child);
                    }
                }
            }
        }

        if ('' === $filter) {
            return $this->entryPoints;
        } else {
            $result = array();
            if ($regex) {
                $filter = addcslashes($filter, '/');
            } else {
                $filter = preg_quote($filter, '/');
            }

            foreach ($this->adminAssets as $asset) {
                if (preg_match('/' . $filter . '/', $asset->getPath())) {
                    $result[] = $asset;
                }
            }
            return $result;
        }
    }

    /**
     *
     * @param bool   $localPath
     * @param string $filter
     * @param bool   $regex
     *
     * @return string[]
     */
    public function getAdminAssetsPaths($localPath = false, $filter = '', $regex = false)
    {
        $files = array();
        $method = $localPath ? 'getLocalPath' : 'getPath';
        foreach ($this->getAdminAssets($filter, $regex) as $asset) {
            if ($asset instanceof Asset) {
                $files[] = $asset->$method();
            } else if ($asset instanceof Assets) {
                foreach ($asset as $subAsset) {
                    $files[] = $subAsset->$method();
                }
            }
        }
        return $files;
    }

    /**
     * @param string $id
     *
     * @return Theme
     */
    public function getTheme($id)
    {
        $this->themes = $this->themes ?: $this->getThemes();
        foreach ($this->themes as $theme) {
            if ($theme->getId() == $id){
                return $theme;
            }
        }
    }

    /**
     * @param $entryPoints
     */
    public function setEntryPoints($entryPoints)
    {
        $this->entryPoints = $entryPoints;
    }

    /**
     * @return EntryPoint[]
     */
    public function getEntryPoints()
    {
        if (null === $this->entryPoints) {
            $childrenElement   = $this->getDirectChild('entry-points');
            $this->entryPoints = array();
            if ($childrenElement) {
                $children          = $childrenElement->childNodes;
                foreach ($children as $child) {
                    if ('entry-point' === $child->nodeName) {
                        $this->entryPoints[] = $this->getModelInstance($child);
                    }
                }
            }
        }
        return $this->entryPoints;
    }

    public function getEntryPointsArray()
    {
        $entryPoints = array();
        foreach ($this->getEntryPoints() as $entryPoint) {
            $entryPoints[$entryPoint->getPath()] = $entryPoint->toArray();
        }
        return $entryPoints;
    }

    public function getAllEntryPoints(EntryPoint $entryPoint = null)
    {
        $entryPoints = array();

        if ($entryPoint) {
            $subEntryPoints = $entryPoint->getChildren();
        } else {
            $subEntryPoints = $this->getEntryPoints();
        }

        foreach ($subEntryPoints as $subEntryPoint) {
            $entryPoints[$subEntryPoint->getFullPath()] = $subEntryPoint;
            $entryPoints = array_merge($entryPoints, $this->getAllEntryPoints($subEntryPoint));
        }

        return $entryPoints;
    }

    /**
     * @param \DOMNode $node
     * @param          $instance
     */
    public function setInstanceForNode(\DOMNode $node, $instance)
    {
        $this->instanceRelations[spl_object_hash($node)] = $instance;
    }

    /**
     * @param \DOMNode $node
     *
     * @return mixed
     */
    public function getInstanceForNode(\DOMNode $node)
    {
        return $this->instanceRelations[spl_object_hash($node)];
    }

    /**
     * @param \DOMNode $node
     *
     * @return mixed
     */
    public function getModelInstance(\DOMNode $node)
    {
        if ($instance = $this->getInstanceForNode($node)) {
            return $instance;
        }

        $blacklist = array('Config');

        $clazz = char2Camelcase($node->nodeName, '-');
        if (in_array($clazz, $blacklist)) {
            return;
        }
        $clazz = '\Core\Config\\' . $clazz;

        if (class_exists($clazz)) {
            $instance = new $clazz($node, $this);
            $this->setInstanceForNode($node, $instance);
            return $instance;
        }
    }

    /**
     * @param $path Full path, delimited with `/`;
     *
     * @return EntryPoint
     */
    public function getEntryPoint($path)
    {
        $this->entryPoints = $this->entryPoints ?: $this->getEntryPoints();
        $first = (false === ($pos = strpos($path, '/'))) ? $path : substr($path, 0, $pos);

        foreach ($this->entryPoints as $entryPoint) {
            if ($first == $entryPoint->getPath()) {
                if (false !== strpos($path, '/')) {
                    return $entryPoint->getChild(substr($path, $pos + 1));
                } else {
                    return $entryPoint;
                }
            }
        }
    }

    /**
     * @return Object[]
     */
    public function getObjects()
    {
        if (null === $this->objects) {
            $element = $this->getDirectChild('objects');
            $this->objects = array();
            if ($element) {
                foreach ($element->childNodes as $node) {
                    if ('object' === $node->nodeName) {
                        $this->objects[] = $this->getModelInstance($node);
                    }
                }
            }
        }

        return $this->objects;
    }

    /**
     * @param string $id
     *
     * @return Object
     */
    public function getObject($id)
    {
        $this->objects = $this->objects ?: $this->getObjects();
        foreach ($this->objects as $object) {
            if ($object->getId() == $id){
                return $object;
            }
        }
    }

    /**
     * @return Theme[]
     */
    public function getThemes()
    {
        if (null === $this->themes) {
            $themes = $this->element->getElementsByTagName('theme');
            $this->themes = array();
            foreach ($themes as $theme) {
                if ('theme' === $theme->nodeName) {
                    $this->themes[] = $this->getModelInstance($theme);
                }
            }
        }

        return $this->themes;
    }

}
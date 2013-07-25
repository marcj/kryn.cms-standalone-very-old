<?php

namespace Core\Config;

use Admin\Utils;
use Core\Kryn;

class Bundle extends Model
{
    protected $attributes = ['id'];

    protected $id;

    /**
     * @var string
     */
    protected $label;

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
    private $bundleName;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var Event[]
     */
    protected $events;

    /**
     * @var Stream[]
     */
    protected $streams;

    /**
     * @param string      $bundleName
     * @param \DOMElement $bundleDoc
     */
    public function __construct($bundleName = '', \DOMElement $bundleDoc = null)
    {
        $this->element = $bundleDoc;
        $this->bundleName = $bundleName;
        $this->rootName = 'bundle';
    }

    public function toArray($element = null)
    {
        $value = parent::toArray($element);
        $value['name'] = $this->getBundleName();
        return $value;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $path
     * @param bool   $withDefaults
     *
     * @return bool
     *
     * @throws \FileNotWritableException
     */
    public function saveConfig($path, $withDefaults = false)
    {
        $xml = $this->toXml($withDefaults);
        $doc = new \DOMDocument();
        $doc->formatOutput = true;
        $doc->loadXML("<config>$xml</config>");
        $xml = substr($doc->saveXML(), strlen('<?xml version="1.0"?>')+1);
        if ((!file_exists($path) && !is_writable(dirname($path))) || (file_exists($path) && !is_writable($path))) {
            throw new \FileNotWritableException(tf('The file `%s` is not writable.', $path));
        }
        return false !== file_put_contents($path, $xml);
    }

    /**
     * @param string $bundleName
     */
    public function setBundleName($bundleName)
    {
        $this->bundleName = $bundleName;
    }

    /**
     * Returns the bundle name with the 'Bundle' suffix.
     *
     * Example: `CoreBundle`.
     *
     * @return string
     */
    public function getBundleName()
    {
        return $this->bundleName;
    }

    /**
     * Returns the Bundle class object.
     *
     * @return \Core\Bundle
     */
    public function getBundleClass()
    {
        return Kryn::getBundle($this->getBundleName());
    }

    /**
     * Returns the bundle name without the 'Bundle' suffix.
     *
     * Example: `Core`.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBundleClass()->getName(true);
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
                $this->element->appendChild($this->element->ownerDocument->importNode($node, true));
            }
        }
    }

    /**
     * @return Plugin[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @return Plugin[]
     */
    public function getPluginsArray()
    {
        if (null !== $this->plugins) {
            $plugins = [];
            foreach ($this->plugins as $plugin) {
                $plugins[$plugin->getId()] = $plugin->toArray();
            }
            return $plugins;
        }
        return $this->plugins;
    }

    /**
     * @param string $id
     *
     * @return Plugin
     */
    public function getPlugin($id)
    {
        if (null !== $this->plugins) {
            foreach ($this->plugins as $plugin) {
                if ($plugin->getId() == $id) {
                    return $plugin;
                }
            }
        }
    }

    /**
     * @param Plugin[] $plugins
     */
    public function setPlugins(array $plugins)
    {
        $this->plugins = $plugins;
    }



    /**
     * @param Stream[] $streams
     */
    public function setStreams(array $streams)
    {
        $this->streams = $streams;
    }

    /**
     * @return Stream[]
     */
    public function getStreams()
    {
        return $this->streams;
    }

    /**
     * @param string $filter
     * @param bool   $regex
     *
     * @return Asset[]|Assets[]
     */
    public function getAdminAssets($filter = '', $regex = false)
    {
        if ('' === $filter) {
            return $this->adminAssets;
        } else {
            $result = array();
            if ($regex) {
                $filter = addcslashes($filter, '/');
            } else {
                $filter = preg_quote($filter, '/');
            }

            if (null !== $this->adminAssets) {
                foreach ($this->adminAssets as $asset) {
                    if (preg_match('/' . $filter . '/', $asset->getPath())) {
                        $result[] = $asset;
                    }
                }
            }
            return $result;
        }
    }

    /**
     * @param Asset[]|Assets[] $adminAssets
     */
    public function setAdminAssets(array $adminAssets)
    {
        $this->adminAssets = $adminAssets;
    }

    /**
     *
     * @param bool   $localPath   Return the real local accessible path or the defined.
     * @param string $filter      a filter value
     * @param bool   $regex       if you pass a own regex as $filter set this to true
     * @param bool   $compression if true or false it returns only assets with this compression value. null returns all
     *
     * @return string[]
     */
    public function getAdminAssetsPaths($localPath = false, $filter = '', $regex = false, $compression = null)
    {
        $files = array();
        $method = $localPath ? 'getLocalPath' : 'getPath';
        foreach ($this->getAdminAssets($filter, $regex) as $asset) {
            if ($asset instanceof Asset) {
                if (null !== $compression && $compression !== $asset->getCompression()) {
                    continue;
                }
                $files[] = $asset->$method();
            } else if ($asset instanceof Assets) {
                foreach ($asset as $subAsset) {
                    if (null !== $compression && $compression !== $subAsset->getCompression()) {
                        continue;
                    }
                    $files[] = $subAsset->$method();
                }
            }
        }
        return array_unique($files);
    }

    /**
     * @param string $id
     *
     * @return Theme
     */
    public function getTheme($id)
    {
        if (null !== $this->themes) {
            foreach ($this->themes as $theme) {
                if ($theme->getId() == $id) {
                    return $theme;
                }
            }
        }
    }

    /**
     * @param EntryPoint[] $entryPoints
     */
    public function setEntryPoints(array $entryPoints)
    {
        $this->entryPoints = $entryPoints;
    }

    /**
     * @return EntryPoint[]
     */
    public function getEntryPoints()
    {
        return $this->entryPoints;
    }

    /**
     * @return array
     */
    public function getEntryPointsArray()
    {
        if (null !== $this->entryPoints) {
            $entryPoints = array();
            foreach ($this->entryPoints as $entryPoint) {
                $entryPoints[$entryPoint->getPath()] = $entryPoint->toArray();
            }
            return $entryPoints;
        }
    }

    /**
     * @param EntryPoint $entryPoint
     * @return EntryPoint[]
     */
    public function getAllEntryPoints(EntryPoint $entryPoint = null)
    {
        $entryPoints = array();

        if ($entryPoint) {
            $subEntryPoints = $entryPoint->getChildren();
        } else {
            $subEntryPoints = $this->getEntryPoints();
        }

        if (null !== $subEntryPoints) {
            foreach ($subEntryPoints as $subEntryPoint) {
                $entryPoints[$this->getBundleName() . '/' . $subEntryPoint->getFullPath()] = $subEntryPoint;
                $entryPoints = array_merge(
                    $entryPoints,
                    $this->getAllEntryPoints($subEntryPoint)
                );
            }
        }

        return $entryPoints;
    }

    /**
     * @param string $path Full path, delimited with `/`;
     *
     * @return EntryPoint
     */
    public function getEntryPoint($path)
    {
        $first = (false === ($pos = strpos($path, '/'))) ? $path : substr($path, 0, $pos);

        if (null !== $this->entryPoints) {
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
    }

    /**
     * @return Object[]
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * @return array
     */
    public function getObjectsArray()
    {
        if (null !== $this->objects) {
            $objects = array();
            foreach ($this->objects as $object) {
                $objects[strtolower($object->getId())] = $object->toArray();
            }
            return $objects;
        }
    }

    /**
     * @param Object[] $objects
     */
    public function setObjects(array $objects)
    {
        $this->objects = $objects;
    }

    /**
     * @param string $id
     *
     * @return Object
     */
    public function getObject($id)
    {
        if (null !== $this->objects) {
            foreach ($this->objects as $object) {
                if (strtolower($object->getId()) == strtolower($id)) {
                    return $object;
                }
            }
        }
    }

    /**
     * @return Theme[]
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * @param Theme[] $themes
     */
    public function setThemes(array $themes)
    {
        $this->themes = $themes;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param Event[] $events
     */
    public function setEvents(array $events)
    {
        $this->events = $events;
    }

    /**
     * @return Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }


}
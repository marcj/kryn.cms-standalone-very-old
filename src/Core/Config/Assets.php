<?php

namespace Core\Config;

use Core\Kryn;
use Admin\Utils;

/**
 * Class Asset
 *
 * Paths are relative to `@bundlePath/Resources/public`.
 */
class Assets extends Model implements \IteratorAggregate
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var Asset[]
     */
    private $assets;

    /**
     * @var bool
     */
    private $recursive = false;

    public function setupObject()
    {
        $this->path = $this->element->nodeValue;
        $this->setAttributeVar('recursive');
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param boolean $recursive
     */
    public function setRecursive($recursive)
    {
        $this->recursive = $recursive;
    }

    /**
     * @return boolean
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * @return Asset[]
     */
    public function getAssets()
    {
        if (null === $this->assets) {
            preg_match('/(\@[a-zA-Z0-9\-_\.\\\\]+)/', $this->getPath(), $match);

            $bundleName   = $match ? $match[1] : '';
            $prefixPath   = $bundleName ? Kryn::getBundleDir($bundleName) . 'Resources/public/' : '';
            $offset = strlen($prefixPath);

            $path   = Kryn::resolvePath($this->getPath(), 'Resources/public');
            $files  = find($path, '', $this->getRecursive());
            foreach ($files as $file) {
                $asset = new Asset();
                $file = ($bundleName ? $bundleName . '/' : '') . substr($file, $offset);
                $asset->setPath($file);
                $this->assets[] = $asset;
            }
        }

        return $this->assets;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getAssets() ?: array());
    }


}
<?php

namespace Core\Config;

use Admin\Utils;
use Core\Kryn;

/**
 * Class Asset
 *
 * Paths are relative to `
 *
 * @bundlePath/Resources/public`.
 */
class Assets extends Model implements \IteratorAggregate
{
    protected $attributes = ['recursive', 'compression'];

    protected $nodeValueVar = 'path';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var Asset[]
     */
    private $assets;

    /**
     * If assets can be compressed with other equal files (js/css compression)
     *
     * @var bool
     */
    protected $compression = true;

    /**
     * @var bool
     */
    protected $recursive = false;

    /**
     * @param boolean $compression
     */
    public function setCompression($compression)
    {
        $this->compression = filter_var($compression, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return boolean
     */
    public function getCompression()
    {
        return $this->compression;
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

            $bundleName = $match ? $match[1] : '';
            $prefixPath = $bundleName ? Kryn::getBundleDir($bundleName) . 'Resources/public/' : '';
            $offset = strlen($prefixPath);

            $path = Kryn::resolvePath($this->getPath(), 'Resources/public');
            $files = find($path, '', $this->getRecursive());
            foreach ($files as $file) {
                $asset = new Asset();
                $file = ($bundleName ? $bundleName . '/' : '') . substr($file, $offset);
                $asset->setPath($file);
                $asset->setCompression($this->getCompression());
                $this->assets[] = $asset;
            }
        }

        return $this->assets;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->getAssets() ? : array());
    }


}
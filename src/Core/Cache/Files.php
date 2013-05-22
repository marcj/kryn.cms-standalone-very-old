<?php

namespace Core\Cache;

use Core\FAL\Local;
use Core\SystemFile;

class Files implements CacheInterface
{
    private $path;

    private $prefix = '';

    /**
     * if no opcode caches is available, we use JSON, since this is then 1.6-1.9 times faster.
     *
     * @var boolean
     */
    private $useJson = false;

    private $falLayer;

    /**
     * {@inheritdoc}
     */
    public function __construct($pConfig)
    {
        $this->testConfig($pConfig);

        if (!$pConfig['path']) {
            $pConfig['path'] = \Core\Kryn::getTempFolder() . 'object-cache/';
        }
        $this->path = $pConfig['path'];

        if (Controller::getFastestCacheClass() == '\Core\Cache\Files') {
            $this->useJson = true;
        }

        if (substr($this->path, -1) != '/') {
            $this->path .= '/';
        }

        if (isset($pConfig['prefix'])) {
            $this->prefix = $pConfig['prefix'];
        }

        $this->falLayer = new Local('', ['root' => $pConfig['path']]);
    }

    /**
     * {@inheritdoc}
     */
    public function testConfig($pConfig)
    {
        if (!$pConfig['path']) {
            $pConfig['path'] = \Core\Kryn::getTempFolder() . 'object-cache/';
        }

        $this->falLayer = new Local('', ['root' => $pConfig['path']]);

        if (!$this->falLayer->createFolder('.')) {
            throw new \Exception('Can not create cache folder: ' . $pConfig['path']);
        }

        return true;
    }

    public function setPrefix($pPrefix)
    {
        $this->prefix = $pPrefix;
    }

    public function getPath($pKey)
    {
        return $this->path . $this->prefix . urlencode($pKey) . ($this->useJson ? '.json' : '.php');
    }

    public function getInternalPath($pKey)
    {
        return $this->prefix . urlencode($pKey) . ($this->useJson ? '.json' : '.php');
    }

    /**
     * {@inheritdoc}
     */
    public function get($pKey)
    {
        $path = $this->getPath($pKey);

        if (!file_exists($path)) {
            return false;
        }
        $h = fopen($path, 'r');

        $maxTries = 400; //wait max. 2 seconds, otherwise force it
        $tries = 0;
        while (!flock($h, LOCK_SH) and $tries <= $maxTries) {
            usleep(1000 * 5); //5ms
            $tries++;
        }

        if (!$this->useJson) {
            $value = include($path);
        } else {
            $value = '';
            while (!feof($h)) {
                $value .= fread($h, 8192);
            }
        }

        flock($h, LOCK_UN);
        fclose($h);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function set($pKey, $pValue, $pTimeout = 0)
    {
        $path = $this->getPath($pKey);
        $this->falLayer->createFile($this->getInternalPath($pKey));

        if (!$this->useJson) {
            $pValue = '<' . "?php \nreturn " . var_export($pValue, true) . ";\n";
        } else {
            $pValue = json_encode($pValue);
        }

        $h = fopen($path, 'w');

        if (!$h) {
            return false;
        }

        $maxTries = 400; //wait max. 2 seconds, otherwise force it
        $tries = 0;
        while (!flock($h, LOCK_EX) and $tries <= $maxTries) {
            usleep(1000 * 5); //5ms
            $tries++;
        }

        fwrite($h, $pValue);
        flock($h, LOCK_UN);
        fclose($h);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($pKey)
    {
        $path = $this->getPath($pKey);

        return @unlink($path);
    }

}

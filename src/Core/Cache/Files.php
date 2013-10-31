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
    public function __construct($config)
    {
        $this->testConfig($config);

        if (!$config['path']) {
            $config['path'] = \Core\Kryn::getTempFolder() . 'object-cache/';
        }
        $this->path = $config['path'];

        if (Controller::getFastestCacheClass() == '\Core\Cache\Files') {
            $this->useJson = true;
        }

        if (substr($this->path, -1) != '/') {
            $this->path .= '/';
        }

        if (isset($config['prefix'])) {
            $this->prefix = $config['prefix'];
        }

        $this->falLayer = new Local('', ['root' => $config['path']]);
    }

    /**
     * {@inheritdoc}
     */
    public function testConfig($config)
    {
        $this->falLayer = new Local('', ['root' => $config['path']]);

        if (!$this->falLayer->createFolder('.')) {
            throw new \Exception('Can not create cache folder: ' . $config['path']);
        }

        return true;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    public function getPath($key)
    {
        return $this->path . $this->prefix . urlencode($key) . ($this->useJson ? '.json' : '.php');
    }

    public function getInternalPath($key)
    {
        return $this->prefix . urlencode($key) . ($this->useJson ? '.json' : '.php');
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $path = $this->getPath($key);

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
    public function set($key, $value, $timeout = 0)
    {
        $path = $this->getPath($key);
        $this->falLayer->createFile($this->getInternalPath($key));

        if (!$this->useJson) {
            $value = '<' . "?php \nreturn " . var_export($value, true) . ";\n";
        } else {
            $value = json_encode($value);
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

        fwrite($h, $value);
        flock($h, LOCK_UN);
        fclose($h);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $path = $this->getPath($key);

        return @unlink($path);
    }

}

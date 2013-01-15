<?php

namespace Core\Cache;

use Core\SystemFile;

class Files implements CacheInterface {

    private $path;

    private $prefix = '';

    /**
     * if no opcode caches is available, we use JSON, since this is then 1.6-1.9 times faster.
     * 
     * @var boolean
     */
    private $useJson = false;

    /**
     * {@inheritdoc}
     */
    public function __construct($pConfig){


        $this->testConfig($pConfig);

        if (!$pConfig['path']) $pConfig['path'] = \Core\Kryn::getTempFolder().'cache-object/';
        $this->path = $pConfig['path'];

        if (Controller::getFastestCacheClass() == '\Core\Cache\Files')
            $this->useJson = true;

        if (substr($this->path, -1) != '/')
            $this->path .= '/';

        if ($pConfig['prefix'])
            $this->prefix = $pConfig['prefix'];

    }

    /**
     * {@inheritdoc}
     */
    public function testConfig($pConfig){
        if (!$pConfig['path']) $pConfig['path'] = \Core\Kryn::getTempFolder().'cache-object/';

        if (!is_dir($pConfig['path'])) {
            if (!mkdir($pConfig['path'])) {
                throw new \Exception('Can not create cache folder: ' . $pConfig['path']);
            }
        }

        if (!is_writable($pConfig['path']))
            throw new \Exception('Cache folder is not writable: ' . $pConfig['path']);

        return true;
    }

    public function setPrefix($pPrefix){
        $this->prefix = $pPrefix;
    }

    public function getPath($pKey){
        return $this->path . $this->prefix . urlencode($pKey) . ($this->useJson ? '.json' : '.php');
    }

    /**
     * {@inheritdoc}
     */
    public function get($pKey){
        $path = $this->getPath($pKey);
        if (!file_exists($path)) return false;

        if ($this->useJson){
            $json = file_get_contents($path);
            return json_decode($json, true);
        }

        return include($path);
    }

    /**
     * {@inheritdoc}
     */
    public function set($pKey, $pValue, $pTimeout = 0){

        $path = $this->getPath($pKey);

        if ($this->useJson){
            return file_put_contents($path, json_encode($pValue));
        }

        return file_put_contents($path, '<' . "?php \nreturn " . var_export($pValue, true) . ";\n ?" . '>');

    }

    /**
     * {@inheritdoc}
     */
    public function delete($pKey){
        $path = $this->getPath($pKey);
        return @unlink($path);
    }

}
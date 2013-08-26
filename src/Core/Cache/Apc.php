<?php

namespace Core\Cache;

class Apc implements CacheInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        $this->testConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return apc_fetch($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $timeout = null)
    {
        return apc_store($key, $value, $timeout);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return apc_delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function testConfig($config)
    {
        if (!function_exists('apc_store')) {
            throw new \Exception('The module Apc is not activated in your PHP environment.');
        }

        return true;
    }
}

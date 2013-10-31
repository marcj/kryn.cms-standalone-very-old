<?php

namespace Core\Cache;

class XCache implements CacheInterface
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
    public function testConfig($config)
    {
        if (!function_exists('xcache_set')) {
            throw new \Exception('The module Apc is not activated in your PHP environment.');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return xcache_get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $timeout = null)
    {
        return xcache_set($key, $value, $timeout);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return xcache_unset($key);
    }
}

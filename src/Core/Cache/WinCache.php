<?php

namespace Core\Cache;

class WinCache implements CacheInterface
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
        if (!function_exists('wincache_ucache_get')) {
            throw new \Exception('The PECL wincache >= 1.1.0 is not activated in your PHP environment.');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return wincache_ucache_get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $timeout = null)
    {
        return wincache_ucache_set($key, $value, $timeout);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        return wincache_ucache_delete($key);
    }
}

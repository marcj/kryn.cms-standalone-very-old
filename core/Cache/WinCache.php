<?php

namespace core\Cache;

class WinCache implements CacheInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct($pConfig)
    {
        $this->testConfig($pConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function testConfig($pConfig)
    {
        if (!function_exists('wincache_ucache_get'))
            throw new \Exception('The PECL wincache >= 1.1.0 is not activated in your PHP environment.');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($pKey)
    {
        return wincache_ucache_get($pKey);
    }

    /**
     * {@inheritdoc}
     */
    public function set($pKey, $pValue, $pTimeout = null)
    {
        return wincache_ucache_set($pKey, $pValue, $pTimeout);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($pKey)
    {
        return wincache_ucache_delete($pKey);
    }
}

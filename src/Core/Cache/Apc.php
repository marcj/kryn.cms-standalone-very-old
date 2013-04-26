<?php

namespace Core\Cache;

class Apc implements CacheInterface
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
    public function get($pKey)
    {
        return apc_fetch($pKey);
    }

    /**
     * {@inheritdoc}
     */
    public function set($pKey, $pValue, $pTimeout = null)
    {
        return apc_store($pKey, $pValue, $pTimeout);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($pKey)
    {
        return apc_delete($pKey);
    }

    /**
     * {@inheritdoc}
     */
    public function testConfig($pConfig)
    {
        if (!function_exists('apc_store'))
            throw new \Exception('The module Apc is not activated in your PHP environment.');

        return true;
    }
}

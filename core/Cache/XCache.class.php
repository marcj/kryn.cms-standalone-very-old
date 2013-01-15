<?php

namespace Core\Cache;

class XCache implements CacheInterface {


    /**
     * {@inheritdoc}
     */
	public function __construct($pConfig){

        $this->testConfig($pConfig);

	}

    /**
     * {@inheritdoc}
     */
    public function testConfig($pConfig){
        if (!function_exists('xcache_set'))
            throw new \Exception('The module Apc is not activated in your PHP environment.');

        return true;
    }

    /**
     * {@inheritdoc}
     */
	public function get($pKey){
		return xcache_get($pKey);
	}

    /**
     * {@inheritdoc}
     */
	public function set($pKey, $pValue, $pTimeout = null){
        return xcache_set($pKey, $pValue, $pTimeout);
	}

    /**
     * {@inheritdoc}
     */
	public function delete($pKey){
        return xcache_unset($pKey);
	}
}
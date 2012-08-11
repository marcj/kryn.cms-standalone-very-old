<?php

namespace Core\Cache;

class XCache implements CacheInterface {

	private $connection;

	public function __construct($pConfig){

        if (!function_exists('xcache_set'))
        	throw new \Exception('The module Apc is not activated in your PHP environment.');

	}

	public function get($pKey){
		return xcache_get($pKey);
	}

	public function set($pKey, $pValue, $pTimeout = null){
        return xcache_set($pKey, $pValue, $pTimeout);
	}

	public function delete($pKey){
        return xcache_unset($pKey);
	}
}
<?php

namespace Core\Cache;

class WinCache implements CacheInterface {

	private $connection;

	public function __construct($pConfig){

        if (!function_exists('wincache_ucache_get'))
        	throw new \Exception('The PECL wincache >= 1.1.0 is not activated in your PHP environment.');

	}

	public function get($pKey){
		return wincache_ucache_get($pKey);
	}

	public function set($pKey, $pValue, $pTimeout = null){
        return wincache_ucache_set($pKey, $pValue, $pTimeout);
	}

	public function delete($pKey){
        return wincache_ucache_delete($pKey);
	}
}
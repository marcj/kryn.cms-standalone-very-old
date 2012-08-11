<?php

namespace Core\Cache;

class Apc implements CacheInterface {

	private $connection;

	public function __construct($pConfig){

        if (!function_exists('apc_store'))
        	throw new \Exception('The module Apc is not activated in your PHP environment.');

	}

	public function get($pKey){
		return apc_fetch($pKey);
	}

	public function set($pKey, $pValue, $pTimeout = null){
        return apc_store($pKey, $pValue, $pTimeout);
	}

	public function delete($pKey){
        return apc_delete($pKey);
	}
}
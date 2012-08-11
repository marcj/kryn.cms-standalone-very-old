<?php


namespace Core\Cache;

interface CacheInterface {

	public function __construct($pConfig);

	public function get($pKey);

	public function set($pKey, $pValue, $pTimeout = null);

	public function delete($pKey);

}
<?php

namespace Core\Cache;

class Redis implements CacheInterface {

	private $connection;

	public function __construct($pConfig){

        if (!class_exists('Redis'))
        	throw new \Exception('The module Redis is not activated in your PHP environment.');
        

        if (!$pConfig['servers'])
        	throw new \Exception('No redis servers set.');	
        

        $this->connection = new Redis;

        foreach ($pConfig['servers'] as $server) {
            $this->connection->connect($server['ip'], $server['port'] + 0);
        }

        $this->connection->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);

	}

	public function get($pKey){
		return $this->connection->get($pKey);
	}

	public function set($pKey, $pValue, $pTimeout = null){
		return $this->connection->setex($pCode, $pTimeout, $pValue);
	}

	public function delete($pKey){
		$this->connection->delete($pKey);
	}
}
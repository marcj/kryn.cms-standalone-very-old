<?php

namespace Core\Cache;

class Memcached implements CacheInterface {

	private $connection;

	public function __construct($pConfig){

        if (!$pConfig['servers'])
        	throw new \Exception('No redis servers set.');	
        

        if (class_exists('Memcache'))
            $this->connection = new Memcache;

        else if (class_exists('Memcached'))
            $this->connection = new Memcached;

		else
            throw new \Exception('The module memcache or memcached is not activated in your PHP environment.');


        foreach ($this->config['servers'] as $server) {
            $this->connection->addServer($server['ip'], $server['port']+0);
        }
	}

	public function get($pKey){
		return $this->connection->get($pKey);
	}

	public function set($pKey, $pValue, $pTimeout = null){
		if ($this->connection instanceof Memcache)
            return $this->connection->set($pKey, $pValue, 0, $pTimeout);
        else
            return $this->connection->set($pKey, $pValue, $pTimeout);
        
	}

	public function delete($pKey){
		$this->connection->delete($pKey);
	}
}
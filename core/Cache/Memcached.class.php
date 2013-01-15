<?php

namespace Core\Cache;

class Memcached implements CacheInterface {

	private $connection;

    private $noServerTest = false;

    /**
     * {@inheritdoc}
     */
    public function __construct($pConfig){

        $this->noServerTest = true;
        $this->testConfig($pConfig);
        $this->noServerTest = false;


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

    /**
     * {@inheritdoc}
     */
    public function testConfig($pConfig){


        if (!(class_exists('Memcache') || class_exists('Memcached')))
            throw new \Exception('The php module memcache or memcached is not activated in your PHP environment.');

        if (!$pConfig['servers'])
            throw new \Exception('No servers set.');

        //TODO, test if all servers are reachable
        if (!$this->noServerTest){

        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
	public function get($pKey){
		return $this->connection->get($pKey);
	}

    /**
     * {@inheritdoc}
     */
	public function set($pKey, $pValue, $pTimeout = null){
		if ($this->connection instanceof Memcache)
            return $this->connection->set($pKey, $pValue, 0, $pTimeout);
        else
            return $this->connection->set($pKey, $pValue, $pTimeout);
        
	}

    /**
     * {@inheritdoc}
     */
	public function delete($pKey){
		$this->connection->delete($pKey);
	}
}
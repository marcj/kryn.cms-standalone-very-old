<?php

namespace Core\Cache;

class Memcached implements CacheInterface
{
    private $connection;

    private $noServerTest = false;

    /**
     * {@inheritdoc}
     */
    public function __construct($config)
    {
        $this->noServerTest = true;
        $this->testConfig($config);
        $this->noServerTest = false;

        if (class_exists('Memcache')) {
            $this->connection = new Memcache;
        } else if (class_exists('Memcached')) {
            $this->connection = new Memcached;
        } else {
            throw new \Exception('The module memcache or memcached is not activated in your PHP environment.');
        }

        foreach ($this->config['servers'] as $server) {
            $this->connection->addServer($server['ip'], $server['port'] + 0);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function testConfig($config)
    {
        if (!(class_exists('Memcache') || class_exists('Memcached'))) {
            throw new \Exception('The php module memcache or memcached is not activated in your PHP environment.');
        }

        if (!$config['servers']) {
            throw new \Exception('No servers set.');
        }

        //TODO, test if all servers are reachable
        if (!$this->noServerTest) {

        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->connection->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $timeout = null)
    {
        if ($this->connection instanceof Memcache) {
            return $this->connection->set($key, $value, 0, $timeout);
        } else {
            return $this->connection->set($key, $value, $timeout);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->connection->delete($key);
    }
}

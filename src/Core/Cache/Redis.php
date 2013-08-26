<?php

namespace Core\Cache;

class Redis implements CacheInterface
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

        $this->connection = new Redis();

        foreach ($config['servers'] as $server) {
            $this->connection->connect($server['ip'], $server['port'] + 0);
        }

        $this->connection->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);

    }

    /**
     * {@inheritdoc}
     */
    public function testConfig($config)
    {
        if (!$config['servers']) {
            throw new \Exception('No redis servers set.');
        }

        if (!class_exists('Redis')) {
            throw new \Exception('The module Redis is not activated in your PHP environment.');
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
        return $this->connection->setex($key, $timeout, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->connection->delete($key);
    }
}

<?php

namespace Core\Cache;

class Redis implements CacheInterface
{
    private $connection;

    private $noServerTest = false;

    /**
     * {@inheritdoc}
     */
    public function __construct($pConfig)
    {
        $this->noServerTest = true;
        $this->testConfig($pConfig);
        $this->noServerTest = false;

        $this->connection = new Redis();

        foreach ($pConfig['servers'] as $server) {
            $this->connection->connect($server['ip'], $server['port'] + 0);
        }

        $this->connection->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);

    }

    /**
     * {@inheritdoc}
     */
    public function testConfig($pConfig)
    {
        if (!$pConfig['servers']) {
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
    public function get($pKey)
    {
        return $this->connection->get($pKey);
    }

    /**
     * {@inheritdoc}
     */
    public function set($pKey, $pValue, $pTimeout = null)
    {
        return $this->connection->setex($pKey, $pTimeout, $pValue);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($pKey)
    {
        $this->connection->delete($pKey);
    }
}

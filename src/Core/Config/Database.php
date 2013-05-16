<?php

namespace Core\Config;

class Database extends Model
{
    protected $docBlocks = [
        'prefix' => 'All tables will be prefixed with this string. Best practise is to suffix it with a underscore.
    Examples: dev_, domain_ or prod_',
        'protectTables' => 'The ORM we use deletes all tables not related to the objects, so enter here your tables
    which should be excluded from this. One table name per line. (Or better do a database-reverse)'
    ];

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var string
     */
    protected $protectTables;

    /**
     * @var Connection[]
     */
    protected $connections;

    /**
     * @param Connection[] $connections
     */
    public function setConnections(array $connections)
    {
        $this->connections = $connections;
    }

    /**
     * @return Connection[]
     */
    public function getConnections()
    {
        return $this->connections;
    }

    public function addConnection(Connection $connection)
    {
        $this->connections[] = $connection;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $protectTables
     */
    public function setProtectTables($protectTables)
    {
        $this->protectTables = $protectTables;
    }

    /**
     * @return string
     */
    public function getProtectTables()
    {
        return $this->protectTables;
    }
}
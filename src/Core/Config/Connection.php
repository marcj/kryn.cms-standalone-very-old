<?php

namespace Core\Config;

class Connection extends Model
{
    protected $rootName = 'connection';

    protected $attributes = ['type', 'persistent'];

    protected $docBlocks = [
        'server' => 'Can be a IP or a hostname. For SQLite enter here the path to the file.',
        'name' => 'The schema/database name'
    ];

    protected $docBlock = '
        type: mysql|pgsql|sqlite (the pdo driver name)
        persistent: true|false (if the connection should be persistent)';

    /**
     * @var string
     */
    protected $type = 'master';

    /**
     * @var bool
     */
    protected $persistent = false;

    /**
     * @var string
     */
    protected $server;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @param boolean $persistent
     */
    public function setPersistent($persistent)
    {
        $this->persistent = $persistent;
    }

    /**
     * @return boolean
     */
    public function getPersistent()
    {
        return $this->persistent;
    }

    /**
     * @param string $rootName
     */
    public function setRootName($rootName)
    {
        $this->rootName = $rootName;
    }

    /**
     * @return string
     */
    public function getRootName()
    {
        return $this->rootName;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $server
     */
    public function setServer($server)
    {
        $this->server = $server;
    }

    /**
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

}
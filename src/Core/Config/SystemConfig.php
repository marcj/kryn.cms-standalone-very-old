<?php

namespace Core\Config;

class SystemConfig extends Model {

    protected $rootName = 'config';

    /**
     * @var Database
     */
    protected $database;

    /**
     * @var SystemConfigFile
     */
    protected $file;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var
     */
    protected $errors;

    /**
     * @var string
     */
    protected $systemTitle;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var string[]
     */
    protected $bundles;

    /**
     * @var SystemConfigClient
     */
    protected $client;

    /**
     * @var SystemMountPoint[]
     */
    protected $mountPoints;

    /**
     * @param \string[] $bundles
     */
    public function setBundles($bundles)
    {
        $this->bundles = $bundles;
    }

    /**
     * @return \string[]
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * @param \Core\Config\SystemConfigCache $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return \Core\Config\SystemConfigCache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param \Core\Config\SystemConfigClient $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return \Core\Config\SystemConfigClient
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param \Core\Config\SystemConfigFile $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return \Core\Config\SystemConfigFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param \Core\Config\SystemMountPoint[] $mountPoints
     */
    public function setMountPoints($mountPoints)
    {
        $this->mountPoints = $mountPoints;
    }

    /**
     * @return \Core\Config\SystemMountPoint[]
     */
    public function getMountPoints()
    {
        return $this->mountPoints;
    }

    /**
     * @param string $systemTitle
     */
    public function setSystemTitle($systemTitle)
    {
        $this->systemTitle = $systemTitle;
    }

    /**
     * @return string
     */
    public function getSystemTitle()
    {
        return $this->systemTitle;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param Database $database
     */
    public function setDatabase(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @return Database
     */
    public function getDatabase()
    {
        return $this->database;
    }

}
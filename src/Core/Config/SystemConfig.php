<?php

namespace Core\Config;

class SystemConfig extends Model {

    protected $rootName = 'config';

    protected $docBlocks = [
        'timezone' => '
    IMPORTANT: Set this to your php timezone.
    see: http://www.php.net/manual/en/timezones.php
    ',
        'systemTitle' => 'The system title of this installation.',
        'bundles' => '
    A list of installed bundles. Enter here the PHP FQDN (Will be resolved through PSR-0 and then loaded)

    Example:
        <bundle>Publication\PublicationBundle</bundle>
    '
    ];

    protected $arrayIndexNames = [
        'bundles' => 'bundle'
    ];

    /**
     * @var Database
     */
    protected $database;

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
     * @var FilePermission
     */
    protected $file;

    /**
     * @param string[] $bundles
     */
    public function setBundles(array $bundles)
    {
        $this->bundles = $bundles;
    }

    /**
     * @return string[]
     */
    public function getBundles()
    {
        return $this->bundles;
    }

    /**
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Errors $errors
     */
    public function setErrors(Errors $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return Errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param FilePermission $file
     */
    public function setFile(FilePermission $file)
    {
        $this->file = $file;
    }

    /**
     * @return FilePermission
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
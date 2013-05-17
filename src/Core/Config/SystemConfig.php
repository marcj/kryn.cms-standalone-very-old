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
    ',
        'adminUrl' => 'Defines under which url the backend is. Default is http://<domain>/kryn. where `kryn` is the `adminUrl`.',
        'email' => 'Is displayed as the administrator\'s email in error messages etc.',
        'tempDir' => 'A directory path where the system stores temp files. Relative to web root. E.g `app/cache/` or `/tmp/`.',
        'id' => 'A installation id. If you have several kryn instances you should define a unique one. Gets defines through the installer.',
        'passwordHashKey' => 'This is a key generated through the installation process. You should not change it!
    The system needs this key to decrypt the passwords in the users database.'
    ];

    protected $arrayIndexNames = [
        'bundles' => 'bundle'
    ];

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $systemTitle;

    /**
     * @var string
     */
    protected $adminUrl = 'kryn';

    /**
     * @var string
     */
    protected $tempDir = 'app/cache';

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @var string
     */
    protected $passwordHashKey;

    /**
     * @var string[]
     */
    protected $bundles;

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
        if (null === $this->client) {
            $this->client = new Client();
        }
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

    /**
     * @param string $passwordHashKey
     */
    public function setPasswordHashKey($passwordHashKey)
    {
        $this->passwordHashKey = $passwordHashKey;
    }

    /**
     * @return string
     */
    public function getPasswordHashKey()
    {
        return $this->passwordHashKey;
    }

    /**
     * @param string $tempDir
     */
    public function setTempDir($tempDir)
    {
        $this->tempDir = $tempDir;
    }

    /**
     * @return string
     */
    public function getTempDir()
    {
        return $this->tempDir;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $adminUrl
     */
    public function setAdminUrl($adminUrl)
    {
        $this->adminUrl = $adminUrl;
    }

    /**
     * @return string
     */
    public function getAdminUrl()
    {
        return $this->adminUrl;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
}
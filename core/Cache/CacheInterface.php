<?php


namespace Core\Cache;

interface CacheInterface {

    /**
     * Initialize the class.
     *
     * @param array $pConfig
     */
    public function __construct($pConfig);

    /**
     * Gets the data for a key.
     *
     * @param string $pKey
     * @return mixed
     */
    public function get($pKey);

    /**
     * Sets data for a key with a timeout.
     *
     * @param string $pKey
     * @param mixed  $pValue
     * @param int    $pTimeout
     * @return boolean
     */
    public function set($pKey, $pValue, $pTimeout = null);

    /**
     * Deletes data for a key.
     *
     * @param string $pKey
     */
    public function delete($pKey);

    /**
     * Test the cache driver whether the config values are correct and useable or not.
     * This should also check whether the driver can be used in general or not (like if a necessary php module
     * is loaded or not).
     *
     * @param array $pConfig
     * @return boolean returns true if everything is fine, if not it should throw an exception with the detailed issue.
     */
    public function testConfig($pConfig);

}
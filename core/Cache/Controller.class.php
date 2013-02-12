<?php

/*
* This file is part of Kryn.cms.
*
* (c) Kryn.labs, MArc Schmidt <marc@Kryn.org>
*
* To get the full copyright and license informations, please view the
* LICENSE file, that was distributed with this source code.
*
*/

namespace Core\Cache;

/**
 * Cache controller
 */
class Controller {

    /**
     * Contains the current class instance.
     * 
     * @type Object
     */
    public $instance;


    /**
     * All gets/sets will be cached in this array for faster access
     * during multiple get() calls on the same key.
     *
     * @var array
     */
    public $cache = array();

    /**
     * This activates the invalidate() mechanism
     *
     * @type bool
     *
     * If activated, each time get() is called, the function searched
     * for parents based on a exploded string by '/'. If a parent is
     * found is a invalidated cache, the call is ignored and false will be returned.
     * Example: call get('workspace/tables/tableA')
     *          => checks 'workspace/tables' for invalidating (getInvalidate('workspace/tables'))
     *          => if 'workspace/tables' was flagged as invalidate (invalidate('workspace/tables')), return false
     *          => checks 'workspace' for invalidating (getInvalidate('workspace'))
     *          => if 'workspace' was flagged as invalidate (invalidate('workspace')), return false
     * So you can invalidate multiple keys with just one call.
     */
    public $withInvalidationChecks = true;


    /**
     * The class name.
     *
     * @var string
     */
    public $class;

    /**
     * Constructor.
     *
     * @param string  $pClass                   The class of the cache service.
     * @param array   $pConfig                  Contains config values.
     *                                          memcached and redis: array(
     *                                              'servers' => array(
     *                                                  array('ip' => '12.12.12.12', 'port' => 6379
     *                                                  array('ip' => '12.12.12.13', 'port' => 6379
     *                                              )
     *                                            )
     *                                          files: array('files_path' => '<path to store the cached files')
     * @param bool    $pWithInvalidationChecks  Activates the invalidating mechanism
     *
     * @throws \Exception
     */
    function __construct($pClass = '\Core\Cache\File', $pConfig = array(), $pWithInvalidationChecks = true) {

        $this->withInvalidationChecks = $pWithInvalidationChecks;
        $this->class = $pClass;

        if (class_exists($pClass)){
            $this->instance = new $pClass($pConfig);
        } else {
            throw new \Exception(tf('The class `%s` does not exist.', $pClass));
        }

    }

    /**
     * @return string
     */
    public function getClass() {
        return $this->class;
    }


    /**
     * Detects the fastest available cache on current machine.
     * 
     * @return string
     */
    public static function getFastestCacheClass(){

        $class = '\Core\Cache\\';
        
        if (function_exists('apc_store'))
            return $class.'Apc';

        if (function_exists('xcache_set'))
            return $class.'XCache';

        if (function_exists('wincache_ucache_get'))
            return $class.'WinCache';

        return $class.'Files';
    }

    /**
     * Returns data of the specified cache-key.
     *
     * @param string $pKey
     * @param bool   $pWithoutValidationCheck
     *
     * @return ref to data
     */
    public function &get($pKey, $pWithoutValidationCheck = false) {

        if (!$this->cache[$pKey]){
            $this->cache[$pKey] = $this->instance->get($pKey);
        }

        if (!$this->cache[$pKey]) {
            return null;
        }

        if ($this->withInvalidationChecks && !$pWithoutValidationCheck) {

            if ($pWithoutValidationCheck == true) {
                if (!$this->cache[$pKey]['value'] || !$this->cache[$pKey]['time']
                    || $this->cache[$pKey]['timeout'] < microtime(true)) {
                    return false;
                }
                return $this->cache[$pKey]['value'];
            }

            //valid cache
            //search if a parent has been flagged as invalid
            if (strpos($pKey, '/') !== false) {

                $parents = explode('/', $pKey);
                $code = '';
                if (is_array($parents)) {
                    foreach ($parents as $parent) {
                        $code .= $parent;
                        $invalidateTime = $this->getInvalidate($code);
                        if ($invalidateTime && $invalidateTime > $this->cache[$pKey]['time']) {
                            return false;
                        }
                        $code .= '/';
                    }
                }
            }
        }

        if ($this->withInvalidationChecks && !$pWithoutValidationCheck)
            return $this->cache[$pKey]['value'];
        else
            return $this->cache[$pKey];

    }

    /**
     * Returns the invalidation time.
     *
     * @param string $pKey
     * @return string
     */
    public function getInvalidate($pKey) {
        return $this->get('invalidate-'.$pKey, true);
    }


    /**
     * Marks a code as invalidate until $pTime.
     *
     * @param string   $pKey
     * @param bool|int $pTime
     */
    public function invalidate($pKey, $pTime = null) {
        $this->cache['invalidate-'.$pKey] = $pTime;
        return $this->instance->set('invalidate-'.$pKey, $pTime, 99999999, true);
    }

    /**
     * Stores data to the specified cache-key.
     *
     * If you want to save php class objects, you should serialize it before.
     * 
     * @param string   $pKey
     * @param mixed    $pValue
     * @param int      $pTimeout In seconds. Default is one hour
     * @param bool     $pWithoutValidationData
     *
     * @return boolean
     */
    public function set($pKey, $pValue, $pTimeout = 3600, $pWithoutValidationData = false) {

        if (!$pKey) return false;

        if (!$pTimeout)
            $pTimeout = 3600;

        if ($this->withInvalidationChecks && !$pWithoutValidationData) {
            $pValue = array(
                'timeout' => microtime(true)+$pTimeout,
                'time' => microtime(true),
                'value' => $pValue
            );
        }

        $this->cache[$pKey] = $pValue;

        return $this->instance->set($pKey, $pValue, $pTimeout);
    }

    /**
     * Deletes the cache for specified cache-key.
     *
     * @param string $pKey
     * @return bool
     */
    public function delete($pKey) {
        unset($this->cache[$pKey]);
        return $this->instance->delete($pKey);
    }
}

?>
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

use Core\Config\Cache;

/**
 * Cache controller
 */
class Controller
{
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
     * @param Cache $cacheConfig               The class of the cache service.
     * @param bool   $withInvalidationChecks  Activates the invalidating mechanism
     *
     * @throws \Exception
     */
    public function __construct(Cache $cacheConfig, $withInvalidationChecks = true)
    {
        $this->withInvalidationChecks = $withInvalidationChecks;
        $this->class = $cacheConfig->getClass();

        if (class_exists($this->class)) {
            $class = $this->class;
            $this->instance = new $class($cacheConfig->getOptions()->toArray());
        } else {
            throw new \Exception(tf('The class `%s` does not exist.', $this->class));
        }

    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Detects the fastest available cache on current machine.
     *
     * @return Cache
     */
    public static function getFastestCacheClass()
    {
        $class = '\Core\Cache\\';

        if (function_exists('apc_store')) {
            $class .= 'Apc';
        } else if (function_exists('xcache_set')) {
            $class .= 'XCache';
        } else if (function_exists('wincache_ucache_get')) {
            $class .= 'WinCache';
        } else {
            $class .= 'Files';
        }

        $cacheConfig = new Cache();
        $cacheConfig->setClass($class);
        return $cacheConfig;
    }

    /**
     * Returns data of the specified cache-key.
     *
     * @param string $key
     * @param bool   $withoutValidationCheck
     *
     * @return ref to data
     */
    public function &get($key, $withoutValidationCheck = false)
    {

        if (!isset($this->cache[$key])) {
            $time = microtime(true);
            $this->cache[$key] = $this->instance->get($key);
            \Core\Utils::$latency['cache'][] = microtime(true) - $time;
        }

        if (!$this->cache[$key]) {
            $rv = null;
            return $rv;
        }

        if ($this->withInvalidationChecks && !$withoutValidationCheck) {

            if ($withoutValidationCheck == true) {
                if (!$this->cache[$key]['value'] || !$this->cache[$key]['time']
                    || $this->cache[$key]['timeout'] < microtime(true)
                ) {
                    return null;
                }

                return $this->cache[$key]['value'];
            }

            //valid cache
            //search if a parent has been flagged as invalid
            if (strpos($key, '/') !== false) {

                $parents = explode('/', $key);
                $code = '';
                if (is_array($parents)) {
                    foreach ($parents as $parent) {
                        $code .= $parent;
                        $invalidateTime = $this->getInvalidate($code);
                        if ($invalidateTime && $invalidateTime > $this->cache[$key]['time']) {
                            return null;
                        }
                        $code .= '/';
                    }
                }
            }
        }

        if ($this->withInvalidationChecks && !$withoutValidationCheck) {
            if (is_array($this->cache[$key])) {
                return $this->cache[$key]['value'];
            } else {
                return null;
            }
        } else {
            return $this->cache[$key];
        }

    }

    /**
     * Returns the invalidation time.
     *
     * @param  string $key
     *
     * @return string
     */
    public function getInvalidate($key)
    {
        return $this->get('invalidate-' . $key, true);
    }

    /**
     * Marks a code as invalidate until $time.
     *
     * @param string   $key
     * @param bool|int $time
     */
    public function invalidate($key, $time = null)
    {
        $this->cache['invalidate-' . $key] = $time;

        $time2 = microtime(true);
        $result = $this->instance->set('invalidate-' . $key, $time, 99999999, true);
        \Core\Utils::$latency['cache'][] = microtime(true) - $time2;
        return $result;
    }

    /**
     * Stores data to the specified cache-key.
     *
     * If you want to save php class objects, you should serialize it before.
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $lifeTime               In seconds. Default is one hour
     * @param bool   $withoutValidationData
     *
     * @return boolean
     */
    public function set($key, $value, $lifeTime = 3600, $withoutValidationData = false)
    {
        if (!$key) {
            return false;
        }

        if (!$lifeTime) {
            $lifeTime = 3600;
        }

        if ($this->withInvalidationChecks && !$withoutValidationData) {
            $value = array(
                'timeout' => microtime(true) + $lifeTime,
                'time' => microtime(true),
                'value' => $value
            );
        }

        $this->cache[$key] = $value;

        $time = microtime(true);
        $result = $this->instance->set($key, $value, $lifeTime);
        \Core\Utils::$latency['cache'][] = microtime(true) - $time;
        return $result;
    }

    /**
     * Deletes the cache for specified cache-key.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function delete($key)
    {
        unset($this->cache[$key]);

        return $this->instance->delete($key);
    }
}

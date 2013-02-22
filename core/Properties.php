<?php

namespace core;

/**
 *
 * Properties class to store array information in the database field from type OBJECT.
 *
 */

class Properties
{
    /**
     * @var array
     */

    public $data = array();

    public function __construct($pData)
    {
        if (is_string($pData)) {
            $this->data = json_decode($pData, true);
        } else {
            $this->data = $pData;
        }

    }

    /**
     * Returns the data as array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Gets the value of $pPath
     *
     * @param  string $pPath slash delimited string
     * @return mixed
     */
    public function getByPath($pPath)
    {
        $path = explode('/', $pPath);

        $data = $this->data;

        foreach ($path as $node) {
            if (!$data[$node]) return false;
            $data = $data[$node];
        }

        return $data;

    }

    /**
     * Sets the value of $pPath
     *
     * @param string $pPath slash delimited string
     * @param mixed  $pData
     */
    public function setByPath($pPath, $pData)
    {
        $path = explode('/', $pPath);

        $data =& $this->data;

        foreach ($path as $node) {
            if (!$data[$node]) $data[$node] = array();
            $data =& $data[$node];
        }

        if ($data) {
            $data = $pData;
        }

    }

}

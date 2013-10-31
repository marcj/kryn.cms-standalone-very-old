<?php

namespace Core\FAL;

/**
 * Abstract class for the FAL (File abstraction layer).
 *
 * Please note: All methods $pPath arguments are relative to your mountpoint!
 * So, if a developer calls Core\File::getFile('AmazonCloud/myFile.img') and 'AmazonCloud'
 * is your mount point, then $pPath is 'myFile.img'.
 *
 */
abstract class AbstractFAL implements FALInterface
{
    /**
     * Current name of the mount point. (in fact, the folder name in media/<folder>)
     *
     * @var string
     */
    private $mountPoint = '';

    /**
     * Current params as array.
     *
     * @var array
     */
    private $params = array();

    /**
     * Constructor
     *
     * @param string $mountPoint The mount name for this layer. (in fact, the folder name in media/<folder>)
     * @param array  $params
     */
    public function __construct($mountPoint, $params = null)
    {
        $this->setMountPoint($mountPoint);

        if ($params) {
            $this->params = $params;
        }
    }

    /**
     * Gets a value of the params.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function getParam($key)
    {
        return $this->params[$key];
    }

    /**
     * Sets a value for a param.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * Sets the name of mount point.
     *
     * @param [type] $pEntryPoint [description]
     */
    public function setMountPoint($mountPoint)
    {
        $this->mountPoint = $mountPoint;
    }

    /**
     * Returns current name of the mount point.
     *
     * @return string
     */
    public function getMountPoint()
    {
        return $this->mountPoint;
    }

    /**
     * Returns the content hash (max 64 byte).
     *
     * @param $path
     *
     * @return string
     */
    public function getHash($path)
    {
        return md5($this->getContent($path));
    }
}

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
     * @param string $pMountPoint The mount name for this layer. (in fact, the folder name in media/<folder>)
     * @param array  $pParams
     */
    public function __construct($pMountPoint, $pParams = null)
    {
        $this->setMountPoint($pMountPoint);

        if ($pParams) {
            $this->params = $pParams;
        }
    }

    /**
     * Gets a value of the params.
     *
     * @param  string $pKey
     *
     * @return mixed
     */
    public function getParam($pKey)
    {
        return $this->params[$pKey];
    }

    /**
     * Sets a value for a param.
     *
     * @param string $pKey
     * @param mixed  $pValue
     */
    public function setParam($pKey, $pValue)
    {
        $this->params[$pKey] = $pValue;
    }

    /**
     * Sets the name of mount point.
     *
     * @param [type] $pEntryPoint [description]
     */
    public function setMountPoint($pMountPoint)
    {
        $this->mountPoint = $pMountPoint;
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
     * @param $pPath
     *
     * @return string
     */
    public function getHash($pPath)
    {
        return md5($this->getContent($pPath));
    }
}

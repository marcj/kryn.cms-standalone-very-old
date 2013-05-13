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
abstract class FALAbstract
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
     * Returns the content hash as md5.
     *
     * @param $pPath
     *
     * @return string
     */
    public function getMd5($pPath)
    {
        return md5($this->getContent($pPath));
    }

    /**
     * Creates a file with default permissions.
     * Creates also the full folder path if the they doesnt exist.
     *
     * @param  string $pPath
     *
     * @return bool
     */
    abstract public function createFile($pPath, $pContent = false);

    /**
     * Creates a folder with default permissions.
     * Creates also the full folder path if the they doesnt exist.
     *
     * @param  string $pPath
     *
     * @return bool
     */
    abstract public function createFolder($pPath);

    /**
     * Sets the content of a file.
     *
     * Creates the file if not exist. Creates also the full folder path if
     * the they doesnt exist.
     *
     * @param  string                    $pPath
     * @param  string                    $pContent
     *
     * @throws \FileNotWritableException If file is not writable.
     * @return bool
     */
    abstract public function setContent($pPath, $pContent);

    /**
     * Gets the content of a file.
     *
     * @param  string      $pPath
     *
     * @return bool|string
     */
    abstract public function getContent($pPath);

    /**
     * List directory contents.
     *
     * Same as in getFile() but in a list.
     *
     *  array(
     *    array(
     *      path => path to the file/folder for usage in the administration and modules. Not the full http path. No trailing slash!
     *      name => basename(path)
     *      ctime => as unix timestamps
     *      mtime => as unix timestamps
     *      size => filesize in bytes (not for folders)
     *      type => 'dir'
     *    )
     *  )
     *
     * @param string $pPath
     *
     * @return int|bool|array Return false if the file doenst exist,
     *                        return 2 if the webserver does not have access
     *                        or return array with the information.
     */
    abstract public function getFiles($pPath);

    /**
     * Return information for a file/folder.
     *
     * The result contains following information:
     *  [path(relative), name, type(dir|file), ctime(unixtimestamp), mtime(unixtimestamp), size(bytes)]
     *
     *  array(
     *    path => path to this file/folder for usage in the administration and modules. Not the full http path. No trailing slash!
     *    name => basename(path)
     *    ctime => as unix timestamps
     *    mtime => as unix timestamps
     *    size => filesize in bytes (not for folders)
     *    type => 'dir' or 'file'
     *  )
     *
     * @param string $pPath
     *
     * @return int|bool|array Return false if the file doenst exist,
     *                        return 2 if the webserver does not have access
     *                        or return array with the information.
     */
    abstract public function getFile($pPath);

    /**
     * Returns the file count inside $pFolderPath
     *
     * @static
     *
     * @param  string $pFolderPath
     *
     * @return mixed
     */
    abstract public function getCount($pFolderPath);

    /**
     * Disk usage
     *
     * @param  string     $pPath
     *
     * @return array|bool [size(bytes), fileCount, folderCount]
     */
    abstract public function getSize($pPath);

    /**
     * Checks if a file exists.
     *
     * @param  string $pPath
     *
     * @return bool
     */
    abstract public function fileExists($pPath);

    /**
     * Copies a file to a destination.
     * If the source is a folder, it copies recursivly.
     *
     * @param  string $pPathSource
     * @param  string $pPathTarget
     *
     * @return bool
     */
    abstract public function copy($pPathSource, $pPathTarget);

    /**
     * Moves a file to new destinaton.
     *
     * @param  string $pPathSource
     * @param  string $pPathTarget
     *
     * @return bool
     */
    abstract public function move($pPathSource, $pPathTarget);

    /**
     * Searchs files in a path by a regex pattern.
     *
     * @param  string  $pPath
     * @param  string  $pPattern      Preg regex
     * @param  integer $pDepth        Maximum depth. -1 for unlimited.
     * @param  integer $pCurrentDepth Internal
     *
     * @return array   Files array
     */
    abstract public function search($pPath, $pPattern, $pDepth = -1, $pCurrentDepth = 1);

    abstract public function getPublicUrl($pPath);

    /**
     * Removes a file or folder (recursive).
     *
     * @param  string   $pPath
     *
     * @return bool|int
     */
    abstract public function remove($pPath);

    /**
     * Returns true if public access is permitted, false if denied and -1 if has not been defined
     *
     * @param  string   $pPath
     *
     * @return bool|int
     */
    abstract public function getPublicAccess($pPath);

    /**
     * Sets the public access.
     *
     * @param  string $pPath
     * @param  bool   $pAccess true if allow, false if deny and -1 if remove the rule
     *
     * @return bool
     */
    abstract public function setPublicAccess($pPath, $pAccess = false);

}

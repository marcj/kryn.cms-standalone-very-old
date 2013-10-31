<?php

namespace Core\FAL;

interface FALInterface {

    public function __construct($pMountPoint, $pParams = null);

    /**
     * Gets a value of the params.
     *
     * @param  string $pKey
     *
     * @return mixed
     */
    public function getParam($pKey);

    /**
     * Sets a value for a param.
     *
     * @param string $pKey
     * @param mixed  $pValue
     */
    public function setParam($pKey, $pValue);

    /**
     * Sets the name of mount point.
     *
     * @param [type] $pEntryPoint [description]
     */
    public function setMountPoint($pMountPoint);

    /**
     * Returns current name of the mount point.
     *
     * @return string
     */
    public function getMountPoint();

    /**
     * Returns the content hash (max 64 byte).
     *
     * @param $pPath
     *
     * @return string
     */
    public function getHash($pPath);

    /**
     * Creates a file with default permissions.
     * Creates also the full folder path if the they doesnt exist.
     *
     * @param  string $pPath
     * @param  string $pContent
     *
     * @return bool
     */
    public function createFile($pPath, $pContent = null);

    /**
     * Creates a folder with default permissions.
     * Creates also the full folder path if the they doesnt exist.
     *
     * @param  string $pPath
     *
     * @return bool
     */
    public function createFolder($pPath);

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
    public function setContent($pPath, $pContent);

    /**
     * Gets the content of a file.
     *
     * @param  string      $pPath
     *
     * @return bool|string
     */
    public function getContent($pPath);

    /**
     * Lists all files in a directory.
     *
     * @param string $pPath
     *
     * @return \Core\File\FileInfo[]
     */
    public function getFiles($pPath);

    /**
     * Returns a file.
     *
     * @param string $pPath
     *
     * @return \Core\File\FileInfo
     */
    public function getFile($pPath);

    /**
     * Returns the file count inside $pFolderPath
     *
     * @static
     *
     * @param  string $pFolderPath
     *
     * @return mixed
     */
    public function getCount($pFolderPath);

    /**
     * Disk usage
     *
     * @param  string     $pPath
     *
     * @return array|bool [size(bytes), fileCount, folderCount]
     */
    public function getSize($pPath);

    /**
     * Checks if a file exists.
     *
     * @param  string $pPath
     *
     * @return bool
     */
    public function fileExists($pPath);

    /**
     * Copies a file to a destination.
     * If the source is a folder, it copies recursivly.
     *
     * @param  string $pPathSource
     * @param  string $pPathTarget
     *
     * @return bool
     */
    public function copy($pPathSource, $pPathTarget);

    /**
     * Moves a file to new destinaton.
     *
     * @param  string $pPathSource
     * @param  string $pPathTarget
     *
     * @return bool
     */
    public function move($pPathSource, $pPathTarget);

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
    public function search($pPath, $pPattern, $pDepth = -1, $pCurrentDepth = 1);

    public function getPublicUrl($pPath);

    /**
     * Removes a file or folder (recursive).
     *
     * @param  string   $pPath
     *
     * @return bool|int
     */
    public function remove($pPath);

    /**
     * Returns true if public access is permitted, false if denied and -1 if has not been defined
     *
     * @param  string   $pPath
     *
     * @return bool|int
     */
    public function getPublicAccess($pPath);

    /**
     * Sets the public access.
     *
     * @param  string $pPath
     * @param  bool   $pAccess true if allow, false if deny and -1 if remove the rule
     *
     * @return bool
     */
    public function setPublicAccess($pPath, $pAccess = false);
}
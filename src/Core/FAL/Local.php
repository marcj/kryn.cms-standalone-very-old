<?php

namespace Core\FAL;

use Core\File\FileInfo;
use Core\Kryn;

/**
 * Local file layer for the local file system.
 *
 */
class Local extends AbstractFAL
{
    /**
     * Current root folder.
     *
     * @var string
     */
    private $root = PATH_WEB;

    /**
     * Default permission modes for directories.
     *
     * @var integer
     */
    public $dirMode = 0700;

    /**
     * Default permission modes for files.
     *
     * @var integer
     */
    public $fileMode = 0600;

    /**
     * Defines whether we chmod the edited file or not.
     *
     * @var bool
     */
    public $changeMode = true;

    /**
     * Default group owner name.
     *
     * @var string
     */
    public $groupName = '';

    public function getFullPath($path)
    {
        $root = $this->getRoot();

        if (substr($root, -1) != '/') {
            $root .= '/';
        }

        if (substr($path, 0, 1) == '/') {
            $path = substr($path, 1);
        }

        return $root . $path;
    }

    /**
     * Sets file permissions on file/folder recursively.
     *
     * @param  string                           $path
     *
     * @throws \FileOperationPermittedException
     * @return bool
     */
    public function setPermission($path)
    {
        $path2 = $this->getFullPath($path);

        if (!file_exists($path2)) {
            return false;
        }

        if ($this->groupName) {
            if (!chgrp($path2, $this->groupName)) {
                throw new \FileOperationPermittedException(tf(
                    'Operation to chgrp the file %s to %s is permitted.',
                    $path2,
                    $this->groupName
                ));
            }
        }

        if (is_dir($path2)) {

            if (!chmod($path2, $this->dirMode)) {
                throw new \FileOperationPermittedException(tf(
                    'Operation to chmod the folder %s to %o is permitted.',
                    $path2,
                    $this->dirMode
                ));
            }

            $sub = find($path2 . '/*', false);
            if (is_array($sub)) {
                foreach ($sub as $path2) {
                    $this->setPermission(substr($path2, 0, strlen($this->getRoot())));
                }
            }
        } elseif (is_file($path2)) {
            @chmod($path2, $this->fileMode);
        }

        return true;

    }

    /**
     * Loads and converts the configuration in Core\Kryn::getSystemConfig()->getFile()
     * to appropriate modes.
     *
     */
    public function loadConfig()
    {
        $this->fileMode = 600;
        $this->dirMode = 700;

        if (Kryn::getSystemConfig()->getFile()->getGroupPermission() == 'rw') {
            $this->fileMode += 60;
            $this->dirMode += 70;
        } elseif (Kryn::getSystemConfig()->getFile()->getGroupPermission() == 'r') {
            $this->fileMode += 40;
            $this->dirMode += 50;
        }

        if (Kryn::getSystemConfig()->getFile()->getEveryonePermission() == 'rw') {
            $this->fileMode += 6;
            $this->dirMode += 7;
        } elseif (Kryn::getSystemConfig()->getFile()->getEveryonePermission() == 'r') {
            $this->fileMode += 4;
            $this->dirMode += 5;
        }

        $this->fileMode = octdec($this->fileMode);
        $this->dirMode = octdec($this->dirMode);
        $this->groupName = Kryn::getSystemConfig()->getFile()->getGroupOwner();
        $this->changeMode = !Kryn::getSystemConfig()->getFile()->getDisableModeChange();
    }

    /**
     * {@inheritDoc}
     */
    public function __construct($mountPoint, $params = null)
    {
        parent::__construct($mountPoint, $params);
        if ($params && $params['root']) {
            $this->setRoot($params['root']);
        }

        $this->loadConfig();
    }

    /**
     * Gets current root folder for this local layer.
     *
     * @param string $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * Sets current root folder for this local layer.
     *
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * {@inheritDoc}
     */
    public function createFile($path, $content = null)
    {
        $path2 = $this->getFullPath($path);

        if (!file_exists(dirname($path2))) {
            $this->createFolder(dirname($path));
        }

        if (!file_exists($path2)) {
            if (!is_writable(dirname($path2))) {
                throw new \FileNotWritableException(tf(
                    'Can not create the file %s in %s, since the folder is not writable.',
                    $path2,
                    dirname($path2)
                ));
            }
            if (null !== $content) {
                file_put_contents($path2, $content);
            } else {
                touch($path2);
            }
            if ($this->changeMode) {
                $this->setPermission($path);
            }
        }

        return file_exists($path2);
    }

    /**
     * @param  string                           $path The full absolute path
     *
     * @return bool
     * @throws \FileOperationPermittedException
     * @throws \FileIOException
     */
    private function _createFolder($path)
    {
        is_dir(dirname($path)) or $this->_createFolder(dirname($path));

        if (!is_dir($path)) {
            if (!@mkdir($path)) {
                throw new \FileIOException(tf('Can not create folder %s.', $path));
            }

            if ($this->groupName) {
                if (!@chgrp($path, $this->groupName)) {
                    throw new \FileOperationPermittedException(tf(
                        'Operation to chgrp the folder %s to %s is permitted.',
                        $path,
                        $this->groupName
                    ));
                }
            }

            if (!chmod($path, $this->dirMode)) {
                throw new \FileOperationPermittedException(tf(
                    'Operation to chmod the folder %s to %o is permitted.',
                    $path,
                    $this->dirMode
                ));
            }
        }

        return is_dir($path);
    }

    /**
     * {@inheritDoc}
     */
    public function createFolder($path)
    {
        if (!file_exists($path2 = $this->getFullPath($path))) {
            return $this->_createFolder($path2);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function setContent($path, $content)
    {
        $path2 = $this->getFullPath($path);

        if (!file_exists($path2)) {
            $fileCreated = $this->createFile($path);
        } else if (!is_writable($path2)) {
            throw new \FileNotWritableException(tf('File %s is not writable.', $path2));
        }

        $res = file_put_contents($path2, $content);

        if (!$fileCreated && $this->changeMode) {
            $this->setPermission($path);
        }

        return $res === false ? false : true;
    }

    /**
     * {@inheritDoc}
     */
    public function getFiles($path)
    {
        $path = $this->getFullPath($path);
        $path = str_replace('..', '', $path);

        if (!file_exists($path)) {
            throw new \FileNotExistException(tf('File `%s` does not exists.', $path));
        }

        if (!is_dir($path)) {
            throw new \FileNotExistException(tf('File `%s` is not a directory.', $path));
        };

        if (substr($path, -1) != '/') {
            $path .= '/';
        }

        $h = @opendir($path);
        if (!$h) {
            throw new \IOException(tf('Can not open `%s`. Probably no permissions.', $path));
        }

        $items = array();
        while ($file = readdir($h)) {

            $fileInfo = new FileInfo();
            if ($file == '.' || $file == '..') {
                continue;
            }
            $file = $path . $file;

            $fileInfo->setPath(substr($file, strlen($this->getRoot()) - 1));

            $fileInfo->setType(is_dir($file) ? FileInfo::DIR : FileInfo::FILE);

            $fileInfo->setCreatedTime(filectime($file));
            $fileInfo->setModifiedTime(filectime($file));
            $fileInfo->setSize(filesize($file));
            $items[] = $fileInfo;
        }

        return $items;
    }

    /**
     * {@inheritDoc}
     */
    public function getFile($path)
    {
        $file = new FileInfo();
        $file->setPath($path ?: '/');
        $path = $this->getFullPath($path ?: '/');
        if (!file_exists($path)) {
            throw new \FileNotExistException(tf('File `%s` does not exists.', $path));
        }

        if (!is_readable($path)) {
            throw new \FileNotExistException(tf('File `%s` is not readable.', $path));
        }

        $file->setType(is_dir($path) ? 'dir' : 'file');

        $file->setCreatedTime(filectime($path));
        $file->setModifiedTime(filectime($path));
        $file->setSize(filesize($path));

        return $file;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize($path)
    {
        $size = 0;
        $fileCount = 0;
        $folderCount = 0;

        $path2 = $this->getRoot() . $path;

        if ($h = opendir($path2)) {
            while (false !== ($file = readdir($h))) {
                $nextPath = $path2 . '/' . $file;
                if ($file != '.' && $file != '..' && !is_link($nextPath)) {
                    if (is_dir($nextPath)) {
                        $folderCount++;
                        $result = self::getSize($nextPath);
                        $size += $result['size'];
                        $fileCount += $result['fileCount'];
                        $folderCount += $result['folderCount'];
                    } elseif (is_file($nextPath)) {
                        $size += filesize($nextPath);
                        $fileCount++;
                    }
                }
            }
        }
        closedir($h);

        return array(
            'size' => $size,
            'fileCount' => $fileCount,
            'folderCount' => $folderCount
        );
    }

    /**
     * {@inheritDoc}
     */
    public function fileExists($path)
    {
        return file_exists($this->getRoot() . $path);
    }

    /**
     * {@inheritDoc}
     */
    public function getCount($folderPath)
    {
        return count(glob($this->getRoot() . $folderPath . '/*'));
    }

    /**
     * {@inheritDoc}
     */
    public function copy($pathSource, $pathTarget)
    {
        if (!file_exists($this->getRoot() . $pathSource)) {
            return false;
        }
        copyr($this->getRoot() . $pathSource, $this->getRoot() . $pathTarget);
        return file_exists($this->getRoot() . $pathTarget);
    }

    /**
     * {@inheritDoc}
     */
    public function move($pathSource, $pathTarget)
    {
        return rename($this->getRoot() . $pathSource, $this->getRoot() . $pathTarget);
    }

    /**
     * {@inheritDoc}
     */
    public function getHash($path)
    {
        return md5_file($this->getRoot() . $path);
    }

    /**
     * {@inheritDoc}
     */
    public function getContent($path)
    {
        $path = $this->getRoot() . $path;

        if (!file_exists($path)) {
            return false;
        }

        $handle = @fopen($path, "r");
        $fs = @filesize($path);

        if ($fs > 0) {
            $content = @fread($handle, $fs);
        }

        @fclose($handle);

        return $content;

    }

    /**
     * {@inheritDoc}
     */
    public function search($path, $pattern, $depth = 1, $currentDepth = 1)
    {
        $result = array();
        $files = $this->getFiles($path);

        $q = str_replace('/', '\/', $pattern);

        foreach ($files as $file) {
            if (preg_match('/^' . $q . '/i', $file->getName(), $match) !== 0) {
                $result[] = $file;
            }
            if ($file->isDir() && ($depth == -1 || $currentDepth < $depth)) {
                $newPath = $path . ($path == '/' ? '' : '/') . $file->getName();
                $more = $this->search($newPath, $pattern, $depth, $currentDepth + 1);
                if (is_array($more)) {
                    $result = array_merge($result, $more);
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getPublicUrl($path)
    {
        return '/' . $this->getRoot() . $path;
    }

    /**
     * {@inheritDoc}
     */
    public function remove($path)
    {
        $path2 = $this->getRoot() . $path;

        if (is_dir($path2)) {
            return delDir($path2);
        } elseif (is_file($path2)) {
            return unlink($path2);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getPublicAccess($path)
    {
        $path2 = $this->getRoot() . $path;

        if (!file_exists($path2)) {
            return false;
        }

        if (!is_dir($path2)) {
            $htaccess = dirname($path2) . '/' . '.htaccess';
        } else {
            $htaccess = $path2 . '/' . '.htaccess';
        }
        $name = basename($path);

        if (@file_exists($htaccess)) {

            $content = kryn::fileRead($htaccess);
            @preg_match_all('/<Files ([^>]*)>\W*(\w*) from all[^<]*<\/Files>/smi', $content, $matches, PREG_SET_ORDER);
            if (count($matches) > 0) {
                foreach ($matches as $match) {

                    $match[1] = str_replace('"', '', $match[1]);
                    $match[1] = str_replace('\'', '', $match[1]);

                    //TODO, what is $res?
                    if ($name == $match[1] || (is_dir($match[1]) && $match[1] == "*")) {
                        return strtolower($match[2]) == 'allow' ? true : false;
                    }
                }
            }
        }

        return -1;
    }

    /**
     * {@inheritDoc}
     */
    public function setPublicAccess($path, $access = false)
    {
        $path2 = $this->getRoot() . $path;

        if (!is_dir($path2) == 'file') {
            $htaccess = dirname($path2) . '/' . '.htaccess';
        } else {
            $htaccess = $path2 . '/' . '.htaccess';
        }

        if (!file_exists($htaccess) && !touch($htaccess)) {
            klog('files', t('Can not set the file access, because the system can not create the .htaccess file'));

            return false;
        }

        $content = kryn::fileRead($htaccess);

        if (!is_dir($path)) {
            $filename = '"' . basename($path) . '"';
            $filenameesc = preg_quote($filename, '/');
        } else {
            $filename = "*";
            $filenameesc = '\*';
        }

        $content = preg_replace('/<Files ' . $filenameesc . '>\W*(\w*) from all[^<]*<\/Files>/i', '', $content);

        if ($access !== -1) {
            $access2 = $access == true ? 'Allow' : 'Deny';
            $content .= "\n<Files $filename>\n\t$access2 from all\n</Files>";
        }

        kryn::fileWrite($htaccess, $content);

        return true;
    }
}

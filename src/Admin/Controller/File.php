<?php

namespace Admin\Controller;

use Core\Kryn;
use Core\Models\Base\FileQuery;
use Core\Permission;
use Core\WebFile;

class File
{

    /**
     * Removes a file or folder (recursively).
     *
     * @param string $pPath
     *
     * @return bool
     */
    public function deleteFile($pPath)
    {
        $this->checkAccess($pPath);

        FileQuery::create()->filterByPath($pPath)->delete();
        return WebFile::remove($pPath);
    }


    /**
     * Creates a file.
     *
     * @param string $pPath
     * @param string $pContent
     *
     * @return bool
     */
    public function createFile($pPath, $pContent = '')
    {
        $this->checkAccess($pPath);
        return WebFile::createFile($pPath, $pContent);
    }

    /**
     * @param string $target
     * @param array  $files
     * @param bool   $overwrite
     * @param bool   $move
     * @return bool
     */
    public function paste($target, $files, $overwrite = false, $move = false)
    {
        $this->checkAccess($target);
        foreach ($files as $file) {
            $this->checkAccess($file);
        }
        return WebFile::paste($files, $target, $move ? 'move' : 'copy');
    }

    /**
     * Creates a folder
     *
     * @param string $pPath
     *
     * @return bool
     */
    public function createFolder($pPath)
    {
        $this->checkAccess(dirname($pPath));
        return WebFile::createFolder($pPath);
    }

    /**
     * Checks the file access.
     *
     * @param $pPath
     *
     * @throws \FileIOException
     * @throws \AccessDeniedException
     */
    public function checkAccess($pPath)
    {
        try {
            $file = WebFile::getFile($pPath);
        } catch (\FileNotExistException $e) {
            $file = WebFile::getFile(dirname($pPath));
        }
        if ($file && !Permission::checkUpdate('Core\\File', array('id' => $file->getId()))) {
            throw new \AccessDeniedException(tf('No access to file `%s`', $pPath));
        }
    }

    /**
     * Prepares a file upload process.
     *
     * @param string $pPath
     * @param string $pName
     * @param bool   $pOverwrite
     *
     * @return array
     */
    public function prepareUpload($pPath, $pName, $pOverwrite = false)
    {

        $oriName = $pName;
        $name = $pName;
        $newPath = ($pPath == '/') ? '/' . $name : $pPath . '/' . $name;

        $this->checkAccess($pPath);

        $res = array();

        if ($name != $oriName) {
            $res['renamed'] = true;
            $res['name'] = $name;
        }

        $exist = WebFile::exists($newPath);
        if ($exist && !$pOverwrite) {
            $res['exist'] = true;
        } else {
            WebFile::createFile($pPath, "\0\0\0\0\0\0\0\nKrynBlockedFile\n" . Kryn::getAdminClient()->getTokenId());
        }

        return $res;
    }

    /**
     * Receives the file through $_FILES and place it at the target path.
     *
     * @param string $pPath
     * @param string $pName
     * @param bool   $pOverwrite
     *
     * @return string
     * @throws \FileUploadException
     * @throws \FileIOException
     * @throws \AccessDeniedException
     */
    public static function doUpload($pPath, $pName = null, $pOverwrite = false)
    {
        $name = $_FILES['file']['name'];
        if ($pName) {
            $name = $pName;
        }

        if ($_FILES["file"]['error']) {

            switch ($_FILES['file']['error']) {
                case 1:
                    $error = t('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
                    break;
                case 2:
                    $error =
                        t('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
                    break;
                case 3:
                    $error = t('The uploaded file was only partially uploaded.');
                    break;
                case 7:
                    $error = t('Failed to write file to disk.');
                    break;
                case 6:
                    $error = t('Missing a temporary folder.');
                    break;
                case 4:
                    $error = t('No file was uploaded.');
                    break;
                case 8:
                    $error = t('A PHP extension stopped the file upload.');
                    break;
            }

            $error = sprintf(t('Failed to upload the file %s to %s. Error: %s'), $name, $pPath, $error);
            klog('file', $error);

            throw new \FileUploadException($error);
        }

        $newPath = ($pPath == '/') ? '/' . $name : $pPath . '/' . $name;

        if (WebFile::exists($newPath)) {

            if (!$pOverwrite) {

                $content = WebFile::getContent($newPath);

                if ($content != "\0\0\0\0\0\0\0\nKrynBlockedFile\n" . Kryn::getAdminClient()->getTokenId()) {
                    //not our file, so cancel
                    throw new \FileUploadException(tf('The target file is currently being uploaded by someone else.'));
                }
            }
        }

        $file = WebFile::getFile(dirname($pPath));
        if ($file && !Permission::checkUpdate('Core\\File', array('id' => $file->getId()))) {
            throw new \AccessDeniedException(tf('No access to file `%s`', $pPath));
        }

        $content = file_get_contents($_FILES['file']['tmp_name']);
        WebFile::setContent($newPath, $content);
        @unlink($_FILES["file"]["tmp_name"]);

        return $newPath;
    }


    public function getContent($path)
    {
        if (!$file = self::getFile($path)) {
            return null;
        }

        if ($file['type'] == 'dir'){
            return $this->getFiles($path);
        } else {
            return WebFile::getContent($path);
        }

    }

    /**
     * Returns a list of files for a folder.
     *
     * @param string $pPath
     *
     * @return array|null
     */
    public function getFiles($pPath)
    {
        if (!self::getFile($pPath)) {
            return null;
        }

        //todo, create new option 'show hidden files' in user settings and depend on that

        $files = WebFile::getFiles($pPath);
        return static::prepareFiles($files);
    }

    public static function prepareFiles($files, $showHiddenFiles = false)
    {
        $result = [];

        $blacklistedFiles = array('/index.php' => 1, '/install.php' => 1);

        $imageTypes = array('jpg', 'jpeg', 'png', 'bmp', 'gif');
        foreach ($files as $key => $file) {
            $file = $file->toArray();
            if (isset($blacklistedFiles[$file['path']]) | (!$showHiddenFiles && substr($file['path'], 0, 2) == '/.')) {
                unset($files[$key]);
            } else {
                $file['writeAccess'] = Permission::checkUpdate('Core\\File', array('id' => $file['id']));

                if (array_search($file['extension'], $imageTypes) !== false) {
                    $content = WebFile::getContent($file['path']);
                    $image = \PHPImageWorkshop\ImageWorkshop::initFromString($content);

                    $file['dimensions'] = array('width' => $image->getWidth(), 'height' => $image->getHeight());
                }
            }
            $result[] = $file;
        }

        return $result;
    }

    public function search($path, $q, $depth = 1)
    {
        $files = WebFile::search($path, $q, $depth);
        return static::prepareFiles($files);
    }

    /**
     * @param string $pPath
     *
     * @return array|bool|int
     */
    public function getFile($pPath)
    {
        $file = WebFile::getFile($pPath);
        if (!Permission::checkListExact('Core\\File', array('id' => $file->getId()))) {
            return;
        }

        $file = $file->toArray();
        $file['writeAccess'] = Permission::checkUpdate('Core\\File', $file['id']);

        return $file;

    }

    /**
     * Displays a thumbnail/resized version of a image.
     * This exists the process and sends a `content-type: image/png` http header.
     *
     * @param string $pPath
     * @param int    $pWidth
     * @param int    $pHeight
     */
    public function showPreview($pPath, $pWidth = 50, $pHeight = 50)
    {
        $image = WebFile::getResizeMax($pPath, $pWidth, $pHeight);

        $expires = 3600;
        header("Pragma: public");
        header("Cache-Control: maxage=" . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
        header('Content-type: image/png');

        imagepng($image->getResult(), null, 8);
        exit;
    }

}

<?php

namespace Admin\Controller;

use Core\File\FileSize;
use Core\Kryn;
use Core\Models\Base\FileQuery;
use Core\Permission;
use Core\WebFile;

class File
{
    /**
     * Removes a file or folder (recursively).
     *
     * @param string $path
     *
     * @return bool
     */
    public function deleteFile($path)
    {
        $this->checkAccess($path);

        FileQuery::create()->filterByPath($path)->delete();
        return WebFile::remove($path);
    }

    /**
     * Creates a file.
     *
     * @param string $path
     * @param string $content
     *
     * @return bool
     */
    public function createFile($path, $content = '')
    {
        $this->checkAccess($path);
        return WebFile::createFile($path, $content);
    }

    public function moveFile($path, $target, $overwrite = false)
    {
        if (!$overwrite && WebFile::exists($target)){
            return ['targetExists' => true];
        }

        $this->checkAccess($path);
        $this->checkAccess($target);

        return WebFile::move($path, $target);
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
     * @param string $path
     *
     * @return bool
     */
    public function createFolder($path)
    {
        $this->checkAccess(dirname($path));
        return WebFile::createFolder($path);
    }

    /**
     * Checks the file access.
     *
     * @param $path
     *
     * @throws \FileIOException
     * @throws \AccessDeniedException
     */
    public function checkAccess($path)
    {
        $file = null;

        try {
            $file = WebFile::getFile($path);
        } catch (\FileNotExistException $e) {
            while ('/' !== $path) {
                try {
                    $path = dirname($path);
                    $file = WebFile::getFile($path);
                } catch (\FileNotExistException $e) {
                }
            }
        }

        if ($file && !Permission::checkUpdate('Core\\File', array('id' => $file->getId()))) {
            throw new \AccessDeniedException(tf('No access to file `%s`', $path));
        }
    }

    /**
     * Prepares a file upload process.
     *
     * @param string $path
     * @param string $name
     * @param bool   $overwrite
     *
     * @return array
     */
    public function prepareUpload($path, $name, $overwrite = false)
    {
        $oriName = $name;
        $name2 = $name;
        $newPath = ($path == '/') ? '/' . $name2 : $path . '/' . $name2;

        $this->checkAccess($path);

        $res = array();

        if ($name2 != $oriName) {
            $res['renamed'] = true;
            $res['name'] = $name2;
        }

        $exist = WebFile::exists($newPath);
        if ($exist && !$overwrite) {
            $res['exist'] = true;
        } else {
            WebFile::createFile($path, "\0\0\0\0\0\0\0\nKrynBlockedFile\n" . Kryn::getAdminClient()->getTokenId());
            $res['ready'] = true;
        }

        return $res;
    }

    /**
     * Receives the file through $_FILES and place it at the target path.
     *
     * @param string $path
     * @param string $name
     * @param bool   $overwrite
     *
     * @return string
     * @throws \FileUploadException
     * @throws \FileIOException
     * @throws \AccessDeniedException
     */
    public static function doUpload($path, $name = null, $overwrite = false)
    {
        $name2 = $_FILES['file']['name'];
        if ($name) {
            $name2 = $name;
        }

        if (!$_FILES['file']) {
            throw new \FileUploadException(tf('No file uploaded.'));
        }

        if ($_FILES['file']['error']) {

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

            $error = sprintf(t('Failed to upload the file %s to %s. Error: %s'), $name2, $path, $error);
            klog('file', $error);

            throw new \FileUploadException($error);
        }

        $newPath = ($path == '/') ? '/' . $name2 : $path . '/' . $name2;
        if (WebFile::exists($newPath)) {

            if (!$overwrite) {

                if (WebFile::exists($newPath)) {
                    $content = WebFile::getContent($newPath);

                    if ($content != "\0\0\0\0\0\0\0\nKrynBlockedFile\n" . Kryn::getAdminClient()->getTokenId()) {
                        //not our file, so cancel
                        throw new \FileUploadException(tf('The target file is currently being uploaded by someone else.'));
                    }
                } else {
                    throw new \FileUploadException(tf('The target file has not be initialized.'));
                }
            }
        }

        $file = WebFile::getFile(dirname($path));
        if ($file && !Permission::checkUpdate('Core\\File', array('id' => $file->getId()))) {
            throw new \AccessDeniedException(tf('No access to file `%s`', $path));
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

        // todo: check for Read permission

        if ($file['type'] == 'dir'){
            return $this->getFiles($path);
        } else {
            return WebFile::getContent($path);
        }
    }

    public function getBinary($path)
    {
        $content = $this->getContent($path);
        die($content);
    }

    /**
     * Returns a list of files for a folder.
     *
     * @param string $path
     *
     * @return array|null
     */
    public function getFiles($path)
    {
        if (!self::getFile($path)) {
            return null;
        }

        //todo, create new option 'show hidden files' in user settings and depend on that

        $files = WebFile::getFiles($path);
        return static::prepareFiles($files);
    }

    public static function prepareFiles($files, $showHiddenFiles = false)
    {
        $result = [];

        $blacklistedFiles = array('/index.php' => 1, '/install.php' => 1);

        foreach ($files as $key => $file) {
            $file = $file->toArray();
            if (!Permission::checkListExact('core:file', array('id' => $file['id']))) continue;

            if (isset($blacklistedFiles[$file['path']]) | (!$showHiddenFiles && substr($file['name'], 0, 1) == '.')) {
                continue;
            } else {
                $file['writeAccess'] = Permission::checkUpdate('Core\\File', array('id' => $file['id']));
                self::appendImageInformation($file);
            }
            $result[] = $file;
        }

        return $result;
    }

    public static function appendImageInformation(&$file) {
        $imageTypes = array('jpg', 'jpeg', 'png', 'bmp', 'gif');

        if (array_search($file['extension'], $imageTypes) !== false) {
            $content = WebFile::getContent($file['path']);

            $size = new FileSize();
            $size->setHandleFromBinary($content);

            $file['imageType'] = $size->getType();
            $size = $size->getSize();
            if ($size) {
                $file['dimensions'] = ['width' => $size[0], 'height' => $size[1]];
            }
        }
    }

    public function search($path, $q, $depth = 1)
    {
        $files = WebFile::search($path, $q, $depth);
        return static::prepareFiles($files);
    }

    /**
     * @param string $path
     *
     * @return array|bool|int
     */
    public function getFile($path)
    {
        $file = WebFile::getFile($path);
        if (!Permission::checkListExact('Core\\File', array('id' => $file->getId()))) {
            return;
        }

        $file = $file->toArray();
        $file['writeAccess'] = Permission::checkUpdate('Core\\File', $file['id']);

        self::appendImageInformation($file);

        return $file;
    }

    /**
     * Displays a thumbnail/resized version of a image.
     * This exists the process and sends a `content-type: image/png` http header.
     *
     * @param string $path
     * @param int    $width
     * @param int    $height
     */
    public function showPreview($path, $width = 50, $height = 50)
    {
        $image = WebFile::getResizeMax($path, $width, $height);

        $expires = 3600;
        header("Pragma: public");
        header("Cache-Control: maxage=" . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');
        header('Content-type: image/png');

        imagepng($image->getResult(), null, 8);
        exit;
    }

    public function setContent($path, $content = '', $contentEncoding = 'plain') {
        if ('base64' === $contentEncoding){
            $content = base64_decode($content);
        }
        $this->checkAccess($path);
        return WebFile::setContent($path, $content);
    }

    /**
     * Displays a image.
     *
     * @param string $path
     */
    public function showImage($path)
    {
        $content = \Core\WebFile::getContent($path);
        $image = \PHPImageWorkshop\ImageWorkshop::initFromString($content);

        $result = $image->getResult();

        $size = new FileSize();
        $size->setHandleFromBinary($content);

        $expires = 3600;
        header("Pragma: public");
        header("Cache-Control: maxage=" . $expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expires) . ' GMT');

        ob_start();

        if ('png' === $size->getType()) {
            header('Content-Type: image/png');
            imagepng($result, null, 3);
        } else {
            header('Content-Type: image/jpeg');
            imagejpeg($result, null, 100);
        }

        header("Content-Length: ". ob_get_length());
        ob_end_flush();

        exit;
    }

}

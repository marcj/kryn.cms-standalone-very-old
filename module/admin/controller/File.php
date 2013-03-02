<?php

namespace Admin;

use Core\WebFile;
use Core\Permission;
use Core\Kryn;

class File
{

    /**
     * Removes a file or folder (recursively).
     *
     * @param string $pPath
     * @return bool
     */
    public function deleteFile($pPath)
    {
        $this->checkAccess($pPath);

        \Core\FileQuery::create()->filterByPath($pPath)->delete();
        return WebFile::remove($pPath);
    }


    /**
     * Creates a file.
     *
     * @param string $pPath
     * @param string $pContent
     * @return bool
     */
    public function createFile($pPath, $pContent = '')
    {
        $this->checkAccess($pPath);
        return WebFile::createFile($pPath, $pContent);
    }

    /**
     * Creates a folder
     *
     * @param string $pPath
     * @return bool
     */
    public function createFolder($pPath)
    {
        $this->checkAccess($pPath);
        return WebFile::createFolder($pPath);
    }

    /**
     * Checks the file access.
     *
     * @param $pPath
     * @throws \FileIOException
     * @throws \AccessDeniedException
     */
    public function checkAccess($pPath){
        $file = WebFile::getFile($pPath);
        if ($file && !Permission::checkUpdate('Core\\File', array('id' => $file['id']))){
            throw new \AccessDeniedException(tf('No access to file `%s`', $pPath));
        }

        if (!$file) {
            $folder = WebFile::getFile(dirname($pPath));
            if (!$folder) {
                throw new \FileIOException(tf('Folder `%s` does not exist.', dirname($pPath)));
            } else if (!Permission::checkUpdate('Core\\File', array('id' => $folder['id']))){
                throw new \AccessDeniedException(tf('No access to folder `%s`', $folder));
            }
        }
    }

    /**
     * Prepares a file upload process.
     *
     * @param string $pPath
     * @param string $pName
     * @param bool $pOverwrite
     * @return array
     */
    public function prepareUpload($pPath, $pName, $pOverwrite = false)
    {

        $oriName = $pName;
        $name    = $pName;
        $newPath = ($pPath == '/') ? '/' . $name : $pPath . '/' . $name;

        $this->checkAccess($pPath);

        $res = array();

        if ($name != $oriName) {
            $res['renamed'] = true;
            $res['name']    = $name;
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
     * @param bool $pOverwrite
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

        $file = WebFile::getFile($pPath);
        if ($file && !Permission::checkUpdate('Core\\File', array('id' => $file['id']))){
            throw new \AccessDeniedException(tf('No access to file `%s`', $pPath));
        }

        if (!$file) {
            $folder = WebFile::getFile(dirname($pPath));
            if (!$folder) {
                throw new \FileIOException(tf('Folder `%s` does not exist.', dirname($pPath)));
            } else if (!Permission::checkUpdate('Core\\File', array('id' => $folder['id']))){
                throw new \AccessDeniedException(tf('No access to folder `%s`', $folder));
            }
        }

        $content = file_get_contents($_FILES['file']['tmp_name']);
        WebFile::setContent($newPath, $content);
        @unlink($_FILES["file"]["tmp_name"]);

        return $newPath;
    }


    /**
     * Returns a list of files for a folder.
     *
     * @param string $pPath
     * @return array|null
     */
    public function getFiles($pPath)
    {

        if (!self::getFile($pPath)) return null;

        //todo, create new option 'show hidden files' in user settings and depend on that
        $showHiddenFiles = false;

        $blacklistedFiles = array('/index.php' => 1, '/install.php' => 1);

        $imageTypes = array('jpg', 'jpeg', 'png', 'bmp', 'gif');

        $files = WebFile::getFiles($pPath);
        foreach ($files as $key => &$file) {
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
        }

        return $files;
    }

    /**
     * @param string $pPath
     * @return array|bool|int
     */
    public function getFile($pPath)
    {

        $file = WebFile::getFile($pPath);
        if (!Permission::checkListExact('Core\\File', array('id' => $file['id']))) return;

        $file['writeAccess'] = Permission::checkUpdate('Core\\File', $file['id']);

        return $file;

    }

    /**
     * Displays a thumbnail/resized version of a image.
     * This exists the process and sends a `content-type: image/png` http header.
     *
     * @param string $pPath
     * @param int $pWidth
     * @param int $pHeight
     */
    public function showThumbnail($pPath, $pWidth = 50, $pHeight = 50)
    {
        $image = WebFile::getResizeMax($pPath, $pWidth, $pHeight);
        header('Content-type: image/png');
        imagepng($image->getResult(), null, 8);
        exit;
    }

}

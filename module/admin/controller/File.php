<?php

namespace Admin;

use Core\WebFile;
use Core\Permission;

class File
{
    public function getFiles($pPath)
    {

        if (!self::getFile($pPath)) return;

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

    public function getFile($pPath)
    {

        $file = WebFile::getFile($pPath);
        if (!Permission::checkListExact('Core\\File', array('id' => $file['id']))) return;

        $file['writeAccess'] = Permission::checkUpdate('Core\\File', $file['id']);

        return $file;

    }

    public function showThumbnail($pPath, $pWidth = 50, $pHeight = 50)
    {
        $image = WebFile::getResizeMax($pPath, $pWidth, $pHeight);
        header('Content-type: image/png');
        imagepng($image->getResult(), null, 8);
        exit;
    }

}

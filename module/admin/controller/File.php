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

        $files = WebFile::getFiles($pPath);
        foreach ($files as $key => &$file) {
            if (isset($blacklistedFiles[$file['path']]) | (!$showHiddenFiles && substr($file['path'], 0, 2) == '/.')) {
                unset($files[$key]);
            } else {
                $file['writeAccess'] = Permission::checkUpdate('Core\\File', array('id' => $file['id']));
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

    public function getThumbnail($pPath)
    {

        if (!self::getFile($pPath)) return;

        $image = WebFile::getThumbnail($pPath, '50x50');

        $expires = 3600;
        header("Pragma: public");
        header("Cache-Control: maxage=".$expires);
        header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
        header("Content-Type: image/png");

        imagepng($image);
        imagedestroy($image);
        exit;
    }

}

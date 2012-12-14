<?php

namespace Admin;

use Core\MediaFile;
use Core\Permission;

class File {

    public function getFiles($pPath){

        //todo, check read access

        $files = MediaFile::getFiles($pPath);
        foreach ($files as &$file){
            $file['writeAccess'] = Permission::checkUpdate('file', $file['id']);
        }
        return $files;
    }


    public function getFile($pPath){

        //todo, check read access

        $file = MediaFile::getFile($pPath);
        $file['writeAccess'] = Permission::checkUpdate('file', $file['id']);
        return $file;

    }

    public function getThumbnail($pPath){

        //todo, check read access

        $image = MediaFile::getThumbnail($pPath, '50x50');

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
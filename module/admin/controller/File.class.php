<?php

namespace Admin;

class File {

    public function getFiles($pPath){

        //todo, check access

        $files = \Core\File::getFiles($pPath);
        foreach ($files as &$file){
            $file['writeAccess'] = \Core\Acl::checkUpdate('file', $file['id']);
        }
        return $files;
    }


    public function getFile($pPath){

        //todo, check access

        $file = \Core\File::getFile($pPath);
        $file['writeAccess'] = \Core\Acl::checkUpdate('file', $file['id']);
        return $file;

    }

    public function getThumbnail($pPath){

        //todo, check access

        $image = \Core\File::getThumbnail($pPath, '50x50');

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
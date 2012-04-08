<?php

class adminFileFieldModel {

    public function getItem($pId){

        $path = krynFile::getPath($pId);

        return array(
            'id' => $pId,
            'path' => $path
        );

    }

}

?>
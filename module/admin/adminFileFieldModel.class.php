<?php

class adminFileFieldModel {

    public function getItem($pId){

        if (is_numeric($pId['id'])){

            $path = krynFile::getPath($pId);

            return array(
                'id' => $pId['id'],
                'path' => $path
            );
        } else {
            return array(
                'id' => $pId['id'],
                'path' => $pId['id']
            );
        }

    }

}

?>
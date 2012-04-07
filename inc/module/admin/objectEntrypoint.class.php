<?php


class objectEntrypoint extends krynObjectAbstract {

    public function getItem($pId, $pFields = '*'){

        return array('path' => $pId);
    }

    public function getItems($pFrom = 0, $pLimit = false, $pCondition = false, $pFields = '*'){

        return array(array('path' => 'admin/test', 'title' => 'test'));
    }

    public function removeItem($pId){

    }

    public function addItem($pId){

    }

    public function updateItem($pId){

    }

    public function getCount($pId, $pCondition){
        return 1;
    }
}

?>
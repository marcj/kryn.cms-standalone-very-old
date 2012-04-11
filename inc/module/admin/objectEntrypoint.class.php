<?php


class objectEntrypoint extends krynObjectAbstract {

    public function getItem($pPrimaryValues, $pFields = '*', $pResolveForeignValues = '*'){

        return array('path' => $pPrimaryValues['path']);
    }

    public function getItems($pPrimaryValues, $pOffset = 0, $pLimit = 0, $pCondition = false, $pFields = '*',
                             $pResolveForeignValues = '*', $pOrder){

        return array(array('path' => 'admin/test', 'title' => 'test'));
    }

    public function removeItem($pId){

    }

    public function addItem($pId){

    }

    public function updateItem($pPrimaryValue, $pValues){

    }

    public function getCount($pCondition = false){
        return 1;
    }
}

?>
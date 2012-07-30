<?php

namespace Admin;

class Object extends \RestServerController {


    public function getItemLabel($pObject, $pPk){



    }


    public function getItem($pObject, $pPk){

        $pPk = \Core\Object::parsePk($pObject, $pPk);

        if (count($pPk) == 1)
            return \Core\Object::get($pObject, $pPk[0]);
        else {
            $items = array();
            foreach ($pPk as $pk){
                $items[] = \Core\Object::get($pObject, $pk);
            }
            return $items;
        }
    }

    public function getItems($pObject, $pFields = null, $pLimit = null, $pOffset = null, $pOrder = null){


        $options = array(
            'fields' => $pFields,
            'limit'  => $pLimit,
            'offset' => $pOffset,
            'order'  => $pOrder
        );

        return \Core\Object::getList($pObject, null, $options);
    }
}
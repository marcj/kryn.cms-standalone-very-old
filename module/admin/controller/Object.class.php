<?php

namespace Admin;

class Object {


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

    public function getItemsByUri($pUrl){

        if (is_numeric($pUrl)){
            //compatibility
            $object_key = '';
        } else {
            list($object_key, $object_ids, $params) = \Core\Object::parseUri($pUrl);
        }

        $definition = \Core\Kryn::$objects[$object_key];
        if (!$definition) return array('error' => 'object_not_found');

        //todo check here access

        if ($definition['chooserFieldDataModel'] == 'custom'){

            $class = $definition['chooserFieldDataModel'];
            $classFile = PATH_MODULE.'/'.$definition['_extension'].'/'.$class.'.class.php';
            if (!file_exists($classFile)) return array('error' => 'classfile_not_found');

            require_once($classFile);
            $dataModel = new $class($object_key);

            $items = $dataModel->getItems($object_ids);

        } else {

            $primaryKeys = \Core\Object::getPrimaries($object_key);

            $fields = array();

            foreach ($definition['chooserFieldDataModelFields'] as $key => $val){
                $fields[] = $key;
            }

            $items = \Core\Object::getList($object_key, $object_ids, array(
                'fields' => $fields,
                'condition' => $definition['chooserFieldDataModelCondition']
            ));
        }

        $res = array();
        if (is_array($items)){
            foreach ($items as &$item){

                $keys = array();
                foreach($primaryKeys as $key => &$field){
                    $keys[] = rawurlencode($item[$key]);
                }
                $res[ implode(',', $keys) ] = $item;
            }
        }

        return $res;
    }
}
<?php

namespace Admin\Object;

class Controller {


    public function getItemLabel($pObject, $pPk){
        
    }

    public function postItem($pObject, $pPk, $pSetFields){

        if (!count($pSetFields))
            throw new \ArgumentMissingException(tf('At least one argument with starting _ is missing.'));

        $primaryKeys = \Core\Object::parsePk($pObject, $pPk);

        $options = array(
            'permissionCheck' => true
        );

        return \Core\Object::update($pObject, $primaryKeys[0], $pSetFields, $options);
    }


    public function deleteItem($pObject, $pPk){
    }

    public function putItem($pObject, $pSetFields){

        if (!count($pSetFields))
            throw new \ArgumentMissingException(tf('At least one argument with starting _ is missing.'));

        $options = array(
            'permissionCheck' => true
        );

        return \Core\Object::add($pObject, $pSetFields, $options);
    }

    /**
     * asd 
     * @param  [type] $pObject [description]
     * @param  [type] $pPk     [description]
     * @param  [type] $pFields [description]
     * @return [type]          [description]
     */
    public function getItem($pObject, $pPk, $pFields = null){

        $primaryKeys = \Core\Object::parsePk($pObject, $pPk);
        $options['fields'] = $pFields;
        $options['permissionCheck'] = true;

        if (count($primaryKeys) == 1)
            return \Core\Object::get($pObject, $primaryKeys[0], $options);
        else {
            foreach ($primaryKeys as $primaryKey){
                if ($item = \Core\Object::get($pObject, $primaryKey, $options))
                $items[] = $item;
            }
            return $items;
        }
    }

    public function getItems($pObject, $pFields = null, $pLimit = null, $pOffset = null,
                             $pOrder = null, $_ = null){

        $options = array(
            'permissionCheck' => true,
            'fields' => $pFields,
            'limit'  => $pLimit,
            'offset' => $pOffset,
            'order'  => $pOrder
        );

        $condition = $this->buildFilter($_);

        return \Core\Object::getList($pObject, $condition, $options);
        
    }

    public function buildFilter($pFilter){
        $condition = null;

        if (is_array($pFilter)){
            //build condition query
            $condition = array();
            foreach ($pFilter as $k => $v){
                if ($condition) $condition[] = 'and';

                $condition[] = array($k, '=', $v);
            }
        }
        return $condition;
    }

    public function getCount($pObject){

        return \Core\Object::getCount($pObject);
    }

    public function getItemsByUri($pUri){

        if (is_numeric($pUri)){
            //compatibility
            $object_key = '';
        } else {
            list($object_key, $object_ids, $params) = \Core\Object::parseUri($pUri);
        }

        //check if we got an id
        if (!current($object_ids[0])){
            throw new \Exception(tf('No id given in uri %s.', $pUri));
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
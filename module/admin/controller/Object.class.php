<?php

namespace Admin;

class Object {


    public function getItemLabel($pObject, $pPk){


    }

    public function handleItem($pMethod, $pObject, $pPk, $pFields = null, $pSetFields = null){

        $primaryKeys = \Core\Object::parsePk($pObject, $pPk);

        $options['fields'] = $pFields;
        $options['permissionCheck'] = true;

        if ($pMethod == 'post' || $pPost == 'put'){
            if (!count($pSetFields))
                throw new \ArgumentMissingException(tf('At least one argument with starting _ is missing.'));
        }

        switch($pMethod){
            case 'get': return \Core\Object::get($pObject, $primaryKeys[0], $options);
            
            case 'post': 
                return \Core\Object::update($pObject, $primaryKeys[0], $pSetFields, $options);

            case 'put': 
                return \Core\Object::add($pObject, $pSetFields, $options);
            

            //todo, implement post,put,delete
        }
    }

    public function getRelatedCount($pRelatedObject, $pPk, $pObject, $pFilter){

        $conditions = \Core\Object::parsePk($pObject, $pPk);

        $options = array(
            'permissionCheck' => true
        );

        $filterCondition = $this->buildFilter($pFilter);

        return \Core\Object::getRelatedCount($pObject, $filterCondition, $pRelatedObject, $conditions[0], $options);
    }

    /**
     * Bla
     * 
     * @param  sring $pMethod
     * @param  string $pObject
     * @param  string $pPk
     * @return mixed
     */
    public function handleRelatedItems($pMethod, $pRelatedObject, $pPk, $pObject, $pFields = null,
                                       $pLimit = null, $pOffset = null, $pOrder = null, $pFilter = null){

        $primaryKeys = \Core\Object::parsePk($pObject, $pPk);

        $options = array(
            'permissionCheck' => true
        );

        $filterCondition = $this->buildFilter($pFilter);

        if ($pMethod == 'get'){

            $options  = array_merge(array(
            'fields' => $pFields,
            'limit'  => $pLimit,
            'offset' => $pOffset,
            'order'  => $pOrder), $options);

            return \Core\Object::getRelatedList($pObject, $filterCondition, $pRelatedObject, $primaryKeys[0], $options);
        }

        return $pRelatedUri;
        switch($pMethod){
            case 'get': return $this->getItem($pObject, $pPk);
            //todo, implement post,put,delete
        }
    }


    public function getItem($pObject, $pPk){

        $conditions = \Core\Object::parsePk($pObject, $pPk);

        $options = array(
            'permissionCheck' => true
        );

        if (count($condition) == 1)
            return \Core\Object::get($pObject, dbSimpleCondition($conditions[0]), $options);
        else {
            foreach ($conditions as $condition){
                if ($item = \Core\Object::get($pObject, dbSimpleCondition($condition), $options))
                $items[] = $item;
            }
            return $items;
        }
    }

    public function handleItems($pMethod, $pObject, $pFields = null, $pLimit = null, $pOffset = null,
                             $pOrder = null, $pFilter = null){

        $options = array(
            'permissionCheck' => true
        );


        if ($pMethod == 'get'){
            $options  = array_merge(array(
                'fields' => $pFields,
                'limit'  => $pLimit,
                'offset' => $pOffset,
                'order'  => $pOrder), $options);

            $condition = $this->buildFilter($pFilter);

            return \Core\Object::getList($pObject, $condition, $options);
        }
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
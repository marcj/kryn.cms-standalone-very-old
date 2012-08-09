<?php

namespace Admin;

class Object {


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


    public function putItem($pObject, $pSetFields){

        if (!count($pSetFields))
            throw new \ArgumentMissingException(tf('At least one argument with starting _ is missing.'));

        $options = array(
            'permissionCheck' => true
        );

        return \Core\Object::add($pObject, $pSetFields, $options);
    }

    public function getRelatedCount($pRelatedObject, $pPk, $pObject, $pFilter){

        $primaryKeys = \Core\Object::parsePk($pObject, $pPk);

        $options = array(
            'permissionCheck' => true
        );

        $filterCondition = $this->buildFilter($pFilter);

        return \Core\Object::getRelatedCount($pObject, $filterCondition, $pRelatedObject, $primaryKeys[0], $options);
    }

    /**
     * Bla
     * 
     * @param  sring $pMethod
     * @param  string $pObject
     * @param  string $pPk
     * @return mixed
     */
    public function getForeignItems($pObject, $pPk, $pField, $pFields = null,
                                    $pLimit = null, $pOffset = null, $pOrder = null, $pFilter = null){

        $primaryKeys = \Core\Object::parsePk($pObject, $pPk);

        if ($pFilter)
            $filterCondition = $this->buildFilter($pFilter);

        $options = array(
            'permissionCheck' => true,
            'fields' => $pFields,
            'limit'  => $pLimit,
            'offset' => $pOffset,
            'order'  => $pOrder
        );

        return \Core\Object::getForeignItems($pObject, $primaryKeys[0], $pField, $filterCondition, $options);
    }


    public function getItem($pObject, $pPk, $pFields){

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
                             $pOrder = null, $pUnderscoreFields = null){

        $options = array(
            'permissionCheck' => true,
            'fields' => $pFields,
            'limit'  => $pLimit,
            'offset' => $pOffset,
            'order'  => $pOrder
        );

        $condition = $this->buildFilter($pUnderscoreFields);

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
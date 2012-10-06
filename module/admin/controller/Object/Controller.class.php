<?php

namespace Admin\Object;

/**
 * Controller
 *
 * Proxy class for \Core\Object
 */
class Controller {

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

        if (!count($_POST))
            throw new \ArgumentMissingException(tf('At least one argument in POST is missing.'));

        $options = array(
            'permissionCheck' => true
        );

        return \Core\Object::add($pObject, $pSetFields, $options);
    }

    /**
     * @param $pObject
     * @param $pPk
     * @param null $pFields
     * @return array|bool
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

    public function getTreeBranch($pObject, $pParentPrimaryKey, $pDepth = 1, $pScope = null, $pFields = null){

        $options['fields'] = $pFields;
        $options['permissionCheck'] = true;

        $primaryKeys = \Core\Object::parsePk($pObject, $pParentPrimaryKey);

        if (!$options['fields']){
            //use default fields from object definition
            $definition = \Core\Kryn::$objects[$pObject];
            $options['fields'][] = $definition['label'];

            if ($definition['chooserBrowserTreeIcon'])
                $options['fields'][] = $definition['chooserBrowserTreeIcon'];

            if ($definition['chooserFieldDataModelFieldExtraFields']){
                $extraFields = explode(',', trim(preg_replace('/[^a-zA-Z0-9_,]/', '', $definition['chooserFieldDataModelFieldExtraFields'])));
                foreach($extraFields as $field)
                    $options['fields'][] = $field;
            }

        }
        return \Core\Object::getTree($pObject, $primaryKeys[0], $condition = false, $pDepth, $pScope, $options);
    }

    public function moveItem($pObjectKey, $pId, $pTo, $pWhere = 'into'){

        $options = array('permissionCheck' => true);

        list($targetObjectKey, $targetObjectId, $targetParams) = \Core\Object::parseUri($pTo);

        return \Core\Object::move($pObjectKey, $pId, $targetObjectId[0], $pWhere, $targetObjectKey, $options);
    }

    public function getTreeRoot($pObject, $pScope){

        $options = array('permissionCheck' => true);

        return \Core\Object::getTreeRoot($pObject, $pScope, $options);
    }

    public function getTree($pObject, $pDepth = 1, $pScope = null, $pFields = null){

        $options['fields'] = $pFields;
        $options['permissionCheck'] = true;

        if (!$options['fields']){
            $options['fields'] = array();

            //use default fields from object definition
            $definition = \Core\Kryn::$objects[$pObject];
            $options['fields'][] = $definition['nestedLabel'];

            if ($definition['chooserBrowserTreeIcon'])
                $options['fields'][] = $definition['chooserBrowserTreeIcon'];

            if ($definition['chooserFieldDataModelFieldExtraFields']){
                $extraFields = explode(',', trim(preg_replace('/[^a-zA-Z0-9_,]/', '', $definition['chooserFieldDataModelFieldExtraFields'])));
                foreach($extraFields as $field)
                    $options['fields'][] = $field;
            }
        }

        return \Core\Object::getTree($pObject, null, $condition = false, $pDepth, $pScope, $options);
    }

    /**
     * @param $pObject
     * @param null $pFields
     * @param null $pLimit
     * @param null $pOffset
     * @param null $pOrder
     * @param null $_
     * @return array|bool
     */
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

    /**
     * @param $pFilter
     * @return array|null
     */
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

    /**
     * @param $pObject
     * @return array
     */
    public function getCount($pObject){

        return \Core\Object::getCount($pObject);
    }

    /**
     * @param $pUri
     * @return array|bool
     * @throws ObjectNotFoundException
     */
    public function getItemPerUri($pUri){

        list($object_key, $object_id, $params) = \Core\Object::parseUri($pUri);

        $definition = \Core\Kryn::$objects[$object_key];
        if (!$definition) throw new ObjectNotFoundException(tf('Object %s does not exists.', $object_key));

        if ($definition['chooserFieldDataModel'] == 'custom'){

            $class = $definition['chooserFieldDataModelClass'];

            $dataModel = new $class($object_key);

            $item = $dataModel->getItem($object_id[0]);
            return array(
                'object' => $object_key,
                'values' => $item
            );

        } else {

            $fields[] = $definition['chooserFieldDataModelField'];

            if ($definition['chooserFieldDataModelCondition']){
                $condition = $definition['chooserFieldDataModelCondition'];
            }

            $item = \Core\Object::get($object_key, $object_id[0], array(
                'fields' => $fields,
                'condition' => $condition
            ));

            return $item;

        }
    }

    /**
     * @param $pUri
     * @return array
     * @throws \Exception
     */
    public function getItemsByUri($pUri){

        list($object_key, $object_ids, $params) = \Core\Object::parseUri($pUri);

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

        $c = count($primaryKeys);
        $firstPK = key($primaryKeys);

        $res = array();
        if (is_array($items)){
            foreach ($items as &$item){

                if ($c > 1){
                    $keys = array();
                    foreach($primaryKeys as $key => &$field){
                        $keys[] = rawurlencode($item[$key]);
                    }
                    $res[ implode(',', $keys) ] = $item;
                } else {
                    $res[$item[$firstPK]] = $item;
                }
            }
        }

        return $res;
    }
}
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
        //TODO
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
     * General output of one object item. /admin/backend/object/<objectKey>/<primaryKey>
     *
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
     * General output of object items. /admin/backend/objects/<objectKey>
     *
     * @param string $pObject
     * @param string $pFields
     * @param int    $pLimit
     * @param int    $pOffset
     * @param array  $pOrder
     * @param mixed  $_
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

                if (strpos($v, '*') !== false){
                    $condition[] = array(substr($k, 1), 'LIKE', str_replace('*', '%', $v));
                } else {
                    $condition[] = array(substr($k, 1), '=', $v);
                }
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
     * General object items output. /admin/backend/object?uri=...
     *
     * @param string $pUri
     * @param string $pFields
     * @return array|bool
     * @throws \ObjectNotFoundException
     */
    public function getItemPerUri($pUri, $pFields = null){

        list($object_key, $object_id) = \Core\Object::parseUri($pUri);

        $definition = \Core\Kryn::$objects[$object_key];
        if (!$definition) throw new \ObjectNotFoundException(tf('Object %s does not exists.', $object_key));

        return \Core\Object::get($object_key, $object_id[0], array('fields' => $pFields, 'permissionCheck' => true));
    }


    /**
     * Object items output for user interface field.  /admin/backend/field-object?uri=...
     *
     * @param string $pUri
     * @param string $pFields
     * @return array|bool
     * @throws \ObjectNotFoundException
     * @throws \ClassNotFoundException
     */
    public function getFieldItem($pObjectKey, $pPk, $pFields = null){

        $definition = \Core\Kryn::$objects[$pObjectKey];
        if (!$definition) throw new \ObjectNotFoundException(tf('Object %s does not exists.', $pObjectKey));

        if ($definition['chooserFieldDataModel'] != 'custom'){
            return \Core\Object::get($pObjectKey, $pPk);
        } else {

            $class = $definition['chooserFieldDataModelClass'];
            if (!class_exists($class)) throw new \ClassNotFoundException(tf('Class %s can not be found.', $class));
            $dataModel = new $class($pObjectKey);

            return $dataModel->getItem($pPk, array('fields' => $pFields, 'permissionCheck' => true));
        }
    }


    public function getFieldItemsCount($pObject){
        //TODO
        return 'TODO';
    }

    /**
     * General object items output. /admin/backend/objects?uri=...
     *
     * @param string $pUri
     * @param string $pFields
     * @param bool   $pReturnKey Returns the list as a hash with the primary key as index. key=implode(',',rawurlencode($keys))
     * @param bool   $pReturnKeyAsRequested. Returns the list as a hash with the requested id as key.
     * @return array
     * @throws \Exception
     * @throws \ClassNotFoundException
     * @throws \ObjectNotFoundException
     */
    public function getItemsByUri($pUri, $pFields = null, $pReturnKey = true, $pReturnKeyAsRequested = false){

        list($object_key, $object_ids, $params) = \Core\Object::parseUri($pUri);
        //check if we got an id
        if (!current($object_ids[0])){
            throw new \Exception(tf('No id given in uri %s.', $pUri));
        }

        $definition = \Core\Kryn::$objects[$object_key];
        if (!$definition) throw new \ObjectNotFoundException(tf('Object %s can not be found.', $object_key));

        $items = \Core\Object::getList($object_key, $object_ids, array(
            'fields' => $pFields,
            'permissionCheck' => true
        ));

        if ($pReturnKey || $pReturnKeyAsRequested) {


            $res = array();
            if ($pReturnKeyAsRequested){

                //map requetsed id to real ids
                $requestedIds = explode(',', \Core\Object::getCroppedObjectId($pUri));
                $map = array();
                foreach ($requestedIds as $id){
                    $pk = \Core\Object::parsePk($object_key, $id);
                    $map[\Core\Object::getObjectUrlId($object_key, $pk[0])+''] = $id;
                }

                if (is_array($items)){
                    foreach ($items as &$item){
                        $pk = \Core\Object::getObjectUrlId($object_key, $item);
                        $res[$map[$pk+'']] = $item;
                    }
                }

            } else {
                $primaryKeys = \Core\Object::getPrimaries($object_key);

                $c = count($primaryKeys);
                $firstPK = key($primaryKeys);

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
            }

            return $res;
        } else {
            return $items;
        }
    }



    /**
     * Object items output for user interface field. /admin/backend/field-objects?uri=...
     *
     *
     * This method does check against object property 'chooserFieldDataModelClass'. If set, we use
     * this class to get the items.
     *
     * @param string $pObjectKey
     * @param string $pFields
     * @param bool   $pReturnHash Returns the list as a hash with the primary key as index.
     * @param int    $pLimit
     * @param int    $pOffset
     * @param array  $pOrder
     * @param mixed  $_
     *
     * @return array
     * @throws \Exception
     * @throws \ClassNotFoundException
     * @throws \ObjectNotFoundException
     */
    public function getFieldItems($pObjectKey, $pFields = null, $pReturnHash = true, $pLimit = null, $pOffset = null,
                                  $pOrder = null, $_ = null){

        $definition = \Core\Kryn::$objects[$pObjectKey];
        if (!$definition) throw new \ObjectNotFoundException(tf('Object %s can not be found.', $pObjectKey));

        $options = array(
            'permissionCheck' => true,
            'fields' => $pFields,
            'limit'  => $pLimit,
            'offset' => $pOffset,
            'order'  => $pOrder
        );

        $condition = $this->buildFilter($_);

        if ($definition['fieldDataModel'] == 'custom'){

            $class = $definition['fieldDataModelClass'];
            if (!class_exists($class)) throw new \ClassNotFoundException(tf('The class %s can not be found.', $class));

            $dataModel = new $class($pObjectKey);

            $items = $dataModel->getItems($condition, $options);

        } else {

            $items = \Core\Object::getList($pObjectKey, $condition, $options);

        }

        if ($pReturnHash) {
            $primaryKeys = \Core\Object::getPrimaries($pObjectKey);

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
        } else {
            return $items;
        }
    }


    /**
     * Object items output for user interface chooser window/browser. /admin/backend/browser-objects?uri=...
     *
     * This method does check against object property 'browserDataModel'. If custom, we use
     * this class to get the items.
     *
     * @param string $pObjectKey
     * @param string $pFields
     * @param bool   $pReturnHash Returns the list as a hash with the primary key as index.
     *
     * @param int    $pLimit
     * @param int    $pOffset
     * @param array  $pOrder
     * @param mixed  $_
     *
     * @return array
     * @throws \Exception
     * @throws \ClassNotFoundException
     * @throws \ObjectNotFoundException
     */
    public function getBrowserItems($pObjectKey, $pFields = null, $pReturnHash = true, $pLimit = null, $pOffset = null,
                                    $pOrder = null, $_ = null){


        $definition = \Core\Kryn::$objects[$pObjectKey];
        if (!$definition) throw new \ObjectNotFoundException(tf('Object %s can not be found.', $pObjectKey));

        if (!$definition['browserColumns'])
            throw new \ObjectMisconfiguration(tf('Object %s does not have browser columns.', $pObjectKey));

        $fields = array_keys($definition['browserColumns']);

        $options = array(
            'permissionCheck' => true,
            'fields' => $fields,
            'limit'  => $pLimit,
            'offset' => $pOffset,
            'order'  => $pOrder
        );

        $condition = $this->buildFilter($_);

        if ($definition['browserDataModel'] == 'custom'){

            $class = $definition['browserDataModelClass'];
            if (!class_exists($class)) throw new \ClassNotFoundException(tf('The class %s can not be found.', $class));

            $dataModel = new $class($pObjectKey);

            $items = $dataModel->getItems($condition, $options);

        } else {

            $items = \Core\Object::getList($pObjectKey, $condition, $options);

        }

        if ($pReturnHash) {
            $primaryKeys = \Core\Object::getPrimaries($pObjectKey);

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
        } else {
            return $items;
        }
    }
}
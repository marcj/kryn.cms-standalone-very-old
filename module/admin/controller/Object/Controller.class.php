<?php

namespace Admin\Object;

use Core\Object;

/**
 * Controller
 *
 * Proxy class for \Core\Object
 */
class Controller {

    /**
     * General object items output. /admin/object?uri=...
     *
     * @param string $pUri
     * @param string $pFields
     * @return array|bool
     * @throws \ObjectNotFoundException
     */
    public function getItemPerUri($pUri, $pFields = null){

        list($objectKey, $object_id) = \Core\Object::parseUri($pUri);

        $definition = \Core\Object::getDefinition($objectKey);
        if (!$definition) throw new \ObjectNotFoundException(tf('Object %s does not exists.', $objectKey));

        return \Core\Object::get($objectKey, $object_id[0], array('fields' => $pFields, 'permissionCheck' => true));
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

        $definition = \Core\Object::getDefinition($pObjectKey);
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



    /**
     * General object items output. /admin/objects?uri=...
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

        list($objectKey, $objectIds, $params) = \Core\Object::parseUri($pUri);
        //check if we got an id
        if ($objectIds[0] === ''){
            throw new \Exception(tf('No id given in uri %s.', $pUri));
        }

        $definition = \Core\Object::getDefinition($objectKey);
        if (!$definition) throw new \ObjectNotFoundException(tf('Object %s can not be found.', $objectKey));


        $options['fields'] = $pFields;
        $options['permissionCheck'] = true;

        $items = array();
        if (count($objectIds) == 1)
            $items[] = \Core\Object::get($objectKey, $objectIds[0], $options);
        else {
            foreach ($objectIds as $primaryKey){
                if ($item = \Core\Object::get($objectKey, $primaryKey, $options))
                    $items[] = $item;
            }
        }

        if ($pReturnKey || $pReturnKeyAsRequested) {


            $res = array();
            if ($pReturnKeyAsRequested){

                //map requetsed id to real ids
                $requestedIds = explode(',', \Core\Object::getCroppedObjectId($pUri));
                $map = array();
                foreach ($requestedIds as $id){
                    $pk = \Core\Object::parsePk($objectKey, $id);
                    $map[\Core\Object::getObjectUrlId($objectKey, $pk[0])+''] = $id;
                }

                if (is_array($items)){
                    foreach ($items as &$item){
                        $pk = \Core\Object::getObjectUrlId($objectKey, $item);
                        $res[$map[$pk+'']] = $item;
                    }
                }

            } else {
                $primaryKeys = \Core\Object::getPrimaries($objectKey);

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

        $definition = \Core\Object::getDefinition($pObjectKey);
        if (!$definition) throw new \ObjectNotFoundException(tf('Object %s can not be found.', $pObjectKey));

        $options = array(
            'permissionCheck' => true,
            'fields' => $pFields,
            'limit'  => $pLimit,
            'offset' => $pOffset,
            'order'  => $pOrder
        );

        $condition = \Admin\ObjectCrud::buildFilter($_);

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
     * Object items output for user interface chooser window/browser. /admin/backend/browser-objects/<objectKey>
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
     * @throws \ObjectNotFoundException
     * @throws \ClassNotFoundException
     * @throws \ObjectMisconfiguration
     */
    public function getBrowserItems($pObjectKey, $pFields = null, $pReturnHash = true, $pLimit = null, $pOffset = null,
                                    $pOrder = null, $_ = null){

        $definition = \Core\Object::getDefinition($pObjectKey);
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

        $condition = \Admin\ObjectCrud::buildFilter($_);

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

    public function getBrowserItemsCount($pObjectKey,$_ = null){

        $definition = \Core\Object::getDefinition($pObjectKey);
        if (!$definition) throw new \ObjectNotFoundException(tf('Object %s can not be found.', $pObjectKey));

        if (!$definition['browserColumns'])
            throw new \ObjectMisconfiguration(tf('Object %s does not have browser columns.', $pObjectKey));

        $fields = array_keys($definition['browserColumns']);

        $options = array(
            'permissionCheck' => true
        );

        $condition = \Admin\ObjectCrud::buildFilter($_);

        if ($definition['browserDataModel'] == 'custom'){

            $class = $definition['browserDataModelClass'];
            if (!class_exists($class)) throw new \ClassNotFoundException(tf('The class %s can not be found.', $class));

            $dataModel = new $class($pObjectKey);

            $count = $dataModel->getCount($condition, $options);

        } else {

            $count = \Core\Object::getCount($pObjectKey, $condition, $options);

        }

        return $count;
    }
}
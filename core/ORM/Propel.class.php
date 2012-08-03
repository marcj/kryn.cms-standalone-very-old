<?php

namespace Core\ORM;

class Propel extends \Core\ORM\ORMAbstract {

    /**
     * Returns the query class.
     *
     * @param $pObject
     * @return Object
     */
    public static function getQueryClass($pObject){

        $clazz = '\\'.ucfirst($pObject).'Query';
        if (!class_exists($clazz)){
            throw new \ObjectNotFoundException(tf('The object query %s of %s does not exist.', $clazz, $pObject));
        }

        return $clazz::create();
    }


    /**
     * @param $pPk
     * @param array $pFields
     * @param string $pResolveForeignValues
     *
     * @return mixed
     *
     * @throws \ObjectItemNotFoundException
     */
    public function getItem($pPk, $pFields = array(), $pResolveForeignValues = '*'){

        $qClazz = self::getQueryClass($this->object_key);

        //since the core provide the pk as array('id' => 123) and not as array(123) we have to convert it
        $pPk = array_values($pPk);
        if (count($pPk) == 1) $pPk = $pPk[0];

        $item = $qClazz->select($pFields)->findPk($pPk);

        if (!$item){
            throw new \ObjectItemNotFoundException(tf("The object '%s' in '%s' can not be found.", $pPk, $this->object_key));
        }

        return $item;
    }

    /**
     *
     * $pOptions is a array which can contain following options. All options are optional.
     *
     *  'fields'          Limit the columns selection. Use a array or a comma separated list (like in SQL SELECT)
     *                    If empty all columns will be selected.
     *  'offset'          Offset of the result set (in SQL OFFSET)
     *  'limit'           Limits the result set (in SQL LIMIT)
     *  'order'           The column to order. Example:
     *                    array(
     *                      array('field' => 'category', 'direction' => 'asc'),
     *                      array('field' => 'title',    'direction' => 'asc')
     *                    )
     *
     *  'foreignKeys'     Defines which column should be resolved. If empty all columns will be resolved.
     *                    Use a array or a comma separated list (like in SQL SELECT). 'field1, field2, field3'
     *
     *  'permissionCheck' Defines whether we check against the ACL or not. true or false. default false
     *
     *
     *
     * @param bool|array   $pCondition
     * @param bool|array   $pOptions
     *
     * @return mixed
     *
     */
    public function getItems($pCondition, $pOptions = array()){
        $qClazz = self::getQueryClass($this->object_key);

        $fields = $pOptions['fields'];

        if ($pCondition){
            $where = dbConditionToSql($pCondition);
            $qClazz->where($where);
        }

        $qClazz->select($fields);

        $items = $qClazz->find()->toArray();

        return $items;
    }

    /**
     * @param $pPrimaryValues
     *
     */
    public function remove($pPrimaryValues){
        // TODO: Implement remove() method.
    }

    /**
     * @param $pValues
     * @param $pParentValues
     * @param $pMode
     * @param $pParentObjectKey
     *
     * @return inserted primary key. (last_insert_id() for SQL backend)
     */
    public function add($pValues, $pParentValues = false, $pMode = 'into', $pParentObjectKey = false){
        // TODO: Implement add() method.
    }

    /**
     * Updates an object
     *
     * @param $pPrimaryValues
     * @param $pValues
     */
    public function update($pPrimaryValues, $pValues){
        // TODO: Implement update() method.
    }

    /**
     * @param bool|string $pCondition
     *
     * @return int
     */
    public function getCount($pCondition = false){
        // TODO: Implement getCount() method.
    }

    public function getTree($pBranch = false, $pCondition = false, $pDepth = 1, $pRootObjectId = false, $pOptions = false){
        // TODO: Implement getTree() method.
    }


}
<?php

namespace Core\ORM;

/**
 * ORM Abstract class for objects.
 *
 * 
 * $pPk is an array with following format
 *
 *  array(
 *      '<keyName>'  => <value>
 *      '<keyName2>' => <value2>
 *  )
 *  
 */
abstract class ORMAbstract {


    /**
     * Cached primary key order.
     * Only keys.
     *
     * @var array
     */
    public $primaryKeys = array();

    public function setPrimaryKeys($pPrimaryKeys){
        $this->primaryKeys = $pPrimaryKeys;
    }

    /**
     * Returns a field definition.
     *
     * @param string $pFieldKey
     * @return array
     */
    public function &getField($pFieldKey){
        return $this->definition['fields'][strtolower($pFieldKey)];
    }

    /**
     * Returns the primary keys as array.
     * 
     * @return array [key1, key2, key3]
     */
    public function getPrimaryKeys(){
        return $this->primaryKeys;
    }


    /**
     * Returns the object key.
     * 
     * @return string
     */
    public function getObjectKey(){
        return $this->object_key;
    }


    /**
     * Converts given primary values from type string into proper array definition.
     * Generates a array for the usage of Core\Object:get()
     *
     * @param string $pPk
     *
     * @return array
     */
    public function primaryStringToArray($pPk){

        if ($pPk === '') return false;
        $groups = explode(',', $pPk);

        $result = array();

        foreach ($groups as $group){

            $item = array();
            $primaryGroups = explode('-', $group);

            foreach ($primaryGroups as $pos => $value){

                if ($ePos = strpos($value, '=')){
                    $key = substr($value, 0, $ePos);
                    if (!in_array($key, $this->primaryKeys)) continue;
                } else if (!$this->primaryKeys[$pos]) continue;

                $key = $this->primaryKeys[$pos];

                $item[$key] = urldecode($value);
            }

            if (count($item) > 0)
                $result[] = $item;
        }

        return $result;

    }

    /**
     *
     * Returns a object item as array.
     *
     * @abstract
     * @param bool|array  $pCondition
     * @param bool|array  $pOptions
     *
     * @return array
     */
    abstract public function getItem($pCondition, $pOptions = false);


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
     * @abstract
     * @param array  $pCondition
     * @param array  $pOptions
     */
    abstract public function getItems($pCondition = null, $pOptions = null);

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
     * @abstract
     * @param array  $pPk
     * @param array  $pRelatedObject
     * @param array  $pRelatedCondition
     * @param array  $pOptions
     */
    //abstract public function getRelatedItems($pConditon = null, $pRelatedObject, $pRelatedPk, $pOptions = array());

    /**
     * 
     * @abstract
     * @param array $pPk
     *
     */
    abstract public function remove($pPk);

    /**
     * @abstract
     * @param array  $pValues
     * @param mixed  $pBranchPk If nested set
     * @param string $pMode  If nested set. 'first' (child), 'last' (child), 'prev' (sibling), 'next' (sibling)
     * @param int  $pScope If nested set with scope
     *
     * @return mixed inserted primary key/s. If the object has multiple PKs, it returns a array.
     */
    abstract public function add($pValues, $pBranchPk = false, $pMode = 'into', $pScope = 0);

    /**
     * Updates an object
     *
     * @abstract
     * @param $pPk
     * @param $pValues
     */
    abstract public function update($pPk, $pValues);

    /**
     * @abstract
     * @param bool|string $pCondition
     *
     * @return int
     */
    abstract public function getCount($pCondition = false);

    /**
     * Returns a branch if the object is a nested set.
     *
     * @param  mixed $pPk
     * @param  mixed $pCondition
     * @param  int   $pDepth
     * @param  int   $pScope
     * @abstract
     *
     * @return  array|bool
     */
    abstract public function getBranch($pPk = false, $pCondition = false, $pDepth = 1, $pScope = 0,
        $pOptions = false);


    /**
     * Returns the parent if exists otherwise false.
     *
     * @param  $pPk
     * @return mixed
     */
    public function getParent($pPk){

        return false;
    }


    /**
     * Returns parent's id, if exists
     *
     * @param $pPk
     * @return array
     */
    public function getParentId($pPk){
        $object = $this->getParent($pPk);

        if (!$object) return false;

        if (count($this->primaryKeys) == 1){
            return $object[key($this->primaryKeys)];
        } else {
            $result = array();
            foreach ($this->primaryKeys as $key){
                $result[] = $object[$key];
            }
            return $result;
        }
    }

}

?>
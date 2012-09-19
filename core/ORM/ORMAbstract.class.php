<?php

namespace Core\ORM;

/**
 * ORM Abstract class for objects.
 *
 * 
 * $pPrimaryKey is an array with following format
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

    /**
     * Constructor
     *
     * @param string $pObjectKey
     * @param array  $pDefinition
     */
    function __construct($pObjectKey, $pDefinition){
        $this->objectKey = $pObjectKey;
        $this->definition = $pDefinition;
        foreach($this->definition['fields'] as $key => $field){
            if ($field['primaryKey'])
                $this->primaryKeys[] = $key;
        }
    }

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
        return $this->definition['fields'][$pFieldKey];
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
     * Normalize the primary key.
     * Possible input:
     *  array('bla'), 'peter', 123,
     * Output
     *  array('id' => 'bla'), array('id' => 'peter'), array('id' => 123)
     *  if the only primary key is named `id`.
     *
     * @param mixed $pPrimaryKey
     * @return array
     */
    public function normalizePrimaryKey($pPrimaryKey){
        if (!is_array($pPrimaryKey)){
            $result = array();
            $result[current($this->primaryKeys)] = $pPrimaryKey;
            return $result;
        } else if (is_numeric(key($pPrimaryKey))){
            $result = array();
            $length = count($this->primaryKeys);
            for($i=0; $i<$length; $i++){
                $result[$this->primaryKeys[$i]] = $pPrimaryKey[$i];
            }
            return $result;
        } else{
            return $pPrimaryKey;
        }
    }

    /**
     * Converts given primary values from type string into proper array definition.
     * Generates a array for the usage of Core\Object:get()
     *
     * @param string $pPrimaryKey
     *
     * @return array
     */
    public function primaryStringToArray($pPrimaryKey){

        if ($pPrimaryKey === '') return false;
        $groups = explode(',', $pPrimaryKey);

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
     * $pOptions is a array which can contain following options. All options are optional.
     *
     *  'fields'          Limit the columns selection. Use a array or a comma separated list (like in SQL SELECT)
     *                    If empty all columns will be selected.
     *
     *  'permissionCheck' Defines whether we check against the ACL or not. true or false. default false
     *
     *
     * @abstract
     * @param array       $pPrimaryKey
     * @param bool|array  $pOptions
     *
     * @return array
     */
    abstract public function getItem($pPrimaryKey, $pOptions = false);

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
     *                      array('category' => 'asc'),
     *                      array(title' => 'asc')
     *                    )
     *
     *  'permissionCheck' Defines whether we check against the ACL or not. true or false. default false
     *
     *
     * @abstract
     * @param array  $pCondition Condition object as it is described in function dbConditionToSql() #Extended.
     * @param array  $pOptions
     */
    abstract public function getItems($pCondition = null, $pOptions = null);

    /**
     * 
     * @abstract
     * @param array $pPrimaryKey
     *
     */
    abstract public function remove($pPrimaryKey);

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
     * @param $pPrimaryKey
     * @param $pValues
     */
    abstract public function update($pPrimaryKey, $pValues);

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
     * @param bool  $pParentPrimaryKey
     * @param bool  $pCondition
     * @param int   $pDepth
     * @param int   $pScope
     * @param mixed $pOptions
     * @abstract
     * @return mixed
     */
    #abstract public function getBranch($pParentPrimaryKey = false, $pCondition = false, $pDepth = 1, $pScope = 0,
    #    $pOptions = false);


    /**
     * Returns the parent if exists otherwise false.
     *
     * @param  $pPrimaryKey
     * @return mixed
     */
    public function getParent($pPrimaryKey){

        return false;
    }


    /**
     * Returns parent's id, if exists
     *
     * @param $pPrimaryKey
     * @return array
     */
    public function getParentId($pPrimaryKey){
        $object = $this->getParent($pPrimaryKey);

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
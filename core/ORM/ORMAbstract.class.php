<?php

namespace Core\ORM;

/**
 * ORM Abstract class for objects.
 *
 * Please do not handle 'permissionCheck' in $pOptions. This is handled in \Core\Object.
 * You will get in getList()  a complex $pCondition object instead (if there are any ACL items)
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
     * The current object key.
     *
     * @var string
     */
    public $objectKey;

    /**
     * Cached the object definition.
     *
     * @var array
     */
    public $definition;



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
     * Normalizes a primary keys, that are normally used inside PHP classes,
     * since developers are lazy and we need to convert the lazy primary key
     * to the full definition.
     *
     * Possible input:
     *  array('bla'), 'peter', 123,
     *
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
     * Converts given primary values from type string into proper normalized array definition.
     * This builds the array for the $pPrimaryKey for all of these methods inside this class.
     *
     * The primaryKey comes primaraly from the REST API.
     *
     *    admin/backend/object/news/1
     *    admin/backend/objects?uri=news/1,2
     * where
     *    admin/backend/object/news/<id>
     *    admin/backend/objects?uri=news/<id>
     *
     * is this ID.
     *
     * 1,2,3 => array( array(id =>1),array(id =>2),array(id =>3) )
     * 1 => array(array(id => 1))
     * idFooBar => array( id => "idFooBar")
     * idFoo-Bar => array(array(id => idFoo, id2 => "Bar"))
     * 1-45, 2-45 => array(array(id => 1, pid = 45), array(id => 2, pid=>45))
     *
     *
     *
     *
     * @param string $pPrimaryKey
     * @return array
     */
    public function primaryStringToArray($pPrimaryKey){

        if ($pPrimaryKey === '') return false;
        $groups = explode(';', $pPrimaryKey);

        $result = array();

        foreach ($groups as $group){

            $item = array();
            $primaryGroups = explode(',', $group);

            foreach ($primaryGroups as $pos => $value){

                if ($ePos = strpos($value, '=')){
                    $key = substr($value, 0, $ePos);
                    $value = substr($value, $ePos+1);
                    if (!in_array($key, $this->primaryKeys)) continue;
                } else if (!$this->primaryKeys[$pos]) continue;

                $key = $this->primaryKeys[$pos];

                $item[$key] = rawurldecode($value);
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
     * @param array  $pPrimaryKey
     * @param array  $pOptions
     *
     * @return array
     */
    abstract public function getItem($pPrimaryKey, $pOptions = null);

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
     * @param array  $pBranchPk If nested set
     * @param string $pMode  If nested set. 'first' (child), 'last' (child), 'prev' (sibling), 'next' (sibling)
     * @param int    $pScope If nested set with scope
     *
     * @return array inserted/new primary key/s always as a array.
     */
    abstract public function add($pValues, $pBranchPk = null, $pMode = 'into', $pScope = null);

    /**
     * Updates an object
     *
     * @abstract
     * @param array $pPrimaryKey
     * @param array $pValues
     * @throws \ObjectItemNotModified
     */
    abstract public function update($pPrimaryKey, $pValues);

    /**
     * @abstract
     * @param array $pCondition
     *
     * @return int
     */
    abstract public function getCount($pCondition = null);


    /**
     * Do whatever is needed, to clear all items out of this object scope.
     *
     * @abstract
     * @return bool
     */
    abstract public function clear();


    /**
     * Removes anything that is required to hold the data. E.g. SQL Tables, Drop Sequences, etc.
     *
     * @abstract
     * @return bool
     */
    public function drop(){

    }


    /**
     *
     *
     * @param        $pPk
     * @param        $pTargetPk
     * @param string $pMode into, below|down, before|up
     * @param        $pTargetObjectKey
     * @throws      \NotImplementedException
     */
    public function move($pPk, $pTargetPk = null, $pMode = 'into', $pTargetObjectKey = null){
        throw new \NotImplementedException('Move method is not implemented for this object layer.');
    }

    /**
     * Returns a branch if the object is a nested set.
     *
     * Result should be:
     *
     *  array(
     *
     *    array(<valuesFromFirstItem>, '_children' => array(<children>), '_childrenCount' => <int> ),
     *    array(<valuesFromSecondItem>, '_children' => array(<children>), '_childrenCount' => <int> ),
     *    ...
     *
     *  )
     *
     * @param array $pParentPrimaryKey
     * @param array $pCondition
     * @param int   $pDepth Started with one. One means, only the first level, no children at all.
     * @param mixed $pScope
     * @param array $pOptions
     *
     * @return array
     */
    public function getTree($pParentPrimaryKey = null, $pCondition = null, $pDepth = 1, $pScope = null, $pOptions = null){
        if (!$this->definition['nested']) throw new \Exception(t('Object %s it not a nested set.', $this->objectKey));
        throw new \NotImplementedException(t('getTree is not implemented.'));
    }



    /**
     * Returns the parent if exists otherwise false.
     *
     * @param array $pPrimaryKey
     * @return mixed
     */
    public function getParent($pPrimaryKey){
        throw new \NotImplementedException(t('getParent is not implemented.'));
    }


    /**
     * Returns parent's id, if exists
     *
     * @param array $pPrimaryKey
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
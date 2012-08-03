<?php

namespace Core\ORM;

abstract class ORMAbstract {


    /**
     * Object definition
     *
     * @var array
     */
    public $definition = array();


    /**
     * The key of the object
     *
     * @var string
     */
    public $object_key = '';


    /**
     * Cached primary key order
     * Only keys
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
        $this->object_key = $pObjectKey;
        $this->definition = $pDefinition;

        if (is_array($this->definition['fields'])){
            foreach ($this->definition['fields'] as $key => $field){
                if ($field['primaryKey'])
                    $this->primaryKeys[] = $key;
            }
        }
    }


    /**
     * Filters $pFields by allowed fields.
     * If '*' we return all allowed fields.
     *
     * @param array|string $pFields
     * @return array
     */
    public function getFields($pFields){

        $fields = array();

        if ($pFields === '*'){
            if ($this->definition['limitSelection']){
                $fields = $this->definition['limitSelection'];
            } else {
                foreach ($this->definition['fields'] as $key => $field){
                    $fields[] = $key;
                }
                return $fields;
            }
        } else if (is_array($pFields)){
            $fields = $pFields;
        }

        if (is_string($fields)){
            $fields = explode(',', str_replace(' ', '', trim($fields)));
        }

        $fields = array_unique(is_array($fields)?array_merge($this->primaryKeys, $fields):$this->primaryKeys);

        if ($this->definition['limitSelection']){

            $allowedFields = strtolower(','.str_replace(' ', '', trim($this->definition['limitSelection'])).',');

            foreach ($fields as $idx => $name){
                if (strpos($allowedFields, strtolower(','.$name.',')) === false){
                    array_splice($fields, $idx, 1);
                }
            }
            return $fields;
        } else return $fields;

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

        if (!$pPk) return false;
        $groups = explode('/', $pPk);

        $result = array();

        foreach ($groups as $group){

            $item = array();
            $primaryGroups = explode(',', $group);

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
     * @param mixed  $pPk
     * @param string $pFields
     * @param string $pResolveForeignValues
     *
     * @return array
     */
    abstract public function getItem($pPk, $pFields = '*', $pResolveForeignValues = '*');

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
     * @param bool|array  $pPk
     * @param bool|array  $pOptions
     */
    abstract public function getItems($pPk, $pOptions = false);

    /**
     * @abstract
     * @param $pPk
     *
     */
    abstract public function remove($pPk);

    /**
     * @abstract
     * @param $pValues
     * @param $pParentPk
     * @param $pMode
     * @param $pParentObjectKey
     *
     * @return inserted primary key. (last_insert_id() for SQL backend)
     */
    abstract public function add($pValues, $pParentPk = false, $pMode = 'into', $pParentObjectKey = false);

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


    abstract public function getTree($pBranch = false, $pCondition = false, $pDepth = 1, $pRootObjectId = false, $pOptions = false);

    /**
     * Returns the parent, if exists
     *
     * @param  $pPk
     * @return bool
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
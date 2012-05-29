<?php

abstract class krynObjectAbstract {

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
     * Converts given primary values from type string into proper array definition.
     * Generates a array for the usage of krynObject:get()
     *
     * @param string $pPrimaryValue
     *
     * @return array
     */
    public function primaryStringToArray($pPrimaryValue){

        if (!$pPrimaryValue) return false;
        $groups = explode('/', $pPrimaryValue);

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
     *
     * @abstract
     * @param mixed $pPrimaryValues
     * @param string $pFields
     * @param string $pResolveForeignValues
     */
    abstract public function getItem($pPrimaryValues, $pFields = '*', $pResolveForeignValues = '*');

    /**
     *
     * @abstract
     * @param mixed $pPrimaryValues
     * @param int $pOffset
     * @param int $pLimit
     * @param bool $pCondition
     * @param string $pFields
     * @param string $pResolveForeignValues
     * @param $pOrder
     */
    abstract public function getItems($pCondition, $pOffset = 0, $pLimit = 0, $pFields = '*', $pResolveForeignValues = '*',
                                      $pOrder);

    /**
     * @abstract
     * @param $pPrimaryValues
     *
     */
    abstract public function remove($pPrimaryValues);

    /**
     * @abstract
     * @param $pValues
     * @param $pParentValues
     * @param $pMode
     * @param $pParentObjectKey
     *
     * @return inserted primary key. (last_insert_id() for SQL backend)
     */
    abstract public function add($pValues, $pParentValues = false, $pMode = 'into', $pParentObjectKey = false);

    /**
     * Updates an object
     *
     * @abstract
     * @param $pPrimaryValues
     * @param $pValues
     */
    abstract public function update($pPrimaryValues, $pValues);

    /**
     * @abstract
     * @param bool|string $pCondition
     *
     * @return int
     */
    abstract public function getCount($pCondition = false);


    abstract public function getTree($pCondition = false, $pDepth = 1, $pExtraFields = '');

    /**
     * Returns the parent, if exists
     *
     * @param $pPrimaryValues
     * @return bool
     */
    public function getParent($pPrimaryValues){

        return false;
    }

    /**
     * Returns parent's id, if exists
     *
     * @param $pPrimaryValues
     * @return array
     */
    public function getParentId($pPrimaryValues){
        $object = $this->getParent($pPrimaryValues);

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
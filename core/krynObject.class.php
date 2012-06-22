<?php



class krynObject {

    /**
     * Current object key.
     *
     * @var string
     */
    private $objectKey;

    /**
     * The data of the current object.
     *
     * @var array
     */
    private $object = array();

    /**
     * Id of the current object.
     *
     * @var bool
     */
    private $objectId = false;

    /**
     * Instance of the current class object.
     *
     * @var bool
     */
    private $objectClassInstance;

    /**
     * Contains changed fields, so that we do not save unchanged fields.
     *
     * @var array
     */
    private $changes = array();

    //just for performance
    private $objectFields = array();

    /**
     * asd
     *
     * @param string   $pObjectKey
     * @param mixed
     * @throws Exception
     */
    public function __construct($pObjectKey, $pObjectId = null){

        if (!kryn::$objects[$pObjectKey])
            throw new Exception('Cant find object '.$pObjectKey);

        $this->objectClassInstance =& krynObjects::getClassObject($pObjectKey);
        $this->objectFields = array_keys($this->objectClassInstance->definition['fields']);

        $this->objectKey = $pObjectKey;

        if ($pObjectId){
            $this->load($pObjectId);
        }

    }

    /**
     * Loads given object id for further actions.
     *
     * @param $pObjectId
     * @return krynObject Current instance
     */
    public function load($pObjectId){

        $this->object = krynObjects::get($this->objectKey, $pObjectId);

        $this->objectId = array();
        foreach ($this->objectClassInstance->primaryKeys as $primKey){
            $this->objectId[$primKey] = $this->object[$primKey];
        }

        return $this;
    }

    /**
     * Wrapper for set<FieldName> and get<FieldName>.
     *
     * @internal
     * @param $pName
     * @param $pArguments
     * @return krynObject Current instance
     * @throws Exception
     */
    public function __call($pName, $pArguments){

        $name = strtolower(substr($pName, 3));
        $pr   = substr($pName, 0, 3);

        if (!in_array($name, $this->objectFields)){
            throw new Exception('Field '.$name.' does not exist in object '.$this->objectKey);
        }

        if ($pr == 'set'){
            $this->changes[] = $name;
            $this->object[$name] = $pArguments[0];
            return $this;
        }
        if ($pr == 'get') return $this->object[$name];
    }

    /**
     * Returns the whole defined object as array.
     *
     * @return array
     */
    public function toArray(){
        return $this->object;
    }

    /**
     * Sets the given values as array to the object.
     *
     * @param $pValues
     * @return krynObject Current instance
     */
    public function asArray($pValues){
        foreach ($this->objectFields as $field){
            if ($pValues[$field]){
                $this->object[$field] = $pValues[$field];
                $this->changes[] = $field;
            }
        }
        return $this;
    }

    /**
     * Magic function for $object-><FieldName> assignment.
     *
     * @param $pName
     * @param $pValue
     * @throws Exception
     */
    public function __set($pName, $pValue){

        if (!in_array($pName, $this->objectFields)){
            throw new Exception('Field '.$pName.' does not exist in object '.$this->objectKey);
        }

        if ($this->object[$pName] != $pValue)
            $this->changes[] = $pName;

        $this->object[$pName] = $pValue;
    }

    /**
     * Magic function for $object-><FieldName> read.
     *
     * @param $pName
     * @return mixed
     * @throws Exception
     */
    public function __get($pName){

        if (!in_array($pName, $this->objectFields)){
            throw new Exception('Field '.$pName.' does not exist in object '.$this->objectKey);
        }

        return $this->object[$pName];

    }

    /**
     * Saves current object values to the database.
     *
     * @return boolean
     * @throws Exception
     */
    public function save(){

        if ($this->objectId){

            $changes = array();
            foreach ($this->changes as $field){
                $changes[$field] =& $this->object[$field];
            }

            if (!$changes) return false;

            return krynObjects::update($this->objectKey, $this->objectId, $changes);

        } else {
            throw new Exception('There is no object id. Use load() or add().');
        }

    }

    /**
     * Adds the current object values into database.
     * From here ->save() is possible.
     *
     * @param bool   $pParentId
     * @param string $pPosition Only for objects with nested mode.
     * @param bool   $pParentObjectKey Only for objects with nested mode.
     * @return int Returns last_inserted_id()
     */
    public function add($pParentId = false, $pPosition = 'into', $pParentObjectKey = false){

        $this->objectId = $this->objectClassInstance->add($this->object, $pParentId, $pPosition, $pParentObjectKey);
        return $this->objectId;

    }


}

?>
<?php

class krynObject {


    /**
     * Array of instances of the object classes
     *
     * @var array
     */
    public static $instances = array();

    /**
     * @var array
     */
    public static $cache = array();

    private $objectKey;

    private $object = array();

    private $objectDefinition = array();

    private $objectId = false;

    private $objectClassInstance;

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

    public function test(){

        print 'hi';
    }

    public function load($pObjectId){

        $this->object = krynObjects::get($this->objectKey, $pObjectId);

        $this->objectId = array();
        foreach ($this->objectClassInstance->primaryKeys as $primKey){
            $this->objectId[$primKey] = $this->object[$primKey];
        }

    }

    public function __call($pName, $pArguments){

        $name = strtolower(substr($pName, 3));
        $pr   = substr($pName, 0, 3);

        if (!in_array($name, $this->objectFields)){
            throw new Exception('Field '.$name.' does not exist in object '.$this->objectKey);
        }

        if ($pr == 'set'){
            $this->changes[] = $name;
            return $this->object[$name] = $pArguments[0];
        }
        if ($pr == 'get') return $this->object[$name];

    }

    public function toArray(){
        return $this->object;
    }

    public function asArray($pValues){
        foreach ($this->objectFields as $field){
            if ($pValues[$field]){
                $this->object[$field] = $pValues[$field];
                $this->changes[] = $field;
            }
        }
    }

    public function __set($pName, $pValue){

        if (!in_array($pName, $this->objectFields)){
            throw new Exception('Field '.$pName.' does not exist in object '.$this->objectKey);
        }

        if ($this->object[$pName] != $pValue)
            $this->changes[] = $pName;

        $this->object[$pName] = $pValue;
    }

    public function __get($pName){

        if (!in_array($pName, $this->objectFields)){
            throw new Exception('Field '.$pName.' does not exist in object '.$this->objectKey);
        }

        return $this->object[$pName];

    }

    public function save(){

        if ($this->objectId){

            $changes = array();
            foreach ($this->changes as $field){
                $changes[$field] =& $this->object[$field];
            }

            if (!$changes) return false;

            return krynObjects::update($this->objectKey, $this->objectId, $changes);

        } else {
            return $this->add();
        }

    }

    public function add($pParentId = false, $pPosition = 'into', $pParentObjectKey = false){

        return $this->objectClassInstance->add($this->object, $pParentId, $pPosition, $pParentObjectKey);

    }


}

?>
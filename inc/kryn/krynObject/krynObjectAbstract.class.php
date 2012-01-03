<?php

abstract class krynObjectAbstract {

    public $definition;

    function __construct($pDefinition){
        $this->definition = $pDefinition;
    }

    abstract public function getItem($pId, $pFields = '*');
    abstract public function getItems($pFrom = 0, $pLimit = false, $pCondition = false, $pFields = '*');

    abstract public function removeItem($pId);
    abstract public function addItem($pId);

    abstract public function updateItem($pId);

}

?>
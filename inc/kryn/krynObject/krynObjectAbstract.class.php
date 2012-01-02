<?php

abstract class krynObjectAbstract {

    abstract function __construct($pDefinition);

    abstract public function getItem($pId, $pFields = '*');
    abstract public function getItems($pFrom = 0, $pLimit = false, $pCondition = false, $pFields = '*');

    abstract public function removeItem($pId);
    abstract public function addItem($pId);

    abstract public function updateItem($pId);

}

?>
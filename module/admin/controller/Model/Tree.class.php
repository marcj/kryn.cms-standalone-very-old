<?php


namespace Admin\Model;

abstract class Tree {

    
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


}
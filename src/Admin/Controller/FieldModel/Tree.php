<?php

namespace Admin\Model;

abstract class Tree
{
    /**
     * Returns a branch if the object is a nested set.
     *
     * @param mixed $pParentPrimaryKey
     * @param mixed $pCondition
     * @param int   $pDepth
     * @param int   $pScope
     * @param mixed $pOptions
     *
     * @abstract
     *
     * @return array|bool
     */
    abstract public function getBranch(
        $pParentPrimaryKey = false,
        $pCondition = false,
        $pDepth = 1,
        $pScope = 0,
        $pOptions = false
    );

}

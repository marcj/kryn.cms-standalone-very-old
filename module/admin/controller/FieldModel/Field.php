<?php

namespace Admin\Model;

abstract class Field
{
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
     * @param bool|array $pPrimaryKey
     * @param bool|array $pOptions
     *
     * @return array
     */
    abstract public function getItem($pPrimaryKey, $pOptions = false);

}

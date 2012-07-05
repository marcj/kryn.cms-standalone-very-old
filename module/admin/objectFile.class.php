<?php

class objectFile extends krynObjectAbstract {
    /**
     *
     *
     * @param mixed $pPrimaryValues
     * @param string $pFields
     * @param string $pResolveForeignValues
     *
     * @return array
     */
    public function getItem($pPrimaryValues, $pFields = '*', $pResolveForeignValues = '*')
    {

        if (is_numeric($pPrimaryValues['id'])){

            $path = krynFile::getPath($pPrimaryValues);

            return array(
                'id' => $pPrimaryValues['id'],
                'path' => $path,
                'name' => basename($path)
            );
        } else {
            return array(
                'id' => $pPrimaryValues['id'],
                'path' => $pPrimaryValues['id'],
                'name' => basename($pPrimaryValues['id'])
            );
        }
    }

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
     * @param bool|array   $pCondition
     * @param bool|array        $pOptions
     */
    public function getItems($pCondition, $pOptions = false)
    {
        // TODO: Implement getItems() method.
    }

    /**
     * @param $pPrimaryValues
     *
     */
    public function remove($pPrimaryValues)
    {
        // TODO: Implement remove() method.
    }

    /**
     * @param $pValues
     * @param $pParentValues
     * @param $pMode
     * @param $pParentObjectKey
     *
     * @return inserted primary key. (last_insert_id() for SQL backend)
     */
    public function add($pValues, $pParentValues = false, $pMode = 'into', $pParentObjectKey = false)
    {
        // TODO: Implement add() method.
    }

    /**
     * Updates an object
     *
     * @param $pPrimaryValues
     * @param $pValues
     */
    public function update($pPrimaryValues, $pValues)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param bool|string $pCondition
     *
     * @return int
     */
    public function getCount($pCondition = false)
    {
        // TODO: Implement getCount() method.
    }

    public function getTree($pCondition = false, $pDepth = 1, $pExtraFields = ''){


        if (!$pCondition){
            //root
            return krynFile::getFiles('/');
        } else {

            $path = $pCondition['id'];
            if (is_numeric($path))
                $path = krynFile::getPath($path);

            $file = krynFile::getFile($path);
            $file['_children'] = krynFile::getFiles($path);
            return $file;
        }

    }

}
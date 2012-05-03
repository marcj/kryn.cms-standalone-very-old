<?php

class objectFile extends krynObjectAbstract {
    /**
     *
     *
     * @param mixed $pPrimaryValues
     * @param string $pFields
     * @param string $pResolveForeignValues
     */
    public function getItem($pPrimaryValues, $pFields = '*', $pResolveForeignValues = '*')
    {

        if (is_numeric($pPrimaryValues['id'])){

            $path = krynFile::getPath($pPrimaryValues);

            return array(
                'id' => $pPrimaryValues['id'],
                'path' => $path
            );
        } else {
            return array(
                'id' => $pPrimaryValues['id'],
                'path' => $pPrimaryValues['id']
            );
        }
    }

    /**
     *
     * @param mixed $pPrimaryValues
     * @param int $pOffset
     * @param int $pLimit
     * @param bool $pCondition
     * @param string $pFields
     * @param string $pResolveForeignValues
     * @param $pOrder
     */
    public function getItems($pCondition, $pOffset = 0, $pLimit = 0, $pFields = '*', $pResolveForeignValues = '*',
                             $pOrder)
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
     *
     * @return inserted primary key. (last_insert_id() for SQL backend)
     */
    public function add($pValues)
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

    public function getTree($pCondition = false, $pDepth = 1, $pExtraFields = '')
    {
        // TODO: Implement getTree() method.
    }

}
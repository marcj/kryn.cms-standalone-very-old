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
        // TODO: Implement getItem() method.
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
    public function getItems($pPrimaryValues, $pOffset = 0, $pLimit = 0, $pCondition = false, $pFields = '*',
                             $pResolveForeignValues = '*', $pOrder)
    {
        // TODO: Implement getItems() method.
    }

    /**
     * @param $pPrimaryValues
     *
     */
    public function removeItem($pPrimaryValues)
    {
        // TODO: Implement removeItem() method.
    }

    /**
     * @param $pValues
     *
     * @return inserted primary key. (last_insert_id() for SQL backend)
     */
    public function addItem($pValues)
    {
        // TODO: Implement addItem() method.
    }

    /**
     * Updates an object
     *
     * @param $pPrimaryValues
     * @param $pValues
     */
    public function updateItem($pPrimaryValues, $pValues)
    {
        // TODO: Implement updateItem() method.
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

}
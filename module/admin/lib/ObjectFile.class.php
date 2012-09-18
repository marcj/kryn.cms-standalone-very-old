<?php

namespace Admin;

class ObjectFile extends \Core\ORM\ORMAbstract {


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
     * @param array       $pPrimaryKey
     * @param bool|array  $pOptions
     *
     * @return array
     */
    public function getItem($pPrimaryKey, $pOptions = false){

        $path = $pPrimaryKey['id'];

        if (is_numeric($path)){
            $path = \Core\File::getPath($path);
        }

        $file = \Core\File::getFile($path);

        return is_array($file) ? $file : null;
    }

    /**
     * asd
     *
     * $pOptions is a array which can contain following options. All options are optional.
     *
     *  'fields'          Limit the columns selection. Use a array or a comma separated list (like in SQL SELECT)
     *                    If empty all columns will be selected.
     *  'offset'          Offset of the result set (in SQL OFFSET)
     *  'limit'           Limits the result set (in SQL LIMIT)
     *  'order'           The column to order. Example:
     *                    array(
     *                      array('category' => 'asc'),
     *                      array(title' => 'asc')
     *                    )
     *
     *  'permissionCheck' Defines whether we check against the ACL or not. true or false. default false
     *
     *
     * @param mixed  $pCondition Condition object as it is described in function dbConditionToSql() #Extended.
     * @param mixed  $pOptions
     *
     * @return array d
     */
    public function getItems($pCondition = null, $pOptions = null){
        //todo
        return array(array('id' => 'huri'));
    }

    /**
     *
     * @param array $pPrimaryKey
     *
     */
    public function remove($pPrimaryKey){
        // TODO: Implement remove() method.
    }

    /**
     * @param array  $pValues
     * @param mixed  $pBranchPk If nested set
     * @param string $pMode  If nested set. 'first' (child), 'last' (child), 'prev' (sibling), 'next' (sibling)
     * @param int  $pScope If nested set with scope
     *
     * @return mixed inserted primary key/s. If the object has multiple PKs, it returns a array.
     */
    public function add($pValues, $pBranchPk = false, $pMode = 'into', $pScope = 0){
        // TODO: Implement add() method.
    }

    /**
     * Updates an object
     *
     * @param $pPrimaryKey
     * @param $pValues
     */
    public function update($pPrimaryKey, $pValues){
        // TODO: Implement update() method.
    }

    /**
     * @param bool|string $pCondition
     *
     * @return int
     */
    public function getCount($pCondition = false){
        // TODO: Implement getCount() method.
    }

    /**
     * Returns a branch if the object is a nested set.
     *
     * @param  mixed $pPrimaryKey
     * @param  mixed $pCondition
     * @param  int   $pDepth
     * @param  int   $pScope
     *
     * @return  array|bool
     */
    public function getBranch($pPrimaryKey = false, $pCondition = false, $pDepth = 1, $pScope = 0,
                              $pOptions = false){
        //todo
        return 'hihi';
    }


}
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

        $path = is_numeric($pPrimaryKey['id'])? \Core\File::getPath($pPrimaryKey['id']) : $pPrimaryKey['id'];

        $file = \Core\File::getFile($path);

        return is_array($file) ? $file : null;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($pCondition = null, $pOptions = null){
        throw new \Exception('Only branch listing available.');
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


    public function getTree($pCondition = false, $pDepth = 1, $pScope = 0, $pOptions = false){

        $rootDir = opendir(PATH_MEDIA);

        if (!$rootDir) throw new \FileIOException(tf('Can not open folder %s.', PATH_MEDIA));

        $files = array();
        while ($file = readdir($rootDir)){



        }

        $file = \Core\File::getFile($path);
        return 'hihi';
    }


}
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
     * Converts given primary values from type string into proper array definition.
     * Generates a array for the usage of Core\Object:get()
     *
     * @param string $pPrimaryKey
     *
     * @return array
     */
    public function primaryStringToArray($pPrimaryKey){

        if ($pPrimaryKey === '') return false;
        $groups = explode(',', $pPrimaryKey);

        $result = array();

        foreach ($groups as $group){

            $item = array();
            $primaryGroups = explode('-', $group);

            foreach ($primaryGroups as $pos => $value){

                if ($ePos = strpos($value, '=')){
                    $key = substr($value, 0, $ePos);
                    if (!in_array($key, $this->primaryKeys)) continue;
                } else if (!$this->primaryKeys[$pos]) continue;

                if (is_numeric($value))
                    $item['id'] = $value;
                else
                    $item['path'] = urldecode($value);
            }

            if (count($item) > 0)
                $result[] = $item;
        }

        return $result;

    }

    /**
     * {@inheritDoc}
     */
    public function getItems($pCondition = null, $pOptions = null){
        $query = 'SELECT * FROM '.pfx.'system_files';

        $data = array();
        if ($pCondition){
            $condition = dbConditionToSql($pCondition, $data);
            $query .= ' WHERE ' . $condition;
        }

        $items = dbExfetchAll($query, $data);
        return $items;
    }

    /**
     *
     * @param array $pPrimaryKey
     *
     */
    public function remove($pPrimaryKey){
        $path = is_numeric($pPrimaryKey['id'])? \Core\File::getPath($pPrimaryKey['id']) : $pPrimaryKey['id'];
        return \Core\File::delete($path);
    }

    /**
     * @param array  $pValues
     * @param mixed  $pBranchPk If nested set
     * @param string $pMode     If nested set. 'first' (child), 'last' (child), 'prev' (sibling), 'next' (sibling)
     * @param int    $pScope    If nested set with scope
     *
     * @return mixed inserted primary key/s. If the object has multiple PKs, it returns a array.
     */
    public function add($pValues, $pBranchPk = false, $pMode = 'into', $pScope = 0){
        if ($pBranchPk)
            $parentPath = is_numeric($pBranchPk['id'])? \Core\File::getPath($pBranchPk['id']) : $pBranchPk['id'];

        $path = $parentPath ? $parentPath . $pValues['name'] : $pValues['name'];
        return \Core\File::setContent($path, $pValues['content']);
    }

    /**
     * Updates an object
     *
     * @param $pPrimaryKey
     * @param $pValues
     */
    public function update($pPrimaryKey, $pValues){
        $path = is_numeric($pPrimaryKey['id'])? \Core\File::getPath($pPrimaryKey['id']) : $pPrimaryKey['id'];
        return \Core\File::setContent($path, $pValues['content']);
    }

    /**
     * @param bool|string $pCondition
     *
     * @return int
     */
    public function getCount($pCondition = false){
        // TODO: Implement getCount() method.
    }


    public function getTree($pParentPrimaryKey = null, $pCondition = null, $pDepth = 1, $pScope = 0, $pOptions = null){

        if ($pParentPrimaryKey)
            $path = is_numeric($pParentPrimaryKey['id'])?
                \Core\File::getPath($pParentPrimaryKey['id']) : $pParentPrimaryKey['id'];
        else
            $path = '/';

        $files = \Core\File::getFiles($path);

        return $files;
    }



}
<?php

namespace Admin;

class ObjectEntryPoint extends \Core\ORM\ORMAbstract {

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
     * @param array  $pPrimaryKey
     * @param array  $pOptions
     *
     * @return array
     */
    public function getItem($pPk, $pOptions = null)
    {

        $entryPoint = Utils::getEntryPoint($pPk['path']);
        if ($entryPoint)
            return array('path' => $pPk['path'],
                      'type' => $entryPoint['type'],
                      'title' => $entryPoint['title']? $entryPoint['title'].' ('.$pPk['path'].')' : $pPk['path']);

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
     *                      array('category' => 'asc'),
     *                      array(title' => 'asc')
     *                    )
     *
     *  'permissionCheck' Defines whether we check against the ACL or not. true or false. default false
     *
     *
     * @param array  $pCondition Condition object as it is described in function dbConditionToSql() #Extended.
     * @param array  $pOptions
     */
    public function getItems($pCondition = null, $pOptions = null)
    {
        // TODO: Implement getItems() method.
    }

    /**
     *
     * @param array $pPrimaryKey
     *
     */
    public function remove($pPrimaryKey)
    {
        // TODO: Implement remove() method.
    }

    /**
     * @param array  $pValues
     * @param array  $pBranchPk If nested set
     * @param string $pMode  If nested set. 'first' (child), 'last' (child), 'prev' (sibling), 'next' (sibling)
     * @param int    $pScope If nested set with scope
     *
     * @return array inserted/new primary key/s always as a array.
     */
    public function add($pValues, $pBranchPk = null, $pMode = 'into', $pScope = null)
    {
        // TODO: Implement add() method.
    }

    /**
     * Updates an object
     *
     * @param array $pPrimaryKey
     * @param array $pValues
     * @throws \ObjectItemNotModified
     */
    public function update($pPrimaryKey, $pValues)
    {
        // TODO: Implement update() method.
    }

    /**
     * @param array $pCondition
     *
     * @return int
     */
    public function getCount($pCondition = null)
    {
        // TODO: Implement getCount() method.
    }

    /**
     * Do whatever is needed, to clear all items out of this object scope.
     *
     * @return bool
     */
    public function clear()
    {
        // TODO: Implement clear() method.
    }

    public function getPrimaryKeys()
    {
        return parent::getPrimaryKeys();
    }

    public static function normalizePath(&$pPath){

        $pPath = str_replace('.', '/', $pPath); //debug

        if (substr($pPath, -1) == '/')
            $pPath = substr($pPath, 0, -1);

    }

    public function setChildren($pPath, &$pItem, $pDepth){

        $children = $this->getTree(array('path' => $pPath), null, $pDepth-1);

        if ($children && count($children) > 0){
            if ($pDepth > 1)
                $pItem['_children'] = $children;
            $pItem['_childrenCount'] = count($children);
        } else {
            $pItem['_childrenCount'] = 0;
        }
    }

    public function getTree($pPk = null, $pCondition = null, $pDepth = 1, $pScope = null, $pOptions = null)
    {

        $result = null;

        if (!$pPk || !$pPk['path']){

            $config = \Core\Kryn::getModuleConfig('admin');
            foreach ($config['entryPoints'] as $key => $entryPoint){
                $item = array(
                    'path' => $key,
                    'type' => $entryPoint['type'],
                    'title' => $entryPoint['title']? $entryPoint['title'].' ('.$key.')' : $key,
                );

                $this->setChildren($key, $item, $pDepth);
                $result[] = $item;
            }


            foreach (\Core\Kryn::$extensions as $extension){
                if ($extension == 'admin') continue;
                $config = \Core\Kryn::getModuleConfig($extension);

                foreach ($config['entryPoints'] as $key => $entryPoint){
                    $item = array('path' => $extension.'/'.$key,
                                  'type' => $entryPoint['type'],
                                  'title' => $entryPoint['title']? $entryPoint['title'].' ('.$key.')' : $key);

                    $this->setChildren($extension.'/'.$key, $item, $pDepth);

                    $result[] = $item;
                }
            }

        } else {

            self::normalizePath($pPk['path']);

            $entryPoint = Utils::getEntryPoint($pPk['path'], true);
            if ($entryPoint && $entryPoint['children'] && count($entryPoint['children']) > 0){

                foreach ($entryPoint['children'] as $key => $entryPoint){
                    $item = array('path' => $pPk['path'].'/'.$key,
                                  'type' => $entryPoint['type'],
                                  'title' => $entryPoint['title']? $entryPoint['title'].' ('.$key.')' : $key);

                    $this->setChildren($pPk['path'].'/'.$key, $item, $pDepth);

                    $result[] = $item;
                }

            }

        }

        return $result;
    }

    public function getParent($pPk)
    {
        parent::getParent($pPk);
    }

    public function getParents($pPk)
    {
        parent::getParents($pPk);
    }

    public function getParentId($pPrimaryKey)
    {
        return parent::getParentId($pPrimaryKey);
    }


}
<?php

namespace Admin\Models;

class ObjectEntryPoint extends \Core\ORM\ORMAbstract
{
    /**
     * {@inheritDoc}
     */
    public function getItem($pk, $options = null)
    {

        $entryPoint = Utils::getEntryPoint($pk['path']);
        if ($entryPoint) {
            return array(
                'path' => $pk['path'],
                'type' => $entryPoint['type'],
                'title' => $entryPoint['title'] ? $entryPoint['title'] . ' (' . $pk['path'] . ')' : $pk['path']
            );
        }

    }

    /**
     * {@inheritDoc}
     */
    public function getItems($condition = null, $options = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function remove($primaryKey)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function add($values, $branchPk = null, $mode = 'into', $scope = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function update($primaryKey, $values)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function patch($primaryKey, $values)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getCount($condition = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getPrimaryKeys()
    {
        return parent::getPrimaryKeys();
    }

    /**
     * {@inheritDoc}
     */
    public static function normalizePath(&$path)
    {
        $path = str_replace('.', '/', $path); //debug

        if (substr($path, -1) == '/') {
            $path = substr($path, 0, -1);
        }

    }

    /**
     * Sets the children information at $item directly.
     */
    public function setChildren($path, &$item, $depth)
    {
        $children = $this->getBranch(array('path' => $path), null, $depth - 1);

        if ($children && count($children) > 0) {
            if ($depth > 1) {
                $item['_children'] = $children;
            }
            $item['_childrenCount'] = count($children);
        } else {
            $item['_childrenCount'] = 0;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getBranch($pk = null, $condition = null, $depth = 1, $scope = null, $options = null)
    {

        $result = null;

        if (!$pk || !$pk['path']) {

            $config = \Core\Kryn::getModuleConfig('admin');
            foreach ($config['entryPoints'] as $key => $entryPoint) {
                $item = array(
                    'path' => $key,
                    'type' => $entryPoint['type'],
                    'title' => $entryPoint['title'] ? $entryPoint['title'] . ' (' . $key . ')' : $key,
                );

                $this->setChildren($key, $item, $depth);
                $result[] = $item;
            }

            foreach (\Core\Kryn::$extensions as $extension) {
                if ($extension == 'admin') {
                    continue;
                }
                $config = \Core\Kryn::getModuleConfig($extension);

                foreach ($config['entryPoints'] as $key => $entryPoint) {
                    $item = array(
                        'path' => $extension . '/' . $key,
                        'type' => $entryPoint['type'],
                        'title' => $entryPoint['title'] ? $entryPoint['title'] . ' (' . $key . ')' : $key
                    );

                    $this->setChildren($extension . '/' . $key, $item, $depth);

                    $result[] = $item;
                }
            }

        } else {

            self::normalizePath($pk['path']);

            $entryPoint = Utils::getEntryPoint($pk['path'], true);
            if ($entryPoint && $entryPoint['children'] && count($entryPoint['children']) > 0) {

                foreach ($entryPoint['children'] as $key => $entryPoint) {
                    $item = array(
                        'path' => $pk['path'] . '/' . $key,
                        'type' => $entryPoint['type'],
                        'title' => $entryPoint['title'] ? $entryPoint['title'] . ' (' . $key . ')' : $key
                    );

                    $this->setChildren($pk['path'] . '/' . $key, $item, $depth);

                    $result[] = $item;
                }

            }

        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent($pk)
    {
        parent::getParent($pk);
    }

    /**
     * {@inheritDoc}
     */
    public function getParents($pk)
    {
        parent::getParents($pk);
    }

    /**
     * {@inheritDoc}
     */
    public function getParentId($primaryKey)
    {
        return parent::getParentId($primaryKey);
    }

}

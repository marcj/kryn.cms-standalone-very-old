<?php

namespace Admin\Models;

class ObjectEntryPoint extends \Core\ORM\ORMAbstract
{
    /**
     * {@inheritDoc}
     */
    public function getItem($pPk, $pOptions = null)
    {

        $entryPoint = Utils::getEntryPoint($pPk['path']);
        if ($entryPoint) {
            return array(
                'path' => $pPk['path'],
                'type' => $entryPoint['type'],
                'title' => $entryPoint['title'] ? $entryPoint['title'] . ' (' . $pPk['path'] . ')' : $pPk['path']
            );
        }

    }

    /**
     * {@inheritDoc}
     */
    public function getItems($pCondition = null, $pOptions = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function remove($pPrimaryKey)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function add($pValues, $pBranchPk = null, $pMode = 'into', $pScope = null)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function update($pPrimaryKey, $pValues)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function patch($pPrimaryKey, $pValues)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getCount($pCondition = null)
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
    public static function normalizePath(&$pPath)
    {
        $pPath = str_replace('.', '/', $pPath); //debug

        if (substr($pPath, -1) == '/') {
            $pPath = substr($pPath, 0, -1);
        }

    }

    /**
     * Sets the children information at $pItem directly.
     */
    public function setChildren($pPath, &$pItem, $pDepth)
    {
        $children = $this->getBranch(array('path' => $pPath), null, $pDepth - 1);

        if ($children && count($children) > 0) {
            if ($pDepth > 1) {
                $pItem['_children'] = $children;
            }
            $pItem['_childrenCount'] = count($children);
        } else {
            $pItem['_childrenCount'] = 0;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getBranch($pPk = null, $pCondition = null, $pDepth = 1, $pScope = null, $pOptions = null)
    {

        $result = null;

        if (!$pPk || !$pPk['path']) {

            $config = \Core\Kryn::getModuleConfig('admin');
            foreach ($config['entryPoints'] as $key => $entryPoint) {
                $item = array(
                    'path' => $key,
                    'type' => $entryPoint['type'],
                    'title' => $entryPoint['title'] ? $entryPoint['title'] . ' (' . $key . ')' : $key,
                );

                $this->setChildren($key, $item, $pDepth);
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

                    $this->setChildren($extension . '/' . $key, $item, $pDepth);

                    $result[] = $item;
                }
            }

        } else {

            self::normalizePath($pPk['path']);

            $entryPoint = Utils::getEntryPoint($pPk['path'], true);
            if ($entryPoint && $entryPoint['children'] && count($entryPoint['children']) > 0) {

                foreach ($entryPoint['children'] as $key => $entryPoint) {
                    $item = array(
                        'path' => $pPk['path'] . '/' . $key,
                        'type' => $entryPoint['type'],
                        'title' => $entryPoint['title'] ? $entryPoint['title'] . ' (' . $key . ')' : $key
                    );

                    $this->setChildren($pPk['path'] . '/' . $key, $item, $pDepth);

                    $result[] = $item;
                }

            }

        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent($pPk)
    {
        parent::getParent($pPk);
    }

    /**
     * {@inheritDoc}
     */
    public function getParents($pPk)
    {
        parent::getParents($pPk);
    }

    /**
     * {@inheritDoc}
     */
    public function getParentId($pPrimaryKey)
    {
        return parent::getParentId($pPrimaryKey);
    }

}

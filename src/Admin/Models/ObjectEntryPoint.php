<?php

namespace Admin\Models;

use Admin\Utils;

class ObjectEntryPoint extends \Core\ORM\ORMAbstract
{
    /**
     * {@inheritDoc}
     */
    public function getItem($pk, $options = null)
    {
        if ('/' === $pk['path']) {
            return array(
                'path' => '/',
                'title' => 'Admin Access (/)'
            );
        }
        $entryPoint = Utils::getEntryPoint($pk['path']);
        if ($entryPoint) {
            return array(
                'path' => $pk['path'],
                'type' => $entryPoint['type'],
                'title' => $entryPoint['title'] ? $entryPoint['title'] . ' (/' . $pk['path'] . ')' : '/'.$pk['path']
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

            return [
                [
                    'path' => '/',
                    'title' => 'Admin Access'
                ]
            ];

        } else if ('/' == $pk['path']) {
            foreach (\Core\Kryn::getBundleClasses() as $bundle) {

                $item = array(
                    'path' => strtolower($bundle->getName(true)),
                    'title' => $bundle->getName()
                );

                $this->setChildren(strtolower($bundle->getName(true)), $item, $depth);

                $result[] = $item;
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
        if ('/' === $pk['path']) return null;

        $lastSlash = strrpos($pk['path'], '/');
        return [
            'path' => substr($pk['path'], 0, $lastSlash) ?: '/'
        ];
    }
}

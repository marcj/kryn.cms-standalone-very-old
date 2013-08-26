<?php

namespace Admin\Models;

use Core\Kryn;
use Core\SystemFile;

class ObjectView extends \Core\ORM\ORMAbstract
{
    /**
     * {@inheritDoc}
     */
    public function getItem($pk, $options = null)
    {
        $path = $pk['path'];

        $file = Kryn::resolvePath($path, 'Views/');
        $fileObj = SystemFile::getFile($file);

        return $fileObj->toArray();
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
     * {@inheritDoc}
     */
    public function getBranch($pk = null, $condition = null, $depth = 1, $scope = null, $options = null)
    {
        $result = null;

        $path = $pk['path'];
        if ($depth === null) {
            $depth = 1;
        }

        if (substr($path, -1) !== '/') {
            $path .= '/';
        }

        $c = 0;
        $offset = $options['offset'];
        $limit = $options['limit'];
        $result = array();

        if (!$path) {

            $result = array();
            foreach (\Core\Kryn::$extensions as $extension) {
                $directory = Kryn::resolvePath('@' . $extension, 'Views/');
                $file = SystemFile::getFile($directory);
                if (!$file) {
                    continue;
                }
                $file['name'] = $extension;
                $file['path'] = '@' . $extension;
                if ($offset && $offset > $c) {
                    continue;
                }
                if ($limit && $limit < $c) {
                    continue;
                }
                if ($condition && !\Core\Object::satisfy($file, $condition)) {
                    continue;
                }
                $c++;

                if ($depth > 0) {
                    $children = self::getBranch(array('path' => $extension), $condition, $depth - 1);
                    $file['_childrenCount'] = count($children);
                    if ($depth > 1 && $file['type'] == 'dir') {
                        $file['_children'] = $children;
                    }
                }
            }
        } else {
            $directory = Kryn::resolvePath($path, 'Views/');
            $files = SystemFile::getFiles($directory);

            foreach ($files as $file) {
                if ($condition && !\Core\Object::satisfy($file, $condition)) {
                    continue;
                }

                $c++;
                if ($offset && $offset >= $c) {
                    continue;
                }
                if ($limit && $limit < $c) {
                    continue;
                }

                $item = $file->toArray();

                $item = array(
                    'name' => $item['name'],
                    'path' => $path . substr($item['path'], strlen($directory))
                );

                if ($file->isDir()) {
                    $children = self::getBranch(array('path' => $item['path']), $condition, $depth - 1);
                    foreach ($children as $child) {
                        $child['name'] = $item['name'] . '/' . $child['name'];
                        $result[] = $child;
                    }
                }

                if ($file->isFile()) {
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

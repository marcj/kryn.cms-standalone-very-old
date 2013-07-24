<?php

namespace Admin\Models;

use Core\Kryn;
use Core\SystemFile;

class ObjectView extends \Core\ORM\ORMAbstract
{
    /**
     * {@inheritDoc}
     */
    public function getItem($pPk, $pOptions = null)
    {
        $path = $pPk['path'];

        $file = Kryn::resolvePath($path, 'Views/');
        $fileObj = SystemFile::getFile($file);

        return $fileObj;
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
     * {@inheritDoc}
     */
    public function getBranch($pPk = null, $pCondition = null, $pDepth = 1, $pScope = null, $pOptions = null)
    {
        $result = null;

        $path = $pPk['path'];
        if ($pDepth === null) {
            $pDepth = 1;
        }

        if (substr($path, -1) !== '/') {
            $path .= '/';
        }

        $c = 0;
        $offset = $pOptions['offset'];
        $limit = $pOptions['limit'];
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
                if ($pCondition && !\Core\Object::satisfy($file, $pCondition)) {
                    continue;
                }
                $c++;

                if ($pDepth > 0) {
                    $children = self::getBranch(array('path' => $extension), $pCondition, $pDepth - 1);
                    $file['_childrenCount'] = count($children);
                    if ($pDepth > 1 && $file['type'] == 'dir') {
                        $file['_children'] = $children;
                    }
                }
            }
        } else {
            $directory = Kryn::resolvePath($path, 'Views/');
            $files = SystemFile::getFiles($directory);

            foreach ($files as $file) {
                if ($pCondition && !\Core\Object::satisfy($file, $pCondition)) {
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
                    $children = self::getBranch(array('path' => $item['path']), $pCondition, $pDepth - 1);
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

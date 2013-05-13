<?php

namespace Admin\Models;

use Core\Kryn;
use Core\WebFile;

class ObjectFile extends \Core\ORM\Propel
{
    /**
     * {@inheritDoc}
     *
     * Same as parent method, except:
     * If we get the PK as path we convert it to internal ID.
     */
    public function primaryStringToArray($pPrimaryKey)
    {
        if ($pPrimaryKey === '') {
            return false;
        }
        $groups = explode(',', $pPrimaryKey);

        $result = array();

        foreach ($groups as $group) {

            $item = array();
            $primaryGroups = explode(',', $group);

            foreach ($primaryGroups as $pos => $value) {

                if ($ePos = strpos($value, '=')) {
                    $key = substr($value, 0, $ePos);
                    $value = substr($value, $ePos + 1);
                    if (!in_array($key, $this->primaryKeys)) {
                        continue;
                    }
                } elseif (!$this->primaryKeys[$pos]) {
                    continue;
                }

                if (!is_numeric($value)) {
                    $file = WebFile::getFile(Kryn::urlDecode($value));
                    if ($file) {
                        $value = $file['id'];
                    } else {
                        continue;
                    }
                }

                $item['id'] = $value;

            }

            if (count($item) > 0) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * We accept as primary key the path as well, so we have to convert it to internal ID.
     *
     * @param $pPrimaryKey
     */
    public function mapPrimaryKey(&$pPrimaryKey)
    {
        if (!is_numeric($pPrimaryKey['id'])) {
            $file = WebFile::getFile(urldecode($pPrimaryKey['id']));
            $pPrimaryKey['id'] = $file['id'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove($pPrimaryKey)
    {
        $this->mapPrimaryKey($pPrimaryKey);

        parent::remove($pPrimaryKey);

        $path = WebFile::getPath($pPrimaryKey['id']);

        return WebFile::remove($path);
    }

    /**
     * {@inheritDoc}
     */
    public function add($pValues, $pBranchPk = false, $pMode = 'into', $pScope = 0)
    {
        if ($pBranchPk) {
            $parentPath = is_numeric($pBranchPk['id']) ? WebFile::getPath($pBranchPk['id']) : $pBranchPk['id'];
        }

        $path = $parentPath ? $parentPath . $pValues['name'] : $pValues['name'];

        WebFile::setContent($path, $pValues['content']);

        return parent::add($pValues, $pBranchPk, $pMode, $pScope);
    }

    /**
     * {@inheritDoc}
     */
    public function update($pPrimaryKey, $pValues)
    {
        $this->mapPrimaryKey($pPrimaryKey);

        $path = is_numeric($pPrimaryKey['id']) ? WebFile::getPath($pPrimaryKey['id']) : $pPrimaryKey['id'];
        WebFile::setContent($path, $pValues['content']);

        return parent::update($pPrimaryKey, $pValues);
    }

    /**
     * {@inheritDoc}
     */
    public function getItem($pPrimaryKey, $pOptions = null)
    {
        if (is_array($pPrimaryKey)) {
            $path = is_numeric($pPrimaryKey['id']) ? WebFile::getPath($pPrimaryKey['id']) : $pPrimaryKey['id'];
        } else {
            $path = $pPrimaryKey ? : '/';
        }

        if (!$path) {
            return;
        }
        return WebFile::getFile($path);
    }

    public function getParents($pPk, $pOptions = null)
    {
        $path = is_numeric($pPk['id']) ? WebFile::getPath($pPk['id']) : $pPk['id'];

        if ('/' === $path) {
            return array();
        }

        $result = array();

        $part = $path;
        while ($part = substr($part, 0, strrpos($part, '/'))) {
            $item = $this->getItem($part);
            $result[] = $item;
        }

        $root = $this->getItem('/');
        $root['_object'] = $this->objectKey;
        $result[] = $root;

        return array_reverse($result);
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($pCondition = null, $pOptions = null)
    {
        throw new \Exception('getItems not available for this object.');
    }

    /**
     * {@inheritDoc}
     */
    public function getBranch($pPk = null, $pCondition = null, $pDepth = 1, $pScope = null, $pOptions = null)
    {
        if ($pPk) {
            $path = is_numeric($pPk['id']) ? WebFile::getPath($pPk['id']) : $pPk['id'];
        } else {
            $path = '/';
        }

        if ($pDepth === null) {
            $pDepth = 1;
        }

        $files = WebFile::getFiles($path);

        $c = 0;
        $offset = $pOptions['offset'];
        $limit = $pOptions['limit'];
        $result = array();

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

            if ($pDepth > 0) {
                $children = array();
                if ($file['type'] == 'dir') {
                    $children = self::getBranch(array('id' => $file['path']), $pCondition, $pDepth - 1);
                }
                $file['_childrenCount'] = count($children);
                if ($pDepth > 1 && $file['type'] == 'dir') {
                    $file['_children'] = $children;
                }
            }
            $result[] = $file;
        }

        return $result;
    }

}

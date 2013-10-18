<?php

namespace Admin\Models;

use Core\Config\Condition;
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
    public function primaryStringToArray($primaryKey)
    {
        if ($primaryKey === '') {
            return false;
        }
        $groups = explode('/', $primaryKey);

        $result = array();

        foreach ($groups as $group) {

            $item = array();
            if ('' === $group) continue;
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
     * @param $primaryKey
     */
    public function mapPrimaryKey(&$primaryKey)
    {
        if (!is_numeric($primaryKey['id'])) {
            $file = WebFile::getFile(urldecode($primaryKey['id']));
            $primaryKey['id'] = $file['id'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove($primaryKey)
    {
        $this->mapPrimaryKey($primaryKey);

        parent::remove($primaryKey);

        $path = WebFile::getPath($primaryKey['id']);

        return WebFile::remove($path);
    }

    /**
     * {@inheritDoc}
     */
    public function add($values, $branchPk = false, $mode = 'into', $scope = 0)
    {
        if ($branchPk) {
            $parentPath = is_numeric($branchPk['id']) ? WebFile::getPath($branchPk['id']) : $branchPk['id'];
        }

        $path = $parentPath ? $parentPath . $values['name'] : $values['name'];

        WebFile::setContent($path, $values['content']);

        return parent::add($values, $branchPk, $mode, $scope);
    }

    /**
     * {@inheritDoc}
     */
    public function update($primaryKey, $values)
    {
        $this->mapPrimaryKey($primaryKey);

        $path = is_numeric($primaryKey['id']) ? WebFile::getPath($primaryKey['id']) : $primaryKey['id'];
        WebFile::setContent($path, $values['content']);

        return parent::update($primaryKey, $values);
    }

    /**
     * {@inheritDoc}
     */
    public function getItem($primaryKey, $options = null)
    {
        if (is_array($primaryKey)) {
            $path = is_numeric($primaryKey['id']) ? WebFile::getPath($primaryKey['id']) : $primaryKey['id'];
        } else {
            $path = $primaryKey ? : '/';
        }

        if (!$path) {
            return;
        }
        $item = WebFile::getFile($path);
        if ($item) {
            return $item->toArray();
        }
    }

    public function getParents($pk, $options = null)
    {
        $path = is_numeric($pk['id']) ? WebFile::getPath($pk['id']) : $pk['id'];

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
    public function getItems(\Core\Config\Condition $condition = null, $options = null)
    {
        throw new \Exception('getItems not available for this object.');
    }

    public function getParentId($pk)
    {
        if ($pk) {
            $path = is_numeric($pk['id']) ? WebFile::getPath($pk['id']) : $pk['id'];
        } else {
            $path = '/';
        }

        if ('/' === $path) return null;

        $lastSlash = strrpos($path, '/');
        $parentPath = substr($path, 0, $lastSlash) ?: '/';
        $file = WebFile::getFile($parentPath);

        return [
            'id' => $file->getId()
        ];
    }


    /**
     * {@inheritDoc}
     */
    public function getBranch($pk = null, Condition $condition = null, $depth = 1, $scope = null, $options = null)
    {
        if ($pk) {
            $path = is_numeric($pk['id']) ? WebFile::getPath($pk['id']) : $pk['id'];
        } else {
            $path = '/';
        }

        if ($depth === null) {
            $depth = 1;
        }

        $files = WebFile::getFiles($path);

        $c = 0;
        $offset = $options['offset'];
        $limit = $options['limit'];
        $result = array();

        foreach ($files as $file) {
            $file = $file->toArray();
            if ($condition && $condition->hasRules() && !$condition->satisfy($file, 'core:file')) {
                continue;
            }

            $c++;
            if ($offset && $offset >= $c) {
                continue;
            }
            if ($limit && $limit < $c) {
                continue;
            }

            if ($depth > 0) {
                $children = array();
                if ($file['type'] == 'dir') {
                    $children = self::getBranch(array('id' => $file['path']), $condition, $depth - 1);
                }
                $file['_childrenCount'] = count($children);
                if ($depth > 1 && $file['type'] == 'dir') {
                    $file['_children'] = $children;
                }
            }
            $result[] = $file;
        }

        return $result;
    }
}

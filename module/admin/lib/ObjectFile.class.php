<?php

namespace Admin;

use Core\MediaFile;

class ObjectFile extends \Core\ORM\Propel {

    /**
     * {@inheritDoc}
     *
     * Same as parent method, except:
     * If we get the PK as path we convert it to internal ID.
     */
    public function primaryStringToArray($pPrimaryKey){

        if ($pPrimaryKey === '') return false;
        $groups = explode(',', $pPrimaryKey);

        $result = array();


        foreach ($groups as $group){

            $item = array();
            $primaryGroups = explode(',', $group);

            foreach ($primaryGroups as $pos => $value){

                if ($ePos = strpos($value, '=')){
                    $key = substr($value, 0, $ePos);
                    $value = substr($value, $ePos+1);
                    if (!in_array($key, $this->primaryKeys)) continue;
                } else if (!$this->primaryKeys[$pos]) continue;

                if (!is_numeric($value)){
                    $file = MediaFile::getFile(rawurldecode($value));
                    if ($file)
                        $value = $file['id'];
                    else continue;
                }

                $item['id'] = $value;

            }

            if (count($item) > 0)
                $result[] = $item;
        }

        return $result;

    }

    /**
     * We accept as primary key the path as well, so we have to convert it to internal ID.
     *
     * @param $pPrimaryKey
     */
    public function mapPrimaryKey(&$pPrimaryKey){
        if (!is_numeric($pPrimaryKey['id'])){
            $file = MediaFile::getFile(urldecode($pPrimaryKey['id']));
            $pPrimaryKey['id'] = $file['id'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove($pPrimaryKey){

        $this->mapPrimaryKey($pPrimaryKey);

        parent::remove($pPrimaryKey);

        $path = MediaFile::getPath($pPrimaryKey['id']);
        return MediaFile::remove($path);
    }

    /**
     * {@inheritDoc}
     */
    public function add($pValues, $pBranchPk = false, $pMode = 'into', $pScope = 0){

        if ($pBranchPk)
            $parentPath = is_numeric($pBranchPk['id'])? MediaFile::getPath($pBranchPk['id']) : $pBranchPk['id'];

        $path = $parentPath ? $parentPath . $pValues['name'] : $pValues['name'];

        MediaFile::setContent($path, $pValues['content']);
        return parent::add($pValues, $pBranchPk, $pMode, $pScope);
    }

    /**
     * {@inheritDoc}
     */
    public function update($pPrimaryKey, $pValues){

        $this->mapPrimaryKey($pPrimaryKey);

        $path = is_numeric($pPrimaryKey['id'])? MediaFile::getPath($pPrimaryKey['id']) : $pPrimaryKey['id'];
        MediaFile::setContent($path, $pValues['content']);

        return parent::update($pPrimaryKey, $pValues);
    }

    /**
     * {@inheritDoc}
     */
    public function getItem($pPrimaryKey, $pOptions = null){

        if ($pPrimaryKey)
            $path = is_numeric($pPrimaryKey['id'])? MediaFile::getPath($pPrimaryKey['id']) : $pPrimaryKey['id'];
        else
            $path = '/';

        if (!$path) return;

        return MediaFile::getFile($path);
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($pCondition = null, $pOptions = null){

        $items = parent::getItems($pCondition, $pOptions);

        $result = array();
        foreach ($items as $item){
            $file = MediaFile::getFile($item['path']);
            if ($file)
                $result[] = $file;
        }
        return $result;
    }


    /**
     * {@inheritDoc}
     */
    public function getBranch($pPk = null, $pCondition = null, $pDepth = 1, $pScope = null, $pOptions = null){

        if ($pPk)
            $path = is_numeric($pPk['id'])?
                MediaFile::getPath($pPk['id']) : $pPk['id'];
        else
            $path = '/';

        $files = MediaFile::getFiles($path);

        $c = 0;
        $offset = $pOptions['offset'];
        $limit = $pOptions['limit'];
        $result = array();

        foreach($files as $file){
            if ($pCondition && !\Core\Object::satisfy($file, $pCondition)) continue;

            $c++;
            if ($offset && $offset >= $c) continue;
            if ($limit && $limit < $c) continue;

            if ($pDepth > 1 && $file['type'] == 'dir'){
                $children = self::getBranch(array('id' => $file['path']), $pCondition, $pDepth-1);
                $file['_childrenCount'] = count($children);
                $file['_children'] = $children;
            } else {
                $file['_childrenCount'] = 0;
            }
            $result[] = $file;
        }

        return $result;
    }



}
<?php

namespace Admin;

use Core\SystemFile;

class ObjectView extends \Core\ORM\ORMAbstract {


    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getItems($pCondition = null, $pOptions = null)
    {
        // TODO: Implement getItems() method.
    }


    /**
     * {@inheritDoc}
     */
    public function remove($pPrimaryKey)
    {
        // TODO: Implement remove() method.
    }


    /**
     * {@inheritDoc}
     */
    public function add($pValues, $pBranchPk = null, $pMode = 'into', $pScope = null)
    {
        // TODO: Implement add() method.
    }


    /**
     * {@inheritDoc}
     */
    public function update($pPrimaryKey, $pValues)
    {
        // TODO: Implement update() method.
    }


    /**
     * {@inheritDoc}
     */
    public function getCount($pCondition = null)
    {
        // TODO: Implement getCount() method.
    }


    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        // TODO: Implement clear() method.
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
    public static function normalizePath(&$pPath){

        $pPath = str_replace('.', '/', $pPath); //debug

        if (substr($pPath, -1) == '/')
            $pPath = substr($pPath, 0, -1);

    }

    /**
     * {@inheritDoc}
     */
    public function getBranch($pPk = null, $pCondition = null, $pDepth = 1, $pScope = null, $pOptions = null)
    {

        $result = null;

        $path    = $pPk['path'];

        $module  = $path;
        $subPath = '';
        if ( ($pos = strpos($path, '/')) !== false){
            $module = substr($path, 0, $pos);
            $subPath = substr($path, $pos+1);
        }

        $c = 0;
        $offset = $pOptions['offset'];
        $limit = $pOptions['limit'];
        $result = array();

        if (!$path){

            $result = array();
            foreach (\Core\Kryn::$extensions as $extension){
                $c++;
                $directory = '/module/'.$extension.'/view/';
                $file = SystemFile::getFile($directory);
                if ($offset && $offset > $c) continue;
                if ($pCondition && !\Core\Object::satisfy($file, $pCondition)) continue;
            }

        } else {

            $directory = '/module/'.$module.'/view/'.$subPath;
            $files     = SystemFile::getFiles($directory);

            foreach($files as $file){
                if ($pCondition && !\Core\Object::satisfy($file, $pCondition)) continue;

                $c++;
                if ($offset && $offset >= $c) continue;
                if ($limit && $limit < $c) continue;

                $fPath = $module.'/'.substr($file['path'], strlen('/module/'.$module.'/view/'));

                if ($pDepth > 1 && $file['type'] == 'dir'){
                    $children = self::getBranch(array('path' => $fPath), $pCondition, $pDepth-1);
                    $file['_childrenCount'] = count($children);
                    $file['_children'] = $children;
                } else {
                    $file['_childrenCount'] = 0;
                }
                $result[] = $file;
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
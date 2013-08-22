<?php

namespace Admin\Controller;

use Core\Config\EntryPoint;
use RestService\Server;

/**
 * RestController for the entry points which are from type store or framework window.
 *
 */
class ObjectCrudController extends Server
{
    /**
     * @var EntryPoint
     */
    public $entryPoint;

    public function exceptionHandler($exception)
    {
        if (get_class($exception) != 'AccessDeniedException') {
            \Core\Utils::exceptionHandler($exception);
        }
    }

    public function setEntryPoint(EntryPoint $entryPoint)
    {
        $this->entryPoint = $entryPoint;
    }

    public function run()
    {
        if ($this->entryPoint && $this->entryPoint->getType() == 'store') {

            if (!$this->entryPoint->getClass()) {
                $obj = new adminStore();
            } else {
                $clazz = $this->entryPoint->getClass();
                $obj = new $clazz();
            }

            try {
                $this->send($obj->handle($this->entryPoint));
            } catch (Exception $e) {
                $this->sendError(
                    'AdminStoreException',
                    array('exception' => $e->getMessage(), 'entryPoint' => $this->entryPoint->toArray())
                );
            }
        } else {

            $this
                ->getClient()->setUrl(substr(\Core\Kryn::getRequest()->getPathInfo(), 1))->getController()
                ->addGetRoute(':branch', 'getRootBranchItems')
                ->addGetRoute(':count', 'getCount')
                ->addGetRoute(':roots', 'getRoots')
                ->addGetRoute(':root', 'getRoot')

                ->addGetRoute('', 'getItems')
                ->addGetRoute('([^/]+)', 'getItem')

                ->addGetRoute('([^/]+)/branch', 'getBranchItems')
                ->addGetRoute('([^/]+)/parent', 'getParent')
                ->addGetRoute('([^/]+)/version/([0-9]*)', 'getVersion')
                ->addGetRoute('([^/]+)/versions', 'getVersions')
                ->addPutRoute('([^/]+)/move/([^/]+)', 'moveItem')

                ->addGetRoute('([^/]+)/parents', 'getParents')
                ->addGetRoute('([^/]+)/children-count', 'getBranchChildrenCount')
                ->addGetRoute(':children-count', 'getBranchChildrenCount')

                ->addPostRoute('', 'addItem')
                ->addPostRoute(':multiple', 'addMultipleItem')
                ->addPutRoute('([^/]+)', 'updateItem')
                ->addPatchRoute('([^/]+)', 'patchItem')
                ->addDeleteRoute('([^/]+)', 'removeItem')
                ->addOptionsRoute('', 'getInfo');

            //run parent
            parent::run();
        }
    }

    public function getVersion($pPk, $pId)
    {
        //todo
    }

    public function getVersions($pPk)
    {
        //todo
    }

    /**
     * Count
     *
     * @return integer
     */
    public function getCount()
    {
        $obj = $this->getObj();

        return $obj->getCount();
    }

    public function moveItem($pPk, $pTargetPk, $pPosition = 'first', $pTargetObjectKey = '')
    {
        $obj = $this->getObj();

        return $obj->moveItem($pPk, $pTargetPk, $pPosition, $pTargetObjectKey);

    }

    public function getRoots()
    {
        $obj = $this->getObj();

        return $obj->getRoots();
    }

    public function getRoot($pScope = null)
    {
        $obj = $this->getObj();

        return $obj->getRoot($pScope);
    }

    public function getParent($pPk)
    {
        $obj = $this->getObj();

        return $obj->getParent($pPk);
    }

    public function getParents($pPk)
    {
        $obj = $this->getObj();

        return $obj->getParents($pPk);
    }

    /**
     * Translate the label/title item of $fields.
     *
     * @param $pFields
     */
    public static function translateFields(&$pFields)
    {
        if (is_array($pFields)) {
            foreach ($pFields as &$field) {
                self::translateFields($field);
            }
        } elseif (is_string($pFields) && substr($pFields, 0, 2) == '[[' && substr($pFields, -2) == ']]') {
            $pFields = t(substr($pFields, 2, -2));
        }

    }

    /**
     * Proxy method for REST DELETE to remove().
     *
     * @param  string $pObject
     *
     * @return mixed
     */
    public function removeItem($pObject = null)
    {
        $obj = $this->getObj();
        $pk = \Core\Object::parsePk($obj->getObject(), $pObject);

        return $obj->remove($pk[0]);
    }

    /**
     * Proxy method for REST PUT to update().
     *
     * @param  null  $pObject
     *
     * @return mixed
     */
    public function updateItem($pObject = null)
    {
        $obj = $this->getObj();

        $pk = \Core\Object::parsePk($obj->getObject(), $pObject);

        return $obj->update($pk[0]);
    }

    /**
     * Proxy method for REST PATCH to patch().
     *
     * @param  null  $pObject
     *
     * @return mixed
     */
    public function patchItem($pObject = null)
    {
        $obj = $this->getObj();

        $pk = \Core\Object::parsePk($obj->getObject(), $pObject);

        return $obj->patch($pk[0]);
    }

    /**
     * Proxy method for REST POST to add().
     *
     * @return mixed
     */
    public function addItem()
    {
        $obj = $this->getObj();

        return $obj->add();
    }

    /**
     * Proxy method for REST POST to add().
     *
     * @return mixed
     */
    public function addMultipleItem()
    {
        $obj = $this->getObj();

        return $obj->addMultiple();
    }

    /**
     * Proxy method for REST GET to getItem/getItems/getPosition.
     *
     * @param  string $pUrl
     * @param  array  $_
     * @param  int    $pLimit
     * @param  int    $pOffset
     * @param  array  $pFields
     * @param  int    $pGetPosition
     *
     * @return mixed
     */
    public function getItems($pUrl = null, $_ = null, $pLimit = null, $pOffset = null, $pFields = null,
                             $pGetPosition = null, $q = '')
    {
        $obj = $this->getObj();

        if ($pGetPosition !== null) {
            return $obj->getPosition($pGetPosition);
        }

        if ($pUrl !== null) {
            $pk = \Core\Object::parsePk($obj->getObject(), $pUrl);

            return $obj->getItem($pk[0], $pFields);
        } else {
            return $obj->getItems($_, $pLimit, $pOffset, $q, $pFields);
        }

    }

    public function getRootBranchItems(
        $pScope = null,
        $pFields = null,
        $pDepth = 1,
        $pLimit = null,
        $pOffset = null,
        $_ = null
    ) {
        $obj = $this->getObj();

        return $obj->getBranchItems(null, $_, $pFields, $pScope, $pDepth, $pLimit, $pOffset);
    }

    public function getBranchItems(
        $pPk = null,
        $pFields = null,
        $pScope = null,
        $pDepth = 1,
        $pLimit = null,
        $pOffset = null,
        $_ = null
    ) {
        $obj = $this->getObj();

        $pk = \Core\Object::normalizePkString($obj->getObject(), $pPk);

        return $obj->getBranchItems($pk, $_, $pFields, $pScope, $pDepth, $pLimit, $pOffset);
    }

    public function getBranchChildrenCount($pPk = null, $pScope = null, $_ = null)
    {
        $obj = $this->getObj();

        if ($pPk) {
            $pPk = \Core\Object::normalizePkString($obj->getObject(), $pPk);
        }

        return $obj->getBranchChildrenCount($pPk, $pScope, $_);

    }

    public function getItem($pPk, $pFields = null)
    {
        $obj = $this->getObj();

        $primaryKeys = \Core\Object::parsePk($obj->getObject(), $pPk);

        if (count($primaryKeys) == 1) {
            return $obj->getItem($primaryKeys[0], $pFields);
        } else {
            foreach ($primaryKeys as $primaryKey) {
                if ($item = $obj->getItem($primaryKey, $pFields)) {
                    $items[] = $item;
                }
            }

            return $items;
        }
    }

    /**
     * Returns the class definition/properties of the class behind this REST endpoint.
     *
     * @return mixed
     */
    public function getInfo()
    {
        $obj = $this->getObj();
        $info = $obj->getInfo();
        $info['_isClassDefinition'] = true;

        return $info;
    }

    /**
     * Returns the class object, depended on the current entryPoint.
     *
     * @return \Admin\ObjectCrud
     * @throws \Exception
     */
    public function getObj()
    {
        if ($this->obj) {
            return $this->obj;
        }

        $class = $this->entryPoint->getClass();

        if (class_exists($class)) {
            $obj = new $class($this->entryPoint);
            $obj->initialize();
        } else {
            throw new \Exception(tf('Class %s not found', $class));
        }

        return $obj;

    }

    /**
     * @param \Admin\ObjectCrud $pObj
     */
    public function setObj($pObj)
    {
        $this->obj = $pObj;
    }

}

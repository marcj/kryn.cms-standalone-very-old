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
                ->addPutRoute(':move', 'moveItem')

                ->addGetRoute('', 'getItems')
                ->addGetRoute('([^/]+)', 'getItem')

                ->addGetRoute('([^/]+)/branch', 'getBranchItems')
                ->addGetRoute('([^/]+)/parent', 'getParent')
                ->addGetRoute('([^/]+)/version/([0-9]*)', 'getVersion')
                ->addGetRoute('([^/]+)/versions', 'getVersions')

                ->addGetRoute('([^/]+)/parents', 'getParents')
                ->addGetRoute('([^/]+)/children-count', 'getBranchChildrenCount')
                ->addGetRoute(':children-count', 'getBranchChildrenCount')

                ->addPostRoute('', 'addItem')
                ->addPostRoute(':multiple', 'addMultipleItem')
                ->addPutRoute('([^/]+)', 'updateItem')
                ->addPatchRoute('([^/]+)', 'patchItem')
                ->addDeleteRoute('([^/]+)', 'removeItem')
                ->addDeleteRoute('', 'removeItem')
                ->addOptionsRoute('', 'getInfo');

            //run parent
            parent::run();
        }
    }

    public function getVersion($pk, $id)
    {
        //todo
    }

    public function getVersions($pk)
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

    public function moveItem($source, $target, $position = 'first', $targetObjectKey = '', $overwrite = false)
    {
        $obj = $this->getObj();

        return $obj->moveItem($source, $target, $position, $targetObjectKey, filter_var($overwrite, FILTER_VALIDATE_BOOLEAN));

    }

    public function getRoots()
    {
        $obj = $this->getObj();

        return $obj->getRoots();
    }

    public function getRoot($scope = null)
    {
        $obj = $this->getObj();

        return $obj->getRoot($scope);
    }

    public function getParent($pk)
    {
        $obj = $this->getObj();

        return $obj->getParent($pk);
    }

    public function getParents($pk)
    {
        $obj = $this->getObj();

        return $obj->getParents($pk);
    }

    /**
     * Translate the label/title item of $fields.
     *
     * @param $fields
     */
    public static function translateFields(&$fields)
    {
        if (is_array($fields)) {
            foreach ($fields as &$field) {
                self::translateFields($field);
            }
        } elseif (is_string($fields) && substr($fields, 0, 2) == '[[' && substr($fields, -2) == ']]') {
            $fields = t(substr($fields, 2, -2));
        }

    }

    /**
     * Proxy method for REST DELETE to remove().
     *
     * @param  string|array $pk
     *
     * @return mixed
     */
    public function removeItem($pk)
    {
        if (is_array($pk)) {
            $result = false;
            foreach ($pk as $item) {
                $result |= $this->removeItem($item);
            }
            return (boolean)$result;
        }

        $obj = $this->getObj();

        return $obj->remove($pk);
    }

    /**
     * Proxy method for REST PUT to update().
     *
     * @param  null  $object
     *
     * @return mixed
     */
    public function updateItem($object = null)
    {
        $obj = $this->getObj();

        $pk = \Core\Object::parsePk($obj->getObject(), $object);

        return $obj->update($pk[0]);
    }

    /**
     * Proxy method for REST PATCH to patch().
     *
     * @param  null  $object
     *
     * @return mixed
     */
    public function patchItem($object = null)
    {
        $obj = $this->getObj();

        $pk = \Core\Object::parsePk($obj->getObject(), $object);

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
     * @param  string $url
     * @param  array  $_
     * @param  int    $limit
     * @param  int    $offset
     * @param  array  $fields
     * @param  int    $getPosition
     *
     * @return mixed
     */
    public function getItems($url = null, $_ = null, $limit = null, $offset = null, $fields = null,
                             $getPosition = null, $orderBy = [], $q = '', $withAcl = false)
    {
        $obj = $this->getObj();

        if ($getPosition !== null) {
            return $obj->getPosition($getPosition);
        }

        if ($url !== null) {
            $pk = \Core\Object::parsePk($obj->getObject(), $url);
            return $obj->getItem($pk[0], $fields, filter_var($withAcl, FILTER_VALIDATE_BOOLEAN));
        } else {
            return $obj->getItems($_, $limit, $offset, $q, $fields, $orderBy);
        }

    }

    public function getRootBranchItems(
        $scope = null,
        $fields = null,
        $depth = 1,
        $limit = null,
        $offset = null,
        $_ = null
    ) {
        $obj = $this->getObj();

        return $obj->getBranchItems(null, $_, $fields, $scope, $depth, $limit, $offset);
    }

    public function getBranchItems(
        $pk = null,
        $fields = null,
        $scope = null,
        $depth = 1,
        $limit = null,
        $offset = null,
        $_ = null
    ) {
        $obj = $this->getObj();

        $pk2 = \Core\Object::normalizePkString($obj->getObject(), $pk);

        return $obj->getBranchItems($pk2, $_, $fields, $scope, $depth, $limit, $offset);
    }

    public function getBranchChildrenCount($pk = null, $scope = null, $_ = null)
    {
        $obj = $this->getObj();

        if ($pk) {
            $pk = \Core\Object::normalizePkString($obj->getObject(), $pk);
        }

        return $obj->getBranchChildrenCount($pk, $scope, $_);

    }

    public function getItem($pk, $fields = null, $withAcl = false)
    {
        $obj = $this->getObj();

        $primaryKeys = \Core\Object::parsePk($obj->getObject(), $pk);

        $withAcl = filter_var($withAcl, FILTER_VALIDATE_BOOLEAN);

        if (count($primaryKeys) == 1) {
            return $obj->getItem($primaryKeys[0], $fields, $withAcl);
        } else {
            foreach ($primaryKeys as $primaryKey) {
                if ($item = $obj->getItem($primaryKey, $fields, $withAcl)) {
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
     * @param \Admin\ObjectCrud $obj
     */
    public function setObj($obj)
    {
        $this->obj = $obj;
    }

}

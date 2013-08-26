<?php

namespace Core\ORM;
use Core\Kryn;

/**
 * ORM Abstract class for objects.
 *
 * Please do not handle 'permissionCheck' in $pOptions. This is handled in \Core\Object.
 * You will get in getList() a complex $pCondition object instead (if there are any ACL items)
 *
 *
 * $pPk is an array with following format
 *
 *  array(
 *      '<keyName>'  => <value>
 *      '<keyName2>' => <value2>
 *  )
 *
 * example
 *
 * array(
 *    'id' => 1234
 * )
 *
 */
abstract class ORMAbstract
{
    const
        MANY_TO_ONE = 'nTo1',
        ONE_TO_MANY = '1ToN',
        ONE_TO_ONE = '1To1',
        MANY_TO_MANY = 'nToM';

    /**
     * Cached primary key order.
     * Only keys.
     *
     * @var array
     */
    public $primaryKeys = array();

    /**
     * The current object key.
     *
     * @var string
     */
    public $objectKey;

    /**
     * Cached the object definition.
     *
     * @var \Core\Config\Object
     */
    public $definition;

    /**
     * Constructor
     *
     * @param string $objectKey
     * @param Object $definition
     */
    public function __construct($objectKey, $definition)
    {
        $this->objectKey = \Core\Object::normalizeObjectKey($objectKey);
        $this->definition = $definition;
        foreach ($this->definition->getFields() as $field) {
            if ($field->isPrimaryKey()) {
                $this->primaryKeys[] = $field->getId();
            }
        }
    }

    public function setPrimaryKeys($pks)
    {
        $this->primaryKeys = $pks;
    }

    /**
     * Returns a field definition.
     *
     * @param  string $fieldKey
     *
     * @return array
     */
    public function &getField($fieldKey)
    {
        return $this->definition['fields'][$fieldKey];
    }

    /**
     * Returns the primary keys as array.
     *
     * @return array [key1, key2, key3]
     */
    public function getPrimaryKeys()
    {
        return $this->primaryKeys;
    }

    /**
     * Returns the object key.
     *
     * @return string
     */
    public function getObjectKey()
    {
        return $this->objectKey;
    }

    /**
     * Normalizes a primary key, that is normally used inside PHP classes,
     * since developers are lazy and we need to convert the lazy primary key
     * to the full definition.
     *
     * Possible input:
     *  array('bla'), 'peter', 123,
     *
     * Output
     *  array('id' => 'bla'), array('id' => 'peter'), array('id' => 123)
     *  if the only primary key is named `id`.
     *
     * @param  mixed $pk
     *
     * @return array A single primary key as array. Example: array('id' => 1).
     */
    public function normalizePrimaryKey($pk)
    {
        if (!is_array($pk)) {
            $result = array();
            $result[$this->primaryKeys[0]] = $pk;
        } elseif (is_numeric(key($pk))) {
            $result = array();
            $length = count($this->primaryKeys);
            for ($i = 0; $i < $length; $i++) {
                $result[$this->primaryKeys[$i]] = $pk[$i];
            }
        } else {
            $result = $pk;
        }

        if (count($this->primaryKeys) > count($result)) {
            foreach ($this->primaryKeys as $pk2) {
                if (!$result[$pk2]) {
                    $result[$pk2] = null;
                }
            }
        }

        return $result;
    }

    /**
     * Converts given primary values from type string into proper normalized array definition.
     * This builds the array for the $pk for all of these methods inside this class.
     *
     * The primaryKey comes primarily from the REST API.
     *
     *    admin/object/news/1
     *    admin/objects?uri=news/1/2
     * where
     *    admin/object/news/<id>
     *    admin/objects?uri=news/<id>
     *
     * is this ID.
     *
     * 1/2/3 => array( array(id =>1),array(id =>2),array(id =>3) )
     * 1 => array(array(id => 1))
     * idFooBar => array( id => "idFooBar")
     * idFoo/Bar => array(array(id => idFoo), array(id2 => "Bar"))
     * 1,45/2,45 => array(array(id => 1, pid = 45), array(id => 2, pid=>45))
     *
     * @param  string $pk
     *
     * @return array  Always a array with primary keys as arrays too. So $return[0] is the first primary key array. Example array(array('id' => 4))
     */
    public function primaryStringToArray($pk)
    {
        if ($pk === '') {
            return false;
        }
        $groups = explode('/', $pk);

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

                $key = $this->primaryKeys[$pos];

                $item[$key] = Kryn::urlDecode($value);
            }

            if (count($this->primaryKeys) > count($item)) {
                foreach ($this->primaryKeys as $pk2) {
                    if (!$item[$pk2]) {
                        $item[$pk2] = null;
                    }
                }
            }

            if (count($item) > 0) {
                $result[] = $item;
            }
        }

        return $result;

    }

    /**
     *
     * $pOptions is a array which can contain following options. All options are optional.
     *
     *  'fields'          Limit the columns selection. Use a array or a comma separated list (like in SQL SELECT)
     *                    If empty all columns will be selected.
     *
     *  'permissionCheck' Defines whether we check against the ACL or not. true or false. default false
     *
     *
     * @abstract
     *
     * @param array $pPk
     * @param array $pOptions
     *
     * @return array
     */
    abstract public function getItem($pPk, $pOptions = null);

    /**
     *
     * $pOptions is a array which can contain following options. All options are optional.
     *
     *  'fields'          Limit the columns selection. Use a array or a comma separated list (like in SQL SELECT)
     *                    If empty all columns will be selected.
     *  'offset'          Offset of the result set (in SQL OFFSET)
     *  'limit'           Limits the result set (in SQL LIMIT)
     *  'order'           The column to order. Example:
     *                    array(
     *                      array('category' => 'asc'),
     *                      array(title' => 'asc')
     *                    )
     *
     *  'permissionCheck' Defines whether we check against the ACL or not. true or false. default false
     *
     *
     * @abstract
     *
     * @param array $pCondition Condition object as it is described in function dbConditionToSql() #Extended.
     * @param array $pOptions
     */
    abstract public function getItems($pCondition = null, $pOptions = null);

    /**
     *
     * @abstract
     *
     * @param array $pPk
     *
     */
    abstract public function remove($pPk);

    /**
     * @abstract
     *
     * @param array  $pValues
     * @param array  $pTargetPk If nested set
     * @param string $pPosition `first` (child), `last` (last child), `prev` (sibling), `next` (sibling)
     * @param int    $pScope    If nested set with scope
     *
     * @return array inserted/new primary key/s always as a array.
     */
    abstract public function add($pValues, $pTargetPk = null, $pPosition = 'first', $pScope = null);

    /**
     * Updates an object entry.  This means, all fields which are not defined will be saved as NULL.
     *
     * @abstract
     *
     * @param  array                  $pPk
     * @param  array                  $pValues
     *
     * @throws \ObjectItemNotModified
     */
    abstract public function update($pPk, $pValues);

    /**
     * Patches a object entry. This means, only defined fields will be saved. Fields which are not defined will
     * not be overwritten.
     *
     * @abstract
     *
     * @param  array                  $pPk
     * @param  array                  $pValues
     *
     * @throws \ObjectItemNotModified
     */
    abstract public function patch($pPk, $pValues);

    /**
     * @abstract
     *
     * @param array $pCondition
     *
     * @return int
     */
    abstract public function getCount($pCondition = null);


    /**
     * Do whatever is needed, to clear all items out of this object scope.
     *
     * @abstract
     * @return bool
     */
    abstract public function clear();


    /**
     * Removes anything that is required to hold the data. E.g. SQL Tables, Drop Sequences, etc.
     *
     * @abstract
     * @return bool
     */
    public function drop()
    {
    }


    /**
     * Moves a item to a new position.
     *
     * @param  array                    $pk              Full PK as array
     * @param  array                    $targetPk        Full PK as array
     * @param  string                   $position        `first` (child), `last` (last child), `prev` (sibling), `next` (sibling)
     * @param                           $targetObjectKey
     *
     * @throws \NotImplementedException
     */
    public function move($pk, $targetPk, $position = 'first', $targetObjectKey = null)
    {
        throw new \NotImplementedException('Move method is not implemented for this object layer.');
    }

    /**
     * Returns a branch if the object is a nested set.
     *
     * Result should be:
     *
     *  array(
     *
     *    array(<valuesFromFirstItem>, '_children' => array(<children>), '_childrenCount' => <int> ),
     *    array(<valuesFromSecondItem>, '_children' => array(<children>), '_childrenCount' => <int> ),
     *    ...
     *
     *  )
     *
     * @param  array                    $pk
     * @param  array                    $condition
     * @param  int                      $depth     Started with one. One means, only the first level, no children at all.
     * @param  mixed                    $scope
     * @param  array                    $options
     *
     * @throws \NotImplementedException
     * @throws \Exception
     *
     * @return array
     */
    public function getBranch($pk = null, $condition = null, $depth = 1, $scope = null, $options = null)
    {
        if (!$this->definition['nested']) {
            throw new \Exception(t('Object %s it not a nested set.', $this->objectKey));
        }
        throw new \NotImplementedException(t('getBranch is not implemented.'));
    }


    /**
     * Returns the parent if exists otherwise false.
     *
     * @param  array                    $pk
     *
     * @throws \NotImplementedException
     * @return mixed
     */
    public function getParent($pk)
    {
        throw new \NotImplementedException(t('getParent is not implemented.'));
    }

    /**
     * Returns all parents.
     *
     * Root object first.
     * Each entry has to have also '_objectKey' as value.
     *
     * @param  array                    $pk
     *
     * @throws \NotImplementedException
     */
    public function getParents($pk)
    {
        throw new \NotImplementedException(t('getParents is not implemented.'));
    }


    /**
     * Returns parent's pk, if exists, otherwise null.
     *
     * @param  array $pk
     *
     * @return array
     */
    public function getParentId($pk)
    {
        $object = $this->getParent($pk);

        if (!$object) {
            return null;
        }

        if (count($this->primaryKeys) == 1) {
            return $object[key($this->primaryKeys)];
        } else {
            $result = array();
            foreach ($this->primaryKeys as $key) {
                $result[] = $object[$key];
            }

            return $result;
        }
    }

}

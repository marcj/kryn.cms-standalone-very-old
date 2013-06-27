<?php

namespace Core\ORM;

use Core\Config\Object as ConfigObject;
use Core\Object;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Propel as RuntimePropel;

/**
 * Propel ORM Wrapper.
 */
class Propel extends ORMAbstract
{
    /**
     * Object definition
     *
     * @var ConfigObject
     */
    public $definition = array();

    /**
     * The key of the object
     *
     * @var string
     */
    public $objectKey;

    public $query;

    public $tableMap;

    public $propelPrimaryKeys;

    public function init()
    {
        if ($this->propelPrimaryKeys) {
            return;
        }

        $this->query = $this->getQueryClass();
        $this->tableMap = $this->query->getTableMap();
        $this->propelPrimaryKeys = $this->tableMap->getPrimaryKeys();
    }

    /**
     * Filters $pFields by allowed fields.
     * If '*' we return all allowed fields.
     *
     * @param  array|string $pFields
     *
     * @return array
     */
    public function getFields($pFields)
    {
        $this->init();

        if ($pFields != '*' && is_string($pFields)) {
            $pFields = explode(',', str_replace(' ', '', trim($pFields)));
        }

        $query = $this->getQueryClass();
        $tableMap = $query->getTableMap();

        $fields = array();
        $relations = array();
        $relationFields = array();

        foreach ($this->propelPrimaryKeys as $primaryKey) {
            $fields[$primaryKey->getPhpName()] = $primaryKey;
        }

        if ($pFields == '*') {

            $columns = $tableMap->getColumns();
            foreach ($columns as $column) {
                $fields[$column->getPhpName()] = $column;
            }

            //add relations
            $relationMap = $tableMap->getRelations();

            foreach ($relationMap as $relationName => $relation) {
                if (!$relations[$relationName]) {
                    $relations[$relationName] = $relation;

                    //add columns
                    if ($localColumns = $relation->getLeftColumns()) {
                        foreach ($localColumns as $col) {
                            $fields[$col->getPhpName()] = $col;
                        }
                    }
                    $relations[ucfirst($relationName)] = $relation;

                    $cols = $relation->getRightTable()->getColumns();
                    foreach ($cols as $col) {
                        if ($relation->getType == RelationMap::ONE_TO_ONE || $relation->getType == RelationMap::MANY_TO_ONE) {
                            $fields[$relationName . '.' . $col->getPhpName()] = $col;
                        } else {
                            $relationFields[ucfirst($relationName)][] = $col->getPhpName();
                        }
                    }
                }
            }

        } else if (is_array($pFields)) {
            foreach ($pFields as $field) {

                $relationFieldSelection = '';
                $relationName = '';

                if (($pos = strpos($field, '.')) !== false) {
                    $relationName = ucfirst(substr($field, 0, $pos));
                    $field = ucfirst(substr($field, $pos + 1));
                    $relationFieldSelection = $field;
                    $addRelationField = $field;
                    if (!$tableMap->hasRelation(ucfirst($relationName))) {
                        continue;
                    }

                } elseif ($tableMap->hasRelation(ucfirst($field))) {
                    $relationName = ucfirst($field);
                }

                if ($relationName) {
                    $relation = $tableMap->getRelation(ucfirst($relationName));
                    //check if $field exists in the foreign table
                    if ($relationFieldSelection) {
                        if (!$relation->getRightTable()->hasColumnByPhpName($relationFieldSelection)) {
                            continue;
                        }
                    }

                    $relations[ucfirst($relationName)] = $relation;

                    //add foreignKeys in main table.
                    if ($localColumns = $relation->getLeftColumns()) {
                        foreach ($localColumns as $col) {
                            $fields[$col->getPhpName()] = $col;
                        }
                    }

                    //select at least all pks of the foreign table
                    $pks = $relation->getRightTable()->getPrimaryKeys();
                    foreach ($pks as $pk) {
                        $relationFields[ucfirst($relationName)][] = $pk->getPhpName();
                    }
                    if ($addRelationField) {
                        $relationFields[ucfirst($relationName)][] = $addRelationField;
                    }

                    continue;
                }

                if ($tableMap->hasColumnByPhpName(ucfirst($field)) &&
                    $column = $tableMap->getColumnByPhpName(ucfirst($field))
                ) {
                    $fields[$column->getPhpName()] = $column;
                }
            }
        }

        //filer relation fields
        foreach ($relationFields as $relation => &$objectFields) {

            $objectName = $relations[$relation]->getRightTable()->getPhpName();
            $def = Object::getDefinition(lcfirst($objectName));
            $limit = $def['blacklistSelection'];
            if (!$limit) {
                continue;
            }
            $allowedFields = strtolower(',' . str_replace(' ', '', trim($limit)) . ',');

            $filteredFields = array();
            foreach ($objectFields as $name) {
                if (strpos($allowedFields, strtolower(',' . $name . ',')) === false) {
                    $filteredFields[] = $name;
                }
            }
            $objectFields = $filteredFields;

        }

        //filter
        if ($blacklistSelection = $this->definition->getBlacklistSelection()) {

            $allowedFields = strtolower(',' . str_replace(' ', '', trim($blacklistSelection)) . ',');

            $filteredFields = array();
            foreach ($fields as $name => $def) {
                if (strpos($allowedFields, strtolower(',' . $name . ',')) === false) {
                    $filteredFields[$name] = $def;
                }
            }
            $filteredRelations = array();
            foreach ($relations as $name => $def) {
                if (strpos($allowedFields, strtolower(',' . $name . ',')) === false) {
                    $filteredRelations[$name] = $def;
                }
            }

            return array($filteredFields, $filteredRelations, $relationFields);
        }

        return array($fields, $relations, $relationFields);

    }

    /**
     * Returns a new query class.
     *
     * @param null $pName
     *
     * @return mixed
     * @throws \ObjectNotFoundException
     */
    public function getQueryClass($pName = null)
    {
        $objectKey = $this->getPhpName($pName);

        $clazz = $objectKey . 'Query';
        if (!class_exists($clazz)) {
            throw new \ObjectNotFoundException(tf('The object query %s of %s does not exist.', $clazz, $objectKey));
        }

        return $clazz::create();
    }

    /**
     * Returns php class name.
     *
     * @param  string $objectName
     *
     * @return string
     */
    public function getPhpName($objectName = null)
    {
        if (!$objectName && class_exists($clazz = $this->definition->getPropelClassName())) {
            return $clazz;
        }
        $clazz = $objectName ?: $this->objectKey;
        $temp = explode('\\', $clazz);
        $clazz = ucfirst($temp[0] . '\\Models\\' . $temp[1]);
        return $clazz;
    }

    /**
     * Since the core provide the pk as array('id' => 123) and not as array(123) we have to convert it for propel orm.
     *
     * @param  array $pPk
     *
     * @return mixed Propel PK
     */
    public function getPropelPk($pPk)
    {
        $pPk = array_values($pPk);
        if (count($pPk) == 1) {
            $pPk = $pPk[0];
        }
        return $pPk;
    }

    /**
     * @param       $pQuery
     * @param array $pOptions
     *
     * @throws \FieldNotFoundException
     */
    public function mapOptions($pQuery, $pOptions = array())
    {
        if ($pOptions['limit']) {
            $pQuery->limit($pOptions['limit']);
        }

        if ($pOptions['offset']) {
            $pQuery->offset($pOptions['offset']);
        }

        if (is_array($pOptions['order'])) {
            foreach ($pOptions['order'] as $field => $direction) {
                if (!$this->tableMap->hasColumnByPhpName(ucfirst($field))) {
                    throw new \FieldNotFoundException(tf('Field %s in object %s not found', $field, $this->objectKey));
                } else {
                    $column = $this->tableMap->getColumnByPhpName(ucfirst($field));

                    $pQuery->orderBy($column->getName(), $direction);
                }
            }
        }
    }

    public function getStm(ModelCriteria $pQuery, $pCondition = null)
    {
        $condition = '';
        $params = [];
        $conditionParams = [];
        $id = (hexdec(uniqid()) / mt_rand()) + mt_rand();

        // check that the columns of the main class are already added (if this is the primary ModelCriteria)
        if (!$pQuery->hasSelectClause() && !$pQuery->getPrimaryCriteria()) {
            $pQuery->addSelfSelectColumns();
        }

        $con = RuntimePropel::getServiceContainer()->getReadConnection($pQuery->getDbName());
        $pQuery->configureSelectColumns();

        $dbMap = RuntimePropel::getServiceContainer()->getDatabaseMap($pQuery->getDbName());
        $db = RuntimePropel::getServiceContainer()->getAdapter($pQuery->getDbName());

        $model = $pQuery->getModelName();
        $tableMap = constant($model . '::TABLE_MAP');

        $pQuery->setPrimaryTableName(constant($tableMap . '::TABLE_NAME'));

        $pQuery->externalBasePreSelect($con);

        if ($pCondition) {
            $pQuery->where($id . ' != ' . $id);
        }

        $sql = $pQuery->createSelectSql($params);

        if ($pCondition) {
            $conditionParams = $params;
            $condition = dbConditionToSql($pCondition, $conditionParams, $pQuery->getPrimaryTableName());
        }


        if ($pCondition) {
            $sql = str_replace($id . ' != ' . $id, '(' . $condition . ')', $sql);
        }

        /** @var \PDOStatement $stmt */
        $stmt = $con->prepare($sql);
        $db->bindValues($stmt, $params, $dbMap);

        if ($conditionParams) {
            foreach ($conditionParams as $idx => $v) {
                if (!is_array($v)) { //propel uses arrays as bind values, we with dbConditionToSql not.
                    $stmt->bindValue($idx, $v);
                }
            }
        }

        try {
            $stmt->execute();
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage() . "\nSQL: $sql");
        }

        return $stmt;
    }

    public function mapToOneRelationFields(&$pQuery, $pRelations, $pRelationFields)
    {
        if ($pRelations) {
            foreach ($pRelations as $name => $relation) {
                if ($relation->getType() != RelationMap::MANY_TO_MANY && $relation->getType(
                ) != RelationMap::ONE_TO_MANY
                ) {

                    $pQuery->{'join' . $name}($name);
                    //$pQuery->with($name);

                    if ($pRelationFields[$name]) {
                        foreach ($pRelationFields[$name] as $col) {
                            $pQuery->withColumn($name . "." . $col, '"' . $name . "." . $col . '"');
                        }
                    }

                }
            }
        }

    }

    /**
     * Generates a row from the propel object using the get*() methods. Resolves *-to-many relations.
     *
     * @param      $pClazz
     * @param      $pRow
     * @param      $pSelects
     * @param      $pRelations
     * @param      $pRelationFields
     * @param bool $pPermissionCheck
     *
     * @return array
     */
    public function populateRow($pClazz, $pRow, $pSelects, $pRelations, $pRelationFields, $pPermissionCheck = false)
    {
        /** @var \Publication\Models\News $item */
        $item = new $pClazz();
        $item->fromArray($pRow);

        foreach ($pSelects as $select) {
            if (strpos($select, '.') === false) {
                $newRow[lcfirst($select)] = $item->{'get' . $select}();
            }
        }

        if ($pRelations) {
            foreach ($pRelations as $name => $relation) {

                if ($relation->getType() != RelationMap::MANY_TO_MANY && $relation->getType(
                ) != RelationMap::ONE_TO_MANY
                ) {

                    if (is_array($pRelationFields[$name])) {

                        $foreignClazz = $relation->getForeignTable()->getClassName();
                        $foreignObj = new $foreignClazz();
                        $foreignRow = array();
                        $allNull = true;

                        foreach ($pRelationFields[$name] as $col) {
                            if ($pRow[$name . "." . $col] !== null) {
                                $foreignRow[$col] = $pRow[$name . "." . $col];
                                $allNull = false;
                            }
                        }

                        if ($allNull) {
                            $newRow[lcfirst($name)] = null;
                        } else {
                            $foreignObj->fromArray($foreignRow);
                            $foreignRow = array();
                            foreach ($pRelationFields[$name] as $col) {
                                $foreignRow[lcfirst($col)] = $foreignObj->{'get' . $col}();
                            }
                            $newRow[lcfirst($name)] = $foreignRow;
                        }
                    }
                } else {
                    //many-to-many, we need a extra query
                    if (is_array($pRelationFields[$name])) {
                        $sClazz = $relation->getRightTable()->getClassname();

                        $queryName = $sClazz . 'Query';
                        $filterBy = 'filterBy' . $relation->getSymmetricalRelation()->getName();

                        $sQuery = $queryName::create()
                            ->select($pRelationFields[$name])
                            ->$filterBy(
                                $item
                            );

                        $condition = array();
                        if ($pPermissionCheck) {
                            $condition = \Core\Permission::getListingCondition(lcfirst($sClazz));
                        }
                        $sStmt = $this->getStm($sQuery, $condition);

                        $sItems = array();
                        while ($subRow = dbFetch($sStmt)) {

                            $sItem = new $sClazz();
                            $sItem->fromArray($subRow);

                            $temp = array();
                            foreach ($pRelationFields[$name] as $select) {
                                $temp[lcfirst($select)] = $sItem->{'get' . $select}();
                            }
                            $sItems[] = $temp;
                        }
                        dbFree($sStmt);
                    } else {
                        $get = 'get' . $relation->getPluralName();
                        $sItems = $item->$get();
                    }

                    if ($sItems instanceof RuntimePropelObjectCollection) {
                        $newRow[lcfirst($name)] = $sItems->toArray(null, null, TableMap::TYPE_STUDLYPHPNAME) ? : null;
                    } else if (is_array($sItems) && $sItems) {
                        $newRow[lcfirst($name)] = $sItems;
                    } else {
                        $newRow[lcfirst($name)] = null;
                    }
                }
            }
        }

        return $newRow;

    }

    /**
     * {@inheritDoc}
     */
    public function getItems($pCondition = null, $pOptions = null)
    {
        $this->init();
        $query = $this->getQueryClass();

        list($fields, $relations, $relationFields) = $this->getFields($pOptions['fields']);
        $selects = array_keys($fields);

        $this->mapOptions($query, $pOptions);

        $this->mapToOneRelationFields($query, $relations, $relationFields);

        if ($this->definition->isNested()) {
            $query->filterByLft(1, Criteria::GREATER_THAN);
            $selects[] = 'Lft';
            $selects[] = 'Rgt';
            $selects[] = 'Lvl';
        }

        $query->select($selects);

        $stmt = $this->getStm($query, $pCondition);

        $clazz = $this->getPhpName();

        while ($row = dbFetch($stmt)) {
            $result[] = $this->populateRow(
                $clazz,
                $row,
                $selects,
                $relations,
                $relationFields,
                $pOptions['permissionCheck']
            );
        }

        dbFree($stmt);

        return $result;
    }

    /**
     * Sets the filterBy<pk> by &$pQuery from $pPk.
     *
     * @param mixed $pQuery
     * @param array $pPk
     */
    public function mapPk(&$pQuery, $pPk)
    {
        foreach ($this->primaryKeys as $key) {
            $filter = 'filterBy' . ucfirst($key);
            $val = $pPk[$key];
            if (method_exists($pQuery, $filter)) {
                $pQuery->$filter($val);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getItem($pPk, $pOptions = array())
    {
        $this->init();
        $query = $this->getQueryClass();
        $query->limit(1);

        list($fields, $relations, $relationFields) = $this->getFields($pOptions['fields']);

        $selects = array_keys($fields);

        $query->select($selects);

        $this->mapOptions($query, $pOptions);

        $this->mapToOneRelationFields($query, $relations, $relationFields);

        $this->mapPk($query, $pPk);

        $item = $query->findOne();
        if (!$item) {
            return false;
        }

        $stmt = $this->getStm($query);

        $row = dbFetch($stmt);
        dbFree($stmt);

        $clazz = $this->getPhpName();

        return $row === false ? null : $this->populateRow(
            $clazz,
            $row,
            $selects,
            $relations,
            $relationFields,
            $pOptions['permissionCheck']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function remove($pPk)
    {
        $query = $this->getQueryClass();

        $this->mapPk($query, $pPk);
        $item = $query->findOne();
        if (!$item) {
            return false;
        }

        $item->delete();

        return $item->isDeleted();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $query = $this->getQueryClass();

        if ($this->definition->getWorkspace()) {
            //delete all versions
            $versionQueryQuery = $this->getPhpName() . 'Query';
            $versionQueryQuery::create()->deleteAll();
        }

        $query->deleteAll();
    }

    public function getVersions($pPk, $pOptions = null)
    {
        $queryClass = $this->getPhpName() . 'VersionQuery';
        $query = new $queryClass();

        $query->select(array('id', 'workspaceRev', 'workspaceAction', 'workspaceActionDate', 'workspaceActionUser'));

        $query->filterByWorkspaceId(\Core\WorkspaceManager::getCurrent());

        $this->mapPk($query, $pPk);

        return $query->find()->toArray();

    }

    public function getVersionDiff($pPk, $pOptions = null)
    {
        //default is the diff to the previous

    }

    /**
     * {@inheritdoc}
     */
    public function update($pPk, $pValues)
    {
        $this->init();

        $query = $this->getQueryClass();

        $this->mapPk($query, $pPk);
        $item = $query->findOne();
        $values = $pPk + $pValues;
        $this->mapValues($item, $values);

        return $item->save() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function patch($pPk, $pValues)
    {
        $this->init();

        $query = $this->getQueryClass();

        $this->mapPk($query, $pPk);
        $item = $query->findOne();
        $this->mapValues($item, $pValues, false);

        return $item->save() ? true : false;
    }

    /**
     * {@inheritDoc}
     */
    public function move($pPk, $pTargetPk, $pPosition = 'first', $pTargetObjectKey = null)
    {
        $query = $this->getQueryClass();
        $item = $query->findPK($this->getPropelPk($pPk));

        $method = 'moveToFirstChildOf';
        if ($pPosition == 'up' || $pPosition == 'before' || $pPosition == 'prev') {
            $method = 'moveToPrevSiblingOf';
        }
        if ($pPosition == 'down' || $pPosition == 'below' || $pPosition == 'next') {
            $method = 'moveToNextSiblingOf';
        }

        if (!$pTargetPk) {
            //we need a target
            return null;
        } else {

            if ($pTargetObjectKey && $this->objectKey != $pTargetObjectKey) {
                if (!$this->definition['nestedRootAsObject']) {
                    throw new \InvalidArgumentException('This object has no different object as root.');
                }

                $scopeId = $item->getScopeValue();
                $method = 'moveToFirstChildOf';

                $target = $query->findRoot($scopeId);
            } else {
                $target = $query->findPK($this->getPropelPk($pTargetPk));
            }

            if (!$target) {
                return false;
            }
        }

        if ($item == $target) {
            return false;
        }

        if ($target) {
            return $item->$method($target) ? true : false;
        } else {
            throw new \Exception('Can not find the appropriate target.');
        }

    }

    public function getRoots($pCondition, $pOptions)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function add($pValues, $pTargetPk = null, $pMode = 'first', $pScope = null)
    {
        $this->init();

        $clazz = $this->getPhpName();
        $obj = new $clazz();

        if ($this->definition['nested']) {

            $query = $this->getQueryClass();
            if ($pTargetPk) {
                $branch = $query->findPk($this->getPropelPk($pTargetPk));
            } elseif ($pScope !== null) {
                $branch = $query->findRoot($pScope);
                $root = true;
                if (!$branch) {
                    //no root, create one
                    $branch = new $clazz();
                    $branch->setScopeValue($pScope);
                    $branch->makeRoot();
                    $branch->save();
                }
            }

            if (!$branch) {
                return false;
            }

            if ($branch->getLeftValue() == 1) {
                $root = true;
            }

            if (!$pScope) {
                $pScope = $branch->getScopeValue();
            }

            switch (strtolower($pMode)) {
                case 'last':
                    $obj->insertAsLastChildOf($branch);
                    break;
                case 'prev':
                    if (!$root) {
                        $obj->insertAsPrevSiblingOf($branch);
                    }
                    break;
                case 'next':
                    if (!$root) {
                        $obj->insertAsNextSiblingOf($branch);
                    }
                    break;

                case 'first':
                default:
                    $obj->insertAsFirstChildOf($branch);
                    break;
            }

            if ($pScope) {
                $obj->setScopeValue($pScope);
            }
        }

        $this->mapValues($obj, $pValues);

        if (!$obj->save()) {
            return false;
        }

        //return new pk

        $newPk = array();
        foreach ($this->primaryKeys as $pk) {
            $newPk[$pk] = $obj->{'get' . ucfirst($pk)}();
        }

        return $newPk;
    }

    public function mapValues(&$pItem, &$pValues, $pSetUndefinedAsNull = true)
    {
        $pluralizer = new \Propel\Common\Pluralizer\StandardEnglishPluralizer;
        foreach ($this->definition['fields'] as $fieldName => $field) {

            $fieldValue = $pValues[$fieldName];

            if ($field['primaryKey']) {
                continue;
            }

            $fieldName = ucfirst($fieldName);

            $set = 'set' . $fieldName;
            $methodExist = method_exists($pItem, $set);

            if (!$field && !$methodExist) {
                continue;
            }

            if ($field['type'] == 'object' || $this->tableMap->hasRelation($fieldName)) {

                if ($field['objectRelation'] == ORMAbstract::MANY_TO_MANY || $field['objectRelation'] == ORMAbstract::ONE_TO_MANY) {

                    $name = $pluralizer->getPluralForm(underscore2Camelcase($fieldName));
                    //$getItems = 'get'.underscore2Camelcase($fieldName).'s';
                    $setItems = 'set' . $name;
                    $getItems = 'get' . $name;
                    $clearItems = 'clear' . $name;
                    $addItem = 'add' . underscore2Camelcase($fieldName);

                    if ($fieldValue) {

                        $foreignQuery = $this->getQueryClass($field['object']);
                        $foreignClass = $this->getPhpName($field['object']);
                        $foreignObjClass = \Core\Object::getClass($field['object']);

                        if ($field['objectRelation'] == ORMAbstract::ONE_TO_MANY) {

                            $foreignItems = array();
                            $coll = new RuntimePropelObjectCollection();
                            $coll->setModel(ucfirst($foreignClass));

                            foreach ($fieldValue as $foreignItem) {
                                $pk = Object::getObjectPk($field['object'], $foreignItem);
                                $item = null;
                                if ($pk) {
                                    $pk = $this->getPropelPk($pk);
                                    $item = $foreignQuery->findPk($pk);
                                }
                                if (!$item) {
                                    $item = new $foreignClass();
                                }
                                $item->fromArray($foreignItem, TableMap::TYPE_STUDLYPHPNAME);
                                $coll[] = $item;
                            }

                            $pItem->$setItems($coll);

                        } else {

                            $primaryKeys = array();
                            foreach ($fieldValue as $value) {
                                $primaryKeys[] = $foreignObjClass->normalizePrimaryKey($value);
                            }

                            $propelPks = array();
                            foreach ($primaryKeys as $primaryKey) {
                                $propelPks[] = $this->getPropelPk($primaryKey);
                            }

                            $collItems = $foreignQuery->findPks($propelPks);
                            $pItem->$setItems($collItems);
                        }
                    } elseif ($pSetUndefinedAsNull) {
                        $pItem->$clearItems();
                    }
                    continue;
                }
            }

            if ($methodExist) {
                if ($fieldValue === null && !$pSetUndefinedAsNull) {
                    continue;
                }

                $pItem->$set($fieldValue);
            } else {
                throw new \FieldNotFoundException(tf(
                    'Field %s in object %s not found (%s)',
                    $fieldName,
                    $this->objectKey,
                    $set
                ));
            }

        }

    }

    /**
     * {@inheritdoc}
     */
    public function getCount($pCondition = false)
    {
        $query = $this->getQueryClass();

        $query->clearSelectColumns()->addSelectColumn('COUNT(*)');

        $stmt = $this->getStm($query, $pCondition);

        $row = dbFetch($stmt);

        dbFree($stmt);

        return current($row) + 0;
    }

    public function getBranchChildrenCount($pPk = null, $pCondition = null, $pScope = null)
    {
        $query = $this->getQueryClass();

        $query->clearSelectColumns()->addSelectColumn('COUNT(*)');

        if ($pPk) {
            $pkQuery = $this->getQueryClass();

            $this->mapPk($pkQuery, $pPk);
            $pkItem = $pkQuery->findOne();

            if (!$pkItem) {
                return null;
            }
            $query->childrenOf($pkItem);
        } elseif ($pScope) {
            $pkQuery = $this->getQueryClass();
            $root = $pkQuery->findRoot($pScope);
            if (!$root) {
                return null;
            }
            $query->childrenOf($root);
        }

        $stmt = $this->getStm($query, $pCondition);

        $row = dbFetch($stmt);

        dbFree($stmt);

        return current($row) + 0;

    }

    public function pkFromRow($pRow)
    {
        $pks = array();
        foreach ($this->primaryKeys as $pk) {
            $pks[$pk] = $pRow[$pk];
        }

        return $pks;
    }

    /**
     * {@inheritdoc}
     */
    public function getBranch($pPk = null, $pCondition = null, $pDepth = 1, $pScope = null, $pOptions = null)
    {
        $query = $this->getQueryClass();
        if (!$pPk) {
            if ($pScope === null && $this->definition['nestedRootAsObject']) {
                throw new \InvalidArgumentException('Argument `scope` is missing. Since this object is a nested set with different roots, we need a `scope` to get the first level.');
            }
            $parent = $query->findRoot($pScope);
        } else {
            $parent = $query->findPK($this->getPropelPk($pPk));
        }

        if (!$parent) {
            return null;
        }

        if ($pDepth === null) {
            $pDepth = 1;
        }

        $query = $this->getQueryClass();

        $query->childrenOf($parent);

        list($fields, $relations, $relationFields) = $this->getFields($pOptions['fields']);
        $selects = array_keys($fields);

        $selects[] = 'Lft';
        $selects[] = 'Rgt';
        $selects[] = 'Title';
        $query->select($selects);

        $query->orderByBranch();

        $this->mapOptions($query, $pOptions);

        $this->mapToOneRelationFields($query, $relations, $relationFields);

        $stmt = $this->getStm($query, $pCondition);

        $clazz = $this->getPhpName();

        while ($row = dbFetch($stmt)) {
            $item = $this->populateRow(
                $clazz,
                $row,
                $selects,
                $relations,
                $relationFields,
                $pOptions['permissionCheck']
            );

            if ($pDepth > 0) {
                if (!$pCondition) {
                    $item['_childrenCount'] = ($item['rgt'] - $item['lft'] - 1) / 2;
                    if ($pDepth > 1 && ($item['rgt'] - $item['lft']) > 0) {
                        $item['_children'] = $this->getBranch(
                            $this->pkFromRow($item),
                            $pCondition,
                            $pDepth - 1,
                            $pScope,
                            $pOptions
                        );
                    }
                } else {

                    //since we have a custom (probably a permission listing condition) we have
                    //firstly to select all children and then count
                    if ($pDepth > 1) {
                        $item['_children'] = $this->getBranch(
                            $this->pkFromRow($item),
                            $pCondition,
                            $pDepth - 1,
                            $pScope,
                            $pOptions
                        );
                        $item['_childrenCount'] = count($item['_children']);
                    } else {
                        $children = $this->getBranch(
                            $this->pkFromRow($item),
                            $pCondition,
                            $pDepth - 1,
                            $pScope,
                            $pOptions
                        );
                        $item['_childrenCount'] = count($children);
                    }
                }
            }
            $result[] = $item;
        }

        dbFree($stmt);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getParents($pPk, $pOptions = null)
    {
        $query = $this->getQueryClass();
        $item = $query->findPK($this->getPropelPk($pPk));

        if (!$item) {
            throw new \Exception('Can not found entry. ' . var_export($pPk, true));
        }
        if (!$item->getRgt()) {
            throw new \Exception('Entry it not in a tree. ' . var_export($pPk, true));
        }

        list($fields, $relations, $relationFields) = $this->getFields($pOptions['fields']);
        $selects = array_keys($fields);

        $selects[] = 'Lft';
        $selects[] = 'Rgt';
        $selects[] = 'Title';
        $query->select($selects);

        $this->mapOptions($query, $pOptions);

        $this->mapToOneRelationFields($query, $relations, $relationFields);

        $query->ancestorsOf($item);
        $query->orderByLevel();

        $stmt = $this->getStm($query);
        $clazz = $this->getPhpName();

        $result = array();

        if ($this->definition['nestedRootAsObject']) {
            //fetch root object entry
            $scopeField = 'get' . ucfirst($this->definition['nestedRootObjectField']);
            $scopeId = $item->$scopeField();
            $root = Object::get($this->definition['nestedRootObject'], $scopeId);
            $root['_object'] = $this->definition['nestedRootObject'];
            $result[] = $root;

        }
        $item = false;

        while ($row = dbFetch($stmt)) {

            //propels nested set requires a own root item, we do not return this
            if (false === $item) {
                $item = true;
                continue;
            }

            $item = $this->populateRow(
                $clazz,
                $row,
                $selects,
                $relations,
                $relationFields,
                $pOptions['permissionCheck']
            );
            $result[] = $item;
        }

        return $result;

    }

    /**
     * {@inheritdoc}
     */
    public function getParent($pPk, $pOptions = null)
    {
        $query = $this->getQueryClass();
        $item = $query->findPK($this->getPropelPk($pPk));

        list($fields, $relations, $relationFields) = $this->getFields($pOptions['fields']);
        $selects = array_keys($fields);

        $selects[] = 'Lft';
        $selects[] = 'Rgt';
        $selects[] = 'Title';
        $query->select($selects);

        $this->mapOptions($query, $pOptions);

        $this->mapToOneRelationFields($query, $relations, $relationFields);

        $query->ancestorsOf($item);
        $query->orderByLevel(true);
        $query->limit(1);

        $stmt = $this->getStm($query);
        $clazz = $this->getPhpName();

        $row = dbFetch($stmt);
        $item = $this->populateRow($clazz, $row, $selects, $relations, $relationFields, $pOptions['permissionCheck']);

        return $item;

    }

}

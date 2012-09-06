<?php

namespace Core\ORM;

use \Core\Kryn;
use \Core\Object;

/**
 * Propel ORM Wrapper.
 */
class Propel extends ORMAbstract {


    /**
     * Object definition
     *
     * @var array
     */
    public $definition = array();


    /**
     * The key of the object
     *
     * @var string
     */
    public $objectKey = '';

    public $query;

    public $tableMap;

    public $propelPrimaryKeys;

    /**
     * Constructor
     *
     * @param string $pObjectKey
     * @param array  $pDefinition
     */
    function __construct($pObjectKey, $pDefinition){
        $this->objectKey = $pObjectKey;
        $this->definition = $pDefinition;
    }

    public function init(){
        if ($this->primaryKeys) return;

        $this->query = $this->getQueryClass();
        $this->tableMap = $this->query->getTableMap();
        $this->propelPrimaryKeys = $this->tableMap->getPrimaryKeys();

        $this->primaryKeys = array();
        foreach ($this->propelPrimaryKeys as $k)
            $this->primaryKeys[] = lcfirst($k->getPhpName());
    }

    public function primaryStringToArray($pPk){
        $this->init(); //load pks
        return parent::primaryStringToArray($pPk);
    }


    /**
     * Filters $pFields by allowed fields.
     * If '*' we return all allowed fields.
     *
     * @param array|string $pFields
     * @return array
     */
    public function getFields($pFields){

        $this->init();

        if ($pFields != '*' && is_string($pFields))
            $pFields = explode(',', str_replace(' ', '', trim($pFields)));

        $query = $this->getQueryClass();
        $tableMap = $query->getTableMap();

        $fields = array();
        $relations = array();
        $relationFields = array();

        foreach ($this->propelPrimaryKeys as $primaryKey){
            $fields[$primaryKey->getPhpName()] = $primaryKey;
        }

        if ($pFields == '*'){

            $columns = $tableMap->getColumns();
            foreach ($columns as $column){
                $fields[$column->getPhpName()] = $column;
            }

            //add relations
            $relationMap = $tableMap->getRelations();

            foreach ($relationMap as $relationName => $relation){
                if (!$relations[$relationName]){
                    $relations[$relationName] = $relation;

                    //add columns
                    if ($localColumns = $relation->getForeignColumns()){
                        foreach ($localColumns as $col){
                            $fields[$col->getPhpName()] = $col;
                        }
                    }
                    $relations[ucfirst($relationName)] = $relation;

                    $cols = $relation->getRightTable()->getColumns();
                    foreach ($cols as $col){
                        if ($relation->getType == \RelationMap::ONE_TO_ONE || $relation->getType == \RelationMap::MANY_TO_ONE){
                            $fields[$relationName.'.'.$col->getPhpName()] = $col;
                        } else {
                            $relationFields[ucfirst($relationName)][] = $col->getPhpName();
                        }
                    }
                }
            }


        } else {
            foreach ($pFields as $field){

                $relationFieldSelection = '';

                if ( ($pos = strpos($field, '.')) !== false){
                    $relationName = ucfirst(substr($field, 0, $pos));
                    $field = ucfirst(substr($field, $pos+1));
                    $relationFieldSelection = $field;
                    $addRelationField = $field;
                    if (!$tableMap->hasRelation(ucfirst($relationName))){
                        continue;
                    }

                } else if ($tableMap->hasRelation(ucfirst($field))){
                    $relationName = ucfirst($field);
                }

                if ($relationName){
                    $relation = $tableMap->getRelation(ucfirst($relationName));

                    //check if $field exists in the foreign table
                    if ($relationFieldSelection)
                        if (!$relation->getRightTable()->hasColumnByPhpName($relationFieldSelection)) continue;

                    $relations[ucfirst($relationName)] = $relation;

                    //add foreignKeys in main table.
                    if ($localColumns = $relation->getLocalColumns()){
                        foreach ($localColumns as $col)
                            $fields[$col->getPhpName()] = $col;
                    }

                    //select at least all pks of the foreign table
                    $pks = $relation->getRightTable()->getPrimaryKeys();
                    foreach ($pks as $pk){
                       $relationFields[ucfirst($relationName)][] = $pk->getPhpName();
                    }
                    if ($addRelationField)
                        $relationFields[ucfirst($relationName)][] = $addRelationField;

                    continue;
                }

                if ($tableMap->hasColumnByPhpName(ucfirst($field)) &&
                    $column = $tableMap->getColumnByPhpName(ucfirst($field))){
                    $fields[$column->getPhpName()] = $column;
                }
            }
        }

        //filer relation fields
        foreach ($relationFields as $relation => &$objectFields){
            $objectName = $relations[$relation]->getRightTable()->getPhpName();
            $limit = Kryn::$objects[lcfirst($objectName)]['blacklistSelection'];
            if (!$limit) continue;
            $allowedFields = strtolower(','.str_replace(' ', '', trim($limit)).',');

            $filteredFields = array();
            foreach ($objectFields as $name){
                if (strpos($allowedFields, strtolower(','.$name.',')) === false){
                    $filteredFields[] = $name;
                }
            }
            $objectFields = $filteredFields;

        }

        //filter
        if ($this->definition['blacklistSelection']){

            $allowedFields = strtolower(','.str_replace(' ', '', trim($this->definition['blacklistSelection'])).',');

            $filteredFields = array();
            foreach ($fields as $name => $def){
                if (strpos($allowedFields, strtolower(','.$name.',')) === false){
                    $filteredFields[$name] = $def;
                }
            }
            $filteredRelations = array();
            foreach ($relations as $name => $def){
                if (strpos($allowedFields, strtolower(','.$name.',')) === false){
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
     * @param string $pName
     * @return Object The query class object.
     */
    public function getQueryClass($pName = null){
        $objectKey = $pName?$pName:$this->getPhpName();

        $clazz = '\\'.ucfirst($objectKey).'Query';
        if (!class_exists($clazz)){
            throw new \ObjectNotFoundException(tf('The object query %s of %s does not exist.', $clazz, $objectKey));
        }

        return $clazz::create();
    }

    /**
     * Returns the peer name.
     *
     * @param string $pName
     * @return string
     */
    public function getPeerName($pName = null){
        $objectKey = $pName?$pName:$this->getPhpName();

        $clazz = '\\'.ucfirst($objectKey).'Peer';
        if (!class_exists($clazz)){
            throw new \ObjectNotFoundException(tf('The object query %s of %s does not exist.', $clazz, $objectKey));
        }

        return $clazz;
    }


    /**
     * Returns php class name.
     *
     * @param string $pName
     * @return string
     */
    public function getPhpName($pName = null){
        return $pName ? ucfirst($pName) : ucfirst($this->objectKey);
    }


    /**
     * Since the core provide the pk as array('id' => 123) and not as array(123) we have to convert it for propel orm.
     * 
     * @param  array $pPk
     * @return mixed Propel PK
     */
    public function getPropelPk($pPk){
        
        $pPk = array_values($pPk);
        if (count($pPk) == 1) $pPk = $pPk[0];
        return $pPk;
    }



    /**
     * {@inheritDoc}
     */
    public function getItem($pCondition, $pOptions = array()){

        $this->init();
        $query = $this->getQueryClass();
        $query->limit(1);

        list($fields, $relations, $relationFields) = $this->getFields($pOptions['fields']);
        $selects = array_keys($fields);

        $query->select($selects);

        $this->mapOptions($query, $pOptions);

        $this->mapToOneRelationFields($query, $relations, $relationFields);

        $stmt = $this->getStm($query, $pCondition);

        $clazz = $this->getPhpName();

        $row = dbFetch($stmt);
        return $row===false?false:$this->populateRow($clazz, $row, $selects, $relations, $relationFields, $pOptions['permissionCheck']);
    }

    public function mapOptions($pQuery, $pOptions = array()){

        if ($pOptions['limit'])
            $pQuery->limit($pOptions['limit']);

        if ($pOptions['offset'])
            $pQuery->offset($pOptions['offset']);

        if (is_array($pOptions['order'])){
            foreach ($pOptions['order'] as $field => $direction){
                if ($column = $this->tableMap->getColumn($field)){
                    if (!$column[0])
                        throw new \FieldNotFoundException(tf('Field %s in object %s not found', $field, $this->objectKey));
                    
                    $pQuery->orderBy($column[1], $direction);
                }
            }
        }
    }

    public function getStm($pQuery, $pCondition){

        //we have a condition, so extract the SQL and append our custom condition object
        $params = array();

        $id = (hexdec(uniqid())/mt_rand())+mt_rand();

        if ($pCondition){
            $pQuery->where($id.' != '.$id);
        }

        $pQuery->setPrimaryTableName(constant($this->getPeerName() . '::TABLE_NAME'));

        list($sql, $params) = $pQuery->getSql();

        if ($pCondition){
            $data = $params;
            $condition = dbConditionToSql($pCondition, $data, $pQuery->getPrimaryTableName());
            $sql = str_replace($id.' != '.$id, $condition, $sql);
        }

        $stmt = $pQuery->bindValues($sql, $params);

        if ($data){
            foreach ($data as $idx => $v){
                if (!is_array($v)){ //propel uses arrays as bind values, we with dbConditionToSql not.
                    $stmt->bindValue($idx, $v);
                }
            }
        }

        $stmt->execute();
        return $stmt;
    }

    public function mapToOneRelationFields($pQuery, $pRelations, $pRelationFields){

        if ($pRelations){
            foreach ($pRelations as $name => $relation){
                if ($relation->getType() != \RelationMap::MANY_TO_MANY && $relation->getType() != \RelationMap::ONE_TO_MANY){

                    $pQuery->{'join'.$name}($name);
                    $pQuery->with($name);

                    if ($pRelationFields[$name]){
                        foreach ($pRelationFields[$name] as $col){
                            $pQuery->addAsColumn('"'.$name.".".$col.'"', $name.".".$col);
                        }
                    }

                    //todo, add ACL condition for object $relation->getForeignTable()->getPhpName()
                    //var_dump($relation->getForeignTable()->getPhpName()); exit;
                }
            }
        }

    }

    public function populateRow($pClazz, $pRow, $pSelects, $pRelations, $pRelationFields, $pPermissionCheck = false){

        $item = new $pClazz();
        $item->hydrateFromNames($pRow, \BasePeer::TYPE_PHPNAME);

        $newRow = array();
        foreach ($pSelects as $select){
            $newRow[lcfirst($select)] = $item->{'get'.$select}();
        }

        if ($pRelations){
            foreach ($pRelations as $name => $relation){

                if ($relation->getType() != \RelationMap::MANY_TO_MANY && $relation->getType() != \RelationMap::ONE_TO_MANY){
                    if (is_array($pRelationFields[$name])){
                        
                        $foreignClazz = $relation->getForeignTable()->getPhpName();
                        $foreignObj = new $foreignClazz();
                        $foreignRow = array();
                        $allNull = true;

                        foreach ($pRelationFields[$name] as $col){
                            if ($pRow[$name.".".$col] !== null){
                                $foreignRow[$col] = $pRow[$name.".".$col];
                                $allNull = false;
                            }
                        }

                        if ($allNull){
                            $newRow[lcfirst($name)] = null;
                        } else {
                            $foreignObj->hydrateFromNames($foreignRow, \BasePeer::TYPE_PHPNAME);

                            $foreignRow = array();
                            foreach ($pRelationFields[$name] as $col){
                                $foreignRow[lcfirst($col)] = $foreignObj->{'get'.$col}();
                            }
                            $newRow[lcfirst($name)] = $foreignRow;
                        }
                    }
                } else {
                    //*-to-many, we need a extra query
                    if (is_array($pRelationFields[$name])){
                        $sClazz    = $relation->getRightTable()->getPhpName();
                        $queryName = $sClazz.'Query';
                        $filterBy  = 'filterBy'.$relation->getSymmetricalRelation()->getName();

                        $sQuery = $queryName::create()
                            ->select($pRelationFields[$name])
                            ->$filterBy($item);

                        $condition = array();
                        if ($pPermissionCheck){
                            $condition = \Core\Acl::getListingCondition(lcfirst($sClazz));
                        }
                        $sStmt = $this->getStm($sQuery, $condition);

                        $sItems = array();
                        while ($subRow = dbFetch($sStmt)){

                            $sItem = new $sClazz();
                            $sItem->hydrateFromNames($subRow, \BasePeer::TYPE_PHPNAME);

                            $temp = array();
                            foreach ($pRelationFields[$name] as $select){
                                $temp[lcfirst($select)] = $sItem->{'get'.$select}();
                            }
                            $sItems[] = $temp;
                        }
                    } else {
                        $get = 'get'.$relation->getPluralName();
                        $sItems = $item->$get();
                    }

                    if ($sItems instanceof \PropelObjectCollection)
                        $newRow[lcfirst($name)] = $sItems->toArray(null, null, \BasePeer::TYPE_STUDLYPHPNAME) ?: null;
                    else if (is_array($sItems) && $sItems)
                        $newRow[lcfirst($name)] = $sItems;
                    else
                        $newRow[lcfirst($name)] = null;
                }
            }
        }

        return $newRow;

    }

    /**
     * {@inheritDoc}
     */
    public function getItems($pCondition = null, $pOptions = null){

        $this->init();
        $query = $this->getQueryClass();

        list($fields, $relations, $relationFields) = $this->getFields($pOptions['fields']);
        $selects = array_keys($fields);

        $query->select($selects);

        $this->mapOptions($query, $pOptions);

        $this->mapToOneRelationFields($query, $relations, $relationFields);

        $stmt = $this->getStm($query, $pCondition);

        $clazz = $this->getPhpName();

        while ($row = dbFetch($stmt)){
            $result[] = $this->populateRow($clazz, $row, $selects, $relations, $relationFields, $pOptions['permissionCheck']);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($pPk){
        $peer = $this->getPeerName();
        return $peer::doDelete($pPk);
    }


    /**
     * {@inheritdoc}
     */
    public function add($pValues, $pBranchPk = false, $pMode = 'first', $pScope = 0){
        $clazz = $this->getPhpName();
        $obj = new $clazz();

        if ($this->definition['nested']){

            $query = $this->getQueryClass();
            if ($pBranchPk)
                $branch = $query->findPk($this->getPropelPk($pBranchPk));
            else {
                $branch = $query->findRoot($pScope);
                $root = true;
            }

            switch (strtolower($pMode)){
                case 'first': $obj->insertAsFirstChildOf($branch); break;
                case 'last':  $obj->insertAsLastChildOf($branch); break;
                case 'prev':  if (!$root) $obj->insertAsPrevSiblingOf($branch); break;
                case 'next':  if (!$root) $obj->insertAsNextSiblingOf($branch); break;
            }

            if ($pScope){
                $obj->setScopeValue($pScope);
            }
        }

        $this->mapValues($obj, $pValues);

        return ($obj->save())? $obj->getPrimaryKey() : false;
    }


    /**
     * {@inheritdoc}
     */
    public function update($pPk, $pValues){
        
        $query = $this->getQueryClass();
        $item = $query->findPk($this->getPropelPk($pPk));

        $this->mapValues($item, $pValues);

        return $item->save()?true:false;
    }


    public function mapValues($pItem, $pValues){

        $query = $this->getQueryClass();

        foreach ($pValues as $fieldName => $fieldValue){

            $field = $this->getField($fieldName);
            $fieldName = ucfirst($fieldName);

            if ($field['type'] == 'object'){

                $primaryKeys = \Core\Object::parsePk($field['object'], $fieldValue);

                if ($field['objectRelation'] == 'nToM'){

                    $setItems = 'set'.underscore2Camelcase($fieldName).'s';

                    $foreignQuery = $this->getQueryClass($field['object']);

                    foreach ($primaryKeys as $primaryKey){
                        $propelPks[] = $this->getPropelPk($primaryKey);
                    }

                    $collItems = $foreignQuery
                        ->findPks($propelPks);

                    $pItem->$setItems($collItems);
                    continue;
                }
            }

            if ($this->tableMap->hasColumnByPhpName($fieldName) && $column = $this->tableMap->getColumnByPhpName($fieldName)){

                $set = 'set'.$column->getPhpName();

                $pItem->$set($fieldValue);
            } else {
                throw new \FieldNotFoundException(tf('Field %s in object %s not found', $fieldName, $this->objectKey));
            }


        }

    }
    /**
     * {@inheritdoc}
     */
    public function getCount($pCondition = false){

        $query = $this->getQueryClass();

        $query->clearSelectColumns()->addSelectColumn('COUNT(*)');

        $stmt = $this->getStm($query, $pCondition);

        $row = dbFetch($stmt);

        return current($row);

    }


    /**
     * {@inheritdoc}
     */
    public function getBranch($pPk = false, $pCondition = false, $pDepth = 1, $pScope = 0,
        $pOptions = false){


        $pQuery = $this->getQueryClass();

        if ($pCondition){
            $where = dbConditionToSql($pCondition);
            $pQuery->where($where);
        }


        return 'hi';

        // TODO: Implement getTree() method.
        // 
    }


}
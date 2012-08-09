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

    /**
     * Constructor
     *
     * @param string $pObjectKey
     * @param array  $pDefinition
     */
    function __construct($pObjectKey, $pDefinition){
        $this->objectKey = $pObjectKey;
        $this->definition = $pDefinition;
        $peer = $this->getPeerName();
    }

    public function init(){
        $this->query = $this->getQueryClass();
        $this->tableMap = $this->query->getTableMap();
        $this->primaryKeys = $this->tableMap->getPrimaryKeys();
    }


    /**
     * Filters $pFields by allowed fields.
     * If '*' we return all allowed fields.
     *
     * @param array|string $pFields
     * @return array
     */
    public function getFields($pFields){

        if ($pFields != '*' && is_string($pFields))
            $pFields = explode(',', str_replace(' ', '', trim($pFields)));

        $query = $this->getQueryClass();
        $tableMap = $query->getTableMap();

        $fields = array();
        $relations = array();
        $relationFields = array();

        foreach ($this->primaryKeys as $primaryKey){
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
                       $relationFields[ucfirst($relationName)][] = $col->getPhpName();
                    }
                }
            }


        } else {
            foreach ($pFields as $field){

                if ( ($pos = strpos($field, '.')) !== false){
                    $relationName = ucfirst(substr($field, 0, $pos));
                    $field = ucfirst(substr($field, $pos+1));
                    $addRelationField = $field;
                    if (!$tableMap->hasRelation(ucfirst($relationName))){
                        continue;
                    }

                } else if ($tableMap->hasRelation(ucfirst($field))){
                    $relationName = ucfirst($field);
                }

                if ($relationName){
                    $relation = $tableMap->getRelation(ucfirst($relationName));
                    $relations[ucfirst($relationName)] = $relation;

                    //add foreignKeys in main table.
                    if ($localColumns = $relation->getLocalColumns()){
                        foreach ($localColumns as $col)
                            $fields[$col->getPhpName()] = $col;
                    }

                    //select at least pks of the foreign table
                    $pks = $relation->getRightTable()->getPrimaryKeys();
                    foreach ($pks as $pk){
                       $relationFields[ucfirst($relationName)][] = $pk->getPhpName();
                    }
                    if ($addRelationField)
                        $relationFields[ucfirst($relationName)][] = $addRelationField;

                    continue;
                }

                if ($tableMap->hasColumnByPhpName(ucfirst($field)) && $column = $tableMap->getColumnByPhpName(ucfirst($field))){
                    $fields[$column->getPhpName()] = $column;
                }
            }
        }
        
        //todo, check for selects in joined column

        //filter
        if ($this->definition['limitSelection']){

            $allowedFields = strtolower(','.str_replace(' ', '', trim($this->definition['limitSelection'])).',');

            $filteredFields = array();
            foreach ($fields as $name){
                if (strpos($allowedFields, strtolower(','.$name.',')) !== false){
                    $filteredFields[] = $name;
                }
            }
            $filteredRelations = array();
            foreach ($fields as $name){
                if (strpos($allowedFields, strtolower(','.$name.',')) !== false){
                    $filteredRelations[] = $name;
                }
            }
            return array($filteredFields, $filteredRelations);
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
        return $pName ? Kryn::$objects[$pName]['phpName'] : $this->definition['phpName'];
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

        $query = $this->getQueryClass();

        $fields = $this->getFields($pOptions['fields']);

        $this->mapSelect($query, $fields);
        $stm = $this->getStm($query, $pCondition);

        return dbFetch($stm);
    }

    /**
     * {@inheritDoc}
     */
    public function getItemsss($pCondition = null, $pOptions = null){

        $query = $this->getQueryClass();

        list($fields, $relations) = $this->getFields($pOptions['fields']);

        //$query->select(array('id', 'username'));
        $this->selectPrimary($query);
        //$this->mapSelect($query, $fields);
        //$this->mapOptions($query, $pOptions);

        return $query->find()->toArray();
        $stm = $this->getStm($query, $pCondition);

        $return = array();

        $clazz = $this->getPhpName();

        //$items = $query->getFormatter()->init($query)->format($stm);
        

        while ($row = dbFetch($stm)){

            $item = new $clazz();
            $item->fromArray($row, \BasePeer::TYPE_RAW_COLNAME);

            $row = $item->toArray(\BasePeer::TYPE_STUDLYPHPNAME);

            foreach ($relations as $relationName){
                $get = 'get'.$relationName.'s'; 
                $relationData = $item->$get();

                if ($relationData instanceof \PropelObjectCollection)
                    $row[lcfirst($relationName)] = $relationData->toArray(null, null, \BasePeer::TYPE_STUDLYPHPNAME);
                else
                    $row[lcfirst($relationName)] = $relationData->toArray(\BasePeer::TYPE_STUDLYPHPNAME);
            }

            $return[] = $row;
        }

        return $return;

        
        $items = $query->find();

        if (count($relations) == 0){
            return $items->toArray(null, null, \BasePeer::TYPE_STUDLYPHPNAME);
        }

        $clazz = $this->getPhpName();

        $result = array();

        foreach ($items as $item){

            if (get_class($item) != $clazz){
                $obj = new $clazz();
                $obj->fromArray($item);
            } else {
                $obj = $item;
            }

            $item = $obj->toArray(\BasePeer::TYPE_STUDLYPHPNAME);

            foreach ($relations as $relation){
                $get = 'get'.$relation.'s';
                $item[$relation] = $obj->$get();

                if ($item[$relation] instanceof \PropelObjectCollection)
                    $item[$relation] = $item[$relation]->toArray(null, null, \BasePeer::TYPE_STUDLYPHPNAME);
                else
                    $item[$relation] = $item[$relation]->toArray(\BasePeer::TYPE_STUDLYPHPNAME);
            }

            $result[] = $item;
        }
        return $result;
        exit;

        $stm = $this->getStm($query, $pCondition);

        return dbFetchAll($stm);
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

    /**
     * {@inheritDoc}
     */
    public function getForeignItems($pConditon = null, $pField, $pForeignCondition, $pOptions = array()){

        $item = $this->getItem($pConditon);
        if (!$item) throw new \ObjectItemNotFoundException(tf('The item %s can not be found.', $pConditon));

        $clazz = $this->getPhpName();
        $obj  = new $clazz();
        $obj->fromArray($item, \BasePeer::TYPE_FIELDNAME);

        $field = $this->getField($pField);

        if (!$field)
            throw new \FieldNotFoundException(tf('The field %s can not be found in object %', $pField, $pObjectKey));

        if ($field['type'] != 'object')
            throw new \InvalidFieldException(tf('The field %s is not from type object (%s)', $pField, $field['type']));

        if (!$field['object'])
            throw new \InvalidFieldException(tf('The field %s is from type object but has no object set.', $pField));

        if (!$definition = Kryn::$objects[$field['object']])
            throw new \ObjectNotFoundException(tf('The object %s in field %s does not exist.', $field['object'], $pField));


        $clazz = get_class($this);
        $ormObject = new $clazz($field['object'], $definition);

        $query = $ormObject->getQueryClass($field['object']);

        //filter by field
        $foreignKey = underscore2Camelcase($pField).'_'.$this->getPhpName();
        $filterBy   = 'filterBy'.$foreignKey;
        $query->$filterBy($obj);

        $fields = $ormObject->getFields($pOptions['fields']);

        $ormObject->selectPrimary($query);
        $ormObject->mapSelect($query, $fields);

        $ormObject->mapOptions($query, $pOptions);

        $stm = $ormObject->getStm($query, $pCondition);

        return dbFetchAll($stm);
        
        var_dump($obj); exit;

        $query = $this->getQueryClass();

        var_dump($this->objectKey);

        $tableMap = $query->getTableMap();
        $relationMap = $tableMap->getRelation(underscore2Camelcase($pForeignKey));

        var_dump($relationMap); exit;
        $foreignObjectKey = '';

        $query = $this->getQueryClass($foreignObjectKey);

        $relatedItem = $query->findPk($pRelatedPk);

        if (!$relatedItem) throw new \ObjectItemNotFoundException(tf('The item %s can not be found.', $pRelatedPk));

        $filterBy = 'filterBy'.underscore2Camelcase($pForeignKey);
        $query->$filterBy($relatedItem);

        return $query->count();

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
                    $stmt->bindValue(':p'.($idx+1), $v);
                }
            }
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * {@inheritDoc}
     */
    public function getItems($pCondition = null, $pOptions = null){

        $this->init();
        $query = $this->getQueryClass();
        $peer  = $this->getPeerName();

        list($fields, $relations, $relationFields) = $this->getFields($pOptions['fields']);

        $selects = array_keys($fields);
        $query->select($selects);

        $this->mapOptions($query, $pOptions);


        $hasSelectedRelations = count($relations);

        if ($hasSelectedRelations){
            foreach ($relations as $name => $relation){
                if ($relation->getType() != \RelationMap::MANY_TO_MANY && $relation->getType() != \RelationMap::ONE_TO_MANY){

                    $query->{'join'.$name}($name);
                    $query->with($name);

                    if ($relationFields[$name]){
                        foreach ($relationFields[$name] as $col){
                            $query->addAsColumn('"'.$name.".".$col.'"', $name.".".$col);
                        }
                    }
                }
            }
        }

        $query->setFormatter('PropelStatementFormatter');

        $stmt = $query->find();

        $clazz = $this->getPhpName();

        while ($row = dbFetch($stmt)){

            $item = new $clazz();
            $item->hydrateFromNames($row, \BasePeer::TYPE_PHPNAME);

            $newRow = array();
            foreach ($selects as $select){
                $newRow[lcfirst($select)] = $item->{'get'.$select}();
            }

            if ($hasSelectedRelations){
                foreach ($relations as $name => $relation){

                    if ($relation->getType() != \RelationMap::MANY_TO_MANY && $relation->getType() != \RelationMap::ONE_TO_MANY){
                        if (is_array($relationFields[$name])){
                            
                            $foreignClazz = $relation->getForeignTable()->getPhpName();
                            $foreignObj = new $foreignClazz();
                            $foreignRow = array();
                            $allNull = true;

                            foreach ($relationFields[$name] as $col){
                                if ($row[$name.".".$col] !== null){
                                    $foreignRow[$col] = $row[$name.".".$col];
                                    $allNull = false;
                                }
                            }

                            if ($allNull){
                                $newRow[lcfirst($name)] = null;
                            } else {
                                $foreignObj->hydrateFromNames($foreignRow, \BasePeer::TYPE_PHPNAME);

                                $foreignRow = array();
                                foreach ($relationFields[$name] as $col){
                                    $foreignRow[lcfirst($col)] = $foreignObj->{'get'.$col}();
                                }
                                $newRow[lcfirst($name)] = $foreignRow;
                            }
                        }
                    } else {
                        //*-to-many, we need a extra query
                         $get = 'get'.$name.'s';

                        $sItems = $item->$get();
                        if ($sItems){
                            if ($sItems instanceof \PropelObjectCollection)
                                $newRow[lcfirst($name)] = $sItems->toArray(null, null, \BasePeer::TYPE_STUDLYPHPNAME) ?: null;
                        } else
                            $newRow[lcfirst($name)] = null;
                    }
                }
            }

            $result[] = $newRow;
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
        $item  = $query->findPk($this->getPropelPk($pPk));

        $this->mapValues($item, $pValues);

        return $item->save()?true:false;
    }


    public function mapValues($pItem, $pValues){

        $query = $this->getQueryClass();

        foreach ($pValues as $fieldName => $fieldValue){

            $field = $this->getField($fieldName);

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

            if ($column = $this->tableMap->getColumn($fieldName)){
                if (!$column[0])
                    throw new \FieldNotFoundException(tf('Field %s in object %s not found', $fieldName, $this->objectKey));

                $set = 'set'.$column[0]->getPhpName();

                $pItem->$set($fieldValue);
            }


        }

    }
    /**
     * {@inheritdoc}
     */
    public function getCount($pCondition = false){

        $pQuery = $this->getQueryClass();

        if ($pCondition){
            $where = dbConditionToSql($pCondition);
            $pQuery->where($where);
        }

        return $pQuery->count();
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
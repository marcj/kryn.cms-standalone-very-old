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
    public $object_key = '';

    /**
     * Constructor
     *
     * @param string $pObjectKey
     * @param array  $pDefinition
     */
    function __construct($pObjectKey, $pDefinition){
        $this->object_key = $pObjectKey;
        $this->definition = $pDefinition;
        $peer = $this->getPeerName();

        //cache primaryKey fields
        if (is_array($this->definition['fields'])){
            foreach ($this->definition['fields'] as $key => $field){
                if ($field['primaryKey'])
                    $this->primaryKeys[] = $peer::translateFieldName($key, \BasePeer::TYPE_FIELDNAME, \BasePeer::TYPE_PHPNAME);
            }
        }
    }


    /**
     * Returns primary key list in propel format. (PHPNAMES)
     * 
     * @param  string $pObjectKey
     * @return array  array(key1, key2, key3, ...)
     */
    public function getPrimaryList($pObjectKey){

        $peer = Kryn::$objects[$pObjectKey]['phpClass'].'Peer';

        //cache primaryKey fields
        if ($fields = Kryn::$objects[$pObjectKey]['fields']){
            foreach ($fields as $key => $field){
                if ($field['primaryKey'])
                    $result[] = $peer::translateFieldName($key, \BasePeer::TYPE_FIELDNAME, \BasePeer::TYPE_PHPNAME);
            }
        }
        return $result;
    }


    /**
     * Filters $pFields by allowed fields.
     * If '*' we return all allowed fields.
     *
     * @param array|string $pFields
     * @return array
     */
    public function getFields($pFields){

        $fields = $pFields;
        $peer = $this->getPeerName();

        if ($pFields === '*'){
            $fields = array();
            if ($this->definition['limitSelection']){
                $fields = $this->definition['limitSelection'];
            } else {
                foreach ($this->definition['fields'] as $key => $field){
                    //try {
                        $fields[] = $key; //$peer::translateFieldName($key, \BasePeer::TYPE_FIELDNAME, \BasePeer::TYPE_PHPNAME);
                    //} catch(\PropelException $e){
                    //    $fields[] = $key;
                    //}
                }

                return $fields;
            }
        } else if (is_array($pFields)){
            $fields = $pFields;
        }

        if (is_string($fields)){
            $fields = explode(',', str_replace(' ', '', trim($fields)));
        }

        $fields = array_unique(is_array($fields)?array_merge($this->primaryKeys, $fields):$this->primaryKeys);

        if ($this->definition['limitSelection']){

            $allowedFields = strtolower(','.str_replace(' ', '', trim($this->definition['limitSelection'])).',');

            $filteredFields = array();
            foreach ($fields as $idx => $name){
                if (strpos($allowedFields, strtolower(','.$name.',')) !== false){
                    $filteredFields[] = $name;
                }
            }
            return $filteredFields;
        } else return $fields;

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
     * Returns PHPclass name.
     *
     * @param string $pName
     * @return string
     */
    public function getPhpName($pName = null){
        return $pName ? Kryn::$objects[$pName]['phpClass'] : $this->definition['phpClass'];
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
     * 
     * 
     * @param bool|array   $pCondition
     * @param bool|array   $pOptions
     * @see  getItems
     * @return mixed
     *
     * @throws \ObjectItemNotFoundException
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
    public function getItems($pCondition = null, $pOptions = null){

        $query = $this->getQueryClass();

        $fields = $this->getFields($pOptions['fields']);

        $this->selectPrimary($query);
        $this->mapSelect($query, $fields);

        $this->mapOptions($query, $pOptions);

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
                if ($column = $pQuery->getColumnFromName(strtoupper($field))){
                    if (!$column[0])
                        throw new \FieldNotFoundException(tf('Field %s in object %s not found', $field, $this->object_key));
                    
                    $pQuery->orderBy($column[1], $direction);
                }
            }
        }

    }

    /**
     * {@inheritDoc}
     */
    public function getRelatedItems($pConditon = null, $pRelatedObject, $pRelatedPk, $pOptions = array()){

        $query = $this->getQueryClass($pRelatedObject);

        //convert kryn pk to propel pk
        $pRelatedPk = array_values($pRelatedPk);
        if (count($pRelatedPk) == 1) $pRelatedPk = $pRelatedPk[0];

        $relatedItem = $query->findPk($pRelatedPk);

        if (!$relatedItem) return false;

        $query = $this->getQueryClass();
        $query->filterByGroup($relatedItem);

        $fields = $this->getFields($pOptions['fields']);

        $this->selectPrimary($query);
        $this->mapSelect($query, $fields);
        $this->mapOptions($query, $pOptions);

        $stm = $this->getStm($query, $pConditon);
        return dbFetchAll($stm);

        $stm = $this->getStm($query, $pRelatedCondition);
        $thisItem = dbFetch($stm);

        $fields = $this->getFields($pOptions['fields']);

        $this->mapSelect($query, $fields);

  //->joinAuthor()
  //->addJoinCondition('Author', 'Author.LastName <> ?', 'Austen')

        $rel =  $this->getPhpName().'.'.ucfirst($pRelatedObject);
        print $rel;
        $query->join($rel);

        $stm = $this->getStm($query, $pCondition);

        return dbFetchAll($stm);
    }

    public function getStm($pQuery, $pCondition){

        //we have a condition, so extract the SQL and append our custom condition object
        $params = array();

        $id = (hexdec(uniqid())/mt_rand())+mt_rand();

        $con = \Propel::getConnection($pQuery->getDbName());
        
        if ($pCondition){
            $pQuery->where($id.' != '.$id);
        }

        try {
            $pQuery->PreSelect($con);
        } catch (\PropelException $e){}

        if (!$pQuery->hasSelectQueries()) {
            $pQuery->setPrimaryTableName(constant($this->getPeerName() . '::TABLE_NAME'));
        }

        $params = array();

        $db = \Propel::getDB($pQuery->getDbName());
        $dbMap = \Propel::getDatabaseMap($pQuery->getDbName());

        $sql = \BasePeer::createSelectSql($pQuery, $params);

        if ($pCondition){
            $data = $params;
            $condition = dbConditionToSql($pCondition, $data, $pQuery->getPrimaryTableName());
            $sql = str_replace($id.' != '.$id, $condition, $sql);
        }

        $stmt = $con->prepare($sql);

        $db->bindValues($stmt, $params, $dbMap);

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


    public function selectPrimary($pQuery){
        foreach ($this->primaryKeys as $columnName){
            $column = $pQuery->getColumnFromName($columnName);
            // always put quotes around the columnName to be safe, we strip them in the formatter
            $pQuery->addAsColumn('"' . lcfirst($columnName) . '"', $column[1]);
        }
    }
    /**
     * Maps fields to SELECT. May add foreign tables, if a fields is a object.
     * 
     * @param  [type] $pQuery  [description]
     * @param  [type] $pFields [description]
     * @return [type]          [description]
     */
    public function mapSelect($pQuery, $pFields, $pObjectName = null){

        $peer = $this->getPeerName($pObjectName);

        foreach ($pFields as $fieldKey){
            $field = $this->getField($fieldKey, $pObjectName);

            if ($field['type'] == 'object'){
                if ($field['objectRelation'] == 'nToM'){

                    $foreignObject =& Kryn::$objects[$field['object']];

                    if ($foreignObject['dataModel'] == 'propel'){

                        $primaryList = $this->getPrimaryList($field['object']);

                        $label = $field['objectLabel'] ? $field['objectLabel'] : $foreignObject['objectLabel'];

                        $pQuery->leftJoin($this->getPhpName().'.'.$field['objectRelationPhpName']);
                        $pQuery->leftJoin($field['objectRelationPhpName'].'.'.$foreignObject['phpClass']);

                        if (count($primaryList) == 1){
                                $key = current($primaryList);

                                if (Kryn::$config['db_type'] != 'pgsql'){
                                    $pQuery->withColumn('group_concat('.$foreignObject['phpClass'].'.'.$key.')', $fieldKey);
                                    if ($label)
                                        $pQuery->withColumn('group_concat('.$foreignObject['phpClass'].'.'.$label.')', $fieldKey.'Label');
                                } else {
                                    $pQuery->withColumn('string_agg('.$foreignObject['phpClass'].'.'.$key.'||\'\', \',\')', $fieldKey);
                                    if ($label)
                                        $pQuery->withColumn('string_agg('.$foreignObject['phpClass'].'.'.$label.'||\'\', \',\')', $fieldKey.'Label');
                                }
                        } else {
                            foreach ($primaryList as $pField){
                                if (Kryn::$config['db_type'] != 'pgsql'){
                                    $pQuery->withColumn('group_concat('.$foreignObject['phpClass'].'.'.$pField.')', $fieldKey.ucfirst($pField));
                                } else {
                                    $pQuery->withColumn('string_agg('.$foreignObject['phpClass'].'.'.$pField.'||\'\', \',\')', $fieldKey.ucfirst($pField));
                                }
                            }

                            if (Kryn::$config['db_type'] != 'pgsql'){
                                if ($label)
                                    $pQuery->withColumn('group_concat('.$foreignObject['phpClass'].'.'.$label.')', $fieldKey.'Label');
                            } else {
                                if ($label)
                                    $pQuery->withColumn('string_agg('.$foreignObject['phpClass'].'.'.$label.'||\'\', \',\')', $fieldKey.'Label');
                            }
                        }
                        $pQuery->groupBy('Id');
                    }
                }
            } else {
                
                if ($column = $pQuery->getColumnFromName(strtoupper($fieldKey))){
                    if (!$column[0])
                        throw new \FieldNotFoundException(tf('Field %s in object %s not found', $fieldKey, $this->object_key));

                    //always put quotes around the columnName to be safe, we strip them in the formatter
                    $pQuery->addAsColumn('"' . lcfirst($column[0]->getPhpName()) . '"', $column[1]);
                }

            }
        }
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
        $obj->fromArray($pValues);

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

        $obj->save();

        return $obj->getPrimaryKey();
    }


    /**
     * {@inheritdoc}
     */
    public function update($pPk, $pValues){
        
        $query = $this->getQueryClass();
        $item  = $query->findPk($this->getPropelPk($pPk));

        $item->fromArray($pValues);
        return $item->save();
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
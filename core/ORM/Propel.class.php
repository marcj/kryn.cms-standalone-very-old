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
                    try {
                        $fields[] = $peer::translateFieldName($key, \BasePeer::TYPE_FIELDNAME, \BasePeer::TYPE_PHPNAME);
                    } catch(\PropelException $e){
                        $fields[] = $key;
                    }
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
     * @return Object The query class object.
     */
    public function getQueryClass(){
        $objectKey = $this->getPhpName();

        $clazz = '\\'.ucfirst($objectKey).'Query';
        if (!class_exists($clazz)){
            throw new \ObjectNotFoundException(tf('The object query %s of %s does not exist.', $clazz, $objectKey));
        }

        return $clazz::create();
    }

    /**
     * Returns the peer name.
     *
     * @return string
     */
    public function getPeerName(){
        $objectKey = $this->getPhpName();

        $clazz = '\\'.ucfirst($objectKey).'Peer';
        if (!class_exists($clazz)){
            throw new \ObjectNotFoundException(tf('The object query %s of %s does not exist.', $clazz, $objectKey));
        }

        return $clazz;
    }


    /**
     * Returns PHPclass name.
     * @return string
     */
    public function getPhpName(){
        return $this->definition['phpClass'];
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
     * @param $pPk
     * @param array $pFields
     * @param string $pResolveForeignValues
     *
     * @return mixed
     *
     * @throws \ObjectItemNotFoundException
     */
    public function getItem($pPk, $pFields = array(), $pResolveForeignValues = '*'){

        $pQuery = $this->getQueryClass();

        $pPk = $this->getPropelPk($pPk);
        $item = $pQuery->select($pFields)->findPk($pPk);

        if (!$item){
            throw new \ObjectItemNotFoundException(tf("The object '%s' in '%s' can not be found.", $pPk, $this->object_key));
        }

        return $item;
    }

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
     *                      array('field' => 'category', 'direction' => 'asc'),
     *                      array('field' => 'title',    'direction' => 'asc')
     *                    )
     *
     *  'foreignKeys'     Defines which column should be resolved. If empty all columns will be resolved.
     *                    Use a array or a comma separated list (like in SQL SELECT). 'field1, field2, field3'
     *
     *  'permissionCheck' Defines whether we check against the ACL or not. true or false. default false
     *
     *
     *
     * @param bool|array   $pCondition
     * @param bool|array   $pOptions
     *
     * @return mixed
     *
     */
    public function getItems($pCondition, $pOptions = array()){

        $pQuery = $this->getQueryClass();

        $fields = $this->getFields($pOptions['fields']);

        if ($pCondition){
            $where = dbConditionToSql($pCondition);
            $pQuery->where($where);
        }

        $select = array();

        $this->mapSelect($pQuery, $fields);

        $items = $pQuery->find()->toArray();

        return $items;
    }

    /**
     * Maps fields to SELECT. May add foreign tables, if a fields is a object.
     * 
     * @param  [type] $pQuery  [description]
     * @param  [type] $pFields [description]
     * @return [type]          [description]
     */
    public function mapSelect($pQuery, $pFields){

        $peer = $this->getPeerName();
        $pQuery->clearSelectColumns();
        $pQuery->select($this->primaryKeys);

        foreach ($pFields as $fieldKey){
            $field = $this->getField($fieldKey);

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
                
                //$select[] = $fieldKey;
                if (strpos($fieldKey, '.') !== false){
                    //$pQuery->withColumn($fieldKey);
                } else {
                    $pQuery->withColumn($this->getPhpName().'.'.$fieldKey, lcfirst($fieldKey));
                }
                //$select[] = $fieldKey;

                // try {
                //     $select[] = $peer::translateFieldName($fieldKey, \BasePeer::TYPE_FIELDNAME, \BasePeer::TYPE_PHPNAME);
                // } catch (\PropelException $e){
                //     $select[] = $fieldKey;
                // }
            }
        }

        //$pQuery->select($select);
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
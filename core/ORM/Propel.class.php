<?php

namespace Core\ORM;

use \Core\Kryn;

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

        //cache primaryKey fields
        if (is_array($this->definition['fields'])){
            foreach ($this->definition['fields'] as $key => $field){
                if ($field['primaryKey'])
                    $this->primaryKeys[] = $key;
            }
        }
    }


    /**
     * Filters $pFields by allowed fields.
     * If '*' we return all allowed fields.
     *
     * @param array|string $pFields
     * @return array
     */
    public function getFields($pFields){

        $fields = array();

        if ($pFields === '*'){
            if ($this->definition['limitSelection']){
                $fields = $this->definition['limitSelection'];
            } else {
                foreach ($this->definition['fields'] as $key => $field){
                    $fields[] = $key;
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
     * @param string $pObject
     * @return Object The query class object.
     */
    public static function getQueryClass($pObject){

        $clazz = '\\'.ucfirst($pObject).'Query';
        if (!class_exists($clazz)){
            throw new \ObjectNotFoundException(tf('The object query %s of %s does not exist.', $clazz, $pObject));
        }

        return $clazz::create();
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

        $qClazz = self::getQueryClass($this->object_key);

        //since the core provide the pk as array('id' => 123) and not as array(123) we have to convert it
        //for propel orm.
        $pPk = array_values($pPk);
        if (count($pPk) == 1) $pPk = $pPk[0];

        $item = $qClazz->select($pFields)->findPk($pPk);

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

        $qClazz = self::getQueryClass($this->object_key);

        $fields = $this->getFields($pOptions['fields']);

        if ($pCondition){
            $where = dbConditionToSql($pCondition);
            $qClazz->where($where);
        }

        $select = array();

        foreach ($fields as $fieldKey){
            $field = $this->getField($fieldKey);

            if ($field['type'] == 'object'){
                if ($field['objectRelation'] == 'nToM'){

                    $foreignObject =& Kryn::$objects[$field['object']];

                    if ($foreignObject['dataModel'] == 'propel'){

                        $qClazz->leftJoin($field['objectRelationPhpName']);
                        $primaryList = Object::getPrimaryList($field['object']);

                        foreach ($primaryList as $pField){
                            $qClazz->addJoin(\UserGroupPeer::GROUP_ID, \GroupPeer::ID);

                            if (Kryn::$config['db_type'] == 'mysql'){
                                $qClazz->withColumn('group_concat('.\GroupPeer::ID.')', 'groups');
                                $qClazz->withColumn('group_concat('.\GroupPeer::NAME.')', 'groupsLabel');
                                $qClazz->groupBy('Id');
                            } else {
                                //$qClazz->withColumn('string_agg('.\GroupPeer::NAME.', \',\')', 'groups')
                            }
                        }
                    }
                }
            } else {
                $select[] = $fieldKey;
            }
        }

        $qClazz->select($select);

        $items = $qClazz->find()->toArray();

        return $items;
    }

    /**
     * @param $pPrimaryValues
     *
     */
    public function remove($pPrimaryValues){
        // TODO: Implement remove() method.
    }

    /**
     * @param $pValues
     * @param $pParentValues
     * @param $pMode
     * @param $pParentObjectKey
     *
     * @return inserted primary key. (last_insert_id() for SQL backend)
     */
    public function add($pValues, $pParentValues = false, $pMode = 'into', $pParentObjectKey = false){
        // TODO: Implement add() method.
    }

    /**
     * Updates an object
     *
     * @param $pPrimaryValues
     * @param $pValues
     */
    public function update($pPrimaryValues, $pValues){
        // TODO: Implement update() method.
    }

    /**
     * @param bool|string $pCondition
     *
     * @return int
     */
    public function getCount($pCondition = false){
        // TODO: Implement getCount() method.
    }

    public function getBranch($pParent = false, $pCondition = false, $pDepth = 1, $pScopeId = false,
        $pOptions = false){

        // TODO: Implement getTree() method.
        // 
    }


}
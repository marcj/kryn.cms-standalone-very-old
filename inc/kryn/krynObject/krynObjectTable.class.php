<?php


class krynObjectTable {

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
     * @param array  $pDefinition
     * @param string $pObjectKey
     */
    function __construct($pDefinition, $pObjectKey){
        $this->definition = $pDefinition;
        $this->object_key = $pObjectKey;
    }

    /**
     * @param $pId
     * @param string $pFields
     * @param bool|string $pResolveForeignValues
     * @return type
     */
    public function getItem($pId, $pFields = '*', $pResolveForeignValues = '*'){


        return $this->_getItems($pId, $pFields, $pResolveForeignValues, false, false, '', true);
    }

    /**
     * @param bool $pId
     * @param string $pFields
     * @param string $pResolveForeignValues
     * @param bool $pOffset
     * @param bool $pLimit
     * @param string $pCondition
     * @param bool $pSingleRow
     * @param string $pOrderBy
     * @param string $pOrderDirection
     * @return array
     */
    private function _getItems($pId = false, $pFields = '*', $pResolveForeignValues = '*', $pOffset = false, $pLimit = false,
                              $pCondition = '', $pSingleRow = false, $pOrderBy = '', $pOrderDirection = 'asc'){

        $options = database::getOptions($this->definition['table']);
        $where  = '1=1 ';

        $aFields = explode(',', $pFields);

        $select = array();
        $joins = array();
        $primaryField = '';

        $foreignColumns = explode(',', str_replace(' ', '', trim($pResolveForeignValues)));

        foreach ($this->definition['fields'] as $key => &$field){

            if (($pResolveForeignValues == '*' || in_array($key, $foreignColumns)) &&
                ($pFields == '*' || in_array($key, $aFields))
               ){

                if ($field['primaryKey'] && $pId){
                    $where .= ' AND '.$this->object_key.'.'.$key.' = ';
                    if ($options[$key]['escape'] == 'int')
                        $where .= (is_array($pId)?$pId[$key]+0:$pId+0);
                    else
                        $where .= "'".esc((is_array($pId)?$pId[$key]:$pId))."'";
                }


                if ($field['type'] == 'object'){
                    $foreignObjectDefinition = kryn::$objects[$field['object']];
                    if (!$foreignObjectDefinition)
                        continue;

                    $oKey = $field['object_label_map']?$field['object_label_map']:$field['object'].'_'.$field['object_label'];
                    $oLabel = $field['object_label']?$field['object_label']:kryn::$objects[$field['object']]['object_label'];

                    $select[] = $field['object'].'.'.$oLabel.' AS '.$oKey;
                    $join = 'LEFT OUTER JOIN '.dbTableName($foreignObjectDefinition['table']).' as '.$field['object'].
                               ' ON ( 1=1';

                    //If we have multiple foreign keys
                    if ($field['foreign_key_map']){

                        //todo, test this stuff
                        foreach ($field['foreign_key_map'] as $primaryKey => $primaryForeignKey){
                            $join .= ' AND '.$field['object'].'.'.$primaryForeignKey.' = '.$this->object_key.'.'.$primaryKey;
                        }

                    } else {
                        //normal foreign key through one column
                        foreach ($foreignObjectDefinition['fields'] as $tempKey => $tempField){
                            if ($tempField['primaryKey']) {
                                $primaryField = $tempKey;
                                break;
                            }
                        }
                        $join .= ' AND '.$field['object'].'.'.$primaryField.' = '.$this->object_key.'.'.$key;
                    }

                    $join .= ')';

                    $joins[] = $join;
                }

            }

        }

        if ($pFields == '*'){
            $sql = 'SELECT '.$this->object_key.'.*';
        } else {
            $selects = explode(',', $pFields);
            foreach ($selects as &$col ){
                $col = $this->object_key.'.'.$col;
            }
            $sql = 'SELECT '.implode(', ', $selects);

        }

        if (count($select)>0)
            $sql .= ','.implode(',', $select);


        $table = dbTableName($this->definition['table']);
        $sql .= ' FROM '.$table.' as '.$this->object_key;

        if (count($joins)>0){
            $sql .= ' '.implode(" \n",$joins);
        }

        if ($pCondition)
            $where .= ' AND '.$pCondition;

        $sql .= ' WHERE '.$where;

        if ($pOrderBy){
            $direction = 'ASC';

            if (strtolower($pOrderDirection) == 'desc')
                $direction = 'DESC';

            if (strpos($pOrderBy, ' ') === false) {
                $sql .= ' ORDER BY '.$pOrderBy.' '.$direction;
            }
        }


        if ($pOffset > 0)
            $sql .= ' OFFSET '.($pOffset+0);

        if ($pLimit > 0)
            $sql .= ' LIMIT '.($pLimit+0);

        print $sql."#\n";

        if ($pSingleRow){
            $item = dbExfetch($sql, 1);
            self::parseValues($item);
            return $item;
        } else {
            $items = dbExfetch($sql, -1);
            if (count($items)>0){
                foreach ($items as &$item){
                    self::parseValues($item);
                }
                return $items;
            }
        }

    }

    public function parseValues(&$pItem){

        foreach ($pItem as $key => &$value ){

            if ($this->definition['fields'][$key]){
                if (strtolower($this->definition['fields'][$key]['type']) == 'layoutelement'){
                    if (substr($value, 0, 13) == '{"template":"'){
                        $value = krynObject::parseLayoutElement($value);
                    }
                }
            }
        }
    }

    /**
     * @param int $pOffset
     * @param int $pLimit
     * @param bool $pCondition
     * @param string $pFields
     * @param string $pResolveForeignValues
     * @param string $pOrderBy
     * @param string $pOrderDirection
     * @return array
     */
    public function getItems ($pOffset = 0, $pLimit = 0, $pCondition = false, $pFields = '*',
                              $pResolveForeignValues = '*', $pOrderBy = '', $pOrderDirection = 'asc'){

        return $this->_getItems(false, $pFields, $pResolveForeignValues, $pOffset, $pLimit, $pCondition, false,
                                $pOrderBy, $pOrderDirection);
    }


    public function getCount($pCondition = false){

        //todo, handle pCondition

        return dbCount($this->definition['table']);


    }
}

?>
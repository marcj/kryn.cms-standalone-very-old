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

        if (substr($pFields, -1) == ',')
            $pFields = substr($pFields, 0, -1);

        $aFields = explode(',', $pFields);

        $select = array(); //columns
        $fSelect = array(); //final selects
        $groupedColumns = array();
        $joins = array();
        $primaryField = '';

        $grouped = false;

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

                    if ($field['object_relation'] != 'nToM'){
                        //n to 1

                        $select[] = $field['object'].'.'.$oLabel.' AS '.$oKey;
                        $join = 'LEFT OUTER JOIN '.dbTableName($foreignObjectDefinition['table']).' AS '.$field['object'].
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

                    } else {

                        //n to m
                        $fSelect[] = 'group_concat(CONCAT('.$field['object'].'.'.$oLabel.', \'s\')) AS '.$oKey;
                        $groupedColumns[$oKey] = true;

                        $join = 'LEFT OUTER JOIN '.dbTableName($field['object_relation_table']).' AS '.
                                $field['object_relation_table'].' ON (1=1 ';

                        foreach ($this->definition['fields'] as $tkey => &$tfield){
                            if ($tfield['primaryKey']){
                                $join .= ' AND '.$field['object_relation_table'];

                                if ($field['object_relation_table_left'])
                                    $join .= '.'.$field['object_relation_table_left'].' = ';
                                else
                                    $join .= '.'.$this->object_key.'_'.$tkey.' = ';

                                $join .= $this->object_key.'.'.$tkey;
                            }

                        }
                        $join .= ')';
                        $joins[] = $join;

                        $join = 'LEFT OUTER JOIN '.dbTableName($foreignObjectDefinition['table']).' AS '.
                                $field['object'].' ON (1=1 ';

                        $primaryFields = array();

                        foreach ($foreignObjectDefinition['fields'] as $tkey => &$tfield){
                            if ($tfield['primaryKey']){
                                $join .= ' AND '.$field['object_relation_table'];

                                if ($field['object_relation_table_right'])
                                    $join .= '.'.$field['object_relation_table_right'].' = ';
                                else
                                    $join .= '.'.$field['object'].'_'.$tkey.' = ';

                                $join .= $field['object'].'.'.$tkey;

                                if ($tfield['type'] == 'number')
                                    $primaryFields[$tkey] = $tfield;
                            }

                        }

                        if (count($primaryFields) == 1){
                            foreach ($primaryFields as $k => $f){
                                $fSelect[] = 'group_concat(CONCAT('.$field['object'].'.'.$k.')) AS '.$key;
                                $groupedColumns[$key] = true;
                            }
                        } else if(count($primaryFields) > 1){
                            foreach ($primaryFields as $k => $f){
                                $fSelect[] = 'group_concat(CONCAT('.$field['object'].'.'.$k.')) AS '.$key.'_'.$k;
                                $groupedColumns[$key.'_'.$k] = true;
                            }
                        }

                        $join .= ')';

                        $joins[] = $join;

                        $grouped = true;

                    }
                } else {
                    $select[] = $this->object_key.'.'.$key;
                }

            }

        }

        $sql = 'SELECT ';

        if (count($select)>0){

            if ($grouped){
                foreach ($select as &$sel){
                    $dotPos = strpos($sel, '.');
                    $sel = 'MAX('.$sel.') as '.substr($sel, $dotPos?$dotPos+1:0);
                }
            }

            $sql .= implode(', ', $select);
        }

        if (count($fSelect)>0){
            if (count($select)>0)
                $sql .= ', ';
            $sql .= implode(', ', $fSelect);
        }

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

        if ($grouped){
            $prim = array();
            foreach ($this->definition['fields'] as $key => &$field){
                if ($field['primaryKey']){
                    $prim[] = $this->object_key.'.'.$key;
                }
            }
            $sql .= ' GROUP BY '.implode(',', $prim);
        }

        if ($pOffset > 0)
            $sql .= ' OFFSET '.($pOffset+0);

        if ($pLimit > 0)
            $sql .= ' LIMIT '.($pLimit+0);

        if ($pSingleRow){
            $item = dbExfetch($sql, 1);

            if (dbError()) throw new Exception(dbError());


            self::parseValues($item);
            if (kryn::$config['db_type'] == 'postgresql')
                foreach ($groupedColumns as $col => $b)
                    if (substr($item[$col], 0, -1) == ',')
                        $item[$col] = substr($item[$col], -1);

            return $item;
        } else {
            $res = dbExec($sql);

            if (dbError()) throw new Exception(dbError());

            $c = count($groupedColumns);

            while ($row = dbFetch($res)){
                self::parseValues($row);
                if ($c > 0 && kryn::$config['db_type'] == 'postgresql')
                    foreach ($groupedColumns as $col => $b)
                        if (substr($row[$col], 0, -1) == ',')
                            $row[$col] = substr($row[$col], 0, -1);
                $items[] = $row;
            }
            return $items;
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
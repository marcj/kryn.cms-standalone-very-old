<?php


class krynObjectTable extends krynObjectAbstract {


    public function getItem($pPrimaryValues, $pFields = '*', $pResolveForeignValues = '*'){

        return $this->_getItems($pPrimaryValues, $pFields, $pResolveForeignValues, false, false, '', true, null, null);
    }

    public function getItems ($pPrimaryValues, $pOffset = 0, $pLimit = 0, $pCondition = false, $pFields = '*',
                              $pResolveForeignValues = '*', $pOrder){

        return $this->_getItems($pPrimaryValues, $pFields, $pResolveForeignValues, $pOffset, $pLimit, $pCondition, false,
            $pOrder);
    }

    public function getCount($pCondition = false){
        return dbCount($this->definition['table'], $pCondition);
    }

    public function removeItem($pPrimaryValues){
        return dbDelete($this->definition['table'], $pPrimaryValues);
    }

    public function addItem($pValues){


        //TODO

        return dbInsert($this->definition['table'], $pValues);

    }

    public function updateItem($pPrimaryValues, $pValues){

        //TODO

        return dbUpdate($this->definition['table'], $pPrimaryValues, $pValues);
    }

    private function _getItems($pPrimaryIds = false, $pFields = '*', $pResolveForeignValues = '*', $pOffset = false, $pLimit = false,
                              $pCondition = '', $pSingleRow = false, $pOrderBy = '', $pOrderDirection = 'asc'){

        $where  = '1=1 ';

        $aFields = $pFields;

        if (!is_array($pFields)){
            if (substr($pFields, -1) == ',')
                $pFields = substr($pFields, 0, -1);

            $aFields = explode(',', $pFields);
        }

        $aResolveForeignValues = $pResolveForeignValues;

        if (!is_array($pResolveForeignValues)){
            if (substr($pResolveForeignValues, -1) == ',')
                $pResolveForeignValues = substr($pResolveForeignValues, 0, -1);

            $aResolveForeignValues = explode(',', $pResolveForeignValues);
        }

        $additionalCondition = false;
        if ($this->definition['tableCondition'])
            $additionalCondition = krynObjectAbstract::conditionArrayToSql($this->definition['tableCondition'], $this->object_key);

        $select = array(); //columns
        $fSelect = array(); //final selects
        $groupedColumns = array();
        $joins = array();
        $primaryField = '';
        $firstPrimaryField = '';

        $grouped = false;

        $foreignColumns = explode(',', str_replace(' ', '', trim($pResolveForeignValues)));

        foreach ($this->definition['fields'] as $key => &$field){

            if ($field['primaryKey'] && !$firstPrimaryField){
                $firstPrimaryField = $key;
            }

            if (($pResolveForeignValues == '*' || in_array($key, $foreignColumns)) &&
                ($pFields == '*' || in_array($key, $aFields))
               ){


                if ($field['type'] == 'object'){

                    $foreignObjectDefinition =& kryn::$objects[$field['object']];
                    if (!$foreignObjectDefinition)
                        continue;

                    $relPrimaryFields = krynObject::getPrimaries($field['object']);

                    $oKey = $key.'_'.$field['object_label'];
                    $oLabel = $field['object_label']?$field['object_label']:kryn::$objects[$field['object']]['object_label'];

                    if ($field['object_relation'] != 'nToM'){
                        //n to 1

                        $select[] = dbQuote($field['object']).'.'.dbQuote($oLabel).' AS '.dbQuote($oKey);

                        $join = 'LEFT OUTER JOIN '.dbQuote(dbTableName($foreignObjectDefinition['table'])).' AS '.dbQuote($field['object']).
                                   ' ON ( 1=1';

                        //If we have multiple foreign keys
                        if (count($relPrimaryFields) > 1){

                            foreach ($relPrimaryFields as $primaryKey => $primaryForeignKey){
                                $join .= ' AND '.dbQuote($this->object_key).'.'.dbQuote($key.'_'.$primaryKey).' = '.
                                         dbQuote($field['object']).'.'.dbQuote($primaryKey);
                            }

                        } else {

                            $select[] = dbQuote($this->object_key).'.'.dbQuote($key);

                            //normal foreign key through one column
                            foreach ($foreignObjectDefinition['fields'] as $tempKey => $tempField){
                                if ($tempField['primaryKey']) {
                                    $primaryField = $tempKey;
                                    break;
                                }
                            }
                            $join .= ' AND '.dbQuote($field['object']).'.'.dbQuote($primaryField).' = '.
                                     dbQuote($this->object_key).'.'.dbQuote($key);
                        }

                        $join .= ')';

                        $joins[] = $join;

                    } else {

                        //n to m
                        if (kryn::$config['db_type'] == 'postgresql')
                            $fSelect[] = 'string_agg('.dbQuote($field['object']).'.'.dbQuote($oLabel).'||\'\', \',\') AS '.dbQuote($oKey);
                        else
                            $fSelect[] = 'group_concat('.dbQuote($field['object']).'.'.dbQuote($oLabel).') AS '.dbQuote($oKey);

                        $groupedColumns[$oKey] = true;

                        $relTableNamePre = 'relation_'.$this->object_key.'_'.$field['object'];
                        $relTableName = $field['object_relation_table']?$field['object_relation_table']:$relTableNamePre;

                        $join = 'LEFT OUTER JOIN '.dbQuote(dbTableName($relTableName)).' AS '.
                                dbQuote($relTableNamePre).' ON (1=1 ';

                        foreach ($relPrimaryFields as $tkey => &$tfield){
                            $join .= ' AND '.dbQuote($relTableNamePre);

                            $join .= '.'.dbQuote($this->object_key.'_'.$tkey).' = ';

                            $join .= dbQuote($this->object_key).'.'.dbQuote($tkey);
                        }

                        $join .= ')';
                        $joins[] = $join;

                        $join = 'LEFT OUTER JOIN '.dbQuote(dbTableName($foreignObjectDefinition['table'])).' AS '.
                                dbQuote($field['object']).' ON (1=1 ';

                        $primaryFields = array();

                        foreach ($relPrimaryFields as $tkey => &$tfield){
                            $join .= ' AND '.dbQuote($relTableNamePre);

                            $join .= '.'.dbQuote($field['object'].'_'.$tkey).' = ';

                            $join .= dbQuote($field['object']).'.'.dbQuote($tkey);

                            if ($tfield['type'] == 'number')
                                $primaryFields[$tkey] = $tfield;

                        }

                        if (count($primaryFields) == 1){
                            foreach ($primaryFields as $k => $f){

                                if (kryn::$config['db_type'] == 'postgresql')
                                    $fSelect[] = 'string_agg('.dbQuote($field['object']).'.'.dbQuote($k).'||\'\', \',\') AS '.dbQuote($key);
                                else
                                    $fSelect[] = 'group_concat('.dbQuote($field['object']).'.'.($k).') AS '.dbQuote($key);

                                $groupedColumns[$key] = true;
                            }
                        } else if(count($primaryFields) > 1){
                            foreach ($primaryFields as $k => $f){

                                if (kryn::$config['db_type'] == 'postgresql')
                                    $fSelect[] = 'string_agg('.dbQuote($field['object']).'.'.dbQuote($k).'||\'\', \',\') AS '.dbQuote($key.'_'.$k);
                                else
                                    $fSelect[] = 'group_concat('.dbQuote($field['object']).'.'.dbQuote($k).') AS '.dbQuote($key.'_'.$k);

                                $groupedColumns[$key.'_'.$k] = true;
                            }
                        }

                        $join .= ')';

                        $joins[] = $join;

                        $grouped = true;

                    }
                } else {
                    $select[] = dbQuote($this->object_key).'.'.dbQuote($key);
                }

            }

        }

        $sql = 'SELECT '.chr(13);

        if (count($select)>0){

            if ($grouped){
                foreach ($select as &$sel){
                    $dotPos = strpos($sel, '.');
                    $sel = 'MAX('.$sel.') AS '.substr($sel, $dotPos?$dotPos+1:0);
                }
            }

            $sql .= implode(", \n", $select);
        }

        if (count($fSelect)>0){
            if (count($select)>0)
                $sql .= ', ';
            $sql .= "\n".implode(", \n", $fSelect);
        }

        $table = dbTableName($this->definition['table']);
        $sql .= " \nFROM ".dbQuote($table).' AS '.dbQuote($this->object_key);

        if (count($joins)>0){
            $sql .= " \n".implode(" \n", $joins);
        }

        $primaryCondition = $this->primaryArrayToSql($pPrimaryIds);

        if ($primaryCondition)
            $where .= ' AND '.$primaryCondition;

        if ($pCondition)
            $where .= ' AND '.$pCondition;

        if ($additionalCondition)
            $where .= ' AND '.$additionalCondition;

        $sql .= " \nWHERE ".$where;


        if ($pOrderBy){
            $direction = 'ASC';

            if (strtolower($pOrderDirection) == 'desc')
                $direction = 'DESC';

            if (strpos($pOrderBy, ' ') === false) {
                $sql .= ' ORDER BY '.dbQuote($pOrderBy).' '.$direction;
            }
        }

        if ($grouped){
            $prim = array();
            foreach ($this->definition['fields'] as $key => &$field){
                if ($field['primaryKey']){
                    $prim[] = dbQuote($this->object_key).'.'.dbQuote($key);
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

            if (kryn::$config['db_type'] == 'postgresql')
                foreach ($groupedColumns as $col => $b)
                    if (substr($item[$col], 0, -1) == ',')
                        $item[$col] = substr($item[$col], -1);

            return $item;
        } else {
            $res = dbExec($sql);

            $c = count($groupedColumns);

            while ($row = dbFetch($res)){


                if ($c > 0 && kryn::$config['db_type'] == 'postgresql')
                    foreach ($groupedColumns as $col => $b)
                        if (substr($row[$col], 0, -1) == ',')
                            $row[$col] = substr($row[$col], 0, -1);
                $items[] = $row;
            }
            return $items;
        }

    }
}

?>
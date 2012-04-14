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

    /**
     * Converts the values array to the proper array for the table columns.
     *
     *
     * @param $pValues
     * @return array|bool
     */
    public function retrieveValues($pValues){

        $row = array();

        error_log(print_r($pValues, true));

        foreach ($this->definition['fields'] as $key => $field){
            if ($pValues[$key]){

                if ($field['type'] == 'object'){

                    $foreignObjectDefinition =& kryn::$objects[$field['object']];
                    if (!$foreignObjectDefinition){
                        return false;
                    }

                    $relPrimaryFields = krynObject::getPrimaries($field['object']);

                    list($object_key, $object_ids, $params) = krynObject::parseUrl($pValues[$key]);

                    if ($field['object_relation'] != 'nToM'){

                        //only one item in $object_ids

                        if(count($relPrimaryFields) == 1){
                            //target table has only one primary key, so we store $object_id in $key
                            $row[$key] = $object_ids[0][key($relPrimaryFields)];
                        } else {
                            //target table has multiple primary keys, so we have to store
                            //$object_ids in different columns

                            foreach ($relPrimaryFields as $rKey => $rField){
                                $row[$key.'_'.$rKey] = $object_ids[0][$rKey];
                            }

                        }

                    } else {

                        //multiple items in $object_ids
                        //save it in updateRelation()

                    }


                } else {
                    $row[$key] = $pValues[$key];
                }


            }
        }
        error_log(print_r($row, true));

        return $row;
    }

    private function getFieldAtPos($pPos){
        $pos = 1;
        foreach ($this->definition['fields'] as $key => $field){
            if ($pos == $pPos) return $key;
            $pos++;
        }
        return false;
    }

    public function parseError($e){

        //check for postgresql
        if (strpos($e, 'duplicate key value violates unique constraint') !== false){

            preg_match('/Key \(([^)]*)\)=\(.*\) already exists/', $e, $matches);
            $fields = explode(',', str_replace(' ', '', $matches[1]));
            return array('error' => 'duplicate_key', 'fields' => $fields);
        }

        //TODO, check for mysql
        if (preg_match("/Duplicate entry '.*' for key ([0-9]*)/", $e, $matches)){
            $field = $this->getFieldAtPos($matches[1]+0);
            return array('error' => 'duplicate_key', 'fields' => array($field));
        }

        return false;
    }

    public function addItem($pValues){

        $row = $this->retrieveValues($pValues);
        $primaries = array();

        try {
            $lastId = dbInsert($this->definition['table'], $row);

            foreach ($this->primaryKeys as $k => $f){
                if ($f['autoIncrement'])
                    $primaries[$k] = $lastId;
                else
                    $primaries[$k] = $row[$k];
            }
            $this->updateRelation($primaries, $pValues);

        } catch(Exception $e){
            $error = $this->parseError($e);
            return $error?$error:false;
        }

        return $lastId;

    }

    public function updateRelation($pPrimaryValues, $pValues){

        foreach ($pValues as $key => $value){

            if (($field = $this->definition['fields'][$key]) && $field['type'] == 'object' && $field['object_relation'] == 'nToM'){

                $relTableNamePre = 'relation_'.$this->object_key.'_'.$field['object'];
                $relTableName = $field['object_relation_table']?$field['object_relation_table']:$relTableNamePre;

                $primary = array();
                foreach ($pPrimaryValues as $key => $val){
                    $primary[$this->object_key.'_'.$key] = $val;
                }
                dbDelete($relTableName, dbPrimaryArrayToSql($primary));

                $primaryRight = array_keys(krynObject::getPrimaries($field['object']));

                foreach ($value as $objectValue){

                    if (count($primaryRight) == 1){

                        $primary[$field['object'].'_'.$primaryRight[0]] = $objectValue;
                    } else if(is_array($objectValue)){
                        foreach ($primaryRight as $k){
                            $primary[$field['object'].'_'.$k] = $objectValue[$k];
                        }
                    }

                    dbInsert($relTableName, $primary);

                }


            }

        }

    }

    public function updateItem($pPrimaryValues, $pValues){

        $row = $this->retrieveValues($pValues);

        try {
            dbUpdate($this->definition['table'], $pPrimaryValues, $row);

            $this->updateRelation($pPrimaryValues, $pValues);

        } catch(Exception $e){
            $error = $this->parseError($e);
            klog('objectTable', 'Error during updateItem('.$this->object_key.'): '.$e);
            return $error?$error:false;
        }

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

        if (!is_array($pResolveForeignValues) && $pResolveForeignValues != '*'){

            die($pResolveForeignValues);
            if (substr($pResolveForeignValues, -1) == ',')
                $pResolveForeignValues = substr($pResolveForeignValues, 0, -1);

            $aResolveForeignValues = explode(',', $pResolveForeignValues);
        }

        $additionalCondition = false;
        if ($this->definition['tableCondition'])
            $additionalCondition = dbConditionArrayToSql($this->definition['tableCondition'], $this->object_key);

        $select = array(); //columns
        $fSelect = array(); //final selects
        $joins = array();
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

                    if ($aResolveForeignValues == '*' || in_array($key, $aResolveForeignValues))
                        $this->getObjectResolveSql($this->object_key, $key, $field, $select, $fSelect, $joins, $grouped);

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

        $primaryCondition = dbPrimaryArrayToSql($pPrimaryIds, $this->object_key);

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

            return $item;
        } else {
            $res = dbExec($sql);

            while ($row = dbFetch($res)){

                $items[] = $row;
            }
            return $items;
        }

    }

    public function getObjectResolveSql($pLeftObject, $pKey, &$pField, &$select, &$fSelect, &$joins, &$grouped){

        $foreignObjectDefinition =& kryn::$objects[$pField['object']];
        if (!$foreignObjectDefinition){
            return false;
        }

        $relPrimaryFields = krynObject::getPrimaries($pField['object']);
        $primaryFields = krynObject::getPrimaries($pLeftObject);

        $oKey = $pKey.'_'.$pField['object_label'];
        $oLabel = $pField['object_label']?$pField['object_label']:kryn::$objects[$pField['object']]['object_label'];

        if ($pField['object_relation'] != 'nToM'){
            //n to 1

            $select[] = dbQuote($pField['object']).'.'.dbQuote($oLabel).' AS '.dbQuote($oKey);

            $join = 'LEFT OUTER JOIN '.dbQuote(dbTableName($foreignObjectDefinition['table'])).' AS '.dbQuote($pField['object']).
            ' ON ( 1=1';

            //If we have multiple foreign keys
            if (count($relPrimaryFields) > 1){

                foreach ($relPrimaryFields as $primaryKey => $primaryForeignKey){
                    $join .= ' AND '.dbQuote($this->object_key).'.'.dbQuote($pKey.'_'.$primaryKey).' = '.
                    dbQuote($pField['object']).'.'.dbQuote($primaryKey);
                }

            } else {

                $select[] = dbQuote($this->object_key).'.'.dbQuote($pKey);

                //normal foreign key through one column
                $primaryField = '';
                foreach ($relPrimaryFields as $tempKey => $tempField){
                    $primaryField = $tempKey;
                    break;
                }

                if ($primaryField)
                    $join .= ' AND '.dbQuote($pField['object']).'.'.dbQuote($primaryField).' = '.
                             dbQuote($this->object_key).'.'.dbQuote($pKey);
            }

            $join .= ')';

            $joins[] = $join;

        } else {

            //n to m
            if (kryn::$config['db_type'] == 'postgresql')
                $fSelect[] = 'string_agg('.dbQuote($pField['object']).'.'.dbQuote($oLabel).'||\'\', \',\') AS '.dbQuote($oKey);
            else
                $fSelect[] = 'group_concat('.dbQuote($pField['object']).'.'.dbQuote($oLabel).') AS '.dbQuote($oKey);

            $relTableNamePre = 'relation_'.$this->object_key.'_'.$pField['object'];
            $relTableName = $pField['object_relation_table']?$pField['object_relation_table']:$relTableNamePre;

            $join = 'LEFT OUTER JOIN '.dbQuote(dbTableName($relTableName)).' AS '.
                dbQuote($relTableNamePre).' ON (1=1 ';

            foreach ($primaryFields as $tkey => &$tfield){
                $join .= ' AND '.dbQuote($relTableNamePre);

                $join .= '.'.dbQuote($this->object_key.'_'.$tkey).' = ';

                $join .= dbQuote($this->object_key).'.'.dbQuote($tkey);
            }

            $join .= ')';
            $joins[] = $join;

            $join = 'LEFT OUTER JOIN '.dbQuote(dbTableName($foreignObjectDefinition['table'])).' AS '.
                    dbQuote($pField['object']).' ON (1=1 ';

            $primaryFields = array();

            foreach ($relPrimaryFields as $tkey => &$tfield){
                $join .= ' AND '.dbQuote($relTableNamePre);

                $join .= '.'.dbQuote($pField['object'].'_'.$tkey).' = ';

                $join .= dbQuote($pField['object']).'.'.dbQuote($tkey);

                if ($tfield['type'] == 'number')
                    $primaryFields[$tkey] = $tfield;

            }

            if (count($primaryFields) == 1){
                foreach ($primaryFields as $k => $f){

                    if (kryn::$config['db_type'] == 'postgresql')
                        $fSelect[] = 'string_agg('.dbQuote($pField['object']).'.'.dbQuote($k).'||\'\', \',\') AS '.dbQuote($pKey);
                    else
                        $fSelect[] = 'group_concat('.dbQuote($pField['object']).'.'.($k).') AS '.dbQuote($pKey);

                }
            } else if(count($primaryFields) > 1){
                foreach ($primaryFields as $k => $f){

                    if (kryn::$config['db_type'] == 'postgresql')
                        $fSelect[] = 'string_agg('.dbQuote($pField['object']).'.'.dbQuote($k).'||\'\', \',\') AS '.dbQuote($pKey.'_'.$k);
                    else
                        $fSelect[] = 'group_concat('.dbQuote($pField['object']).'.'.dbQuote($k).') AS '.dbQuote($pKey.'_'.$k);

                }
            }

            $join .= ')';

            $joins[] = $join;

            $grouped = true;
        }
    }

}
?>
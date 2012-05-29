<?php


class krynObjectTable extends krynObjectAbstract {


    public function getItem($pPrimaryValues, $pFields = '*', $pResolveForeignValues = '*'){

        return $this->_getItems($pPrimaryValues, $pFields, $pResolveForeignValues, false, false, true, null, null);
    }

    public function getItems ($pCondition, $pOffset = 0, $pLimit = 0, $pFields = '*',
                              $pResolveForeignValues = '*', $pOrder){

        return $this->_getItems($pCondition, $pFields, $pResolveForeignValues, $pOffset, $pLimit, false, $pOrder);
    }

    public function getCount($pCondition = false){
        return dbCount($this->definition['table'], $pCondition);
    }

    public function remove($pPrimaryValues){

        //todo, remove relations

        return dbDelete($this->definition['table'], $pPrimaryValues);
    }

    public function getParents($pPrimaryValues){

        if (!$this->definition['tableNested']){
            throw new Exception('Object is not marked as nested.');
        }

        if (!$this->definition['chooserBrowserTreeLabel']){
            throw new Exception('chooserBrowserTreeLabel in object not defined.');
        }
        $primKey = current($this->primaryKeys);
        $idValue = $pPrimaryValues[$primKey]+0;
        $id      = dbQuote('node').'.'.dbQuote($primKey);
        $icon  = $this->definition['chooserBrowserTreeIcon'];


        $title = $this->definition['chooserBrowserTreeLabel'];

        $selectId = $id.' AS '.$primKey;
        $selects[] = dbQuote($primKey, 'parent').' AS '.$primKey;
        $selects[] = dbQuote('parent').'.'.dbQuote($title).' AS '.dbQuote($title);
        if ($icon)
            $selects[] = dbQuote('node').'.'.dbQuote($icon).' AS '.dbQuote($icon);

        $selects = implode(",\n", $selects);


        $table = dbQuote(dbTableName($this->definition['table']));
        $tables[] = "$table as ".dbQuote('parent');
        $tables[] = "$table as ".dbQuote('node');
        $tablesDefault = implode(', ', $tables);

        $sql = "
            SELECT $selects
            FROM $tablesDefault
            WHERE
                node.lft BETWEEN parent.lft AND parent.rgt
                AND $id = $idValue
            ORDER BY node.lft";

        $result = array();
        $res = dbExec($sql);

        while ($row = dbFetch($res)){
            $result[ $row[$primKey] ] = $row;
        }

        return $result;
    }


    /**
     * If the object has nested mode enabled, we do move around it with this function.
     * Modifies lft and rgt field to the new sort
     *
     * @param $pSourcePrimaryValues
     * @param $pTargetPrimaryValues
     * @param $pMode 'over' | 'into' | 'below'
     * @param $pTargetObjectKey
     *
     * @return boolean
     */
    public function move($pSourcePrimaryValues, $pTargetPrimaryValues, $pMode, $pTargetObjectKey = false){

        $source = $this->getItem($pSourcePrimaryValues);

        $rootCondition = ' 1=1';
        if ($rField = $this->definition['chooserBrowserTreeRootObjectField']){
            $field = dbQuote($rField);
            $rootCondition = " $field = '".esc($source[$rField])."'";
        }

        if ($pTargetObjectKey && $pTargetObjectKey != $this->object_key && $rField){

            $rgt = dbQuote('rgt', 'parent');
            $table = dbQuote(dbTableName($this->definition['table']));
            $row = dbExFetch("SELECT $rgt FROM $table as parent WHERE $rootCondition ORDER BY $rgt DESC LIMIT 1", 1);

            $target = array( 'lft' => 0, 'rgt' => $row['rgt']+1 );

            $objectRow = krynObject::get($pTargetObjectKey, $pTargetPrimaryValues);
            $primaries = krynObject::getPrimaryList($pTargetObjectKey);
            $target[$rField] = $objectRow[$primaries[0]];

        } else {
            $target = $this->getItem($pTargetPrimaryValues);
        }

        if ($rField){
            $rValue = $target[$rField];
        }

        $modes = array('over', 'below', 'into');
        if (!in_array($pMode, $modes)) return false;

        if ($pMode == 'over' && $source['rgt']+1 == $target['lft']) return false;
        if ($pMode == 'below' && $source['lft']-1 == $target['rgt']) return false;
        if ($pMode == 'into' && $source['lft']-1 == $target['lft']) return false;
        if ($pMode == 'into' && $source['lft'] == $target['lft']) return false;

        //no move to his own children
        if ($source['lft'] < $target['lft'] && $source['rgt'] > $target['lft']) return false;

        $targetLeft  = $target['lft'];
        $targetRight = $target['rgt'];
        $sourceRight = $source['rgt'];
        $sourceLeft  = $source['lft'];
        $sourceWidth = $sourceRight-$sourceLeft;

        //quote some field names
        $table = dbTableName($this->definition['table']);
        $tableQuoted = dbQuote($table);
        $lft = dbQuote('lft');
        $rgt = dbQuote('rgt');


        //Step 1. Hide source by converting lft and rgt to negative values
        $transformSource = "
            UPDATE
                $tableQuoted
            SET
                lft = lft-$sourceRight,
                rgt = rgt-$sourceRight
            WHERE
                lft >= $sourceLeft AND rgt <= $sourceRight
                AND $rootCondition
            ";

        //Step 2. close hole
        $closeHole = "
            UPDATE
                $tableQuoted
            SET
                lft = lft-$sourceWidth-1
            WHERE
                lft >= $sourceLeft AND $rootCondition;

            UPDATE
                $tableQuoted
            SET
                rgt = rgt-$sourceWidth-1
            WHERE
                rgt >= $sourceRight AND $rootCondition;

        ";


        //step 3. create new place for target root condition

        if ($source['lft'] < $target['lft']){
            $targetLeft -= $sourceWidth+1;
            $targetRight -= $sourceWidth+1;
        }

        $where = '';
        $whereParents = "lft < $targetLeft AND rgt > $targetRight";

        if ($pMode == 'over'){
            $where = "lft >= $targetLeft";
            $newSourceLeft = $targetLeft;

        } else if ($pMode == 'below'){
            $where = "lft > $targetRight";
            $newSourceLeft = $targetRight+1;

        } else {//into
            $where = "lft > $targetLeft";
            $whereParents = "lft <= $targetLeft AND rgt >= $targetRight";
            $newSourceLeft = $targetLeft+1;
        }

        $createPlace = "
            UPDATE
                $tableQuoted
            SET
                lft = lft+$sourceWidth+1,
                rgt = rgt+$sourceWidth+1
            WHERE
                $where
                AND $rootCondition;

            UPDATE
                $tableQuoted
            SET
                rgt = rgt+$sourceWidth+1
            WHERE
                $whereParents
                AND $rootCondition;
        ";

        //step 4. move source to new created place
        $changeRoot = ($rField) ? ", $rField = '".esc($rValue)."' " : '';
        $moveSource = "
            UPDATE
                $tableQuoted
            SET
                lft = lft + $sourceWidth + $newSourceLeft,
                rgt = rgt + $sourceWidth + $newSourceLeft
                $changeRoot
            WHERE
                rgt <= 0
        ";


        dbBegin();
        dbWriteLock($table);

        try {

            dbExec($transformSource);
            dbExec($closeHole);
            dbExec($createPlace);
            dbExec($moveSource);

            dbCommit();
        } catch (Exception $e){
            error_log($e);
            dbRollback();
            return false;
        }

        kryn::invalidateCache('systemObjectTrees');

        return true;

    }

    /**
     * @param bool|array $pParent array('field_key' => 'value')
     * @param int $pDepth  0 returns only the root. 1 returns with one level of children, 2 with two levels etc
     * @param bool|array $pRootObjectId The primary value of the root object
     * @param string|array $pExtraFields
     *
     * @return array|bool
     * @throws Exception
     */
    public function getTree($pParent = false, $pDepth = 1, $pRootObjectId = false, $pExtraFields = ''){

        $start = microtime(true);
        if (!$this->definition['tableNested']){
            throw new Exception('Object is not marked as nested.');
        }

        if (!$this->definition['chooserBrowserTreeLabel']){
            throw new Exception('chooserBrowserTreeLabel in object not defined.');
        }

        $primKey = current($this->primaryKeys);

        if ($pParent)
            $idValue = $pParent[$primKey]?$pParent[$primKey]+0:'root';

        $cacheKey = 'systemObjectTrees_'.$this->object_key.'-'.md5($idValue.'-'.$pDepth.'-'.$pRootObjectId);

        if (true || !($result = kryn::getCache($cacheKey))){

            $condition = array();
            $pDepth += 0;

            if (!is_array($pExtraFields) && $pExtraFields != '')
                $pExtraFields = explode(',', str_replace(' ', '', trim($pExtraFields)));


            $title = $this->definition['chooserBrowserTreeLabel'];
            $icon  = $this->definition['chooserBrowserTreeIcon'];

            $table = dbQuote(dbTableName($this->definition['table']));
            $id    = dbQuote('node').'.'.dbQuote($primKey);
            $pid    = dbQuote('parent').'.'.dbQuote(current($this->primaryKeys));

            $depth = dbQuote('_depth');

            $selectId = 'MAX('.$id.') as '.$primKey;
            $selects[] = $selectId;
            $selects[] = 'MAX('.dbQuote('node').'.'.dbQuote($title).') AS '.dbQuote($title);
            if ($icon)
                $selects[] = 'MAX('.dbQuote('node').'.'.dbQuote($icon).') AS '.dbQuote($icon);

            $selects[] = '((MAX('.dbQuote('rgt', 'node').')-1-MAX('.dbQuote('lft', 'node').'))/2) AS '.dbQuote('_children_count');

            if (is_array($pExtraFields) && count($pExtraFields) > 0){
                foreach ($pExtraFields as $extraField)
                    $selects[] = 'MAX('.dbQuote($extraField, 'node').')';
            }

            $tables[] = "$table as ".dbQuote('parent');
            $tables[] = "$table as ".dbQuote('node');
            $tablesDefault = implode(', ', $tables);

            $aDepth = "(COUNT($pid) - 1)";

            $nodeLft = dbQuote('lft', 'node');
            $parentLft = dbQuote('lft', 'parent');
            $parentRgt = dbQuote('rgt', 'parent');
            $parent1Lft = dbQuote('lft', 'parent1');
            $parent1Rgt = dbQuote('rgt', 'parent1');
            $parent1 = dbQuote('parent1');


            if ($pParent){

                $conditionSql = dbConditionArrayToSql($pParent, 'node');

                $tables[] = "(
                    SELECT
                        MAX(node.lft) as lft, MAX(node.rgt) as rgt, (COUNT($id)) AS $depth
                    FROM
                        $tablesDefault
                    WHERE $nodeLft BETWEEN $parentLft AND $parentRgt
                    AND $conditionSql
                    GROUP BY $id
                    ORDER BY MAX($nodeLft)
                  ) AS $parent1";

                $additionalWhere = " AND $nodeLft BETWEEN $parent1Lft AND $parent1Rgt";
                $aDepth = "(COUNT($pid) - MAX($parent1.$depth))";

            } else {
                //all on first and second level
                $additionalWhere = '';

                //if we have chooserBrowserTreeRootAsObject as 1 we do not allow to fetch elements without conditions
                //since rgt and lft would overlap

                if ($this->definition['chooserBrowserTreeRootAsObject'] == 1 && !$pRootObjectId) return false;

                if ($pRootObjectId){
                    $field = dbQuote($this->definition['chooserBrowserTreeRootObjectField'], 'parent');
                    $additionalWhere = " AND $field = '".esc($pRootObjectId)."'";
                }

            }

            $selects[] = "$aDepth AS ".$depth;

            $tables  = implode(",\n", $tables);
            $selects = implode(",\n", $selects);

            $sql = "
            SELECT   $selects, MAX(node.lft) as lft, MAX(node.rgt) as rgt
            FROM     $tables
            WHERE    $nodeLft BETWEEN $parentLft AND $parentRgt
            $additionalWhere
            GROUP BY $id
            HAVING $aDepth <= $pDepth
            ORDER BY MAX($nodeLft)
            ";

            $res = dbExec($sql);

            if (!$res) return false;

            $result = array();
            $lastParent = array();

            while ($row = dbFetch($res)){

                if ($row['_depth'] == 0){
                    if ($pParent){
                        $result = $row;
                        $lastParent[$row['_depth']] =& $result;
                    } else{
                        $result[] = $row;
                        $lastParent[$row['_depth']] =& $result[count($result)-1];
                    }
                } else {
                    $p =& $lastParent[$row['_depth']-1]['_children'];
                    $p[] = $row;
                    $lastParent[$row['_depth']] =& $p[count($p)-1];
                }

            }
            kryn::setCache($cacheKey, $result);

        }

        return $result;

    }

    /**
     * Converts the values in the array to the proper column names. Especially for object fields.
     *
     *
     * @param $pValues
     * @return array|bool
     */
    public function retrieveValues($pValues){

        $row = array();

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

    public function add($pValues, $pParentValues = false, $pMode = 'into', $pParentObjectKey = false){

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

            if ($this->definition['tableNested']){
                if ($this->object_key != $pParentObjectKey && $this->definition['chooserBrowserTreeRootAsObject']){

                    $targetPrimaries = krynObject::getPrimaryList($this->definition['chooserBrowserTreeRootObject']);
                    $targetPrimaries[$targetPrimaries[0]] = $row[ $this->definition['chooserBrowserTreeRootObjectField'] ];

                    $this->move($primaries, $targetPrimaries, $pMode, $this->definition['chooserBrowserTreeRootObject']);
                }
            }


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

    public function update($pPrimaryValues, $pValues){

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
                                $pSingleRow = false, $pOrderBy = '', $pOrderDirection = 'asc'){

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

        $primaryCondition = dbPrimaryArrayToSql($pPrimaryIds, $this->object_key, $this->object_key);

        if ($primaryCondition)
            $where .= ' AND '.$primaryCondition;

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

        if ($pLimit > 0)
            $sql .= ' LIMIT '.($pLimit+0);

        if ($pOffset > 0)
            $sql .= ' OFFSET '.($pOffset+0);

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
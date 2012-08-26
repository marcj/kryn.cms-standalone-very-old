<?php


/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license information, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */


/**
 * Global framework functions
 *
 * @author MArc Schmidt <marc@Core\Kryn.org>
 */


/**
 * Escape a string for usage in SQL.
 * Depending on the current database this functions choose the proper escape
 * function.
 *
 * @param string     $pValue
 * @param int|string $pType 1=(default) normal escape, 2=remove all except a-zA-Z0-9-_, or PDO::PARAM_*=
 *                   PDO::PARAM_STR, PDO::PARAM_INT, etc
 *
 * @global
 * @return string Escaped string
 */
function esc($pValue, $pType = 1) {

    if ($pType == 1){
        $pType = is_string($pValue) ? PDO::PARAM_STR : PDO::PARAM_INT;
    }

    if ($pType == 2) {
        return preg_replace('/[^a-zA-Z0-9-_]/', '', $pValue);
    }

    return dbConnection()->quote($pValue, $pType);
}

/**
 * Quotes $pValue. Adds $pTable to the beginning if set.
 *
 * @param string|array $pValue Possible is "test, bla, blub" or just "foo". If array("foo", "bar") it returns a array again
 * @param $pTable
 *
 * @return mixed
 */
function dbQuote($pValue, $pTable = ''){

    if (is_array($pValue)){
        foreach ($pValue as &$value)
            $value = dbQuote($value);
        return $pValue;
    }
    if (strpos($pValue, ',') !== false){
        $values = explode(',', str_replace(' ', '', $pValue));
        $values = dbQuote($values);
        return implode(', ', $values);
    }

    if ($pTable && strpos($pValue, '.') === false){
        return dbQuote($pTable).'.'.dbQuote($pValue);
    }
    return preg_replace('/[^a-zA-Z0-9-_]/', '', $pValue);;
}

/**
 * Get the PDO connection instance
 *
 * @param bool $pSlave
 *
 * @return PDO
 */
function dbConnection($pSlave = null) {
    if ($pSlave !== null) Core\Kryn::$dbConnectionIsSlave = $pSlave;
    Core\Kryn::$dbConnection = Propel::getConnection(null, Core\Kryn::$dbConnectionIsSlave);
    return Core\Kryn::$dbConnection;
}

/**
 * Begins a transaction. If we've connected to a database slave, this call let us reconnect to a master
 *
 */
function dbBegin(){
    dbConnection()->beginTransaction();
}

/**
 * Reverts back to the original version before the call of dbBegin().
 * This also unlocks all locked tables.
 */
function dbRollback(){

    dbConnection()->rollback();
    if (database::$activeLock && Core\Kryn::$config['database']['type'] == 'mysql'){
        dbLock('UNLOCK TABLES');
        database::$activeLock = false;
    }

    if (!database::$activeTransaction) return;
    dbExec('ROLLBACK');
    database::$activeTransaction = false;
}

/**
 * Stores all changed between dbBegin() and dbCommit()
 * This also unlocks all locked tables.
 *
 */
function dbCommit(){
    dbConnection()->commit();
}

/**
 * Execute a query and return the item
 *
 * @param string  $pQuery  The SQL query to execute
 * @param array   $pParams The parameters
 *
 * @return array
 */
function dbExFetch($pQuery, $pParams = null) {
    return dbExec($pQuery, $pParams)->fetch(PDO::FETCH_ASSOC);
}

/**
 * Execute a query and return a list of items
 *
 * @param string  $pQuery  The SQL query to execute
 * @param array   $pParams The parameters
 *
 * @return array
 */
function dbExFetchAll($pQuery, $pParams = null) {
    return dbExec($pQuery, $pParams)->fetchAll(PDO::FETCH_ASSOC);
}



/**
 * Executes an SQL query.
 *
 * If $pParams is used a prepared statement is used.
 * If an SQLLogger is configured, the execution is logged.
 *
 * @param string $pQuery  The SQL query to execute
 * @param array  $pParams The parameters to bind to the query
 *
 * @return PDOStatement
 */
function dbExec($pQuery, $pParams = null) {

    if ($pParams !== null){
        var_dump($pQuery);
        $sth = dbConnection()->prepare($pQuery);
        if (!is_array($pParams)) $pParams = array($pParams);
        $sth->execute($pParams);
    } else {
        $sth = dbConnection()->query($pQuery);
    }
    return $sth;
}

/**
 *
 *
 * @param $pTable
 * @param $pCount
 * @param bool $pWhere
 * @return array
 */
function dbTableLang($pTable, $pCount = -1, $pWhere = false) {
    if ($_REQUEST['lang'])
        $lang = esc($_REQUEST['lang']);
    else
        $lang = Core\Kryn::$language;
    if ($pWhere)
        $pWhere = " lang = '$lang' AND " . $pWhere;
    else
        $pWhere = "lang = '$lang'";
    return dbTableFetch($pTable, $pCount, $pWhere);
}


/**
 * Select items based on pWhere on, table pTable and returns pCount items.
 *
 * @param string  $pTable The table name based on your extension table definition.
 * @param integer $pCount How many items it will returns, with 1 you'll get direct the array without a list.
 * @param string  $pWhere condition object
 * @param string  $pFields Comma separated list of the columns
 *
 * @return array
 */
function dbTableFetch($pTable, $pCount = -1, $pWhere = '', $pFields = '*') {

    //to change pCount <-> pWhere
    if (is_numeric($pWhere)){
        $pNewWhere = $pCount;
        $pNewCount = $pWhere;
        $pWhere = $pNewWhere;
        $pCount = $pNewCount;
    }

    $table = dbTableName($pTable);

    if ($pFields != '*')
        $pFields = dbQuote($pFields);

    $sql = "SELECT $pFields FROM $table";
    if ($pWhere != false){
        if (is_array($pWhere)) $pWhere = dbConditionToSql($pWhere);
        $sql .= " WHERE $pWhere";
    }

    return dbExfetch($sql, $pCount);
}

/**
 * Returns the table with prefix if $pTable does not start with / (slash)
 * This means, that if you use table names with a starting slash then the
 * framework won't add the prefix at the beginning.
 *
 * @param $pTable
 * @return string
 */
function dbTableName($pTable){
    return strtolower((substr($pTable,0,1) == '/')?$pTable:Core\Kryn::$config['database']['prefix'].$pTable);
}

/**
 * Inserts the values based on pFields into the table pTable.
 *
 * @param string $pTable  The table name based on your extension table definition
 * @param array  $pData Array as a key-value pair. key is the column name and the value is the value. More infos under http://www.Core\Kryn.org/docu/developer/framework-database
 *
 * @return integer The last_insert_id() (if you use auto_increment/sequences)
 */
function dbInsert($pTable, $pData) {

    $table = dbTableName($pTable);
    $cols = array_keys($pData);

    foreach ($pData as $value) {
        $values[] = '?';
    }


    $query = 'INSERT INTO ' . $table
        . ' (' . implode(', ', $cols) . ')'
        . ' VALUES (' . implode(', ', $values) . ')';

    if (dbExec($query, array_values($pData)))
        return dbLastId();
    else
        return false;
}

/**
 * Converts $pItems to an array index with $pIndex
 *
 * @param $pItems
 * @param $pIndex
 *
 * @return array
 */
function dbToKeyIndex(&$pItems, $pIndex) {
    $res = array();
    if (count($pItems) > 0)
        foreach ($pItems as $item) {
            $res[$item[$pIndex]] = $item;
        }
    return $res;
}

/**
 * Fetch the SQLSTATE associated with the last operation on the database handle.
 *
 * @global
 * @return mixed
 */

function dbError() {
    return dbConnection()->errorCode ();
}

/**
 *  Fetch extended error information associated with the last operation on the database handle.
 *
 * @global
 * @return array
 */

function dbErrorInfo() {
    return dbConnection()->errorInfo();
}
/**
 * Returns PDO::lastInsertId
 *
 * @return mixed
 */
function dbLastId() {
    return dbConnection()->lastInsertId ();
}


/**
 * Update a row or rows with the values based on pFields into the table pTable.
 *
 * @param string       $pTable   The table name based on your extension table definition
 * @param string|array $pCondition Define the limitation as a SQL or as a array ('field' => 'value')
 * @param array        $pData  Array as a key-value pair. key is the column name and the value is the value. More infos under http://www.Core\Kryn.org/docu/developer/framework-database
 *
 * @return type
 */
function dbUpdate($pTable, $pCondition = array(), $pData = array()) {

    $table = dbTableName($pTable);

    $fields = array();
    foreach ($pData as $column => $value) {
        $fields[] = $column.' = ?';
    }

    $data = array_merge(array_values($pData), array_values($pCondition));

    $sql  = 'UPDATE ' . $table . ' SET ' . implode(', ', $fields)
          .' WHERE ' . implode('=? AND ', array_keys($pCondition)) . '= ?';

    return dbExec($sql, $data)?true:false;
}

/**
 * Deletes rows from the table based on the pWhere
 *
 * @param string $pTable The table name based on your extension table definition
 * @param string|array $pWhere Do not forget this, otherwise the table will be truncated. You can use array as in
 *
 * @return bool
 */
function dbDelete($pTable, $pWhere = '') {

    $table = dbTableName($pTable);

    $sql = "DELETE FROM " . $table . "";
    if (is_string($pWhere) && $pWhere)
        $sql .= " WHERE $pWhere ";
    if (is_array($pWhere))
        $sql .= " WHERE ".dbConditionToSql($pWhere);

    return dbExec($sql);
}

/**
 * Returns the number of rows affected by the last SQL statement
 *
 * @param $pStatement
 * @return int
 */
function dbNumRows($pStatement){
    return $pStatement->rowCount();
}

/**
 * Closes the cursor, enabling the statement to be executed again
 *
 * @param $pStatement
 * @return bool
 */
function dbFree($pStatement){
    return $pStatement->closeCursor();
}

/**
 * Fetches the next row from a result set
 *
 * @param PDOStatement  $pStatement
 *
 * @return array
 */
function dbFetch($pStatement) {
    return !$pStatement?false:$pStatement->fetch(PDO::FETCH_ASSOC);
}

/**
 * Returns an array containing all of the result set rows
 *
 * @param PDOStatement  $pStatement
 *
 * @return array
 */
function dbFetchAll($pStatement) {
    return !$pStatement?false:$pStatement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 *
 * Returns the SQL counterpart of the Order array.
 *
 * Structure is:
 *
 *      array(
 *          array('field' => 'category', 'direction' => 'asc'),
 *          array('field' => 'title',    'direction' => 'asc')
 *      )
 *
 * or
 *      array(
 *         array('category' => 'asc'),
 *         array('title' => 'desc')
 *       )
 * or
 *      array('category' => 'desc')
 *
 *
 * @param $pValues
 * @param $pTable
 *
 * @return string SQL
 */
function dbOrderToSql($pValues, $pTable = ''){

    $sql = ' ORDER BY ';

    if (count($pValues) == 1 && !is_array($pValues[0])){
        return $sql.dbQuote(key($pValues), $pTable).' '.((strtolower(current($pValues))=='asc')?'ASC':'DESC');
    }


    if (is_numeric(key($pValues[0]))){

        foreach ($pValues as $order ){
            $sql .= dbQuote($order['field'], $pTable).' '.((strtolower($order['direction'])=='asc')?'ASC':'DESC').',';
        }
    }

    if (!is_numeric(key($pValues[0]))){

        foreach ($pValues as $key => $order ){
            $sql .= dbQuote($key, $pTable).' '.((strtolower($order)=='asc')?'ASC':'DESC').',';
        }
    }

    return substr($sql, 0, -1);
}

/**####################################################################################################################
   ####################################################################################################################
 *
 *  DEPRECATED
 *
 * ####################################################################################################################
 * ####################################################################################################################
 */


/**
 * Returns count
 *
 * @param string $pTable
 * @param bool   $pWhere
 *
 *
 * @return int
 */
function dbCount($pTable, $pWhere = false) {

    $table = dbQuote(dbTableName($pTable));

    if (Core\Kryn::$config['database']['type'] == 'pgsql'){

        $columns = array_keys(database::getColumns(dbTableName($pTable)));
        $firstColumn = $columns[0];

        $sql = "SELECT $firstColumn FROM $table";
        if ($pWhere != false)
            $sql .= " WHERE $pWhere ";

        $res = dbExec($sql);

        $count = dbNumRows($res);

        dbFree($res);

        return $count;

    } else {
        $sql = "SELECT count(*) as ".dbQuote('counter')." FROM $table";
        if ($pWhere != false)
            $sql .= " WHERE $pWhere ";

        $row = dbExfetch($sql);
        return $row['counter'];
    }
}



/**
 * Returns a array with as first element a comma sperated list of all keys and as second
 * element a comma seperated list of the values. Can be used in INSERT queries.
 *
 * If a element in $pValues has a numeric key, the value will be retrieved
 * from getArgv($key)
 *
 * Example:
 *
 * array('title' => 'Foo', 'category_id' => 2)
 * => returns array( "title, category_id", "'foo', 2" )
 *
 * @param  array $pValues
 * @return string
 */
function dbValuesToCommaSeperated($pValues){

    $fields = array();
    $values = array();
    foreach ($pValues as $key => $field) {

        if (is_numeric($key)) {
            $fieldName = $field;
            $val = getArgv($field);
        } else {
            $fieldName = $key;
            $val = $field;
        }

        $fields[] = $fieldName;

        $values[] = is_numeric($val) ? $val : "'".esc($val)."'";
    }

    return array(
        implode(', ', $fields),
        implode(', ', $values)
    );
}

/**
 * Extracts all field names from a Order array.
 *
 * @param $pValues
 * @param string $pTable
 * @return array
 */
function dbExtractOrderFields($pValues, $pTable = ''){

    $fields = array();

    if (count($pValues) == 1 && !is_array($pValues[0])){
        return array(dbQuote(key($pValues), $pTable));
    }


    if (is_numeric(key($pValues[0]))){

        foreach ($pValues as $order ){
            $fields[] = dbQuote($order['field'], $pTable);
        }
    }

    if (!is_numeric(key($pValues[0]))){

        foreach ($pValues as $key => $order ){
            $fields[] = dbQuote($key, $pTable);
        }
    }

    return $fields;
}



/**
 * Converts primarykey to normal (complex) condition object used in dbCondition().
 *
 * 
 * @see PrimaryKeys
 * 
 * @param array       $pPrimaryValue
 * @param string      $pTable Adds the table name in front of the field names. ($pTable.<column>)
 * @param string|bool $pObjectKey
 * @return bool|string
 */
function dbPrimaryKeyToCondition($pCondition, $pObjectKey = false, $pTable = ''){

    $sql = '';
    $result = array();

    // 
    // condition: 
    // [
    //   ["bla", "=", 1], "and"
    // 
    // ]
    // 
    // pk:
    //  1
    // 
    // pk: 
    // ["bla" => 2, "hosa" => 1]
    // 
    // pk: 
    // [ ["bla" => 1], ["bla" => 2] ]
    if (is_array($pCondition) && is_array($pCondition[0]) && is_numeric(key($pCondition)) && is_numeric(key($pCondition[0]))){
        //its already a condition object
        return $pCondition;
    }

    if ($pObjectKey){
        $primaries = \Core\Object::getPrimaryList($pObjectKey);
    }

    if (array_key_exists(0, $pCondition)){
        foreach ($pCondition as $idx => $group){
            $cGroup = array();

            if (is_array($group)){

                foreach ($group as $primKey => $primValue){

                    if (!is_string($primKey))
                        $primKey = $primaries[$primKey];

                    if ($cGroup) $cGroup[] = 'and';
                    $cGroup[] = array(dbQuote($primKey, $pTable), '=', $primValue);
                }
            } else {

                if (!is_string($idx))
                    $primKey = $primaries[0];

                if ($cGroup) $cGroup[] = 'and';
                $cGroup[] = array(dbQuote($primKey, $pTable), '=', $group);
            }
            if ($result) $result[] = 'or';
            $result[] = $cGroup;
        }

    } else {
        //we only have to select one row
        $group = array();

        foreach ($pCondition as $primKey => $primValue){
            if ($group) $group[] = 'and';
            $group[] = array(dbQuote($primKey, $pTable), '=', $primValue);
        }
        $result[] = $group;
    }

    return $result;

}



/**
 *
 * Condition object to SQL.
 *
 * $pCondition can be following structure.
 *
 * Quick definition:
 * For this you need to define $pObjectKey, so that the function can map the values with the primary keys.
 *
 * 23 (need $pObjectKey then)=> "id = 23"
 * array( 4 ) (need $pObjectKey then)=> "id = 4"
 * array( 1, 3 ) (need $pObjectKey then) => "(id = 1) or (id = 3)"
 *
 *
 * Normal:
 * If your object has more than one primary key, you should use this definition.
 *
 * array( 'id' => 1, 'cat_id' => 3) => "id = 1 AND cat_id = 3"
 *
 * array(
 *  array('id' => 1, 'cat_id' => 3),
 *  array('id' => 1, 'cat_id' => 4)
 * )
 *   => "(id = 1 AND cat_id = 3) OR (id = 1 AND cat_id = 4)"
 *
 *
 * Extended:
 * Such a structure will be returned from the ka.Field type 'condition'.
 * Note: A simple array('id', '=', '2') won't work! You need to wrap a second array() around it.
 *
 * array(
 *   array('id', '=', '2'),
 *   'OR',
 *   array('id', '>', 1),
 *   'OR',
 *   array(
 *     array('category_id', '=', 15),
 *     'AND',
 *     array('id', '=', 0)
 *   )
 * )
 *
 *
 * @param mixed   $pConditions
 * @param array   &$pData The data for prepared statement.
 * @param string  $pTablePrefix
 * @param string  $pObjectKey
 *
 * @return bool|string
 */
function dbConditionToSql($pConditions, &$pData, $pTablePrefix = '', $pObjectKey = ''){

    $result = '';
    if (!is_array($pConditions) && $pConditions !== false && $pConditions !== null) $pConditions = array($pConditions);

    if (is_array($pConditions) && !is_numeric(key($pConditions))){
        //array( 'bla' => 'hui' );
        //we have a structure like in dbPrimaryKeyToConditionToSql, so call it
        return dbConditionToSql(dbPrimaryKeyToCondition($pConditions, $pObjectKey), $pData, $pTablePrefix, $pObjectKey);
    }

    if (is_array($pConditions[0]) && !is_numeric(key($pConditions[0]))){
        //array( array('bla' => 'bla', ... );
        //we have a structure like in dbPrimaryKeyToConditionToSql, so call it
        return dbConditionToSql(dbPrimaryKeyToCondition($pConditions, $pObjectKey), $pData, $pTablePrefix, $pObjectKey);
    }

    if (!is_array($pConditions[0])){
        //array( 1, 2, 3 );
        return dbConditionToSql(dbPrimaryKeyToCondition($pConditions, $pObjectKey), $pData, $pTablePrefix, $pObjectKey);
    }

    return dbFullConditionToSql($pConditions, $pData, $pTablePrefix);

}

function dbFullConditionToSql($pConditions, &$pData, $pTablePrefix){

    $result = '';

    if (is_array($pConditions)){
        foreach ($pConditions as $condition){

            if (is_array($condition) && is_array($condition[0])){
                $result .= ' ('.dbFullConditionToSql($condition, $pData, $pTablePrefix).')';
            } else if(is_array($condition)){
                $result .= dbConditionSingleField($condition, $pData, $pTablePrefix);
            } else if (is_string($condition)){
                $result .= ' '.$condition.' ';
            }

        }
    } else {
        //only one condition, ex: array('id', '>', 0)
        $result = dbFullConditionToSql($pConditions, $pData, $pTablePrefix);

    }

    return $result;
}

/**
 * Helper function for dbConditionToSql()
 *
 * @internal
 * @param array $pCondition
 * @param array &$pData
 * @param string $pTable
 * @return string
 */
function dbConditionSingleField($pCondition, &$pData, $pTable = ''){

    if (!is_numeric($pCondition[0])){
        if (($pos = strpos($pCondition[0], '.')) === false){
            $result = ($pTable?dbQuote($pTable).'.':'').dbQuote($pCondition[0]).' ';
        } else {
            $result = dbQuote(substr($pCondition[0], 0, $pos)).'.'.dbQuote(substr($pCondition[0], $pos)).' ';
        }
    } else {
        $result = ($pCondition[0]+0);
    }

    if (strtolower($pCondition[1]) == 'regexp')
        $result .= Core\Kryn::$config['database']['type']=='mysql'?'REGEXP':'~';
    else
        $result .= $pCondition[1];

    if (!is_numeric($pCondition[0])){
        $pData[] = $pCondition[2];
        $p = ':p'.count($pData);
        if (strtolower($pCondition[1]) == 'in'){
            $result .= " ($p)";
        } else {
            $result .= ' '.$p;
        }
    } else {
        $result .= ' '.($pCondition[0]+0);
    }

    return $result;
}


?>
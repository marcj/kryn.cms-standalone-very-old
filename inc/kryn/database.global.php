<?php


/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */


/**
 * Global framework functions
 *
 * @author MArc Schmidt <marc@kryn.org>
 */


/**
 * Escape a string for usage in SQL.
 * Depending on the current database this functions choose the proper escape
 * function.
 *
 * @param string $p
 * @param int $pEscape
 *
 * @return string Escaped string
 */
function esc($p, $pEscape = 1) {
    global $kdb, $cfg;

    dbConnect();

    if (is_array($p)) {
        foreach ($p as $k => $v) {
            $p2[$k] = esc($v);
        }
        return $p2;
    }

    if ($pEscape == 2) {
        return preg_replace('/[^a-zA-Z0-9-_]/', '', $p);
    }

    if ($cfg['db_pdo'] + 0 == 1 || $cfg['db_pdo'] === '') {
        return substr(substr($kdb->pdo->quote($p), 1), 0, -1);
    } else {
        switch ($cfg['db_type']) {
            case 'sqlite':
                return sqlite_escape_string($p);
            case 'mysql':
                return mysql_real_escape_string($p, $kdb->connection);
            case 'mysqli':
                return mysqli_real_escape_string($kdb->connection, $p);
            case 'postgresql':
                return pg_escape_string($kdb->connection, $p);
        }
    }
}

/**
 * Quotes column or table names and return pValue with quotes surrounded and
 * lowercase (because table names and column names have to be lowercased)
 *
 * @param string|array $pValue Possible is "test, bla, blub" or just "foo". If array("foo", "bar") it returns a array again
 * @return mixed
 */
function dbQuote($pValue){

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
    return strtolower((kryn::$config['db_type'] == 'mysql') ? '`'.$pValue.'`': '"'.$pValue.'"');
}

/**
 * Connects to the database
 *
 * @param bool $pReadOnly If true, we try to connect to a slave (if defined)
 *
 * @return mixed
 */
function dbConnect($pReadOnly = false) {
    global $kdb, $cfg;

    if ($kdb) return;

    //todo, handle $pReadOnly

    $kdb = new database(
        $cfg['db_type'],
        $cfg['db_server'],
        $cfg['db_user'],
        $cfg['db_passwd'],
        $cfg['db_name'],
        ($cfg['db_pdo'] + 0 == 1 || $cfg['db_pdo'] === '') ? true : false,
        ($cfg['db_forceutf8'] == '1') ? true : false
    );

    $kdb->readOnly = $pReadOnly;

    if (!$kdb->isActive()) {
        kryn::internalError('Can not connect to the database. Error: ' . $kdb->lastError());
    }

}

/**
 * Begins a transaction. If we've connected to a database slave, this call let us reconnect to a master
 *
 */
function dbBegin(){
    dbExec('BEGIN');
}

/**
 * Reverts back to the original version before the call of dbBegin()
 */
function dbRollback(){
    dbExec('ROLLBACK');
}

/**
 * Stores all changed between dbBegin() and dbCommit()
 *
 */
function dbCommit(){
    dbExec('COMMIT');
}


/**
 * Execute a query and return the items
 * If you want to have a exact count of lines use SQL's LIMIT with $pRowCount as -1,
 * except you really know what you'r doing.
 *
 * @param string  $pSql      The SQL
 * @param integer $pRowCount How much rows you want. Use -1 for all, with 1 you'll get direct the array without a list.
 *
 * @return array
 */
function dbExFetch($pSql, $pRowCount = 1) {
    global $kdb, $cfg;

    dbConnect();

    $pSql = str_replace('%pfx%', $cfg['db_prefix'], $pSql);
    return $kdb->exfetch($pSql, $pRowCount);
}


/**
 * Execute a query and return the resultSet. To retrieve the values, call dbFetch() with the result.
 *
 * @param string $pSql
 *
 * @return resultSet
 */
function dbExec($pSql) {
    global $kdb;

    dbConnect();

    $pSql = str_replace('%pfx%', pfx, $pSql);

    $res = $kdb->exec($pSql);

    if (dbError())
        klog('database', dbError());

    return $res;
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
        $lang = kryn::$language;
    if ($pWhere)
        $pWhere = " lang = '$lang' AND " . $pWhere;
    else
        $pWhere = "lang = '$lang'";
    return dbTableFetch($pTable, $pCount, $pWhere);
}


/**
 * Select items based on pWhere on table pTable and returns pCount items.
 *
 * @param string  $pTable The table name based on your extension table definition.
 * @param integer $pCount How many items it will returns, with 1 you'll get direct the array without a list.
 * @param string  $pWhere
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

    $sql = "SELECT $pFields FROM $table";
    if ($pWhere != false)
        $sql .= " WHERE $pWhere";

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
    return strtolower((substr($pTable,0,1) == '/')?$pTable:pfx.$pTable);
}

/**
 * Inserts the values based on pFields into the table pTable.
 *
 * @param string $pTable  The table name based on your extension table definition
 * @param array  $pFields Array as a key-value pair. key is the column name and the value is the value. More infos under http://www.kryn.org/docu/developer/framework-database
 *
 * @return integer The last_insert_id() (if you use auto_increment/sequences)
 */
function dbInsert($pTable, $pValues) {

    $table = dbQuote(dbTableName($pTable));
    $values = dbValuesToCommaSeperated($pValues);

    $fields = dbQuote($values[0]);
    $values = $values[1];

    $sql = "INSERT INTO $table ($fields) VALUES ($values)";

    if (dbExec($sql))
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
 * Returns the last occured error if exists
 *
 * @return mixed
 */

function dbError() {
    global $kdb;

    return $kdb->lastError();
}

/**
 * Returns the last_insert_id() (if you use auto_increment/sequences)
 *
 * @return mixed
 */
function dbLastId() {
    global $kdb;

    return $kdb->lastId();
}


/**
 * Update a row or rows with the values based on pFields into the table pTable.
 *
 * @param string       $pTable   The table name based on your extension table definition
 * @param string|array $pPrimary Define the limitation as a SQL or as a array ('field' => 'value')
 * @param array        $pFields  Array as a key-value pair. key is the column name and the value is the value. More infos under http://www.kryn.org/docu/developer/framework-database
 *
 * @return type
 */
function dbUpdate($pTable, $pPrimary, $pFields) {

    $table = dbQuote(dbTableName($pTable));
    $values = dbValuesToUpdateSql($pFields);

    if (is_array($pPrimary) || !$pPrimary)
        $pPrimary = (!$pPrimary)?'1=1':dbPrimaryArrayToSql($pPrimary);

    $sql = "UPDATE $table SET $values WHERE $pPrimary";

    return dbExec($sql)?true:false;
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
    if ($pWhere)
        $sql .= " WHERE $pWhere ";

    return dbExec($sql);
}

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
    $table = dbTableName($pTable);
    $sql = "SELECT count(*) as count FROM $table";
    if ($pWhere != false)
        $sql .= " WHERE $pWhere ";
    $row = dbExfetch($sql);
    return $row['count'];
}

/**
 * Fetch a row based on the specified resultset from dbExec()
 *
 * @param resultset $pRes   The result of dbExec()
 * @param int    $pCount Defines how many items the function returns
 *
 * @return array
 */
function dbFetch($pRes, $pCount = 1) {
    global $kdb;
    return $kdb->fetch($pRes, $pCount);
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
 * array('title' => 'Foo', 'category_rsn' => 2)
 * => returns array( "title, category_rsn", "'foo', 2" )
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
 * Returns a comma sperated list of $pValues to be used in UPDATE queries.
 * If a element in $pValues has a numeric key, the value will be retrieved
 * from getArgv($key). The keys will go through dbQuote()
 *
 * Example:
 *
 * array('title' => 'Foo', 'category_rsn' => 2)
 * => returns "`title` = 'Foo', `category_rsn` = 2"
 *
 * @param  array $pValues
 * @return string
 */
function dbValuesToUpdateSql($pValues){

    $values = array();

    foreach ($pValues as $key => $field) {
        if (is_numeric($key)) {
            $fieldName = $field;
            $val = getArgv($field);
        } else {
            $fieldName = $key;
            $val = $field;
        }

        $values[] = dbQuote($fieldName).' = '.(is_numeric($val) ? $val : "'".esc($val)."'");
    }

    return implode(', ', $values);
}

/**
 * Converts given primary values into proper SQL.
 * Resolve all patterns in krynObject::parseUrl();
 *
 * Examples:
 *
 * array( 'id' => 1, 'cat_id' => 3) => "id = 1 AND cat_id = 3"
 *
 * array(
 *  array('id' => 1, 'cat_id' => 3,
 *  array('id' => 1, 'cat_id' => 4
 * )) => "(id = 1 AND cat_id = 3) OR (id = 1 AND cat_id = 4)"
 *
 * @param array $pPrimaryValue
 * @param string $pTable Adds the table in front of the field names
 * @return bool|string
 */
function dbPrimaryArrayToSql($pPrimaryValue, $pTable = ''){

    $sql = '';

    if (!is_array($pPrimaryValue)){
        return false;
    }

    if (!$pPrimaryValue) return false;

    if (array_key_exists(0, $pPrimaryValue)){
        //we have to select multiple rows
        foreach ($pPrimaryValue as $group){
            $sql .= ' (';
            foreach ($group as $primKey => $primValue){
                $sql .= ($pTable?dbQuote($pTable).".":'').dbQuote($primKey)." = '".esc($primValue)."' AND ";
            }
            $sql = substr($sql, 0, -5).') OR ';
        }

        $sql = substr($sql, 0, -3);
    } else {
        //we only have to select one row
        $sql .= ' (';
        foreach ($pPrimaryValue as $primKey => $primValue){
            $sql .= ($pTable?dbQuote($pTable).".":'').dbQuote($primKey)." = '".esc($primValue)."' AND ";
        }
        $sql = substr($sql, 0, -5).')';
    }

    return $sql;

}

/**
 * Converts the array from ka.field type 'condition' to SQL
 *
 * @param array  $pConditions
 * @param string $pTable
 *
 * @return string
 */
function dbConditionArrayToSql($pConditions, $pTable = ''){

    $result = '';

    if (is_string($pConditions[0])){
        //only one condition, ex: array('rsn', '>', 0)

        $result = dbConditionSingleField($pConditions, $pTable);

    } else {
        foreach ($pConditions as $condition){

            if (is_array($condition) && is_array($condition[0])){
                $result .= ' ('.dbConditionArrayToSql($condition, $pTable).')';
            } else if(is_array($condition)){
                $result .= dbConditionSingleField($condition, $pTable);
            } else if (is_string($condition)){
                $result .= ' '.$condition.' ';
            }

        }
    }

    return $result;
}

/**
 * Helper function for dbConditionArrayToSql()
 *
 * @internal
 * @param $pCondition
 * @param string $pTable
 * @return string
 */
function dbConditionSingleField($pCondition, $pTable = ''){

    if (($pos = strpos($pCondition[0], '.')) === false){
        $result = ($pTable?dbQuote($pTable).'.':'').dbQuote($pCondition[0]).' ';
    } else {
        $result = dbQuote(substr($pCondition[0], 0, $pos)).'.'.dbQuote(substr($pCondition[0], $pos)).' ';
    }

    if ($pCondition[1] == 'REGEXP')
        $result .= kryn::$config['db_type']=='mysql'?'REGEXP':'~';
    else
        $result .= $pCondition[1];

    if ($pCondition[1] == 'IN'){
        if (is_numeric($pCondition[2]))
            $result .= ' '.$pCondition[2];
        else
            $result .= " '".esc($pCondition[2])."'";
    } else {
        $result .= ' '.$pCondition[2];
    }

    return $result;
}


?>
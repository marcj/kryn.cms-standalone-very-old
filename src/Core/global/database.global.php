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
 *                          PDO::PARAM_STR, PDO::PARAM_INT, etc
 *
 * @global
 * @return string Escaped string
 */
function esc($pValue, $pType = 1)
{
    if ($pType == 1) {
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
 * @param              $pTable
 *
 * @return mixed
 */
function dbQuote($pValue, $pTable = '')
{
    if (is_array($pValue)) {
        foreach ($pValue as &$value) {
            $value = dbQuote($value);
        }

        return $pValue;
    }
    if (strpos($pValue, ',') !== false) {
        $values = explode(',', str_replace(' ', '', $pValue));
        $values = dbQuote($values);

        return implode(', ', $values);
    }

    if ($pTable && strpos($pValue, '.') === false) {
        return dbQuote($pTable) . '.' . dbQuote($pValue);
    }

    return preg_replace('/[^a-zA-Z0-9-_]/', '', $pValue);;
}

/**
 * Get the PDO connection instance
 *
 * @param bool $write force a write-able connection
 *
 * @return PDO
 */
function dbConnection($write = false)
{
    //if (null !== $pSlave) Core\Kryn::$dbConnectionIsSlave = $pSlave;
    Core\Kryn::$dbConnection = \Propel\Runtime\Propel::getConnection();

    return Core\Kryn::$dbConnection;
}

/**
 * Begins a transaction. If we've connected to a database slave, this call let us reconnect to a master
 *
 */
function dbBegin()
{
    dbConnection()->beginTransaction();
}

/**
 * Reverts back to the original version before the call of dbBegin().
 * This also unlocks all locked tables.
 */
function dbRollback()
{
    static $activeLock = false;
    static $activeTransaction = false;

    dbConnection()->rollback();
    if ($activeLock && Core\Kryn::$config['database']['type'] == 'mysql') {
        dbLock('UNLOCK TABLES');
        $activeLock = false;
    }

    if (!$activeTransaction) {
        return;
    }
    dbExec('ROLLBACK');
    $activeTransaction = false;
}

/**
 * Stores all changed between dbBegin() and dbCommit()
 * This also unlocks all locked tables.
 *
 */
function dbCommit()
{
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
function dbExFetch($pQuery, $pParams = null)
{
    $stm = dbQuery($pQuery, $pParams);
    $items = $stm->fetch(PDO::FETCH_ASSOC);
    $stm->close();

    return $items;
}

/**
 * Execute a query and return a list of items
 *
 * @param string  $pQuery  The SQL query to execute
 * @param array   $pParams The parameters
 *
 * @return array
 */
function dbExFetchAll($pQuery, $pParams = null)
{
    $stm = dbQuery($pQuery, $pParams);
    $items = iterator_to_array($stm);
    $stm->close();

    return $items;
}


/**
 * Executes an SQL query and returns the PDOStatement.
 * Do not forget to closeCursor().
 *
 * If $pParams is given a prepared statement is used.
 *
 * @param string $pQuery  The SQL query to execute
 * @param array  $pParams The parameters to bind to the query
 *
 * @return PDOStatement
 */
function dbQuery($pQuery, $pParams = null)
{
    global $dbLastInsertedTable;

    if (preg_match('/[\s\n\t]*INSERT[\t\n ]+INTO[\t\n ]+([a-z0-9\_\-]+)/is', $pQuery, $matches)) {
        $dbLastInsertedTable = $matches[1];
    }

    if ($pParams !== null) {
        $stm = dbConnection()->prepare($pQuery);

        if (!is_array($pParams)) {
            $pParams = array($pParams);
        }
        $stm->execute($pParams);
        return dbConnection()->getDataFetcher($stm);
    } else {
        return dbConnection()->query($pQuery);
    }
}


/**
 * Executes an SQL statement and returns the number of affected rows.
 *
 * If $pParams is given a prepared statement is used.
 *
 * @param        $pQuery
 * @param array  $pParams The parameters to bind to the query
 *
 * @return int
 */

function dbExec($pQuery, $pParams = null)
{
    $stmt = dbQuery($pQuery, $pParams);
    $int = $stmt->count();
    dbFree($stmt);

    return $int;
}

/**
 *
 *
 * @param      $pTable
 * @param      $pCount
 * @param bool $pWhere
 *
 * @return array
 */
function dbTableLang($pTable, $pCount = -1, $pWhere = false)
{
    if ($_REQUEST['lang']) {
        $lang = esc($_REQUEST['lang']);
    } else {
        $lang = Core\Kryn::$language;
    }
    if ($pWhere) {
        $pWhere = " lang = '$lang' AND " . $pWhere;
    } else {
        $pWhere = "lang = '$lang'";
    }

    return dbTableFetch($pTable, $pCount, $pWhere);
}


/**
 * Returns first item based on pWhere, pTable.
 *
 * @param string  $pTable  The table name based on your extension table definition.
 * @param mixed   $pWhere  condition object or string
 * @param string  $pFields Comma separated list of the columns
 *
 * @return array
 */
function dbTableFetch($pTable, $pWhere = '', $pFields = '*')
{
    $table = dbTableName($pTable);

    if ($pFields != '*') {
        $pFields = dbQuote($pFields);
    }

    $sql = "SELECT $pFields FROM $table";
    $data = array();
    if ($pWhere !== '') {
        if (is_array($pWhere)) {
            $pWhere = \Core\Config\Condition::create($pWhere)->toSql($data, $pTable);
        }
        $sql .= " WHERE $pWhere";
    }

    return dbExFetch($sql, $data);
}

/**
 * Returns a list of items from pTable limited with pWhere.
 *
 * @param string  $pTable  The table name based on your extension table definition.
 * @param mixed   $pWhere  condition object or string
 * @param string  $pFields Comma separated list of the columns
 *
 * @return array
 */
function dbTableFetchAll($pTable, $pWhere = '', $pFields = '*')
{
    $table = dbTableName($pTable);

    if ($pFields != '*') {
        $pFields = dbQuote($pFields);
    }

    $data = array();
    $sql = "SELECT $pFields FROM $table";
    if ($pWhere !== '') {
        if (is_array($pWhere)) {
            $pWhere = \Core\Config\Condition::create($pWhere)->toSql($data, $pTable);
        }
        $sql .= " WHERE $pWhere";
    }

    return dbExFetchAll($sql, $data);
}

/**
 * Returns the table with prefix if $pTable does not start with / (slash)
 * This means, that if you use table names with a starting slash then the
 * framework won't add the prefix at the beginning.
 *
 * @param $pTable
 *
 * @return string
 */
function dbTableName($pTable)
{
    return strtolower((substr($pTable, 0, 1) == '/') ? $pTable : Core\Kryn::$config['database']['prefix'] . $pTable);
}

/**
 * Inserts the values based on pFields into the table pTable.
 *
 * @param string $pTable  The table name based on your extension table definition
 * @param array  $pData   Array as a key-value pair. key is the column name and the value is the value. More infos under http://www.Core\Kryn.org/docu/developer/framework-database
 *
 * @return integer The last_insert_id() (if you use auto_increment/sequences)
 */
function dbInsert($pTable, $pData)
{
    $table = dbTableName($pTable);
    $cols = array_keys($pData);

    foreach ($pData as $value) {
        $values[] = '?';
    }

    $query = 'INSERT INTO ' . $table
        . ' (' . implode(', ', $cols) . ')'
        . ' VALUES (' . implode(', ', $values) . ')';

    if (dbExec($query, array_values($pData))) {
        return dbLastId();
    } else {
        return false;
    }
}

/**
 * Converts $pItems to an array index with $pIndex
 *
 * @param $pItems
 * @param $pIndex
 *
 * @return array
 */
function dbToKeyIndex(&$pItems, $pIndex)
{
    $res = array();
    if (count($pItems) > 0) {
        foreach ($pItems as $item) {
            $res[$item[$pIndex]] = $item;
        }
    }

    return $res;
}

/**
 * Fetch the SQLSTATE associated with the last operation on the database handle.
 *
 * @global
 * @return mixed
 */

function dbError()
{
    return dbConnection()->errorCode();
}

/**
 *  Fetch extended error information associated with the last operation on the database handle.
 *
 * @global
 * @return array
 */

function dbErrorInfo()
{
    return dbConnection()->errorInfo();
}

/**
 * Returns PDO::lastInsertId
 *
 * @return mixed
 */
function dbLastId()
{
    if (\Core\Kryn::$config['database']['type'] == 'pgsql') {
        try {
            $row = dbExfetch('SELECT LASTVAL() as last_val');

            return $row['last_val'];
        } catch (\Exception $e) {
            return 0;
        }
    } else {
        return dbConnection()->lastInsertId();
    }
}

/**
 * Update a row or rows with the values based on pFields into the table pTable.
 *
 * @param string       $pTable     The table name based on your extension table definition
 * @param string|array $pCondition Define the limitation as a SQL or as a array ('field' => 'value')
 * @param array        $pData      Array as a key-value pair. key is the column name and the value is the value. More infos under http://www.Core\Kryn.org/docu/developer/framework-database
 *
 * @return type
 */
function dbUpdate($pTable, $pCondition = array(), $pData = array())
{
    $table = dbTableName($pTable);

    $fields = array();
    foreach ($pData as $column => $value) {
        $fields[] = $column . ' = ?';
    }

    $data = array_merge(array_values($pData), array_values($pCondition));

    $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $fields)
        . ' WHERE ' . implode('=? AND ', array_keys($pCondition)) . '= ?';

    return dbExec($sql, $data) ? true : false;
}

/**
 * Deletes rows from the table based on the pWhere
 *
 * @param string       $pTable The table name based on your extension table definition
 * @param string|array $pWhere Do not forget this, otherwise the table will be truncated. You can use array as in
 *
 * @return bool
 */
function dbDelete($pTable, $pWhere = '')
{
    $table = dbTableName($pTable);

    $data = array();
    $sql = "DELETE FROM " . $table . "";
    if (is_string($pWhere) && $pWhere) {
        $sql .= " WHERE $pWhere ";
    }
    if (is_array($pWhere)) {
        $sql .= " WHERE " . \Core\Config\Condition::create($pWhere)->toSql($data, $pTable);
    }

    return dbExec($sql, $data);
}

/**
 * Returns the number of rows affected by the last SQL statement
 *
 * @param $pStatement
 *
 * @return int
 */
function dbNumRows($pStatement)
{
    return $pStatement->rowCount();
}

/**
 * Closes the cursor, enabling the statement to be executed again
 *
 * @param $pStatement
 *
 * @return bool
 */
function dbFree($pStatement)
{
    if ($pStatement && $pStatement instanceof \PDOStatement) {
        return $pStatement->closeCursor();
    }
}

/**
 * Fetches the next row from a result set
 *
 * @param PDOStatement  $pStatement
 *
 * @return array
 */
function dbFetch($pStatement)
{
    return !$pStatement ? false : $pStatement->fetch(PDO::FETCH_ASSOC);
}

/**
 * Returns an array containing all of the result set rows
 *
 * @param PDOStatement  $pStatement
 *
 * @return array
 */
function dbFetchAll($pStatement)
{
    return !$pStatement ? false : $pStatement->fetchAll(PDO::FETCH_ASSOC);
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
function dbOrderToSql($pValues, $pTable = '')
{
    $sql = ' ORDER BY ';

    if (count($pValues) == 1 && !is_array($pValues[0])) {
        return $sql . dbQuote(key($pValues), $pTable) . ' ' . ((strtolower(
            current($pValues)
        ) == 'asc') ? 'ASC' : 'DESC');
    }

    if (is_numeric(key($pValues[0]))) {

        foreach ($pValues as $order) {
            $sql .= dbQuote($order['field'], $pTable) . ' ' . ((strtolower(
                $order['direction']
            ) == 'asc') ? 'ASC' : 'DESC') . ',';
        }
    }

    if (!is_numeric(key($pValues[0]))) {

        foreach ($pValues as $key => $order) {
            $sql .= dbQuote($key, $pTable) . ' ' . ((strtolower($order) == 'asc') ? 'ASC' : 'DESC') . ',';
        }
    }

    return substr($sql, 0, -1);
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
function dbCount($pTable, $pWhere = false)
{
    $table = dbQuote(dbTableName($pTable));

    if (Core\Kryn::$config['database']['type'] == 'pgsql') {
        $sql = "SELECT 1 FROM $table";
        if ($pWhere != false) {
            $sql .= " WHERE $pWhere ";
        }

        $res = dbQuery($sql);

        $count = dbNumRows($res);

        dbFree($res);

        return $count;

    } else {
        $sql = "SELECT count(*) as " . dbQuote('counter') . " FROM $table";
        if ($pWhere != false) {
            $sql .= " WHERE $pWhere ";
        }

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
 *
 * @return string
 */
function dbValuesToCommaSeperated($pValues)
{
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

        $values[] = is_numeric($val) ? $val : "'" . esc($val) . "'";
    }

    return array(
        implode(', ', $fields),
        implode(', ', $values)
    );
}

/**
 * Extracts all field names from a Order array.
 *
 * @param        $pValues
 * @param string $pTable
 *
 * @return array
 */
function dbExtractOrderFields($pValues, $pTable = '')
{
    $fields = array();

    if (count($pValues) == 1 && !is_array($pValues[0])) {
        return array(dbQuote(key($pValues), $pTable));
    }

    if (is_numeric(key($pValues[0]))) {

        foreach ($pValues as $order) {
            $fields[] = dbQuote($order['field'], $pTable);
        }
    }

    if (!is_numeric(key($pValues[0]))) {

        foreach ($pValues as $key => $order) {
            $fields[] = dbQuote($key, $pTable);
        }
    }

    return $fields;
}

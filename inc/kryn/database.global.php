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

function dbConnect() {
    global $kdb, $cfg;

    if ($kdb) return;

    $kdb = new database(
        $cfg['db_type'],
        $cfg['db_server'],
        $cfg['db_user'],
        $cfg['db_passwd'],
        $cfg['db_name'],
        ($cfg['db_pdo'] + 0 == 1 || $cfg['db_pdo'] === '') ? true : false,
        ($cfg['db_forceutf8'] == '1') ? true : false
    );

    if (!$kdb->isActive()) {
        kryn::internalError('Can not connect to the database. Error: ' . $kdb->lastError());
    }

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
function dbExfetch($pSql, $pRowCount = 1) {
    global $kdb, $cfg;

    dbConnect();

    $pSql = str_replace('%pfx%', $cfg['db_prefix'], $pSql);
    return $kdb->exfetch($pSql, $pRowCount);
}


/**
 * Execute a query and return the resultset
 *
 * @param string $pSql
 *
 * @return array
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


function dbTableLang($pTable, $pCount = -1, $pWhere = false) {
    if ($_REQUEST['lang'])
        $lang = $_REQUEST['lang'];
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
 * @return type
 */
function dbTableFetch($pTable, $pCount = -1, $pWhere = false, $pFields = '*') {

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
    return (substr($pTable,0,1) == '/')?$pTable:pfx.$pTable;
}

/**
 * Inserts the values based on pFields into the table pTable.
 *
 * @param string $pTable  The table name based on your extension table definition
 * @param array  $pFields Array as a key-value pair. key is the column name and the value is the value. More infos under http://www.kryn.org/docu/developer/framework-database
 *
 * @return integer The last_insert_id() (if you use auto_increment/sequences)
 */
function dbInsert($pTable, $pFields) {

    $options = database::getOptions($pTable);

    if (substr($pTable,1)!='/')
        $table = pfx.$pTable;

    $sql = "INSERT INTO $table (";

    $fields = array();
    foreach ($pFields as $key => $field) {
        if ($options[$key])
            $fields[$key] = $field;
        else if ($options[$field])
            $fields[] = $field;
    }

    $sqlFields = '';
    $sqlInsert = '';

    foreach ($fields as $key => $field) {

        if (is_numeric($key)) {
            $fieldName = $field;
            $val = getArgv($field);
        } else {
            $fieldName = $key;
            $val = $field;
        }

        if (!$options[$fieldName]) continue;

        $sqlFields .= "$fieldName,";

        if ($options[$fieldName]['escape'] == 'int') {
            $sqlInsert .= ($val + 0) . ",";
        } else {
            $sqlInsert .= "'" . esc($val) . "',";
        }
    }

    $sqlInsert = substr($sqlInsert, 0, -1);
    $sqlFields = substr($sqlFields, 0, -1);

    $sql .= " $sqlFields ) VALUES( $sqlInsert )";

    if (dbExec($sql))
        return database::last_id();
    else
        return false;
}


function dbToKeyIndex(&$pItems, $pIndex) {
    $res = array();
    if (count($pItems) > 0)
        foreach ($pItems as $item) {
            $res[$item[$pIndex]] = $item;
        }
    return $res;
}

function dbError() {
    global $kdb;

    return $kdb->lastError();

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

    $options = database::getOptions($pTable);

    $table = dbTableName($pTable);

    $sql = "UPDATE $table SET ";

    if (is_array($pPrimary)) {
        $where = ' ';
        foreach ($pPrimary as $fieldName => $fieldValue) {
            if (!$options[$fieldName]) continue;

            $where .= '' . $fieldName . ' ';
            if ($options[$fieldName]['escape'] == 'int') {
                $where .= ' = ' . ($fieldValue + 0) . " AND ";
            } else {
                $where .= " = '" . esc($fieldValue) . "' AND ";
            }
        }

        $where = substr($where, 0, -4);
    } else {
        $where = $pPrimary;
    }

    $sqlInsert = '';
    foreach ($pFields as $key => $field) {

        if (is_numeric($key)) {
            $fieldName = $field;
            $val = getArgv($field);
        } else {
            $fieldName = $key;
            $val = $field;
        }

        if (!$options[$fieldName]) continue;

        $sqlInsert .= "$fieldName";

        if ($options[$fieldName]['escape'] == 'int') {
            $sqlInsert .= ' = ' . ($val + 0) . ",";
        } else {
            $sqlInsert .= " = '" . esc($val) . "',";
        }
    }

    $sqlInsert = substr($sqlInsert, 0, -1);
    $sql .= " $sqlInsert WHERE $where ";
    return dbExec($sql);
}

/**
 * Deletes rows from the table based on the pWhere
 *
 * @param type $pTable The table name based on your extension table definition
 * @param type $pWhere Do not forget this, otherwise the table will be truncated.
 */
function dbDelete($pTable, $pWhere = false) {

    $table = dbTableName($pTable);

    $sql = "DELETE FROM " . $table . "";
    if ($pWhere != false)
        $sql .= " WHERE $pWhere ";
    dbExec($sql);
}

function dbCount($pTable, $pWhere = false) {
    $table = dbTableName($pTable);
    $sql = "SELECT count(*) as count FROM $table";
    if ($pWhere != false)
        $sql .= " WHERE $pWhere ";
    $row = dbExfetch($sql);
    return $row['count'];
}

/**
 * Fetch a row based on the specified Resultset from dbExec()
 *
 * @param type $pRes   The result of dbExec()
 * @param type $pCount Defines how many items the function returns
 *
 * @return type
 */
function dbFetch($pRes, $pCount = 1) {
    global $kdb;
    return $kdb->fetch($pRes, $pCount);
}

?>
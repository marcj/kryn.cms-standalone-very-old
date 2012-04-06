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
 * Class to handle/sync the database table schemes from config.json "db".

 */

define('DB_PRIMARY', 1);
define('DB_INDEX', 2);

class adminDb {

    function install($pModuleConfig) {
        if (is_array($pModuleConfig['db']))
            return self::_install($pModuleConfig['db']);
        return false;
    }

    function remove($pModuleConfig) {
        if (!is_array($pModuleConfig['db'])) return false;
        self::_remove($pModuleConfig['db']);
        return true;
    }

    function _remove($pDb) {
        foreach ($pDb as $tableName => $tableFields) {
            $sql = "DROP TABLE %pfx%$tableName";
            dbExec($sql);
        }
    }

    function _install($pDb) {
        global $cfg, $kdb;

        $db = &$pDb;

        if (!count($db) > 0)
            return 'No Tables.';

        $ttables = database::getAllTables();
        if (count($ttables) > 0) {
            foreach ($ttables as $table) {
                $tables[$table] = true;
            }
        }

        $res = '';

        foreach ($db as $tableName => $tableFields) {
            $tableName = strtolower(pfx . $tableName);

            if ($tables[$tableName]) {
                self::_updateTable($tableName, $tableFields);
                $res .= "Update table <i>$tableName</i>\n";
            } else {
                self::_installTable($tableName, $tableFields);
                $res .= "Create table <i>$tableName</i>\n";
            }

            //check index bundles
            if ($tableFields['___index']){
                foreach ($tableFields['___index'] as $indexBundle){
                    $indexName = preg_replace('/\W/', '-', $indexBundle);
                    dbExec('CREATE INDEX '.strtolower($indexName).' ON '.$tableName.' ('.$indexBundle.')');
                }
            }

            database::clearOptionsCache($tableName);
        }
        $res .= "\nDatabase installed.\n";

        $kdb->updateSequences($db);
        return $res;
    }


    function _updateTable($pTable, $pFields) {
        global $cfg;

        self::updateIndexes($pTable, $pFields, false); //delete all and don't create new

        $column = array();
        $columns = database::getColumns($pTable);

        $primaries = array();

        foreach ($pFields as $fName => $fOptions) {

            if ($fName == '___index') continue;

            if (!array_key_exists($fName, $columns)) {
                self::addColumn($pTable, $fName, $fOptions);
            } else {
                //found check type
                //
                $isType = $columns[$fName]['type'];
                $nType = $fOptions[0];

                if (strpos($isType, '(') !== false) {
                    $temp = explode('(', $isType);
                    $isType = $temp[0];
                    if ($isType == 'varchar')
                        $varcharLength = str_replace(')', '', $temp[1]);
                }
                if ($isType == 'integer')
                    $isType = 'int';

                if ($pTable == 'kryn_publication_news_category') {
                }
                if ($isType != $nType || ($isType == 'varchar' && $varcharLength != $fOptions[1])) {

                    $sql = self::addColumn($pTable, $fName, $fOptions, 2);

                    if ($cfg['db_type'] == 'mysql' || $cfg['db_type'] == 'mysqli') {
                        $sql = 'ALTER TABLE ' . $pTable . ' CHANGE COLUMN ' . $fName . ' ' . $sql;
                    } else {
                        $sql = 'ALTER TABLE ' . $pTable . ' ALTER COLUMN ' . $sql;
                    }
                    dbExec($sql);
                }

                if ($fOptions[2] == 'DB_PRIMARY')
                    $primaries[] = $fName;

            }
        }

        //check primary index
        if (count($primaries) > 0){
            $name = implode(',', $primaries);
            dbExec('CREATE INDEX ' . preg_replace('/\W/', '_', $name) . ' ON ' . $pTable . ' (' . $name . ')');
        }


        foreach ($columns as $fieldName => &$field) {
            if (!array_key_exists($fieldName, $pFields)) {
                //there exists a column in the database, which isn't in the config.json
                //delete it
                dbExec("ALTER TABLE $pTable DROP $fieldName");
            }
        }

        self::updateIndexes($pTable, $pFields, true); //delete all and create new
    }

    public static function _installTable($pTable, $pFields) {
        global $cfg;
        $sql = 'CREATE TABLE ' . $pTable . ' (' . "\n";

        $primaries = '';

        foreach ($pFields as $fName => $fOptions) {

            if ($fName == '___index') continue;

            $sql .= self::addColumn($pTable, $fName, $fOptions, 1) . ", \n";

            if ($fOptions[2] == "DB_PRIMARY")
                $primaries .= '' . $fName . ',';
        }

        $primaries = substr($primaries, 0, -1);

        if ($primaries == '')
            $sql = substr($sql, 0, -1);
        else
            $sql .= ' PRIMARY KEY ( ' . $primaries . ' )';

        $sql .= "\n )";

        if ($cfg['db_type'] == 'mysql' || $cfg['db_type'] == 'mysqli')
            $sql .= 'ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;';

        dbExec($sql);

        self::updateIndexes($pTable, $pFields, true); //delete all and create new
    }

    public static function deleteIndex($pName, $pTable) {
        global $cfg;

        switch ($cfg['db_type']) {
            case 'mysql':
            case 'mysqli':
                dbExec('DROP INDEX ' . $pName . ' ON ' . $pTable);
                break;
            case 'postgresql':
            case 'sqlite':
                dbExec('DROP INDEX IF EXISTS ' . $pName);
                break;
        }
    }

    public static function updateIndexes($pTable, $pFields, $pCreate = true) {
        global $cfg;

        //dont throw error's to log
        database::$hideSql = true;
        foreach ($pFields as $fName => $fOptions) {

            $indexName = 'kryn_idx_' . $pTable . '_' . $fName;
            self::deleteIndex($indexName, $pTable);
            self::deleteIndex($fName, $pTable);

            if ($fOptions[2] == "DB_INDEX" || $fOptions[2] == "DB_FULLTEXT") { //DB_FULLTEXT deprecated since 1.0
                if ($pCreate) {
                    if ($fOptions[0] == 'text')
                        $fName .= '(255)';

                    dbExec('CREATE INDEX ' . $indexName . ' ON ' . $pTable . ' (' . $fName . ')');
                }
            }
        }
        database::$hideSql = false;

    }

    public static function addColumn($pTable, $pFieldName, $pFieldOptions, $pMode = false) {

        /*
        * $pMode
        *  false: full sql
        *  1: only the column definition
        *  2: only the column definition for ALTER COLUMN
        *
        */
        global $cfg;

        $sqlBegin = 'ALTER TABLE '.strtolower($pTable).' ADD ';

        $sql = strtolower($pFieldName).' ';


        if ($cfg['db_type'] == 'postgresql' && $pMode == 2) {
            $sql .= 'TYPE ';
        }

        $field = strtolower($pFieldOptions[0]);
        $unsigned = false;

        if (strpos($field, ' unsigned') !== false){
            $unsigned = true;
            $field = str_replace(' unsigned', '', $field);
        }
        switch ($field) {

            case 'char':
                $sql .= 'char( ' . $pFieldOptions[1] . ' ) '; break;
            case 'varchar':
                $sql .= 'varchar( ' . $pFieldOptions[1] . ' ) '; break;
            case 'text':
                $sql .= 'text '; break;

            case 'enum': //deprecated since 1.0
                $sql .= 'varchar(255) ';
                break;

            //dates
            case 'date':
                $sql .= 'date '; break;
            case 'time':
                $sql .= 'time '; break;
            case 'timestamp':
                $sql .= 'timestamp '; break;


            //numerics
            case 'boolean':
                $sql .= 'boolean '; break;

            case 'smallint':
                $sql .= 'smallint '; break;

            case 'int':
            case 'integer':
                $sql .= 'integer ';break;

            case 'decimal':
                $sql .= 'decimal( ' . $pFieldOptions[1] . ' ) '; break;

            case 'bigint':
                $sql .= 'bigint ';break;

            case 'float4':
                if ($cfg['db_type'] == 'postgresql')
                    $sql .= 'float4 ';
                else
                    $sql .= 'float ';
                break;

            case 'double precision':
                $sql .= 'double precision '; break;

        }

        if ($unsigned && $cfg['db_type'] != 'postgresql')
            $sql .= ' UNSIGNED ';

        if ($pFieldOptions[2] == "DB_PRIMARY")
            $sql .= 'NOT NULL ';
        else
            $sql .= 'NULL ';

        //auto increment
        if ($pFieldOptions[3] == true) {

            if ($cfg['db_type'] == 'mysql' || $cfg['db_type'] == 'mysqli') {
                $sql .= ' AUTO_INCREMENT ';
            }

            //http://www.sqlite.org/faq.html#q1
            //if( $cfg['db_type'] == 'sqlite' ){
            //	$sql .= ' AUTOINCREMENT ';
            //}

            if ($cfg['db_type'] == 'postgresql') {
                database::$hideSql = true;
                try {
                    dbExec('CREATE SEQUENCE kryn_' . $pTable . '_seq;');
                    dbExec('ALTER SEQUENCE kryn_' . $pTable . '_seq RESTART WITH 1');
                } catch (Exception $e){
                    //force silence
                }
                database::$hideSql = false;
                $sql .= " DEFAULT nextval('kryn_" . $pTable . "_seq') ";
            }

        }

        if ($pMode)
            return $sql;

        $sql .= ';';
        dbExec($sqlBegin . $sql);
    }

}

?>

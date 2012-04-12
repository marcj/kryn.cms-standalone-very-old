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

    public static function install($pModuleConfig) {
        $res = '';
        if (is_array($pModuleConfig['db']))
            $res .= self::_install($pModuleConfig['db']);

        if (is_array($pModuleConfig['objects'])){
            foreach ($pModuleConfig['objects'] as $objectKey => $object){
                $res .= adminDb::installObjectTable($objectKey);
            }
        };

        $res .= "\nDatabase installed.\n";
        return $res;
    }

    public static function checkObjectTable($pObjectTable){

        $res = '';

        foreach (kryn::$objects as $objectKey => $object){
            if ($object['table'] && $object['table'] == $pObjectTable){
                $res .= self::installObjectTable($objectKey);
            }
        }

        return $res;
    }

    public static function installObjectTable($pObjectKey){

        $objectKey = $pObjectKey;
        $object = kryn::$objects[$objectKey];
        if (!$object || !$object['tableSync'] || !$object['table']) return false;

        $typesMap = array(
            'text' => array('textarea', 'wysiwyg', 'codemirror', 'array', 'layoutelement', 'checkboxgroup', 'filelist'),
            'int' => array('number')
        );

        $tables = array();

        $table = array();
        foreach ($object['fields'] as $fieldKey => $field){
            $type = 'varchar';
            $length = 255;
            $autoincrement = false;
            $mode = '';

            if ($field['primaryKey'])
                $mode = 'DB_PRIMARY';

            if ($field['dbIndex'])
                $mode = 'DB_INDEX';

            if ($field['autoIncrement'])
                $autoincrement = true;

            if ($field['type'] == 'text' || $field['type'] == 'password' || $field['type'] == 'number'){
                if ($field['maxlength'] && $field['maxlength'] <= 255)
                    $length = $field['maxlength'];
            }

            if ($field['type'] == 'object'){

                $definition = kryn::$objects[$field['object']];
                if (!$definition){
                    klog('adminDb', 'The wished object cant be found: '.$field['object'].' for field '.$fieldKey.' '.
                                    'during the initializing of table '.$object['table'].' for object'.$pObjectKey);
                    continue;
                }

                if (!$definition['fields']){
                    klog('adminDb', 'The wished object doesnt have any fields: '.$field['object'].' for field '.$fieldKey.' '.
                        'during the initializing of table '.$object['table'].' for object'.$pObjectKey);
                    continue;
                }

                if ($definition && $definition['fields']){

                    if ($field['object_relation'] == 'nToM'){

                        $relTable = array();
                        $leftPrimaryKeys = array();

                        $relTableName = $field['object_relation_table'];
                        if (!$relTableName)
                            $relTableName = 'relation_'.$objectKey.'_'.$field['object'];

                        foreach ($object['fields'] as $relFieldKey => $relField){

                            if ($relField['primaryKey']){
                                $relTable[$objectKey.'_'.$relFieldKey] = array(
                                    ($relField['type'] == 'number')?'int':'varchar',
                                    $relField['maxlength']?$relField['maxlength']:(($relField['type'] == 'number')?'':'255')
                                );
                                $leftPrimaryKeys[] = $objectKey.'_'.$relFieldKey;
                            }
                        }
                        $relTable['___index'][] = implode(',', $leftPrimaryKeys);

                        foreach ($definition['fields'] as $relFieldKey => $relField){
                            if ($relField['primaryKey']){
                                $relTable[$field['object'].'_'.$relFieldKey] = array(
                                    ($relField['type'] == 'number')?'int':'varchar',
                                    $relField['maxlength']?$relField['maxlength']:(($relField['type'] == 'number')?'':'255')
                                );
                                $leftPrimaryKeys[] = $relFieldKey;
                            }
                        }

                        $tables[$relTableName] = $relTable;


                    } else {
                        //n-1

                        $primaryKeys = array();
                        $lastPrimaryKey = array();

                        foreach ($definition['fields'] as $dKey => $dField){
                            if ($dField['primaryKey']){
                                $primaryKeys[$dKey] = $dField;
                                $lastPrimaryKey = $dField;
                            }
                        }

                        if (count($primaryKeys) == 1){
                            $type = ($lastPrimaryKey['type'] == 'number')?'int':'varchar';
                            $length = $lastPrimaryKey['maxlength']?$lastPrimaryKey['maxlength']:(($lastPrimaryKey['type'] == 'number')?'':'255');

                        } else if(count($primaryKeys) > 1){

                            $index = array();
                            foreach ($primaryKeys as $pKey => $pField){
                                $index[] = $pKey;

                                $table[ $fieldKey.'_'.$pKey ] = array(
                                    ($pField['type'] == 'number')?'int':'varchar',
                                    $pField['maxlength']?$pField['maxlength']:(($pField['type'] == 'number')?'':'255')
                                );

                            }

                            continue;
                        }
                    }
                }
            }

            if (in_array($field['type'], $typesMap['text'])){
                $type = 'text';
                $length = '';
            }

            if ($field['type'] == 'number'){
                $type = 'int';
                $length = '';
            }

            $table[$fieldKey] = array(
                $type,
                $length,
                $mode,
                $autoincrement
            );
        }

        $tables[$object['table']] = $table;

        return self::_install($tables);
    }

    public static function remove($pModuleConfig) {
        if (!is_array($pModuleConfig['db'])) return false;
        self::_remove($pModuleConfig['db']);
        return true;
    }

    public static function _remove($pDb) {
        foreach ($pDb as $tableName => $tableFields) {
            $sql = "DROP TABLE %pfx%$tableName";
            try {
                @dbExec($sql);
            } catch(Exception $e){

            }
        }
    }

    private static function _install($pDb) {
        global $kdb;

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

                    $indexName = dbQuote(preg_replace('/\W/', '_', $indexBundle));
                    $indexFields = explode(',', $indexBundle);
                    $indexFields = implode(', ', dbQuote($indexFields));

                    try {
                        dbExec('CREATE INDEX '.strtolower($indexName).' ON '.dbQuote($tableName).' ('.$indexFields.')');
                    } catch (Exception $e){

                    }
                }
            }

            database::clearOptionsCache($tableName);
        }

        $kdb->updateSequences($db);
        return $res;
    }


    private static function _updateTable($pTable, $pFields) {
        global $cfg;

        self::updateIndexes($pTable, $pFields, false); //delete all and don't create new

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

                if ($isType != $nType || ($isType == 'varchar' && $varcharLength != $fOptions[1])) {

                    $sql = self::addColumn($pTable, $fName, $fOptions, 2);

                    if ($cfg['db_type'] == 'mysql' || $cfg['db_type'] == 'mysqli') {
                        $sql = 'ALTER TABLE ' . dbQuote($pTable) . ' CHANGE COLUMN ' . dbQuote($fName) . ' ' . $sql;
                    } else {
                        $sql = 'ALTER TABLE ' . dbQuote($pTable) . ' ALTER COLUMN ' . $sql;
                    }
                    dbExec($sql);
                }

                if ($fOptions[2] == 'DB_PRIMARY')
                    $primaries[] = $fName;

            }
        }

        //check primary index
        if (count($primaries) > 0){
            $fields = implode(',', dbQuote($primaries));
            $name = str_replace(' ', '', implode(',', $primaries));
            dbExec('CREATE INDEX ' . dbQuote(preg_replace('/\W/', '_', $name)) . ' ON ' . dbQuote($pTable) . ' (' . $fields . ')');
        }

        self::updateIndexes($pTable, $pFields, true); //delete all and create new
    }

    public static function _installTable($pTable, $pFields) {
        global $cfg;
        $sql = 'CREATE TABLE ' . dbQuote($pTable) . ' (' . "\n";

        $primaries = '';

        foreach ($pFields as $fName => $fOptions) {

            if ($fName == '___index') continue;

            $sql .= self::addColumn($pTable, $fName, $fOptions, 1) . ", \n";

            if ($fOptions[2] == "DB_PRIMARY")
                $primaries .=  dbQuote($fName) . ',';
        }

        $primaries = substr($primaries, 0, -1);

        if ($primaries == '')
            $sql = substr($sql, 0, -3);
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

        try {
            switch ($cfg['db_type']) {
                case 'mysql':
                case 'mysqli':
                    dbExec('DROP INDEX ' . dbQuote($pName) . ' ON ' . dbQuote($pTable));
                    break;
                case 'postgresql':
                case 'sqlite':
                    dbExec('DROP INDEX IF EXISTS ' . dbQuote($pName));
                    break;
            }
            return true;
        } catch(Exception $e){
            return false;
        }
    }

    public static function updateIndexes($pTable, $pFields, $pCreate = true) {
        global $cfg;

        //dont throw error's to log
        database::$hideSql = true;
        $primaries = array();
        foreach ($pFields as $fName => $fOptions) {

            if ($fName == '___index') continue;

            $indexName = $pTable . '_' . $fName;
            self::deleteIndex($indexName, $pTable);
            self::deleteIndex($fName, $pTable);

            if ($fOptions[2] == 'DB_PRIMARY'){
                $primaries[] = $fName;
            }

            if ($fOptions[2] == "DB_INDEX" || $fOptions[2] == "DB_FULLTEXT") { //DB_FULLTEXT deprecated since 1.0
                if ($pCreate) {
                    if ($fOptions[0] == 'text')
                        $fName .= '(255)';

                    dbExec('CREATE INDEX ' . dbQuote($indexName) . ' ON ' . dbQuote($pTable) . ' (' . dbQuote($fName) . ')');
                }
            }
        }

        //check primary index
        if (count($primaries) > 0){
            $name = str_replace(' ', '', implode(',', $primaries));
            self::deleteIndex(preg_replace('/\W/', '_', $name), $pTable);
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

        $sqlBegin = 'ALTER TABLE '.dbQuote(strtolower($pTable)).' ADD ';

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

        //if ($pFieldOptions[2] == "DB_PRIMARY")
        //    $sql .= 'NOT NULL ';

        //auto increment
        if ($pFieldOptions[3] == true) {

            if ($cfg['db_type'] == 'mysql' || $cfg['db_type'] == 'mysqli') {
                $sql .= ' AUTO_INCREMENT ';
            }

            if ($cfg['db_type'] == 'postgresql') {
                database::$hideSql = true;
                try {
                    @dbExec('CREATE SEQUENCE kryn_' . $pTable . '_seq;');
                    @dbExec('ALTER SEQUENCE kryn_' . $pTable . '_seq RESTART WITH 1');
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
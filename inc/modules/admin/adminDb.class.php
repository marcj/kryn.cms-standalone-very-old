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
 *
 * Class to handle/sync the database table schemes from config.json "db".
 *
 */

define( 'DB_PRIMARY', 1 );
define( 'DB_INDEX', 2 );

class adminDb {

    function install( $pModuleConfig, $pDelete4Install = false ){
        return self::_install( $pModuleConfig['db'], $pDelete4Install );
    }

    function remove( $pModuleConfig ){
        if( !is_array($pModuleConfig['db']) ) return false;
        self::_remove( $pModuleConfig['db'] );
        return true;
    }

    function _remove( $pDb ){
        foreach( $pDb as $tableName => $tableFields ){
            $sql = "DROP TABLE %pfx%$tableName";
            dbExec( $sql );
        }
    }
    
    public function checkPostgres(){
        
        dbExec("create aggregate array_accum (
            sfunc = array_append,
            basetype = anyelement,
            stype = anyarray,
            initcond = '{}'
            );");

        dbExec("
            CREATE OR REPLACE FUNCTION group_concat(text, text)
            RETURNS text AS $$
            SELECT CASE
            WHEN $2 IS NULL THEN $1
            WHEN $1 IS NULL THEN $2
            ELSE $1 operator(pg_catalog.||) ',' operator(pg_catalog.||) $2
            END
            $$ IMMUTABLE LANGUAGE SQL;");

        dbExec("
            CREATE AGGREGATE group_concat (
            BASETYPE = text,
            SFUNC = group_concat,
            STYPE = text
            );");
        
    }

    function _install( $pDb, $pDelete4Install = false ){
        global $cfg, $kdb, $kryn;

        $db = &$pDb;
        
        if( $kdb->type == 'postgresql' ){
            self::checkPostgres();
        }

        if(! count($db) > 0 )
            return 'No Tables.';

        if( $pDelete4Install == true ){
            foreach( $db as $tableName => $tableFields ){
                $tableName = pfx . $tableName;
                dbExec("DROP TABLE IF EXISTS $tableName");
            }
        } else {
        	$ttables = database::getAllTables();
            if( count($ttables) > 0 ){
                foreach( $ttables as $table ){
                    $tables[ $table ] = true;
                }
            }
        }

        foreach( $db as $tableName => $tableFields ){
            $tableName = strtolower(pfx . $tableName);
            
            if( $tables[$tableName] ){
                self::updateIndexes( $tableName, $tableFields, false ); //delete all
                self::_updateTable( $tableName, $tableFields );
                $res .= "Update table <i>$tableName</i>\n";
                $res .= self::updateIndexes( $tableName, $tableFields );
            } else {
                self::_installTable( $tableName, $tableFields );
                $res .= "Create table <i>$tableName</i>\n";
                $res .= self::updateIndexes( $tableName, $tableFields );
            }
            $kdb->tableInfos[$tableName] = $tableFields;
        }
        $res .= "\nDatabase installed.\n";
        
		database::readTables();
        database::updateSequences( $db );
        return $res;
    }
    

    function _updateTable( $pTable, $pFields ){
        global $cfg;
        
        $column = array();
        $columns = database::getColumns($pTable);
            
        foreach( $pFields as $fName => $fOptions ){

            if( !array_key_exists($fName, $columns) ){ //$column['Field'] != $fName ){
                self::addColumn( $pTable, $fName, $fOptions );
            } else {
                //found check type
                //
                $isType = $columns[$fName]['type'];
                $nType = $fOptions[0];
                
                if( strpos($isType, '(') !== false ){
                    $temp = explode('(', $isType);
                    $isType = $temp[0];
                    if( $isType == 'varchar' )
                        $varcharLength = str_replace(')', '', $temp[1]);
                }
                if( $isType == 'integer' )
                    $isType = 'int';

                if( $pTable == 'kryn_publication_news_category' ){
                }
                if( $isType != $nType ||
                    ($isType == 'varchar' && $varcharLength != $fOptions[1] ) ){
                    //different field type => alter this field

                    self::updateIndexes( $pTable, $fName, false ); //delete index if exists

                    $sql = self::addColumn( $pTable, $fName, $fOptions, 2 );
                    
                    if( $cfg['db_type'] == 'mysql' || $cfg['db_type'] == 'mysqli' ){
                        $sql = 'ALTER TABLE '.$pTable.' CHANGE COLUMN '.$fName.' '.$sql;
                    } else {
                        $sql = 'ALTER TABLE '.$pTable.' ALTER COLUMN '.$sql;
                    }
                    dbExec($sql);
                }
            }
        }
        
        foreach( $columns as $fieldName => &$field ){
            if( !array_key_exists($fieldName, $pFields) ){
                //there exists a column in the database, which isn't in the config.json
                //delete it
                dbExec("ALTER TABLE $pTable DROP $fieldName");
            }
        }
        
    }

    public static function _installTable( $pTable, $pFields ){
		global $cfg;
        $sql = 'CREATE TABLE '.$pTable.' ('."\n";

        $primaries = ''; 

        foreach( $pFields as $fName => $fOptions ){
            $sql .= self::addColumn( $pTable, $fName, $fOptions, 1 ) . ", \n";
            if( $fOptions[2] == "DB_PRIMARY" )
                $primaries .= '' . $fName . ',';
        }
        
        $primaries = substr( $primaries, 0, -1 );

        if( $primaries == '' )
            $sql = substr( $sql, 0, -1 );
        else
            $sql .= ' PRIMARY KEY ( ' . $primaries . ' )';

        $sql .= "\n )";
        
        if( $cfg['db_type'] == 'mysql' )
        	$sql .= 'ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;';

        dbExec( $sql );
    }

    public static function deleteIndex( $pName, $pTable ){
        global $cfg;
        
        switch( $cfg['db_type'] ){
    		case 'mysql':
    			dbExec('DROP INDEX '.$pName.' ON '.$pTable);
    			break;
    		case 'postgresql':
    		case 'sqlite':
    			dbExec('DROP INDEX IF EXISTS '.$pName);
    			break;
    	}
    }
    
    public static function updateIndexes( $pTable, $pFields, $pCreate = true ){
    	global $cfg;
    	
    	//dont throw error's to log
    	database::$hideSql = true;
    	
        foreach( $pFields as $fName => $fOptions ){
        	
        	$indexName = 'kryn_idx_'.$pTable.'_'.$fName;
        	self::deleteIndex( $indexName, $pTable );
        	self::deleteIndex( $fName, $pTable );
        	
            if( $fOptions[2] == "DB_INDEX" || $fOptions[2] == "DB_FULLTEXT" ){
                if( $pCreate ){
                    if( $fOptions[0] == 'text' )
                        $fName .= '(255)';
                        
        		    dbExec('CREATE INDEX '.$indexName.' ON '.$pTable.' ('. $fName .')');
                }
            }
        }
        database::$hideSql = false;
        
    }

    public static function addColumn( $pTable, $pFieldName, $pFieldOptions, $pMode = false ){
        
        /*
         * $pMode
         * 	false: full sql
         *  1: only the column definition
         *  2: only the column definition for ALTER COLUMN
         * 
         */
		global $cfg;
		
        $sqlBegin = "ALTER TABLE $pTable ADD ";

        $sql = "$pFieldName ";
        
        
        if( $cfg['db_type'] == 'postgresql' && $pMode == 2 ){
            $sql .= 'TYPE ';
        }
        
        
        switch( strtolower($pFieldOptions[0]) ){
        case 'int':
            $sql .= 'integer ';
            //if( $pFieldOptions[1] > 0 )
            //    $sql .= ' (' . $pFieldOptions[1] . ') ';
            break;
        case 'smallint':
            $sql .= 'smallint ';
        case 'bigint':
            $sql .= 'bigint ';
        case 'real':
            $sql .= 'real ';
        case 'double precision':
            $sql .= 'double precision ';
        case 'text':
            $sql .= 'text ';
            break;
        case 'varchar':
            $sql .= 'varchar( '.$pFieldOptions[1].' ) ';
            break;
        case 'enum':
        	if( $cfg['db_type'] == 'mysql' )
        		$sql .= 'enum( '.$pFieldOptions[1].' ) ';
        	else
        		$sql .= 'varchar(255) '; //CHECK ('.$pFieldName.' IN (  '.$pFieldOptions[1].'  )) ';
            break;
        }

        
        if( $cfg['db_type'] != 'postgresql' && $pFieldOptions[2] != "DB_PRIMARY" )
       		$sql .= ' NULL ';

        if( !$pMode && $pFieldOptions[2] == "DB_PRIMARY" )
            $sql .= 'PRIMARY KEY ';

        if( $pFieldOptions[3] == true ){
        	if( $cfg['db_type'] == 'mysql' ){
        		$sql .= ' AUTO_INCREMENT ';
        	}
        		
        	//if( $cfg['db_type'] == 'sqlite' ){
        	//	$sql .= ' AUTOINCREMENT ';
        	//}
        		
        	if( $cfg['db_type'] != 'mysql' && $cfg['db_type'] != 'mysqli' &&  $cfg['db_type'] != 'sqlite' ){
    			database::$hideSql = true;
        		dbExec('CREATE SEQUENCE kryn_'.$pTable.'_seq;');
        		dbExec('ALTER SEQUENCE kryn_'.$pTable.'_seq RESTART WITH 1');
    			database::$hideSql = false;
        		$sql .= " DEFAULT nextval('kryn_".$pTable."_seq') ";
        	}
        	
        }
            
        if( $pMode )
            return $sql;

        $sql .= ';';
        dbExec( $sqlBegin . $sql );
    }

    //obsolete since 0.6
    public static function addIndex( $pTable, $pField, $pType = 'INDEX' ){

        $pType = str_replace("DB_", "", $pType);

        $index['Key_name'] = '';
        $oldType = $pType;

        $equalFound = false;
        $indexExist = false;
		
        //postgres: http://manniwood.com/postgresql_stuff/index.html
        //prepare postgres for new function table_indexes
        if( $postgres ){
        	$type = dbExfetch("SELECT * FROM pg_type WHERE typname = 'kryn_fnc_table_indexes_result'", 1);
        	if( $type['typname'] != 'kryn_fnc_table_indexes_result' ){
        		
        		//create indexes function
        		dbExec("create type kryn_fnc_table_indexes_result as (
						    index_name text);
						
						create or replace function kryn_table_indexes(schmname text, tblname text) returns setof kryn_fnc_table_indexes_result as
						$body$
						declare
						    stmt text;
						    tblcount integer;
						    result idx_func_return_type%rowtype;
						begin
						    stmt := 'select count(*) '
						          ||   'from pg_class as tbl '
						          ||   'join pg_namespace as schm '
						          ||     'on tbl.relnamespace = schm.oid '
						          ||  'where schm.nspname = ''' || schmname || ''' '
						          ||    'and tbl.relname = ''' || tblname || ''' ';
						    execute stmt into tblcount;
						    if ( tblcount = 0 ) then
						        raise exception 'schema/table does not exist';
						    end if;
						
						    stmt := 'select idx_info.relname as index_name '
						          ||  'from pg_index as idx '
						          ||  'join pg_class as tbl on tbl.oid = idx.indrelid '
						          ||  'join pg_namespace as schm on tbl.relnamespace = schm.oid '
						          ||  'join pg_class as idx_info on idx.indexrelid = idx_info.oid '
						          || 'where schm.nspname = ''' || schmname || ''' '
						          ||   'and tbl.relname = ''' || tblname || ''' ';
						    for result in execute stmt loop
						        return next result;
						    end loop;
						    return;
						end;
						$body$ language 'plpgsql';
						commit;");
        		
        	}
        }
        
        
        $indexes = dbExfetch( "SHOW INDEX FROM $pTable", DB_FETCH_ALL );
        if( count($indexes) > 0 ){
            foreach( $indexes as $myindex ){
                if( $myindex['Key_name'] == $pField ){
                    $indexExist = true;

                    $index = $myindex;
                    $oldType = $myindex['Index_type'];
                    if( $myindex['Index_type'] == 'BTREE' )
                        $oldType = 'INDEX';

                    if( $oldType == $pType )
                        $equalFound = true;
                }
            }
        }

        if( $indexExist && !$equalFound )//key found but not with type == pType
            dbExec("ALTER TABLE $pTable DROP $oldType $pField");

        if(! $pIsIndex && $indexExist ){
            $sql = "ALTER TABLE $pTable DROP $oldType $pField";
        } elseif( !$indexExist ) {
            $sql = "ALTER TABLE $pTable ADD $pType ( $pField )";
        }

        if( $sql )
            dbExec( $sql );
    }

}

?>

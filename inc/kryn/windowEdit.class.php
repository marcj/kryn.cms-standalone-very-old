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
 * This class have to be used as motherclass in your framework classes, which
 * are defined from the links in your extension.
 * 
 * @author Kryn.labs <info@krynlabs.com>
 * @package Kryn
 * @subpackage FrameworkWindow
 * 
 */

class windowEdit {

    /**
     * 
     * Defines the table which should be accessed
     * @var string
     */
    public $table = '';
    
    /**
     * Defines your primary fiels as a array.
     * Example: $primary = array('rsn');
     * Example: $primary = array('id', 'name');
     * @abstract
     * @var array
     */
    public $primary = array();
    
     /**
     * 
     * Defines whether the list windows should display the language select box.
     * Note: Your table need a field 'lang' varchar(2). The windowList class filter by this.
     * @var bool
     */
    public $multiLanguage = false;
    
    
    /**
     *
     *
     */
    public $previewPlugins = false;
    
    /**
     * 
     * Defines the fields. (ka.fields)
     * @var array
     */
    public $fields = array();
    
     /**
     * 
     * Defines each tab and inside it the fields. (ka.fields)
     * @var array
     */
    public $tabFields = array();

    
    /**
     * Defines whether the versioning for this form is enabled or not
     * @var boolean
     */
    public $versioning = false;
    
    
    /**
     * Initialize $fields. Called when opened the window.
     * @return windowEdit 
     */
    public function init( $pAndLoadPreviewPages ){
        $this->_fields = array();
        if( $this->fields ){
            $this->prepareFieldItem( $this->fields );
        }
        if( $this->tabFields ){
            foreach( $this->tabFields as &$field )
                $this->prepareFieldItem( $field );
        }
        
        if( $pAndLoadPreviewPages )
            $this->loadPreviewPages();
        
        return $this;
    }
    
    /**
     * Loads all pages which have included the plugin in $previewPlugins
     *
     */
    public function loadPreviewPages(){
    
        if( !$this->previewPlugins )
            return;
            
        $cachedPluginRelations =& cache::get('kryn_pluginrelations');
        if( true || !$cachedPluginRelations || count($cachedPluginRelations) == 0 ){
            self::cachePluginsRelations();
            $cachedPluginRelations =& cache::get('kryn_pluginrelations');
        }
        
        $module = getArgv('module');
        
        foreach( $this->previewPlugins as $plugin => $urlGetter ){
            
            $moduleToUse = $module;
            $pluginToUse = $plugin;
            
            if( strpos($plugin, '/') !== false ){
                $ex = explode('/', $plugin);
                $moduleToUse = $ex[0];
                $pluginToUse = $ex[1];
            }
            
            $pages =& $cachedPluginRelations[ $moduleToUse ][$pluginToUse];
            if( count($pages) > 0 ){
                foreach( $pages as &$page ){
                    $this->previewPluginPages[$moduleToUse.'/'.$pluginToUse][ $page['domain_rsn'] ][ $page['rsn'] ] = array(
                        'title' => $page['title'],
                        'path' => kryn::getPagePath( $page['rsn'] )
                    );
                }
            }
        }
    
    }
    
    /**
     * Loads all plugins from system_contents to a indexed cached array
     * 
     */
    public static function cachePluginsRelations(){
    
        $res = dbExec('
        SELECT p.domain_rsn, p.rsn, c.content, p.title
        FROM 
            %pfx%system_contents c,
            %pfx%system_pagesversions v,
            %pfx%system_pages p
        WHERE 1=1
            AND c.type = \'plugin\'
            AND c.hide = 0
            AND v.rsn = c.version_rsn
            AND p.rsn = v.page_rsn
            AND (p.access_denied = \'0\' OR p.access_denied IS NULL)
            AND v.active = 1
        ');
        
        if( !$res ){
            cache::set('kryn_pluginrelations', array());
            return;
        }
        
        $pluginRelations = array();
        
        while( $row = dbFetch($res) ){
            
            preg_match( '/([a-zA-Z0-9_-]*)::([a-zA-Z0-9_-]*)::(.*)/', $row['content'], $matches );
            $pluginRelations[$matches[1]][$matches[2]][] = $row;
        
        }
        cache::set('kryn_pluginrelations', $pluginRelations);
    }

    /**
     * Prepare fields. Loading tableItems by select and file fields.
     * @param array $pFields
     * @param bool $pKey
     */
    public function prepareFieldItem( &$pFields, $pKey = false ){
        if( is_array( $pFields ) && $pFields['type'] == '' ){
            foreach( $pFields as $key => &$field ){
                if( $field['type'] != '' && is_array($field) ){
                    if( $this->prepareFieldItem( $field, $key ) == false ){
                    	unset( $pFields[$key] );
                    }
                }
            }
        } else {
            if( $pFields['needAccess'] && !kryn::checkUrlAccess($pFields['needAccess']) ){
                return false;
            }
            $this->_fields[ $pKey ] = $pFields;
            
            switch( $pFields['type'] ){
                case 'select':
                	
                    if( !empty($field['eval']) )
                        $pFields['tableItems'] = eval($field['eval']);
                    elseif( $pFields['relation'] == 'n-n')
                        $pFields['tableItems'] = dbTableFetch( $pFields['n-n']['right'], DB_FETCH_ALL);
                    else if( $pFields['table'] )
                        $pFields['tableItems'] = dbTableFetch( $pFields['table'], DB_FETCH_ALL);
                    else if( $pFields['sql'] )
                        $pFields['tableItems'] = dbExFetch( $pFields['sql'], DB_FETCH_ALL);
                    else if( $pFields['method'] ){
                        $nam = $pFields['method'];
                        if( method_exists( $this, $nam) )
                            $pFields['tableItems'] = $this->$nam( $pFields );
                    }
                        
                    if($pFields['modifier'] && !empty($pFields['modifier']) && method_exists( $this, $pFields['modifier'] ))                   
                        $pFields['tableItems'] = $this->$pFields['modifier']( $pFields['tableItems'] );

                        
                    break;
                 case 'files':
                     
                    $files = kryn::readFolder( $pFields['directory'], $pFields['withExtension'] );
                    if( count($files)>0 ){
                        foreach( $files as $file ){
                            $pFields['tableItems'][] = array('id' =>$file, 'label' => $file);
                        }
                    } else {
                        $pFields['tableItems'] = array();
                    }
                    $pFields['table_key'] = 'id';
                    $pFields['table_label'] = 'label';
                    $pFields['type'] = 'select';
                
                    break;
            }
            if( is_array( $pFields['depends'] ) ){
                $this->prepareFieldItem( $pFields['depends'] );
            }
        }
        return true;
    }

    /**
     * Building the WHERE area.
     * @return string
     */
    public function buildWhere(){
        //old
        foreach( $this->primary as $primary ){
            if( $tableInfo[$primary][0] == 'int' )
                $val = getArgv($primary);
            else
                $val = "'".getArgv($primary)."'";
            $where = " AND $primary = $val";
        }
        return $where;
    }

    
    /**
     * Return the selected item from database.
     * @return array
     */
    public function getItem(){

        $tableInfo = $this->db[$this->table];
        $where = '';
        $primaries = array();
        $code = $this->table;
        
        foreach( $this->primary as $primary ){
            if( $tableInfo[$primary][0] == 'int' ) 
                $val = getArgv($primary);
            else
                $val = "'".getArgv($primary)."'";
            
            $primaries[$primary] = getArgv($primary);
            $where .= " AND $primary = $val";
            
            $code .= '_'.$primary.'='.$val;
        }
        $code = esc($code);
        
        if( getArgv('version') ){
            
            $version = getArgv('version')+0;
            $row = dbTableFetch('system_frameworkversion', "code = '$code' AND version = $version", 1);
            $res['version'] = $row['version'];
            $res['values'] = json_decode( $row['content'], true );
            
        } else {

            $sql = "
                SELECT * FROM %pfx%".$this->table."
                WHERE 1=1
                    $where
                LIMIT 1";
    
            $res['values'] = dbExfetch( $sql, 1 );
        }
    
        $res['preview_urls'] = $this->getPreviewUrls( $res['values'] );
        
        if( $this->versioning == true ){
            $res['versions'] = array();
            
            $res['versions'] = dbExfetch("
            	SELECT v.*, u.username as user_username FROM %pfx%system_frameworkversion v
            	LEFT OUTER JOIN %pfx%system_user u ON (u.rsn = v.user_rsn)
            	WHERE code = '$code'
            	ORDER BY v.version DESC", -1);
            
            if( is_array($res['versions']) ){
                foreach( $res['versions'] as &$version ){
                    $version['title'] = '#'.$version['version'].', '.$version['user_username'].' '.date('d.m.y H:i:s', $version['cdate']);
                }
            }
        }

        foreach( $this->_fields as $key => $field ){
            if( $field['customValue'] ){
                $func = $field['customValue'];
                $res['values'][$key] = $this->$func( $primaries, $res );
            }
            if( $field['type'] == 'select' && $field['relation'] == 'n-n' ){
                $sql = "
                    SELECT tableright.*, tablemiddle.".$field['n-n']['middle_keyright']."
                    FROM 
                        %pfx%".$field['n-n']['right']." as tableright, 
                        %pfx%".$field['n-n']['middle']." as tablemiddle, 
                        %pfx%".$this->table." as tableleft
                    WHERE 
                        tableright.".$field['n-n']['right_key']." = tablemiddle.".$field['n-n']['middle_keyright']." AND
                        tableleft.".$field['n-n']['left_key']." = tablemiddle.".$field['n-n']['middle_keyleft']." AND
                        tableleft.".$field['n-n']['left_key']." = ".$res['values'][$field['n-n']['left_key']]."
                    ";
                $res['values'][$key] = dbExfetch( $sql, DB_FETCH_ALL);
            }else if($field['type'] == 'select' && $field['multi'] && !$field['relation'] ) {
                $res['values'][$key] = json_decode( $res['values'][$key]);
            }
        }
        
        return $res;
    }

    
    /**
     * Saves the item to database.
     */
    public function saveItem(){
        $tableInfo = $this->db[$this->table];

        $sql = 'UPDATE %pfx%'.$this->table.' SET ';
        $values = array();
        
        $row = array();
        foreach( $this->_fields as $key => $field ){
            if( $field['fake'] == true ) continue;

            $val = getArgv($key);
            
            //if( $field['needAccess'] && !kryn::checkUrlAccess($field['needAccess']) ){
            //    continue;
            //}
            
            if( $field['customSave'] != '' ){
                $func = $field['customSave'];
                if( function_exists( $func ) )
                    $func();
                if( method_exists( $this, $func ) )
                    $this->$func();
                continue;
            }
            
            if( $field['disabled'] == true )
                continue;

            if( $field['type'] == 'select' && $field['relation'] == 'n-n' )
                continue;

            if( $field['update']['onlyIfFilled'] || $field['onlyIfFilled'] ){
                if( empty($val) ) continue;
            }

            $mod = ($field['update']['modifier'])?$field['update']['modifier']:$field['modifier'];
            if( $mod ){
                #$val = $this->$mod($val);
                if( function_exists( $mod ) )
                    $val = $mod($val);
                if( method_exists( $this, $mod ) )
                    $val = $this->$mod( $val );
            }

            if( $field['type'] == 'fileList' ){
                $val = json_encode( $val );
            }else if($field['type'] == 'select' && $field['multi'] && !$field['relation']) {
                $val = json_encode( $val);
            }

            $row[$key] = $val;
            
            /*if( $field[0] == 'int' || $field['update']['type'] == 'int' )
                $val = $val+0;
            else
                $val = "'".esc($val)."'";

            $values[$key] = $val;
            $sql .= "$key = $val,";
            */
		}
        
     	if( $this->multiLanguage ){
        	$curLang = getArgv('lang', 2);
        	$sql .= "lang = '$curLang',";
        }
		
        $sql = substr($sql, 0, -1);
        $sql .= " WHERE 1=1 ";

        $primary = array();
        foreach( $tableInfo as $key => $field ){
            
            if( $field[2] != "DB_PRIMARY" ) continue;
            
            /*if( $field[0] == 'int' )
                $val = getArgv($key);
            else
                $val = "'".getArgv($key,true)."'";
            */
            $val = getArgv($key);
            
            if( isset($val) ){
                $primary[$key] = $val;
                $row[$key] = $val;
            }

            //$sql .= " AND $key = $val";
        }
        
        $res = array();
        if( $this->versioning == true && getArgv('publish') != 1  ){
            
            //only save in versiontable
            $res['version_rsn'] = admin::addVersionRow( $this->table, $primary, $row );
            
        } else {
        
            if( $this->versioning == true ){
                //save old state
                admin::addVersion( $this->table, $primary );
            }
            
            //publish - means: write in origin table
            dbUpdate( $this->table, $primary, $row );
            $res['version_rsn'] = '-'; //means live
            
            foreach( $this->_fields as $key => $field ){
                if( $field['relation'] == 'n-n' ){
                    $values = json_decode( getArgv($key) );
                    $sqlDelete = "
                        DELETE FROM %pfx%".$field['n-n']['middle']."
                        WHERE ".$field['n-n']['middle_keyleft']." = ".getArgv($field['n-n']['left_key']);
                    dbExec( $sqlDelete );
                    foreach( $values as $value ){
                        $sqlInsert = "
                            INSERT INTO %pfx%".$field['n-n']['middle']."
                            ( ".$field['n-n']['middle_keyleft'].", ".$field['n-n']['middle_keyright']." )
                            VALUES ( '".getArgv($field['n-n']['left_key'])."', '".esc($value)."' );";
                        dbExec( $sqlInsert );
                    }
                }
            }
        }
        
        $res['preview_urls'] = $this->getPreviewUrls( $row );
        return $res;
    }
    
    public function getPreviewUrls( $pRow ){
    
        if( $this->previewPlugins ){
            
            $cachedPluginRelations =& cache::get('kryn_pluginrelations');
            $module = getArgv('module');
            
            foreach( $this->previewPlugins as $plugin => $urlGetter ){
                
                $moduleToUse = $module;
                $pluginToUse = $plugin;
                
                if( strpos($plugin, '/') !== false ){
                    $ex = explode('/', $plugin);
                    $moduleToUse = $ex[0];
                    $pluginToUse = $ex[1];
                }
                
                $pages =& $cachedPluginRelations[ $moduleToUse ][ $pluginToUse ];
                if( count($pages) > 0 ){
                    foreach( $pages as &$page ){
                        
                        $pluginValue = substr( $page['content'], strpos($page['content'], '::') );
                        $pluginValue = substr( $pluginValue, strpos($pluginValue, '::') );
                        
                        if( method_exists( $this, $urlGetter ) ){
                            $previewUrls[$moduleToUse.'/'.$pluginToUse][ $page['rsn'] ] =
                                kryn::pageUrl($page['rsn']).'/'.$this->$urlGetter( $pRow, json_decode($pluginValue,true), $page['rsn'] );
                        }
                        
                    }
                }
            }
        }
        return $previewUrls;
    }

}


?>

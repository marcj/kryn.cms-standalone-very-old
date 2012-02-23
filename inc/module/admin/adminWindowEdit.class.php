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
 * @author MArc Schmidt <marc@kryn.org>

 */

class adminWindowEdit {

    /**
     * Defines the table which should be accessed.
     * Use this only if you know, what you're doing,
     * normally this is not filled and the table comes
     * from the object settings.
     *
     * @var string
     */
    public $table = '';

    /**
     * Defines the object key which should be accessed
     *
     * @var string
     */
    public $object = '';

    /**
     * Defines your primary fiels as a array.
     * Example: $primary = array('rsn');
     * Example: $primary = array('id', 'name');
     *
     * Use this only if you know, what you're doing,
     * normally this comes from the object settings.
     *
     * @abstract
     * @var array
     */
    public $primary = array();

    /**
     * Defines whether the list windows should display the language select box.
     * Note: Your table need a field 'lang' varchar(2). The windowList class filter by this.
     *
     * @var bool
     */
    public $multiLanguage = false;


    /**
     * Defines the preview plugins
     * Example content: array(
     *     '<pluginKey>' => '<urlGetterMethod'
     * )
     * Example urlGetterMethod:
     * public function getUrl( $pItem, $pPluginValues, $pPageRsn ){
     *     return kryn::toModRewrite( $pItem['title'] ) . '/' . $pItem['rsn'];
     * }

     */
    public $previewPlugins = false;

    /**
     * Defines the fields. (ka.fields)
     * Example: array(
     *      'kaField1' => array( ka.field params goes here ),
     *      'kaField2' => array( ka.field params goes here )
     * )
     *
     * @var array
     */
    public $fields = array();

    /**
     * Defines each tab and inside it the fields. (ka.fields)
     * Example content: array(
     *  'TabTitle1' => array(
     *      'kaField1' => array( ka.field params goes here )
     *  ),
     *  'TabTitle2' => array(
     *      'kaField2' => array( ka.field params goes here ),
     *      'kaField3' => array( ka.field params goes here )
     *  )
     * (
     *
     * @var array
     */
    public $tabFields = array();


    /**
     * Defines whether the versioning for this form is enabled or not
     *
     * @var boolean
     */
    public $versioning = false;


    /**
     * Defines which field the window should use for his title.
     *
     * @var string
     */
    public $titleField = false;


    /**
     *  object definition cache
     */
    private $objectDefinition = array();

    function __construct(){

        if ($this->object){
            $this->objectDefinition = kryn::$objects[$this->object];
            $this->table = $this->objectDefinition['table'];
            foreach ($this->objectDefinition['fields'] as $key => &$field){
                if($field['primaryKey']){
                    $this->primary[] = $key;
                }
            }

            if ($this->fields){
                $this->prepareFieldDefinition($this->fields);
            }

            if ($this->tabFields){
                foreach ($this->tabFields as &$fields){
                    $this->prepareFieldDefinition($fields);
                }
            }

        }
    }

    public function prepareFieldDefinition(&$pFields){

        $i = 0;
        foreach ($pFields as $key => $field){
            if (is_numeric($key)){

                $newItem = $this->objectDefinition['fields'][$field];
                if (!$newItem['label']) $newItem['label'] = $field;

                $pFields = array_merge(
                    array_slice($pFields, 0, $i),
                    array($field => $newItem),
                    array_slice($pFields, $i+1)
                );
                reset($pFields);
                $i = -1;
            }
            $i++;
        }

        foreach ($pFields as $key => &$field){
            if ($field['depends']) $this->prepareFieldDefinition($field['depends']);
        }

    }

    /**
     * Initialize $fields and $tabFields. Called when opened the window
     *
     * @param bool $pAndLoadPreviewPages
     * @return adminWindowEdit
     */
    public function init($pAndLoadPreviewPages = false) {

        $this->_fields = array();
        if ($this->fields) {
            $this->prepareFieldItem($this->fields);
        }
        if ($this->tabFields) {
            foreach ($this->tabFields as &$fields)
                $this->prepareFieldItem($fields);
        }

        if ($pAndLoadPreviewPages)
            $this->loadPreviewPages();

        return $this;
    }

    /**
     * Loads all pages which have included the plugin in $previewPlugins
     */
    public function loadPreviewPages() {

        if (!$this->previewPlugins)
            return;

        $cachedPluginRelations =& kryn::getCache('kryn_pluginrelations');
        if (true || !$cachedPluginRelations || count($cachedPluginRelations) == 0) {
            self::cachePluginsRelations();
            $cachedPluginRelations =& kryn::getCache('kryn_pluginrelations');
        }

        $module = $this->module;

        $this->previewPluginPages = array();

        foreach ($this->previewPlugins as $plugin => $urlGetter) {

            $moduleToUse = $module;
            $pluginToUse = $plugin;

            if (strpos($plugin, '/') !== false) {
                $ex = explode('/', $plugin);
                $moduleToUse = $ex[0];
                $pluginToUse = $ex[1];
            }

            $pages =& $cachedPluginRelations[$moduleToUse][$pluginToUse];
            if (count($pages) > 0) {
                foreach ($pages as &$page) {
                    $this->previewPluginPages[$moduleToUse . '/' . $pluginToUse][$page['domain_rsn']][$page['rsn']] =
                        array(
                            'title' => $page['title'],
                            'path' => kryn::getPagePath($page['rsn'])
                        );
                }
            }
        }
    }

    public function unlock($pType, $pId) {
        dbDelete('system_lock', "type = '$pType' AND key = '$pId'");
        return true;
    }

    public function canLock($pType, $pId) {
        global $user;

        $row = dbTableFetch('system_lock', 1, "type = '$pType' AND key = '$pId'");
        if ($row['session_id'] == $user->sessionid) return true;
        if (!$row['rsn'] > 0) return true;

        $user = dbTableFetch('system_user');
        return false;
    }

    public function lock($pType, $pId) {
        global $user;

        $row = dbTableFetch('system_lock', 1, "type = '$pType' AND key = '$pId'");
        if ($row['rsn'] > 0) return false;

        dbInsert('system_lock', array(
            'type' => $pType,
            'key' => $pId,
            'session_id' => $user->sessionid,
            'time' => time()
        ));
        return true;
    }


    /**
     * Loads all plugins from system_contents to a indexed cached array

     */
    public static function cachePluginsRelations() {

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

        if (!$res) {
            kryn::setCache('kryn_pluginrelations', array());
            return;
        }

        $pluginRelations = array();

        while ($row = dbFetch($res)) {

            preg_match('/([a-zA-Z0-9_-]*)::([a-zA-Z0-9_-]*)::(.*)/', $row['content'], $matches);
            $pluginRelations[$matches[1]][$matches[2]][] = $row;

        }
        kryn::setCache('kryn_pluginrelations', $pluginRelations);
    }

    /**
     * Prepare fields. Loading tableItems by select and file fields.
     * Note for selects: please use 'stores' instead of 'sql' etc
     *
     * @param array $pFields
     * @param bool  $pKey
     * @return bool
     */
    public function prepareFieldItem(&$pFields, $pKey = false) {

        if (is_array($pFields) && $pFields['type'] == '') {
            foreach ($pFields as $key => &$field) {

                if ($field['type'] != '' && is_array($field)) {
                    if ($this->prepareFieldItem($field, $key) == false) {
                        unset($pFields[$key]);
                    }
                }
            }
        } else {
            if ($pFields['needAccess'] && !kryn::checkUrlAccess($pFields['needAccess'])) {
                return false;
            }

            error_log($pKey);
            if ($pKey == 'groups')
                error_log(print_r($pFields,true));

            if (!$pFields['store']){
                switch ($pFields['type']) {
                    case 'select':

                        if (!empty($field['eval']))
                            $pFields['tableItems'] = eval($field['eval']);
                        elseif ($pFields['relation'] == 'n-n')
                            $pFields['tableItems'] = dbTableFetch($pFields['n-n']['right'], DB_FETCH_ALL);
                        else if ($pFields['table'])
                            $pFields['tableItems'] = dbTableFetch($pFields['table'], DB_FETCH_ALL);
                        else if ($pFields['sql'])
                            $pFields['tableItems'] = dbExFetch($pFields['sql'], DB_FETCH_ALL);
                        else if ($pFields['method']) {
                            $nam = $pFields['method'];
                            if (method_exists($this, $nam))
                                $pFields['tableItems'] = $this->$nam($pFields);
                        }

                        if ($pFields['modifier'] && !empty($pFields['modifier']) &&
                                         method_exists($this, $pFields['modifier']))
                            $pFields['tableItems'] = $this->$pFields['modifier']($pFields['tableItems']);


                        break;
                    case 'files':

                        $files = kryn::readFolder($pFields['directory'], $pFields['withExtension']);
                        if (count($files) > 0) {
                            foreach ($files as $file) {
                                $pFields['tableItems'][] = array('id' => $file, 'label' => $file);
                            }
                        } else {
                            $pFields['tableItems'] = array();
                        }
                        $pFields['table_key'] = 'id';
                        $pFields['table_label'] = 'label';
                        $pFields['type'] = 'select';

                        break;
                }
            }


            $this->_fields[$pKey] = $pFields;

            if (is_array($pFields['depends'])) {
                $this->prepareFieldItem($pFields['depends']);
            }
        }
        return true;
    }


    /**
     * Return the selected item from database.
     *
     * @return array
     */
    public function getItem() {

        $tableInfo = database::getOptions($this->table);
        $where = '';
        $primaries = array();
        $code = $this->table;

        foreach ($this->primary as $primary) {

            if ($tableInfo[$primary]['escape'] == 'int')
                $val = getArgv($primary) + 0;
            else
                $val = "'" . getArgv($primary, 1) . "'";

            $primaries[$primary] = getArgv($primary);
            $where .= " AND $primary = $val";

            $code .= '_' . $primary . '=' . getArgv($primary);
        }
        $code = esc($code);

        if (getArgv('version')) {

            $version = getArgv('version') + 0;
            $row = dbTableFetch('system_frameworkversion', "code = '$code' AND version = $version", 1);
            $res['version'] = $row['version'];
            $res['values'] = json_decode($row['content'], true);

        } else {

            $sql = "
                SELECT * FROM %pfx%" . $this->table . "
                WHERE 1=1
                    $where
                LIMIT 1";

            $res['values'] = dbExfetch($sql, 1);
        }

        $res['preview_urls'] = $this->getPreviewUrls($res['values']);

        if ($this->versioning == true) {
            $res['versions'] = array();

            $res['versions'] = dbExfetch("
            	SELECT v.*, u.username as user_username FROM %pfx%system_frameworkversion v
            	LEFT OUTER JOIN %pfx%system_user u ON (u.rsn = v.user_rsn)
            	WHERE code = '$code'
            	ORDER BY v.version DESC", -1);

            if (is_array($res['versions'])) {
                foreach ($res['versions'] as &$version) {
                    $version['title'] = '#' . $version['version'] . ', ' . $version['user_username'] . ' ' .
                                        date('d.m.y H:i:s', $version['cdate']);
                }
            }
        }

        foreach ($this->_fields as $key => $field) {
            if ($field['customValue']) {
                $func = $field['customValue'];
                $res['values'][$key] = $this->$func($primaries, $res);
            }
            if ($field['relation'] == 'n-n') {
                if ($field['type'] == 'select') {
                    $sql = "
                        SELECT tableright.*, tablemiddle." . $field['n-n']['middle_keyright'] . "
                        FROM 
                            %pfx%" . $field['n-n']['right'] . " as tableright,
                            %pfx%" . $field['n-n']['middle'] . " as tablemiddle,
                            %pfx%" . $this->table . " as tableleft
                        WHERE 
                            tableright." . $field['n-n']['right_key'] . " = tablemiddle." .
                           $field['n-n']['middle_keyright'] . " AND
                            tableleft." . $field['n-n']['left_key'] . " = tablemiddle." .
                           $field['n-n']['middle_keyleft'] . " AND
                            tableleft." . $field['n-n']['left_key'] . " = " .
                           $res['values'][$field['n-n']['left_key']] . "
                        ";
                    $res['values'][$key] = dbExfetch($sql, DB_FETCH_ALL);
                } else if ($field['type'] == 'textlist') {
                    $sql = "
                        SELECT 
                            tablemiddle." . $field['n-n']['middle_keyright'] . " as middlevalue
                        FROM
                            %pfx%" . $field['n-n']['middle'] . " as tablemiddle
                        WHERE
                            tablemiddle." . $field['n-n']['middle_keyleft'] . " = " .
                           $res['values'][$field['n-n']['left_key']] . "
                    ";
                    $dbRes = dbExec($sql);
                    $res['values'][$key] = array();
                    while ($row = dbFetch($dbRes)) {
                        $res['values'][$key][] = $row['middlevalue'];
                    }
                }
            }
        }

        return $res;
    }


    /**
     * Saves the item to database.
     */
    public function saveItem() {

        $tableInfo = $this->db[$this->table];

        $values = array();

        $row = array();
        foreach ($this->_fields as $key => $field) {
            if ($field['fake'] == true) continue;
            if ($field['type'] == 'subview') continue;

            $val = getArgv($key);

            if ($field['update']['onlyIfFilled'] || $field['onlyIfFilled']) {
                if (empty($val)) continue;
            }

            if ($field['customSave'] != '') {
                $func = $field['customSave'];
                if (method_exists($this, $func))
                    $this->$func($row);
                else if (function_exists($func))
                    $func($row);
                continue;
            }

            if ($field['disabled'] == true)
                continue;

            if ($field['type'] == 'select' && $field['relation'] == 'n-n')
                continue;

            $mod = ($field['update']['modifier']) ? $field['update']['modifier'] : $field['modifier'];
            if ($mod) {
                #$val = $this->$mod($val);
                if (method_exists($this, $mod))
                    $val = $this->$mod($val);
                else if (function_exists($mod))
                    $val = $mod($val);
            }

            if ($field['type'] == 'fileList') {
                $val = json_encode($val);
            } else if ($field['type'] == 'select' && $field['multi'] && !$field['relation']) {
                $val = json_encode($val);
            }

            $row[$key] = $val;
        }

        if (getArgv('_kryn_relation_table')) {
            $relation = database::getRelation(getArgv('_kryn_relation_table'), $this->table);
            if ($relation) {
                $params = getArgv('_kryn_relation_params');
                foreach ($relation['fields'] as $field_left => $field_right) {
                    if (!$row[$field_right]) {
                        $row[$field_right] = $params[$field_right];
                    }
                }
            }
        }

        if ($this->multiLanguage) {
            $curLang = getArgv('lang', 2);
            $row['lang'] = $curLang;
        }

        $primary = array();
        foreach ($this->primary as $field) {
            $val = getArgv($field);

            if (isset($val)) {
                $primary[$field] = $val;
                $row[$field] = $val;
            }
        }

        $res = array();
        if ($this->versioning == true && getArgv('publish') != 1) {

            //only save in versiontable
            $res['version_rsn'] = admin::addVersionRow($this->table, $primary, $row);

        } else {

            if ($this->versioning == true) {
                //save old state
                admin::addVersion($this->table, $primary);
            }

            //publish - means: write in origin table
            dbUpdate($this->table, $primary, $row);

            $res['version_rsn'] = '-'; //means live

            foreach ($this->_fields as $key => $field) {
                if ($field['relation'] == 'n-n') {
                    $values = json_decode(getArgv($key));
                    $sqlDelete = "
                        DELETE FROM %pfx%" . $field['n-n']['middle'] . "
                        WHERE " . $field['n-n']['middle_keyleft'] . " = '" . getArgv($field['n-n']['left_key'], 1) .
                                 "'";
                    dbExec($sqlDelete);
                    foreach ($values as $value) {
                        $sqlInsert = "
                            INSERT INTO %pfx%" . $field['n-n']['middle'] . "
                            ( " . $field['n-n']['middle_keyleft'] . ", " . $field['n-n']['middle_keyright'] . " )
                            VALUES ( '" . getArgv($field['n-n']['left_key'], 1) . "', '" . esc($value) . "' );";
                        dbExec($sqlInsert);
                    }
                }
            }
        }

        $res['preview_urls'] = $this->getPreviewUrls($row);
        return $res;
    }

    public function getPreviewUrls($pRow) {

        if ($this->previewPlugins) {

            $cachedPluginRelations =& kryn::getCache('kryn_pluginrelations');
            $module = $this->module;

            foreach ($this->previewPlugins as $plugin => $urlGetter) {

                $moduleToUse = $module;
                $pluginToUse = $plugin;

                if (strpos($plugin, '/') !== false) {
                    $ex = explode('/', $plugin);
                    $moduleToUse = $ex[0];
                    $pluginToUse = $ex[1];
                }

                $pages =& $cachedPluginRelations[$moduleToUse][$pluginToUse];
                if (count($pages) > 0) {
                    foreach ($pages as &$page) {

                        $pluginValue = substr($page['content'], strpos($page['content'], '::'));
                        $pluginValue = substr($pluginValue, strpos($pluginValue, '::'));

                        if (method_exists($this, $urlGetter)) {
                            $previewUrls[$moduleToUse . '/' . $pluginToUse][$page['rsn']] =
                                kryn::pageUrl($page['rsn']) . '/' .
                                $this->$urlGetter($pRow, json_decode($pluginValue, true), $page['rsn']);
                        }

                    }
                }
            }
        }
        return $previewUrls;
    }

}


/*
* Compatibility for older extension
* @deprecated
*/
class windowEdit extends adminWindowEdit {

}


?>

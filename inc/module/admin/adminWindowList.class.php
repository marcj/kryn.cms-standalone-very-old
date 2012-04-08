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
 * This class need to be the motherclass in your framework classes, which
 * are defined via the window links in your extension.
 *
 * @author MArc Schmidt <marc@kryn.org>
 */


class adminWindowList {

    /**
     * Defines the table which should be accessed.
     * This variable has to be set by any subclass.
     *
     * Use this only if you know, what you're doing,
     * normally this comes from the object settings.
     *
     * @var string table name
     * @abstract
     */
    public $table = '';

    /**
     * Defines the object which should be listed.
     *
     * @var string object key
     */
    public $object = '';

    /**
     * Copy of the object definition
     *
     * @var array
     */
    private $objectDefinition = array();

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
     * Defines the columns of your table which should be displayed.
     *
     * @abstract
     * @var array
     */
    public $columns = array();

    /**
     * Defines how many rows should be displayed per page.
     *
     * @var integer number of rows per page
     */
    public $itemsPerPage = 10;

    /**
     * Order field
     *
     * @deprecated Use $order instead
     * @var string
     */
    public $orderBy = '';

    /**
     * Order field
     *
     * @private
     * @var string
     */
    private $customOrderBy = '';

    /**
     * Order direction
     *
     * @deprecated Use $order instead
     * @var string
     */
    public $orderByDirection = 'ASC';

    /**
     * Order direction
     *
     * @private
     * @var string
     */
    private $custonOrderByDirection = 'ASC';

    /**
     * Default order
     *
     * $order = array(
     *      array('field' => 'group_id', 'direction' => 'asc'),
     *      array('field' => 'title', 'direction' => 'asc')
     * );
     *
     * @var array
     */
    public $order = array();

    /**
     * Defines the icon for the add button. Relative to inc/template/admin/images/icons/
     *
     * @var string name of image
     * @deprecated Use $addIcon instead
     */
    public $iconAdd = 'add.png';
    /**
     * Defines the icon for the edit button. Relative to inc/template/admin/images/icons/
     *
     * @var string name of image
     * @deprecated Use $editIcon instead
     */
    public $iconEdit = 'page_white_edit.png';
    /**
     * Defines the icon for the remove/delete button. Relative to inc/template/admin/images/icons/
     *
     * @var string name of image
     * @deprecated Use $removeIcon instead
     */
    public $iconDelete = 'delete.png';


    /**
     * Defines the icon for the add button. Relative to inc/template/
     *
     * @var string name of image
     */
    public $addIcon = '';
    /**
     * Defines the icon for the edit button. Relative to inc/template/
     *
     * @var string name of image
     */
    public $editIcon = '';
    /**
     * Defines the icon for the remove/delete button. Relative to inc/template/
     *
     * @var string name of image
     */
    public $removeIcon = '';




    /**
     * Defines whether the add button should be displayed
     *
     * @var boolean
     */
    public $add = false;
    /**
     * Defines whether the remove/delete button should be displayed
     * Also on each row the Delete-Button and the checkboxes.
     *
     * @var boolean
     */
    public $remove = false;
    /**
     * Defines whether the edit button should be displayed
     *
     * @var boolean
     */
    public $edit = false;
    /**
     * TBD
     *
     * @var boolean
     */
    public $navigation = false;

    /**
     * Defines whether the list windows should display the language select box.
     * Note: Your table need a field 'lang' varchar(2). The windowList class filter by this.
     *
     * @var bool
     */
    public $multiLanguage = false;

    /**
     * TBD
     *
     * @return object this object
     */
    function init() {

        //store this in the acl-table in the future
        #$this->add = true;

        return $this;
    }

    /**
     * Constructor
     */
    function __construct() {


        if ($this->object){
            $this->objectDefinition = kryn::$objects[$this->object];
            if (!$this->objectDefinition){
                throw new Exception("Can not find object '".$this->object."'");
            }
            $this->table = $this->objectDefinition['table'];
            foreach ($this->objectDefinition['fields'] as $key => &$field){
                if($field['primaryKey']){
                    $this->primary[] = $key;
                }
            }

            if ($this->columns){
                $this->prepareFieldDefinition($this->columns);
            }
        }

        if (!$this->orderBy && count($this->order) == 0)
            $this->orderBy = $this->primary[0];

        if (getArgv('orderBy') != '')
            $this->customOrderBy = getArgv('orderBy', 1);

        if (getArgv('orderByDirection') != '')
            $this->custonOrderByDirection = (strtolower(getArgv('orderByDirection')) == 'asc') ? 'ASC' : 'DESC';

        $this->_fields = array();
        $this->filterFields = array();
        if ($this->filter) {
            foreach ($this->filter as $key => $val) {

                if (is_numeric($key)) {
                    //no special definition
                    $fieldKey = $val;
                    $field = $this->columns[$val];
                } else {
                    $field = $val;
                    $fieldKey = $key;
                }


                $this->prepareFieldItem($field);
                $this->filterFields[$fieldKey] = $field;
            }

            $this->prepareFieldItem($this->fields);
        }
        if ($this->tabFields) {
            foreach ($this->tabFields as &$field)
                $this->prepareFieldItem($field);
        }

    }

    /**
     * prepares $pFields. Replace array items which are only a key (with no array definition) with
     * the array definition of the proper field from the object fields.
     *
     * @param $pFields
     */
    private function prepareFieldDefinition(&$pFields){

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
     * Prepare fields. Loading tableItems by select and file fields.
     *
     * @param array $pFields
     * @param bool  $pKey
     */
    private function prepareFieldItem(&$pFields, $pKey = false) {
        if (is_array($pFields) && $pFields['type'] == '') {
            foreach ($pFields as $key => &$field) {
                if ($field['type'] != '' && is_array($field)) {
                    $this->prepareFieldItem($field, $key);
                }
            }
        } else {
            if ($pFields['needAccess'] && !kryn::checkUrlAccess($pFields['needAccess'])) {
                $pFields = null;
                return;
            }
            $this->_fields[$pKey] = $pFields;

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
                        method_exists($this, $pFields['modifier'])
                    )
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
            if (is_array($pFields['depends'])) {
                $this->prepareFieldItem($pFields['depends']);
            }
        }
    }
    /**
     * Deletes the Item from the database which is specified in the request
     *
     * @return bool
     */
    public function deleteItem() {
        $options = database::getOptions($this->table);

        foreach ($this->primary as $primary) {
            if ($options[$primary]['escape'] == 'int')
                $val = $_POST['item'][$primary] + 0;
            else
                $val = "'" . esc($_POST['item'][$primary]) . "'";
            $where = " AND $primary = $val";
        }

        $this->removeN2N($_POST['item']);

        $sql = "DELETE FROM " . dbTableName($this->table) . " WHERE 1=1 $where";
        dbExec($sql);

        return true;
    }

    /**
     * Removes selected files from database.
     *
     * @return boolean
     */
    public function removeSelected() {

        $selected = json_decode(getArgv('selected'), 1);
        $where = '';
        foreach ($selected as $select) {

            $where .= ' OR (';
            //TODO check ACL before remove
            foreach ($this->primary as $primary) {
                $where .= " $primary = '" . esc($select[$primary]) . "' AND ";
            }
            $where = substr($where, 0, -4) . ' )';

            $this->_removeN2N($select);
        }

        $sql = "DELETE FROM " . dbTableName($this->table) . " WHERE 1=0 $where";
        dbExec($sql);
        return true;
    }

    /**
     * Remove all related database entries from selected item.
     *
     * @param datatype $pVal
     */
    private function removeN2N($pVal) {
        foreach ($this->columns as $key => $column) {
            if ($column['type'] == 'select' && $column['relation'] == 'n-n') {
                $sql = "DELETE FROM " . dbTableName($column['n-n']['middle']) . " WHERE " . $column['n-n']['middle_keyleft'] .
                       " = " . $pVal[$column['n-n']['left_key']];
                dbExec($sql);
            }
        }
    }

    /**
     * Build a WHERE clause for search functionality.
     *
     * @return datatype
     */
    function getWhereSql() {

        $table = pfx . $this->table;


        if (getArgv('filter') == 1) {
            if (!count($this->filter) > 0)
                return '';

            $res = '';

            $filterVals = json_decode(getArgv('filterVals'), true);

            foreach ($this->filterFields as $key => $filter) {


                if ($filterVals[$key] != '') {

                    switch ($filter['type']) {
                        case 'select':

                            if ($filterVals[$key] + 0 > 0) {
                                $value = $filterVals[$key] + 0;
                                $res = "AND $table.$key = $value ";
                            } else {

                                $value = esc($filterVals[$key]);
                                $res = "AND $table.$key = '$value' ";
                            }
                            $value = $filterVals[$key] + 0;
                            $res = "AND $table.$key = $value ";

                            break;
                        case 'integer':
                            $value = $filterVals[$key] + 0;
                            $res = "AND $table.$key = $value ";
                            breal;
                        default:
                            $value = esc(str_replace("*", "%", $filterVals[$key]));
                            $res = "AND $table.$key LIKE '$value' ";

                    }
                }
            }
            return $res;
        }
        return '';
    }

    /**
     * Defines a extra filter in WHERE. Starting with "AND "
     *
     * @return string
     */
    function getExtraWhereSql() {
        return '';
    }

    /**
     * Builds the complete SQL for all items.
     *
     * @param bool $pCountSql Defines whether the SQL is used for counting or not
     *
     * @return string
     */
    function getFullSql($pCountSql = false) {
        global $kdb, $cfg, $user;

        $extraFields = array();
        $joins = "";

        $table = dbTableName($this->table);

        $filter = "WHERE 1=1 " . $this->getWhereSql();
        $extraWhere = " " . $this->getExtraWhereSql();

        //relation stuff
        $options = database::getOptions($this->table);

        if (getArgv('relation_table')) {

            $relation = database::getRelation(getArgv('relation_table'), $this->table);

            if ($relation) {
                $params = getArgv('relation_params');

                foreach ($relation['fields'] as $field_left => $field_right) {

                    $extraWhere .= " AND $table.$field_right = ";
                    if ($options[$field_right]['escape'] == 'int')
                        $extraWhere .= $params[$field_right] + 0;
                    else
                        $extraWhere .= "'" . esc($params[$field_right]) . "'";

                }
            }
        }

        if ($this->multiLanguage) {
            $curLang = getArgv('language', 2);
            $filter .= " AND ( $table.lang = '$curLang' OR $table.lang is NULL OR $table.lang = '' )";
        }

        $fields = "";
        $end = "";

        foreach ($this->columns as $key => $column) {
            if ($pCountSql == false) {
                if ($column['type'] == 'select' && $column['relation'] != 'n-n') {
                    $exTable = dbTableName($column['table']);
                    $extraFields[] = $exTable . "." . $column['table_label'] . " AS $key" . "__label";
                    //get all fields from joined table if modifier is active
                    $mod = $this->modifier;
                    if (!empty($mod) && method_exists($this, $mod))
                        $extraFields[] = $exTable . ".*";


                    $joins .= "LEFT OUTER JOIN " . $exTable . " ON " . $exTable . "." . $column['table_key'] .
                              " = $table.$key\n";
                }
                if (kryn::$config['db_type'] != 'postgresql' && $column['type'] == 'select' &&
                    $column['relation'] == 'n-n'
                ) {
                    $extraFields[] =
                        ' group_concat( ' . dbTableName($column['n-n']['right']) . '.' . $column['n-n']['right_label'] .
                        ', \', \') AS ' . $key . '__label';
                    $joins .= "
                            LEFT OUTER JOIN " . dbTableName($column['n-n']['middle']) . " ON(
                                " . dbTableName($column['n-n']['middle']) . "." . $column['n-n']['middle_keyleft'] . "= " .
                              dbTableName($this->table) . "." . $column['n-n']['left_key'] . " )

                            LEFT OUTER JOIN " . dbTableName($column['n-n']['right']) . " ON (
                                " . dbTableName($column['n-n']['right']) . "." . $column['n-n']['right_key'] . " = " .
                              dbTableName($column['n-n']['middle']) . "." . $column['n-n']['middle_keyright'] . " ) ";

                    $end .= " GROUP BY " . dbTableName($this->table) . "." . $this->primary[0] . " \n";
                }
            }
        }


        if (count($extraFields) > 0)
            $fields .= ", " . implode(",", $extraFields);

        if ($pCountSql == false) {

            $sql = "
                SELECT $table.* $fields
                FROM $table
                $joins
                $filter
                $extraWhere
                $end
                ";
        } else {
            $sql = "
                SELECT $table.* $fields
                FROM $table
                $filter
                $extraWhere
                $end";
        }
        return $sql;
    }

    /**
     * Returns the SQL for counting all items.
     *
     * @return string SQL
     */
    public function getCountSql() {
        return preg_replace('/SELECT(.*)FROM/mi', 'SELECT count(*) as ctn FROM', str_replace("\n", " ", $this->getFullSql(true)));
    }


    /**
     * Returns the order SQL statesment
     *
     * @return string
     */
    public function getOrderSql(){

        $order = " ORDER BY ";

        if ($this->customOrderBy){

            $order .= dbTableName($this->table).'.'.$this->customOrderBy.' ';
            $order .= strtolower($this->customOrderByDirection)=='asc'?'asc':'desc';
            return $order;
        }

        if (is_array($this->order) && count($this->order) > 0){
            foreach($this->order as $order){
                $order .= dbTableName($this->table).'.'.$order['field'].' ';
                $order .= strtolower($order['field'])=='asc'?'asc':'desc';
                $order .= ', ';
            }
        }

        if ($this->orderBy && $this->orderByDirection){
            $order .= dbTableName($this->table).'.'.$this->orderBy.' ';
            $order .= strtolower($this->orderByDirection)=='asc'?'asc':'desc';
            $order .= ', ';
        }

        if ($this->secondOrderBy && $this->secondOrderByDirection){
            $order .= dbTableName($this->table).'.'.$this->secondOrderBy.' ';
            $order .= strtolower($this->secondOrderByDirection)=='asc'?'asc':'desc';
            $order .= ', ';
        }

        $order = substr($order, 0, -2);

        return $order;
    }

    /**
     * Gets all Items for getArvg('page')
     *
     * @param string $pPage
     * @return array
     */
    function getItems($pPage) {
        global $kdb, $cfg;

        $results['page'] = $pPage;

        $start = ($pPage * $this->itemsPerPage) - $this->itemsPerPage;
        $end = $this->itemsPerPage;

        $this->listSql = $this->getFullSql();

        /* count sql */
        $countSql = $this->getCountSql();
        $temp = dbExfetch($countSql);
        $results['maxItems'] = $temp['ctn'];

        if ($temp['ctn'] > 0)
            $results['maxPages'] = ceil($temp['ctn'] / $this->itemsPerPage);
        else
            $results['maxPages'] = 0;

        $order = $this->getOrderSql();


        if ($_POST['getPosition']) {

            $limit = "";
            $itemsBefore = array();
            $itemsAfter = array();

            $fields = implode(',', $this->primary);

            $unique = '';
            $sql = "
                " . $this->listSql . "
                $unique
                $order";

            $aWhere = array();

            $table = database::getTable($this->table);
            $options = database::getOptions($table);

            $selected = getArgv('getPosition');

            $sqlInsert = '';
            foreach ($this->primary as $primary) {

                $val = $selected[$primary];
                if ($options[$primary]['escape'] == 'int') {
                    $sqlInsert .= ($val + 0);
                } else {
                    $sqlInsert .= "'" . esc($val) . "'";
                }

                $aWhere[] = "t.$primary = " . $sqlInsert;
            }

            $where = implode(' AND ', $aWhere);

            $res = dbExec($sql);

            $c = 1;
            $found = false;
            while ($row = dbFetch($res)) {

                $found = true;
                foreach ($this->primary as $primary) {
                    if ($row[$primary] != $selected[$primary])
                        $found = false;
                }

                if ($found == true) {
                    json($c);
                }

                $c++;
            }

            json(1);

        } else {

            if (!$pPage) {
                $from = getArgv('from') + 0;
                $max = getArgv('max') + 0;
                $limit = " LIMIT $max OFFSET $from";

            } else {
                //default behaviour
                $limit = " LIMIT $end OFFSET $start";
            }

            $unique = '';
            $listSql = "
            SELECT * FROM (
                " . $this->listSql . "
                $unique
                $order
            ) as t
            $limit";

        }

        $res = dbExec($listSql);

        while ($item = dbFetch($res)) {

            foreach ($this->columns as $key => $column) {
                if (kryn::$config['db_type'] == 'postgresql') {
                    if ($column['type'] == 'select' && $column['relation'] == 'n-n') {
                        $tempRow = dbExfetch("
                            SELECT group_concat(" .dbTableName($column['n-n']['right']) . "." . $column['n-n']['right_label'] .
                                             ") AS " . $key . "__label
                            FROM " .dbTableName($column['n-n']['right']) . ", " . dbTableName($column['n-n']['middle']) . "
                            WHERE
                            ".dbTableName($column['n-n']['right']) . "." . $column['n-n']['right_key'] . " = " .
                                dbTableName($column['n-n']['middle']) . "." . $column['n-n']['middle_keyright'] . " AND
                            ".dbTableName($column['n-n']['middle']) . "." . $column['n-n']['middle_keyleft'] . " = " .
                                             $item[$column['n-n']['left_key']], 1);
                        $item[$key . '__label'] = $tempRow[$key . '__label'];

                    }
                }
            }
            $_res = $this->acl($item);

            $mod = $this->modifier;
            if (!empty($mod) && method_exists($this, $mod))
                $_res = $this->$mod($_res);

            if ($res != null)
                $results['items'][] = $_res;

        }
        return $results;
    }

    /**
     * Build and send the items via specified exportType to the client.
     */
    function exportItems() {

        //TODO

        $this->listSql = $this->getFullSql();
        $order = $this->getOrderSql();

        $listSql =
            $this->listSql .
            $order;

        $sres = dbExec($listSql);

        $exportType = getArgv('exportType', 2);
        $fields = $this->export[$exportType];

        $res = '"' . implode('";"', $fields) . "\"\r\n";
        while ($item = dbFetch($sres)) {
            $_res = $this->acl($item);
            if ($res != null) {
                $items[] = $_res;

                foreach ($fields as $field) {
                    if ($exportType == 'csv') {
                        $res .= '"' . esc($item[$field]) . '";';
                    }
                }
                $res = substr($res, 0, -1) . "\r\n";
            }
        }

        if ($exportType == 'csv')
            header('Content-Type: text/csv');

        $filename = 'export_' . date('ymd-his') . '.' . $exportType;
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        print $res;

        exit;
    }

    /**
     * Each item go through this function in getItems(). Defines whether a item is editable or deleteable.
     *
     * @param array $pItem
     *
     * @return array
     */
    function acl($pItem) {

        //store this in the acl-table in the future
        $visible = true;
        $editable = $this->edit;
        $deleteable = $this->remove;

        $res = null;
        if ($visible) {
            $res = array();
            $res['values'] = $pItem;
            $res['edit'] = $editable;
            $res['remove'] = $deleteable;
        }
        return $res;
    }

}


/*
* Compatibility for older extension
* @deprecated
*/
class windowList extends adminWindowList {

}

?>
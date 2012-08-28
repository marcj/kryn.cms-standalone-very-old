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

namespace Admin\Window;

use \Core\Kryn;

class Listing extends WindowAbstract {

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
        foreach ($this->fields as $key => $column) {
            if ($column['type'] == 'select' && $column['relation'] == 'n-n') {
                $sql = "DELETE FROM " . dbTableName($column['n-n']['middle']) . " WHERE " . $column['n-n']['middle_keyleft'] .
                       " = ?";
                dbExec($sql, array($pVal[$column['n-n']['left_key']]));
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

        $extraFields = array();
        $joins = "";

        $table = dbTableName($this->table);

        $filter = "WHERE 1=1 " . $this->getWhereSql();
        $extraWhere = " " . $this->getExtraWhereSql();

        if ($this->multiLanguage) {
            $curLang = getArgv('language', 2);
            $filter .= " AND ( $table.lang = '$curLang' OR $table.lang is NULL OR $table.lang = '' )";
        }

        $fields = "";
        $end = "";

        //add primary fields to SELECT
        foreach($this->primary as $field){
            $extraFields[] = dbTableName($this->table).'.'.$field;
        }

        foreach ($this->fields as $key => $column) {

            $extraFields[] = dbTableName($this->table).'.'.$key;

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
                if (Kryn::$config['db_type'] != 'postgresql' && $column['type'] == 'select' &&
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
            $fields = implode(",", $extraFields);

        if ($pCountSql == false) {

            $sql = "
                SELECT $fields
                FROM $table
                $joins
                $filter
                $extraWhere
                $end
                ";
        } else {
            $sql = "
                SELECT $fields
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
     * Returns the order SQL statesment.
     * Inly in table mode
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
            foreach($this->order as $field){
                $order .= dbTableName($this->table).'.'.$field['field'].' ';
                $order .= strtolower($field['direction'])=='asc'?'asc':'desc';
                $order .= ', ';
            }
        } else {

            if ($this->orderBy && $this->orderByDirection){
                $order .= dbTableName($this->table).'.'.$this->orderBy.' ';
                $order .= strtolower($this->orderByDirection)=='asc'?'asc':'desc';
                $order .= ', ';
            }

        }

        $order = substr($order, 0, -2);

        return $order;
    }

    public function getClass(){

        $clazz = $this->object?'Propel':'SQL';

        $clazz = '\Core\ORM\\'.$clazz;

        return $this->object?
            new $clazz($this->object, Kryn::$objects[$this->object]) : new $clazz();

    }

    /**
     * Gets all Items for getArvg('page')
     *
     * @param string $pPage
     * @return array
     */
    function getItems($pPage) {

        $pPage = $pPage?$pPage:1;

        $options   = array();
        $options['offset'] = ($pPage * $this->itemsPerPage) - $this->itemsPerPage;
        $options['limit'] = $this->itemsPerPage;

        $obj = \Core\Object::getClass($this->object);

        $condition = '';

        foreach ($this->fields as $k => $v){
            if (is_numeric($k))
                $options['fields'][] = $v;
            else
                $options['fields'][] = $k;
        }

        $maxItems = $obj->getCount($condition, $options);

        if ($maxItems > 0)
            $maxPages = ceil($results['maxItems'] / $this->itemsPerPage);
        else
            $maxPages = 0;

        $items = $obj->getItems($condition, $options);
        foreach ($items as &$item){
            $item = $this->prepareRow($item);
        }

        return array(
            'items' => $items,
            'page'  => $pPage,
            'maxPages' => $maxPages,
            'maxItems' => $maxItems
        );

        if ($this->object){




            if (!$pPage) {
                $start = getArgv('from') + 0;
                $end = getArgv('max') + 0;
            }


            $clazz = ucfirst($this->object).'Query';
            if (!class_exists($clazz)) throw new MissingClassException(tf('The class %s does not exist.', $clazz));

            $query = $clazz::create();

            foreach ($this->order as $field => $order){
                $query->orderBy($field, $order);
            }

            $fields = array();
            foreach ($this->fields as $field => $column){
                if (is_numeric($field)){
                    $query->joinWith('Groups');
                } else {
                    $fields[] = $field;
                }
            }

            $query->select($fields);

            $query->limit($end);
            $query->offset($start);

            $results['items'] = $query->find();


            var_dump($results); exit;

            $items = krynObjects::getList($this->object, false, array(
                'offset' => $start,
                'fields' => $fields,
                'limit'  => $end,
                'order' => $this->order
            ));

            $results['maxItems'] = krynObjects::getCount($this->object);

            if ($results['maxItems'] > 0)
                $results['maxPages'] = ceil($results['maxItems'] / $this->itemsPerPage);
            else
                $results['maxPages'] = 0;

            foreach ($items as $item){
                $_res = $this->acl($item);

                if ($_res){
                    $mod = $this->modifier;
                    if (!empty($mod) && method_exists($this, $mod))
                        $_res = $this->$mod($_res);

                    if ($_res != null)
                        $results['items'][] = $_res;
                }
            }
            return $results;


        } else {

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

                foreach ($this->fields as $key => $column) {
                    if (Kryn::$config['db_type'] == 'postgresql') {
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
     * Each item goes through this function in getItems(). Defines whether a item is editable or deleteable.
     * You can attach here extra action icons, too.
     *
     * Result should be:
     *
     * array(
     *     'values' => $pItem,
     *     'edit' => bool (can be edited),
     *     'remove' => bool (can be removed),
     *     'actions' => array(
     *         array('/* action * /') //todo
     *     )
     * )
     *
     * @param array $pItem
     *
     * @return array
     */
    function prepareRow($pItem) {

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

?>
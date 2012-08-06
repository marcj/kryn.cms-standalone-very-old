<?php


namespace Core\ORM;


class SQL extends ORMAbstract {

	/**
	 * Table name
	 * @var string
	 */
	public $table = '';

	public $fields = array();

	public $rowModifier = '';

    public function __construct($pProperties){
        if (is_array($pProperties)){
            foreach ($pProperties as $k => $v){
                $this[$k] = $v;
            }
        }

    }

	public function remove($pPk){

		$res = dbDelete($this->table, $pPk);
        $this->removeN2N($pPk);
        return $res;

	}

    /**
     * Remove all related database entries from selected item.
     *
     * @param array $pPk
     */
    private function removeN2N($pPk) {
        foreach ($this->fields as $key => $column) {
            if ($column['type'] == 'select' && $column['relation'] == 'n-n') {

                $sql = "DELETE FROM " . dbTableName($column['n-n']['middle']) . " WHERE " . $column['n-n']['middle_keyleft'] .
                       " = ?";
                dbExec($sql, array($pPk[$column['n-n']['left_key']]));
            }
        }
    }

	/**
     * Builds the complete SQL for all items.
     *
     * @param bool $pCountSql Defines whether the SQL is used for counting or not
     *
     * @return string
     */
    function getFullListSql($pCountSql = false) {

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

        foreach ($this->columns as $key => $column) {

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
    public function getCount($pCondition = false) {

        /* count sql */
        $countSql = preg_replace('/SELECT(.*)FROM/mi', 'SELECT count(*) as ctn FROM', str_replace("\n", " ", $this->getFullSql(true)));
        $temp = dbExfetch($countSql);
        return $temp['ctn']+0;
    }


    public function getItems($pCondition, $pOptions = false){

    	$this->listSql = $this->getFullListSql();

        $results['maxItems'] = $this->getCount();

        if ($results['maxItems'] > 0)
            $results['maxPages'] = ceil($results['maxItems'] / $this->itemsPerPage);
        else
            $results['maxPages'] = 0;

    	$order = '';
        if ($pOptions['order'])
			$order = dbOrderToSql($pOptions['order']);

		$limit = '';
		if ($pOptios['offset'])
			$limit .= 'LIMIT '.($pOptios['offset']+0);
		else if ($this->itemsPerPage){
			$limit .= 'LIMIT '.($this->itemsPerPage+0);
		}

		if ($pOptios['offset']){
			$limit .= ' OFFSET '.($pOptios['offset']+0);
		}

        $unique = '';
        $listSql = "
        SELECT * FROM (
            " . $this->listSql . "
            $unique
            $order
        ) as t
        $limit";

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

            $mod = $this->rowModifier;
            if ($mod && method_exists($this, $mod))
                $item = $this->$mod($item);

            if ($item !== false && $item !== null)
                $results[] = $item;

        }
        return $result;

    }









    /**
     *
     * Returns a object item as array.
     *
     * @abstract
     * @param mixed  $pPk
     * @param string $pFields
     * @param string $pResolveForeignValues
     *
     * @return array
     */
    public function getItem($pPk, $pFields = '*', $pResolveForeignValues = '*'){

    }


    /**
     * @abstract
     * @param $pValues
     * @param $pParentPk
     * @param $pMode
     * @param $pParentObjectKey
     *
     * @return inserted primary key. (last_insert_id() for SQL backend)
     */
    public function add($pValues, $pParentPk = false, $pMode = 'into', $pParentObjectKey = false){

    }

    /**
     * Updates an object
     *
     * @abstract
     * @param $pPk
     * @param $pValues
     */
    public function update($pPk, $pValues){

    }


    public function getBranch($pParent = false, $pCondition = false, $pDepth = 1, $pScopeId = false,
        $pOptions = false){

    }









}
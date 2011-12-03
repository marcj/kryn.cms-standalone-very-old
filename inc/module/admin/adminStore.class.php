<?php


class adminStore {


    public $itemsMaxCount = 5;

    public $table;
    public $label;
    public $id;

    /**
     * Additional where clauses started with 'AND '
     */
    public $where;

    public $sql;

    public $order;
    public $orderDirection;


    /**
     * Handles the incomming commands/arguments (posts/gets)
     */
    public function handle($pDefinition) {

        if ($pDefinition) {
            foreach ($pDefinition as $key => $value) {
                $this->$key = $value;
            }
        }

        switch (getArgv('cmd')) {
            case 'item':
                return $this->getItem(getArgv('id'));
            case 'items':
                return $this->getItems(getArgv('from') + 0, getArgv('count') + 0);
            default:
                if (getArgv('id'))
                    return $this->getItem(getArgv('id'));
                else
                    return $this->getItems(getArgv('from') + 0, getArgv('count') + 0);
        }

    }

    public function getItem($pId) {

        $res = array();
        if (!$this->table) return $res;
        $table = database::getTable($this->table);

        $id = $pId;
        if ($id + 0 > 0) {
            $id += 0;
        } else {
            $id = "'" . esc($id) . "'";
        }

        $sql = ' SELECT ' . $this->id . ' as id, ' . $this->label . ' as label
            FROM ' . $table . '
            WHERE ' . $this->id . ' = ' . $id;

        $res = dbExfetch($sql, 1);
        return $res;
    }

    /**
     * Returns a where clausel without "WHERE "
     *
     * @return string
     */

    public function getItemsWhere() {
        return '';
    }

    public function getSearchWhere() {

        if (getArgv('search')) {
            $search = strtolower(getArgv('search', 1));
            return ' AND LOWER(' . $this->label . ") LIKE '$search%' ";
        }

        return '';
    }

    public function getLimit($pFrom, $pCount) {
        $limit = '';
        if ($pFrom > 0)
            $limit = 'OFFSET ' . $pFrom;

        if ($pCount > 0)
            $limit .= ' LIMIT ' . $pCount;

        return $limit;
    }

    /**
     * Returns the items as a hash ( id => label)
     *
     * @return array
     */
    public function getItems($pFrom = 0, $pCount = 0) {

        $res = array();
        $pFrom += 0;
        $pCount += 0;

        if (!$this->table) return $res;

        $table = database::getTable($this->table);

        $limit = $this->getLimit($pFrom, $pCount);

        $where = $this->where;
        if (!$where)
            $where = $this->getItemsWhere();

        $where .= $this->getSearchWhere();

        if ($this->sql) {
            $sql .= $limit;
        } else {
            $sql = ' SELECT ' . $this->id . ', ' . $this->label . '
            FROM ' . $table .
                   ' WHERE 1=1 ' . $where
                   . $limit;
        }

        $dbRes = dbExec($sql);
        while ($row = dbFetch($dbRes)) {
            $res[$row[$this->id]] = $row[$this->label];
        }

        return $res;
    }

}

?>
<?php

namespace Core\Config;

use Core\Kryn;

class ConditionSubSelect extends Condition
{

    CONST DESC = 'DESC';
    CONST ASC = 'ASC';

    protected $select;

    protected $order;

    protected $joins = [];

    protected $selfJoins = [];

    protected $tableName = '';

    protected $tableNameSelect = '';

    public function fromArray($values, $key = null)
    {
        $this->rules = $values;
    }

    public function addJoin($table, $on)
    {
        $this->joins[] = $table . ' ON ' . $on;
    }

    public function addSelfJoin($alias, $on)
    {
        $this->selfJoins[$alias] = $on;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function setTableNameSelect($tableName)
    {
        $this->tableNameSelect = $tableName;
    }


    public function select($select)
    {
        $this->select = $select;
    }

    public function orderBy($field, $order = ConditionSubSelect::ASC)
    {
        $this->order = [$field, $order];
    }

    public function getSelect()
    {
        return $this->select;
    }

    public function getOrderBy()
    {
        return $this->order;
    }

    /**
     * Returns the actual result of the sub-select
     *
     * @return mixed
     */
    public function getValue($objectKey, &$usedFieldNames = array())
    {
        $params = [];
        $sql = $this->toSql($params, $objectKey, $usedFieldNames);
        $row = dbExFetch($sql, $params);

        return 1 === count($row) ? current($row) : $row;
    }

    public function toSql(&$params, $objectKey, &$usedFieldNames = array())
    {
        $tableName = $this->tableNameSelect;
        if ($objectKey) {
            $def = \Core\Object::getDefinition($objectKey);
            if ($def) {
                $tableName = Kryn::getSystemConfig()->getDatabase()->getPrefix() . $def->getTable();
            }
        }

        if (is_array($this->select)) {
            foreach ($this->select as $select) {
                $usedFieldNames[] = $select;
            }
            $selected = implode(', ', $this->select);
        } else {
            $usedFieldNames[] = $this->select;
            $selected = $tableName.'.'.$this->select;
        }

        $joins = '';

        if ($this->joins) {
            $joins .= implode("\n", $this->joins);
        }

        if ($this->selfJoins) {
            foreach ($this->selfJoins as $alias => $on) {
                $joins .= sprintf('JOIN %s as %s ON (%s)',
                    $tableName,
                    $alias,
                    str_replace('%table%', $tableName, $on)
                );
            }
        }

        $sql = sprintf('SELECT %s FROM %s %s',
            $selected,
            $tableName ? : $objectKey,
            $joins
        );

        if ($where = parent::toSql($params, $objectKey, $usedFieldNames)) {
            $sql .= ' WHERE ' . $where;
        }

        if ($this->order) {
            $sql .= sprintf(' ORDER BY %s %s', $this->order[0], $this->order[1]);
        }

        return $sql;
    }

}
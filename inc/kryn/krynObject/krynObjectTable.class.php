<?php


class krynObjectTable {

    /**
     * Object definition
     *
     * @var array
     */
    public $definition = array();

    /**
     * Constructor
     *
     * @param $pDefinition
     */
    function __construct($pDefinition){
        $this->definition = $pDefinition;
    }


    /**
     * Returns the value of $pPrimary
     * @param $pId
     * @param string $pFields
     * @return type
     */
    public function getItem($pId, $pFields = '*'){

        $primary = $this->definition['table_primary'];
        $options = database::getOptions($this->definition['table']);

        $where = '';
        if (is_array($primary)){
            $where  = '1=1 ';
            foreach ($primary as $p) {
                $where .= ' AND '.$p.' = ';
                if ($options[$p]['escape'] == 'int')
                    $where .= $pId[$p]+0;
                else
                    $where .= "'".esc($pId[$p])."'";
            }
        } else {
            $where = $primary.' = ';
            if ($options[$primary]['escape'] == 'int')
                $where .= $pId+0;
            else
                $where .= "'".esc($pId)."'";
        }

        return dbTableFetch($this->definition['table'], $where, 1, $pFields);
    }

    /**
     * @param int $pFrom
     * @param bool $pLimit
     * @param bool $pCondition
     * @param string $pFields
     * @return type
     */
    public function getItems ($pOffset = 0, $pLimit = false, $pCondition = false, $pFields = '*'){

        $where = '';

        if ($pOffset > 0)
            $where .= ' OFFSET '.($pOffset+0);

        if ($pLimit > 0)
            $where .= ' LIMIT '.($pLimit+0);

        //todo, handle pCondition

        return dbTableFetch($this->definition['table'], $where, -1, $pFields);
    }


    public function getCount($pCondition = false){

        //todo, handle pCondition

        return dbCount($this->definition['table']);


    }
}

?>
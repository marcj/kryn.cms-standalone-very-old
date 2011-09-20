<?php


class adminStore {


    public $itemsMaxCount = 5;
    
    public $table;
    public $label;
    public $id;
    public $where;
    
    public $sql;

    public $order;
    public $orderDirection;


    /**
    * Handles the incomming commands/arguments (posts/gets)
    */
    public function handle( $pDefinition ){

        if( $pDefinition ){
            foreach( $pDefinition as $key => $value ){
                $this->$key = $value;
            }
        }

        switch( getArgv('cmd') ){
            case 'items':
            default:
                return self::getItems( getArgv('from')+0, getArgv('count')+0 );
        }
    
    }
    
    /**
    * Returns a where clausel without "WHERE "
    * @return string 
    */
    
    public function getItemsWhere(){
        return '';
    }
    
    /**
    * Returns the items as a hash ( id => label)
    * @return array 
    */
    public function getItems( $pFrom = 0, $pCount = 0 ){
        
        $res = array();
        
        if( !$this->table ) return $res;
        
        $table = database::getTable( $this->table );
        
        $limit = 'OFFSET '.($pFrom+0);
        if( $pCount > 0 )
            $limit .= ' LIMIT '.($pCount+0);
        
        $where = $this->where;
        if( !$where )
            $where = $this->getItemsWhere();
        
        if( $this->sql ){
            $sql .= $limit;
        } else {
            $sql = ' SELECT '.$this->id.', '.$this->label.'
            FROM '.$table.'
            '.($where?'WHERE '.$where:'').'
            '.$limit;
        }
        $dbRes = dbExec( $sql );
        while( $row = dbFetch($dbRes) ){
            $res[ $row[$this->id] ] = $row[$this->label];
        }

        return $res;
    }

}

?>
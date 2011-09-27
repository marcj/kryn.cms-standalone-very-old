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
            case 'item':
                return self::getItem( getArgv('id') );
            case 'items':
            default:
                return self::getItems( getArgv('from')+0, getArgv('count')+0 );
        }
    
    }
    
    public function getItem( $pId ){
    
        $res = array();
        if( !$this->table ) return $res;
        $table = database::getTable( $this->table );
        
        $id = $pId;
        if( $id+0 > 0 ){
            $id += 0;
        } else {
            $id = "'".esc($id)."'";
        }
        
        $sql = ' SELECT '.$this->id.' as id, '.$this->label.' as label
            FROM '.$table.'
            WHERE '.$this->id.' = '.$id;
            
        $res = dbExfetch( $sql , 1 );
        return $res;
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
        $pFrom += 0;
        $pCount += 0;
        
        if( !$this->table ) return $res;
        
        $table = database::getTable( $this->table );
        
        if( $pFrom > 0 )
            $limit = 'OFFSET '.$pFrom;

        if( $pCount > 0 )
                $limit .= ' LIMIT '.$pCount;
        
        $where = $this->where;
        if( !$where )
            $where = $this->getItemsWhere();
            
        if( getArgv('search') ){
            $search = strtolower(getArgv('search', 1));
            
            $add = strlen($where) > 0 ? ' AND ':'';
            
            $where = $add.' LOWER('.$this->label.") LIKE '$search%'";

        }
        
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
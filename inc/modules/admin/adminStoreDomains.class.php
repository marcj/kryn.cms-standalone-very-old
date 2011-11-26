<?php


class adminStoreDomains extends adminStore {

    
    public function getItem( $pId ){
    
        if( !kryn::checkDomainAccess($pId, 'showDomain') )
            return array();
    
        $pId += 0;
        $domain = dbExfetch('SELECT rsn, domain, lang FROM %pfx%system_domains WHERE rsn = '.$pId);
        if( !$domain )
            return array();

        return array(
            'id' => $pId,
            'label' => '['.$domain['lang']. '] ' .$domain['domain']
        );
    }
    
    public function getItems( $pFrom = 0, $pCount = 0 ){
        
        $where = $this->getSearchWhere();
        $limit = $this->getLimit($pFrom, $pCount);

        $res = dbExec('SELECT rsn, domain, lang FROM %pfx%system_domains '.$where.$limit);

        $domains = array();
        while( $domain = dbFetch($res) ){
            if( kryn::checkDomainAccess($domain['rsn'], 'showDomain') ){
                $domains[$domain['rsn']] =  '['.$domain['lang']. '] ' .$domain['domain'];
            }
        }

        return $domains;
    }

}

?>
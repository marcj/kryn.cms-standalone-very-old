<?php


class adminStoreDomains extends adminStore {


    public function getItem($pId) {

        if (!kryn::checkDomainAccess($pId, 'showDomain'))
            return array();

        $pId += 0;
        $domain = dbExfetch('SELECT id, domain, lang FROM %pfx%system_domains WHERE id = ' . $pId);
        if (!$domain)
            return array();

        return array(
            'id' => $pId,
            'label' => '[' . $domain['lang'] . '] ' . $domain['domain']
        );
    }

    public function getItems($pFrom = 0, $pCount = 0) {

        $where = $this->getSearchWhere();
        $limit = $this->getLimit($pFrom, $pCount);

        $res = dbExec('SELECT id, domain, lang FROM %pfx%system_domains ' . $where . $limit);

        $domains = array();
        while ($domain = dbFetch($res)) {
            if (kryn::checkDomainAccess($domain['id'], 'showDomain')) {
                $domains[$domain['id']] = '[' . $domain['lang'] . '] ' . $domain['domain'];
            }
        }

        return $domains;
    }

}

?>
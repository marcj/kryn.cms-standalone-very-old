<?php

namespace Admin;

class SearchIndexList extends windowList
{
    public $table = 'system_search';
    public $itemsPerPage = 20;
    public $orderBy = 'domain_id';

    public $iconAdd = 'add.png';
    public $iconDelete = 'cross.png';
    public $iconCustom = 'admin/images/icons/lightning_delete.png';

    public $filter = array('title', 'domain_id', 'url');

    public $add = false;
    public $edit = false;
    public $remove = false;

    public $modifier = 'addDomainLanguage';

    public $primary = array('url', 'domain_id');

    /*public $itemActions = array(
        array('Set this url on the blacklist', 'admin/images/icons/cross.png', 'admin/system/searchBlacklist/addPage'),
    );*/

    public $columns = array(
        'url' => array(
            'label' => 'URL',
            'type' => 'text'
        ),
        'title' => array(
            'label' => 'Titel',
            'type' => 'text'
        ),
        'mdate' => array(
            'label' => 'Date of index',
            'width' => 110,
            'type' => 'datetime'
        ),
        'domain_id' => array(
            'label' => 'Domain ( Language )',
            'type' => 'select',
            'table' => 'system_domains',
            'table_label' => 'domain',
            'width' => 210,
            'table_key' => 'id'
        )
    );

    public function addDomainLanguage($pItem)
    {
        $pItem['values']['domain_id__label'] =
            $pItem['values']['domain_id__label'] . " ( " . $pItem['values']['lang'] . " )";

        return $pItem;
    }

    public function filterSql()
    {
        $res = parent::filterSql();
        $res .= " AND " . pfx . $this->table . ".mdate > 0 ";

        return $res;
    }

}

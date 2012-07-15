<?php



/**
 * This class defines the structure of the 'kryn_system_page' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.Kryn.map
 */
class PageTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Kryn.map.PageTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('kryn_system_page');
        $this->setPhpName('Page');
        $this->setClassname('Page');
        $this->setPackage('Kryn');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('kryn_system_page_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('PARENT_ID', 'ParentId', 'INTEGER', 'kryn_system_page', 'ID', false, null, null);
        $this->addForeignKey('DOMAIN_ID', 'DomainId', 'INTEGER', 'kryn_system_domain', 'ID', false, null, null);
        $this->addColumn('LFT', 'Lft', 'INTEGER', false, null, null);
        $this->addColumn('RGT', 'Rgt', 'INTEGER', false, null, null);
        $this->addColumn('LVL', 'Lvl', 'INTEGER', false, null, null);
        $this->addColumn('TYPE', 'Type', 'INTEGER', false, null, null);
        $this->addColumn('TITLE', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('PAGE_TITLE', 'PageTitle', 'VARCHAR', false, 255, null);
        $this->addColumn('URL', 'Url', 'VARCHAR', false, 255, null);
        $this->addColumn('FULL_URL', 'FullUrl', 'VARCHAR', false, 255, null);
        $this->addColumn('LINK', 'Link', 'VARCHAR', false, 255, null);
        $this->addColumn('LAYOUT', 'Layout', 'VARCHAR', false, 64, null);
        $this->addColumn('SORT', 'Sort', 'INTEGER', false, null, null);
        $this->addColumn('SORT_MODE', 'SortMode', 'VARCHAR', false, 8, null);
        $this->addColumn('TARGET', 'Target', 'VARCHAR', false, 64, null);
        $this->addColumn('VISIBLE', 'Visible', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_DENIED', 'AccessDenied', 'VARCHAR', false, 255, null);
        $this->addColumn('META', 'Meta', 'LONGVARCHAR', false, null, null);
        $this->addColumn('PROPERTIES', 'Properties', 'LONGVARCHAR', false, null, null);
        $this->addColumn('CDATE', 'Cdate', 'INTEGER', false, null, null);
        $this->addColumn('MDATE', 'Mdate', 'INTEGER', false, null, null);
        $this->addColumn('DRAFT_EXIST', 'DraftExist', 'INTEGER', false, null, null);
        $this->addColumn('FORCE_HTTPS', 'ForceHttps', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_FROM', 'AccessFrom', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_TO', 'AccessTo', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_REDIRECTTO', 'AccessRedirectto', 'VARCHAR', false, 255, null);
        $this->addColumn('ACCESS_NOHIDENAVI', 'AccessNohidenavi', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_NEED_VIA', 'AccessNeedVia', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_FROM_GROUPS', 'AccessFromGroups', 'VARCHAR', false, 32, null);
        $this->addColumn('CACHE', 'Cache', 'INTEGER', false, null, null);
        $this->addColumn('SEARCH_WORDS', 'SearchWords', 'LONGVARCHAR', false, null, null);
        $this->addColumn('UNSEARCHABLE', 'Unsearchable', 'INTEGER', false, null, null);
        $this->addColumn('ACTIVE_VERSION_ID', 'ActiveVersionId', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Domain', 'Domain', RelationMap::MANY_TO_ONE, array('domain_id' => 'id', ), 'CASCADE', null);
        $this->addRelation('PageRelatedByParentId', 'Page', RelationMap::MANY_TO_ONE, array('parent_id' => 'id', ), null, null);
        $this->addRelation('PageContent', 'PageContent', RelationMap::ONE_TO_MANY, array('id' => 'page_id', ), 'CASCADE', null, 'PageContents');
        $this->addRelation('PageRelatedById', 'Page', RelationMap::ONE_TO_MANY, array('id' => 'parent_id', ), null, null, 'PagesRelatedById');
        $this->addRelation('Urlalias', 'Urlalias', RelationMap::ONE_TO_MANY, array('id' => 'to_page_id', ), null, null, 'Urlaliass');
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'nested_set' => array('left_column' => 'lft', 'right_column' => 'rgt', 'level_column' => 'lvl', 'use_scope' => 'true', 'scope_column' => 'domain_id', 'method_proxies' => 'false', ),
        );
    } // getBehaviors()

} // PageTableMap

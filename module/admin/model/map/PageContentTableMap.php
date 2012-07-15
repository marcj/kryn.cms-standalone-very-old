<?php



/**
 * This class defines the structure of the 'kryn_system_page_content' table.
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
class PageContentTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Kryn.map.PageContentTableMap';

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
        $this->setName('kryn_system_page_content');
        $this->setPhpName('PageContent');
        $this->setClassname('PageContent');
        $this->setPackage('Kryn');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('kryn_system_page_content_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addForeignKey('PAGE_ID', 'PageId', 'INTEGER', 'kryn_system_page', 'ID', false, null, null);
        $this->addColumn('BOX_ID', 'BoxId', 'INTEGER', false, null, null);
        $this->addColumn('SORTABLE_ID', 'SortableId', 'VARCHAR', false, 32, null);
        $this->addColumn('TITLE', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('CONTENT', 'Content', 'LONGVARCHAR', false, null, null);
        $this->addColumn('TEMPLATE', 'Template', 'VARCHAR', false, 64, null);
        $this->addColumn('TYPE', 'Type', 'VARCHAR', false, 64, null);
        $this->addColumn('HIDE', 'Hide', 'INTEGER', false, null, null);
        $this->addColumn('OWNER_ID', 'OwnerId', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_FROM', 'AccessFrom', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_TO', 'AccessTo', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_FROM_GROUPS', 'AccessFromGroups', 'VARCHAR', false, 32, null);
        $this->addColumn('UNSEARCHABLE', 'Unsearchable', 'INTEGER', false, null, null);
        $this->addColumn('SORTABLE_RANK', 'SortableRank', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Page', 'Page', RelationMap::MANY_TO_ONE, array('page_id' => 'id', ), 'CASCADE', null);
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
            'sluggable' => array('slug_column' => 'sortable_id', 'slug_pattern' => '{PageId}_{BoxId}', 'replace_pattern' => '/[^\w\/]+/u', 'replacement' => '-', 'separator' => '/', 'permanent' => 'false', 'scope_column' => '', ),
            'sortable' => array('rank_column' => 'sortable_rank', 'use_scope' => 'true', 'scope_column' => 'sortable_id', ),
        );
    } // getBehaviors()

} // PageContentTableMap

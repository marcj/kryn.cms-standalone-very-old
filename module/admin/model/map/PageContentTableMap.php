<?php



/**
 * This class defines the structure of the 'kryn_system_content' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.kryn.map
 */
class PageContentTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'kryn.map.PageContentTableMap';

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
        $this->setName('kryn_system_content');
        $this->setPhpName('PageContent');
        $this->setClassname('PageContent');
        $this->setPackage('kryn');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('kryn_system_content_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('PAGE_ID', 'PageId', 'INTEGER', false, null, null);
        $this->addColumn('VERSION_ID', 'VersionId', 'INTEGER', false, null, null);
        $this->addColumn('TITLE', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('CONTENT', 'Content', 'LONGVARCHAR', false, null, null);
        $this->addColumn('TEMPLATE', 'Template', 'VARCHAR', false, 64, null);
        $this->addColumn('TYPE', 'Type', 'VARCHAR', false, 64, null);
        $this->addColumn('MDATE', 'Mdate', 'INTEGER', false, null, null);
        $this->addColumn('CDATE', 'Cdate', 'INTEGER', false, null, null);
        $this->addColumn('HIDE', 'Hide', 'INTEGER', false, null, null);
        $this->addColumn('SORT', 'Sort', 'INTEGER', false, null, null);
        $this->addColumn('BOX_ID', 'BoxId', 'INTEGER', false, null, null);
        $this->addColumn('OWNER_ID', 'OwnerId', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_FROM', 'AccessFrom', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_TO', 'AccessTo', 'INTEGER', false, null, null);
        $this->addColumn('ACCESS_FROM_GROUPS', 'AccessFromGroups', 'VARCHAR', false, 32, null);
        $this->addColumn('UNSEARCHABLE', 'Unsearchable', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // PageContentTableMap

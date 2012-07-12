<?php



/**
 * This class defines the structure of the 'kryn_system_pages' table.
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
class SystemPagesTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'kryn.map.SystemPagesTableMap';

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
        $this->setName('kryn_system_pages');
        $this->setPhpName('SystemPages');
        $this->setClassname('SystemPages');
        $this->setPackage('kryn');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('kryn_system_pages_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('PID', 'Pid', 'INTEGER', false, null, null);
        $this->addColumn('DOMAIN_ID', 'DomainId', 'INTEGER', false, null, null);
        $this->addColumn('TYPE', 'Type', 'INTEGER', false, null, null);
        $this->addColumn('TITLE', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('PAGE_TITLE', 'PageTitle', 'VARCHAR', false, 255, null);
        $this->addColumn('URL', 'Url', 'VARCHAR', false, 255, null);
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
        $this->addColumn('LFT', 'Lft', 'INTEGER', false, null, null);
        $this->addColumn('RGT', 'Rgt', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // SystemPagesTableMap

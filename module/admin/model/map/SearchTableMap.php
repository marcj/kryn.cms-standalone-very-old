<?php



/**
 * This class defines the structure of the 'kryn_system_search' table.
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
class SearchTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Kryn.map.SearchTableMap';

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
        $this->setName('kryn_system_search');
        $this->setPhpName('Search');
        $this->setClassname('Search');
        $this->setPackage('Kryn');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('URL', 'Url', 'VARCHAR', true, 255, null);
        $this->addColumn('TITLE', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('MD5', 'Md5', 'VARCHAR', false, 255, null);
        $this->addColumn('MDATE', 'Mdate', 'INTEGER', false, null, null);
        $this->addColumn('BLACKLIST', 'Blacklist', 'INTEGER', false, null, null);
        $this->addColumn('PAGE_ID', 'PageId', 'INTEGER', false, null, null);
        $this->addPrimaryKey('DOMAIN_ID', 'DomainId', 'INTEGER', true, null, null);
        $this->addColumn('PAGE_CONTENT', 'PageContent', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // SearchTableMap

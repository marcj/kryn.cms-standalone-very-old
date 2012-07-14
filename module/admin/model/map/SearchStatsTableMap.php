<?php



/**
 * This class defines the structure of the 'kryn_system_search_stats' table.
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
class SearchStatsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Kryn.map.SearchStatsTableMap';

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
        $this->setName('kryn_system_search_stats');
        $this->setPhpName('SearchStats');
        $this->setClassname('SearchStats');
        $this->setPackage('Kryn');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('WORD', 'Word', 'VARCHAR', true, 255, null);
        $this->addColumn('SEARCHCOUNT', 'Searchcount', 'INTEGER', false, null, null);
        $this->addPrimaryKey('FOUND', 'Found', 'INTEGER', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // SearchStatsTableMap

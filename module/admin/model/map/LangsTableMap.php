<?php



/**
 * This class defines the structure of the 'kryn_system_langs' table.
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
class LangsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Kryn.map.LangsTableMap';

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
        $this->setName('kryn_system_langs');
        $this->setPhpName('Langs');
        $this->setClassname('Langs');
        $this->setPackage('Kryn');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('CODE', 'Code', 'VARCHAR', true, 3, null);
        $this->addColumn('TITLE', 'Title', 'VARCHAR', false, 255, null);
        $this->addColumn('LANGTITLE', 'Langtitle', 'VARCHAR', false, 255, null);
        $this->addColumn('USERDEFINED', 'Userdefined', 'INTEGER', false, null, null);
        $this->addColumn('VISIBLE', 'Visible', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // LangsTableMap

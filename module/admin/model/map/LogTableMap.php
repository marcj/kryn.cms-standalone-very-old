<?php



/**
 * This class defines the structure of the 'kryn_system_log' table.
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
class LogTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Kryn.map.LogTableMap';

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
        $this->setName('kryn_system_log');
        $this->setPhpName('Log');
        $this->setClassname('Log');
        $this->setPackage('Kryn');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('kryn_system_log_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('DATE', 'Date', 'INTEGER', false, null, null);
        $this->addColumn('USERNAME', 'Username', 'VARCHAR', false, 64, null);
        $this->addColumn('IP', 'Ip', 'VARCHAR', false, 32, null);
        $this->addColumn('CODE', 'Code', 'VARCHAR', false, 32, null);
        $this->addColumn('MESSAGE', 'Message', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // LogTableMap

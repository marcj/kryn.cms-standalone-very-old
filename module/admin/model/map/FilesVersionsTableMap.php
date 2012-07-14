<?php



/**
 * This class defines the structure of the 'kryn_system_files_versions' table.
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
class FilesVersionsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'kryn.map.FilesVersionsTableMap';

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
        $this->setName('kryn_system_files_versions');
        $this->setPhpName('FilesVersions');
        $this->setClassname('FilesVersions');
        $this->setPackage('kryn');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('kryn_system_files_versions_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('PATH', 'Path', 'VARCHAR', false, 255, null);
        $this->addColumn('CREATED', 'Created', 'INTEGER', false, null, null);
        $this->addColumn('MTIME', 'Mtime', 'INTEGER', false, null, null);
        $this->addColumn('USER_ID', 'UserId', 'INTEGER', false, null, null);
        $this->addColumn('VERSIONPATH', 'Versionpath', 'VARCHAR', false, 255, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // FilesVersionsTableMap

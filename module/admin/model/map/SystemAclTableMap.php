<?php



/**
 * This class defines the structure of the 'kryn_system_acl' table.
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
class SystemAclTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'kryn.map.SystemAclTableMap';

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
        $this->setName('kryn_system_acl');
        $this->setPhpName('SystemAcl');
        $this->setClassname('SystemAcl');
        $this->setPackage('kryn');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('kryn_system_acl_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('OBJECT', 'Object', 'VARCHAR', false, 64, null);
        $this->addColumn('TARGET_TYPE', 'TargetType', 'INTEGER', false, null, null);
        $this->addColumn('TARGET_ID', 'TargetId', 'INTEGER', false, null, null);
        $this->addColumn('SUB', 'Sub', 'SMALLINT', false, null, null);
        $this->addColumn('FIELDS', 'Fields', 'LONGVARCHAR', false, null, null);
        $this->addColumn('ACCESS', 'Access', 'SMALLINT', false, null, null);
        $this->addColumn('PRIO', 'Prio', 'INTEGER', false, null, null);
        $this->addColumn('MODE', 'Mode', 'SMALLINT', false, null, null);
        $this->addColumn('CONSTRAINT_TYPE', 'ConstraintType', 'SMALLINT', false, null, null);
        $this->addColumn('CONSTRAINT_CODE', 'ConstraintCode', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // SystemAclTableMap

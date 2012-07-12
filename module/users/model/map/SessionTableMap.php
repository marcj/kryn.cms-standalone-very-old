<?php



/**
 * This class defines the structure of the 'kryn_system_session' table.
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
class SessionTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'kryn.map.SessionTableMap';

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
        $this->setName('kryn_system_session');
        $this->setPhpName('Session');
        $this->setClassname('Session');
        $this->setPackage('kryn');
        $this->setUseIdGenerator(false);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'VARCHAR', true, 255, null);
        $this->addForeignKey('USER_ID', 'UserId', 'INTEGER', 'kryn_system_user', 'ID', false, null, null);
        $this->addColumn('TIME', 'Time', 'INTEGER', true, null, null);
        $this->addColumn('IP', 'Ip', 'VARCHAR', false, 255, null);
        $this->addColumn('USERAGENT', 'Useragent', 'VARCHAR', false, 255, null);
        $this->addColumn('LANGUAGE', 'Language', 'VARCHAR', false, 255, null);
        $this->addColumn('PAGE', 'Page', 'VARCHAR', false, 255, null);
        $this->addColumn('REFRESHED', 'Refreshed', 'INTEGER', false, null, null);
        $this->addColumn('EXTRA', 'Extra', 'LONGVARCHAR', false, null, null);
        $this->addColumn('CREATED', 'Created', 'INTEGER', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('User', 'User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), null, null);
    } // buildRelations()

} // SessionTableMap

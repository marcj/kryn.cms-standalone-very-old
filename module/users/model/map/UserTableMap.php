<?php



/**
 * This class defines the structure of the 'kryn_system_user' table.
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
class UserTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Kryn.map.UserTableMap';

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
        $this->setName('kryn_system_user');
        $this->setPhpName('User');
        $this->setClassname('User');
        $this->setPackage('Kryn');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('kryn_system_user_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('USERNAME', 'Username', 'VARCHAR', true, 255, null);
        $this->addColumn('AUTH_CLASS', 'AuthClass', 'VARCHAR', false, 255, null);
        $this->addColumn('PASSWD', 'Passwd', 'VARCHAR', false, 255, null);
        $this->addColumn('PASSWD_SALT', 'PasswdSalt', 'VARCHAR', false, 255, null);
        $this->addColumn('ACTIVATIONKEY', 'Activationkey', 'VARCHAR', false, 255, null);
        $this->addColumn('EMAIL', 'Email', 'VARCHAR', false, 255, null);
        $this->addColumn('DESKTOP', 'Desktop', 'LONGVARCHAR', false, null, null);
        $this->addColumn('SETTINGS', 'Settings', 'LONGVARCHAR', false, null, null);
        $this->addColumn('CREATED', 'Created', 'INTEGER', true, null, null);
        $this->addColumn('MODIFIED', 'Modified', 'INTEGER', false, null, null);
        $this->addColumn('FIRST_NAME', 'FirstName', 'VARCHAR', false, 255, null);
        $this->addColumn('LAST_NAME', 'LastName', 'VARCHAR', false, 255, null);
        $this->addColumn('SEX', 'Sex', 'INTEGER', false, null, null);
        $this->addColumn('LOGINS', 'Logins', 'INTEGER', false, null, null);
        $this->addColumn('LASTLOGIN', 'Lastlogin', 'INTEGER', false, null, null);
        $this->addColumn('ACTIVATE', 'Activate', 'BOOLEAN', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Session', 'Session', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), null, null, 'Sessions');
        $this->addRelation('UserGroup', 'UserGroup', RelationMap::ONE_TO_MANY, array('id' => 'user_id', ), null, null, 'UserGroups');
    } // buildRelations()

} // UserTableMap

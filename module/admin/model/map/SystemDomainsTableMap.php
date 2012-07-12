<?php



/**
 * This class defines the structure of the 'kryn_system_domains' table.
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
class SystemDomainsTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'kryn.map.SystemDomainsTableMap';

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
        $this->setName('kryn_system_domains');
        $this->setPhpName('SystemDomains');
        $this->setClassname('SystemDomains');
        $this->setPackage('kryn');
        $this->setUseIdGenerator(true);
        $this->setPrimaryKeyMethodInfo('kryn_system_domains_id_seq');
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('DOMAIN', 'Domain', 'VARCHAR', false, 255, null);
        $this->addColumn('TITLE_FORMAT', 'TitleFormat', 'VARCHAR', false, 255, null);
        $this->addColumn('LANG', 'Lang', 'VARCHAR', false, 128, null);
        $this->addColumn('STARTPAGE_ID', 'StartpageId', 'INTEGER', false, null, null);
        $this->addColumn('ALIAS', 'Alias', 'VARCHAR', false, 255, null);
        $this->addColumn('REDIRECT', 'Redirect', 'VARCHAR', false, 255, null);
        $this->addColumn('PAGE404_ID', 'Page404id', 'INTEGER', false, null, null);
        $this->addColumn('PAGE404INTERFACE', 'Page404interface', 'VARCHAR', false, 128, null);
        $this->addColumn('MASTER', 'Master', 'INTEGER', false, null, null);
        $this->addColumn('RESOURCECOMPRESSION', 'Resourcecompression', 'INTEGER', false, null, null);
        $this->addColumn('LAYOUTS', 'Layouts', 'LONGVARCHAR', false, null, null);
        $this->addColumn('PHPLOCALE', 'Phplocale', 'VARCHAR', false, 128, null);
        $this->addColumn('PATH', 'Path', 'VARCHAR', false, 64, null);
        $this->addColumn('THEMEPROPERTIES', 'Themeproperties', 'LONGVARCHAR', false, null, null);
        $this->addColumn('EXTPROPERTIES', 'Extproperties', 'LONGVARCHAR', false, null, null);
        $this->addColumn('EMAIL', 'Email', 'VARCHAR', false, 64, null);
        $this->addColumn('SEARCH_INDEX_KEY', 'SearchIndexKey', 'VARCHAR', false, 255, null);
        $this->addColumn('ROBOTS', 'Robots', 'LONGVARCHAR', false, null, null);
        $this->addColumn('SESSION', 'Session', 'LONGVARCHAR', false, null, null);
        $this->addColumn('FAVICON', 'Favicon', 'VARCHAR', false, 255, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
    } // buildRelations()

} // SystemDomainsTableMap

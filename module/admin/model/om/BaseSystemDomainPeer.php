<?php


/**
 * Base static class for performing query and update operations on the 'kryn_system_domain' table.
 *
 * 
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemDomainPeer {

    /** the default database name for this class */
    const DATABASE_NAME = 'kryn';

    /** the table name for this class */
    const TABLE_NAME = 'kryn_system_domain';

    /** the related Propel class for this table */
    const OM_CLASS = 'SystemDomain';

    /** the related TableMap class for this table */
    const TM_CLASS = 'SystemDomainTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 21;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 21;

    /** the column name for the ID field */
    const ID = 'kryn_system_domain.ID';

    /** the column name for the DOMAIN field */
    const DOMAIN = 'kryn_system_domain.DOMAIN';

    /** the column name for the TITLE_FORMAT field */
    const TITLE_FORMAT = 'kryn_system_domain.TITLE_FORMAT';

    /** the column name for the LANG field */
    const LANG = 'kryn_system_domain.LANG';

    /** the column name for the STARTPAGE_ID field */
    const STARTPAGE_ID = 'kryn_system_domain.STARTPAGE_ID';

    /** the column name for the ALIAS field */
    const ALIAS = 'kryn_system_domain.ALIAS';

    /** the column name for the REDIRECT field */
    const REDIRECT = 'kryn_system_domain.REDIRECT';

    /** the column name for the PAGE404_ID field */
    const PAGE404_ID = 'kryn_system_domain.PAGE404_ID';

    /** the column name for the PAGE404INTERFACE field */
    const PAGE404INTERFACE = 'kryn_system_domain.PAGE404INTERFACE';

    /** the column name for the MASTER field */
    const MASTER = 'kryn_system_domain.MASTER';

    /** the column name for the RESOURCECOMPRESSION field */
    const RESOURCECOMPRESSION = 'kryn_system_domain.RESOURCECOMPRESSION';

    /** the column name for the LAYOUTS field */
    const LAYOUTS = 'kryn_system_domain.LAYOUTS';

    /** the column name for the PHPLOCALE field */
    const PHPLOCALE = 'kryn_system_domain.PHPLOCALE';

    /** the column name for the PATH field */
    const PATH = 'kryn_system_domain.PATH';

    /** the column name for the THEMEPROPERTIES field */
    const THEMEPROPERTIES = 'kryn_system_domain.THEMEPROPERTIES';

    /** the column name for the EXTPROPERTIES field */
    const EXTPROPERTIES = 'kryn_system_domain.EXTPROPERTIES';

    /** the column name for the EMAIL field */
    const EMAIL = 'kryn_system_domain.EMAIL';

    /** the column name for the SEARCH_INDEX_KEY field */
    const SEARCH_INDEX_KEY = 'kryn_system_domain.SEARCH_INDEX_KEY';

    /** the column name for the ROBOTS field */
    const ROBOTS = 'kryn_system_domain.ROBOTS';

    /** the column name for the SESSION field */
    const SESSION = 'kryn_system_domain.SESSION';

    /** the column name for the FAVICON field */
    const FAVICON = 'kryn_system_domain.FAVICON';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identiy map to hold any loaded instances of SystemDomain objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array SystemDomain[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. SystemDomainPeer::$fieldNames[SystemDomainPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'Domain', 'TitleFormat', 'Lang', 'StartpageId', 'Alias', 'Redirect', 'Page404id', 'Page404interface', 'Master', 'Resourcecompression', 'Layouts', 'Phplocale', 'Path', 'Themeproperties', 'Extproperties', 'Email', 'SearchIndexKey', 'Robots', 'Session', 'Favicon', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'domain', 'titleFormat', 'lang', 'startpageId', 'alias', 'redirect', 'page404id', 'page404interface', 'master', 'resourcecompression', 'layouts', 'phplocale', 'path', 'themeproperties', 'extproperties', 'email', 'searchIndexKey', 'robots', 'session', 'favicon', ),
        BasePeer::TYPE_COLNAME => array (SystemDomainPeer::ID, SystemDomainPeer::DOMAIN, SystemDomainPeer::TITLE_FORMAT, SystemDomainPeer::LANG, SystemDomainPeer::STARTPAGE_ID, SystemDomainPeer::ALIAS, SystemDomainPeer::REDIRECT, SystemDomainPeer::PAGE404_ID, SystemDomainPeer::PAGE404INTERFACE, SystemDomainPeer::MASTER, SystemDomainPeer::RESOURCECOMPRESSION, SystemDomainPeer::LAYOUTS, SystemDomainPeer::PHPLOCALE, SystemDomainPeer::PATH, SystemDomainPeer::THEMEPROPERTIES, SystemDomainPeer::EXTPROPERTIES, SystemDomainPeer::EMAIL, SystemDomainPeer::SEARCH_INDEX_KEY, SystemDomainPeer::ROBOTS, SystemDomainPeer::SESSION, SystemDomainPeer::FAVICON, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'DOMAIN', 'TITLE_FORMAT', 'LANG', 'STARTPAGE_ID', 'ALIAS', 'REDIRECT', 'PAGE404_ID', 'PAGE404INTERFACE', 'MASTER', 'RESOURCECOMPRESSION', 'LAYOUTS', 'PHPLOCALE', 'PATH', 'THEMEPROPERTIES', 'EXTPROPERTIES', 'EMAIL', 'SEARCH_INDEX_KEY', 'ROBOTS', 'SESSION', 'FAVICON', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'domain', 'title_format', 'lang', 'startpage_id', 'alias', 'redirect', 'page404_id', 'page404interface', 'master', 'resourcecompression', 'layouts', 'phplocale', 'path', 'themeproperties', 'extproperties', 'email', 'search_index_key', 'robots', 'session', 'favicon', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. SystemDomainPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Domain' => 1, 'TitleFormat' => 2, 'Lang' => 3, 'StartpageId' => 4, 'Alias' => 5, 'Redirect' => 6, 'Page404id' => 7, 'Page404interface' => 8, 'Master' => 9, 'Resourcecompression' => 10, 'Layouts' => 11, 'Phplocale' => 12, 'Path' => 13, 'Themeproperties' => 14, 'Extproperties' => 15, 'Email' => 16, 'SearchIndexKey' => 17, 'Robots' => 18, 'Session' => 19, 'Favicon' => 20, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'domain' => 1, 'titleFormat' => 2, 'lang' => 3, 'startpageId' => 4, 'alias' => 5, 'redirect' => 6, 'page404id' => 7, 'page404interface' => 8, 'master' => 9, 'resourcecompression' => 10, 'layouts' => 11, 'phplocale' => 12, 'path' => 13, 'themeproperties' => 14, 'extproperties' => 15, 'email' => 16, 'searchIndexKey' => 17, 'robots' => 18, 'session' => 19, 'favicon' => 20, ),
        BasePeer::TYPE_COLNAME => array (SystemDomainPeer::ID => 0, SystemDomainPeer::DOMAIN => 1, SystemDomainPeer::TITLE_FORMAT => 2, SystemDomainPeer::LANG => 3, SystemDomainPeer::STARTPAGE_ID => 4, SystemDomainPeer::ALIAS => 5, SystemDomainPeer::REDIRECT => 6, SystemDomainPeer::PAGE404_ID => 7, SystemDomainPeer::PAGE404INTERFACE => 8, SystemDomainPeer::MASTER => 9, SystemDomainPeer::RESOURCECOMPRESSION => 10, SystemDomainPeer::LAYOUTS => 11, SystemDomainPeer::PHPLOCALE => 12, SystemDomainPeer::PATH => 13, SystemDomainPeer::THEMEPROPERTIES => 14, SystemDomainPeer::EXTPROPERTIES => 15, SystemDomainPeer::EMAIL => 16, SystemDomainPeer::SEARCH_INDEX_KEY => 17, SystemDomainPeer::ROBOTS => 18, SystemDomainPeer::SESSION => 19, SystemDomainPeer::FAVICON => 20, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'DOMAIN' => 1, 'TITLE_FORMAT' => 2, 'LANG' => 3, 'STARTPAGE_ID' => 4, 'ALIAS' => 5, 'REDIRECT' => 6, 'PAGE404_ID' => 7, 'PAGE404INTERFACE' => 8, 'MASTER' => 9, 'RESOURCECOMPRESSION' => 10, 'LAYOUTS' => 11, 'PHPLOCALE' => 12, 'PATH' => 13, 'THEMEPROPERTIES' => 14, 'EXTPROPERTIES' => 15, 'EMAIL' => 16, 'SEARCH_INDEX_KEY' => 17, 'ROBOTS' => 18, 'SESSION' => 19, 'FAVICON' => 20, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'domain' => 1, 'title_format' => 2, 'lang' => 3, 'startpage_id' => 4, 'alias' => 5, 'redirect' => 6, 'page404_id' => 7, 'page404interface' => 8, 'master' => 9, 'resourcecompression' => 10, 'layouts' => 11, 'phplocale' => 12, 'path' => 13, 'themeproperties' => 14, 'extproperties' => 15, 'email' => 16, 'search_index_key' => 17, 'robots' => 18, 'session' => 19, 'favicon' => 20, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, )
    );

    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return string          translated name of the field.
     * @throws PropelException - if the specified name could not be found in the fieldname mappings.
     */
    public static function translateFieldName($name, $fromType, $toType)
    {
        $toNames = SystemDomainPeer::getFieldNames($toType);
        $key = isset(SystemDomainPeer::$fieldKeys[$fromType][$name]) ? SystemDomainPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(SystemDomainPeer::$fieldKeys[$fromType], true));
        }

        return $toNames[$key];
    }

    /**
     * Returns an array of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                      BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
     * @return array           A list of field names
     * @throws PropelException - if the type is not valid.
     */
    public static function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, SystemDomainPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return SystemDomainPeer::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *		$c->addAlias("alias1", TablePeer::TABLE_NAME);
     *		$c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. SystemDomainPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(SystemDomainPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      Criteria $criteria object containing the columns to add.
     * @param      string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(SystemDomainPeer::ID);
            $criteria->addSelectColumn(SystemDomainPeer::DOMAIN);
            $criteria->addSelectColumn(SystemDomainPeer::TITLE_FORMAT);
            $criteria->addSelectColumn(SystemDomainPeer::LANG);
            $criteria->addSelectColumn(SystemDomainPeer::STARTPAGE_ID);
            $criteria->addSelectColumn(SystemDomainPeer::ALIAS);
            $criteria->addSelectColumn(SystemDomainPeer::REDIRECT);
            $criteria->addSelectColumn(SystemDomainPeer::PAGE404_ID);
            $criteria->addSelectColumn(SystemDomainPeer::PAGE404INTERFACE);
            $criteria->addSelectColumn(SystemDomainPeer::MASTER);
            $criteria->addSelectColumn(SystemDomainPeer::RESOURCECOMPRESSION);
            $criteria->addSelectColumn(SystemDomainPeer::LAYOUTS);
            $criteria->addSelectColumn(SystemDomainPeer::PHPLOCALE);
            $criteria->addSelectColumn(SystemDomainPeer::PATH);
            $criteria->addSelectColumn(SystemDomainPeer::THEMEPROPERTIES);
            $criteria->addSelectColumn(SystemDomainPeer::EXTPROPERTIES);
            $criteria->addSelectColumn(SystemDomainPeer::EMAIL);
            $criteria->addSelectColumn(SystemDomainPeer::SEARCH_INDEX_KEY);
            $criteria->addSelectColumn(SystemDomainPeer::ROBOTS);
            $criteria->addSelectColumn(SystemDomainPeer::SESSION);
            $criteria->addSelectColumn(SystemDomainPeer::FAVICON);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.DOMAIN');
            $criteria->addSelectColumn($alias . '.TITLE_FORMAT');
            $criteria->addSelectColumn($alias . '.LANG');
            $criteria->addSelectColumn($alias . '.STARTPAGE_ID');
            $criteria->addSelectColumn($alias . '.ALIAS');
            $criteria->addSelectColumn($alias . '.REDIRECT');
            $criteria->addSelectColumn($alias . '.PAGE404_ID');
            $criteria->addSelectColumn($alias . '.PAGE404INTERFACE');
            $criteria->addSelectColumn($alias . '.MASTER');
            $criteria->addSelectColumn($alias . '.RESOURCECOMPRESSION');
            $criteria->addSelectColumn($alias . '.LAYOUTS');
            $criteria->addSelectColumn($alias . '.PHPLOCALE');
            $criteria->addSelectColumn($alias . '.PATH');
            $criteria->addSelectColumn($alias . '.THEMEPROPERTIES');
            $criteria->addSelectColumn($alias . '.EXTPROPERTIES');
            $criteria->addSelectColumn($alias . '.EMAIL');
            $criteria->addSelectColumn($alias . '.SEARCH_INDEX_KEY');
            $criteria->addSelectColumn($alias . '.ROBOTS');
            $criteria->addSelectColumn($alias . '.SESSION');
            $criteria->addSelectColumn($alias . '.FAVICON');
        }
    }

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @return int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null)
    {
        // we may modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(SystemDomainPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            SystemDomainPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(SystemDomainPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        // BasePeer returns a PDOStatement
        $stmt = BasePeer::doCount($criteria, $con);

        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $count = (int) $row[0];
        } else {
            $count = 0; // no rows returned; we infer that means 0 matches.
        }
        $stmt->closeCursor();

        return $count;
    }
    /**
     * Selects one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      PropelPDO $con
     * @return                 SystemDomain
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = SystemDomainPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }

        return null;
    }
    /**
     * Selects several row from the DB.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con
     * @return array           Array of selected Objects
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, PropelPDO $con = null)
    {
        return SystemDomainPeer::populateObjects(SystemDomainPeer::doSelectStmt($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect() method to execute a PDOStatement.
     *
     * Use this method directly if you want to work with an executed statement durirectly (for example
     * to perform your own object hydration).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      PropelPDO $con The connection to use
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return PDOStatement The executed PDOStatement object.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            SystemDomainPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(SystemDomainPeer::DATABASE_NAME);

        // BasePeer returns a PDOStatement
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * Adds an object to the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doSelect*()
     * methods in your stub classes -- you may need to explicitly add objects
     * to the cache in order to ensure that the same objects are always returned by doSelect*()
     * and retrieveByPK*() calls.
     *
     * @param      SystemDomain $obj A SystemDomain object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            SystemDomainPeer::$instances[$key] = $obj;
        }
    }

    /**
     * Removes an object from the instance pool.
     *
     * Propel keeps cached copies of objects in an instance pool when they are retrieved
     * from the database.  In some cases -- especially when you override doDelete
     * methods in your stub classes -- you may need to explicitly remove objects
     * from the cache in order to prevent returning objects that no longer exist.
     *
     * @param      mixed $value A SystemDomain object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof SystemDomain) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or SystemDomain object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(SystemDomainPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return   SystemDomain Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(SystemDomainPeer::$instances[$key])) {
                return SystemDomainPeer::$instances[$key];
            }
        }

        return null; // just to be explicit
    }
    
    /**
     * Clear the instance pool.
     *
     * @return void
     */
    public static function clearInstancePool()
    {
        SystemDomainPeer::$instances = array();
    }
    
    /**
     * Method to invalidate the instance pool of all tables related to kryn_system_domain
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in SystemPagePeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        SystemPagePeer::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return string A string version of PK or NULL if the components of primary key in result array are all null.
     */
    public static function getPrimaryKeyHashFromRow($row, $startcol = 0)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[$startcol] === null) {
            return null;
        }

        return (string) $row[$startcol];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $startcol = 0)
    {

        return (int) $row[$startcol];
    }
    
    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function populateObjects(PDOStatement $stmt)
    {
        $results = array();
    
        // set the class once to avoid overhead in the loop
        $cls = SystemDomainPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = SystemDomainPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = SystemDomainPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                SystemDomainPeer::addInstanceToPool($obj, $key);
            } // if key exists
        }
        $stmt->closeCursor();

        return $results;
    }
    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param      array $row PropelPDO resultset row.
     * @param      int $startcol The 0-based offset for reading from the resultset row.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     * @return array (SystemDomain object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = SystemDomainPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = SystemDomainPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + SystemDomainPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = SystemDomainPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            SystemDomainPeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(SystemDomainPeer::DATABASE_NAME)->getTable(SystemDomainPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseSystemDomainPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseSystemDomainPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new SystemDomainTableMap());
      }
    }

    /**
     * The class that the Peer will make instances of.
     *
     *
     * @return string ClassName
     */
    public static function getOMClass()
    {
        return SystemDomainPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a SystemDomain or Criteria object.
     *
     * @param      mixed $values Criteria or SystemDomain object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from SystemDomain object
        }

        if ($criteria->containsKey(SystemDomainPeer::ID) && $criteria->keyContainsValue(SystemDomainPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.SystemDomainPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(SystemDomainPeer::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->beginTransaction();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }

        return $pk;
    }

    /**
     * Performs an UPDATE on the database, given a SystemDomain or Criteria object.
     *
     * @param      mixed $values Criteria or SystemDomain object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(SystemDomainPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(SystemDomainPeer::ID);
            $value = $criteria->remove(SystemDomainPeer::ID);
            if ($value) {
                $selectCriteria->add(SystemDomainPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(SystemDomainPeer::TABLE_NAME);
            }

        } else { // $values is SystemDomain object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(SystemDomainPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the kryn_system_domain table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(SystemDomainPeer::TABLE_NAME, $con, SystemDomainPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SystemDomainPeer::clearInstancePool();
            SystemDomainPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a SystemDomain or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or SystemDomain object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      PropelPDO $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *				if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, PropelPDO $con = null)
     {
        if ($con === null) {
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            SystemDomainPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof SystemDomain) { // it's a model object
            // invalidate the cache for this single object
            SystemDomainPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SystemDomainPeer::DATABASE_NAME);
            $criteria->add(SystemDomainPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                SystemDomainPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(SystemDomainPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            
            $affectedRows += BasePeer::doDelete($criteria, $con);
            SystemDomainPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given SystemDomain object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      SystemDomain $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(SystemDomainPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(SystemDomainPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->hasColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(SystemDomainPeer::DATABASE_NAME, SystemDomainPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return SystemDomain
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = SystemDomainPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(SystemDomainPeer::DATABASE_NAME);
        $criteria->add(SystemDomainPeer::ID, $pk);

        $v = SystemDomainPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return SystemDomain[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(SystemDomainPeer::DATABASE_NAME);
            $criteria->add(SystemDomainPeer::ID, $pks, Criteria::IN);
            $objs = SystemDomainPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseSystemDomainPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseSystemDomainPeer::buildTableMap();


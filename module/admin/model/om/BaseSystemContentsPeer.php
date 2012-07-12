<?php


/**
 * Base static class for performing query and update operations on the 'kryn_system_contents' table.
 *
 * 
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemContentsPeer {

    /** the default database name for this class */
    const DATABASE_NAME = 'kryn';

    /** the table name for this class */
    const TABLE_NAME = 'kryn_system_contents';

    /** the related Propel class for this table */
    const OM_CLASS = 'SystemContents';

    /** the related TableMap class for this table */
    const TM_CLASS = 'SystemContentsTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 17;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 17;

    /** the column name for the ID field */
    const ID = 'kryn_system_contents.ID';

    /** the column name for the PAGE_ID field */
    const PAGE_ID = 'kryn_system_contents.PAGE_ID';

    /** the column name for the VERSION_ID field */
    const VERSION_ID = 'kryn_system_contents.VERSION_ID';

    /** the column name for the TITLE field */
    const TITLE = 'kryn_system_contents.TITLE';

    /** the column name for the CONTENT field */
    const CONTENT = 'kryn_system_contents.CONTENT';

    /** the column name for the TEMPLATE field */
    const TEMPLATE = 'kryn_system_contents.TEMPLATE';

    /** the column name for the TYPE field */
    const TYPE = 'kryn_system_contents.TYPE';

    /** the column name for the MDATE field */
    const MDATE = 'kryn_system_contents.MDATE';

    /** the column name for the CDATE field */
    const CDATE = 'kryn_system_contents.CDATE';

    /** the column name for the HIDE field */
    const HIDE = 'kryn_system_contents.HIDE';

    /** the column name for the SORT field */
    const SORT = 'kryn_system_contents.SORT';

    /** the column name for the BOX_ID field */
    const BOX_ID = 'kryn_system_contents.BOX_ID';

    /** the column name for the OWNER_ID field */
    const OWNER_ID = 'kryn_system_contents.OWNER_ID';

    /** the column name for the ACCESS_FROM field */
    const ACCESS_FROM = 'kryn_system_contents.ACCESS_FROM';

    /** the column name for the ACCESS_TO field */
    const ACCESS_TO = 'kryn_system_contents.ACCESS_TO';

    /** the column name for the ACCESS_FROM_GROUPS field */
    const ACCESS_FROM_GROUPS = 'kryn_system_contents.ACCESS_FROM_GROUPS';

    /** the column name for the UNSEARCHABLE field */
    const UNSEARCHABLE = 'kryn_system_contents.UNSEARCHABLE';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identiy map to hold any loaded instances of SystemContents objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array SystemContents[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. SystemContentsPeer::$fieldNames[SystemContentsPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'PageId', 'VersionId', 'Title', 'Content', 'Template', 'Type', 'Mdate', 'Cdate', 'Hide', 'Sort', 'BoxId', 'OwnerId', 'AccessFrom', 'AccessTo', 'AccessFromGroups', 'Unsearchable', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'pageId', 'versionId', 'title', 'content', 'template', 'type', 'mdate', 'cdate', 'hide', 'sort', 'boxId', 'ownerId', 'accessFrom', 'accessTo', 'accessFromGroups', 'unsearchable', ),
        BasePeer::TYPE_COLNAME => array (SystemContentsPeer::ID, SystemContentsPeer::PAGE_ID, SystemContentsPeer::VERSION_ID, SystemContentsPeer::TITLE, SystemContentsPeer::CONTENT, SystemContentsPeer::TEMPLATE, SystemContentsPeer::TYPE, SystemContentsPeer::MDATE, SystemContentsPeer::CDATE, SystemContentsPeer::HIDE, SystemContentsPeer::SORT, SystemContentsPeer::BOX_ID, SystemContentsPeer::OWNER_ID, SystemContentsPeer::ACCESS_FROM, SystemContentsPeer::ACCESS_TO, SystemContentsPeer::ACCESS_FROM_GROUPS, SystemContentsPeer::UNSEARCHABLE, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'PAGE_ID', 'VERSION_ID', 'TITLE', 'CONTENT', 'TEMPLATE', 'TYPE', 'MDATE', 'CDATE', 'HIDE', 'SORT', 'BOX_ID', 'OWNER_ID', 'ACCESS_FROM', 'ACCESS_TO', 'ACCESS_FROM_GROUPS', 'UNSEARCHABLE', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'page_id', 'version_id', 'title', 'content', 'template', 'type', 'mdate', 'cdate', 'hide', 'sort', 'box_id', 'owner_id', 'access_from', 'access_to', 'access_from_groups', 'unsearchable', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. SystemContentsPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'PageId' => 1, 'VersionId' => 2, 'Title' => 3, 'Content' => 4, 'Template' => 5, 'Type' => 6, 'Mdate' => 7, 'Cdate' => 8, 'Hide' => 9, 'Sort' => 10, 'BoxId' => 11, 'OwnerId' => 12, 'AccessFrom' => 13, 'AccessTo' => 14, 'AccessFromGroups' => 15, 'Unsearchable' => 16, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'pageId' => 1, 'versionId' => 2, 'title' => 3, 'content' => 4, 'template' => 5, 'type' => 6, 'mdate' => 7, 'cdate' => 8, 'hide' => 9, 'sort' => 10, 'boxId' => 11, 'ownerId' => 12, 'accessFrom' => 13, 'accessTo' => 14, 'accessFromGroups' => 15, 'unsearchable' => 16, ),
        BasePeer::TYPE_COLNAME => array (SystemContentsPeer::ID => 0, SystemContentsPeer::PAGE_ID => 1, SystemContentsPeer::VERSION_ID => 2, SystemContentsPeer::TITLE => 3, SystemContentsPeer::CONTENT => 4, SystemContentsPeer::TEMPLATE => 5, SystemContentsPeer::TYPE => 6, SystemContentsPeer::MDATE => 7, SystemContentsPeer::CDATE => 8, SystemContentsPeer::HIDE => 9, SystemContentsPeer::SORT => 10, SystemContentsPeer::BOX_ID => 11, SystemContentsPeer::OWNER_ID => 12, SystemContentsPeer::ACCESS_FROM => 13, SystemContentsPeer::ACCESS_TO => 14, SystemContentsPeer::ACCESS_FROM_GROUPS => 15, SystemContentsPeer::UNSEARCHABLE => 16, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'PAGE_ID' => 1, 'VERSION_ID' => 2, 'TITLE' => 3, 'CONTENT' => 4, 'TEMPLATE' => 5, 'TYPE' => 6, 'MDATE' => 7, 'CDATE' => 8, 'HIDE' => 9, 'SORT' => 10, 'BOX_ID' => 11, 'OWNER_ID' => 12, 'ACCESS_FROM' => 13, 'ACCESS_TO' => 14, 'ACCESS_FROM_GROUPS' => 15, 'UNSEARCHABLE' => 16, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'page_id' => 1, 'version_id' => 2, 'title' => 3, 'content' => 4, 'template' => 5, 'type' => 6, 'mdate' => 7, 'cdate' => 8, 'hide' => 9, 'sort' => 10, 'box_id' => 11, 'owner_id' => 12, 'access_from' => 13, 'access_to' => 14, 'access_from_groups' => 15, 'unsearchable' => 16, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
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
        $toNames = SystemContentsPeer::getFieldNames($toType);
        $key = isset(SystemContentsPeer::$fieldKeys[$fromType][$name]) ? SystemContentsPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(SystemContentsPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, SystemContentsPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return SystemContentsPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. SystemContentsPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(SystemContentsPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(SystemContentsPeer::ID);
            $criteria->addSelectColumn(SystemContentsPeer::PAGE_ID);
            $criteria->addSelectColumn(SystemContentsPeer::VERSION_ID);
            $criteria->addSelectColumn(SystemContentsPeer::TITLE);
            $criteria->addSelectColumn(SystemContentsPeer::CONTENT);
            $criteria->addSelectColumn(SystemContentsPeer::TEMPLATE);
            $criteria->addSelectColumn(SystemContentsPeer::TYPE);
            $criteria->addSelectColumn(SystemContentsPeer::MDATE);
            $criteria->addSelectColumn(SystemContentsPeer::CDATE);
            $criteria->addSelectColumn(SystemContentsPeer::HIDE);
            $criteria->addSelectColumn(SystemContentsPeer::SORT);
            $criteria->addSelectColumn(SystemContentsPeer::BOX_ID);
            $criteria->addSelectColumn(SystemContentsPeer::OWNER_ID);
            $criteria->addSelectColumn(SystemContentsPeer::ACCESS_FROM);
            $criteria->addSelectColumn(SystemContentsPeer::ACCESS_TO);
            $criteria->addSelectColumn(SystemContentsPeer::ACCESS_FROM_GROUPS);
            $criteria->addSelectColumn(SystemContentsPeer::UNSEARCHABLE);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.PAGE_ID');
            $criteria->addSelectColumn($alias . '.VERSION_ID');
            $criteria->addSelectColumn($alias . '.TITLE');
            $criteria->addSelectColumn($alias . '.CONTENT');
            $criteria->addSelectColumn($alias . '.TEMPLATE');
            $criteria->addSelectColumn($alias . '.TYPE');
            $criteria->addSelectColumn($alias . '.MDATE');
            $criteria->addSelectColumn($alias . '.CDATE');
            $criteria->addSelectColumn($alias . '.HIDE');
            $criteria->addSelectColumn($alias . '.SORT');
            $criteria->addSelectColumn($alias . '.BOX_ID');
            $criteria->addSelectColumn($alias . '.OWNER_ID');
            $criteria->addSelectColumn($alias . '.ACCESS_FROM');
            $criteria->addSelectColumn($alias . '.ACCESS_TO');
            $criteria->addSelectColumn($alias . '.ACCESS_FROM_GROUPS');
            $criteria->addSelectColumn($alias . '.UNSEARCHABLE');
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
        $criteria->setPrimaryTableName(SystemContentsPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            SystemContentsPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(SystemContentsPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(SystemContentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 SystemContents
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = SystemContentsPeer::doSelect($critcopy, $con);
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
        return SystemContentsPeer::populateObjects(SystemContentsPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(SystemContentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            SystemContentsPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(SystemContentsPeer::DATABASE_NAME);

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
     * @param      SystemContents $obj A SystemContents object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            SystemContentsPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A SystemContents object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof SystemContents) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or SystemContents object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(SystemContentsPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return   SystemContents Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(SystemContentsPeer::$instances[$key])) {
                return SystemContentsPeer::$instances[$key];
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
        SystemContentsPeer::$instances = array();
    }
    
    /**
     * Method to invalidate the instance pool of all tables related to kryn_system_contents
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
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
        $cls = SystemContentsPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = SystemContentsPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = SystemContentsPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                SystemContentsPeer::addInstanceToPool($obj, $key);
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
     * @return array (SystemContents object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = SystemContentsPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = SystemContentsPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + SystemContentsPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = SystemContentsPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            SystemContentsPeer::addInstanceToPool($obj, $key);
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
        return Propel::getDatabaseMap(SystemContentsPeer::DATABASE_NAME)->getTable(SystemContentsPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseSystemContentsPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseSystemContentsPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new SystemContentsTableMap());
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
        return SystemContentsPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a SystemContents or Criteria object.
     *
     * @param      mixed $values Criteria or SystemContents object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemContentsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from SystemContents object
        }

        if ($criteria->containsKey(SystemContentsPeer::ID) && $criteria->keyContainsValue(SystemContentsPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.SystemContentsPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(SystemContentsPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a SystemContents or Criteria object.
     *
     * @param      mixed $values Criteria or SystemContents object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemContentsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(SystemContentsPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(SystemContentsPeer::ID);
            $value = $criteria->remove(SystemContentsPeer::ID);
            if ($value) {
                $selectCriteria->add(SystemContentsPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(SystemContentsPeer::TABLE_NAME);
            }

        } else { // $values is SystemContents object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(SystemContentsPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the kryn_system_contents table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemContentsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(SystemContentsPeer::TABLE_NAME, $con, SystemContentsPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SystemContentsPeer::clearInstancePool();
            SystemContentsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a SystemContents or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or SystemContents object or primary key or array of primary keys
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
            $con = Propel::getConnection(SystemContentsPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            SystemContentsPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof SystemContents) { // it's a model object
            // invalidate the cache for this single object
            SystemContentsPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SystemContentsPeer::DATABASE_NAME);
            $criteria->add(SystemContentsPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                SystemContentsPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(SystemContentsPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            
            $affectedRows += BasePeer::doDelete($criteria, $con);
            SystemContentsPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given SystemContents object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      SystemContents $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(SystemContentsPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(SystemContentsPeer::TABLE_NAME);

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

        return BasePeer::doValidate(SystemContentsPeer::DATABASE_NAME, SystemContentsPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return SystemContents
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = SystemContentsPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(SystemContentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(SystemContentsPeer::DATABASE_NAME);
        $criteria->add(SystemContentsPeer::ID, $pk);

        $v = SystemContentsPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return SystemContents[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemContentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(SystemContentsPeer::DATABASE_NAME);
            $criteria->add(SystemContentsPeer::ID, $pks, Criteria::IN);
            $objs = SystemContentsPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseSystemContentsPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseSystemContentsPeer::buildTableMap();


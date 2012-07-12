<?php


/**
 * Base static class for performing query and update operations on the 'kryn_system_acl' table.
 *
 * 
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemAclPeer {

    /** the default database name for this class */
    const DATABASE_NAME = 'kryn';

    /** the table name for this class */
    const TABLE_NAME = 'kryn_system_acl';

    /** the related Propel class for this table */
    const OM_CLASS = 'SystemAcl';

    /** the related TableMap class for this table */
    const TM_CLASS = 'SystemAclTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 11;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 11;

    /** the column name for the ID field */
    const ID = 'kryn_system_acl.ID';

    /** the column name for the OBJECT field */
    const OBJECT = 'kryn_system_acl.OBJECT';

    /** the column name for the TARGET_TYPE field */
    const TARGET_TYPE = 'kryn_system_acl.TARGET_TYPE';

    /** the column name for the TARGET_ID field */
    const TARGET_ID = 'kryn_system_acl.TARGET_ID';

    /** the column name for the SUB field */
    const SUB = 'kryn_system_acl.SUB';

    /** the column name for the FIELDS field */
    const FIELDS = 'kryn_system_acl.FIELDS';

    /** the column name for the ACCESS field */
    const ACCESS = 'kryn_system_acl.ACCESS';

    /** the column name for the PRIO field */
    const PRIO = 'kryn_system_acl.PRIO';

    /** the column name for the MODE field */
    const MODE = 'kryn_system_acl.MODE';

    /** the column name for the CONSTRAINT_TYPE field */
    const CONSTRAINT_TYPE = 'kryn_system_acl.CONSTRAINT_TYPE';

    /** the column name for the CONSTRAINT_CODE field */
    const CONSTRAINT_CODE = 'kryn_system_acl.CONSTRAINT_CODE';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identiy map to hold any loaded instances of SystemAcl objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array SystemAcl[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. SystemAclPeer::$fieldNames[SystemAclPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'Object', 'TargetType', 'TargetId', 'Sub', 'Fields', 'Access', 'Prio', 'Mode', 'ConstraintType', 'ConstraintCode', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'object', 'targetType', 'targetId', 'sub', 'fields', 'access', 'prio', 'mode', 'constraintType', 'constraintCode', ),
        BasePeer::TYPE_COLNAME => array (SystemAclPeer::ID, SystemAclPeer::OBJECT, SystemAclPeer::TARGET_TYPE, SystemAclPeer::TARGET_ID, SystemAclPeer::SUB, SystemAclPeer::FIELDS, SystemAclPeer::ACCESS, SystemAclPeer::PRIO, SystemAclPeer::MODE, SystemAclPeer::CONSTRAINT_TYPE, SystemAclPeer::CONSTRAINT_CODE, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'OBJECT', 'TARGET_TYPE', 'TARGET_ID', 'SUB', 'FIELDS', 'ACCESS', 'PRIO', 'MODE', 'CONSTRAINT_TYPE', 'CONSTRAINT_CODE', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'object', 'target_type', 'target_id', 'sub', 'fields', 'access', 'prio', 'mode', 'constraint_type', 'constraint_code', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. SystemAclPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Object' => 1, 'TargetType' => 2, 'TargetId' => 3, 'Sub' => 4, 'Fields' => 5, 'Access' => 6, 'Prio' => 7, 'Mode' => 8, 'ConstraintType' => 9, 'ConstraintCode' => 10, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'object' => 1, 'targetType' => 2, 'targetId' => 3, 'sub' => 4, 'fields' => 5, 'access' => 6, 'prio' => 7, 'mode' => 8, 'constraintType' => 9, 'constraintCode' => 10, ),
        BasePeer::TYPE_COLNAME => array (SystemAclPeer::ID => 0, SystemAclPeer::OBJECT => 1, SystemAclPeer::TARGET_TYPE => 2, SystemAclPeer::TARGET_ID => 3, SystemAclPeer::SUB => 4, SystemAclPeer::FIELDS => 5, SystemAclPeer::ACCESS => 6, SystemAclPeer::PRIO => 7, SystemAclPeer::MODE => 8, SystemAclPeer::CONSTRAINT_TYPE => 9, SystemAclPeer::CONSTRAINT_CODE => 10, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'OBJECT' => 1, 'TARGET_TYPE' => 2, 'TARGET_ID' => 3, 'SUB' => 4, 'FIELDS' => 5, 'ACCESS' => 6, 'PRIO' => 7, 'MODE' => 8, 'CONSTRAINT_TYPE' => 9, 'CONSTRAINT_CODE' => 10, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'object' => 1, 'target_type' => 2, 'target_id' => 3, 'sub' => 4, 'fields' => 5, 'access' => 6, 'prio' => 7, 'mode' => 8, 'constraint_type' => 9, 'constraint_code' => 10, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, )
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
        $toNames = SystemAclPeer::getFieldNames($toType);
        $key = isset(SystemAclPeer::$fieldKeys[$fromType][$name]) ? SystemAclPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(SystemAclPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, SystemAclPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return SystemAclPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. SystemAclPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(SystemAclPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(SystemAclPeer::ID);
            $criteria->addSelectColumn(SystemAclPeer::OBJECT);
            $criteria->addSelectColumn(SystemAclPeer::TARGET_TYPE);
            $criteria->addSelectColumn(SystemAclPeer::TARGET_ID);
            $criteria->addSelectColumn(SystemAclPeer::SUB);
            $criteria->addSelectColumn(SystemAclPeer::FIELDS);
            $criteria->addSelectColumn(SystemAclPeer::ACCESS);
            $criteria->addSelectColumn(SystemAclPeer::PRIO);
            $criteria->addSelectColumn(SystemAclPeer::MODE);
            $criteria->addSelectColumn(SystemAclPeer::CONSTRAINT_TYPE);
            $criteria->addSelectColumn(SystemAclPeer::CONSTRAINT_CODE);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.OBJECT');
            $criteria->addSelectColumn($alias . '.TARGET_TYPE');
            $criteria->addSelectColumn($alias . '.TARGET_ID');
            $criteria->addSelectColumn($alias . '.SUB');
            $criteria->addSelectColumn($alias . '.FIELDS');
            $criteria->addSelectColumn($alias . '.ACCESS');
            $criteria->addSelectColumn($alias . '.PRIO');
            $criteria->addSelectColumn($alias . '.MODE');
            $criteria->addSelectColumn($alias . '.CONSTRAINT_TYPE');
            $criteria->addSelectColumn($alias . '.CONSTRAINT_CODE');
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
        $criteria->setPrimaryTableName(SystemAclPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            SystemAclPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(SystemAclPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(SystemAclPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 SystemAcl
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = SystemAclPeer::doSelect($critcopy, $con);
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
        return SystemAclPeer::populateObjects(SystemAclPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(SystemAclPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            SystemAclPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(SystemAclPeer::DATABASE_NAME);

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
     * @param      SystemAcl $obj A SystemAcl object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            SystemAclPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A SystemAcl object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof SystemAcl) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or SystemAcl object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(SystemAclPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return   SystemAcl Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(SystemAclPeer::$instances[$key])) {
                return SystemAclPeer::$instances[$key];
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
        SystemAclPeer::$instances = array();
    }
    
    /**
     * Method to invalidate the instance pool of all tables related to kryn_system_acl
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
        $cls = SystemAclPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = SystemAclPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = SystemAclPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                SystemAclPeer::addInstanceToPool($obj, $key);
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
     * @return array (SystemAcl object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = SystemAclPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = SystemAclPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + SystemAclPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = SystemAclPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            SystemAclPeer::addInstanceToPool($obj, $key);
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
        return Propel::getDatabaseMap(SystemAclPeer::DATABASE_NAME)->getTable(SystemAclPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseSystemAclPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseSystemAclPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new SystemAclTableMap());
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
        return SystemAclPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a SystemAcl or Criteria object.
     *
     * @param      mixed $values Criteria or SystemAcl object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemAclPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from SystemAcl object
        }

        if ($criteria->containsKey(SystemAclPeer::ID) && $criteria->keyContainsValue(SystemAclPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.SystemAclPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(SystemAclPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a SystemAcl or Criteria object.
     *
     * @param      mixed $values Criteria or SystemAcl object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemAclPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(SystemAclPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(SystemAclPeer::ID);
            $value = $criteria->remove(SystemAclPeer::ID);
            if ($value) {
                $selectCriteria->add(SystemAclPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(SystemAclPeer::TABLE_NAME);
            }

        } else { // $values is SystemAcl object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(SystemAclPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the kryn_system_acl table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemAclPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(SystemAclPeer::TABLE_NAME, $con, SystemAclPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SystemAclPeer::clearInstancePool();
            SystemAclPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a SystemAcl or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or SystemAcl object or primary key or array of primary keys
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
            $con = Propel::getConnection(SystemAclPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            SystemAclPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof SystemAcl) { // it's a model object
            // invalidate the cache for this single object
            SystemAclPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SystemAclPeer::DATABASE_NAME);
            $criteria->add(SystemAclPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                SystemAclPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(SystemAclPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            
            $affectedRows += BasePeer::doDelete($criteria, $con);
            SystemAclPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given SystemAcl object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      SystemAcl $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(SystemAclPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(SystemAclPeer::TABLE_NAME);

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

        return BasePeer::doValidate(SystemAclPeer::DATABASE_NAME, SystemAclPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return SystemAcl
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = SystemAclPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(SystemAclPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(SystemAclPeer::DATABASE_NAME);
        $criteria->add(SystemAclPeer::ID, $pk);

        $v = SystemAclPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return SystemAcl[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemAclPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(SystemAclPeer::DATABASE_NAME);
            $criteria->add(SystemAclPeer::ID, $pks, Criteria::IN);
            $objs = SystemAclPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseSystemAclPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseSystemAclPeer::buildTableMap();


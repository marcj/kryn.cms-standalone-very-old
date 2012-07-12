<?php


/**
 * Base static class for performing query and update operations on the 'kryn_system_pages' table.
 *
 * 
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemPagesPeer {

    /** the default database name for this class */
    const DATABASE_NAME = 'kryn';

    /** the table name for this class */
    const TABLE_NAME = 'kryn_system_pages';

    /** the related Propel class for this table */
    const OM_CLASS = 'SystemPages';

    /** the related TableMap class for this table */
    const TM_CLASS = 'SystemPagesTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 31;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 31;

    /** the column name for the ID field */
    const ID = 'kryn_system_pages.ID';

    /** the column name for the PID field */
    const PID = 'kryn_system_pages.PID';

    /** the column name for the DOMAIN_ID field */
    const DOMAIN_ID = 'kryn_system_pages.DOMAIN_ID';

    /** the column name for the TYPE field */
    const TYPE = 'kryn_system_pages.TYPE';

    /** the column name for the TITLE field */
    const TITLE = 'kryn_system_pages.TITLE';

    /** the column name for the PAGE_TITLE field */
    const PAGE_TITLE = 'kryn_system_pages.PAGE_TITLE';

    /** the column name for the URL field */
    const URL = 'kryn_system_pages.URL';

    /** the column name for the LINK field */
    const LINK = 'kryn_system_pages.LINK';

    /** the column name for the LAYOUT field */
    const LAYOUT = 'kryn_system_pages.LAYOUT';

    /** the column name for the SORT field */
    const SORT = 'kryn_system_pages.SORT';

    /** the column name for the SORT_MODE field */
    const SORT_MODE = 'kryn_system_pages.SORT_MODE';

    /** the column name for the TARGET field */
    const TARGET = 'kryn_system_pages.TARGET';

    /** the column name for the VISIBLE field */
    const VISIBLE = 'kryn_system_pages.VISIBLE';

    /** the column name for the ACCESS_DENIED field */
    const ACCESS_DENIED = 'kryn_system_pages.ACCESS_DENIED';

    /** the column name for the META field */
    const META = 'kryn_system_pages.META';

    /** the column name for the PROPERTIES field */
    const PROPERTIES = 'kryn_system_pages.PROPERTIES';

    /** the column name for the CDATE field */
    const CDATE = 'kryn_system_pages.CDATE';

    /** the column name for the MDATE field */
    const MDATE = 'kryn_system_pages.MDATE';

    /** the column name for the DRAFT_EXIST field */
    const DRAFT_EXIST = 'kryn_system_pages.DRAFT_EXIST';

    /** the column name for the FORCE_HTTPS field */
    const FORCE_HTTPS = 'kryn_system_pages.FORCE_HTTPS';

    /** the column name for the ACCESS_FROM field */
    const ACCESS_FROM = 'kryn_system_pages.ACCESS_FROM';

    /** the column name for the ACCESS_TO field */
    const ACCESS_TO = 'kryn_system_pages.ACCESS_TO';

    /** the column name for the ACCESS_REDIRECTTO field */
    const ACCESS_REDIRECTTO = 'kryn_system_pages.ACCESS_REDIRECTTO';

    /** the column name for the ACCESS_NOHIDENAVI field */
    const ACCESS_NOHIDENAVI = 'kryn_system_pages.ACCESS_NOHIDENAVI';

    /** the column name for the ACCESS_NEED_VIA field */
    const ACCESS_NEED_VIA = 'kryn_system_pages.ACCESS_NEED_VIA';

    /** the column name for the ACCESS_FROM_GROUPS field */
    const ACCESS_FROM_GROUPS = 'kryn_system_pages.ACCESS_FROM_GROUPS';

    /** the column name for the CACHE field */
    const CACHE = 'kryn_system_pages.CACHE';

    /** the column name for the SEARCH_WORDS field */
    const SEARCH_WORDS = 'kryn_system_pages.SEARCH_WORDS';

    /** the column name for the UNSEARCHABLE field */
    const UNSEARCHABLE = 'kryn_system_pages.UNSEARCHABLE';

    /** the column name for the LFT field */
    const LFT = 'kryn_system_pages.LFT';

    /** the column name for the RGT field */
    const RGT = 'kryn_system_pages.RGT';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identiy map to hold any loaded instances of SystemPages objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array SystemPages[]
     */
    public static $instances = array();


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. SystemPagesPeer::$fieldNames[SystemPagesPeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'Pid', 'DomainId', 'Type', 'Title', 'PageTitle', 'Url', 'Link', 'Layout', 'Sort', 'SortMode', 'Target', 'Visible', 'AccessDenied', 'Meta', 'Properties', 'Cdate', 'Mdate', 'DraftExist', 'ForceHttps', 'AccessFrom', 'AccessTo', 'AccessRedirectto', 'AccessNohidenavi', 'AccessNeedVia', 'AccessFromGroups', 'Cache', 'SearchWords', 'Unsearchable', 'Lft', 'Rgt', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'pid', 'domainId', 'type', 'title', 'pageTitle', 'url', 'link', 'layout', 'sort', 'sortMode', 'target', 'visible', 'accessDenied', 'meta', 'properties', 'cdate', 'mdate', 'draftExist', 'forceHttps', 'accessFrom', 'accessTo', 'accessRedirectto', 'accessNohidenavi', 'accessNeedVia', 'accessFromGroups', 'cache', 'searchWords', 'unsearchable', 'lft', 'rgt', ),
        BasePeer::TYPE_COLNAME => array (SystemPagesPeer::ID, SystemPagesPeer::PID, SystemPagesPeer::DOMAIN_ID, SystemPagesPeer::TYPE, SystemPagesPeer::TITLE, SystemPagesPeer::PAGE_TITLE, SystemPagesPeer::URL, SystemPagesPeer::LINK, SystemPagesPeer::LAYOUT, SystemPagesPeer::SORT, SystemPagesPeer::SORT_MODE, SystemPagesPeer::TARGET, SystemPagesPeer::VISIBLE, SystemPagesPeer::ACCESS_DENIED, SystemPagesPeer::META, SystemPagesPeer::PROPERTIES, SystemPagesPeer::CDATE, SystemPagesPeer::MDATE, SystemPagesPeer::DRAFT_EXIST, SystemPagesPeer::FORCE_HTTPS, SystemPagesPeer::ACCESS_FROM, SystemPagesPeer::ACCESS_TO, SystemPagesPeer::ACCESS_REDIRECTTO, SystemPagesPeer::ACCESS_NOHIDENAVI, SystemPagesPeer::ACCESS_NEED_VIA, SystemPagesPeer::ACCESS_FROM_GROUPS, SystemPagesPeer::CACHE, SystemPagesPeer::SEARCH_WORDS, SystemPagesPeer::UNSEARCHABLE, SystemPagesPeer::LFT, SystemPagesPeer::RGT, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'PID', 'DOMAIN_ID', 'TYPE', 'TITLE', 'PAGE_TITLE', 'URL', 'LINK', 'LAYOUT', 'SORT', 'SORT_MODE', 'TARGET', 'VISIBLE', 'ACCESS_DENIED', 'META', 'PROPERTIES', 'CDATE', 'MDATE', 'DRAFT_EXIST', 'FORCE_HTTPS', 'ACCESS_FROM', 'ACCESS_TO', 'ACCESS_REDIRECTTO', 'ACCESS_NOHIDENAVI', 'ACCESS_NEED_VIA', 'ACCESS_FROM_GROUPS', 'CACHE', 'SEARCH_WORDS', 'UNSEARCHABLE', 'LFT', 'RGT', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'pid', 'domain_id', 'type', 'title', 'page_title', 'url', 'link', 'layout', 'sort', 'sort_mode', 'target', 'visible', 'access_denied', 'meta', 'properties', 'cdate', 'mdate', 'draft_exist', 'force_https', 'access_from', 'access_to', 'access_redirectto', 'access_nohidenavi', 'access_need_via', 'access_from_groups', 'cache', 'search_words', 'unsearchable', 'lft', 'rgt', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. SystemPagesPeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Pid' => 1, 'DomainId' => 2, 'Type' => 3, 'Title' => 4, 'PageTitle' => 5, 'Url' => 6, 'Link' => 7, 'Layout' => 8, 'Sort' => 9, 'SortMode' => 10, 'Target' => 11, 'Visible' => 12, 'AccessDenied' => 13, 'Meta' => 14, 'Properties' => 15, 'Cdate' => 16, 'Mdate' => 17, 'DraftExist' => 18, 'ForceHttps' => 19, 'AccessFrom' => 20, 'AccessTo' => 21, 'AccessRedirectto' => 22, 'AccessNohidenavi' => 23, 'AccessNeedVia' => 24, 'AccessFromGroups' => 25, 'Cache' => 26, 'SearchWords' => 27, 'Unsearchable' => 28, 'Lft' => 29, 'Rgt' => 30, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'pid' => 1, 'domainId' => 2, 'type' => 3, 'title' => 4, 'pageTitle' => 5, 'url' => 6, 'link' => 7, 'layout' => 8, 'sort' => 9, 'sortMode' => 10, 'target' => 11, 'visible' => 12, 'accessDenied' => 13, 'meta' => 14, 'properties' => 15, 'cdate' => 16, 'mdate' => 17, 'draftExist' => 18, 'forceHttps' => 19, 'accessFrom' => 20, 'accessTo' => 21, 'accessRedirectto' => 22, 'accessNohidenavi' => 23, 'accessNeedVia' => 24, 'accessFromGroups' => 25, 'cache' => 26, 'searchWords' => 27, 'unsearchable' => 28, 'lft' => 29, 'rgt' => 30, ),
        BasePeer::TYPE_COLNAME => array (SystemPagesPeer::ID => 0, SystemPagesPeer::PID => 1, SystemPagesPeer::DOMAIN_ID => 2, SystemPagesPeer::TYPE => 3, SystemPagesPeer::TITLE => 4, SystemPagesPeer::PAGE_TITLE => 5, SystemPagesPeer::URL => 6, SystemPagesPeer::LINK => 7, SystemPagesPeer::LAYOUT => 8, SystemPagesPeer::SORT => 9, SystemPagesPeer::SORT_MODE => 10, SystemPagesPeer::TARGET => 11, SystemPagesPeer::VISIBLE => 12, SystemPagesPeer::ACCESS_DENIED => 13, SystemPagesPeer::META => 14, SystemPagesPeer::PROPERTIES => 15, SystemPagesPeer::CDATE => 16, SystemPagesPeer::MDATE => 17, SystemPagesPeer::DRAFT_EXIST => 18, SystemPagesPeer::FORCE_HTTPS => 19, SystemPagesPeer::ACCESS_FROM => 20, SystemPagesPeer::ACCESS_TO => 21, SystemPagesPeer::ACCESS_REDIRECTTO => 22, SystemPagesPeer::ACCESS_NOHIDENAVI => 23, SystemPagesPeer::ACCESS_NEED_VIA => 24, SystemPagesPeer::ACCESS_FROM_GROUPS => 25, SystemPagesPeer::CACHE => 26, SystemPagesPeer::SEARCH_WORDS => 27, SystemPagesPeer::UNSEARCHABLE => 28, SystemPagesPeer::LFT => 29, SystemPagesPeer::RGT => 30, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'PID' => 1, 'DOMAIN_ID' => 2, 'TYPE' => 3, 'TITLE' => 4, 'PAGE_TITLE' => 5, 'URL' => 6, 'LINK' => 7, 'LAYOUT' => 8, 'SORT' => 9, 'SORT_MODE' => 10, 'TARGET' => 11, 'VISIBLE' => 12, 'ACCESS_DENIED' => 13, 'META' => 14, 'PROPERTIES' => 15, 'CDATE' => 16, 'MDATE' => 17, 'DRAFT_EXIST' => 18, 'FORCE_HTTPS' => 19, 'ACCESS_FROM' => 20, 'ACCESS_TO' => 21, 'ACCESS_REDIRECTTO' => 22, 'ACCESS_NOHIDENAVI' => 23, 'ACCESS_NEED_VIA' => 24, 'ACCESS_FROM_GROUPS' => 25, 'CACHE' => 26, 'SEARCH_WORDS' => 27, 'UNSEARCHABLE' => 28, 'LFT' => 29, 'RGT' => 30, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'pid' => 1, 'domain_id' => 2, 'type' => 3, 'title' => 4, 'page_title' => 5, 'url' => 6, 'link' => 7, 'layout' => 8, 'sort' => 9, 'sort_mode' => 10, 'target' => 11, 'visible' => 12, 'access_denied' => 13, 'meta' => 14, 'properties' => 15, 'cdate' => 16, 'mdate' => 17, 'draft_exist' => 18, 'force_https' => 19, 'access_from' => 20, 'access_to' => 21, 'access_redirectto' => 22, 'access_nohidenavi' => 23, 'access_need_via' => 24, 'access_from_groups' => 25, 'cache' => 26, 'search_words' => 27, 'unsearchable' => 28, 'lft' => 29, 'rgt' => 30, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, )
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
        $toNames = SystemPagesPeer::getFieldNames($toType);
        $key = isset(SystemPagesPeer::$fieldKeys[$fromType][$name]) ? SystemPagesPeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(SystemPagesPeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, SystemPagesPeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return SystemPagesPeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. SystemPagesPeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(SystemPagesPeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(SystemPagesPeer::ID);
            $criteria->addSelectColumn(SystemPagesPeer::PID);
            $criteria->addSelectColumn(SystemPagesPeer::DOMAIN_ID);
            $criteria->addSelectColumn(SystemPagesPeer::TYPE);
            $criteria->addSelectColumn(SystemPagesPeer::TITLE);
            $criteria->addSelectColumn(SystemPagesPeer::PAGE_TITLE);
            $criteria->addSelectColumn(SystemPagesPeer::URL);
            $criteria->addSelectColumn(SystemPagesPeer::LINK);
            $criteria->addSelectColumn(SystemPagesPeer::LAYOUT);
            $criteria->addSelectColumn(SystemPagesPeer::SORT);
            $criteria->addSelectColumn(SystemPagesPeer::SORT_MODE);
            $criteria->addSelectColumn(SystemPagesPeer::TARGET);
            $criteria->addSelectColumn(SystemPagesPeer::VISIBLE);
            $criteria->addSelectColumn(SystemPagesPeer::ACCESS_DENIED);
            $criteria->addSelectColumn(SystemPagesPeer::META);
            $criteria->addSelectColumn(SystemPagesPeer::PROPERTIES);
            $criteria->addSelectColumn(SystemPagesPeer::CDATE);
            $criteria->addSelectColumn(SystemPagesPeer::MDATE);
            $criteria->addSelectColumn(SystemPagesPeer::DRAFT_EXIST);
            $criteria->addSelectColumn(SystemPagesPeer::FORCE_HTTPS);
            $criteria->addSelectColumn(SystemPagesPeer::ACCESS_FROM);
            $criteria->addSelectColumn(SystemPagesPeer::ACCESS_TO);
            $criteria->addSelectColumn(SystemPagesPeer::ACCESS_REDIRECTTO);
            $criteria->addSelectColumn(SystemPagesPeer::ACCESS_NOHIDENAVI);
            $criteria->addSelectColumn(SystemPagesPeer::ACCESS_NEED_VIA);
            $criteria->addSelectColumn(SystemPagesPeer::ACCESS_FROM_GROUPS);
            $criteria->addSelectColumn(SystemPagesPeer::CACHE);
            $criteria->addSelectColumn(SystemPagesPeer::SEARCH_WORDS);
            $criteria->addSelectColumn(SystemPagesPeer::UNSEARCHABLE);
            $criteria->addSelectColumn(SystemPagesPeer::LFT);
            $criteria->addSelectColumn(SystemPagesPeer::RGT);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.PID');
            $criteria->addSelectColumn($alias . '.DOMAIN_ID');
            $criteria->addSelectColumn($alias . '.TYPE');
            $criteria->addSelectColumn($alias . '.TITLE');
            $criteria->addSelectColumn($alias . '.PAGE_TITLE');
            $criteria->addSelectColumn($alias . '.URL');
            $criteria->addSelectColumn($alias . '.LINK');
            $criteria->addSelectColumn($alias . '.LAYOUT');
            $criteria->addSelectColumn($alias . '.SORT');
            $criteria->addSelectColumn($alias . '.SORT_MODE');
            $criteria->addSelectColumn($alias . '.TARGET');
            $criteria->addSelectColumn($alias . '.VISIBLE');
            $criteria->addSelectColumn($alias . '.ACCESS_DENIED');
            $criteria->addSelectColumn($alias . '.META');
            $criteria->addSelectColumn($alias . '.PROPERTIES');
            $criteria->addSelectColumn($alias . '.CDATE');
            $criteria->addSelectColumn($alias . '.MDATE');
            $criteria->addSelectColumn($alias . '.DRAFT_EXIST');
            $criteria->addSelectColumn($alias . '.FORCE_HTTPS');
            $criteria->addSelectColumn($alias . '.ACCESS_FROM');
            $criteria->addSelectColumn($alias . '.ACCESS_TO');
            $criteria->addSelectColumn($alias . '.ACCESS_REDIRECTTO');
            $criteria->addSelectColumn($alias . '.ACCESS_NOHIDENAVI');
            $criteria->addSelectColumn($alias . '.ACCESS_NEED_VIA');
            $criteria->addSelectColumn($alias . '.ACCESS_FROM_GROUPS');
            $criteria->addSelectColumn($alias . '.CACHE');
            $criteria->addSelectColumn($alias . '.SEARCH_WORDS');
            $criteria->addSelectColumn($alias . '.UNSEARCHABLE');
            $criteria->addSelectColumn($alias . '.LFT');
            $criteria->addSelectColumn($alias . '.RGT');
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
        $criteria->setPrimaryTableName(SystemPagesPeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            SystemPagesPeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(SystemPagesPeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 SystemPages
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = SystemPagesPeer::doSelect($critcopy, $con);
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
        return SystemPagesPeer::populateObjects(SystemPagesPeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            SystemPagesPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(SystemPagesPeer::DATABASE_NAME);

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
     * @param      SystemPages $obj A SystemPages object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            SystemPagesPeer::$instances[$key] = $obj;
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
     * @param      mixed $value A SystemPages object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof SystemPages) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or SystemPages object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(SystemPagesPeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return   SystemPages Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(SystemPagesPeer::$instances[$key])) {
                return SystemPagesPeer::$instances[$key];
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
        SystemPagesPeer::$instances = array();
    }
    
    /**
     * Method to invalidate the instance pool of all tables related to kryn_system_pages
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
        $cls = SystemPagesPeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = SystemPagesPeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = SystemPagesPeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                SystemPagesPeer::addInstanceToPool($obj, $key);
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
     * @return array (SystemPages object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = SystemPagesPeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = SystemPagesPeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + SystemPagesPeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = SystemPagesPeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            SystemPagesPeer::addInstanceToPool($obj, $key);
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
        return Propel::getDatabaseMap(SystemPagesPeer::DATABASE_NAME)->getTable(SystemPagesPeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BaseSystemPagesPeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BaseSystemPagesPeer::TABLE_NAME)) {
        $dbMap->addTableObject(new SystemPagesTableMap());
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
        return SystemPagesPeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a SystemPages or Criteria object.
     *
     * @param      mixed $values Criteria or SystemPages object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from SystemPages object
        }

        if ($criteria->containsKey(SystemPagesPeer::ID) && $criteria->keyContainsValue(SystemPagesPeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.SystemPagesPeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(SystemPagesPeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a SystemPages or Criteria object.
     *
     * @param      mixed $values Criteria or SystemPages object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(SystemPagesPeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(SystemPagesPeer::ID);
            $value = $criteria->remove(SystemPagesPeer::ID);
            if ($value) {
                $selectCriteria->add(SystemPagesPeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(SystemPagesPeer::TABLE_NAME);
            }

        } else { // $values is SystemPages object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(SystemPagesPeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the kryn_system_pages table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(SystemPagesPeer::TABLE_NAME, $con, SystemPagesPeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SystemPagesPeer::clearInstancePool();
            SystemPagesPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a SystemPages or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or SystemPages object or primary key or array of primary keys
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
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            SystemPagesPeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof SystemPages) { // it's a model object
            // invalidate the cache for this single object
            SystemPagesPeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SystemPagesPeer::DATABASE_NAME);
            $criteria->add(SystemPagesPeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                SystemPagesPeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(SystemPagesPeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            
            $affectedRows += BasePeer::doDelete($criteria, $con);
            SystemPagesPeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given SystemPages object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      SystemPages $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(SystemPagesPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(SystemPagesPeer::TABLE_NAME);

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

        return BasePeer::doValidate(SystemPagesPeer::DATABASE_NAME, SystemPagesPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return SystemPages
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = SystemPagesPeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(SystemPagesPeer::DATABASE_NAME);
        $criteria->add(SystemPagesPeer::ID, $pk);

        $v = SystemPagesPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return SystemPages[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(SystemPagesPeer::DATABASE_NAME);
            $criteria->add(SystemPagesPeer::ID, $pks, Criteria::IN);
            $objs = SystemPagesPeer::doSelect($criteria, $con);
        }

        return $objs;
    }

} // BaseSystemPagesPeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BaseSystemPagesPeer::buildTableMap();


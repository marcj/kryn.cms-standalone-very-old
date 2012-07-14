<?php


/**
 * Base static class for performing query and update operations on the 'kryn_system_page' table.
 *
 * 
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BasePagePeer {

    /** the default database name for this class */
    const DATABASE_NAME = 'Kryn';

    /** the table name for this class */
    const TABLE_NAME = 'kryn_system_page';

    /** the related Propel class for this table */
    const OM_CLASS = 'Page';

    /** the related TableMap class for this table */
    const TM_CLASS = 'PageTableMap';

    /** The total number of columns. */
    const NUM_COLUMNS = 33;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /** The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS) */
    const NUM_HYDRATE_COLUMNS = 33;

    /** the column name for the ID field */
    const ID = 'kryn_system_page.ID';

    /** the column name for the PID field */
    const PID = 'kryn_system_page.PID';

    /** the column name for the DOMAIN_ID field */
    const DOMAIN_ID = 'kryn_system_page.DOMAIN_ID';

    /** the column name for the LFT field */
    const LFT = 'kryn_system_page.LFT';

    /** the column name for the RGT field */
    const RGT = 'kryn_system_page.RGT';

    /** the column name for the LVL field */
    const LVL = 'kryn_system_page.LVL';

    /** the column name for the TYPE field */
    const TYPE = 'kryn_system_page.TYPE';

    /** the column name for the TITLE field */
    const TITLE = 'kryn_system_page.TITLE';

    /** the column name for the PAGE_TITLE field */
    const PAGE_TITLE = 'kryn_system_page.PAGE_TITLE';

    /** the column name for the URL field */
    const URL = 'kryn_system_page.URL';

    /** the column name for the LINK field */
    const LINK = 'kryn_system_page.LINK';

    /** the column name for the LAYOUT field */
    const LAYOUT = 'kryn_system_page.LAYOUT';

    /** the column name for the SORT field */
    const SORT = 'kryn_system_page.SORT';

    /** the column name for the SORT_MODE field */
    const SORT_MODE = 'kryn_system_page.SORT_MODE';

    /** the column name for the TARGET field */
    const TARGET = 'kryn_system_page.TARGET';

    /** the column name for the VISIBLE field */
    const VISIBLE = 'kryn_system_page.VISIBLE';

    /** the column name for the ACCESS_DENIED field */
    const ACCESS_DENIED = 'kryn_system_page.ACCESS_DENIED';

    /** the column name for the META field */
    const META = 'kryn_system_page.META';

    /** the column name for the PROPERTIES field */
    const PROPERTIES = 'kryn_system_page.PROPERTIES';

    /** the column name for the CDATE field */
    const CDATE = 'kryn_system_page.CDATE';

    /** the column name for the MDATE field */
    const MDATE = 'kryn_system_page.MDATE';

    /** the column name for the DRAFT_EXIST field */
    const DRAFT_EXIST = 'kryn_system_page.DRAFT_EXIST';

    /** the column name for the FORCE_HTTPS field */
    const FORCE_HTTPS = 'kryn_system_page.FORCE_HTTPS';

    /** the column name for the ACCESS_FROM field */
    const ACCESS_FROM = 'kryn_system_page.ACCESS_FROM';

    /** the column name for the ACCESS_TO field */
    const ACCESS_TO = 'kryn_system_page.ACCESS_TO';

    /** the column name for the ACCESS_REDIRECTTO field */
    const ACCESS_REDIRECTTO = 'kryn_system_page.ACCESS_REDIRECTTO';

    /** the column name for the ACCESS_NOHIDENAVI field */
    const ACCESS_NOHIDENAVI = 'kryn_system_page.ACCESS_NOHIDENAVI';

    /** the column name for the ACCESS_NEED_VIA field */
    const ACCESS_NEED_VIA = 'kryn_system_page.ACCESS_NEED_VIA';

    /** the column name for the ACCESS_FROM_GROUPS field */
    const ACCESS_FROM_GROUPS = 'kryn_system_page.ACCESS_FROM_GROUPS';

    /** the column name for the CACHE field */
    const CACHE = 'kryn_system_page.CACHE';

    /** the column name for the SEARCH_WORDS field */
    const SEARCH_WORDS = 'kryn_system_page.SEARCH_WORDS';

    /** the column name for the UNSEARCHABLE field */
    const UNSEARCHABLE = 'kryn_system_page.UNSEARCHABLE';

    /** the column name for the ACTIVE_VERSION_ID field */
    const ACTIVE_VERSION_ID = 'kryn_system_page.ACTIVE_VERSION_ID';

    /** The default string format for model objects of the related table **/
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * An identiy map to hold any loaded instances of Page objects.
     * This must be public so that other peer classes can access this when hydrating from JOIN
     * queries.
     * @var        array Page[]
     */
    public static $instances = array();


	// nested_set behavior
	
	/**
	 * Left column for the set
	 */
	const LEFT_COL = 'kryn_system_page.LFT';
	
	/**
	 * Right column for the set
	 */
	const RIGHT_COL = 'kryn_system_page.RGT';
	
	/**
	 * Level column for the set
	 */
	const LEVEL_COL = 'kryn_system_page.LVL';
	
	/**
	 * Scope column for the set
	 */
	const SCOPE_COL = 'kryn_system_page.DOMAIN_ID';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. PagePeer::$fieldNames[PagePeer::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('Id', 'Pid', 'DomainId', 'Lft', 'Rgt', 'Lvl', 'Type', 'Title', 'PageTitle', 'Url', 'Link', 'Layout', 'Sort', 'SortMode', 'Target', 'Visible', 'AccessDenied', 'Meta', 'Properties', 'Cdate', 'Mdate', 'DraftExist', 'ForceHttps', 'AccessFrom', 'AccessTo', 'AccessRedirectto', 'AccessNohidenavi', 'AccessNeedVia', 'AccessFromGroups', 'Cache', 'SearchWords', 'Unsearchable', 'ActiveVersionId', ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id', 'pid', 'domainId', 'lft', 'rgt', 'lvl', 'type', 'title', 'pageTitle', 'url', 'link', 'layout', 'sort', 'sortMode', 'target', 'visible', 'accessDenied', 'meta', 'properties', 'cdate', 'mdate', 'draftExist', 'forceHttps', 'accessFrom', 'accessTo', 'accessRedirectto', 'accessNohidenavi', 'accessNeedVia', 'accessFromGroups', 'cache', 'searchWords', 'unsearchable', 'activeVersionId', ),
        BasePeer::TYPE_COLNAME => array (PagePeer::ID, PagePeer::PID, PagePeer::DOMAIN_ID, PagePeer::LFT, PagePeer::RGT, PagePeer::LVL, PagePeer::TYPE, PagePeer::TITLE, PagePeer::PAGE_TITLE, PagePeer::URL, PagePeer::LINK, PagePeer::LAYOUT, PagePeer::SORT, PagePeer::SORT_MODE, PagePeer::TARGET, PagePeer::VISIBLE, PagePeer::ACCESS_DENIED, PagePeer::META, PagePeer::PROPERTIES, PagePeer::CDATE, PagePeer::MDATE, PagePeer::DRAFT_EXIST, PagePeer::FORCE_HTTPS, PagePeer::ACCESS_FROM, PagePeer::ACCESS_TO, PagePeer::ACCESS_REDIRECTTO, PagePeer::ACCESS_NOHIDENAVI, PagePeer::ACCESS_NEED_VIA, PagePeer::ACCESS_FROM_GROUPS, PagePeer::CACHE, PagePeer::SEARCH_WORDS, PagePeer::UNSEARCHABLE, PagePeer::ACTIVE_VERSION_ID, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID', 'PID', 'DOMAIN_ID', 'LFT', 'RGT', 'LVL', 'TYPE', 'TITLE', 'PAGE_TITLE', 'URL', 'LINK', 'LAYOUT', 'SORT', 'SORT_MODE', 'TARGET', 'VISIBLE', 'ACCESS_DENIED', 'META', 'PROPERTIES', 'CDATE', 'MDATE', 'DRAFT_EXIST', 'FORCE_HTTPS', 'ACCESS_FROM', 'ACCESS_TO', 'ACCESS_REDIRECTTO', 'ACCESS_NOHIDENAVI', 'ACCESS_NEED_VIA', 'ACCESS_FROM_GROUPS', 'CACHE', 'SEARCH_WORDS', 'UNSEARCHABLE', 'ACTIVE_VERSION_ID', ),
        BasePeer::TYPE_FIELDNAME => array ('id', 'pid', 'domain_id', 'lft', 'rgt', 'lvl', 'type', 'title', 'page_title', 'url', 'link', 'layout', 'sort', 'sort_mode', 'target', 'visible', 'access_denied', 'meta', 'properties', 'cdate', 'mdate', 'draft_exist', 'force_https', 'access_from', 'access_to', 'access_redirectto', 'access_nohidenavi', 'access_need_via', 'access_from_groups', 'cache', 'search_words', 'unsearchable', 'active_version_id', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. PagePeer::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('Id' => 0, 'Pid' => 1, 'DomainId' => 2, 'Lft' => 3, 'Rgt' => 4, 'Lvl' => 5, 'Type' => 6, 'Title' => 7, 'PageTitle' => 8, 'Url' => 9, 'Link' => 10, 'Layout' => 11, 'Sort' => 12, 'SortMode' => 13, 'Target' => 14, 'Visible' => 15, 'AccessDenied' => 16, 'Meta' => 17, 'Properties' => 18, 'Cdate' => 19, 'Mdate' => 20, 'DraftExist' => 21, 'ForceHttps' => 22, 'AccessFrom' => 23, 'AccessTo' => 24, 'AccessRedirectto' => 25, 'AccessNohidenavi' => 26, 'AccessNeedVia' => 27, 'AccessFromGroups' => 28, 'Cache' => 29, 'SearchWords' => 30, 'Unsearchable' => 31, 'ActiveVersionId' => 32, ),
        BasePeer::TYPE_STUDLYPHPNAME => array ('id' => 0, 'pid' => 1, 'domainId' => 2, 'lft' => 3, 'rgt' => 4, 'lvl' => 5, 'type' => 6, 'title' => 7, 'pageTitle' => 8, 'url' => 9, 'link' => 10, 'layout' => 11, 'sort' => 12, 'sortMode' => 13, 'target' => 14, 'visible' => 15, 'accessDenied' => 16, 'meta' => 17, 'properties' => 18, 'cdate' => 19, 'mdate' => 20, 'draftExist' => 21, 'forceHttps' => 22, 'accessFrom' => 23, 'accessTo' => 24, 'accessRedirectto' => 25, 'accessNohidenavi' => 26, 'accessNeedVia' => 27, 'accessFromGroups' => 28, 'cache' => 29, 'searchWords' => 30, 'unsearchable' => 31, 'activeVersionId' => 32, ),
        BasePeer::TYPE_COLNAME => array (PagePeer::ID => 0, PagePeer::PID => 1, PagePeer::DOMAIN_ID => 2, PagePeer::LFT => 3, PagePeer::RGT => 4, PagePeer::LVL => 5, PagePeer::TYPE => 6, PagePeer::TITLE => 7, PagePeer::PAGE_TITLE => 8, PagePeer::URL => 9, PagePeer::LINK => 10, PagePeer::LAYOUT => 11, PagePeer::SORT => 12, PagePeer::SORT_MODE => 13, PagePeer::TARGET => 14, PagePeer::VISIBLE => 15, PagePeer::ACCESS_DENIED => 16, PagePeer::META => 17, PagePeer::PROPERTIES => 18, PagePeer::CDATE => 19, PagePeer::MDATE => 20, PagePeer::DRAFT_EXIST => 21, PagePeer::FORCE_HTTPS => 22, PagePeer::ACCESS_FROM => 23, PagePeer::ACCESS_TO => 24, PagePeer::ACCESS_REDIRECTTO => 25, PagePeer::ACCESS_NOHIDENAVI => 26, PagePeer::ACCESS_NEED_VIA => 27, PagePeer::ACCESS_FROM_GROUPS => 28, PagePeer::CACHE => 29, PagePeer::SEARCH_WORDS => 30, PagePeer::UNSEARCHABLE => 31, PagePeer::ACTIVE_VERSION_ID => 32, ),
        BasePeer::TYPE_RAW_COLNAME => array ('ID' => 0, 'PID' => 1, 'DOMAIN_ID' => 2, 'LFT' => 3, 'RGT' => 4, 'LVL' => 5, 'TYPE' => 6, 'TITLE' => 7, 'PAGE_TITLE' => 8, 'URL' => 9, 'LINK' => 10, 'LAYOUT' => 11, 'SORT' => 12, 'SORT_MODE' => 13, 'TARGET' => 14, 'VISIBLE' => 15, 'ACCESS_DENIED' => 16, 'META' => 17, 'PROPERTIES' => 18, 'CDATE' => 19, 'MDATE' => 20, 'DRAFT_EXIST' => 21, 'FORCE_HTTPS' => 22, 'ACCESS_FROM' => 23, 'ACCESS_TO' => 24, 'ACCESS_REDIRECTTO' => 25, 'ACCESS_NOHIDENAVI' => 26, 'ACCESS_NEED_VIA' => 27, 'ACCESS_FROM_GROUPS' => 28, 'CACHE' => 29, 'SEARCH_WORDS' => 30, 'UNSEARCHABLE' => 31, 'ACTIVE_VERSION_ID' => 32, ),
        BasePeer::TYPE_FIELDNAME => array ('id' => 0, 'pid' => 1, 'domain_id' => 2, 'lft' => 3, 'rgt' => 4, 'lvl' => 5, 'type' => 6, 'title' => 7, 'page_title' => 8, 'url' => 9, 'link' => 10, 'layout' => 11, 'sort' => 12, 'sort_mode' => 13, 'target' => 14, 'visible' => 15, 'access_denied' => 16, 'meta' => 17, 'properties' => 18, 'cdate' => 19, 'mdate' => 20, 'draft_exist' => 21, 'force_https' => 22, 'access_from' => 23, 'access_to' => 24, 'access_redirectto' => 25, 'access_nohidenavi' => 26, 'access_need_via' => 27, 'access_from_groups' => 28, 'cache' => 29, 'search_words' => 30, 'unsearchable' => 31, 'active_version_id' => 32, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, )
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
        $toNames = PagePeer::getFieldNames($toType);
        $key = isset(PagePeer::$fieldKeys[$fromType][$name]) ? PagePeer::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(PagePeer::$fieldKeys[$fromType], true));
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
        if (!array_key_exists($type, PagePeer::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. ' . $type . ' was given.');
        }

        return PagePeer::$fieldNames[$type];
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
     * @param      string $column The column name for current table. (i.e. PagePeer::COLUMN_NAME).
     * @return string
     */
    public static function alias($alias, $column)
    {
        return str_replace(PagePeer::TABLE_NAME.'.', $alias.'.', $column);
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
            $criteria->addSelectColumn(PagePeer::ID);
            $criteria->addSelectColumn(PagePeer::PID);
            $criteria->addSelectColumn(PagePeer::DOMAIN_ID);
            $criteria->addSelectColumn(PagePeer::LFT);
            $criteria->addSelectColumn(PagePeer::RGT);
            $criteria->addSelectColumn(PagePeer::LVL);
            $criteria->addSelectColumn(PagePeer::TYPE);
            $criteria->addSelectColumn(PagePeer::TITLE);
            $criteria->addSelectColumn(PagePeer::PAGE_TITLE);
            $criteria->addSelectColumn(PagePeer::URL);
            $criteria->addSelectColumn(PagePeer::LINK);
            $criteria->addSelectColumn(PagePeer::LAYOUT);
            $criteria->addSelectColumn(PagePeer::SORT);
            $criteria->addSelectColumn(PagePeer::SORT_MODE);
            $criteria->addSelectColumn(PagePeer::TARGET);
            $criteria->addSelectColumn(PagePeer::VISIBLE);
            $criteria->addSelectColumn(PagePeer::ACCESS_DENIED);
            $criteria->addSelectColumn(PagePeer::META);
            $criteria->addSelectColumn(PagePeer::PROPERTIES);
            $criteria->addSelectColumn(PagePeer::CDATE);
            $criteria->addSelectColumn(PagePeer::MDATE);
            $criteria->addSelectColumn(PagePeer::DRAFT_EXIST);
            $criteria->addSelectColumn(PagePeer::FORCE_HTTPS);
            $criteria->addSelectColumn(PagePeer::ACCESS_FROM);
            $criteria->addSelectColumn(PagePeer::ACCESS_TO);
            $criteria->addSelectColumn(PagePeer::ACCESS_REDIRECTTO);
            $criteria->addSelectColumn(PagePeer::ACCESS_NOHIDENAVI);
            $criteria->addSelectColumn(PagePeer::ACCESS_NEED_VIA);
            $criteria->addSelectColumn(PagePeer::ACCESS_FROM_GROUPS);
            $criteria->addSelectColumn(PagePeer::CACHE);
            $criteria->addSelectColumn(PagePeer::SEARCH_WORDS);
            $criteria->addSelectColumn(PagePeer::UNSEARCHABLE);
            $criteria->addSelectColumn(PagePeer::ACTIVE_VERSION_ID);
        } else {
            $criteria->addSelectColumn($alias . '.ID');
            $criteria->addSelectColumn($alias . '.PID');
            $criteria->addSelectColumn($alias . '.DOMAIN_ID');
            $criteria->addSelectColumn($alias . '.LFT');
            $criteria->addSelectColumn($alias . '.RGT');
            $criteria->addSelectColumn($alias . '.LVL');
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
            $criteria->addSelectColumn($alias . '.ACTIVE_VERSION_ID');
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
        $criteria->setPrimaryTableName(PagePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            PagePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
        $criteria->setDbName(PagePeer::DATABASE_NAME); // Set the correct dbName

        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return                 Page
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, PropelPDO $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = PagePeer::doSelect($critcopy, $con);
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
        return PagePeer::populateObjects(PagePeer::doSelectStmt($criteria, $con));
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
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        if (!$criteria->hasSelectClause()) {
            $criteria = clone $criteria;
            PagePeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(PagePeer::DATABASE_NAME);

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
     * @param      Page $obj A Page object.
     * @param      string $key (optional) key to use for instance map (for performance boost if key was already calculated externally).
     */
    public static function addInstanceToPool($obj, $key = null)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if ($key === null) {
                $key = (string) $obj->getId();
            } // if key === null
            PagePeer::$instances[$key] = $obj;
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
     * @param      mixed $value A Page object or a primary key value.
     *
     * @return void
     * @throws PropelException - if the value is invalid.
     */
    public static function removeInstanceFromPool($value)
    {
        if (Propel::isInstancePoolingEnabled() && $value !== null) {
            if (is_object($value) && $value instanceof Page) {
                $key = (string) $value->getId();
            } elseif (is_scalar($value)) {
                // assume we've been passed a primary key
                $key = (string) $value;
            } else {
                $e = new PropelException("Invalid value passed to removeInstanceFromPool().  Expected primary key or Page object; got " . (is_object($value) ? get_class($value) . ' object.' : var_export($value,true)));
                throw $e;
            }

            unset(PagePeer::$instances[$key]);
        }
    } // removeInstanceFromPool()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param      string $key The key (@see getPrimaryKeyHash()) for this instance.
     * @return   Page Found object or NULL if 1) no instance exists for specified key or 2) instance pooling has been disabled.
     * @see        getPrimaryKeyHash()
     */
    public static function getInstanceFromPool($key)
    {
        if (Propel::isInstancePoolingEnabled()) {
            if (isset(PagePeer::$instances[$key])) {
                return PagePeer::$instances[$key];
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
        PagePeer::$instances = array();
    }
    
    /**
     * Method to invalidate the instance pool of all tables related to kryn_system_page
     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in PageContentPeer instance pool,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        PageContentPeer::clearInstancePool();
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
        $cls = PagePeer::getOMClass();
        // populate the object(s)
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key = PagePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj = PagePeer::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                PagePeer::addInstanceToPool($obj, $key);
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
     * @return array (Page object, last column rank)
     */
    public static function populateObject($row, $startcol = 0)
    {
        $key = PagePeer::getPrimaryKeyHashFromRow($row, $startcol);
        if (null !== ($obj = PagePeer::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $startcol, true); // rehydrate
            $col = $startcol + PagePeer::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = PagePeer::OM_CLASS;
            $obj = new $cls();
            $col = $obj->hydrate($row, $startcol);
            PagePeer::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }


    /**
     * Returns the number of rows matching criteria, joining the related Domain table
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinDomain(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(PagePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            PagePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(PagePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(PagePeer::DOMAIN_ID, DomainPeer::ID, $join_behavior);

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
     * Selects a collection of Page objects pre-filled with their Domain objects.
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Page objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinDomain(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(PagePeer::DATABASE_NAME);
        }

        PagePeer::addSelectColumns($criteria);
        $startcol = PagePeer::NUM_HYDRATE_COLUMNS;
        DomainPeer::addSelectColumns($criteria);

        $criteria->addJoin(PagePeer::DOMAIN_ID, DomainPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = PagePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = PagePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {

                $cls = PagePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                PagePeer::addInstanceToPool($obj1, $key1);
            } // if $obj1 already loaded

            $key2 = DomainPeer::getPrimaryKeyHashFromRow($row, $startcol);
            if ($key2 !== null) {
                $obj2 = DomainPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = DomainPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol);
                    DomainPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 already loaded

                // Add the $obj1 (Page) to $obj2 (Domain)
                $obj2->addPage($obj1);

            } // if joined row was not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, PropelPDO $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // We need to set the primary table name, since in the case that there are no WHERE columns
        // it will be impossible for the BasePeer::createSelectSql() method to determine which
        // tables go into the FROM clause.
        $criteria->setPrimaryTableName(PagePeer::TABLE_NAME);

        if ($distinct && !in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->setDistinct();
        }

        if (!$criteria->hasSelectClause()) {
            PagePeer::addSelectColumns($criteria);
        }

        $criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

        // Set the correct dbName
        $criteria->setDbName(PagePeer::DATABASE_NAME);

        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria->addJoin(PagePeer::DOMAIN_ID, DomainPeer::ID, $join_behavior);

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
     * Selects a collection of Page objects pre-filled with all related objects.
     *
     * @param      Criteria  $criteria
     * @param      PropelPDO $con
     * @param      String    $join_behavior the type of joins to use, defaults to Criteria::LEFT_JOIN
     * @return array           Array of Page objects.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $criteria, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $criteria = clone $criteria;

        // Set the correct dbName if it has not been overridden
        if ($criteria->getDbName() == Propel::getDefaultDB()) {
            $criteria->setDbName(PagePeer::DATABASE_NAME);
        }

        PagePeer::addSelectColumns($criteria);
        $startcol2 = PagePeer::NUM_HYDRATE_COLUMNS;

        DomainPeer::addSelectColumns($criteria);
        $startcol3 = $startcol2 + DomainPeer::NUM_HYDRATE_COLUMNS;

        $criteria->addJoin(PagePeer::DOMAIN_ID, DomainPeer::ID, $join_behavior);

        $stmt = BasePeer::doSelect($criteria, $con);
        $results = array();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $key1 = PagePeer::getPrimaryKeyHashFromRow($row, 0);
            if (null !== ($obj1 = PagePeer::getInstanceFromPool($key1))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj1->hydrate($row, 0, true); // rehydrate
            } else {
                $cls = PagePeer::getOMClass();

                $obj1 = new $cls();
                $obj1->hydrate($row);
                PagePeer::addInstanceToPool($obj1, $key1);
            } // if obj1 already loaded

            // Add objects for joined Domain rows

            $key2 = DomainPeer::getPrimaryKeyHashFromRow($row, $startcol2);
            if ($key2 !== null) {
                $obj2 = DomainPeer::getInstanceFromPool($key2);
                if (!$obj2) {

                    $cls = DomainPeer::getOMClass();

                    $obj2 = new $cls();
                    $obj2->hydrate($row, $startcol2);
                    DomainPeer::addInstanceToPool($obj2, $key2);
                } // if obj2 loaded

                // Add the $obj1 (Page) to the collection in $obj2 (Domain)
                $obj2->addPage($obj1);
            } // if joined row not null

            $results[] = $obj1;
        }
        $stmt->closeCursor();

        return $results;
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
        return Propel::getDatabaseMap(PagePeer::DATABASE_NAME)->getTable(PagePeer::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this peer class.
     */
    public static function buildTableMap()
    {
      $dbMap = Propel::getDatabaseMap(BasePagePeer::DATABASE_NAME);
      if (!$dbMap->hasTable(BasePagePeer::TABLE_NAME)) {
        $dbMap->addTableObject(new PageTableMap());
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
        return PagePeer::OM_CLASS;
    }

    /**
     * Performs an INSERT on the database, given a Page or Criteria object.
     *
     * @param      mixed $values Criteria or Page object containing data that is used to create the INSERT statement.
     * @param      PropelPDO $con the PropelPDO connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from Page object
        }

        if ($criteria->containsKey(PagePeer::ID) && $criteria->keyContainsValue(PagePeer::ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.PagePeer::ID.')');
        }


        // Set the correct dbName
        $criteria->setDbName(PagePeer::DATABASE_NAME);

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
     * Performs an UPDATE on the database, given a Page or Criteria object.
     *
     * @param      mixed $values Criteria or Page object containing data that is used to create the UPDATE statement.
     * @param      PropelPDO $con The connection to use (specify PropelPDO connection object to exert more control over transactions).
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $selectCriteria = new Criteria(PagePeer::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(PagePeer::ID);
            $value = $criteria->remove(PagePeer::ID);
            if ($value) {
                $selectCriteria->add(PagePeer::ID, $value, $comparison);
            } else {
                $selectCriteria->setPrimaryTableName(PagePeer::TABLE_NAME);
            }

        } else { // $values is Page object
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(PagePeer::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Deletes all rows from the kryn_system_page table.
     *
     * @param      PropelPDO $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).
     * @throws PropelException
     */
    public static function doDeleteAll(PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            $affectedRows += BasePeer::doDeleteAll(PagePeer::TABLE_NAME, $con, PagePeer::DATABASE_NAME);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            PagePeer::clearInstancePool();
            PagePeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs a DELETE on the database, given a Page or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or Page object or primary key or array of primary keys
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
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        if ($values instanceof Criteria) {
            // invalidate the cache for all objects of this type, since we have no
            // way of knowing (without running a query) what objects should be invalidated
            // from the cache based on this Criteria.
            PagePeer::clearInstancePool();
            // rename for clarity
            $criteria = clone $values;
        } elseif ($values instanceof Page) { // it's a model object
            // invalidate the cache for this single object
            PagePeer::removeInstanceFromPool($values);
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(PagePeer::DATABASE_NAME);
            $criteria->add(PagePeer::ID, (array) $values, Criteria::IN);
            // invalidate the cache for this object(s)
            foreach ((array) $values as $singleval) {
                PagePeer::removeInstanceFromPool($singleval);
            }
        }

        // Set the correct dbName
        $criteria->setDbName(PagePeer::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->beginTransaction();
            
            $affectedRows += BasePeer::doDelete($criteria, $con);
            PagePeer::clearRelatedInstancePool();
            $con->commit();

            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given Page object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      Page $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate($obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(PagePeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(PagePeer::TABLE_NAME);

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

        return BasePeer::doValidate(PagePeer::DATABASE_NAME, PagePeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      int $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return Page
     */
    public static function retrieveByPK($pk, PropelPDO $con = null)
    {

        if (null !== ($obj = PagePeer::getInstanceFromPool((string) $pk))) {
            return $obj;
        }

        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $criteria = new Criteria(PagePeer::DATABASE_NAME);
        $criteria->add(PagePeer::ID, $pk);

        $v = PagePeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      PropelPDO $con the connection to use
     * @return Page[]
     * @throws PropelException Any exceptions caught during processing will be
     *		 rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, PropelPDO $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria(PagePeer::DATABASE_NAME);
            $criteria->add(PagePeer::ID, $pks, Criteria::IN);
            $objs = PagePeer::doSelect($criteria, $con);
        }

        return $objs;
    }

	// nested_set behavior
	
	/**
	 * Returns the root nodes for the tree
	 *
	 * @param      PropelPDO $con	Connection to use.
	 * @return     Page			Propel object for root node
	 */
	public static function retrieveRoots(Criteria $criteria = null, PropelPDO $con = null)
	{
	    if ($criteria === null) {
	        $criteria = new Criteria(PagePeer::DATABASE_NAME);
	    }
	    $criteria->add(PagePeer::LEFT_COL, 1, Criteria::EQUAL);
	
	    return PagePeer::doSelect($criteria, $con);
	}
	
	/**
	 * Returns the root node for a given scope
	 *
	 * @param      int $scope		Scope to determine which root node to return
	 * @param      PropelPDO $con	Connection to use.
	 * @return     Page			Propel object for root node
	 */
	public static function retrieveRoot($scope = null, PropelPDO $con = null)
	{
	    $c = new Criteria(PagePeer::DATABASE_NAME);
	    $c->add(PagePeer::LEFT_COL, 1, Criteria::EQUAL);
	    $c->add(PagePeer::SCOPE_COL, $scope, Criteria::EQUAL);
	
	    return PagePeer::doSelectOne($c, $con);
	}
	
	/**
	 * Returns the whole tree node for a given scope
	 *
	 * @param      int $scope		Scope to determine which root node to return
	 * @param      Criteria $criteria	Optional Criteria to filter the query
	 * @param      PropelPDO $con	Connection to use.
	 * @return     Page			Propel object for root node
	 */
	public static function retrieveTree($scope = null, Criteria $criteria = null, PropelPDO $con = null)
	{
	    if ($criteria === null) {
	        $criteria = new Criteria(PagePeer::DATABASE_NAME);
	    }
	    $criteria->addAscendingOrderByColumn(PagePeer::LEFT_COL);
	    $criteria->add(PagePeer::SCOPE_COL, $scope, Criteria::EQUAL);
	
	    return PagePeer::doSelect($criteria, $con);
	}
	
	/**
	 * Tests if node is valid
	 *
	 * @param      Page $node	Propel object for src node
	 * @return     bool
	 */
	public static function isValid(Page $node = null)
	{
	    if (is_object($node) && $node->getRightValue() > $node->getLeftValue()) {
	        return true;
	    } else {
	        return false;
	    }
	}
	
	/**
	 * Delete an entire tree
	 * 
	 * @param      int $scope		Scope to determine which tree to delete
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     int  The number of deleted nodes
	 */
	public static function deleteTree($scope = null, PropelPDO $con = null)
	{
	    $c = new Criteria(PagePeer::DATABASE_NAME);
	    $c->add(PagePeer::SCOPE_COL, $scope, Criteria::EQUAL);
	
	    return PagePeer::doDelete($c, $con);
	}
	
	/**
	 * Adds $delta to all L and R values that are >= $first and <= $last.
	 * '$delta' can also be negative.
	 *
	 * @param      int $delta		Value to be shifted by, can be negative
	 * @param      int $first		First node to be shifted
	 * @param      int $last			Last node to be shifted (optional)
	 * @param      int $scope		Scope to use for the shift
	 * @param      PropelPDO $con		Connection to use.
	 */
	public static function shiftRLValues($delta, $first, $last = null, $scope = null, PropelPDO $con = null)
	{
	    if ($con === null) {
	        $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
	    }
	
	    // Shift left column values
	    $whereCriteria = new Criteria(PagePeer::DATABASE_NAME);
	    $criterion = $whereCriteria->getNewCriterion(PagePeer::LEFT_COL, $first, Criteria::GREATER_EQUAL);
	    if (null !== $last) {
	        $criterion->addAnd($whereCriteria->getNewCriterion(PagePeer::LEFT_COL, $last, Criteria::LESS_EQUAL));
	    }
	    $whereCriteria->add($criterion);
	    $whereCriteria->add(PagePeer::SCOPE_COL, $scope, Criteria::EQUAL);
	
	    $valuesCriteria = new Criteria(PagePeer::DATABASE_NAME);
	    $valuesCriteria->add(PagePeer::LEFT_COL, array('raw' => PagePeer::LEFT_COL . ' + ?', 'value' => $delta), Criteria::CUSTOM_EQUAL);
	
	    BasePeer::doUpdate($whereCriteria, $valuesCriteria, $con);
	
	    // Shift right column values
	    $whereCriteria = new Criteria(PagePeer::DATABASE_NAME);
	    $criterion = $whereCriteria->getNewCriterion(PagePeer::RIGHT_COL, $first, Criteria::GREATER_EQUAL);
	    if (null !== $last) {
	        $criterion->addAnd($whereCriteria->getNewCriterion(PagePeer::RIGHT_COL, $last, Criteria::LESS_EQUAL));
	    }
	    $whereCriteria->add($criterion);
	    $whereCriteria->add(PagePeer::SCOPE_COL, $scope, Criteria::EQUAL);
	
	    $valuesCriteria = new Criteria(PagePeer::DATABASE_NAME);
	    $valuesCriteria->add(PagePeer::RIGHT_COL, array('raw' => PagePeer::RIGHT_COL . ' + ?', 'value' => $delta), Criteria::CUSTOM_EQUAL);
	
	    BasePeer::doUpdate($whereCriteria, $valuesCriteria, $con);
	}
	
	/**
	 * Adds $delta to level for nodes having left value >= $first and right value <= $last.
	 * '$delta' can also be negative.
	 *
	 * @param      int $delta		Value to be shifted by, can be negative
	 * @param      int $first		First node to be shifted
	 * @param      int $last			Last node to be shifted
	 * @param      int $scope		Scope to use for the shift
	 * @param      PropelPDO $con		Connection to use.
	 */
	public static function shiftLevel($delta, $first, $last, $scope = null, PropelPDO $con = null)
	{
	    if ($con === null) {
	        $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
	    }
	
	    $whereCriteria = new Criteria(PagePeer::DATABASE_NAME);
	    $whereCriteria->add(PagePeer::LEFT_COL, $first, Criteria::GREATER_EQUAL);
	    $whereCriteria->add(PagePeer::RIGHT_COL, $last, Criteria::LESS_EQUAL);
	    $whereCriteria->add(PagePeer::SCOPE_COL, $scope, Criteria::EQUAL);
	
	    $valuesCriteria = new Criteria(PagePeer::DATABASE_NAME);
	    $valuesCriteria->add(PagePeer::LEVEL_COL, array('raw' => PagePeer::LEVEL_COL . ' + ?', 'value' => $delta), Criteria::CUSTOM_EQUAL);
	
	    BasePeer::doUpdate($whereCriteria, $valuesCriteria, $con);
	}
	
	/**
	 * Reload all already loaded nodes to sync them with updated db
	 *
	 * @param      Page $prune		Object to prune from the update
	 * @param      PropelPDO $con		Connection to use.
	 */
	public static function updateLoadedNodes($prune = null, PropelPDO $con = null)
	{
	    if (Propel::isInstancePoolingEnabled()) {
	        $keys = array();
	        foreach (PagePeer::$instances as $obj) {
	            if (!$prune || !$prune->equals($obj)) {
	                $keys[] = $obj->getPrimaryKey();
	            }
	        }
	
	        if (!empty($keys)) {
	            // We don't need to alter the object instance pool; we're just modifying these ones
	            // already in the pool.
	            $criteria = new Criteria(PagePeer::DATABASE_NAME);
	            $criteria->add(PagePeer::ID, $keys, Criteria::IN);
	            $stmt = PagePeer::doSelectStmt($criteria, $con);
	            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
	                $key = PagePeer::getPrimaryKeyHashFromRow($row, 0);
	                if (null !== ($object = PagePeer::getInstanceFromPool($key))) {
	                    $object->setLeftValue($row[3]);
	                    $object->setRightValue($row[4]);
	                    $object->setLevel($row[5]);
	                    $object->clearNestedSetChildren();
	                }
	            }
	            $stmt->closeCursor();
	        }
	    }
	}
	
	/**
	 * Update the tree to allow insertion of a leaf at the specified position
	 *
	 * @param      int $left	left column value
	 * @param      integer $scope	scope column value
	 * @param      mixed $prune	Object to prune from the shift
	 * @param      PropelPDO $con	Connection to use.
	 */
	public static function makeRoomForLeaf($left, $scope, $prune = null, PropelPDO $con = null)
	{
	    // Update database nodes
	    PagePeer::shiftRLValues(2, $left, null, $scope, $con);
	
	    // Update all loaded nodes
	    PagePeer::updateLoadedNodes($prune, $con);
	}
	
	/**
	 * Update the tree to allow insertion of a leaf at the specified position
	 *
	 * @param      integer $scope	scope column value
	 * @param      PropelPDO $con	Connection to use.
	 */
	public static function fixLevels($scope, PropelPDO $con = null)
	{
	    $c = new Criteria();
	    $c->add(PagePeer::SCOPE_COL, $scope, Criteria::EQUAL);
	    $c->addAscendingOrderByColumn(PagePeer::LEFT_COL);
	    $stmt = PagePeer::doSelectStmt($c, $con);
	    
	    // set the class once to avoid overhead in the loop
	    $cls = PagePeer::getOMClass(false);
	    $level = null;
	    // iterate over the statement
	    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
	
	        // hydrate object
	        $key = PagePeer::getPrimaryKeyHashFromRow($row, 0);
	        if (null === ($obj = PagePeer::getInstanceFromPool($key))) {
	            $obj = new $cls();
	            $obj->hydrate($row);
	            PagePeer::addInstanceToPool($obj, $key);
	        }
	
	        // compute level
	        // Algorithm shamelessly stolen from sfPropelActAsNestedSetBehaviorPlugin
	        // Probably authored by Tristan Rivoallan
	        if ($level === null) {
	            $level = 0;
	            $i = 0;
	            $prev = array($obj->getRightValue());
	        } else {
	            while ($obj->getRightValue() > $prev[$i]) {
	                $i--;
	            }
	            $level = ++$i;
	            $prev[$i] = $obj->getRightValue();
	        }
	
	        // update level in node if necessary
	        if ($obj->getLevel() !== $level) {
	            $obj->setLevel($level);
	            $obj->save($con);
	        }
	    }
	    $stmt->closeCursor();
	}

} // BasePagePeer

// This is the static code needed to register the TableMap for this table with the main Propel class.
//
BasePagePeer::buildTableMap();


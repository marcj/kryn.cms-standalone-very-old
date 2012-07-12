<?php


/**
 * Base class that represents a query for the 'kryn_system_domains' table.
 *
 * 
 *
 * @method     SystemDomainsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     SystemDomainsQuery orderByDomain($order = Criteria::ASC) Order by the domain column
 * @method     SystemDomainsQuery orderByTitleFormat($order = Criteria::ASC) Order by the title_format column
 * @method     SystemDomainsQuery orderByLang($order = Criteria::ASC) Order by the lang column
 * @method     SystemDomainsQuery orderByStartpageId($order = Criteria::ASC) Order by the startpage_id column
 * @method     SystemDomainsQuery orderByAlias($order = Criteria::ASC) Order by the alias column
 * @method     SystemDomainsQuery orderByRedirect($order = Criteria::ASC) Order by the redirect column
 * @method     SystemDomainsQuery orderByPage404id($order = Criteria::ASC) Order by the page404_id column
 * @method     SystemDomainsQuery orderByPage404interface($order = Criteria::ASC) Order by the page404interface column
 * @method     SystemDomainsQuery orderByMaster($order = Criteria::ASC) Order by the master column
 * @method     SystemDomainsQuery orderByResourcecompression($order = Criteria::ASC) Order by the resourcecompression column
 * @method     SystemDomainsQuery orderByLayouts($order = Criteria::ASC) Order by the layouts column
 * @method     SystemDomainsQuery orderByPhplocale($order = Criteria::ASC) Order by the phplocale column
 * @method     SystemDomainsQuery orderByPath($order = Criteria::ASC) Order by the path column
 * @method     SystemDomainsQuery orderByThemeproperties($order = Criteria::ASC) Order by the themeproperties column
 * @method     SystemDomainsQuery orderByExtproperties($order = Criteria::ASC) Order by the extproperties column
 * @method     SystemDomainsQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     SystemDomainsQuery orderBySearchIndexKey($order = Criteria::ASC) Order by the search_index_key column
 * @method     SystemDomainsQuery orderByRobots($order = Criteria::ASC) Order by the robots column
 * @method     SystemDomainsQuery orderBySession($order = Criteria::ASC) Order by the session column
 * @method     SystemDomainsQuery orderByFavicon($order = Criteria::ASC) Order by the favicon column
 *
 * @method     SystemDomainsQuery groupById() Group by the id column
 * @method     SystemDomainsQuery groupByDomain() Group by the domain column
 * @method     SystemDomainsQuery groupByTitleFormat() Group by the title_format column
 * @method     SystemDomainsQuery groupByLang() Group by the lang column
 * @method     SystemDomainsQuery groupByStartpageId() Group by the startpage_id column
 * @method     SystemDomainsQuery groupByAlias() Group by the alias column
 * @method     SystemDomainsQuery groupByRedirect() Group by the redirect column
 * @method     SystemDomainsQuery groupByPage404id() Group by the page404_id column
 * @method     SystemDomainsQuery groupByPage404interface() Group by the page404interface column
 * @method     SystemDomainsQuery groupByMaster() Group by the master column
 * @method     SystemDomainsQuery groupByResourcecompression() Group by the resourcecompression column
 * @method     SystemDomainsQuery groupByLayouts() Group by the layouts column
 * @method     SystemDomainsQuery groupByPhplocale() Group by the phplocale column
 * @method     SystemDomainsQuery groupByPath() Group by the path column
 * @method     SystemDomainsQuery groupByThemeproperties() Group by the themeproperties column
 * @method     SystemDomainsQuery groupByExtproperties() Group by the extproperties column
 * @method     SystemDomainsQuery groupByEmail() Group by the email column
 * @method     SystemDomainsQuery groupBySearchIndexKey() Group by the search_index_key column
 * @method     SystemDomainsQuery groupByRobots() Group by the robots column
 * @method     SystemDomainsQuery groupBySession() Group by the session column
 * @method     SystemDomainsQuery groupByFavicon() Group by the favicon column
 *
 * @method     SystemDomainsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SystemDomainsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SystemDomainsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SystemDomains findOne(PropelPDO $con = null) Return the first SystemDomains matching the query
 * @method     SystemDomains findOneOrCreate(PropelPDO $con = null) Return the first SystemDomains matching the query, or a new SystemDomains object populated from the query conditions when no match is found
 *
 * @method     SystemDomains findOneById(int $id) Return the first SystemDomains filtered by the id column
 * @method     SystemDomains findOneByDomain(string $domain) Return the first SystemDomains filtered by the domain column
 * @method     SystemDomains findOneByTitleFormat(string $title_format) Return the first SystemDomains filtered by the title_format column
 * @method     SystemDomains findOneByLang(string $lang) Return the first SystemDomains filtered by the lang column
 * @method     SystemDomains findOneByStartpageId(int $startpage_id) Return the first SystemDomains filtered by the startpage_id column
 * @method     SystemDomains findOneByAlias(string $alias) Return the first SystemDomains filtered by the alias column
 * @method     SystemDomains findOneByRedirect(string $redirect) Return the first SystemDomains filtered by the redirect column
 * @method     SystemDomains findOneByPage404id(int $page404_id) Return the first SystemDomains filtered by the page404_id column
 * @method     SystemDomains findOneByPage404interface(string $page404interface) Return the first SystemDomains filtered by the page404interface column
 * @method     SystemDomains findOneByMaster(int $master) Return the first SystemDomains filtered by the master column
 * @method     SystemDomains findOneByResourcecompression(int $resourcecompression) Return the first SystemDomains filtered by the resourcecompression column
 * @method     SystemDomains findOneByLayouts(string $layouts) Return the first SystemDomains filtered by the layouts column
 * @method     SystemDomains findOneByPhplocale(string $phplocale) Return the first SystemDomains filtered by the phplocale column
 * @method     SystemDomains findOneByPath(string $path) Return the first SystemDomains filtered by the path column
 * @method     SystemDomains findOneByThemeproperties(string $themeproperties) Return the first SystemDomains filtered by the themeproperties column
 * @method     SystemDomains findOneByExtproperties(string $extproperties) Return the first SystemDomains filtered by the extproperties column
 * @method     SystemDomains findOneByEmail(string $email) Return the first SystemDomains filtered by the email column
 * @method     SystemDomains findOneBySearchIndexKey(string $search_index_key) Return the first SystemDomains filtered by the search_index_key column
 * @method     SystemDomains findOneByRobots(string $robots) Return the first SystemDomains filtered by the robots column
 * @method     SystemDomains findOneBySession(string $session) Return the first SystemDomains filtered by the session column
 * @method     SystemDomains findOneByFavicon(string $favicon) Return the first SystemDomains filtered by the favicon column
 *
 * @method     array findById(int $id) Return SystemDomains objects filtered by the id column
 * @method     array findByDomain(string $domain) Return SystemDomains objects filtered by the domain column
 * @method     array findByTitleFormat(string $title_format) Return SystemDomains objects filtered by the title_format column
 * @method     array findByLang(string $lang) Return SystemDomains objects filtered by the lang column
 * @method     array findByStartpageId(int $startpage_id) Return SystemDomains objects filtered by the startpage_id column
 * @method     array findByAlias(string $alias) Return SystemDomains objects filtered by the alias column
 * @method     array findByRedirect(string $redirect) Return SystemDomains objects filtered by the redirect column
 * @method     array findByPage404id(int $page404_id) Return SystemDomains objects filtered by the page404_id column
 * @method     array findByPage404interface(string $page404interface) Return SystemDomains objects filtered by the page404interface column
 * @method     array findByMaster(int $master) Return SystemDomains objects filtered by the master column
 * @method     array findByResourcecompression(int $resourcecompression) Return SystemDomains objects filtered by the resourcecompression column
 * @method     array findByLayouts(string $layouts) Return SystemDomains objects filtered by the layouts column
 * @method     array findByPhplocale(string $phplocale) Return SystemDomains objects filtered by the phplocale column
 * @method     array findByPath(string $path) Return SystemDomains objects filtered by the path column
 * @method     array findByThemeproperties(string $themeproperties) Return SystemDomains objects filtered by the themeproperties column
 * @method     array findByExtproperties(string $extproperties) Return SystemDomains objects filtered by the extproperties column
 * @method     array findByEmail(string $email) Return SystemDomains objects filtered by the email column
 * @method     array findBySearchIndexKey(string $search_index_key) Return SystemDomains objects filtered by the search_index_key column
 * @method     array findByRobots(string $robots) Return SystemDomains objects filtered by the robots column
 * @method     array findBySession(string $session) Return SystemDomains objects filtered by the session column
 * @method     array findByFavicon(string $favicon) Return SystemDomains objects filtered by the favicon column
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemDomainsQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseSystemDomainsQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'kryn', $modelName = 'SystemDomains', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SystemDomainsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     SystemDomainsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SystemDomainsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SystemDomainsQuery) {
            return $criteria;
        }
        $query = new SystemDomainsQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query 
     * @param     PropelPDO $con an optional connection object
     *
     * @return   SystemDomains|SystemDomains[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SystemDomainsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SystemDomainsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return   SystemDomains A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, DOMAIN, TITLE_FORMAT, LANG, STARTPAGE_ID, ALIAS, REDIRECT, PAGE404_ID, PAGE404INTERFACE, MASTER, RESOURCECOMPRESSION, LAYOUTS, PHPLOCALE, PATH, THEMEPROPERTIES, EXTPROPERTIES, EMAIL, SEARCH_INDEX_KEY, ROBOTS, SESSION, FAVICON FROM kryn_system_domains WHERE ID = :p0';
        try {
            $stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new SystemDomains();
            $obj->hydrate($row);
            SystemDomainsPeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return SystemDomains|SystemDomains[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|SystemDomains[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SystemDomainsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SystemDomainsPeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(SystemDomainsPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the domain column
     *
     * Example usage:
     * <code>
     * $query->filterByDomain('fooValue');   // WHERE domain = 'fooValue'
     * $query->filterByDomain('%fooValue%'); // WHERE domain LIKE '%fooValue%'
     * </code>
     *
     * @param     string $domain The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByDomain($domain = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($domain)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $domain)) {
                $domain = str_replace('*', '%', $domain);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::DOMAIN, $domain, $comparison);
    }

    /**
     * Filter the query on the title_format column
     *
     * Example usage:
     * <code>
     * $query->filterByTitleFormat('fooValue');   // WHERE title_format = 'fooValue'
     * $query->filterByTitleFormat('%fooValue%'); // WHERE title_format LIKE '%fooValue%'
     * </code>
     *
     * @param     string $titleFormat The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByTitleFormat($titleFormat = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($titleFormat)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $titleFormat)) {
                $titleFormat = str_replace('*', '%', $titleFormat);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::TITLE_FORMAT, $titleFormat, $comparison);
    }

    /**
     * Filter the query on the lang column
     *
     * Example usage:
     * <code>
     * $query->filterByLang('fooValue');   // WHERE lang = 'fooValue'
     * $query->filterByLang('%fooValue%'); // WHERE lang LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lang The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByLang($lang = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lang)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lang)) {
                $lang = str_replace('*', '%', $lang);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::LANG, $lang, $comparison);
    }

    /**
     * Filter the query on the startpage_id column
     *
     * Example usage:
     * <code>
     * $query->filterByStartpageId(1234); // WHERE startpage_id = 1234
     * $query->filterByStartpageId(array(12, 34)); // WHERE startpage_id IN (12, 34)
     * $query->filterByStartpageId(array('min' => 12)); // WHERE startpage_id > 12
     * </code>
     *
     * @param     mixed $startpageId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByStartpageId($startpageId = null, $comparison = null)
    {
        if (is_array($startpageId)) {
            $useMinMax = false;
            if (isset($startpageId['min'])) {
                $this->addUsingAlias(SystemDomainsPeer::STARTPAGE_ID, $startpageId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($startpageId['max'])) {
                $this->addUsingAlias(SystemDomainsPeer::STARTPAGE_ID, $startpageId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::STARTPAGE_ID, $startpageId, $comparison);
    }

    /**
     * Filter the query on the alias column
     *
     * Example usage:
     * <code>
     * $query->filterByAlias('fooValue');   // WHERE alias = 'fooValue'
     * $query->filterByAlias('%fooValue%'); // WHERE alias LIKE '%fooValue%'
     * </code>
     *
     * @param     string $alias The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByAlias($alias = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($alias)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $alias)) {
                $alias = str_replace('*', '%', $alias);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::ALIAS, $alias, $comparison);
    }

    /**
     * Filter the query on the redirect column
     *
     * Example usage:
     * <code>
     * $query->filterByRedirect('fooValue');   // WHERE redirect = 'fooValue'
     * $query->filterByRedirect('%fooValue%'); // WHERE redirect LIKE '%fooValue%'
     * </code>
     *
     * @param     string $redirect The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByRedirect($redirect = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($redirect)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $redirect)) {
                $redirect = str_replace('*', '%', $redirect);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::REDIRECT, $redirect, $comparison);
    }

    /**
     * Filter the query on the page404_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPage404id(1234); // WHERE page404_id = 1234
     * $query->filterByPage404id(array(12, 34)); // WHERE page404_id IN (12, 34)
     * $query->filterByPage404id(array('min' => 12)); // WHERE page404_id > 12
     * </code>
     *
     * @param     mixed $page404id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByPage404id($page404id = null, $comparison = null)
    {
        if (is_array($page404id)) {
            $useMinMax = false;
            if (isset($page404id['min'])) {
                $this->addUsingAlias(SystemDomainsPeer::PAGE404_ID, $page404id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($page404id['max'])) {
                $this->addUsingAlias(SystemDomainsPeer::PAGE404_ID, $page404id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::PAGE404_ID, $page404id, $comparison);
    }

    /**
     * Filter the query on the page404interface column
     *
     * Example usage:
     * <code>
     * $query->filterByPage404interface('fooValue');   // WHERE page404interface = 'fooValue'
     * $query->filterByPage404interface('%fooValue%'); // WHERE page404interface LIKE '%fooValue%'
     * </code>
     *
     * @param     string $page404interface The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByPage404interface($page404interface = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($page404interface)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $page404interface)) {
                $page404interface = str_replace('*', '%', $page404interface);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::PAGE404INTERFACE, $page404interface, $comparison);
    }

    /**
     * Filter the query on the master column
     *
     * Example usage:
     * <code>
     * $query->filterByMaster(1234); // WHERE master = 1234
     * $query->filterByMaster(array(12, 34)); // WHERE master IN (12, 34)
     * $query->filterByMaster(array('min' => 12)); // WHERE master > 12
     * </code>
     *
     * @param     mixed $master The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByMaster($master = null, $comparison = null)
    {
        if (is_array($master)) {
            $useMinMax = false;
            if (isset($master['min'])) {
                $this->addUsingAlias(SystemDomainsPeer::MASTER, $master['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($master['max'])) {
                $this->addUsingAlias(SystemDomainsPeer::MASTER, $master['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::MASTER, $master, $comparison);
    }

    /**
     * Filter the query on the resourcecompression column
     *
     * Example usage:
     * <code>
     * $query->filterByResourcecompression(1234); // WHERE resourcecompression = 1234
     * $query->filterByResourcecompression(array(12, 34)); // WHERE resourcecompression IN (12, 34)
     * $query->filterByResourcecompression(array('min' => 12)); // WHERE resourcecompression > 12
     * </code>
     *
     * @param     mixed $resourcecompression The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByResourcecompression($resourcecompression = null, $comparison = null)
    {
        if (is_array($resourcecompression)) {
            $useMinMax = false;
            if (isset($resourcecompression['min'])) {
                $this->addUsingAlias(SystemDomainsPeer::RESOURCECOMPRESSION, $resourcecompression['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($resourcecompression['max'])) {
                $this->addUsingAlias(SystemDomainsPeer::RESOURCECOMPRESSION, $resourcecompression['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::RESOURCECOMPRESSION, $resourcecompression, $comparison);
    }

    /**
     * Filter the query on the layouts column
     *
     * Example usage:
     * <code>
     * $query->filterByLayouts('fooValue');   // WHERE layouts = 'fooValue'
     * $query->filterByLayouts('%fooValue%'); // WHERE layouts LIKE '%fooValue%'
     * </code>
     *
     * @param     string $layouts The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByLayouts($layouts = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($layouts)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $layouts)) {
                $layouts = str_replace('*', '%', $layouts);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::LAYOUTS, $layouts, $comparison);
    }

    /**
     * Filter the query on the phplocale column
     *
     * Example usage:
     * <code>
     * $query->filterByPhplocale('fooValue');   // WHERE phplocale = 'fooValue'
     * $query->filterByPhplocale('%fooValue%'); // WHERE phplocale LIKE '%fooValue%'
     * </code>
     *
     * @param     string $phplocale The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByPhplocale($phplocale = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($phplocale)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $phplocale)) {
                $phplocale = str_replace('*', '%', $phplocale);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::PHPLOCALE, $phplocale, $comparison);
    }

    /**
     * Filter the query on the path column
     *
     * Example usage:
     * <code>
     * $query->filterByPath('fooValue');   // WHERE path = 'fooValue'
     * $query->filterByPath('%fooValue%'); // WHERE path LIKE '%fooValue%'
     * </code>
     *
     * @param     string $path The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByPath($path = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($path)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $path)) {
                $path = str_replace('*', '%', $path);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::PATH, $path, $comparison);
    }

    /**
     * Filter the query on the themeproperties column
     *
     * Example usage:
     * <code>
     * $query->filterByThemeproperties('fooValue');   // WHERE themeproperties = 'fooValue'
     * $query->filterByThemeproperties('%fooValue%'); // WHERE themeproperties LIKE '%fooValue%'
     * </code>
     *
     * @param     string $themeproperties The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByThemeproperties($themeproperties = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($themeproperties)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $themeproperties)) {
                $themeproperties = str_replace('*', '%', $themeproperties);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::THEMEPROPERTIES, $themeproperties, $comparison);
    }

    /**
     * Filter the query on the extproperties column
     *
     * Example usage:
     * <code>
     * $query->filterByExtproperties('fooValue');   // WHERE extproperties = 'fooValue'
     * $query->filterByExtproperties('%fooValue%'); // WHERE extproperties LIKE '%fooValue%'
     * </code>
     *
     * @param     string $extproperties The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByExtproperties($extproperties = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($extproperties)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $extproperties)) {
                $extproperties = str_replace('*', '%', $extproperties);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::EXTPROPERTIES, $extproperties, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $email The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($email)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $email)) {
                $email = str_replace('*', '%', $email);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::EMAIL, $email, $comparison);
    }

    /**
     * Filter the query on the search_index_key column
     *
     * Example usage:
     * <code>
     * $query->filterBySearchIndexKey('fooValue');   // WHERE search_index_key = 'fooValue'
     * $query->filterBySearchIndexKey('%fooValue%'); // WHERE search_index_key LIKE '%fooValue%'
     * </code>
     *
     * @param     string $searchIndexKey The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterBySearchIndexKey($searchIndexKey = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($searchIndexKey)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $searchIndexKey)) {
                $searchIndexKey = str_replace('*', '%', $searchIndexKey);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::SEARCH_INDEX_KEY, $searchIndexKey, $comparison);
    }

    /**
     * Filter the query on the robots column
     *
     * Example usage:
     * <code>
     * $query->filterByRobots('fooValue');   // WHERE robots = 'fooValue'
     * $query->filterByRobots('%fooValue%'); // WHERE robots LIKE '%fooValue%'
     * </code>
     *
     * @param     string $robots The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByRobots($robots = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($robots)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $robots)) {
                $robots = str_replace('*', '%', $robots);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::ROBOTS, $robots, $comparison);
    }

    /**
     * Filter the query on the session column
     *
     * Example usage:
     * <code>
     * $query->filterBySession('fooValue');   // WHERE session = 'fooValue'
     * $query->filterBySession('%fooValue%'); // WHERE session LIKE '%fooValue%'
     * </code>
     *
     * @param     string $session The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterBySession($session = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($session)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $session)) {
                $session = str_replace('*', '%', $session);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::SESSION, $session, $comparison);
    }

    /**
     * Filter the query on the favicon column
     *
     * Example usage:
     * <code>
     * $query->filterByFavicon('fooValue');   // WHERE favicon = 'fooValue'
     * $query->filterByFavicon('%fooValue%'); // WHERE favicon LIKE '%fooValue%'
     * </code>
     *
     * @param     string $favicon The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function filterByFavicon($favicon = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($favicon)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $favicon)) {
                $favicon = str_replace('*', '%', $favicon);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemDomainsPeer::FAVICON, $favicon, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   SystemDomains $systemDomains Object to remove from the list of results
     *
     * @return SystemDomainsQuery The current query, for fluid interface
     */
    public function prune($systemDomains = null)
    {
        if ($systemDomains) {
            $this->addUsingAlias(SystemDomainsPeer::ID, $systemDomains->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseSystemDomainsQuery
<?php


/**
 * Base class that represents a query for the 'kryn_system_domain' table.
 *
 * 
 *
 * @method     DomainQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     DomainQuery orderByDomain($order = Criteria::ASC) Order by the domain column
 * @method     DomainQuery orderByTitleFormat($order = Criteria::ASC) Order by the title_format column
 * @method     DomainQuery orderByLang($order = Criteria::ASC) Order by the lang column
 * @method     DomainQuery orderByStartpageId($order = Criteria::ASC) Order by the startpage_id column
 * @method     DomainQuery orderByAlias($order = Criteria::ASC) Order by the alias column
 * @method     DomainQuery orderByRedirect($order = Criteria::ASC) Order by the redirect column
 * @method     DomainQuery orderByPage404id($order = Criteria::ASC) Order by the page404_id column
 * @method     DomainQuery orderByPage404interface($order = Criteria::ASC) Order by the page404interface column
 * @method     DomainQuery orderByMaster($order = Criteria::ASC) Order by the master column
 * @method     DomainQuery orderByResourcecompression($order = Criteria::ASC) Order by the resourcecompression column
 * @method     DomainQuery orderByLayouts($order = Criteria::ASC) Order by the layouts column
 * @method     DomainQuery orderByPhplocale($order = Criteria::ASC) Order by the phplocale column
 * @method     DomainQuery orderByPath($order = Criteria::ASC) Order by the path column
 * @method     DomainQuery orderByThemeproperties($order = Criteria::ASC) Order by the themeproperties column
 * @method     DomainQuery orderByExtproperties($order = Criteria::ASC) Order by the extproperties column
 * @method     DomainQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     DomainQuery orderBySearchIndexKey($order = Criteria::ASC) Order by the search_index_key column
 * @method     DomainQuery orderByRobots($order = Criteria::ASC) Order by the robots column
 * @method     DomainQuery orderBySession($order = Criteria::ASC) Order by the session column
 * @method     DomainQuery orderByFavicon($order = Criteria::ASC) Order by the favicon column
 *
 * @method     DomainQuery groupById() Group by the id column
 * @method     DomainQuery groupByDomain() Group by the domain column
 * @method     DomainQuery groupByTitleFormat() Group by the title_format column
 * @method     DomainQuery groupByLang() Group by the lang column
 * @method     DomainQuery groupByStartpageId() Group by the startpage_id column
 * @method     DomainQuery groupByAlias() Group by the alias column
 * @method     DomainQuery groupByRedirect() Group by the redirect column
 * @method     DomainQuery groupByPage404id() Group by the page404_id column
 * @method     DomainQuery groupByPage404interface() Group by the page404interface column
 * @method     DomainQuery groupByMaster() Group by the master column
 * @method     DomainQuery groupByResourcecompression() Group by the resourcecompression column
 * @method     DomainQuery groupByLayouts() Group by the layouts column
 * @method     DomainQuery groupByPhplocale() Group by the phplocale column
 * @method     DomainQuery groupByPath() Group by the path column
 * @method     DomainQuery groupByThemeproperties() Group by the themeproperties column
 * @method     DomainQuery groupByExtproperties() Group by the extproperties column
 * @method     DomainQuery groupByEmail() Group by the email column
 * @method     DomainQuery groupBySearchIndexKey() Group by the search_index_key column
 * @method     DomainQuery groupByRobots() Group by the robots column
 * @method     DomainQuery groupBySession() Group by the session column
 * @method     DomainQuery groupByFavicon() Group by the favicon column
 *
 * @method     DomainQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     DomainQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     DomainQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     DomainQuery leftJoinPage($relationAlias = null) Adds a LEFT JOIN clause to the query using the Page relation
 * @method     DomainQuery rightJoinPage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Page relation
 * @method     DomainQuery innerJoinPage($relationAlias = null) Adds a INNER JOIN clause to the query using the Page relation
 *
 * @method     Domain findOne(PropelPDO $con = null) Return the first Domain matching the query
 * @method     Domain findOneOrCreate(PropelPDO $con = null) Return the first Domain matching the query, or a new Domain object populated from the query conditions when no match is found
 *
 * @method     Domain findOneById(int $id) Return the first Domain filtered by the id column
 * @method     Domain findOneByDomain(string $domain) Return the first Domain filtered by the domain column
 * @method     Domain findOneByTitleFormat(string $title_format) Return the first Domain filtered by the title_format column
 * @method     Domain findOneByLang(string $lang) Return the first Domain filtered by the lang column
 * @method     Domain findOneByStartpageId(int $startpage_id) Return the first Domain filtered by the startpage_id column
 * @method     Domain findOneByAlias(string $alias) Return the first Domain filtered by the alias column
 * @method     Domain findOneByRedirect(string $redirect) Return the first Domain filtered by the redirect column
 * @method     Domain findOneByPage404id(int $page404_id) Return the first Domain filtered by the page404_id column
 * @method     Domain findOneByPage404interface(string $page404interface) Return the first Domain filtered by the page404interface column
 * @method     Domain findOneByMaster(int $master) Return the first Domain filtered by the master column
 * @method     Domain findOneByResourcecompression(int $resourcecompression) Return the first Domain filtered by the resourcecompression column
 * @method     Domain findOneByLayouts(string $layouts) Return the first Domain filtered by the layouts column
 * @method     Domain findOneByPhplocale(string $phplocale) Return the first Domain filtered by the phplocale column
 * @method     Domain findOneByPath(string $path) Return the first Domain filtered by the path column
 * @method     Domain findOneByThemeproperties(string $themeproperties) Return the first Domain filtered by the themeproperties column
 * @method     Domain findOneByExtproperties(string $extproperties) Return the first Domain filtered by the extproperties column
 * @method     Domain findOneByEmail(string $email) Return the first Domain filtered by the email column
 * @method     Domain findOneBySearchIndexKey(string $search_index_key) Return the first Domain filtered by the search_index_key column
 * @method     Domain findOneByRobots(string $robots) Return the first Domain filtered by the robots column
 * @method     Domain findOneBySession(string $session) Return the first Domain filtered by the session column
 * @method     Domain findOneByFavicon(string $favicon) Return the first Domain filtered by the favicon column
 *
 * @method     array findById(int $id) Return Domain objects filtered by the id column
 * @method     array findByDomain(string $domain) Return Domain objects filtered by the domain column
 * @method     array findByTitleFormat(string $title_format) Return Domain objects filtered by the title_format column
 * @method     array findByLang(string $lang) Return Domain objects filtered by the lang column
 * @method     array findByStartpageId(int $startpage_id) Return Domain objects filtered by the startpage_id column
 * @method     array findByAlias(string $alias) Return Domain objects filtered by the alias column
 * @method     array findByRedirect(string $redirect) Return Domain objects filtered by the redirect column
 * @method     array findByPage404id(int $page404_id) Return Domain objects filtered by the page404_id column
 * @method     array findByPage404interface(string $page404interface) Return Domain objects filtered by the page404interface column
 * @method     array findByMaster(int $master) Return Domain objects filtered by the master column
 * @method     array findByResourcecompression(int $resourcecompression) Return Domain objects filtered by the resourcecompression column
 * @method     array findByLayouts(string $layouts) Return Domain objects filtered by the layouts column
 * @method     array findByPhplocale(string $phplocale) Return Domain objects filtered by the phplocale column
 * @method     array findByPath(string $path) Return Domain objects filtered by the path column
 * @method     array findByThemeproperties(string $themeproperties) Return Domain objects filtered by the themeproperties column
 * @method     array findByExtproperties(string $extproperties) Return Domain objects filtered by the extproperties column
 * @method     array findByEmail(string $email) Return Domain objects filtered by the email column
 * @method     array findBySearchIndexKey(string $search_index_key) Return Domain objects filtered by the search_index_key column
 * @method     array findByRobots(string $robots) Return Domain objects filtered by the robots column
 * @method     array findBySession(string $session) Return Domain objects filtered by the session column
 * @method     array findByFavicon(string $favicon) Return Domain objects filtered by the favicon column
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BaseDomainQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseDomainQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Kryn', $modelName = 'Domain', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new DomainQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     DomainQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return DomainQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof DomainQuery) {
            return $criteria;
        }
        $query = new DomainQuery();
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
     * @return   Domain|Domain[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = DomainPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(DomainPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Domain A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, DOMAIN, TITLE_FORMAT, LANG, STARTPAGE_ID, ALIAS, REDIRECT, PAGE404_ID, PAGE404INTERFACE, MASTER, RESOURCECOMPRESSION, LAYOUTS, PHPLOCALE, PATH, THEMEPROPERTIES, EXTPROPERTIES, EMAIL, SEARCH_INDEX_KEY, ROBOTS, SESSION, FAVICON FROM kryn_system_domain WHERE ID = :p0';
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
            $obj = new Domain();
            $obj->hydrate($row);
            DomainPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Domain|Domain[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Domain[]|mixed the list of results, formatted by the current formatter
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
     * @return DomainQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(DomainPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return DomainQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(DomainPeer::ID, $keys, Criteria::IN);
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
     * @return DomainQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(DomainPeer::ID, $id, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::DOMAIN, $domain, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::TITLE_FORMAT, $titleFormat, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::LANG, $lang, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
     */
    public function filterByStartpageId($startpageId = null, $comparison = null)
    {
        if (is_array($startpageId)) {
            $useMinMax = false;
            if (isset($startpageId['min'])) {
                $this->addUsingAlias(DomainPeer::STARTPAGE_ID, $startpageId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($startpageId['max'])) {
                $this->addUsingAlias(DomainPeer::STARTPAGE_ID, $startpageId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DomainPeer::STARTPAGE_ID, $startpageId, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::ALIAS, $alias, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::REDIRECT, $redirect, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
     */
    public function filterByPage404id($page404id = null, $comparison = null)
    {
        if (is_array($page404id)) {
            $useMinMax = false;
            if (isset($page404id['min'])) {
                $this->addUsingAlias(DomainPeer::PAGE404_ID, $page404id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($page404id['max'])) {
                $this->addUsingAlias(DomainPeer::PAGE404_ID, $page404id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DomainPeer::PAGE404_ID, $page404id, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::PAGE404INTERFACE, $page404interface, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
     */
    public function filterByMaster($master = null, $comparison = null)
    {
        if (is_array($master)) {
            $useMinMax = false;
            if (isset($master['min'])) {
                $this->addUsingAlias(DomainPeer::MASTER, $master['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($master['max'])) {
                $this->addUsingAlias(DomainPeer::MASTER, $master['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DomainPeer::MASTER, $master, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
     */
    public function filterByResourcecompression($resourcecompression = null, $comparison = null)
    {
        if (is_array($resourcecompression)) {
            $useMinMax = false;
            if (isset($resourcecompression['min'])) {
                $this->addUsingAlias(DomainPeer::RESOURCECOMPRESSION, $resourcecompression['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($resourcecompression['max'])) {
                $this->addUsingAlias(DomainPeer::RESOURCECOMPRESSION, $resourcecompression['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(DomainPeer::RESOURCECOMPRESSION, $resourcecompression, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::LAYOUTS, $layouts, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::PHPLOCALE, $phplocale, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::PATH, $path, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::THEMEPROPERTIES, $themeproperties, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::EXTPROPERTIES, $extproperties, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::EMAIL, $email, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::SEARCH_INDEX_KEY, $searchIndexKey, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::ROBOTS, $robots, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::SESSION, $session, $comparison);
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
     * @return DomainQuery The current query, for fluid interface
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

        return $this->addUsingAlias(DomainPeer::FAVICON, $favicon, $comparison);
    }

    /**
     * Filter the query by a related Page object
     *
     * @param   Page|PropelObjectCollection $page  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   DomainQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByPage($page, $comparison = null)
    {
        if ($page instanceof Page) {
            return $this
                ->addUsingAlias(DomainPeer::ID, $page->getDomainId(), $comparison);
        } elseif ($page instanceof PropelObjectCollection) {
            return $this
                ->usePageQuery()
                ->filterByPrimaryKeys($page->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPage() only accepts arguments of type Page or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Page relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return DomainQuery The current query, for fluid interface
     */
    public function joinPage($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Page');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Page');
        }

        return $this;
    }

    /**
     * Use the Page relation Page object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   PageQuery A secondary query class using the current class as primary query
     */
    public function usePageQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Page', 'PageQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Domain $domain Object to remove from the list of results
     *
     * @return DomainQuery The current query, for fluid interface
     */
    public function prune($domain = null)
    {
        if ($domain) {
            $this->addUsingAlias(DomainPeer::ID, $domain->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseDomainQuery
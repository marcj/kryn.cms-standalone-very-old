<?php


/**
 * Base class that represents a query for the 'kryn_system_pages' table.
 *
 * 
 *
 * @method     SystemPagesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     SystemPagesQuery orderByPid($order = Criteria::ASC) Order by the pid column
 * @method     SystemPagesQuery orderByDomainId($order = Criteria::ASC) Order by the domain_id column
 * @method     SystemPagesQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     SystemPagesQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     SystemPagesQuery orderByPageTitle($order = Criteria::ASC) Order by the page_title column
 * @method     SystemPagesQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     SystemPagesQuery orderByLink($order = Criteria::ASC) Order by the link column
 * @method     SystemPagesQuery orderByLayout($order = Criteria::ASC) Order by the layout column
 * @method     SystemPagesQuery orderBySort($order = Criteria::ASC) Order by the sort column
 * @method     SystemPagesQuery orderBySortMode($order = Criteria::ASC) Order by the sort_mode column
 * @method     SystemPagesQuery orderByTarget($order = Criteria::ASC) Order by the target column
 * @method     SystemPagesQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     SystemPagesQuery orderByAccessDenied($order = Criteria::ASC) Order by the access_denied column
 * @method     SystemPagesQuery orderByMeta($order = Criteria::ASC) Order by the meta column
 * @method     SystemPagesQuery orderByProperties($order = Criteria::ASC) Order by the properties column
 * @method     SystemPagesQuery orderByCdate($order = Criteria::ASC) Order by the cdate column
 * @method     SystemPagesQuery orderByMdate($order = Criteria::ASC) Order by the mdate column
 * @method     SystemPagesQuery orderByDraftExist($order = Criteria::ASC) Order by the draft_exist column
 * @method     SystemPagesQuery orderByForceHttps($order = Criteria::ASC) Order by the force_https column
 * @method     SystemPagesQuery orderByAccessFrom($order = Criteria::ASC) Order by the access_from column
 * @method     SystemPagesQuery orderByAccessTo($order = Criteria::ASC) Order by the access_to column
 * @method     SystemPagesQuery orderByAccessRedirectto($order = Criteria::ASC) Order by the access_redirectto column
 * @method     SystemPagesQuery orderByAccessNohidenavi($order = Criteria::ASC) Order by the access_nohidenavi column
 * @method     SystemPagesQuery orderByAccessNeedVia($order = Criteria::ASC) Order by the access_need_via column
 * @method     SystemPagesQuery orderByAccessFromGroups($order = Criteria::ASC) Order by the access_from_groups column
 * @method     SystemPagesQuery orderByCache($order = Criteria::ASC) Order by the cache column
 * @method     SystemPagesQuery orderBySearchWords($order = Criteria::ASC) Order by the search_words column
 * @method     SystemPagesQuery orderByUnsearchable($order = Criteria::ASC) Order by the unsearchable column
 * @method     SystemPagesQuery orderByLft($order = Criteria::ASC) Order by the lft column
 * @method     SystemPagesQuery orderByRgt($order = Criteria::ASC) Order by the rgt column
 *
 * @method     SystemPagesQuery groupById() Group by the id column
 * @method     SystemPagesQuery groupByPid() Group by the pid column
 * @method     SystemPagesQuery groupByDomainId() Group by the domain_id column
 * @method     SystemPagesQuery groupByType() Group by the type column
 * @method     SystemPagesQuery groupByTitle() Group by the title column
 * @method     SystemPagesQuery groupByPageTitle() Group by the page_title column
 * @method     SystemPagesQuery groupByUrl() Group by the url column
 * @method     SystemPagesQuery groupByLink() Group by the link column
 * @method     SystemPagesQuery groupByLayout() Group by the layout column
 * @method     SystemPagesQuery groupBySort() Group by the sort column
 * @method     SystemPagesQuery groupBySortMode() Group by the sort_mode column
 * @method     SystemPagesQuery groupByTarget() Group by the target column
 * @method     SystemPagesQuery groupByVisible() Group by the visible column
 * @method     SystemPagesQuery groupByAccessDenied() Group by the access_denied column
 * @method     SystemPagesQuery groupByMeta() Group by the meta column
 * @method     SystemPagesQuery groupByProperties() Group by the properties column
 * @method     SystemPagesQuery groupByCdate() Group by the cdate column
 * @method     SystemPagesQuery groupByMdate() Group by the mdate column
 * @method     SystemPagesQuery groupByDraftExist() Group by the draft_exist column
 * @method     SystemPagesQuery groupByForceHttps() Group by the force_https column
 * @method     SystemPagesQuery groupByAccessFrom() Group by the access_from column
 * @method     SystemPagesQuery groupByAccessTo() Group by the access_to column
 * @method     SystemPagesQuery groupByAccessRedirectto() Group by the access_redirectto column
 * @method     SystemPagesQuery groupByAccessNohidenavi() Group by the access_nohidenavi column
 * @method     SystemPagesQuery groupByAccessNeedVia() Group by the access_need_via column
 * @method     SystemPagesQuery groupByAccessFromGroups() Group by the access_from_groups column
 * @method     SystemPagesQuery groupByCache() Group by the cache column
 * @method     SystemPagesQuery groupBySearchWords() Group by the search_words column
 * @method     SystemPagesQuery groupByUnsearchable() Group by the unsearchable column
 * @method     SystemPagesQuery groupByLft() Group by the lft column
 * @method     SystemPagesQuery groupByRgt() Group by the rgt column
 *
 * @method     SystemPagesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SystemPagesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SystemPagesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SystemPages findOne(PropelPDO $con = null) Return the first SystemPages matching the query
 * @method     SystemPages findOneOrCreate(PropelPDO $con = null) Return the first SystemPages matching the query, or a new SystemPages object populated from the query conditions when no match is found
 *
 * @method     SystemPages findOneById(int $id) Return the first SystemPages filtered by the id column
 * @method     SystemPages findOneByPid(int $pid) Return the first SystemPages filtered by the pid column
 * @method     SystemPages findOneByDomainId(int $domain_id) Return the first SystemPages filtered by the domain_id column
 * @method     SystemPages findOneByType(int $type) Return the first SystemPages filtered by the type column
 * @method     SystemPages findOneByTitle(string $title) Return the first SystemPages filtered by the title column
 * @method     SystemPages findOneByPageTitle(string $page_title) Return the first SystemPages filtered by the page_title column
 * @method     SystemPages findOneByUrl(string $url) Return the first SystemPages filtered by the url column
 * @method     SystemPages findOneByLink(string $link) Return the first SystemPages filtered by the link column
 * @method     SystemPages findOneByLayout(string $layout) Return the first SystemPages filtered by the layout column
 * @method     SystemPages findOneBySort(int $sort) Return the first SystemPages filtered by the sort column
 * @method     SystemPages findOneBySortMode(string $sort_mode) Return the first SystemPages filtered by the sort_mode column
 * @method     SystemPages findOneByTarget(string $target) Return the first SystemPages filtered by the target column
 * @method     SystemPages findOneByVisible(int $visible) Return the first SystemPages filtered by the visible column
 * @method     SystemPages findOneByAccessDenied(string $access_denied) Return the first SystemPages filtered by the access_denied column
 * @method     SystemPages findOneByMeta(string $meta) Return the first SystemPages filtered by the meta column
 * @method     SystemPages findOneByProperties(string $properties) Return the first SystemPages filtered by the properties column
 * @method     SystemPages findOneByCdate(int $cdate) Return the first SystemPages filtered by the cdate column
 * @method     SystemPages findOneByMdate(int $mdate) Return the first SystemPages filtered by the mdate column
 * @method     SystemPages findOneByDraftExist(int $draft_exist) Return the first SystemPages filtered by the draft_exist column
 * @method     SystemPages findOneByForceHttps(int $force_https) Return the first SystemPages filtered by the force_https column
 * @method     SystemPages findOneByAccessFrom(int $access_from) Return the first SystemPages filtered by the access_from column
 * @method     SystemPages findOneByAccessTo(int $access_to) Return the first SystemPages filtered by the access_to column
 * @method     SystemPages findOneByAccessRedirectto(string $access_redirectto) Return the first SystemPages filtered by the access_redirectto column
 * @method     SystemPages findOneByAccessNohidenavi(int $access_nohidenavi) Return the first SystemPages filtered by the access_nohidenavi column
 * @method     SystemPages findOneByAccessNeedVia(int $access_need_via) Return the first SystemPages filtered by the access_need_via column
 * @method     SystemPages findOneByAccessFromGroups(string $access_from_groups) Return the first SystemPages filtered by the access_from_groups column
 * @method     SystemPages findOneByCache(int $cache) Return the first SystemPages filtered by the cache column
 * @method     SystemPages findOneBySearchWords(string $search_words) Return the first SystemPages filtered by the search_words column
 * @method     SystemPages findOneByUnsearchable(int $unsearchable) Return the first SystemPages filtered by the unsearchable column
 * @method     SystemPages findOneByLft(int $lft) Return the first SystemPages filtered by the lft column
 * @method     SystemPages findOneByRgt(int $rgt) Return the first SystemPages filtered by the rgt column
 *
 * @method     array findById(int $id) Return SystemPages objects filtered by the id column
 * @method     array findByPid(int $pid) Return SystemPages objects filtered by the pid column
 * @method     array findByDomainId(int $domain_id) Return SystemPages objects filtered by the domain_id column
 * @method     array findByType(int $type) Return SystemPages objects filtered by the type column
 * @method     array findByTitle(string $title) Return SystemPages objects filtered by the title column
 * @method     array findByPageTitle(string $page_title) Return SystemPages objects filtered by the page_title column
 * @method     array findByUrl(string $url) Return SystemPages objects filtered by the url column
 * @method     array findByLink(string $link) Return SystemPages objects filtered by the link column
 * @method     array findByLayout(string $layout) Return SystemPages objects filtered by the layout column
 * @method     array findBySort(int $sort) Return SystemPages objects filtered by the sort column
 * @method     array findBySortMode(string $sort_mode) Return SystemPages objects filtered by the sort_mode column
 * @method     array findByTarget(string $target) Return SystemPages objects filtered by the target column
 * @method     array findByVisible(int $visible) Return SystemPages objects filtered by the visible column
 * @method     array findByAccessDenied(string $access_denied) Return SystemPages objects filtered by the access_denied column
 * @method     array findByMeta(string $meta) Return SystemPages objects filtered by the meta column
 * @method     array findByProperties(string $properties) Return SystemPages objects filtered by the properties column
 * @method     array findByCdate(int $cdate) Return SystemPages objects filtered by the cdate column
 * @method     array findByMdate(int $mdate) Return SystemPages objects filtered by the mdate column
 * @method     array findByDraftExist(int $draft_exist) Return SystemPages objects filtered by the draft_exist column
 * @method     array findByForceHttps(int $force_https) Return SystemPages objects filtered by the force_https column
 * @method     array findByAccessFrom(int $access_from) Return SystemPages objects filtered by the access_from column
 * @method     array findByAccessTo(int $access_to) Return SystemPages objects filtered by the access_to column
 * @method     array findByAccessRedirectto(string $access_redirectto) Return SystemPages objects filtered by the access_redirectto column
 * @method     array findByAccessNohidenavi(int $access_nohidenavi) Return SystemPages objects filtered by the access_nohidenavi column
 * @method     array findByAccessNeedVia(int $access_need_via) Return SystemPages objects filtered by the access_need_via column
 * @method     array findByAccessFromGroups(string $access_from_groups) Return SystemPages objects filtered by the access_from_groups column
 * @method     array findByCache(int $cache) Return SystemPages objects filtered by the cache column
 * @method     array findBySearchWords(string $search_words) Return SystemPages objects filtered by the search_words column
 * @method     array findByUnsearchable(int $unsearchable) Return SystemPages objects filtered by the unsearchable column
 * @method     array findByLft(int $lft) Return SystemPages objects filtered by the lft column
 * @method     array findByRgt(int $rgt) Return SystemPages objects filtered by the rgt column
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemPagesQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseSystemPagesQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'kryn', $modelName = 'SystemPages', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SystemPagesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     SystemPagesQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SystemPagesQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SystemPagesQuery) {
            return $criteria;
        }
        $query = new SystemPagesQuery();
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
     * @return   SystemPages|SystemPages[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SystemPagesPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   SystemPages A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PID, DOMAIN_ID, TYPE, TITLE, PAGE_TITLE, URL, LINK, LAYOUT, SORT, SORT_MODE, TARGET, VISIBLE, ACCESS_DENIED, META, PROPERTIES, CDATE, MDATE, DRAFT_EXIST, FORCE_HTTPS, ACCESS_FROM, ACCESS_TO, ACCESS_REDIRECTTO, ACCESS_NOHIDENAVI, ACCESS_NEED_VIA, ACCESS_FROM_GROUPS, CACHE, SEARCH_WORDS, UNSEARCHABLE, LFT, RGT FROM kryn_system_pages WHERE ID = :p0';
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
            $obj = new SystemPages();
            $obj->hydrate($row);
            SystemPagesPeer::addInstanceToPool($obj, (string) $key);
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
     * @return SystemPages|SystemPages[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|SystemPages[]|mixed the list of results, formatted by the current formatter
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
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SystemPagesPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SystemPagesPeer::ID, $keys, Criteria::IN);
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
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(SystemPagesPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the pid column
     *
     * Example usage:
     * <code>
     * $query->filterByPid(1234); // WHERE pid = 1234
     * $query->filterByPid(array(12, 34)); // WHERE pid IN (12, 34)
     * $query->filterByPid(array('min' => 12)); // WHERE pid > 12
     * </code>
     *
     * @param     mixed $pid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByPid($pid = null, $comparison = null)
    {
        if (is_array($pid)) {
            $useMinMax = false;
            if (isset($pid['min'])) {
                $this->addUsingAlias(SystemPagesPeer::PID, $pid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($pid['max'])) {
                $this->addUsingAlias(SystemPagesPeer::PID, $pid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::PID, $pid, $comparison);
    }

    /**
     * Filter the query on the domain_id column
     *
     * Example usage:
     * <code>
     * $query->filterByDomainId(1234); // WHERE domain_id = 1234
     * $query->filterByDomainId(array(12, 34)); // WHERE domain_id IN (12, 34)
     * $query->filterByDomainId(array('min' => 12)); // WHERE domain_id > 12
     * </code>
     *
     * @param     mixed $domainId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByDomainId($domainId = null, $comparison = null)
    {
        if (is_array($domainId)) {
            $useMinMax = false;
            if (isset($domainId['min'])) {
                $this->addUsingAlias(SystemPagesPeer::DOMAIN_ID, $domainId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($domainId['max'])) {
                $this->addUsingAlias(SystemPagesPeer::DOMAIN_ID, $domainId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::DOMAIN_ID, $domainId, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType(1234); // WHERE type = 1234
     * $query->filterByType(array(12, 34)); // WHERE type IN (12, 34)
     * $query->filterByType(array('min' => 12)); // WHERE type > 12
     * </code>
     *
     * @param     mixed $type The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (is_array($type)) {
            $useMinMax = false;
            if (isset($type['min'])) {
                $this->addUsingAlias(SystemPagesPeer::TYPE, $type['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($type['max'])) {
                $this->addUsingAlias(SystemPagesPeer::TYPE, $type['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the title column
     *
     * Example usage:
     * <code>
     * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
     * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $title The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByTitle($title = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($title)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $title)) {
                $title = str_replace('*', '%', $title);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the page_title column
     *
     * Example usage:
     * <code>
     * $query->filterByPageTitle('fooValue');   // WHERE page_title = 'fooValue'
     * $query->filterByPageTitle('%fooValue%'); // WHERE page_title LIKE '%fooValue%'
     * </code>
     *
     * @param     string $pageTitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByPageTitle($pageTitle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($pageTitle)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $pageTitle)) {
                $pageTitle = str_replace('*', '%', $pageTitle);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::PAGE_TITLE, $pageTitle, $comparison);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByUrl('%fooValue%'); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $url The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByUrl($url = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($url)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $url)) {
                $url = str_replace('*', '%', $url);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::URL, $url, $comparison);
    }

    /**
     * Filter the query on the link column
     *
     * Example usage:
     * <code>
     * $query->filterByLink('fooValue');   // WHERE link = 'fooValue'
     * $query->filterByLink('%fooValue%'); // WHERE link LIKE '%fooValue%'
     * </code>
     *
     * @param     string $link The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByLink($link = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($link)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $link)) {
                $link = str_replace('*', '%', $link);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::LINK, $link, $comparison);
    }

    /**
     * Filter the query on the layout column
     *
     * Example usage:
     * <code>
     * $query->filterByLayout('fooValue');   // WHERE layout = 'fooValue'
     * $query->filterByLayout('%fooValue%'); // WHERE layout LIKE '%fooValue%'
     * </code>
     *
     * @param     string $layout The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByLayout($layout = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($layout)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $layout)) {
                $layout = str_replace('*', '%', $layout);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::LAYOUT, $layout, $comparison);
    }

    /**
     * Filter the query on the sort column
     *
     * Example usage:
     * <code>
     * $query->filterBySort(1234); // WHERE sort = 1234
     * $query->filterBySort(array(12, 34)); // WHERE sort IN (12, 34)
     * $query->filterBySort(array('min' => 12)); // WHERE sort > 12
     * </code>
     *
     * @param     mixed $sort The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterBySort($sort = null, $comparison = null)
    {
        if (is_array($sort)) {
            $useMinMax = false;
            if (isset($sort['min'])) {
                $this->addUsingAlias(SystemPagesPeer::SORT, $sort['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sort['max'])) {
                $this->addUsingAlias(SystemPagesPeer::SORT, $sort['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::SORT, $sort, $comparison);
    }

    /**
     * Filter the query on the sort_mode column
     *
     * Example usage:
     * <code>
     * $query->filterBySortMode('fooValue');   // WHERE sort_mode = 'fooValue'
     * $query->filterBySortMode('%fooValue%'); // WHERE sort_mode LIKE '%fooValue%'
     * </code>
     *
     * @param     string $sortMode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterBySortMode($sortMode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($sortMode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $sortMode)) {
                $sortMode = str_replace('*', '%', $sortMode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::SORT_MODE, $sortMode, $comparison);
    }

    /**
     * Filter the query on the target column
     *
     * Example usage:
     * <code>
     * $query->filterByTarget('fooValue');   // WHERE target = 'fooValue'
     * $query->filterByTarget('%fooValue%'); // WHERE target LIKE '%fooValue%'
     * </code>
     *
     * @param     string $target The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByTarget($target = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($target)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $target)) {
                $target = str_replace('*', '%', $target);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::TARGET, $target, $comparison);
    }

    /**
     * Filter the query on the visible column
     *
     * Example usage:
     * <code>
     * $query->filterByVisible(1234); // WHERE visible = 1234
     * $query->filterByVisible(array(12, 34)); // WHERE visible IN (12, 34)
     * $query->filterByVisible(array('min' => 12)); // WHERE visible > 12
     * </code>
     *
     * @param     mixed $visible The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(SystemPagesPeer::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(SystemPagesPeer::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::VISIBLE, $visible, $comparison);
    }

    /**
     * Filter the query on the access_denied column
     *
     * Example usage:
     * <code>
     * $query->filterByAccessDenied('fooValue');   // WHERE access_denied = 'fooValue'
     * $query->filterByAccessDenied('%fooValue%'); // WHERE access_denied LIKE '%fooValue%'
     * </code>
     *
     * @param     string $accessDenied The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByAccessDenied($accessDenied = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($accessDenied)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $accessDenied)) {
                $accessDenied = str_replace('*', '%', $accessDenied);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::ACCESS_DENIED, $accessDenied, $comparison);
    }

    /**
     * Filter the query on the meta column
     *
     * Example usage:
     * <code>
     * $query->filterByMeta('fooValue');   // WHERE meta = 'fooValue'
     * $query->filterByMeta('%fooValue%'); // WHERE meta LIKE '%fooValue%'
     * </code>
     *
     * @param     string $meta The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByMeta($meta = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($meta)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $meta)) {
                $meta = str_replace('*', '%', $meta);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::META, $meta, $comparison);
    }

    /**
     * Filter the query on the properties column
     *
     * Example usage:
     * <code>
     * $query->filterByProperties('fooValue');   // WHERE properties = 'fooValue'
     * $query->filterByProperties('%fooValue%'); // WHERE properties LIKE '%fooValue%'
     * </code>
     *
     * @param     string $properties The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByProperties($properties = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($properties)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $properties)) {
                $properties = str_replace('*', '%', $properties);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::PROPERTIES, $properties, $comparison);
    }

    /**
     * Filter the query on the cdate column
     *
     * Example usage:
     * <code>
     * $query->filterByCdate(1234); // WHERE cdate = 1234
     * $query->filterByCdate(array(12, 34)); // WHERE cdate IN (12, 34)
     * $query->filterByCdate(array('min' => 12)); // WHERE cdate > 12
     * </code>
     *
     * @param     mixed $cdate The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByCdate($cdate = null, $comparison = null)
    {
        if (is_array($cdate)) {
            $useMinMax = false;
            if (isset($cdate['min'])) {
                $this->addUsingAlias(SystemPagesPeer::CDATE, $cdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cdate['max'])) {
                $this->addUsingAlias(SystemPagesPeer::CDATE, $cdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::CDATE, $cdate, $comparison);
    }

    /**
     * Filter the query on the mdate column
     *
     * Example usage:
     * <code>
     * $query->filterByMdate(1234); // WHERE mdate = 1234
     * $query->filterByMdate(array(12, 34)); // WHERE mdate IN (12, 34)
     * $query->filterByMdate(array('min' => 12)); // WHERE mdate > 12
     * </code>
     *
     * @param     mixed $mdate The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByMdate($mdate = null, $comparison = null)
    {
        if (is_array($mdate)) {
            $useMinMax = false;
            if (isset($mdate['min'])) {
                $this->addUsingAlias(SystemPagesPeer::MDATE, $mdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mdate['max'])) {
                $this->addUsingAlias(SystemPagesPeer::MDATE, $mdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::MDATE, $mdate, $comparison);
    }

    /**
     * Filter the query on the draft_exist column
     *
     * Example usage:
     * <code>
     * $query->filterByDraftExist(1234); // WHERE draft_exist = 1234
     * $query->filterByDraftExist(array(12, 34)); // WHERE draft_exist IN (12, 34)
     * $query->filterByDraftExist(array('min' => 12)); // WHERE draft_exist > 12
     * </code>
     *
     * @param     mixed $draftExist The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByDraftExist($draftExist = null, $comparison = null)
    {
        if (is_array($draftExist)) {
            $useMinMax = false;
            if (isset($draftExist['min'])) {
                $this->addUsingAlias(SystemPagesPeer::DRAFT_EXIST, $draftExist['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($draftExist['max'])) {
                $this->addUsingAlias(SystemPagesPeer::DRAFT_EXIST, $draftExist['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::DRAFT_EXIST, $draftExist, $comparison);
    }

    /**
     * Filter the query on the force_https column
     *
     * Example usage:
     * <code>
     * $query->filterByForceHttps(1234); // WHERE force_https = 1234
     * $query->filterByForceHttps(array(12, 34)); // WHERE force_https IN (12, 34)
     * $query->filterByForceHttps(array('min' => 12)); // WHERE force_https > 12
     * </code>
     *
     * @param     mixed $forceHttps The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByForceHttps($forceHttps = null, $comparison = null)
    {
        if (is_array($forceHttps)) {
            $useMinMax = false;
            if (isset($forceHttps['min'])) {
                $this->addUsingAlias(SystemPagesPeer::FORCE_HTTPS, $forceHttps['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($forceHttps['max'])) {
                $this->addUsingAlias(SystemPagesPeer::FORCE_HTTPS, $forceHttps['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::FORCE_HTTPS, $forceHttps, $comparison);
    }

    /**
     * Filter the query on the access_from column
     *
     * Example usage:
     * <code>
     * $query->filterByAccessFrom(1234); // WHERE access_from = 1234
     * $query->filterByAccessFrom(array(12, 34)); // WHERE access_from IN (12, 34)
     * $query->filterByAccessFrom(array('min' => 12)); // WHERE access_from > 12
     * </code>
     *
     * @param     mixed $accessFrom The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByAccessFrom($accessFrom = null, $comparison = null)
    {
        if (is_array($accessFrom)) {
            $useMinMax = false;
            if (isset($accessFrom['min'])) {
                $this->addUsingAlias(SystemPagesPeer::ACCESS_FROM, $accessFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessFrom['max'])) {
                $this->addUsingAlias(SystemPagesPeer::ACCESS_FROM, $accessFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::ACCESS_FROM, $accessFrom, $comparison);
    }

    /**
     * Filter the query on the access_to column
     *
     * Example usage:
     * <code>
     * $query->filterByAccessTo(1234); // WHERE access_to = 1234
     * $query->filterByAccessTo(array(12, 34)); // WHERE access_to IN (12, 34)
     * $query->filterByAccessTo(array('min' => 12)); // WHERE access_to > 12
     * </code>
     *
     * @param     mixed $accessTo The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByAccessTo($accessTo = null, $comparison = null)
    {
        if (is_array($accessTo)) {
            $useMinMax = false;
            if (isset($accessTo['min'])) {
                $this->addUsingAlias(SystemPagesPeer::ACCESS_TO, $accessTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessTo['max'])) {
                $this->addUsingAlias(SystemPagesPeer::ACCESS_TO, $accessTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::ACCESS_TO, $accessTo, $comparison);
    }

    /**
     * Filter the query on the access_redirectto column
     *
     * Example usage:
     * <code>
     * $query->filterByAccessRedirectto('fooValue');   // WHERE access_redirectto = 'fooValue'
     * $query->filterByAccessRedirectto('%fooValue%'); // WHERE access_redirectto LIKE '%fooValue%'
     * </code>
     *
     * @param     string $accessRedirectto The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByAccessRedirectto($accessRedirectto = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($accessRedirectto)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $accessRedirectto)) {
                $accessRedirectto = str_replace('*', '%', $accessRedirectto);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::ACCESS_REDIRECTTO, $accessRedirectto, $comparison);
    }

    /**
     * Filter the query on the access_nohidenavi column
     *
     * Example usage:
     * <code>
     * $query->filterByAccessNohidenavi(1234); // WHERE access_nohidenavi = 1234
     * $query->filterByAccessNohidenavi(array(12, 34)); // WHERE access_nohidenavi IN (12, 34)
     * $query->filterByAccessNohidenavi(array('min' => 12)); // WHERE access_nohidenavi > 12
     * </code>
     *
     * @param     mixed $accessNohidenavi The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByAccessNohidenavi($accessNohidenavi = null, $comparison = null)
    {
        if (is_array($accessNohidenavi)) {
            $useMinMax = false;
            if (isset($accessNohidenavi['min'])) {
                $this->addUsingAlias(SystemPagesPeer::ACCESS_NOHIDENAVI, $accessNohidenavi['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessNohidenavi['max'])) {
                $this->addUsingAlias(SystemPagesPeer::ACCESS_NOHIDENAVI, $accessNohidenavi['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::ACCESS_NOHIDENAVI, $accessNohidenavi, $comparison);
    }

    /**
     * Filter the query on the access_need_via column
     *
     * Example usage:
     * <code>
     * $query->filterByAccessNeedVia(1234); // WHERE access_need_via = 1234
     * $query->filterByAccessNeedVia(array(12, 34)); // WHERE access_need_via IN (12, 34)
     * $query->filterByAccessNeedVia(array('min' => 12)); // WHERE access_need_via > 12
     * </code>
     *
     * @param     mixed $accessNeedVia The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByAccessNeedVia($accessNeedVia = null, $comparison = null)
    {
        if (is_array($accessNeedVia)) {
            $useMinMax = false;
            if (isset($accessNeedVia['min'])) {
                $this->addUsingAlias(SystemPagesPeer::ACCESS_NEED_VIA, $accessNeedVia['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessNeedVia['max'])) {
                $this->addUsingAlias(SystemPagesPeer::ACCESS_NEED_VIA, $accessNeedVia['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::ACCESS_NEED_VIA, $accessNeedVia, $comparison);
    }

    /**
     * Filter the query on the access_from_groups column
     *
     * Example usage:
     * <code>
     * $query->filterByAccessFromGroups('fooValue');   // WHERE access_from_groups = 'fooValue'
     * $query->filterByAccessFromGroups('%fooValue%'); // WHERE access_from_groups LIKE '%fooValue%'
     * </code>
     *
     * @param     string $accessFromGroups The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByAccessFromGroups($accessFromGroups = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($accessFromGroups)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $accessFromGroups)) {
                $accessFromGroups = str_replace('*', '%', $accessFromGroups);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::ACCESS_FROM_GROUPS, $accessFromGroups, $comparison);
    }

    /**
     * Filter the query on the cache column
     *
     * Example usage:
     * <code>
     * $query->filterByCache(1234); // WHERE cache = 1234
     * $query->filterByCache(array(12, 34)); // WHERE cache IN (12, 34)
     * $query->filterByCache(array('min' => 12)); // WHERE cache > 12
     * </code>
     *
     * @param     mixed $cache The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByCache($cache = null, $comparison = null)
    {
        if (is_array($cache)) {
            $useMinMax = false;
            if (isset($cache['min'])) {
                $this->addUsingAlias(SystemPagesPeer::CACHE, $cache['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cache['max'])) {
                $this->addUsingAlias(SystemPagesPeer::CACHE, $cache['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::CACHE, $cache, $comparison);
    }

    /**
     * Filter the query on the search_words column
     *
     * Example usage:
     * <code>
     * $query->filterBySearchWords('fooValue');   // WHERE search_words = 'fooValue'
     * $query->filterBySearchWords('%fooValue%'); // WHERE search_words LIKE '%fooValue%'
     * </code>
     *
     * @param     string $searchWords The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterBySearchWords($searchWords = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($searchWords)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $searchWords)) {
                $searchWords = str_replace('*', '%', $searchWords);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::SEARCH_WORDS, $searchWords, $comparison);
    }

    /**
     * Filter the query on the unsearchable column
     *
     * Example usage:
     * <code>
     * $query->filterByUnsearchable(1234); // WHERE unsearchable = 1234
     * $query->filterByUnsearchable(array(12, 34)); // WHERE unsearchable IN (12, 34)
     * $query->filterByUnsearchable(array('min' => 12)); // WHERE unsearchable > 12
     * </code>
     *
     * @param     mixed $unsearchable The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByUnsearchable($unsearchable = null, $comparison = null)
    {
        if (is_array($unsearchable)) {
            $useMinMax = false;
            if (isset($unsearchable['min'])) {
                $this->addUsingAlias(SystemPagesPeer::UNSEARCHABLE, $unsearchable['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($unsearchable['max'])) {
                $this->addUsingAlias(SystemPagesPeer::UNSEARCHABLE, $unsearchable['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::UNSEARCHABLE, $unsearchable, $comparison);
    }

    /**
     * Filter the query on the lft column
     *
     * Example usage:
     * <code>
     * $query->filterByLft(1234); // WHERE lft = 1234
     * $query->filterByLft(array(12, 34)); // WHERE lft IN (12, 34)
     * $query->filterByLft(array('min' => 12)); // WHERE lft > 12
     * </code>
     *
     * @param     mixed $lft The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByLft($lft = null, $comparison = null)
    {
        if (is_array($lft)) {
            $useMinMax = false;
            if (isset($lft['min'])) {
                $this->addUsingAlias(SystemPagesPeer::LFT, $lft['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lft['max'])) {
                $this->addUsingAlias(SystemPagesPeer::LFT, $lft['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::LFT, $lft, $comparison);
    }

    /**
     * Filter the query on the rgt column
     *
     * Example usage:
     * <code>
     * $query->filterByRgt(1234); // WHERE rgt = 1234
     * $query->filterByRgt(array(12, 34)); // WHERE rgt IN (12, 34)
     * $query->filterByRgt(array('min' => 12)); // WHERE rgt > 12
     * </code>
     *
     * @param     mixed $rgt The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function filterByRgt($rgt = null, $comparison = null)
    {
        if (is_array($rgt)) {
            $useMinMax = false;
            if (isset($rgt['min'])) {
                $this->addUsingAlias(SystemPagesPeer::RGT, $rgt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($rgt['max'])) {
                $this->addUsingAlias(SystemPagesPeer::RGT, $rgt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagesPeer::RGT, $rgt, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   SystemPages $systemPages Object to remove from the list of results
     *
     * @return SystemPagesQuery The current query, for fluid interface
     */
    public function prune($systemPages = null)
    {
        if ($systemPages) {
            $this->addUsingAlias(SystemPagesPeer::ID, $systemPages->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseSystemPagesQuery
<?php


/**
 * Base class that represents a query for the 'kryn_system_page' table.
 *
 * 
 *
 * @method     SystemPageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     SystemPageQuery orderByPid($order = Criteria::ASC) Order by the pid column
 * @method     SystemPageQuery orderByDomainId($order = Criteria::ASC) Order by the domain_id column
 * @method     SystemPageQuery orderByLft($order = Criteria::ASC) Order by the lft column
 * @method     SystemPageQuery orderByRgt($order = Criteria::ASC) Order by the rgt column
 * @method     SystemPageQuery orderByLvl($order = Criteria::ASC) Order by the lvl column
 * @method     SystemPageQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     SystemPageQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     SystemPageQuery orderByPageTitle($order = Criteria::ASC) Order by the page_title column
 * @method     SystemPageQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     SystemPageQuery orderByLink($order = Criteria::ASC) Order by the link column
 * @method     SystemPageQuery orderByLayout($order = Criteria::ASC) Order by the layout column
 * @method     SystemPageQuery orderBySort($order = Criteria::ASC) Order by the sort column
 * @method     SystemPageQuery orderBySortMode($order = Criteria::ASC) Order by the sort_mode column
 * @method     SystemPageQuery orderByTarget($order = Criteria::ASC) Order by the target column
 * @method     SystemPageQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     SystemPageQuery orderByAccessDenied($order = Criteria::ASC) Order by the access_denied column
 * @method     SystemPageQuery orderByMeta($order = Criteria::ASC) Order by the meta column
 * @method     SystemPageQuery orderByProperties($order = Criteria::ASC) Order by the properties column
 * @method     SystemPageQuery orderByCdate($order = Criteria::ASC) Order by the cdate column
 * @method     SystemPageQuery orderByMdate($order = Criteria::ASC) Order by the mdate column
 * @method     SystemPageQuery orderByDraftExist($order = Criteria::ASC) Order by the draft_exist column
 * @method     SystemPageQuery orderByForceHttps($order = Criteria::ASC) Order by the force_https column
 * @method     SystemPageQuery orderByAccessFrom($order = Criteria::ASC) Order by the access_from column
 * @method     SystemPageQuery orderByAccessTo($order = Criteria::ASC) Order by the access_to column
 * @method     SystemPageQuery orderByAccessRedirectto($order = Criteria::ASC) Order by the access_redirectto column
 * @method     SystemPageQuery orderByAccessNohidenavi($order = Criteria::ASC) Order by the access_nohidenavi column
 * @method     SystemPageQuery orderByAccessNeedVia($order = Criteria::ASC) Order by the access_need_via column
 * @method     SystemPageQuery orderByAccessFromGroups($order = Criteria::ASC) Order by the access_from_groups column
 * @method     SystemPageQuery orderByCache($order = Criteria::ASC) Order by the cache column
 * @method     SystemPageQuery orderBySearchWords($order = Criteria::ASC) Order by the search_words column
 * @method     SystemPageQuery orderByUnsearchable($order = Criteria::ASC) Order by the unsearchable column
 * @method     SystemPageQuery orderByActiveVersionId($order = Criteria::ASC) Order by the active_version_id column
 *
 * @method     SystemPageQuery groupById() Group by the id column
 * @method     SystemPageQuery groupByPid() Group by the pid column
 * @method     SystemPageQuery groupByDomainId() Group by the domain_id column
 * @method     SystemPageQuery groupByLft() Group by the lft column
 * @method     SystemPageQuery groupByRgt() Group by the rgt column
 * @method     SystemPageQuery groupByLvl() Group by the lvl column
 * @method     SystemPageQuery groupByType() Group by the type column
 * @method     SystemPageQuery groupByTitle() Group by the title column
 * @method     SystemPageQuery groupByPageTitle() Group by the page_title column
 * @method     SystemPageQuery groupByUrl() Group by the url column
 * @method     SystemPageQuery groupByLink() Group by the link column
 * @method     SystemPageQuery groupByLayout() Group by the layout column
 * @method     SystemPageQuery groupBySort() Group by the sort column
 * @method     SystemPageQuery groupBySortMode() Group by the sort_mode column
 * @method     SystemPageQuery groupByTarget() Group by the target column
 * @method     SystemPageQuery groupByVisible() Group by the visible column
 * @method     SystemPageQuery groupByAccessDenied() Group by the access_denied column
 * @method     SystemPageQuery groupByMeta() Group by the meta column
 * @method     SystemPageQuery groupByProperties() Group by the properties column
 * @method     SystemPageQuery groupByCdate() Group by the cdate column
 * @method     SystemPageQuery groupByMdate() Group by the mdate column
 * @method     SystemPageQuery groupByDraftExist() Group by the draft_exist column
 * @method     SystemPageQuery groupByForceHttps() Group by the force_https column
 * @method     SystemPageQuery groupByAccessFrom() Group by the access_from column
 * @method     SystemPageQuery groupByAccessTo() Group by the access_to column
 * @method     SystemPageQuery groupByAccessRedirectto() Group by the access_redirectto column
 * @method     SystemPageQuery groupByAccessNohidenavi() Group by the access_nohidenavi column
 * @method     SystemPageQuery groupByAccessNeedVia() Group by the access_need_via column
 * @method     SystemPageQuery groupByAccessFromGroups() Group by the access_from_groups column
 * @method     SystemPageQuery groupByCache() Group by the cache column
 * @method     SystemPageQuery groupBySearchWords() Group by the search_words column
 * @method     SystemPageQuery groupByUnsearchable() Group by the unsearchable column
 * @method     SystemPageQuery groupByActiveVersionId() Group by the active_version_id column
 *
 * @method     SystemPageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SystemPageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SystemPageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SystemPageQuery leftJoinSystemDomain($relationAlias = null) Adds a LEFT JOIN clause to the query using the SystemDomain relation
 * @method     SystemPageQuery rightJoinSystemDomain($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SystemDomain relation
 * @method     SystemPageQuery innerJoinSystemDomain($relationAlias = null) Adds a INNER JOIN clause to the query using the SystemDomain relation
 *
 * @method     SystemPageQuery leftJoinSystemUrlalias($relationAlias = null) Adds a LEFT JOIN clause to the query using the SystemUrlalias relation
 * @method     SystemPageQuery rightJoinSystemUrlalias($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SystemUrlalias relation
 * @method     SystemPageQuery innerJoinSystemUrlalias($relationAlias = null) Adds a INNER JOIN clause to the query using the SystemUrlalias relation
 *
 * @method     SystemPage findOne(PropelPDO $con = null) Return the first SystemPage matching the query
 * @method     SystemPage findOneOrCreate(PropelPDO $con = null) Return the first SystemPage matching the query, or a new SystemPage object populated from the query conditions when no match is found
 *
 * @method     SystemPage findOneById(int $id) Return the first SystemPage filtered by the id column
 * @method     SystemPage findOneByPid(int $pid) Return the first SystemPage filtered by the pid column
 * @method     SystemPage findOneByDomainId(int $domain_id) Return the first SystemPage filtered by the domain_id column
 * @method     SystemPage findOneByLft(int $lft) Return the first SystemPage filtered by the lft column
 * @method     SystemPage findOneByRgt(int $rgt) Return the first SystemPage filtered by the rgt column
 * @method     SystemPage findOneByLvl(int $lvl) Return the first SystemPage filtered by the lvl column
 * @method     SystemPage findOneByType(int $type) Return the first SystemPage filtered by the type column
 * @method     SystemPage findOneByTitle(string $title) Return the first SystemPage filtered by the title column
 * @method     SystemPage findOneByPageTitle(string $page_title) Return the first SystemPage filtered by the page_title column
 * @method     SystemPage findOneByUrl(string $url) Return the first SystemPage filtered by the url column
 * @method     SystemPage findOneByLink(string $link) Return the first SystemPage filtered by the link column
 * @method     SystemPage findOneByLayout(string $layout) Return the first SystemPage filtered by the layout column
 * @method     SystemPage findOneBySort(int $sort) Return the first SystemPage filtered by the sort column
 * @method     SystemPage findOneBySortMode(string $sort_mode) Return the first SystemPage filtered by the sort_mode column
 * @method     SystemPage findOneByTarget(string $target) Return the first SystemPage filtered by the target column
 * @method     SystemPage findOneByVisible(int $visible) Return the first SystemPage filtered by the visible column
 * @method     SystemPage findOneByAccessDenied(string $access_denied) Return the first SystemPage filtered by the access_denied column
 * @method     SystemPage findOneByMeta(string $meta) Return the first SystemPage filtered by the meta column
 * @method     SystemPage findOneByProperties(string $properties) Return the first SystemPage filtered by the properties column
 * @method     SystemPage findOneByCdate(int $cdate) Return the first SystemPage filtered by the cdate column
 * @method     SystemPage findOneByMdate(int $mdate) Return the first SystemPage filtered by the mdate column
 * @method     SystemPage findOneByDraftExist(int $draft_exist) Return the first SystemPage filtered by the draft_exist column
 * @method     SystemPage findOneByForceHttps(int $force_https) Return the first SystemPage filtered by the force_https column
 * @method     SystemPage findOneByAccessFrom(int $access_from) Return the first SystemPage filtered by the access_from column
 * @method     SystemPage findOneByAccessTo(int $access_to) Return the first SystemPage filtered by the access_to column
 * @method     SystemPage findOneByAccessRedirectto(string $access_redirectto) Return the first SystemPage filtered by the access_redirectto column
 * @method     SystemPage findOneByAccessNohidenavi(int $access_nohidenavi) Return the first SystemPage filtered by the access_nohidenavi column
 * @method     SystemPage findOneByAccessNeedVia(int $access_need_via) Return the first SystemPage filtered by the access_need_via column
 * @method     SystemPage findOneByAccessFromGroups(string $access_from_groups) Return the first SystemPage filtered by the access_from_groups column
 * @method     SystemPage findOneByCache(int $cache) Return the first SystemPage filtered by the cache column
 * @method     SystemPage findOneBySearchWords(string $search_words) Return the first SystemPage filtered by the search_words column
 * @method     SystemPage findOneByUnsearchable(int $unsearchable) Return the first SystemPage filtered by the unsearchable column
 * @method     SystemPage findOneByActiveVersionId(int $active_version_id) Return the first SystemPage filtered by the active_version_id column
 *
 * @method     array findById(int $id) Return SystemPage objects filtered by the id column
 * @method     array findByPid(int $pid) Return SystemPage objects filtered by the pid column
 * @method     array findByDomainId(int $domain_id) Return SystemPage objects filtered by the domain_id column
 * @method     array findByLft(int $lft) Return SystemPage objects filtered by the lft column
 * @method     array findByRgt(int $rgt) Return SystemPage objects filtered by the rgt column
 * @method     array findByLvl(int $lvl) Return SystemPage objects filtered by the lvl column
 * @method     array findByType(int $type) Return SystemPage objects filtered by the type column
 * @method     array findByTitle(string $title) Return SystemPage objects filtered by the title column
 * @method     array findByPageTitle(string $page_title) Return SystemPage objects filtered by the page_title column
 * @method     array findByUrl(string $url) Return SystemPage objects filtered by the url column
 * @method     array findByLink(string $link) Return SystemPage objects filtered by the link column
 * @method     array findByLayout(string $layout) Return SystemPage objects filtered by the layout column
 * @method     array findBySort(int $sort) Return SystemPage objects filtered by the sort column
 * @method     array findBySortMode(string $sort_mode) Return SystemPage objects filtered by the sort_mode column
 * @method     array findByTarget(string $target) Return SystemPage objects filtered by the target column
 * @method     array findByVisible(int $visible) Return SystemPage objects filtered by the visible column
 * @method     array findByAccessDenied(string $access_denied) Return SystemPage objects filtered by the access_denied column
 * @method     array findByMeta(string $meta) Return SystemPage objects filtered by the meta column
 * @method     array findByProperties(string $properties) Return SystemPage objects filtered by the properties column
 * @method     array findByCdate(int $cdate) Return SystemPage objects filtered by the cdate column
 * @method     array findByMdate(int $mdate) Return SystemPage objects filtered by the mdate column
 * @method     array findByDraftExist(int $draft_exist) Return SystemPage objects filtered by the draft_exist column
 * @method     array findByForceHttps(int $force_https) Return SystemPage objects filtered by the force_https column
 * @method     array findByAccessFrom(int $access_from) Return SystemPage objects filtered by the access_from column
 * @method     array findByAccessTo(int $access_to) Return SystemPage objects filtered by the access_to column
 * @method     array findByAccessRedirectto(string $access_redirectto) Return SystemPage objects filtered by the access_redirectto column
 * @method     array findByAccessNohidenavi(int $access_nohidenavi) Return SystemPage objects filtered by the access_nohidenavi column
 * @method     array findByAccessNeedVia(int $access_need_via) Return SystemPage objects filtered by the access_need_via column
 * @method     array findByAccessFromGroups(string $access_from_groups) Return SystemPage objects filtered by the access_from_groups column
 * @method     array findByCache(int $cache) Return SystemPage objects filtered by the cache column
 * @method     array findBySearchWords(string $search_words) Return SystemPage objects filtered by the search_words column
 * @method     array findByUnsearchable(int $unsearchable) Return SystemPage objects filtered by the unsearchable column
 * @method     array findByActiveVersionId(int $active_version_id) Return SystemPage objects filtered by the active_version_id column
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemPageQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseSystemPageQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'kryn', $modelName = 'SystemPage', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SystemPageQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     SystemPageQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SystemPageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SystemPageQuery) {
            return $criteria;
        }
        $query = new SystemPageQuery();
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
     * @return   SystemPage|SystemPage[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SystemPagePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SystemPagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   SystemPage A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PID, DOMAIN_ID, LFT, RGT, LVL, TYPE, TITLE, PAGE_TITLE, URL, LINK, LAYOUT, SORT, SORT_MODE, TARGET, VISIBLE, ACCESS_DENIED, META, PROPERTIES, CDATE, MDATE, DRAFT_EXIST, FORCE_HTTPS, ACCESS_FROM, ACCESS_TO, ACCESS_REDIRECTTO, ACCESS_NOHIDENAVI, ACCESS_NEED_VIA, ACCESS_FROM_GROUPS, CACHE, SEARCH_WORDS, UNSEARCHABLE, ACTIVE_VERSION_ID FROM kryn_system_page WHERE ID = :p0';
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
            $obj = new SystemPage();
            $obj->hydrate($row);
            SystemPagePeer::addInstanceToPool($obj, (string) $key);
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
     * @return SystemPage|SystemPage[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|SystemPage[]|mixed the list of results, formatted by the current formatter
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SystemPagePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SystemPagePeer::ID, $keys, Criteria::IN);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(SystemPagePeer::ID, $id, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByPid($pid = null, $comparison = null)
    {
        if (is_array($pid)) {
            $useMinMax = false;
            if (isset($pid['min'])) {
                $this->addUsingAlias(SystemPagePeer::PID, $pid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($pid['max'])) {
                $this->addUsingAlias(SystemPagePeer::PID, $pid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::PID, $pid, $comparison);
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
     * @see       filterBySystemDomain()
     *
     * @param     mixed $domainId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByDomainId($domainId = null, $comparison = null)
    {
        if (is_array($domainId)) {
            $useMinMax = false;
            if (isset($domainId['min'])) {
                $this->addUsingAlias(SystemPagePeer::DOMAIN_ID, $domainId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($domainId['max'])) {
                $this->addUsingAlias(SystemPagePeer::DOMAIN_ID, $domainId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::DOMAIN_ID, $domainId, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByLft($lft = null, $comparison = null)
    {
        if (is_array($lft)) {
            $useMinMax = false;
            if (isset($lft['min'])) {
                $this->addUsingAlias(SystemPagePeer::LFT, $lft['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lft['max'])) {
                $this->addUsingAlias(SystemPagePeer::LFT, $lft['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::LFT, $lft, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByRgt($rgt = null, $comparison = null)
    {
        if (is_array($rgt)) {
            $useMinMax = false;
            if (isset($rgt['min'])) {
                $this->addUsingAlias(SystemPagePeer::RGT, $rgt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($rgt['max'])) {
                $this->addUsingAlias(SystemPagePeer::RGT, $rgt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::RGT, $rgt, $comparison);
    }

    /**
     * Filter the query on the lvl column
     *
     * Example usage:
     * <code>
     * $query->filterByLvl(1234); // WHERE lvl = 1234
     * $query->filterByLvl(array(12, 34)); // WHERE lvl IN (12, 34)
     * $query->filterByLvl(array('min' => 12)); // WHERE lvl > 12
     * </code>
     *
     * @param     mixed $lvl The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByLvl($lvl = null, $comparison = null)
    {
        if (is_array($lvl)) {
            $useMinMax = false;
            if (isset($lvl['min'])) {
                $this->addUsingAlias(SystemPagePeer::LVL, $lvl['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lvl['max'])) {
                $this->addUsingAlias(SystemPagePeer::LVL, $lvl['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::LVL, $lvl, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (is_array($type)) {
            $useMinMax = false;
            if (isset($type['min'])) {
                $this->addUsingAlias(SystemPagePeer::TYPE, $type['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($type['max'])) {
                $this->addUsingAlias(SystemPagePeer::TYPE, $type['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::TYPE, $type, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::TITLE, $title, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::PAGE_TITLE, $pageTitle, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::URL, $url, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::LINK, $link, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::LAYOUT, $layout, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterBySort($sort = null, $comparison = null)
    {
        if (is_array($sort)) {
            $useMinMax = false;
            if (isset($sort['min'])) {
                $this->addUsingAlias(SystemPagePeer::SORT, $sort['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sort['max'])) {
                $this->addUsingAlias(SystemPagePeer::SORT, $sort['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::SORT, $sort, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::SORT_MODE, $sortMode, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::TARGET, $target, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(SystemPagePeer::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(SystemPagePeer::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::VISIBLE, $visible, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::ACCESS_DENIED, $accessDenied, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::META, $meta, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::PROPERTIES, $properties, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByCdate($cdate = null, $comparison = null)
    {
        if (is_array($cdate)) {
            $useMinMax = false;
            if (isset($cdate['min'])) {
                $this->addUsingAlias(SystemPagePeer::CDATE, $cdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cdate['max'])) {
                $this->addUsingAlias(SystemPagePeer::CDATE, $cdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::CDATE, $cdate, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByMdate($mdate = null, $comparison = null)
    {
        if (is_array($mdate)) {
            $useMinMax = false;
            if (isset($mdate['min'])) {
                $this->addUsingAlias(SystemPagePeer::MDATE, $mdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mdate['max'])) {
                $this->addUsingAlias(SystemPagePeer::MDATE, $mdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::MDATE, $mdate, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByDraftExist($draftExist = null, $comparison = null)
    {
        if (is_array($draftExist)) {
            $useMinMax = false;
            if (isset($draftExist['min'])) {
                $this->addUsingAlias(SystemPagePeer::DRAFT_EXIST, $draftExist['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($draftExist['max'])) {
                $this->addUsingAlias(SystemPagePeer::DRAFT_EXIST, $draftExist['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::DRAFT_EXIST, $draftExist, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByForceHttps($forceHttps = null, $comparison = null)
    {
        if (is_array($forceHttps)) {
            $useMinMax = false;
            if (isset($forceHttps['min'])) {
                $this->addUsingAlias(SystemPagePeer::FORCE_HTTPS, $forceHttps['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($forceHttps['max'])) {
                $this->addUsingAlias(SystemPagePeer::FORCE_HTTPS, $forceHttps['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::FORCE_HTTPS, $forceHttps, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByAccessFrom($accessFrom = null, $comparison = null)
    {
        if (is_array($accessFrom)) {
            $useMinMax = false;
            if (isset($accessFrom['min'])) {
                $this->addUsingAlias(SystemPagePeer::ACCESS_FROM, $accessFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessFrom['max'])) {
                $this->addUsingAlias(SystemPagePeer::ACCESS_FROM, $accessFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::ACCESS_FROM, $accessFrom, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByAccessTo($accessTo = null, $comparison = null)
    {
        if (is_array($accessTo)) {
            $useMinMax = false;
            if (isset($accessTo['min'])) {
                $this->addUsingAlias(SystemPagePeer::ACCESS_TO, $accessTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessTo['max'])) {
                $this->addUsingAlias(SystemPagePeer::ACCESS_TO, $accessTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::ACCESS_TO, $accessTo, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::ACCESS_REDIRECTTO, $accessRedirectto, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByAccessNohidenavi($accessNohidenavi = null, $comparison = null)
    {
        if (is_array($accessNohidenavi)) {
            $useMinMax = false;
            if (isset($accessNohidenavi['min'])) {
                $this->addUsingAlias(SystemPagePeer::ACCESS_NOHIDENAVI, $accessNohidenavi['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessNohidenavi['max'])) {
                $this->addUsingAlias(SystemPagePeer::ACCESS_NOHIDENAVI, $accessNohidenavi['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::ACCESS_NOHIDENAVI, $accessNohidenavi, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByAccessNeedVia($accessNeedVia = null, $comparison = null)
    {
        if (is_array($accessNeedVia)) {
            $useMinMax = false;
            if (isset($accessNeedVia['min'])) {
                $this->addUsingAlias(SystemPagePeer::ACCESS_NEED_VIA, $accessNeedVia['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessNeedVia['max'])) {
                $this->addUsingAlias(SystemPagePeer::ACCESS_NEED_VIA, $accessNeedVia['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::ACCESS_NEED_VIA, $accessNeedVia, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::ACCESS_FROM_GROUPS, $accessFromGroups, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByCache($cache = null, $comparison = null)
    {
        if (is_array($cache)) {
            $useMinMax = false;
            if (isset($cache['min'])) {
                $this->addUsingAlias(SystemPagePeer::CACHE, $cache['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cache['max'])) {
                $this->addUsingAlias(SystemPagePeer::CACHE, $cache['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::CACHE, $cache, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemPagePeer::SEARCH_WORDS, $searchWords, $comparison);
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
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByUnsearchable($unsearchable = null, $comparison = null)
    {
        if (is_array($unsearchable)) {
            $useMinMax = false;
            if (isset($unsearchable['min'])) {
                $this->addUsingAlias(SystemPagePeer::UNSEARCHABLE, $unsearchable['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($unsearchable['max'])) {
                $this->addUsingAlias(SystemPagePeer::UNSEARCHABLE, $unsearchable['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::UNSEARCHABLE, $unsearchable, $comparison);
    }

    /**
     * Filter the query on the active_version_id column
     *
     * Example usage:
     * <code>
     * $query->filterByActiveVersionId(1234); // WHERE active_version_id = 1234
     * $query->filterByActiveVersionId(array(12, 34)); // WHERE active_version_id IN (12, 34)
     * $query->filterByActiveVersionId(array('min' => 12)); // WHERE active_version_id > 12
     * </code>
     *
     * @param     mixed $activeVersionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function filterByActiveVersionId($activeVersionId = null, $comparison = null)
    {
        if (is_array($activeVersionId)) {
            $useMinMax = false;
            if (isset($activeVersionId['min'])) {
                $this->addUsingAlias(SystemPagePeer::ACTIVE_VERSION_ID, $activeVersionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($activeVersionId['max'])) {
                $this->addUsingAlias(SystemPagePeer::ACTIVE_VERSION_ID, $activeVersionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemPagePeer::ACTIVE_VERSION_ID, $activeVersionId, $comparison);
    }

    /**
     * Filter the query by a related SystemDomain object
     *
     * @param   SystemDomain|PropelObjectCollection $systemDomain The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   SystemPageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterBySystemDomain($systemDomain, $comparison = null)
    {
        if ($systemDomain instanceof SystemDomain) {
            return $this
                ->addUsingAlias(SystemPagePeer::DOMAIN_ID, $systemDomain->getId(), $comparison);
        } elseif ($systemDomain instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SystemPagePeer::DOMAIN_ID, $systemDomain->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySystemDomain() only accepts arguments of type SystemDomain or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SystemDomain relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function joinSystemDomain($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SystemDomain');

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
            $this->addJoinObject($join, 'SystemDomain');
        }

        return $this;
    }

    /**
     * Use the SystemDomain relation SystemDomain object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   SystemDomainQuery A secondary query class using the current class as primary query
     */
    public function useSystemDomainQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSystemDomain($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SystemDomain', 'SystemDomainQuery');
    }

    /**
     * Filter the query by a related SystemUrlalias object
     *
     * @param   SystemUrlalias|PropelObjectCollection $systemUrlalias  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   SystemPageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterBySystemUrlalias($systemUrlalias, $comparison = null)
    {
        if ($systemUrlalias instanceof SystemUrlalias) {
            return $this
                ->addUsingAlias(SystemPagePeer::ID, $systemUrlalias->getToPageId(), $comparison);
        } elseif ($systemUrlalias instanceof PropelObjectCollection) {
            return $this
                ->useSystemUrlaliasQuery()
                ->filterByPrimaryKeys($systemUrlalias->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySystemUrlalias() only accepts arguments of type SystemUrlalias or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SystemUrlalias relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function joinSystemUrlalias($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SystemUrlalias');

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
            $this->addJoinObject($join, 'SystemUrlalias');
        }

        return $this;
    }

    /**
     * Use the SystemUrlalias relation SystemUrlalias object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   SystemUrlaliasQuery A secondary query class using the current class as primary query
     */
    public function useSystemUrlaliasQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSystemUrlalias($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SystemUrlalias', 'SystemUrlaliasQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   SystemPage $systemPage Object to remove from the list of results
     *
     * @return SystemPageQuery The current query, for fluid interface
     */
    public function prune($systemPage = null)
    {
        if ($systemPage) {
            $this->addUsingAlias(SystemPagePeer::ID, $systemPage->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

	// nested_set behavior
	
	/**
	 * Filter the query to restrict the result to root objects
	 *
	 * @return    SystemPageQuery The current query, for fluid interface
	 */
	public function treeRoots()
	{
	    return $this->addUsingAlias(SystemPagePeer::LEFT_COL, 1, Criteria::EQUAL);
	}
	
	/**
	 * Returns the objects in a certain tree, from the tree scope
	 *
	 * @param     int $scope		Scope to determine which objects node to return
	 *
	 * @return    SystemPageQuery The current query, for fluid interface
	 */
	public function inTree($scope = null)
	{
	    return $this->addUsingAlias(SystemPagePeer::SCOPE_COL, $scope, Criteria::EQUAL);
	}
	
	/**
	 * Filter the query to restrict the result to descendants of an object
	 *
	 * @param     SystemPage $systemPage The object to use for descendant search
	 *
	 * @return    SystemPageQuery The current query, for fluid interface
	 */
	public function descendantsOf($systemPage)
	{
	    return $this
	        ->inTree($systemPage->getScopeValue())
	        ->addUsingAlias(SystemPagePeer::LEFT_COL, $systemPage->getLeftValue(), Criteria::GREATER_THAN)
	        ->addUsingAlias(SystemPagePeer::LEFT_COL, $systemPage->getRightValue(), Criteria::LESS_THAN);
	}
	
	/**
	 * Filter the query to restrict the result to the branch of an object.
	 * Same as descendantsOf(), except that it includes the object passed as parameter in the result
	 *
	 * @param     SystemPage $systemPage The object to use for branch search
	 *
	 * @return    SystemPageQuery The current query, for fluid interface
	 */
	public function branchOf($systemPage)
	{
	    return $this
	        ->inTree($systemPage->getScopeValue())
	        ->addUsingAlias(SystemPagePeer::LEFT_COL, $systemPage->getLeftValue(), Criteria::GREATER_EQUAL)
	        ->addUsingAlias(SystemPagePeer::LEFT_COL, $systemPage->getRightValue(), Criteria::LESS_EQUAL);
	}
	
	/**
	 * Filter the query to restrict the result to children of an object
	 *
	 * @param     SystemPage $systemPage The object to use for child search
	 *
	 * @return    SystemPageQuery The current query, for fluid interface
	 */
	public function childrenOf($systemPage)
	{
	    return $this
	        ->descendantsOf($systemPage)
	        ->addUsingAlias(SystemPagePeer::LEVEL_COL, $systemPage->getLevel() + 1, Criteria::EQUAL);
	}
	
	/**
	 * Filter the query to restrict the result to siblings of an object.
	 * The result does not include the object passed as parameter.
	 *
	 * @param     SystemPage $systemPage The object to use for sibling search
	 * @param      PropelPDO $con Connection to use.
	 *
	 * @return    SystemPageQuery The current query, for fluid interface
	 */
	public function siblingsOf($systemPage, PropelPDO $con = null)
	{
	    if ($systemPage->isRoot()) {
	        return $this->
	            add(SystemPagePeer::LEVEL_COL, '1<>1', Criteria::CUSTOM);
	    } else {
	        return $this
	            ->childrenOf($systemPage->getParent($con))
	            ->prune($systemPage);
	    }
	}
	
	/**
	 * Filter the query to restrict the result to ancestors of an object
	 *
	 * @param     SystemPage $systemPage The object to use for ancestors search
	 *
	 * @return    SystemPageQuery The current query, for fluid interface
	 */
	public function ancestorsOf($systemPage)
	{
	    return $this
	        ->inTree($systemPage->getScopeValue())
	        ->addUsingAlias(SystemPagePeer::LEFT_COL, $systemPage->getLeftValue(), Criteria::LESS_THAN)
	        ->addUsingAlias(SystemPagePeer::RIGHT_COL, $systemPage->getRightValue(), Criteria::GREATER_THAN);
	}
	
	/**
	 * Filter the query to restrict the result to roots of an object.
	 * Same as ancestorsOf(), except that it includes the object passed as parameter in the result
	 *
	 * @param     SystemPage $systemPage The object to use for roots search
	 *
	 * @return    SystemPageQuery The current query, for fluid interface
	 */
	public function rootsOf($systemPage)
	{
	    return $this
	        ->inTree($systemPage->getScopeValue())
	        ->addUsingAlias(SystemPagePeer::LEFT_COL, $systemPage->getLeftValue(), Criteria::LESS_EQUAL)
	        ->addUsingAlias(SystemPagePeer::RIGHT_COL, $systemPage->getRightValue(), Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Order the result by branch, i.e. natural tree order
	 *
	 * @param     bool $reverse if true, reverses the order
	 *
	 * @return    SystemPageQuery The current query, for fluid interface
	 */
	public function orderByBranch($reverse = false)
	{
	    if ($reverse) {
	        return $this
	            ->addDescendingOrderByColumn(SystemPagePeer::LEFT_COL);
	    } else {
	        return $this
	            ->addAscendingOrderByColumn(SystemPagePeer::LEFT_COL);
	    }
	}
	
	/**
	 * Order the result by level, the closer to the root first
	 *
	 * @param     bool $reverse if true, reverses the order
	 *
	 * @return    SystemPageQuery The current query, for fluid interface
	 */
	public function orderByLevel($reverse = false)
	{
	    if ($reverse) {
	        return $this
	            ->addAscendingOrderByColumn(SystemPagePeer::RIGHT_COL);
	    } else {
	        return $this
	            ->addDescendingOrderByColumn(SystemPagePeer::RIGHT_COL);
	    }
	}
	
	/**
	 * Returns a root node for the tree
	 *
	 * @param      int $scope		Scope to determine which root node to return
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     SystemPage The tree root object
	 */
	public function findRoot($scope = null, $con = null)
	{
	    return $this
	        ->addUsingAlias(SystemPagePeer::LEFT_COL, 1, Criteria::EQUAL)
	        ->inTree($scope)
	        ->findOne($con);
	}
	
	/**
	 * Returns the root objects for all trees.
	 *
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return    mixed the list of results, formatted by the current formatter
	 */
	public function findRoots($con = null)
	{
	    return $this
	        ->treeRoots()
	        ->find($con);
	}
	
	/**
	 * Returns a tree of objects
	 *
	 * @param      int $scope		Scope to determine which tree node to return
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     mixed the list of results, formatted by the current formatter
	 */
	public function findTree($scope = null, $con = null)
	{
	    return $this
	        ->inTree($scope)
	        ->orderByBranch()
	        ->find($con);
	}

} // BaseSystemPageQuery
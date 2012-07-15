<?php


/**
 * Base class that represents a query for the 'kryn_system_page' table.
 *
 * 
 *
 * @method     PageQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     PageQuery orderByParentId($order = Criteria::ASC) Order by the parent_id column
 * @method     PageQuery orderByDomainId($order = Criteria::ASC) Order by the domain_id column
 * @method     PageQuery orderByLft($order = Criteria::ASC) Order by the lft column
 * @method     PageQuery orderByRgt($order = Criteria::ASC) Order by the rgt column
 * @method     PageQuery orderByLvl($order = Criteria::ASC) Order by the lvl column
 * @method     PageQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     PageQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     PageQuery orderByPageTitle($order = Criteria::ASC) Order by the page_title column
 * @method     PageQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     PageQuery orderByFullUrl($order = Criteria::ASC) Order by the full_url column
 * @method     PageQuery orderByLink($order = Criteria::ASC) Order by the link column
 * @method     PageQuery orderByLayout($order = Criteria::ASC) Order by the layout column
 * @method     PageQuery orderBySort($order = Criteria::ASC) Order by the sort column
 * @method     PageQuery orderBySortMode($order = Criteria::ASC) Order by the sort_mode column
 * @method     PageQuery orderByTarget($order = Criteria::ASC) Order by the target column
 * @method     PageQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 * @method     PageQuery orderByAccessDenied($order = Criteria::ASC) Order by the access_denied column
 * @method     PageQuery orderByMeta($order = Criteria::ASC) Order by the meta column
 * @method     PageQuery orderByProperties($order = Criteria::ASC) Order by the properties column
 * @method     PageQuery orderByCdate($order = Criteria::ASC) Order by the cdate column
 * @method     PageQuery orderByMdate($order = Criteria::ASC) Order by the mdate column
 * @method     PageQuery orderByDraftExist($order = Criteria::ASC) Order by the draft_exist column
 * @method     PageQuery orderByForceHttps($order = Criteria::ASC) Order by the force_https column
 * @method     PageQuery orderByAccessFrom($order = Criteria::ASC) Order by the access_from column
 * @method     PageQuery orderByAccessTo($order = Criteria::ASC) Order by the access_to column
 * @method     PageQuery orderByAccessRedirectto($order = Criteria::ASC) Order by the access_redirectto column
 * @method     PageQuery orderByAccessNohidenavi($order = Criteria::ASC) Order by the access_nohidenavi column
 * @method     PageQuery orderByAccessNeedVia($order = Criteria::ASC) Order by the access_need_via column
 * @method     PageQuery orderByAccessFromGroups($order = Criteria::ASC) Order by the access_from_groups column
 * @method     PageQuery orderByCache($order = Criteria::ASC) Order by the cache column
 * @method     PageQuery orderBySearchWords($order = Criteria::ASC) Order by the search_words column
 * @method     PageQuery orderByUnsearchable($order = Criteria::ASC) Order by the unsearchable column
 * @method     PageQuery orderByActiveVersionId($order = Criteria::ASC) Order by the active_version_id column
 *
 * @method     PageQuery groupById() Group by the id column
 * @method     PageQuery groupByParentId() Group by the parent_id column
 * @method     PageQuery groupByDomainId() Group by the domain_id column
 * @method     PageQuery groupByLft() Group by the lft column
 * @method     PageQuery groupByRgt() Group by the rgt column
 * @method     PageQuery groupByLvl() Group by the lvl column
 * @method     PageQuery groupByType() Group by the type column
 * @method     PageQuery groupByTitle() Group by the title column
 * @method     PageQuery groupByPageTitle() Group by the page_title column
 * @method     PageQuery groupByUrl() Group by the url column
 * @method     PageQuery groupByFullUrl() Group by the full_url column
 * @method     PageQuery groupByLink() Group by the link column
 * @method     PageQuery groupByLayout() Group by the layout column
 * @method     PageQuery groupBySort() Group by the sort column
 * @method     PageQuery groupBySortMode() Group by the sort_mode column
 * @method     PageQuery groupByTarget() Group by the target column
 * @method     PageQuery groupByVisible() Group by the visible column
 * @method     PageQuery groupByAccessDenied() Group by the access_denied column
 * @method     PageQuery groupByMeta() Group by the meta column
 * @method     PageQuery groupByProperties() Group by the properties column
 * @method     PageQuery groupByCdate() Group by the cdate column
 * @method     PageQuery groupByMdate() Group by the mdate column
 * @method     PageQuery groupByDraftExist() Group by the draft_exist column
 * @method     PageQuery groupByForceHttps() Group by the force_https column
 * @method     PageQuery groupByAccessFrom() Group by the access_from column
 * @method     PageQuery groupByAccessTo() Group by the access_to column
 * @method     PageQuery groupByAccessRedirectto() Group by the access_redirectto column
 * @method     PageQuery groupByAccessNohidenavi() Group by the access_nohidenavi column
 * @method     PageQuery groupByAccessNeedVia() Group by the access_need_via column
 * @method     PageQuery groupByAccessFromGroups() Group by the access_from_groups column
 * @method     PageQuery groupByCache() Group by the cache column
 * @method     PageQuery groupBySearchWords() Group by the search_words column
 * @method     PageQuery groupByUnsearchable() Group by the unsearchable column
 * @method     PageQuery groupByActiveVersionId() Group by the active_version_id column
 *
 * @method     PageQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     PageQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     PageQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     PageQuery leftJoinDomain($relationAlias = null) Adds a LEFT JOIN clause to the query using the Domain relation
 * @method     PageQuery rightJoinDomain($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Domain relation
 * @method     PageQuery innerJoinDomain($relationAlias = null) Adds a INNER JOIN clause to the query using the Domain relation
 *
 * @method     PageQuery leftJoinPageRelatedByParentId($relationAlias = null) Adds a LEFT JOIN clause to the query using the PageRelatedByParentId relation
 * @method     PageQuery rightJoinPageRelatedByParentId($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PageRelatedByParentId relation
 * @method     PageQuery innerJoinPageRelatedByParentId($relationAlias = null) Adds a INNER JOIN clause to the query using the PageRelatedByParentId relation
 *
 * @method     PageQuery leftJoinPageContent($relationAlias = null) Adds a LEFT JOIN clause to the query using the PageContent relation
 * @method     PageQuery rightJoinPageContent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PageContent relation
 * @method     PageQuery innerJoinPageContent($relationAlias = null) Adds a INNER JOIN clause to the query using the PageContent relation
 *
 * @method     PageQuery leftJoinPageRelatedById($relationAlias = null) Adds a LEFT JOIN clause to the query using the PageRelatedById relation
 * @method     PageQuery rightJoinPageRelatedById($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PageRelatedById relation
 * @method     PageQuery innerJoinPageRelatedById($relationAlias = null) Adds a INNER JOIN clause to the query using the PageRelatedById relation
 *
 * @method     PageQuery leftJoinUrlalias($relationAlias = null) Adds a LEFT JOIN clause to the query using the Urlalias relation
 * @method     PageQuery rightJoinUrlalias($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Urlalias relation
 * @method     PageQuery innerJoinUrlalias($relationAlias = null) Adds a INNER JOIN clause to the query using the Urlalias relation
 *
 * @method     Page findOne(PropelPDO $con = null) Return the first Page matching the query
 * @method     Page findOneOrCreate(PropelPDO $con = null) Return the first Page matching the query, or a new Page object populated from the query conditions when no match is found
 *
 * @method     Page findOneById(int $id) Return the first Page filtered by the id column
 * @method     Page findOneByParentId(int $parent_id) Return the first Page filtered by the parent_id column
 * @method     Page findOneByDomainId(int $domain_id) Return the first Page filtered by the domain_id column
 * @method     Page findOneByLft(int $lft) Return the first Page filtered by the lft column
 * @method     Page findOneByRgt(int $rgt) Return the first Page filtered by the rgt column
 * @method     Page findOneByLvl(int $lvl) Return the first Page filtered by the lvl column
 * @method     Page findOneByType(int $type) Return the first Page filtered by the type column
 * @method     Page findOneByTitle(string $title) Return the first Page filtered by the title column
 * @method     Page findOneByPageTitle(string $page_title) Return the first Page filtered by the page_title column
 * @method     Page findOneByUrl(string $url) Return the first Page filtered by the url column
 * @method     Page findOneByFullUrl(string $full_url) Return the first Page filtered by the full_url column
 * @method     Page findOneByLink(string $link) Return the first Page filtered by the link column
 * @method     Page findOneByLayout(string $layout) Return the first Page filtered by the layout column
 * @method     Page findOneBySort(int $sort) Return the first Page filtered by the sort column
 * @method     Page findOneBySortMode(string $sort_mode) Return the first Page filtered by the sort_mode column
 * @method     Page findOneByTarget(string $target) Return the first Page filtered by the target column
 * @method     Page findOneByVisible(int $visible) Return the first Page filtered by the visible column
 * @method     Page findOneByAccessDenied(string $access_denied) Return the first Page filtered by the access_denied column
 * @method     Page findOneByMeta(string $meta) Return the first Page filtered by the meta column
 * @method     Page findOneByProperties(string $properties) Return the first Page filtered by the properties column
 * @method     Page findOneByCdate(int $cdate) Return the first Page filtered by the cdate column
 * @method     Page findOneByMdate(int $mdate) Return the first Page filtered by the mdate column
 * @method     Page findOneByDraftExist(int $draft_exist) Return the first Page filtered by the draft_exist column
 * @method     Page findOneByForceHttps(int $force_https) Return the first Page filtered by the force_https column
 * @method     Page findOneByAccessFrom(int $access_from) Return the first Page filtered by the access_from column
 * @method     Page findOneByAccessTo(int $access_to) Return the first Page filtered by the access_to column
 * @method     Page findOneByAccessRedirectto(string $access_redirectto) Return the first Page filtered by the access_redirectto column
 * @method     Page findOneByAccessNohidenavi(int $access_nohidenavi) Return the first Page filtered by the access_nohidenavi column
 * @method     Page findOneByAccessNeedVia(int $access_need_via) Return the first Page filtered by the access_need_via column
 * @method     Page findOneByAccessFromGroups(string $access_from_groups) Return the first Page filtered by the access_from_groups column
 * @method     Page findOneByCache(int $cache) Return the first Page filtered by the cache column
 * @method     Page findOneBySearchWords(string $search_words) Return the first Page filtered by the search_words column
 * @method     Page findOneByUnsearchable(int $unsearchable) Return the first Page filtered by the unsearchable column
 * @method     Page findOneByActiveVersionId(int $active_version_id) Return the first Page filtered by the active_version_id column
 *
 * @method     array findById(int $id) Return Page objects filtered by the id column
 * @method     array findByParentId(int $parent_id) Return Page objects filtered by the parent_id column
 * @method     array findByDomainId(int $domain_id) Return Page objects filtered by the domain_id column
 * @method     array findByLft(int $lft) Return Page objects filtered by the lft column
 * @method     array findByRgt(int $rgt) Return Page objects filtered by the rgt column
 * @method     array findByLvl(int $lvl) Return Page objects filtered by the lvl column
 * @method     array findByType(int $type) Return Page objects filtered by the type column
 * @method     array findByTitle(string $title) Return Page objects filtered by the title column
 * @method     array findByPageTitle(string $page_title) Return Page objects filtered by the page_title column
 * @method     array findByUrl(string $url) Return Page objects filtered by the url column
 * @method     array findByFullUrl(string $full_url) Return Page objects filtered by the full_url column
 * @method     array findByLink(string $link) Return Page objects filtered by the link column
 * @method     array findByLayout(string $layout) Return Page objects filtered by the layout column
 * @method     array findBySort(int $sort) Return Page objects filtered by the sort column
 * @method     array findBySortMode(string $sort_mode) Return Page objects filtered by the sort_mode column
 * @method     array findByTarget(string $target) Return Page objects filtered by the target column
 * @method     array findByVisible(int $visible) Return Page objects filtered by the visible column
 * @method     array findByAccessDenied(string $access_denied) Return Page objects filtered by the access_denied column
 * @method     array findByMeta(string $meta) Return Page objects filtered by the meta column
 * @method     array findByProperties(string $properties) Return Page objects filtered by the properties column
 * @method     array findByCdate(int $cdate) Return Page objects filtered by the cdate column
 * @method     array findByMdate(int $mdate) Return Page objects filtered by the mdate column
 * @method     array findByDraftExist(int $draft_exist) Return Page objects filtered by the draft_exist column
 * @method     array findByForceHttps(int $force_https) Return Page objects filtered by the force_https column
 * @method     array findByAccessFrom(int $access_from) Return Page objects filtered by the access_from column
 * @method     array findByAccessTo(int $access_to) Return Page objects filtered by the access_to column
 * @method     array findByAccessRedirectto(string $access_redirectto) Return Page objects filtered by the access_redirectto column
 * @method     array findByAccessNohidenavi(int $access_nohidenavi) Return Page objects filtered by the access_nohidenavi column
 * @method     array findByAccessNeedVia(int $access_need_via) Return Page objects filtered by the access_need_via column
 * @method     array findByAccessFromGroups(string $access_from_groups) Return Page objects filtered by the access_from_groups column
 * @method     array findByCache(int $cache) Return Page objects filtered by the cache column
 * @method     array findBySearchWords(string $search_words) Return Page objects filtered by the search_words column
 * @method     array findByUnsearchable(int $unsearchable) Return Page objects filtered by the unsearchable column
 * @method     array findByActiveVersionId(int $active_version_id) Return Page objects filtered by the active_version_id column
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BasePageQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BasePageQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Kryn', $modelName = 'Page', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new PageQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     PageQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return PageQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof PageQuery) {
            return $criteria;
        }
        $query = new PageQuery();
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
     * @return   Page|Page[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = PagePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Page A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PARENT_ID, DOMAIN_ID, LFT, RGT, LVL, TYPE, TITLE, PAGE_TITLE, URL, FULL_URL, LINK, LAYOUT, SORT, SORT_MODE, TARGET, VISIBLE, ACCESS_DENIED, META, PROPERTIES, CDATE, MDATE, DRAFT_EXIST, FORCE_HTTPS, ACCESS_FROM, ACCESS_TO, ACCESS_REDIRECTTO, ACCESS_NOHIDENAVI, ACCESS_NEED_VIA, ACCESS_FROM_GROUPS, CACHE, SEARCH_WORDS, UNSEARCHABLE, ACTIVE_VERSION_ID FROM kryn_system_page WHERE ID = :p0';
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
            $obj = new Page();
            $obj->hydrate($row);
            PagePeer::addInstanceToPool($obj, (string) $key);
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
     * @return Page|Page[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Page[]|mixed the list of results, formatted by the current formatter
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PagePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PagePeer::ID, $keys, Criteria::IN);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(PagePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the parent_id column
     *
     * Example usage:
     * <code>
     * $query->filterByParentId(1234); // WHERE parent_id = 1234
     * $query->filterByParentId(array(12, 34)); // WHERE parent_id IN (12, 34)
     * $query->filterByParentId(array('min' => 12)); // WHERE parent_id > 12
     * </code>
     *
     * @see       filterByPageRelatedByParentId()
     *
     * @param     mixed $parentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByParentId($parentId = null, $comparison = null)
    {
        if (is_array($parentId)) {
            $useMinMax = false;
            if (isset($parentId['min'])) {
                $this->addUsingAlias(PagePeer::PARENT_ID, $parentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($parentId['max'])) {
                $this->addUsingAlias(PagePeer::PARENT_ID, $parentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::PARENT_ID, $parentId, $comparison);
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
     * @see       filterByDomain()
     *
     * @param     mixed $domainId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByDomainId($domainId = null, $comparison = null)
    {
        if (is_array($domainId)) {
            $useMinMax = false;
            if (isset($domainId['min'])) {
                $this->addUsingAlias(PagePeer::DOMAIN_ID, $domainId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($domainId['max'])) {
                $this->addUsingAlias(PagePeer::DOMAIN_ID, $domainId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::DOMAIN_ID, $domainId, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByLft($lft = null, $comparison = null)
    {
        if (is_array($lft)) {
            $useMinMax = false;
            if (isset($lft['min'])) {
                $this->addUsingAlias(PagePeer::LFT, $lft['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lft['max'])) {
                $this->addUsingAlias(PagePeer::LFT, $lft['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::LFT, $lft, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByRgt($rgt = null, $comparison = null)
    {
        if (is_array($rgt)) {
            $useMinMax = false;
            if (isset($rgt['min'])) {
                $this->addUsingAlias(PagePeer::RGT, $rgt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($rgt['max'])) {
                $this->addUsingAlias(PagePeer::RGT, $rgt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::RGT, $rgt, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByLvl($lvl = null, $comparison = null)
    {
        if (is_array($lvl)) {
            $useMinMax = false;
            if (isset($lvl['min'])) {
                $this->addUsingAlias(PagePeer::LVL, $lvl['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lvl['max'])) {
                $this->addUsingAlias(PagePeer::LVL, $lvl['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::LVL, $lvl, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (is_array($type)) {
            $useMinMax = false;
            if (isset($type['min'])) {
                $this->addUsingAlias(PagePeer::TYPE, $type['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($type['max'])) {
                $this->addUsingAlias(PagePeer::TYPE, $type['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::TYPE, $type, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::TITLE, $title, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::PAGE_TITLE, $pageTitle, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::URL, $url, $comparison);
    }

    /**
     * Filter the query on the full_url column
     *
     * Example usage:
     * <code>
     * $query->filterByFullUrl('fooValue');   // WHERE full_url = 'fooValue'
     * $query->filterByFullUrl('%fooValue%'); // WHERE full_url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $fullUrl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByFullUrl($fullUrl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($fullUrl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $fullUrl)) {
                $fullUrl = str_replace('*', '%', $fullUrl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(PagePeer::FULL_URL, $fullUrl, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::LINK, $link, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::LAYOUT, $layout, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterBySort($sort = null, $comparison = null)
    {
        if (is_array($sort)) {
            $useMinMax = false;
            if (isset($sort['min'])) {
                $this->addUsingAlias(PagePeer::SORT, $sort['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sort['max'])) {
                $this->addUsingAlias(PagePeer::SORT, $sort['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::SORT, $sort, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::SORT_MODE, $sortMode, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::TARGET, $target, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(PagePeer::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(PagePeer::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::VISIBLE, $visible, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::ACCESS_DENIED, $accessDenied, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::META, $meta, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::PROPERTIES, $properties, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByCdate($cdate = null, $comparison = null)
    {
        if (is_array($cdate)) {
            $useMinMax = false;
            if (isset($cdate['min'])) {
                $this->addUsingAlias(PagePeer::CDATE, $cdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cdate['max'])) {
                $this->addUsingAlias(PagePeer::CDATE, $cdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::CDATE, $cdate, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByMdate($mdate = null, $comparison = null)
    {
        if (is_array($mdate)) {
            $useMinMax = false;
            if (isset($mdate['min'])) {
                $this->addUsingAlias(PagePeer::MDATE, $mdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mdate['max'])) {
                $this->addUsingAlias(PagePeer::MDATE, $mdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::MDATE, $mdate, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByDraftExist($draftExist = null, $comparison = null)
    {
        if (is_array($draftExist)) {
            $useMinMax = false;
            if (isset($draftExist['min'])) {
                $this->addUsingAlias(PagePeer::DRAFT_EXIST, $draftExist['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($draftExist['max'])) {
                $this->addUsingAlias(PagePeer::DRAFT_EXIST, $draftExist['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::DRAFT_EXIST, $draftExist, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByForceHttps($forceHttps = null, $comparison = null)
    {
        if (is_array($forceHttps)) {
            $useMinMax = false;
            if (isset($forceHttps['min'])) {
                $this->addUsingAlias(PagePeer::FORCE_HTTPS, $forceHttps['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($forceHttps['max'])) {
                $this->addUsingAlias(PagePeer::FORCE_HTTPS, $forceHttps['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::FORCE_HTTPS, $forceHttps, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByAccessFrom($accessFrom = null, $comparison = null)
    {
        if (is_array($accessFrom)) {
            $useMinMax = false;
            if (isset($accessFrom['min'])) {
                $this->addUsingAlias(PagePeer::ACCESS_FROM, $accessFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessFrom['max'])) {
                $this->addUsingAlias(PagePeer::ACCESS_FROM, $accessFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::ACCESS_FROM, $accessFrom, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByAccessTo($accessTo = null, $comparison = null)
    {
        if (is_array($accessTo)) {
            $useMinMax = false;
            if (isset($accessTo['min'])) {
                $this->addUsingAlias(PagePeer::ACCESS_TO, $accessTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessTo['max'])) {
                $this->addUsingAlias(PagePeer::ACCESS_TO, $accessTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::ACCESS_TO, $accessTo, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::ACCESS_REDIRECTTO, $accessRedirectto, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByAccessNohidenavi($accessNohidenavi = null, $comparison = null)
    {
        if (is_array($accessNohidenavi)) {
            $useMinMax = false;
            if (isset($accessNohidenavi['min'])) {
                $this->addUsingAlias(PagePeer::ACCESS_NOHIDENAVI, $accessNohidenavi['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessNohidenavi['max'])) {
                $this->addUsingAlias(PagePeer::ACCESS_NOHIDENAVI, $accessNohidenavi['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::ACCESS_NOHIDENAVI, $accessNohidenavi, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByAccessNeedVia($accessNeedVia = null, $comparison = null)
    {
        if (is_array($accessNeedVia)) {
            $useMinMax = false;
            if (isset($accessNeedVia['min'])) {
                $this->addUsingAlias(PagePeer::ACCESS_NEED_VIA, $accessNeedVia['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessNeedVia['max'])) {
                $this->addUsingAlias(PagePeer::ACCESS_NEED_VIA, $accessNeedVia['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::ACCESS_NEED_VIA, $accessNeedVia, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::ACCESS_FROM_GROUPS, $accessFromGroups, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByCache($cache = null, $comparison = null)
    {
        if (is_array($cache)) {
            $useMinMax = false;
            if (isset($cache['min'])) {
                $this->addUsingAlias(PagePeer::CACHE, $cache['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cache['max'])) {
                $this->addUsingAlias(PagePeer::CACHE, $cache['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::CACHE, $cache, $comparison);
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
     * @return PageQuery The current query, for fluid interface
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

        return $this->addUsingAlias(PagePeer::SEARCH_WORDS, $searchWords, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByUnsearchable($unsearchable = null, $comparison = null)
    {
        if (is_array($unsearchable)) {
            $useMinMax = false;
            if (isset($unsearchable['min'])) {
                $this->addUsingAlias(PagePeer::UNSEARCHABLE, $unsearchable['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($unsearchable['max'])) {
                $this->addUsingAlias(PagePeer::UNSEARCHABLE, $unsearchable['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::UNSEARCHABLE, $unsearchable, $comparison);
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
     * @return PageQuery The current query, for fluid interface
     */
    public function filterByActiveVersionId($activeVersionId = null, $comparison = null)
    {
        if (is_array($activeVersionId)) {
            $useMinMax = false;
            if (isset($activeVersionId['min'])) {
                $this->addUsingAlias(PagePeer::ACTIVE_VERSION_ID, $activeVersionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($activeVersionId['max'])) {
                $this->addUsingAlias(PagePeer::ACTIVE_VERSION_ID, $activeVersionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PagePeer::ACTIVE_VERSION_ID, $activeVersionId, $comparison);
    }

    /**
     * Filter the query by a related Domain object
     *
     * @param   Domain|PropelObjectCollection $domain The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   PageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByDomain($domain, $comparison = null)
    {
        if ($domain instanceof Domain) {
            return $this
                ->addUsingAlias(PagePeer::DOMAIN_ID, $domain->getId(), $comparison);
        } elseif ($domain instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PagePeer::DOMAIN_ID, $domain->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByDomain() only accepts arguments of type Domain or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Domain relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function joinDomain($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Domain');

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
            $this->addJoinObject($join, 'Domain');
        }

        return $this;
    }

    /**
     * Use the Domain relation Domain object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   DomainQuery A secondary query class using the current class as primary query
     */
    public function useDomainQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinDomain($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Domain', 'DomainQuery');
    }

    /**
     * Filter the query by a related Page object
     *
     * @param   Page|PropelObjectCollection $page The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   PageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByPageRelatedByParentId($page, $comparison = null)
    {
        if ($page instanceof Page) {
            return $this
                ->addUsingAlias(PagePeer::PARENT_ID, $page->getId(), $comparison);
        } elseif ($page instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(PagePeer::PARENT_ID, $page->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPageRelatedByParentId() only accepts arguments of type Page or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PageRelatedByParentId relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function joinPageRelatedByParentId($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PageRelatedByParentId');

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
            $this->addJoinObject($join, 'PageRelatedByParentId');
        }

        return $this;
    }

    /**
     * Use the PageRelatedByParentId relation Page object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   PageQuery A secondary query class using the current class as primary query
     */
    public function usePageRelatedByParentIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPageRelatedByParentId($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PageRelatedByParentId', 'PageQuery');
    }

    /**
     * Filter the query by a related PageContent object
     *
     * @param   PageContent|PropelObjectCollection $pageContent  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   PageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByPageContent($pageContent, $comparison = null)
    {
        if ($pageContent instanceof PageContent) {
            return $this
                ->addUsingAlias(PagePeer::ID, $pageContent->getPageId(), $comparison);
        } elseif ($pageContent instanceof PropelObjectCollection) {
            return $this
                ->usePageContentQuery()
                ->filterByPrimaryKeys($pageContent->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPageContent() only accepts arguments of type PageContent or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PageContent relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function joinPageContent($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PageContent');

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
            $this->addJoinObject($join, 'PageContent');
        }

        return $this;
    }

    /**
     * Use the PageContent relation PageContent object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   PageContentQuery A secondary query class using the current class as primary query
     */
    public function usePageContentQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPageContent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PageContent', 'PageContentQuery');
    }

    /**
     * Filter the query by a related Page object
     *
     * @param   Page|PropelObjectCollection $page  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   PageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByPageRelatedById($page, $comparison = null)
    {
        if ($page instanceof Page) {
            return $this
                ->addUsingAlias(PagePeer::ID, $page->getParentId(), $comparison);
        } elseif ($page instanceof PropelObjectCollection) {
            return $this
                ->usePageRelatedByIdQuery()
                ->filterByPrimaryKeys($page->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPageRelatedById() only accepts arguments of type Page or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PageRelatedById relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function joinPageRelatedById($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PageRelatedById');

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
            $this->addJoinObject($join, 'PageRelatedById');
        }

        return $this;
    }

    /**
     * Use the PageRelatedById relation Page object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   PageQuery A secondary query class using the current class as primary query
     */
    public function usePageRelatedByIdQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPageRelatedById($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PageRelatedById', 'PageQuery');
    }

    /**
     * Filter the query by a related Urlalias object
     *
     * @param   Urlalias|PropelObjectCollection $urlalias  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   PageQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByUrlalias($urlalias, $comparison = null)
    {
        if ($urlalias instanceof Urlalias) {
            return $this
                ->addUsingAlias(PagePeer::ID, $urlalias->getToPageId(), $comparison);
        } elseif ($urlalias instanceof PropelObjectCollection) {
            return $this
                ->useUrlaliasQuery()
                ->filterByPrimaryKeys($urlalias->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUrlalias() only accepts arguments of type Urlalias or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Urlalias relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function joinUrlalias($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Urlalias');

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
            $this->addJoinObject($join, 'Urlalias');
        }

        return $this;
    }

    /**
     * Use the Urlalias relation Urlalias object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   UrlaliasQuery A secondary query class using the current class as primary query
     */
    public function useUrlaliasQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinUrlalias($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Urlalias', 'UrlaliasQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Page $page Object to remove from the list of results
     *
     * @return PageQuery The current query, for fluid interface
     */
    public function prune($page = null)
    {
        if ($page) {
            $this->addUsingAlias(PagePeer::ID, $page->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

	// nested_set behavior
	
	/**
	 * Filter the query to restrict the result to root objects
	 *
	 * @return    PageQuery The current query, for fluid interface
	 */
	public function treeRoots()
	{
	    return $this->addUsingAlias(PagePeer::LEFT_COL, 1, Criteria::EQUAL);
	}
	
	/**
	 * Returns the objects in a certain tree, from the tree scope
	 *
	 * @param     int $scope		Scope to determine which objects node to return
	 *
	 * @return    PageQuery The current query, for fluid interface
	 */
	public function inTree($scope = null)
	{
	    return $this->addUsingAlias(PagePeer::SCOPE_COL, $scope, Criteria::EQUAL);
	}
	
	/**
	 * Filter the query to restrict the result to descendants of an object
	 *
	 * @param     Page $page The object to use for descendant search
	 *
	 * @return    PageQuery The current query, for fluid interface
	 */
	public function descendantsOf($page)
	{
	    return $this
	        ->inTree($page->getScopeValue())
	        ->addUsingAlias(PagePeer::LEFT_COL, $page->getLeftValue(), Criteria::GREATER_THAN)
	        ->addUsingAlias(PagePeer::LEFT_COL, $page->getRightValue(), Criteria::LESS_THAN);
	}
	
	/**
	 * Filter the query to restrict the result to the branch of an object.
	 * Same as descendantsOf(), except that it includes the object passed as parameter in the result
	 *
	 * @param     Page $page The object to use for branch search
	 *
	 * @return    PageQuery The current query, for fluid interface
	 */
	public function branchOf($page)
	{
	    return $this
	        ->inTree($page->getScopeValue())
	        ->addUsingAlias(PagePeer::LEFT_COL, $page->getLeftValue(), Criteria::GREATER_EQUAL)
	        ->addUsingAlias(PagePeer::LEFT_COL, $page->getRightValue(), Criteria::LESS_EQUAL);
	}
	
	/**
	 * Filter the query to restrict the result to children of an object
	 *
	 * @param     Page $page The object to use for child search
	 *
	 * @return    PageQuery The current query, for fluid interface
	 */
	public function childrenOf($page)
	{
	    return $this
	        ->descendantsOf($page)
	        ->addUsingAlias(PagePeer::LEVEL_COL, $page->getLevel() + 1, Criteria::EQUAL);
	}
	
	/**
	 * Filter the query to restrict the result to siblings of an object.
	 * The result does not include the object passed as parameter.
	 *
	 * @param     Page $page The object to use for sibling search
	 * @param      PropelPDO $con Connection to use.
	 *
	 * @return    PageQuery The current query, for fluid interface
	 */
	public function siblingsOf($page, PropelPDO $con = null)
	{
	    if ($page->isRoot()) {
	        return $this->
	            add(PagePeer::LEVEL_COL, '1<>1', Criteria::CUSTOM);
	    } else {
	        return $this
	            ->childrenOf($page->getParent($con))
	            ->prune($page);
	    }
	}
	
	/**
	 * Filter the query to restrict the result to ancestors of an object
	 *
	 * @param     Page $page The object to use for ancestors search
	 *
	 * @return    PageQuery The current query, for fluid interface
	 */
	public function ancestorsOf($page)
	{
	    return $this
	        ->inTree($page->getScopeValue())
	        ->addUsingAlias(PagePeer::LEFT_COL, $page->getLeftValue(), Criteria::LESS_THAN)
	        ->addUsingAlias(PagePeer::RIGHT_COL, $page->getRightValue(), Criteria::GREATER_THAN);
	}
	
	/**
	 * Filter the query to restrict the result to roots of an object.
	 * Same as ancestorsOf(), except that it includes the object passed as parameter in the result
	 *
	 * @param     Page $page The object to use for roots search
	 *
	 * @return    PageQuery The current query, for fluid interface
	 */
	public function rootsOf($page)
	{
	    return $this
	        ->inTree($page->getScopeValue())
	        ->addUsingAlias(PagePeer::LEFT_COL, $page->getLeftValue(), Criteria::LESS_EQUAL)
	        ->addUsingAlias(PagePeer::RIGHT_COL, $page->getRightValue(), Criteria::GREATER_EQUAL);
	}
	
	/**
	 * Order the result by branch, i.e. natural tree order
	 *
	 * @param     bool $reverse if true, reverses the order
	 *
	 * @return    PageQuery The current query, for fluid interface
	 */
	public function orderByBranch($reverse = false)
	{
	    if ($reverse) {
	        return $this
	            ->addDescendingOrderByColumn(PagePeer::LEFT_COL);
	    } else {
	        return $this
	            ->addAscendingOrderByColumn(PagePeer::LEFT_COL);
	    }
	}
	
	/**
	 * Order the result by level, the closer to the root first
	 *
	 * @param     bool $reverse if true, reverses the order
	 *
	 * @return    PageQuery The current query, for fluid interface
	 */
	public function orderByLevel($reverse = false)
	{
	    if ($reverse) {
	        return $this
	            ->addAscendingOrderByColumn(PagePeer::RIGHT_COL);
	    } else {
	        return $this
	            ->addDescendingOrderByColumn(PagePeer::RIGHT_COL);
	    }
	}
	
	/**
	 * Returns a root node for the tree
	 *
	 * @param      int $scope		Scope to determine which root node to return
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     Page The tree root object
	 */
	public function findRoot($scope = null, $con = null)
	{
	    return $this
	        ->addUsingAlias(PagePeer::LEFT_COL, 1, Criteria::EQUAL)
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

} // BasePageQuery
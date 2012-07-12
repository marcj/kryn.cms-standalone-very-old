<?php


/**
 * Base class that represents a query for the 'kryn_system_search' table.
 *
 * 
 *
 * @method     SystemSearchQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     SystemSearchQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     SystemSearchQuery orderByMd5($order = Criteria::ASC) Order by the md5 column
 * @method     SystemSearchQuery orderByMdate($order = Criteria::ASC) Order by the mdate column
 * @method     SystemSearchQuery orderByBlacklist($order = Criteria::ASC) Order by the blacklist column
 * @method     SystemSearchQuery orderByPageId($order = Criteria::ASC) Order by the page_id column
 * @method     SystemSearchQuery orderByDomainId($order = Criteria::ASC) Order by the domain_id column
 * @method     SystemSearchQuery orderByPageContent($order = Criteria::ASC) Order by the page_content column
 *
 * @method     SystemSearchQuery groupByUrl() Group by the url column
 * @method     SystemSearchQuery groupByTitle() Group by the title column
 * @method     SystemSearchQuery groupByMd5() Group by the md5 column
 * @method     SystemSearchQuery groupByMdate() Group by the mdate column
 * @method     SystemSearchQuery groupByBlacklist() Group by the blacklist column
 * @method     SystemSearchQuery groupByPageId() Group by the page_id column
 * @method     SystemSearchQuery groupByDomainId() Group by the domain_id column
 * @method     SystemSearchQuery groupByPageContent() Group by the page_content column
 *
 * @method     SystemSearchQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SystemSearchQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SystemSearchQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SystemSearch findOne(PropelPDO $con = null) Return the first SystemSearch matching the query
 * @method     SystemSearch findOneOrCreate(PropelPDO $con = null) Return the first SystemSearch matching the query, or a new SystemSearch object populated from the query conditions when no match is found
 *
 * @method     SystemSearch findOneByUrl(string $url) Return the first SystemSearch filtered by the url column
 * @method     SystemSearch findOneByTitle(string $title) Return the first SystemSearch filtered by the title column
 * @method     SystemSearch findOneByMd5(string $md5) Return the first SystemSearch filtered by the md5 column
 * @method     SystemSearch findOneByMdate(int $mdate) Return the first SystemSearch filtered by the mdate column
 * @method     SystemSearch findOneByBlacklist(int $blacklist) Return the first SystemSearch filtered by the blacklist column
 * @method     SystemSearch findOneByPageId(int $page_id) Return the first SystemSearch filtered by the page_id column
 * @method     SystemSearch findOneByDomainId(int $domain_id) Return the first SystemSearch filtered by the domain_id column
 * @method     SystemSearch findOneByPageContent(string $page_content) Return the first SystemSearch filtered by the page_content column
 *
 * @method     array findByUrl(string $url) Return SystemSearch objects filtered by the url column
 * @method     array findByTitle(string $title) Return SystemSearch objects filtered by the title column
 * @method     array findByMd5(string $md5) Return SystemSearch objects filtered by the md5 column
 * @method     array findByMdate(int $mdate) Return SystemSearch objects filtered by the mdate column
 * @method     array findByBlacklist(int $blacklist) Return SystemSearch objects filtered by the blacklist column
 * @method     array findByPageId(int $page_id) Return SystemSearch objects filtered by the page_id column
 * @method     array findByDomainId(int $domain_id) Return SystemSearch objects filtered by the domain_id column
 * @method     array findByPageContent(string $page_content) Return SystemSearch objects filtered by the page_content column
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemSearchQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseSystemSearchQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'kryn', $modelName = 'SystemSearch', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SystemSearchQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     SystemSearchQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SystemSearchQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SystemSearchQuery) {
            return $criteria;
        }
        $query = new SystemSearchQuery();
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
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array $key Primary key to use for the query 
                         A Primary key composition: [$url, $domain_id]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   SystemSearch|SystemSearch[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SystemSearchPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SystemSearchPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   SystemSearch A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT URL, TITLE, MD5, MDATE, BLACKLIST, PAGE_ID, DOMAIN_ID, PAGE_CONTENT FROM kryn_system_search WHERE URL = :p0 AND DOMAIN_ID = :p1';
        try {
            $stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key[0], PDO::PARAM_STR);
			$stmt->bindValue(':p1', $key[1], PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new SystemSearch();
            $obj->hydrate($row);
            SystemSearchPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return SystemSearch|SystemSearch[]|mixed the result, formatted by the current formatter
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
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|SystemSearch[]|mixed the list of results, formatted by the current formatter
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
     * @return SystemSearchQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(SystemSearchPeer::URL, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(SystemSearchPeer::DOMAIN_ID, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SystemSearchQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(SystemSearchPeer::URL, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(SystemSearchPeer::DOMAIN_ID, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
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
     * @return SystemSearchQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemSearchPeer::URL, $url, $comparison);
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
     * @return SystemSearchQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemSearchPeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the md5 column
     *
     * Example usage:
     * <code>
     * $query->filterByMd5('fooValue');   // WHERE md5 = 'fooValue'
     * $query->filterByMd5('%fooValue%'); // WHERE md5 LIKE '%fooValue%'
     * </code>
     *
     * @param     string $md5 The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemSearchQuery The current query, for fluid interface
     */
    public function filterByMd5($md5 = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($md5)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $md5)) {
                $md5 = str_replace('*', '%', $md5);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemSearchPeer::MD5, $md5, $comparison);
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
     * @return SystemSearchQuery The current query, for fluid interface
     */
    public function filterByMdate($mdate = null, $comparison = null)
    {
        if (is_array($mdate)) {
            $useMinMax = false;
            if (isset($mdate['min'])) {
                $this->addUsingAlias(SystemSearchPeer::MDATE, $mdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mdate['max'])) {
                $this->addUsingAlias(SystemSearchPeer::MDATE, $mdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemSearchPeer::MDATE, $mdate, $comparison);
    }

    /**
     * Filter the query on the blacklist column
     *
     * Example usage:
     * <code>
     * $query->filterByBlacklist(1234); // WHERE blacklist = 1234
     * $query->filterByBlacklist(array(12, 34)); // WHERE blacklist IN (12, 34)
     * $query->filterByBlacklist(array('min' => 12)); // WHERE blacklist > 12
     * </code>
     *
     * @param     mixed $blacklist The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemSearchQuery The current query, for fluid interface
     */
    public function filterByBlacklist($blacklist = null, $comparison = null)
    {
        if (is_array($blacklist)) {
            $useMinMax = false;
            if (isset($blacklist['min'])) {
                $this->addUsingAlias(SystemSearchPeer::BLACKLIST, $blacklist['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($blacklist['max'])) {
                $this->addUsingAlias(SystemSearchPeer::BLACKLIST, $blacklist['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemSearchPeer::BLACKLIST, $blacklist, $comparison);
    }

    /**
     * Filter the query on the page_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPageId(1234); // WHERE page_id = 1234
     * $query->filterByPageId(array(12, 34)); // WHERE page_id IN (12, 34)
     * $query->filterByPageId(array('min' => 12)); // WHERE page_id > 12
     * </code>
     *
     * @param     mixed $pageId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemSearchQuery The current query, for fluid interface
     */
    public function filterByPageId($pageId = null, $comparison = null)
    {
        if (is_array($pageId)) {
            $useMinMax = false;
            if (isset($pageId['min'])) {
                $this->addUsingAlias(SystemSearchPeer::PAGE_ID, $pageId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($pageId['max'])) {
                $this->addUsingAlias(SystemSearchPeer::PAGE_ID, $pageId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemSearchPeer::PAGE_ID, $pageId, $comparison);
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
     * @return SystemSearchQuery The current query, for fluid interface
     */
    public function filterByDomainId($domainId = null, $comparison = null)
    {
        if (is_array($domainId) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(SystemSearchPeer::DOMAIN_ID, $domainId, $comparison);
    }

    /**
     * Filter the query on the page_content column
     *
     * Example usage:
     * <code>
     * $query->filterByPageContent('fooValue');   // WHERE page_content = 'fooValue'
     * $query->filterByPageContent('%fooValue%'); // WHERE page_content LIKE '%fooValue%'
     * </code>
     *
     * @param     string $pageContent The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemSearchQuery The current query, for fluid interface
     */
    public function filterByPageContent($pageContent = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($pageContent)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $pageContent)) {
                $pageContent = str_replace('*', '%', $pageContent);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemSearchPeer::PAGE_CONTENT, $pageContent, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   SystemSearch $systemSearch Object to remove from the list of results
     *
     * @return SystemSearchQuery The current query, for fluid interface
     */
    public function prune($systemSearch = null)
    {
        if ($systemSearch) {
            $this->addCond('pruneCond0', $this->getAliasedColName(SystemSearchPeer::URL), $systemSearch->getUrl(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(SystemSearchPeer::DOMAIN_ID), $systemSearch->getDomainId(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

} // BaseSystemSearchQuery
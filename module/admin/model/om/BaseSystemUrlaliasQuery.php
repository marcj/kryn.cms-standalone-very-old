<?php


/**
 * Base class that represents a query for the 'kryn_system_urlalias' table.
 *
 * 
 *
 * @method     SystemUrlaliasQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     SystemUrlaliasQuery orderByUrl($order = Criteria::ASC) Order by the url column
 * @method     SystemUrlaliasQuery orderByToPageId($order = Criteria::ASC) Order by the to_page_id column
 * @method     SystemUrlaliasQuery orderByDomainId($order = Criteria::ASC) Order by the domain_id column
 *
 * @method     SystemUrlaliasQuery groupById() Group by the id column
 * @method     SystemUrlaliasQuery groupByUrl() Group by the url column
 * @method     SystemUrlaliasQuery groupByToPageId() Group by the to_page_id column
 * @method     SystemUrlaliasQuery groupByDomainId() Group by the domain_id column
 *
 * @method     SystemUrlaliasQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SystemUrlaliasQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SystemUrlaliasQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SystemUrlaliasQuery leftJoinSystemPage($relationAlias = null) Adds a LEFT JOIN clause to the query using the SystemPage relation
 * @method     SystemUrlaliasQuery rightJoinSystemPage($relationAlias = null) Adds a RIGHT JOIN clause to the query using the SystemPage relation
 * @method     SystemUrlaliasQuery innerJoinSystemPage($relationAlias = null) Adds a INNER JOIN clause to the query using the SystemPage relation
 *
 * @method     SystemUrlalias findOne(PropelPDO $con = null) Return the first SystemUrlalias matching the query
 * @method     SystemUrlalias findOneOrCreate(PropelPDO $con = null) Return the first SystemUrlalias matching the query, or a new SystemUrlalias object populated from the query conditions when no match is found
 *
 * @method     SystemUrlalias findOneById(int $id) Return the first SystemUrlalias filtered by the id column
 * @method     SystemUrlalias findOneByUrl(string $url) Return the first SystemUrlalias filtered by the url column
 * @method     SystemUrlalias findOneByToPageId(int $to_page_id) Return the first SystemUrlalias filtered by the to_page_id column
 * @method     SystemUrlalias findOneByDomainId(int $domain_id) Return the first SystemUrlalias filtered by the domain_id column
 *
 * @method     array findById(int $id) Return SystemUrlalias objects filtered by the id column
 * @method     array findByUrl(string $url) Return SystemUrlalias objects filtered by the url column
 * @method     array findByToPageId(int $to_page_id) Return SystemUrlalias objects filtered by the to_page_id column
 * @method     array findByDomainId(int $domain_id) Return SystemUrlalias objects filtered by the domain_id column
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemUrlaliasQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseSystemUrlaliasQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'kryn', $modelName = 'SystemUrlalias', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SystemUrlaliasQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     SystemUrlaliasQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SystemUrlaliasQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SystemUrlaliasQuery) {
            return $criteria;
        }
        $query = new SystemUrlaliasQuery();
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
     * @return   SystemUrlalias|SystemUrlalias[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SystemUrlaliasPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SystemUrlaliasPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   SystemUrlalias A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, URL, TO_PAGE_ID, DOMAIN_ID FROM kryn_system_urlalias WHERE ID = :p0';
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
            $obj = new SystemUrlalias();
            $obj->hydrate($row);
            SystemUrlaliasPeer::addInstanceToPool($obj, (string) $key);
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
     * @return SystemUrlalias|SystemUrlalias[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|SystemUrlalias[]|mixed the list of results, formatted by the current formatter
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
     * @return SystemUrlaliasQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SystemUrlaliasPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SystemUrlaliasQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SystemUrlaliasPeer::ID, $keys, Criteria::IN);
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
     * @return SystemUrlaliasQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(SystemUrlaliasPeer::ID, $id, $comparison);
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
     * @return SystemUrlaliasQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemUrlaliasPeer::URL, $url, $comparison);
    }

    /**
     * Filter the query on the to_page_id column
     *
     * Example usage:
     * <code>
     * $query->filterByToPageId(1234); // WHERE to_page_id = 1234
     * $query->filterByToPageId(array(12, 34)); // WHERE to_page_id IN (12, 34)
     * $query->filterByToPageId(array('min' => 12)); // WHERE to_page_id > 12
     * </code>
     *
     * @see       filterBySystemPage()
     *
     * @param     mixed $toPageId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemUrlaliasQuery The current query, for fluid interface
     */
    public function filterByToPageId($toPageId = null, $comparison = null)
    {
        if (is_array($toPageId)) {
            $useMinMax = false;
            if (isset($toPageId['min'])) {
                $this->addUsingAlias(SystemUrlaliasPeer::TO_PAGE_ID, $toPageId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($toPageId['max'])) {
                $this->addUsingAlias(SystemUrlaliasPeer::TO_PAGE_ID, $toPageId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemUrlaliasPeer::TO_PAGE_ID, $toPageId, $comparison);
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
     * @return SystemUrlaliasQuery The current query, for fluid interface
     */
    public function filterByDomainId($domainId = null, $comparison = null)
    {
        if (is_array($domainId)) {
            $useMinMax = false;
            if (isset($domainId['min'])) {
                $this->addUsingAlias(SystemUrlaliasPeer::DOMAIN_ID, $domainId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($domainId['max'])) {
                $this->addUsingAlias(SystemUrlaliasPeer::DOMAIN_ID, $domainId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemUrlaliasPeer::DOMAIN_ID, $domainId, $comparison);
    }

    /**
     * Filter the query by a related SystemPage object
     *
     * @param   SystemPage|PropelObjectCollection $systemPage The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   SystemUrlaliasQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterBySystemPage($systemPage, $comparison = null)
    {
        if ($systemPage instanceof SystemPage) {
            return $this
                ->addUsingAlias(SystemUrlaliasPeer::TO_PAGE_ID, $systemPage->getId(), $comparison);
        } elseif ($systemPage instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SystemUrlaliasPeer::TO_PAGE_ID, $systemPage->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterBySystemPage() only accepts arguments of type SystemPage or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the SystemPage relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return SystemUrlaliasQuery The current query, for fluid interface
     */
    public function joinSystemPage($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('SystemPage');

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
            $this->addJoinObject($join, 'SystemPage');
        }

        return $this;
    }

    /**
     * Use the SystemPage relation SystemPage object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   SystemPageQuery A secondary query class using the current class as primary query
     */
    public function useSystemPageQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSystemPage($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'SystemPage', 'SystemPageQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   SystemUrlalias $systemUrlalias Object to remove from the list of results
     *
     * @return SystemUrlaliasQuery The current query, for fluid interface
     */
    public function prune($systemUrlalias = null)
    {
        if ($systemUrlalias) {
            $this->addUsingAlias(SystemUrlaliasPeer::ID, $systemUrlalias->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseSystemUrlaliasQuery
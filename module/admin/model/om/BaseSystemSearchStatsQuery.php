<?php


/**
 * Base class that represents a query for the 'kryn_system_search_stats' table.
 *
 * 
 *
 * @method     SystemSearchStatsQuery orderByWord($order = Criteria::ASC) Order by the word column
 * @method     SystemSearchStatsQuery orderBySearchcount($order = Criteria::ASC) Order by the searchcount column
 * @method     SystemSearchStatsQuery orderByFound($order = Criteria::ASC) Order by the found column
 *
 * @method     SystemSearchStatsQuery groupByWord() Group by the word column
 * @method     SystemSearchStatsQuery groupBySearchcount() Group by the searchcount column
 * @method     SystemSearchStatsQuery groupByFound() Group by the found column
 *
 * @method     SystemSearchStatsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SystemSearchStatsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SystemSearchStatsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SystemSearchStats findOne(PropelPDO $con = null) Return the first SystemSearchStats matching the query
 * @method     SystemSearchStats findOneOrCreate(PropelPDO $con = null) Return the first SystemSearchStats matching the query, or a new SystemSearchStats object populated from the query conditions when no match is found
 *
 * @method     SystemSearchStats findOneByWord(string $word) Return the first SystemSearchStats filtered by the word column
 * @method     SystemSearchStats findOneBySearchcount(int $searchcount) Return the first SystemSearchStats filtered by the searchcount column
 * @method     SystemSearchStats findOneByFound(int $found) Return the first SystemSearchStats filtered by the found column
 *
 * @method     array findByWord(string $word) Return SystemSearchStats objects filtered by the word column
 * @method     array findBySearchcount(int $searchcount) Return SystemSearchStats objects filtered by the searchcount column
 * @method     array findByFound(int $found) Return SystemSearchStats objects filtered by the found column
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemSearchStatsQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseSystemSearchStatsQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'kryn', $modelName = 'SystemSearchStats', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SystemSearchStatsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     SystemSearchStatsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SystemSearchStatsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SystemSearchStatsQuery) {
            return $criteria;
        }
        $query = new SystemSearchStatsQuery();
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
                         A Primary key composition: [$word, $found]
     * @param     PropelPDO $con an optional connection object
     *
     * @return   SystemSearchStats|SystemSearchStats[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SystemSearchStatsPeer::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SystemSearchStatsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   SystemSearchStats A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT WORD, SEARCHCOUNT, FOUND FROM kryn_system_search_stats WHERE WORD = :p0 AND FOUND = :p1';
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
            $obj = new SystemSearchStats();
            $obj->hydrate($row);
            SystemSearchStatsPeer::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
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
     * @return SystemSearchStats|SystemSearchStats[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|SystemSearchStats[]|mixed the list of results, formatted by the current formatter
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
     * @return SystemSearchStatsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(SystemSearchStatsPeer::WORD, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(SystemSearchStatsPeer::FOUND, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SystemSearchStatsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(SystemSearchStatsPeer::WORD, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(SystemSearchStatsPeer::FOUND, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the word column
     *
     * Example usage:
     * <code>
     * $query->filterByWord('fooValue');   // WHERE word = 'fooValue'
     * $query->filterByWord('%fooValue%'); // WHERE word LIKE '%fooValue%'
     * </code>
     *
     * @param     string $word The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemSearchStatsQuery The current query, for fluid interface
     */
    public function filterByWord($word = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($word)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $word)) {
                $word = str_replace('*', '%', $word);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemSearchStatsPeer::WORD, $word, $comparison);
    }

    /**
     * Filter the query on the searchcount column
     *
     * Example usage:
     * <code>
     * $query->filterBySearchcount(1234); // WHERE searchcount = 1234
     * $query->filterBySearchcount(array(12, 34)); // WHERE searchcount IN (12, 34)
     * $query->filterBySearchcount(array('min' => 12)); // WHERE searchcount > 12
     * </code>
     *
     * @param     mixed $searchcount The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemSearchStatsQuery The current query, for fluid interface
     */
    public function filterBySearchcount($searchcount = null, $comparison = null)
    {
        if (is_array($searchcount)) {
            $useMinMax = false;
            if (isset($searchcount['min'])) {
                $this->addUsingAlias(SystemSearchStatsPeer::SEARCHCOUNT, $searchcount['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($searchcount['max'])) {
                $this->addUsingAlias(SystemSearchStatsPeer::SEARCHCOUNT, $searchcount['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemSearchStatsPeer::SEARCHCOUNT, $searchcount, $comparison);
    }

    /**
     * Filter the query on the found column
     *
     * Example usage:
     * <code>
     * $query->filterByFound(1234); // WHERE found = 1234
     * $query->filterByFound(array(12, 34)); // WHERE found IN (12, 34)
     * $query->filterByFound(array('min' => 12)); // WHERE found > 12
     * </code>
     *
     * @param     mixed $found The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemSearchStatsQuery The current query, for fluid interface
     */
    public function filterByFound($found = null, $comparison = null)
    {
        if (is_array($found) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(SystemSearchStatsPeer::FOUND, $found, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   SystemSearchStats $systemSearchStats Object to remove from the list of results
     *
     * @return SystemSearchStatsQuery The current query, for fluid interface
     */
    public function prune($systemSearchStats = null)
    {
        if ($systemSearchStats) {
            $this->addCond('pruneCond0', $this->getAliasedColName(SystemSearchStatsPeer::WORD), $systemSearchStats->getWord(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(SystemSearchStatsPeer::FOUND), $systemSearchStats->getFound(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

} // BaseSystemSearchStatsQuery
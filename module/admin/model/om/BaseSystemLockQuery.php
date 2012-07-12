<?php


/**
 * Base class that represents a query for the 'kryn_system_lock' table.
 *
 * 
 *
 * @method     SystemLockQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     SystemLockQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     SystemLockQuery orderByCkey($order = Criteria::ASC) Order by the ckey column
 * @method     SystemLockQuery orderBySessionId($order = Criteria::ASC) Order by the session_id column
 * @method     SystemLockQuery orderByTime($order = Criteria::ASC) Order by the time column
 *
 * @method     SystemLockQuery groupById() Group by the id column
 * @method     SystemLockQuery groupByType() Group by the type column
 * @method     SystemLockQuery groupByCkey() Group by the ckey column
 * @method     SystemLockQuery groupBySessionId() Group by the session_id column
 * @method     SystemLockQuery groupByTime() Group by the time column
 *
 * @method     SystemLockQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SystemLockQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SystemLockQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SystemLock findOne(PropelPDO $con = null) Return the first SystemLock matching the query
 * @method     SystemLock findOneOrCreate(PropelPDO $con = null) Return the first SystemLock matching the query, or a new SystemLock object populated from the query conditions when no match is found
 *
 * @method     SystemLock findOneById(int $id) Return the first SystemLock filtered by the id column
 * @method     SystemLock findOneByType(string $type) Return the first SystemLock filtered by the type column
 * @method     SystemLock findOneByCkey(string $ckey) Return the first SystemLock filtered by the ckey column
 * @method     SystemLock findOneBySessionId(int $session_id) Return the first SystemLock filtered by the session_id column
 * @method     SystemLock findOneByTime(int $time) Return the first SystemLock filtered by the time column
 *
 * @method     array findById(int $id) Return SystemLock objects filtered by the id column
 * @method     array findByType(string $type) Return SystemLock objects filtered by the type column
 * @method     array findByCkey(string $ckey) Return SystemLock objects filtered by the ckey column
 * @method     array findBySessionId(int $session_id) Return SystemLock objects filtered by the session_id column
 * @method     array findByTime(int $time) Return SystemLock objects filtered by the time column
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemLockQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseSystemLockQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'kryn', $modelName = 'SystemLock', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SystemLockQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     SystemLockQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SystemLockQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SystemLockQuery) {
            return $criteria;
        }
        $query = new SystemLockQuery();
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
     * @return   SystemLock|SystemLock[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SystemLockPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SystemLockPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   SystemLock A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, TYPE, CKEY, SESSION_ID, TIME FROM kryn_system_lock WHERE ID = :p0';
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
            $obj = new SystemLock();
            $obj->hydrate($row);
            SystemLockPeer::addInstanceToPool($obj, (string) $key);
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
     * @return SystemLock|SystemLock[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|SystemLock[]|mixed the list of results, formatted by the current formatter
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
     * @return SystemLockQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SystemLockPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SystemLockQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SystemLockPeer::ID, $keys, Criteria::IN);
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
     * @return SystemLockQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(SystemLockPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemLockQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $type)) {
                $type = str_replace('*', '%', $type);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemLockPeer::TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the ckey column
     *
     * Example usage:
     * <code>
     * $query->filterByCkey('fooValue');   // WHERE ckey = 'fooValue'
     * $query->filterByCkey('%fooValue%'); // WHERE ckey LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ckey The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemLockQuery The current query, for fluid interface
     */
    public function filterByCkey($ckey = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ckey)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ckey)) {
                $ckey = str_replace('*', '%', $ckey);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemLockPeer::CKEY, $ckey, $comparison);
    }

    /**
     * Filter the query on the session_id column
     *
     * Example usage:
     * <code>
     * $query->filterBySessionId(1234); // WHERE session_id = 1234
     * $query->filterBySessionId(array(12, 34)); // WHERE session_id IN (12, 34)
     * $query->filterBySessionId(array('min' => 12)); // WHERE session_id > 12
     * </code>
     *
     * @param     mixed $sessionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemLockQuery The current query, for fluid interface
     */
    public function filterBySessionId($sessionId = null, $comparison = null)
    {
        if (is_array($sessionId)) {
            $useMinMax = false;
            if (isset($sessionId['min'])) {
                $this->addUsingAlias(SystemLockPeer::SESSION_ID, $sessionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sessionId['max'])) {
                $this->addUsingAlias(SystemLockPeer::SESSION_ID, $sessionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemLockPeer::SESSION_ID, $sessionId, $comparison);
    }

    /**
     * Filter the query on the time column
     *
     * Example usage:
     * <code>
     * $query->filterByTime(1234); // WHERE time = 1234
     * $query->filterByTime(array(12, 34)); // WHERE time IN (12, 34)
     * $query->filterByTime(array('min' => 12)); // WHERE time > 12
     * </code>
     *
     * @param     mixed $time The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemLockQuery The current query, for fluid interface
     */
    public function filterByTime($time = null, $comparison = null)
    {
        if (is_array($time)) {
            $useMinMax = false;
            if (isset($time['min'])) {
                $this->addUsingAlias(SystemLockPeer::TIME, $time['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($time['max'])) {
                $this->addUsingAlias(SystemLockPeer::TIME, $time['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemLockPeer::TIME, $time, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   SystemLock $systemLock Object to remove from the list of results
     *
     * @return SystemLockQuery The current query, for fluid interface
     */
    public function prune($systemLock = null)
    {
        if ($systemLock) {
            $this->addUsingAlias(SystemLockPeer::ID, $systemLock->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseSystemLockQuery
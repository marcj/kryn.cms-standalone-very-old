<?php


/**
 * Base class that represents a query for the 'kryn_system_lock' table.
 *
 * 
 *
 * @method     LockQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     LockQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     LockQuery orderByCkey($order = Criteria::ASC) Order by the ckey column
 * @method     LockQuery orderBySessionId($order = Criteria::ASC) Order by the session_id column
 * @method     LockQuery orderByTime($order = Criteria::ASC) Order by the time column
 *
 * @method     LockQuery groupById() Group by the id column
 * @method     LockQuery groupByType() Group by the type column
 * @method     LockQuery groupByCkey() Group by the ckey column
 * @method     LockQuery groupBySessionId() Group by the session_id column
 * @method     LockQuery groupByTime() Group by the time column
 *
 * @method     LockQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     LockQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     LockQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     Lock findOne(PropelPDO $con = null) Return the first Lock matching the query
 * @method     Lock findOneOrCreate(PropelPDO $con = null) Return the first Lock matching the query, or a new Lock object populated from the query conditions when no match is found
 *
 * @method     Lock findOneById(int $id) Return the first Lock filtered by the id column
 * @method     Lock findOneByType(string $type) Return the first Lock filtered by the type column
 * @method     Lock findOneByCkey(string $ckey) Return the first Lock filtered by the ckey column
 * @method     Lock findOneBySessionId(int $session_id) Return the first Lock filtered by the session_id column
 * @method     Lock findOneByTime(int $time) Return the first Lock filtered by the time column
 *
 * @method     array findById(int $id) Return Lock objects filtered by the id column
 * @method     array findByType(string $type) Return Lock objects filtered by the type column
 * @method     array findByCkey(string $ckey) Return Lock objects filtered by the ckey column
 * @method     array findBySessionId(int $session_id) Return Lock objects filtered by the session_id column
 * @method     array findByTime(int $time) Return Lock objects filtered by the time column
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BaseLockQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseLockQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Kryn', $modelName = 'Lock', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new LockQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     LockQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return LockQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof LockQuery) {
            return $criteria;
        }
        $query = new LockQuery();
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
     * @return   Lock|Lock[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LockPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(LockPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Lock A model object, or null if the key is not found
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
            $obj = new Lock();
            $obj->hydrate($row);
            LockPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Lock|Lock[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Lock[]|mixed the list of results, formatted by the current formatter
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
     * @return LockQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LockPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return LockQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LockPeer::ID, $keys, Criteria::IN);
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
     * @return LockQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(LockPeer::ID, $id, $comparison);
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
     * @return LockQuery The current query, for fluid interface
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

        return $this->addUsingAlias(LockPeer::TYPE, $type, $comparison);
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
     * @return LockQuery The current query, for fluid interface
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

        return $this->addUsingAlias(LockPeer::CKEY, $ckey, $comparison);
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
     * @return LockQuery The current query, for fluid interface
     */
    public function filterBySessionId($sessionId = null, $comparison = null)
    {
        if (is_array($sessionId)) {
            $useMinMax = false;
            if (isset($sessionId['min'])) {
                $this->addUsingAlias(LockPeer::SESSION_ID, $sessionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sessionId['max'])) {
                $this->addUsingAlias(LockPeer::SESSION_ID, $sessionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LockPeer::SESSION_ID, $sessionId, $comparison);
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
     * @return LockQuery The current query, for fluid interface
     */
    public function filterByTime($time = null, $comparison = null)
    {
        if (is_array($time)) {
            $useMinMax = false;
            if (isset($time['min'])) {
                $this->addUsingAlias(LockPeer::TIME, $time['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($time['max'])) {
                $this->addUsingAlias(LockPeer::TIME, $time['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LockPeer::TIME, $time, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Lock $lock Object to remove from the list of results
     *
     * @return LockQuery The current query, for fluid interface
     */
    public function prune($lock = null)
    {
        if ($lock) {
            $this->addUsingAlias(LockPeer::ID, $lock->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseLockQuery
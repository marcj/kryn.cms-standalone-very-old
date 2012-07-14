<?php


/**
 * Base class that represents a query for the 'kryn_system_frameworkversion' table.
 *
 * 
 *
 * @method     FrameworkversionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     FrameworkversionQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     FrameworkversionQuery orderByContent($order = Criteria::ASC) Order by the content column
 * @method     FrameworkversionQuery orderByVersion($order = Criteria::ASC) Order by the version column
 * @method     FrameworkversionQuery orderByCdate($order = Criteria::ASC) Order by the cdate column
 * @method     FrameworkversionQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 *
 * @method     FrameworkversionQuery groupById() Group by the id column
 * @method     FrameworkversionQuery groupByCode() Group by the code column
 * @method     FrameworkversionQuery groupByContent() Group by the content column
 * @method     FrameworkversionQuery groupByVersion() Group by the version column
 * @method     FrameworkversionQuery groupByCdate() Group by the cdate column
 * @method     FrameworkversionQuery groupByUserId() Group by the user_id column
 *
 * @method     FrameworkversionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     FrameworkversionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     FrameworkversionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     Frameworkversion findOne(PropelPDO $con = null) Return the first Frameworkversion matching the query
 * @method     Frameworkversion findOneOrCreate(PropelPDO $con = null) Return the first Frameworkversion matching the query, or a new Frameworkversion object populated from the query conditions when no match is found
 *
 * @method     Frameworkversion findOneById(int $id) Return the first Frameworkversion filtered by the id column
 * @method     Frameworkversion findOneByCode(string $code) Return the first Frameworkversion filtered by the code column
 * @method     Frameworkversion findOneByContent(string $content) Return the first Frameworkversion filtered by the content column
 * @method     Frameworkversion findOneByVersion(int $version) Return the first Frameworkversion filtered by the version column
 * @method     Frameworkversion findOneByCdate(int $cdate) Return the first Frameworkversion filtered by the cdate column
 * @method     Frameworkversion findOneByUserId(int $user_id) Return the first Frameworkversion filtered by the user_id column
 *
 * @method     array findById(int $id) Return Frameworkversion objects filtered by the id column
 * @method     array findByCode(string $code) Return Frameworkversion objects filtered by the code column
 * @method     array findByContent(string $content) Return Frameworkversion objects filtered by the content column
 * @method     array findByVersion(int $version) Return Frameworkversion objects filtered by the version column
 * @method     array findByCdate(int $cdate) Return Frameworkversion objects filtered by the cdate column
 * @method     array findByUserId(int $user_id) Return Frameworkversion objects filtered by the user_id column
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BaseFrameworkversionQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseFrameworkversionQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Kryn', $modelName = 'Frameworkversion', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new FrameworkversionQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     FrameworkversionQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return FrameworkversionQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof FrameworkversionQuery) {
            return $criteria;
        }
        $query = new FrameworkversionQuery();
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
     * @return   Frameworkversion|Frameworkversion[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = FrameworkversionPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(FrameworkversionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Frameworkversion A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, CODE, CONTENT, VERSION, CDATE, USER_ID FROM kryn_system_frameworkversion WHERE ID = :p0';
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
            $obj = new Frameworkversion();
            $obj->hydrate($row);
            FrameworkversionPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Frameworkversion|Frameworkversion[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Frameworkversion[]|mixed the list of results, formatted by the current formatter
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
     * @return FrameworkversionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(FrameworkversionPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return FrameworkversionQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(FrameworkversionPeer::ID, $keys, Criteria::IN);
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
     * @return FrameworkversionQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(FrameworkversionPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the code column
     *
     * Example usage:
     * <code>
     * $query->filterByCode('fooValue');   // WHERE code = 'fooValue'
     * $query->filterByCode('%fooValue%'); // WHERE code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $code The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FrameworkversionQuery The current query, for fluid interface
     */
    public function filterByCode($code = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($code)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $code)) {
                $code = str_replace('*', '%', $code);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FrameworkversionPeer::CODE, $code, $comparison);
    }

    /**
     * Filter the query on the content column
     *
     * Example usage:
     * <code>
     * $query->filterByContent('fooValue');   // WHERE content = 'fooValue'
     * $query->filterByContent('%fooValue%'); // WHERE content LIKE '%fooValue%'
     * </code>
     *
     * @param     string $content The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FrameworkversionQuery The current query, for fluid interface
     */
    public function filterByContent($content = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($content)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $content)) {
                $content = str_replace('*', '%', $content);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(FrameworkversionPeer::CONTENT, $content, $comparison);
    }

    /**
     * Filter the query on the version column
     *
     * Example usage:
     * <code>
     * $query->filterByVersion(1234); // WHERE version = 1234
     * $query->filterByVersion(array(12, 34)); // WHERE version IN (12, 34)
     * $query->filterByVersion(array('min' => 12)); // WHERE version > 12
     * </code>
     *
     * @param     mixed $version The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FrameworkversionQuery The current query, for fluid interface
     */
    public function filterByVersion($version = null, $comparison = null)
    {
        if (is_array($version)) {
            $useMinMax = false;
            if (isset($version['min'])) {
                $this->addUsingAlias(FrameworkversionPeer::VERSION, $version['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($version['max'])) {
                $this->addUsingAlias(FrameworkversionPeer::VERSION, $version['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FrameworkversionPeer::VERSION, $version, $comparison);
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
     * @return FrameworkversionQuery The current query, for fluid interface
     */
    public function filterByCdate($cdate = null, $comparison = null)
    {
        if (is_array($cdate)) {
            $useMinMax = false;
            if (isset($cdate['min'])) {
                $this->addUsingAlias(FrameworkversionPeer::CDATE, $cdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cdate['max'])) {
                $this->addUsingAlias(FrameworkversionPeer::CDATE, $cdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FrameworkversionPeer::CDATE, $cdate, $comparison);
    }

    /**
     * Filter the query on the user_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUserId(1234); // WHERE user_id = 1234
     * $query->filterByUserId(array(12, 34)); // WHERE user_id IN (12, 34)
     * $query->filterByUserId(array('min' => 12)); // WHERE user_id > 12
     * </code>
     *
     * @param     mixed $userId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return FrameworkversionQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(FrameworkversionPeer::USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(FrameworkversionPeer::USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(FrameworkversionPeer::USER_ID, $userId, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Frameworkversion $frameworkversion Object to remove from the list of results
     *
     * @return FrameworkversionQuery The current query, for fluid interface
     */
    public function prune($frameworkversion = null)
    {
        if ($frameworkversion) {
            $this->addUsingAlias(FrameworkversionPeer::ID, $frameworkversion->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseFrameworkversionQuery
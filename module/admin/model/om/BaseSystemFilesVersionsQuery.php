<?php


/**
 * Base class that represents a query for the 'kryn_system_files_versions' table.
 *
 * 
 *
 * @method     SystemFilesVersionsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     SystemFilesVersionsQuery orderByPath($order = Criteria::ASC) Order by the path column
 * @method     SystemFilesVersionsQuery orderByCreated($order = Criteria::ASC) Order by the created column
 * @method     SystemFilesVersionsQuery orderByMtime($order = Criteria::ASC) Order by the mtime column
 * @method     SystemFilesVersionsQuery orderByUserId($order = Criteria::ASC) Order by the user_id column
 * @method     SystemFilesVersionsQuery orderByVersionpath($order = Criteria::ASC) Order by the versionpath column
 *
 * @method     SystemFilesVersionsQuery groupById() Group by the id column
 * @method     SystemFilesVersionsQuery groupByPath() Group by the path column
 * @method     SystemFilesVersionsQuery groupByCreated() Group by the created column
 * @method     SystemFilesVersionsQuery groupByMtime() Group by the mtime column
 * @method     SystemFilesVersionsQuery groupByUserId() Group by the user_id column
 * @method     SystemFilesVersionsQuery groupByVersionpath() Group by the versionpath column
 *
 * @method     SystemFilesVersionsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SystemFilesVersionsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SystemFilesVersionsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SystemFilesVersions findOne(PropelPDO $con = null) Return the first SystemFilesVersions matching the query
 * @method     SystemFilesVersions findOneOrCreate(PropelPDO $con = null) Return the first SystemFilesVersions matching the query, or a new SystemFilesVersions object populated from the query conditions when no match is found
 *
 * @method     SystemFilesVersions findOneById(int $id) Return the first SystemFilesVersions filtered by the id column
 * @method     SystemFilesVersions findOneByPath(string $path) Return the first SystemFilesVersions filtered by the path column
 * @method     SystemFilesVersions findOneByCreated(int $created) Return the first SystemFilesVersions filtered by the created column
 * @method     SystemFilesVersions findOneByMtime(int $mtime) Return the first SystemFilesVersions filtered by the mtime column
 * @method     SystemFilesVersions findOneByUserId(int $user_id) Return the first SystemFilesVersions filtered by the user_id column
 * @method     SystemFilesVersions findOneByVersionpath(string $versionpath) Return the first SystemFilesVersions filtered by the versionpath column
 *
 * @method     array findById(int $id) Return SystemFilesVersions objects filtered by the id column
 * @method     array findByPath(string $path) Return SystemFilesVersions objects filtered by the path column
 * @method     array findByCreated(int $created) Return SystemFilesVersions objects filtered by the created column
 * @method     array findByMtime(int $mtime) Return SystemFilesVersions objects filtered by the mtime column
 * @method     array findByUserId(int $user_id) Return SystemFilesVersions objects filtered by the user_id column
 * @method     array findByVersionpath(string $versionpath) Return SystemFilesVersions objects filtered by the versionpath column
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemFilesVersionsQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseSystemFilesVersionsQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'kryn', $modelName = 'SystemFilesVersions', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SystemFilesVersionsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     SystemFilesVersionsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SystemFilesVersionsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SystemFilesVersionsQuery) {
            return $criteria;
        }
        $query = new SystemFilesVersionsQuery();
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
     * @return   SystemFilesVersions|SystemFilesVersions[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SystemFilesVersionsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SystemFilesVersionsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   SystemFilesVersions A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PATH, CREATED, MTIME, USER_ID, VERSIONPATH FROM kryn_system_files_versions WHERE ID = :p0';
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
            $obj = new SystemFilesVersions();
            $obj->hydrate($row);
            SystemFilesVersionsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return SystemFilesVersions|SystemFilesVersions[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|SystemFilesVersions[]|mixed the list of results, formatted by the current formatter
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
     * @return SystemFilesVersionsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SystemFilesVersionsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SystemFilesVersionsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SystemFilesVersionsPeer::ID, $keys, Criteria::IN);
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
     * @return SystemFilesVersionsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(SystemFilesVersionsPeer::ID, $id, $comparison);
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
     * @return SystemFilesVersionsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemFilesVersionsPeer::PATH, $path, $comparison);
    }

    /**
     * Filter the query on the created column
     *
     * Example usage:
     * <code>
     * $query->filterByCreated(1234); // WHERE created = 1234
     * $query->filterByCreated(array(12, 34)); // WHERE created IN (12, 34)
     * $query->filterByCreated(array('min' => 12)); // WHERE created > 12
     * </code>
     *
     * @param     mixed $created The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemFilesVersionsQuery The current query, for fluid interface
     */
    public function filterByCreated($created = null, $comparison = null)
    {
        if (is_array($created)) {
            $useMinMax = false;
            if (isset($created['min'])) {
                $this->addUsingAlias(SystemFilesVersionsPeer::CREATED, $created['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($created['max'])) {
                $this->addUsingAlias(SystemFilesVersionsPeer::CREATED, $created['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemFilesVersionsPeer::CREATED, $created, $comparison);
    }

    /**
     * Filter the query on the mtime column
     *
     * Example usage:
     * <code>
     * $query->filterByMtime(1234); // WHERE mtime = 1234
     * $query->filterByMtime(array(12, 34)); // WHERE mtime IN (12, 34)
     * $query->filterByMtime(array('min' => 12)); // WHERE mtime > 12
     * </code>
     *
     * @param     mixed $mtime The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemFilesVersionsQuery The current query, for fluid interface
     */
    public function filterByMtime($mtime = null, $comparison = null)
    {
        if (is_array($mtime)) {
            $useMinMax = false;
            if (isset($mtime['min'])) {
                $this->addUsingAlias(SystemFilesVersionsPeer::MTIME, $mtime['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mtime['max'])) {
                $this->addUsingAlias(SystemFilesVersionsPeer::MTIME, $mtime['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemFilesVersionsPeer::MTIME, $mtime, $comparison);
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
     * @return SystemFilesVersionsQuery The current query, for fluid interface
     */
    public function filterByUserId($userId = null, $comparison = null)
    {
        if (is_array($userId)) {
            $useMinMax = false;
            if (isset($userId['min'])) {
                $this->addUsingAlias(SystemFilesVersionsPeer::USER_ID, $userId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userId['max'])) {
                $this->addUsingAlias(SystemFilesVersionsPeer::USER_ID, $userId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemFilesVersionsPeer::USER_ID, $userId, $comparison);
    }

    /**
     * Filter the query on the versionpath column
     *
     * Example usage:
     * <code>
     * $query->filterByVersionpath('fooValue');   // WHERE versionpath = 'fooValue'
     * $query->filterByVersionpath('%fooValue%'); // WHERE versionpath LIKE '%fooValue%'
     * </code>
     *
     * @param     string $versionpath The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemFilesVersionsQuery The current query, for fluid interface
     */
    public function filterByVersionpath($versionpath = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($versionpath)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $versionpath)) {
                $versionpath = str_replace('*', '%', $versionpath);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemFilesVersionsPeer::VERSIONPATH, $versionpath, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   SystemFilesVersions $systemFilesVersions Object to remove from the list of results
     *
     * @return SystemFilesVersionsQuery The current query, for fluid interface
     */
    public function prune($systemFilesVersions = null)
    {
        if ($systemFilesVersions) {
            $this->addUsingAlias(SystemFilesVersionsPeer::ID, $systemFilesVersions->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseSystemFilesVersionsQuery
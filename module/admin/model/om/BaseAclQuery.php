<?php


/**
 * Base class that represents a query for the 'kryn_system_acl' table.
 *
 * 
 *
 * @method     AclQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     AclQuery orderByObject($order = Criteria::ASC) Order by the object column
 * @method     AclQuery orderByTargetType($order = Criteria::ASC) Order by the target_type column
 * @method     AclQuery orderByTargetId($order = Criteria::ASC) Order by the target_id column
 * @method     AclQuery orderBySub($order = Criteria::ASC) Order by the sub column
 * @method     AclQuery orderByFields($order = Criteria::ASC) Order by the fields column
 * @method     AclQuery orderByAccess($order = Criteria::ASC) Order by the access column
 * @method     AclQuery orderByPrio($order = Criteria::ASC) Order by the prio column
 * @method     AclQuery orderByMode($order = Criteria::ASC) Order by the mode column
 * @method     AclQuery orderByConstraintType($order = Criteria::ASC) Order by the constraint_type column
 * @method     AclQuery orderByConstraintCode($order = Criteria::ASC) Order by the constraint_code column
 *
 * @method     AclQuery groupById() Group by the id column
 * @method     AclQuery groupByObject() Group by the object column
 * @method     AclQuery groupByTargetType() Group by the target_type column
 * @method     AclQuery groupByTargetId() Group by the target_id column
 * @method     AclQuery groupBySub() Group by the sub column
 * @method     AclQuery groupByFields() Group by the fields column
 * @method     AclQuery groupByAccess() Group by the access column
 * @method     AclQuery groupByPrio() Group by the prio column
 * @method     AclQuery groupByMode() Group by the mode column
 * @method     AclQuery groupByConstraintType() Group by the constraint_type column
 * @method     AclQuery groupByConstraintCode() Group by the constraint_code column
 *
 * @method     AclQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     AclQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     AclQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     Acl findOne(PropelPDO $con = null) Return the first Acl matching the query
 * @method     Acl findOneOrCreate(PropelPDO $con = null) Return the first Acl matching the query, or a new Acl object populated from the query conditions when no match is found
 *
 * @method     Acl findOneById(int $id) Return the first Acl filtered by the id column
 * @method     Acl findOneByObject(string $object) Return the first Acl filtered by the object column
 * @method     Acl findOneByTargetType(int $target_type) Return the first Acl filtered by the target_type column
 * @method     Acl findOneByTargetId(int $target_id) Return the first Acl filtered by the target_id column
 * @method     Acl findOneBySub(int $sub) Return the first Acl filtered by the sub column
 * @method     Acl findOneByFields(string $fields) Return the first Acl filtered by the fields column
 * @method     Acl findOneByAccess(int $access) Return the first Acl filtered by the access column
 * @method     Acl findOneByPrio(int $prio) Return the first Acl filtered by the prio column
 * @method     Acl findOneByMode(int $mode) Return the first Acl filtered by the mode column
 * @method     Acl findOneByConstraintType(int $constraint_type) Return the first Acl filtered by the constraint_type column
 * @method     Acl findOneByConstraintCode(string $constraint_code) Return the first Acl filtered by the constraint_code column
 *
 * @method     array findById(int $id) Return Acl objects filtered by the id column
 * @method     array findByObject(string $object) Return Acl objects filtered by the object column
 * @method     array findByTargetType(int $target_type) Return Acl objects filtered by the target_type column
 * @method     array findByTargetId(int $target_id) Return Acl objects filtered by the target_id column
 * @method     array findBySub(int $sub) Return Acl objects filtered by the sub column
 * @method     array findByFields(string $fields) Return Acl objects filtered by the fields column
 * @method     array findByAccess(int $access) Return Acl objects filtered by the access column
 * @method     array findByPrio(int $prio) Return Acl objects filtered by the prio column
 * @method     array findByMode(int $mode) Return Acl objects filtered by the mode column
 * @method     array findByConstraintType(int $constraint_type) Return Acl objects filtered by the constraint_type column
 * @method     array findByConstraintCode(string $constraint_code) Return Acl objects filtered by the constraint_code column
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BaseAclQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseAclQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Kryn', $modelName = 'Acl', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new AclQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     AclQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return AclQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof AclQuery) {
            return $criteria;
        }
        $query = new AclQuery();
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
     * @return   Acl|Acl[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = AclPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(AclPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Acl A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, OBJECT, TARGET_TYPE, TARGET_ID, SUB, FIELDS, ACCESS, PRIO, MODE, CONSTRAINT_TYPE, CONSTRAINT_CODE FROM kryn_system_acl WHERE ID = :p0';
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
            $obj = new Acl();
            $obj->hydrate($row);
            AclPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Acl|Acl[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Acl[]|mixed the list of results, formatted by the current formatter
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
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(AclPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(AclPeer::ID, $keys, Criteria::IN);
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
     * @return AclQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(AclPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the object column
     *
     * Example usage:
     * <code>
     * $query->filterByObject('fooValue');   // WHERE object = 'fooValue'
     * $query->filterByObject('%fooValue%'); // WHERE object LIKE '%fooValue%'
     * </code>
     *
     * @param     string $object The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByObject($object = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($object)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $object)) {
                $object = str_replace('*', '%', $object);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AclPeer::OBJECT, $object, $comparison);
    }

    /**
     * Filter the query on the target_type column
     *
     * Example usage:
     * <code>
     * $query->filterByTargetType(1234); // WHERE target_type = 1234
     * $query->filterByTargetType(array(12, 34)); // WHERE target_type IN (12, 34)
     * $query->filterByTargetType(array('min' => 12)); // WHERE target_type > 12
     * </code>
     *
     * @param     mixed $targetType The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByTargetType($targetType = null, $comparison = null)
    {
        if (is_array($targetType)) {
            $useMinMax = false;
            if (isset($targetType['min'])) {
                $this->addUsingAlias(AclPeer::TARGET_TYPE, $targetType['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($targetType['max'])) {
                $this->addUsingAlias(AclPeer::TARGET_TYPE, $targetType['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclPeer::TARGET_TYPE, $targetType, $comparison);
    }

    /**
     * Filter the query on the target_id column
     *
     * Example usage:
     * <code>
     * $query->filterByTargetId(1234); // WHERE target_id = 1234
     * $query->filterByTargetId(array(12, 34)); // WHERE target_id IN (12, 34)
     * $query->filterByTargetId(array('min' => 12)); // WHERE target_id > 12
     * </code>
     *
     * @param     mixed $targetId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByTargetId($targetId = null, $comparison = null)
    {
        if (is_array($targetId)) {
            $useMinMax = false;
            if (isset($targetId['min'])) {
                $this->addUsingAlias(AclPeer::TARGET_ID, $targetId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($targetId['max'])) {
                $this->addUsingAlias(AclPeer::TARGET_ID, $targetId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclPeer::TARGET_ID, $targetId, $comparison);
    }

    /**
     * Filter the query on the sub column
     *
     * Example usage:
     * <code>
     * $query->filterBySub(1234); // WHERE sub = 1234
     * $query->filterBySub(array(12, 34)); // WHERE sub IN (12, 34)
     * $query->filterBySub(array('min' => 12)); // WHERE sub > 12
     * </code>
     *
     * @param     mixed $sub The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterBySub($sub = null, $comparison = null)
    {
        if (is_array($sub)) {
            $useMinMax = false;
            if (isset($sub['min'])) {
                $this->addUsingAlias(AclPeer::SUB, $sub['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sub['max'])) {
                $this->addUsingAlias(AclPeer::SUB, $sub['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclPeer::SUB, $sub, $comparison);
    }

    /**
     * Filter the query on the fields column
     *
     * Example usage:
     * <code>
     * $query->filterByFields('fooValue');   // WHERE fields = 'fooValue'
     * $query->filterByFields('%fooValue%'); // WHERE fields LIKE '%fooValue%'
     * </code>
     *
     * @param     string $fields The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByFields($fields = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($fields)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $fields)) {
                $fields = str_replace('*', '%', $fields);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AclPeer::FIELDS, $fields, $comparison);
    }

    /**
     * Filter the query on the access column
     *
     * Example usage:
     * <code>
     * $query->filterByAccess(1234); // WHERE access = 1234
     * $query->filterByAccess(array(12, 34)); // WHERE access IN (12, 34)
     * $query->filterByAccess(array('min' => 12)); // WHERE access > 12
     * </code>
     *
     * @param     mixed $access The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByAccess($access = null, $comparison = null)
    {
        if (is_array($access)) {
            $useMinMax = false;
            if (isset($access['min'])) {
                $this->addUsingAlias(AclPeer::ACCESS, $access['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($access['max'])) {
                $this->addUsingAlias(AclPeer::ACCESS, $access['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclPeer::ACCESS, $access, $comparison);
    }

    /**
     * Filter the query on the prio column
     *
     * Example usage:
     * <code>
     * $query->filterByPrio(1234); // WHERE prio = 1234
     * $query->filterByPrio(array(12, 34)); // WHERE prio IN (12, 34)
     * $query->filterByPrio(array('min' => 12)); // WHERE prio > 12
     * </code>
     *
     * @param     mixed $prio The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByPrio($prio = null, $comparison = null)
    {
        if (is_array($prio)) {
            $useMinMax = false;
            if (isset($prio['min'])) {
                $this->addUsingAlias(AclPeer::PRIO, $prio['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($prio['max'])) {
                $this->addUsingAlias(AclPeer::PRIO, $prio['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclPeer::PRIO, $prio, $comparison);
    }

    /**
     * Filter the query on the mode column
     *
     * Example usage:
     * <code>
     * $query->filterByMode(1234); // WHERE mode = 1234
     * $query->filterByMode(array(12, 34)); // WHERE mode IN (12, 34)
     * $query->filterByMode(array('min' => 12)); // WHERE mode > 12
     * </code>
     *
     * @param     mixed $mode The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByMode($mode = null, $comparison = null)
    {
        if (is_array($mode)) {
            $useMinMax = false;
            if (isset($mode['min'])) {
                $this->addUsingAlias(AclPeer::MODE, $mode['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mode['max'])) {
                $this->addUsingAlias(AclPeer::MODE, $mode['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclPeer::MODE, $mode, $comparison);
    }

    /**
     * Filter the query on the constraint_type column
     *
     * Example usage:
     * <code>
     * $query->filterByConstraintType(1234); // WHERE constraint_type = 1234
     * $query->filterByConstraintType(array(12, 34)); // WHERE constraint_type IN (12, 34)
     * $query->filterByConstraintType(array('min' => 12)); // WHERE constraint_type > 12
     * </code>
     *
     * @param     mixed $constraintType The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByConstraintType($constraintType = null, $comparison = null)
    {
        if (is_array($constraintType)) {
            $useMinMax = false;
            if (isset($constraintType['min'])) {
                $this->addUsingAlias(AclPeer::CONSTRAINT_TYPE, $constraintType['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($constraintType['max'])) {
                $this->addUsingAlias(AclPeer::CONSTRAINT_TYPE, $constraintType['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(AclPeer::CONSTRAINT_TYPE, $constraintType, $comparison);
    }

    /**
     * Filter the query on the constraint_code column
     *
     * Example usage:
     * <code>
     * $query->filterByConstraintCode('fooValue');   // WHERE constraint_code = 'fooValue'
     * $query->filterByConstraintCode('%fooValue%'); // WHERE constraint_code LIKE '%fooValue%'
     * </code>
     *
     * @param     string $constraintCode The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function filterByConstraintCode($constraintCode = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($constraintCode)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $constraintCode)) {
                $constraintCode = str_replace('*', '%', $constraintCode);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(AclPeer::CONSTRAINT_CODE, $constraintCode, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Acl $acl Object to remove from the list of results
     *
     * @return AclQuery The current query, for fluid interface
     */
    public function prune($acl = null)
    {
        if ($acl) {
            $this->addUsingAlias(AclPeer::ID, $acl->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseAclQuery
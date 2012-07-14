<?php


/**
 * Base class that represents a query for the 'kryn_system_langs' table.
 *
 * 
 *
 * @method     LangsQuery orderByCode($order = Criteria::ASC) Order by the code column
 * @method     LangsQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     LangsQuery orderByLangtitle($order = Criteria::ASC) Order by the langtitle column
 * @method     LangsQuery orderByUserdefined($order = Criteria::ASC) Order by the userdefined column
 * @method     LangsQuery orderByVisible($order = Criteria::ASC) Order by the visible column
 *
 * @method     LangsQuery groupByCode() Group by the code column
 * @method     LangsQuery groupByTitle() Group by the title column
 * @method     LangsQuery groupByLangtitle() Group by the langtitle column
 * @method     LangsQuery groupByUserdefined() Group by the userdefined column
 * @method     LangsQuery groupByVisible() Group by the visible column
 *
 * @method     LangsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     LangsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     LangsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     Langs findOne(PropelPDO $con = null) Return the first Langs matching the query
 * @method     Langs findOneOrCreate(PropelPDO $con = null) Return the first Langs matching the query, or a new Langs object populated from the query conditions when no match is found
 *
 * @method     Langs findOneByCode(string $code) Return the first Langs filtered by the code column
 * @method     Langs findOneByTitle(string $title) Return the first Langs filtered by the title column
 * @method     Langs findOneByLangtitle(string $langtitle) Return the first Langs filtered by the langtitle column
 * @method     Langs findOneByUserdefined(int $userdefined) Return the first Langs filtered by the userdefined column
 * @method     Langs findOneByVisible(int $visible) Return the first Langs filtered by the visible column
 *
 * @method     array findByCode(string $code) Return Langs objects filtered by the code column
 * @method     array findByTitle(string $title) Return Langs objects filtered by the title column
 * @method     array findByLangtitle(string $langtitle) Return Langs objects filtered by the langtitle column
 * @method     array findByUserdefined(int $userdefined) Return Langs objects filtered by the userdefined column
 * @method     array findByVisible(int $visible) Return Langs objects filtered by the visible column
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BaseLangsQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseLangsQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Kryn', $modelName = 'Langs', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new LangsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     LangsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return LangsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof LangsQuery) {
            return $criteria;
        }
        $query = new LangsQuery();
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
     * @return   Langs|Langs[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = LangsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(LangsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   Langs A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT CODE, TITLE, LANGTITLE, USERDEFINED, VISIBLE FROM kryn_system_langs WHERE CODE = :p0';
        try {
            $stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Langs();
            $obj->hydrate($row);
            LangsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return Langs|Langs[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|Langs[]|mixed the list of results, formatted by the current formatter
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
     * @return LangsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LangsPeer::CODE, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return LangsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LangsPeer::CODE, $keys, Criteria::IN);
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
     * @return LangsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(LangsPeer::CODE, $code, $comparison);
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
     * @return LangsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(LangsPeer::TITLE, $title, $comparison);
    }

    /**
     * Filter the query on the langtitle column
     *
     * Example usage:
     * <code>
     * $query->filterByLangtitle('fooValue');   // WHERE langtitle = 'fooValue'
     * $query->filterByLangtitle('%fooValue%'); // WHERE langtitle LIKE '%fooValue%'
     * </code>
     *
     * @param     string $langtitle The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LangsQuery The current query, for fluid interface
     */
    public function filterByLangtitle($langtitle = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($langtitle)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $langtitle)) {
                $langtitle = str_replace('*', '%', $langtitle);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(LangsPeer::LANGTITLE, $langtitle, $comparison);
    }

    /**
     * Filter the query on the userdefined column
     *
     * Example usage:
     * <code>
     * $query->filterByUserdefined(1234); // WHERE userdefined = 1234
     * $query->filterByUserdefined(array(12, 34)); // WHERE userdefined IN (12, 34)
     * $query->filterByUserdefined(array('min' => 12)); // WHERE userdefined > 12
     * </code>
     *
     * @param     mixed $userdefined The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return LangsQuery The current query, for fluid interface
     */
    public function filterByUserdefined($userdefined = null, $comparison = null)
    {
        if (is_array($userdefined)) {
            $useMinMax = false;
            if (isset($userdefined['min'])) {
                $this->addUsingAlias(LangsPeer::USERDEFINED, $userdefined['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($userdefined['max'])) {
                $this->addUsingAlias(LangsPeer::USERDEFINED, $userdefined['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LangsPeer::USERDEFINED, $userdefined, $comparison);
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
     * @return LangsQuery The current query, for fluid interface
     */
    public function filterByVisible($visible = null, $comparison = null)
    {
        if (is_array($visible)) {
            $useMinMax = false;
            if (isset($visible['min'])) {
                $this->addUsingAlias(LangsPeer::VISIBLE, $visible['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($visible['max'])) {
                $this->addUsingAlias(LangsPeer::VISIBLE, $visible['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LangsPeer::VISIBLE, $visible, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   Langs $langs Object to remove from the list of results
     *
     * @return LangsQuery The current query, for fluid interface
     */
    public function prune($langs = null)
    {
        if ($langs) {
            $this->addUsingAlias(LangsPeer::CODE, $langs->getCode(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseLangsQuery
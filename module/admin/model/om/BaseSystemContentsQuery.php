<?php


/**
 * Base class that represents a query for the 'kryn_system_contents' table.
 *
 * 
 *
 * @method     SystemContentsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     SystemContentsQuery orderByPageId($order = Criteria::ASC) Order by the page_id column
 * @method     SystemContentsQuery orderByVersionId($order = Criteria::ASC) Order by the version_id column
 * @method     SystemContentsQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     SystemContentsQuery orderByContent($order = Criteria::ASC) Order by the content column
 * @method     SystemContentsQuery orderByTemplate($order = Criteria::ASC) Order by the template column
 * @method     SystemContentsQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     SystemContentsQuery orderByMdate($order = Criteria::ASC) Order by the mdate column
 * @method     SystemContentsQuery orderByCdate($order = Criteria::ASC) Order by the cdate column
 * @method     SystemContentsQuery orderByHide($order = Criteria::ASC) Order by the hide column
 * @method     SystemContentsQuery orderBySort($order = Criteria::ASC) Order by the sort column
 * @method     SystemContentsQuery orderByBoxId($order = Criteria::ASC) Order by the box_id column
 * @method     SystemContentsQuery orderByOwnerId($order = Criteria::ASC) Order by the owner_id column
 * @method     SystemContentsQuery orderByAccessFrom($order = Criteria::ASC) Order by the access_from column
 * @method     SystemContentsQuery orderByAccessTo($order = Criteria::ASC) Order by the access_to column
 * @method     SystemContentsQuery orderByAccessFromGroups($order = Criteria::ASC) Order by the access_from_groups column
 * @method     SystemContentsQuery orderByUnsearchable($order = Criteria::ASC) Order by the unsearchable column
 *
 * @method     SystemContentsQuery groupById() Group by the id column
 * @method     SystemContentsQuery groupByPageId() Group by the page_id column
 * @method     SystemContentsQuery groupByVersionId() Group by the version_id column
 * @method     SystemContentsQuery groupByTitle() Group by the title column
 * @method     SystemContentsQuery groupByContent() Group by the content column
 * @method     SystemContentsQuery groupByTemplate() Group by the template column
 * @method     SystemContentsQuery groupByType() Group by the type column
 * @method     SystemContentsQuery groupByMdate() Group by the mdate column
 * @method     SystemContentsQuery groupByCdate() Group by the cdate column
 * @method     SystemContentsQuery groupByHide() Group by the hide column
 * @method     SystemContentsQuery groupBySort() Group by the sort column
 * @method     SystemContentsQuery groupByBoxId() Group by the box_id column
 * @method     SystemContentsQuery groupByOwnerId() Group by the owner_id column
 * @method     SystemContentsQuery groupByAccessFrom() Group by the access_from column
 * @method     SystemContentsQuery groupByAccessTo() Group by the access_to column
 * @method     SystemContentsQuery groupByAccessFromGroups() Group by the access_from_groups column
 * @method     SystemContentsQuery groupByUnsearchable() Group by the unsearchable column
 *
 * @method     SystemContentsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SystemContentsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SystemContentsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SystemContents findOne(PropelPDO $con = null) Return the first SystemContents matching the query
 * @method     SystemContents findOneOrCreate(PropelPDO $con = null) Return the first SystemContents matching the query, or a new SystemContents object populated from the query conditions when no match is found
 *
 * @method     SystemContents findOneById(int $id) Return the first SystemContents filtered by the id column
 * @method     SystemContents findOneByPageId(int $page_id) Return the first SystemContents filtered by the page_id column
 * @method     SystemContents findOneByVersionId(int $version_id) Return the first SystemContents filtered by the version_id column
 * @method     SystemContents findOneByTitle(string $title) Return the first SystemContents filtered by the title column
 * @method     SystemContents findOneByContent(string $content) Return the first SystemContents filtered by the content column
 * @method     SystemContents findOneByTemplate(string $template) Return the first SystemContents filtered by the template column
 * @method     SystemContents findOneByType(string $type) Return the first SystemContents filtered by the type column
 * @method     SystemContents findOneByMdate(int $mdate) Return the first SystemContents filtered by the mdate column
 * @method     SystemContents findOneByCdate(int $cdate) Return the first SystemContents filtered by the cdate column
 * @method     SystemContents findOneByHide(int $hide) Return the first SystemContents filtered by the hide column
 * @method     SystemContents findOneBySort(int $sort) Return the first SystemContents filtered by the sort column
 * @method     SystemContents findOneByBoxId(int $box_id) Return the first SystemContents filtered by the box_id column
 * @method     SystemContents findOneByOwnerId(int $owner_id) Return the first SystemContents filtered by the owner_id column
 * @method     SystemContents findOneByAccessFrom(int $access_from) Return the first SystemContents filtered by the access_from column
 * @method     SystemContents findOneByAccessTo(int $access_to) Return the first SystemContents filtered by the access_to column
 * @method     SystemContents findOneByAccessFromGroups(string $access_from_groups) Return the first SystemContents filtered by the access_from_groups column
 * @method     SystemContents findOneByUnsearchable(int $unsearchable) Return the first SystemContents filtered by the unsearchable column
 *
 * @method     array findById(int $id) Return SystemContents objects filtered by the id column
 * @method     array findByPageId(int $page_id) Return SystemContents objects filtered by the page_id column
 * @method     array findByVersionId(int $version_id) Return SystemContents objects filtered by the version_id column
 * @method     array findByTitle(string $title) Return SystemContents objects filtered by the title column
 * @method     array findByContent(string $content) Return SystemContents objects filtered by the content column
 * @method     array findByTemplate(string $template) Return SystemContents objects filtered by the template column
 * @method     array findByType(string $type) Return SystemContents objects filtered by the type column
 * @method     array findByMdate(int $mdate) Return SystemContents objects filtered by the mdate column
 * @method     array findByCdate(int $cdate) Return SystemContents objects filtered by the cdate column
 * @method     array findByHide(int $hide) Return SystemContents objects filtered by the hide column
 * @method     array findBySort(int $sort) Return SystemContents objects filtered by the sort column
 * @method     array findByBoxId(int $box_id) Return SystemContents objects filtered by the box_id column
 * @method     array findByOwnerId(int $owner_id) Return SystemContents objects filtered by the owner_id column
 * @method     array findByAccessFrom(int $access_from) Return SystemContents objects filtered by the access_from column
 * @method     array findByAccessTo(int $access_to) Return SystemContents objects filtered by the access_to column
 * @method     array findByAccessFromGroups(string $access_from_groups) Return SystemContents objects filtered by the access_from_groups column
 * @method     array findByUnsearchable(int $unsearchable) Return SystemContents objects filtered by the unsearchable column
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemContentsQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseSystemContentsQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'kryn', $modelName = 'SystemContents', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new SystemContentsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     SystemContentsQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return SystemContentsQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof SystemContentsQuery) {
            return $criteria;
        }
        $query = new SystemContentsQuery();
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
     * @return   SystemContents|SystemContents[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = SystemContentsPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(SystemContentsPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   SystemContents A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, PAGE_ID, VERSION_ID, TITLE, CONTENT, TEMPLATE, TYPE, MDATE, CDATE, HIDE, SORT, BOX_ID, OWNER_ID, ACCESS_FROM, ACCESS_TO, ACCESS_FROM_GROUPS, UNSEARCHABLE FROM kryn_system_contents WHERE ID = :p0';
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
            $obj = new SystemContents();
            $obj->hydrate($row);
            SystemContentsPeer::addInstanceToPool($obj, (string) $key);
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
     * @return SystemContents|SystemContents[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|SystemContents[]|mixed the list of results, formatted by the current formatter
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
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SystemContentsPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SystemContentsPeer::ID, $keys, Criteria::IN);
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
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(SystemContentsPeer::ID, $id, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByPageId($pageId = null, $comparison = null)
    {
        if (is_array($pageId)) {
            $useMinMax = false;
            if (isset($pageId['min'])) {
                $this->addUsingAlias(SystemContentsPeer::PAGE_ID, $pageId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($pageId['max'])) {
                $this->addUsingAlias(SystemContentsPeer::PAGE_ID, $pageId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::PAGE_ID, $pageId, $comparison);
    }

    /**
     * Filter the query on the version_id column
     *
     * Example usage:
     * <code>
     * $query->filterByVersionId(1234); // WHERE version_id = 1234
     * $query->filterByVersionId(array(12, 34)); // WHERE version_id IN (12, 34)
     * $query->filterByVersionId(array('min' => 12)); // WHERE version_id > 12
     * </code>
     *
     * @param     mixed $versionId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByVersionId($versionId = null, $comparison = null)
    {
        if (is_array($versionId)) {
            $useMinMax = false;
            if (isset($versionId['min'])) {
                $this->addUsingAlias(SystemContentsPeer::VERSION_ID, $versionId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($versionId['max'])) {
                $this->addUsingAlias(SystemContentsPeer::VERSION_ID, $versionId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::VERSION_ID, $versionId, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemContentsPeer::TITLE, $title, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemContentsPeer::CONTENT, $content, $comparison);
    }

    /**
     * Filter the query on the template column
     *
     * Example usage:
     * <code>
     * $query->filterByTemplate('fooValue');   // WHERE template = 'fooValue'
     * $query->filterByTemplate('%fooValue%'); // WHERE template LIKE '%fooValue%'
     * </code>
     *
     * @param     string $template The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByTemplate($template = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($template)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $template)) {
                $template = str_replace('*', '%', $template);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::TEMPLATE, $template, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemContentsPeer::TYPE, $type, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByMdate($mdate = null, $comparison = null)
    {
        if (is_array($mdate)) {
            $useMinMax = false;
            if (isset($mdate['min'])) {
                $this->addUsingAlias(SystemContentsPeer::MDATE, $mdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($mdate['max'])) {
                $this->addUsingAlias(SystemContentsPeer::MDATE, $mdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::MDATE, $mdate, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByCdate($cdate = null, $comparison = null)
    {
        if (is_array($cdate)) {
            $useMinMax = false;
            if (isset($cdate['min'])) {
                $this->addUsingAlias(SystemContentsPeer::CDATE, $cdate['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($cdate['max'])) {
                $this->addUsingAlias(SystemContentsPeer::CDATE, $cdate['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::CDATE, $cdate, $comparison);
    }

    /**
     * Filter the query on the hide column
     *
     * Example usage:
     * <code>
     * $query->filterByHide(1234); // WHERE hide = 1234
     * $query->filterByHide(array(12, 34)); // WHERE hide IN (12, 34)
     * $query->filterByHide(array('min' => 12)); // WHERE hide > 12
     * </code>
     *
     * @param     mixed $hide The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByHide($hide = null, $comparison = null)
    {
        if (is_array($hide)) {
            $useMinMax = false;
            if (isset($hide['min'])) {
                $this->addUsingAlias(SystemContentsPeer::HIDE, $hide['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($hide['max'])) {
                $this->addUsingAlias(SystemContentsPeer::HIDE, $hide['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::HIDE, $hide, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterBySort($sort = null, $comparison = null)
    {
        if (is_array($sort)) {
            $useMinMax = false;
            if (isset($sort['min'])) {
                $this->addUsingAlias(SystemContentsPeer::SORT, $sort['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sort['max'])) {
                $this->addUsingAlias(SystemContentsPeer::SORT, $sort['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::SORT, $sort, $comparison);
    }

    /**
     * Filter the query on the box_id column
     *
     * Example usage:
     * <code>
     * $query->filterByBoxId(1234); // WHERE box_id = 1234
     * $query->filterByBoxId(array(12, 34)); // WHERE box_id IN (12, 34)
     * $query->filterByBoxId(array('min' => 12)); // WHERE box_id > 12
     * </code>
     *
     * @param     mixed $boxId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByBoxId($boxId = null, $comparison = null)
    {
        if (is_array($boxId)) {
            $useMinMax = false;
            if (isset($boxId['min'])) {
                $this->addUsingAlias(SystemContentsPeer::BOX_ID, $boxId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($boxId['max'])) {
                $this->addUsingAlias(SystemContentsPeer::BOX_ID, $boxId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::BOX_ID, $boxId, $comparison);
    }

    /**
     * Filter the query on the owner_id column
     *
     * Example usage:
     * <code>
     * $query->filterByOwnerId(1234); // WHERE owner_id = 1234
     * $query->filterByOwnerId(array(12, 34)); // WHERE owner_id IN (12, 34)
     * $query->filterByOwnerId(array('min' => 12)); // WHERE owner_id > 12
     * </code>
     *
     * @param     mixed $ownerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByOwnerId($ownerId = null, $comparison = null)
    {
        if (is_array($ownerId)) {
            $useMinMax = false;
            if (isset($ownerId['min'])) {
                $this->addUsingAlias(SystemContentsPeer::OWNER_ID, $ownerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ownerId['max'])) {
                $this->addUsingAlias(SystemContentsPeer::OWNER_ID, $ownerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::OWNER_ID, $ownerId, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByAccessFrom($accessFrom = null, $comparison = null)
    {
        if (is_array($accessFrom)) {
            $useMinMax = false;
            if (isset($accessFrom['min'])) {
                $this->addUsingAlias(SystemContentsPeer::ACCESS_FROM, $accessFrom['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessFrom['max'])) {
                $this->addUsingAlias(SystemContentsPeer::ACCESS_FROM, $accessFrom['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::ACCESS_FROM, $accessFrom, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByAccessTo($accessTo = null, $comparison = null)
    {
        if (is_array($accessTo)) {
            $useMinMax = false;
            if (isset($accessTo['min'])) {
                $this->addUsingAlias(SystemContentsPeer::ACCESS_TO, $accessTo['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accessTo['max'])) {
                $this->addUsingAlias(SystemContentsPeer::ACCESS_TO, $accessTo['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::ACCESS_TO, $accessTo, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
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

        return $this->addUsingAlias(SystemContentsPeer::ACCESS_FROM_GROUPS, $accessFromGroups, $comparison);
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
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function filterByUnsearchable($unsearchable = null, $comparison = null)
    {
        if (is_array($unsearchable)) {
            $useMinMax = false;
            if (isset($unsearchable['min'])) {
                $this->addUsingAlias(SystemContentsPeer::UNSEARCHABLE, $unsearchable['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($unsearchable['max'])) {
                $this->addUsingAlias(SystemContentsPeer::UNSEARCHABLE, $unsearchable['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SystemContentsPeer::UNSEARCHABLE, $unsearchable, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   SystemContents $systemContents Object to remove from the list of results
     *
     * @return SystemContentsQuery The current query, for fluid interface
     */
    public function prune($systemContents = null)
    {
        if ($systemContents) {
            $this->addUsingAlias(SystemContentsPeer::ID, $systemContents->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseSystemContentsQuery
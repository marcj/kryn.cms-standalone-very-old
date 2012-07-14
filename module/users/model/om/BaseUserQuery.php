<?php


/**
 * Base class that represents a query for the 'kryn_system_user' table.
 *
 * 
 *
 * @method     UserQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     UserQuery orderByUsername($order = Criteria::ASC) Order by the username column
 * @method     UserQuery orderByAuthClass($order = Criteria::ASC) Order by the auth_class column
 * @method     UserQuery orderByPasswd($order = Criteria::ASC) Order by the passwd column
 * @method     UserQuery orderByPasswdSalt($order = Criteria::ASC) Order by the passwd_salt column
 * @method     UserQuery orderByActivationkey($order = Criteria::ASC) Order by the activationkey column
 * @method     UserQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     UserQuery orderByDesktop($order = Criteria::ASC) Order by the desktop column
 * @method     UserQuery orderBySettings($order = Criteria::ASC) Order by the settings column
 * @method     UserQuery orderByCreated($order = Criteria::ASC) Order by the created column
 * @method     UserQuery orderByModified($order = Criteria::ASC) Order by the modified column
 * @method     UserQuery orderByFirstName($order = Criteria::ASC) Order by the first_name column
 * @method     UserQuery orderByLastName($order = Criteria::ASC) Order by the last_name column
 * @method     UserQuery orderBySex($order = Criteria::ASC) Order by the sex column
 * @method     UserQuery orderByLogins($order = Criteria::ASC) Order by the logins column
 * @method     UserQuery orderByLastlogin($order = Criteria::ASC) Order by the lastlogin column
 * @method     UserQuery orderByActivate($order = Criteria::ASC) Order by the activate column
 *
 * @method     UserQuery groupById() Group by the id column
 * @method     UserQuery groupByUsername() Group by the username column
 * @method     UserQuery groupByAuthClass() Group by the auth_class column
 * @method     UserQuery groupByPasswd() Group by the passwd column
 * @method     UserQuery groupByPasswdSalt() Group by the passwd_salt column
 * @method     UserQuery groupByActivationkey() Group by the activationkey column
 * @method     UserQuery groupByEmail() Group by the email column
 * @method     UserQuery groupByDesktop() Group by the desktop column
 * @method     UserQuery groupBySettings() Group by the settings column
 * @method     UserQuery groupByCreated() Group by the created column
 * @method     UserQuery groupByModified() Group by the modified column
 * @method     UserQuery groupByFirstName() Group by the first_name column
 * @method     UserQuery groupByLastName() Group by the last_name column
 * @method     UserQuery groupBySex() Group by the sex column
 * @method     UserQuery groupByLogins() Group by the logins column
 * @method     UserQuery groupByLastlogin() Group by the lastlogin column
 * @method     UserQuery groupByActivate() Group by the activate column
 *
 * @method     UserQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     UserQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     UserQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     UserQuery leftJoinSession($relationAlias = null) Adds a LEFT JOIN clause to the query using the Session relation
 * @method     UserQuery rightJoinSession($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Session relation
 * @method     UserQuery innerJoinSession($relationAlias = null) Adds a INNER JOIN clause to the query using the Session relation
 *
 * @method     UserQuery leftJoinUserGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the UserGroup relation
 * @method     UserQuery rightJoinUserGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UserGroup relation
 * @method     UserQuery innerJoinUserGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the UserGroup relation
 *
 * @method     User findOne(PropelPDO $con = null) Return the first User matching the query
 * @method     User findOneOrCreate(PropelPDO $con = null) Return the first User matching the query, or a new User object populated from the query conditions when no match is found
 *
 * @method     User findOneById(int $id) Return the first User filtered by the id column
 * @method     User findOneByUsername(string $username) Return the first User filtered by the username column
 * @method     User findOneByAuthClass(string $auth_class) Return the first User filtered by the auth_class column
 * @method     User findOneByPasswd(string $passwd) Return the first User filtered by the passwd column
 * @method     User findOneByPasswdSalt(string $passwd_salt) Return the first User filtered by the passwd_salt column
 * @method     User findOneByActivationkey(string $activationkey) Return the first User filtered by the activationkey column
 * @method     User findOneByEmail(string $email) Return the first User filtered by the email column
 * @method     User findOneByDesktop(string $desktop) Return the first User filtered by the desktop column
 * @method     User findOneBySettings(string $settings) Return the first User filtered by the settings column
 * @method     User findOneByCreated(int $created) Return the first User filtered by the created column
 * @method     User findOneByModified(int $modified) Return the first User filtered by the modified column
 * @method     User findOneByFirstName(string $first_name) Return the first User filtered by the first_name column
 * @method     User findOneByLastName(string $last_name) Return the first User filtered by the last_name column
 * @method     User findOneBySex(int $sex) Return the first User filtered by the sex column
 * @method     User findOneByLogins(int $logins) Return the first User filtered by the logins column
 * @method     User findOneByLastlogin(int $lastlogin) Return the first User filtered by the lastlogin column
 * @method     User findOneByActivate(boolean $activate) Return the first User filtered by the activate column
 *
 * @method     array findById(int $id) Return User objects filtered by the id column
 * @method     array findByUsername(string $username) Return User objects filtered by the username column
 * @method     array findByAuthClass(string $auth_class) Return User objects filtered by the auth_class column
 * @method     array findByPasswd(string $passwd) Return User objects filtered by the passwd column
 * @method     array findByPasswdSalt(string $passwd_salt) Return User objects filtered by the passwd_salt column
 * @method     array findByActivationkey(string $activationkey) Return User objects filtered by the activationkey column
 * @method     array findByEmail(string $email) Return User objects filtered by the email column
 * @method     array findByDesktop(string $desktop) Return User objects filtered by the desktop column
 * @method     array findBySettings(string $settings) Return User objects filtered by the settings column
 * @method     array findByCreated(int $created) Return User objects filtered by the created column
 * @method     array findByModified(int $modified) Return User objects filtered by the modified column
 * @method     array findByFirstName(string $first_name) Return User objects filtered by the first_name column
 * @method     array findByLastName(string $last_name) Return User objects filtered by the last_name column
 * @method     array findBySex(int $sex) Return User objects filtered by the sex column
 * @method     array findByLogins(int $logins) Return User objects filtered by the logins column
 * @method     array findByLastlogin(int $lastlogin) Return User objects filtered by the lastlogin column
 * @method     array findByActivate(boolean $activate) Return User objects filtered by the activate column
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BaseUserQuery extends ModelCriteria
{
    
    /**
     * Initializes internal state of BaseUserQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'Kryn', $modelName = 'User', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new UserQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     UserQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return UserQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof UserQuery) {
            return $criteria;
        }
        $query = new UserQuery();
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
     * @return   User|User[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = UserPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
     * @return   User A model object, or null if the key is not found
     * @throws   PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT ID, USERNAME, AUTH_CLASS, PASSWD, PASSWD_SALT, ACTIVATIONKEY, EMAIL, DESKTOP, SETTINGS, CREATED, MODIFIED, FIRST_NAME, LAST_NAME, SEX, LOGINS, LASTLOGIN, ACTIVATE FROM kryn_system_user WHERE ID = :p0';
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
            $obj = new User();
            $obj->hydrate($row);
            UserPeer::addInstanceToPool($obj, (string) $key);
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
     * @return User|User[]|mixed the result, formatted by the current formatter
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
     * @return PropelObjectCollection|User[]|mixed the list of results, formatted by the current formatter
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
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(UserPeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(UserPeer::ID, $keys, Criteria::IN);
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
     * @return UserQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id) && null === $comparison) {
            $comparison = Criteria::IN;
        }

        return $this->addUsingAlias(UserPeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the username column
     *
     * Example usage:
     * <code>
     * $query->filterByUsername('fooValue');   // WHERE username = 'fooValue'
     * $query->filterByUsername('%fooValue%'); // WHERE username LIKE '%fooValue%'
     * </code>
     *
     * @param     string $username The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByUsername($username = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($username)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $username)) {
                $username = str_replace('*', '%', $username);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::USERNAME, $username, $comparison);
    }

    /**
     * Filter the query on the auth_class column
     *
     * Example usage:
     * <code>
     * $query->filterByAuthClass('fooValue');   // WHERE auth_class = 'fooValue'
     * $query->filterByAuthClass('%fooValue%'); // WHERE auth_class LIKE '%fooValue%'
     * </code>
     *
     * @param     string $authClass The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByAuthClass($authClass = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($authClass)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $authClass)) {
                $authClass = str_replace('*', '%', $authClass);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::AUTH_CLASS, $authClass, $comparison);
    }

    /**
     * Filter the query on the passwd column
     *
     * Example usage:
     * <code>
     * $query->filterByPasswd('fooValue');   // WHERE passwd = 'fooValue'
     * $query->filterByPasswd('%fooValue%'); // WHERE passwd LIKE '%fooValue%'
     * </code>
     *
     * @param     string $passwd The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByPasswd($passwd = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($passwd)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $passwd)) {
                $passwd = str_replace('*', '%', $passwd);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::PASSWD, $passwd, $comparison);
    }

    /**
     * Filter the query on the passwd_salt column
     *
     * Example usage:
     * <code>
     * $query->filterByPasswdSalt('fooValue');   // WHERE passwd_salt = 'fooValue'
     * $query->filterByPasswdSalt('%fooValue%'); // WHERE passwd_salt LIKE '%fooValue%'
     * </code>
     *
     * @param     string $passwdSalt The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByPasswdSalt($passwdSalt = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($passwdSalt)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $passwdSalt)) {
                $passwdSalt = str_replace('*', '%', $passwdSalt);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::PASSWD_SALT, $passwdSalt, $comparison);
    }

    /**
     * Filter the query on the activationkey column
     *
     * Example usage:
     * <code>
     * $query->filterByActivationkey('fooValue');   // WHERE activationkey = 'fooValue'
     * $query->filterByActivationkey('%fooValue%'); // WHERE activationkey LIKE '%fooValue%'
     * </code>
     *
     * @param     string $activationkey The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByActivationkey($activationkey = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($activationkey)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $activationkey)) {
                $activationkey = str_replace('*', '%', $activationkey);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::ACTIVATIONKEY, $activationkey, $comparison);
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
     * </code>
     *
     * @param     string $email The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByEmail($email = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($email)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $email)) {
                $email = str_replace('*', '%', $email);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::EMAIL, $email, $comparison);
    }

    /**
     * Filter the query on the desktop column
     *
     * Example usage:
     * <code>
     * $query->filterByDesktop('fooValue');   // WHERE desktop = 'fooValue'
     * $query->filterByDesktop('%fooValue%'); // WHERE desktop LIKE '%fooValue%'
     * </code>
     *
     * @param     string $desktop The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByDesktop($desktop = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($desktop)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $desktop)) {
                $desktop = str_replace('*', '%', $desktop);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::DESKTOP, $desktop, $comparison);
    }

    /**
     * Filter the query on the settings column
     *
     * Example usage:
     * <code>
     * $query->filterBySettings('fooValue');   // WHERE settings = 'fooValue'
     * $query->filterBySettings('%fooValue%'); // WHERE settings LIKE '%fooValue%'
     * </code>
     *
     * @param     string $settings The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterBySettings($settings = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($settings)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $settings)) {
                $settings = str_replace('*', '%', $settings);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::SETTINGS, $settings, $comparison);
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
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByCreated($created = null, $comparison = null)
    {
        if (is_array($created)) {
            $useMinMax = false;
            if (isset($created['min'])) {
                $this->addUsingAlias(UserPeer::CREATED, $created['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($created['max'])) {
                $this->addUsingAlias(UserPeer::CREATED, $created['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserPeer::CREATED, $created, $comparison);
    }

    /**
     * Filter the query on the modified column
     *
     * Example usage:
     * <code>
     * $query->filterByModified(1234); // WHERE modified = 1234
     * $query->filterByModified(array(12, 34)); // WHERE modified IN (12, 34)
     * $query->filterByModified(array('min' => 12)); // WHERE modified > 12
     * </code>
     *
     * @param     mixed $modified The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByModified($modified = null, $comparison = null)
    {
        if (is_array($modified)) {
            $useMinMax = false;
            if (isset($modified['min'])) {
                $this->addUsingAlias(UserPeer::MODIFIED, $modified['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($modified['max'])) {
                $this->addUsingAlias(UserPeer::MODIFIED, $modified['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserPeer::MODIFIED, $modified, $comparison);
    }

    /**
     * Filter the query on the first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFirstName('fooValue');   // WHERE first_name = 'fooValue'
     * $query->filterByFirstName('%fooValue%'); // WHERE first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $firstName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByFirstName($firstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($firstName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $firstName)) {
                $firstName = str_replace('*', '%', $firstName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::FIRST_NAME, $firstName, $comparison);
    }

    /**
     * Filter the query on the last_name column
     *
     * Example usage:
     * <code>
     * $query->filterByLastName('fooValue');   // WHERE last_name = 'fooValue'
     * $query->filterByLastName('%fooValue%'); // WHERE last_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $lastName The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByLastName($lastName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($lastName)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $lastName)) {
                $lastName = str_replace('*', '%', $lastName);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(UserPeer::LAST_NAME, $lastName, $comparison);
    }

    /**
     * Filter the query on the sex column
     *
     * Example usage:
     * <code>
     * $query->filterBySex(1234); // WHERE sex = 1234
     * $query->filterBySex(array(12, 34)); // WHERE sex IN (12, 34)
     * $query->filterBySex(array('min' => 12)); // WHERE sex > 12
     * </code>
     *
     * @param     mixed $sex The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterBySex($sex = null, $comparison = null)
    {
        if (is_array($sex)) {
            $useMinMax = false;
            if (isset($sex['min'])) {
                $this->addUsingAlias(UserPeer::SEX, $sex['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($sex['max'])) {
                $this->addUsingAlias(UserPeer::SEX, $sex['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserPeer::SEX, $sex, $comparison);
    }

    /**
     * Filter the query on the logins column
     *
     * Example usage:
     * <code>
     * $query->filterByLogins(1234); // WHERE logins = 1234
     * $query->filterByLogins(array(12, 34)); // WHERE logins IN (12, 34)
     * $query->filterByLogins(array('min' => 12)); // WHERE logins > 12
     * </code>
     *
     * @param     mixed $logins The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByLogins($logins = null, $comparison = null)
    {
        if (is_array($logins)) {
            $useMinMax = false;
            if (isset($logins['min'])) {
                $this->addUsingAlias(UserPeer::LOGINS, $logins['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($logins['max'])) {
                $this->addUsingAlias(UserPeer::LOGINS, $logins['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserPeer::LOGINS, $logins, $comparison);
    }

    /**
     * Filter the query on the lastlogin column
     *
     * Example usage:
     * <code>
     * $query->filterByLastlogin(1234); // WHERE lastlogin = 1234
     * $query->filterByLastlogin(array(12, 34)); // WHERE lastlogin IN (12, 34)
     * $query->filterByLastlogin(array('min' => 12)); // WHERE lastlogin > 12
     * </code>
     *
     * @param     mixed $lastlogin The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByLastlogin($lastlogin = null, $comparison = null)
    {
        if (is_array($lastlogin)) {
            $useMinMax = false;
            if (isset($lastlogin['min'])) {
                $this->addUsingAlias(UserPeer::LASTLOGIN, $lastlogin['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($lastlogin['max'])) {
                $this->addUsingAlias(UserPeer::LASTLOGIN, $lastlogin['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(UserPeer::LASTLOGIN, $lastlogin, $comparison);
    }

    /**
     * Filter the query on the activate column
     *
     * Example usage:
     * <code>
     * $query->filterByActivate(true); // WHERE activate = true
     * $query->filterByActivate('yes'); // WHERE activate = true
     * </code>
     *
     * @param     boolean|string $activate The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function filterByActivate($activate = null, $comparison = null)
    {
        if (is_string($activate)) {
            $activate = in_array(strtolower($activate), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(UserPeer::ACTIVATE, $activate, $comparison);
    }

    /**
     * Filter the query by a related Session object
     *
     * @param   Session|PropelObjectCollection $session  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   UserQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterBySession($session, $comparison = null)
    {
        if ($session instanceof Session) {
            return $this
                ->addUsingAlias(UserPeer::ID, $session->getUserId(), $comparison);
        } elseif ($session instanceof PropelObjectCollection) {
            return $this
                ->useSessionQuery()
                ->filterByPrimaryKeys($session->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySession() only accepts arguments of type Session or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Session relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function joinSession($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Session');

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
            $this->addJoinObject($join, 'Session');
        }

        return $this;
    }

    /**
     * Use the Session relation Session object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   SessionQuery A secondary query class using the current class as primary query
     */
    public function useSessionQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinSession($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Session', 'SessionQuery');
    }

    /**
     * Filter the query by a related UserGroup object
     *
     * @param   UserGroup|PropelObjectCollection $userGroup  the related object to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return   UserQuery The current query, for fluid interface
     * @throws   PropelException - if the provided filter is invalid.
     */
    public function filterByUserGroup($userGroup, $comparison = null)
    {
        if ($userGroup instanceof UserGroup) {
            return $this
                ->addUsingAlias(UserPeer::ID, $userGroup->getUserId(), $comparison);
        } elseif ($userGroup instanceof PropelObjectCollection) {
            return $this
                ->useUserGroupQuery()
                ->filterByPrimaryKeys($userGroup->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByUserGroup() only accepts arguments of type UserGroup or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UserGroup relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function joinUserGroup($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UserGroup');

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
            $this->addJoinObject($join, 'UserGroup');
        }

        return $this;
    }

    /**
     * Use the UserGroup relation UserGroup object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   UserGroupQuery A secondary query class using the current class as primary query
     */
    public function useUserGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinUserGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UserGroup', 'UserGroupQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   User $user Object to remove from the list of results
     *
     * @return UserQuery The current query, for fluid interface
     */
    public function prune($user = null)
    {
        if ($user) {
            $this->addUsingAlias(UserPeer::ID, $user->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

} // BaseUserQuery
<?php


/**
 * Base class that represents a row from the 'kryn_system_user' table.
 *
 * 
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BaseUser extends BaseObject 
{

    /**
     * Peer class name
     */
    const PEER = 'UserPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        UserPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinit loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the username field.
     * @var        string
     */
    protected $username;

    /**
     * The value for the auth_class field.
     * @var        string
     */
    protected $auth_class;

    /**
     * The value for the passwd field.
     * @var        string
     */
    protected $passwd;

    /**
     * The value for the passwd_salt field.
     * @var        string
     */
    protected $passwd_salt;

    /**
     * The value for the activationkey field.
     * @var        string
     */
    protected $activationkey;

    /**
     * The value for the email field.
     * @var        string
     */
    protected $email;

    /**
     * The value for the desktop field.
     * @var        string
     */
    protected $desktop;

    /**
     * The value for the settings field.
     * @var        string
     */
    protected $settings;

    /**
     * The value for the first_name field.
     * @var        string
     */
    protected $first_name;

    /**
     * The value for the last_name field.
     * @var        string
     */
    protected $last_name;

    /**
     * The value for the sex field.
     * @var        int
     */
    protected $sex;

    /**
     * The value for the logins field.
     * @var        int
     */
    protected $logins;

    /**
     * The value for the lastlogin field.
     * @var        int
     */
    protected $lastlogin;

    /**
     * The value for the activate field.
     * @var        boolean
     */
    protected $activate;

    /**
     * @var        PropelObjectCollection|Session[] Collection to store aggregation of Session objects.
     */
    protected $collSessions;

    /**
     * @var        PropelObjectCollection|UserGroup[] Collection to store aggregation of UserGroup objects.
     */
    protected $collUserGroups;

    /**
     * @var        PropelObjectCollection|Group[] Collection to store aggregation of Group objects.
     */
    protected $collGroups;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $groupsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $sessionsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $userGroupsScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     * 
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [username] column value.
     * 
     * @return   string
     */
    public function getUsername()
    {

        return $this->username;
    }

    /**
     * Get the [auth_class] column value.
     * 
     * @return   string
     */
    public function getAuthClass()
    {

        return $this->auth_class;
    }

    /**
     * Get the [passwd] column value.
     * 
     * @return   string
     */
    public function getPasswd()
    {

        return $this->passwd;
    }

    /**
     * Get the [passwd_salt] column value.
     * 
     * @return   string
     */
    public function getPasswdSalt()
    {

        return $this->passwd_salt;
    }

    /**
     * Get the [activationkey] column value.
     * 
     * @return   string
     */
    public function getActivationkey()
    {

        return $this->activationkey;
    }

    /**
     * Get the [email] column value.
     * 
     * @return   string
     */
    public function getEmail()
    {

        return $this->email;
    }

    /**
     * Get the [desktop] column value.
     * 
     * @return   string
     */
    public function getDesktop()
    {

        return $this->desktop;
    }

    /**
     * Get the [settings] column value.
     * 
     * @return   string
     */
    public function getSettings()
    {

        return $this->settings;
    }

    /**
     * Get the [first_name] column value.
     * 
     * @return   string
     */
    public function getFirstName()
    {

        return $this->first_name;
    }

    /**
     * Get the [last_name] column value.
     * 
     * @return   string
     */
    public function getLastName()
    {

        return $this->last_name;
    }

    /**
     * Get the [sex] column value.
     * 
     * @return   int
     */
    public function getSex()
    {

        return $this->sex;
    }

    /**
     * Get the [logins] column value.
     * 
     * @return   int
     */
    public function getLogins()
    {

        return $this->logins;
    }

    /**
     * Get the [lastlogin] column value.
     * 
     * @return   int
     */
    public function getLastlogin()
    {

        return $this->lastlogin;
    }

    /**
     * Get the [activate] column value.
     * 
     * @return   boolean
     */
    public function getActivate()
    {

        return $this->activate;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = UserPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [username] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setUsername($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->username !== $v) {
            $this->username = $v;
            $this->modifiedColumns[] = UserPeer::USERNAME;
        }


        return $this;
    } // setUsername()

    /**
     * Set the value of [auth_class] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setAuthClass($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->auth_class !== $v) {
            $this->auth_class = $v;
            $this->modifiedColumns[] = UserPeer::AUTH_CLASS;
        }


        return $this;
    } // setAuthClass()

    /**
     * Set the value of [passwd] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setPasswd($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->passwd !== $v) {
            $this->passwd = $v;
            $this->modifiedColumns[] = UserPeer::PASSWD;
        }


        return $this;
    } // setPasswd()

    /**
     * Set the value of [passwd_salt] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setPasswdSalt($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->passwd_salt !== $v) {
            $this->passwd_salt = $v;
            $this->modifiedColumns[] = UserPeer::PASSWD_SALT;
        }


        return $this;
    } // setPasswdSalt()

    /**
     * Set the value of [activationkey] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setActivationkey($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->activationkey !== $v) {
            $this->activationkey = $v;
            $this->modifiedColumns[] = UserPeer::ACTIVATIONKEY;
        }


        return $this;
    } // setActivationkey()

    /**
     * Set the value of [email] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = UserPeer::EMAIL;
        }


        return $this;
    } // setEmail()

    /**
     * Set the value of [desktop] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setDesktop($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->desktop !== $v) {
            $this->desktop = $v;
            $this->modifiedColumns[] = UserPeer::DESKTOP;
        }


        return $this;
    } // setDesktop()

    /**
     * Set the value of [settings] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setSettings($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->settings !== $v) {
            $this->settings = $v;
            $this->modifiedColumns[] = UserPeer::SETTINGS;
        }


        return $this;
    } // setSettings()

    /**
     * Set the value of [first_name] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setFirstName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->first_name !== $v) {
            $this->first_name = $v;
            $this->modifiedColumns[] = UserPeer::FIRST_NAME;
        }


        return $this;
    } // setFirstName()

    /**
     * Set the value of [last_name] column.
     * 
     * @param      string $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setLastName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->last_name !== $v) {
            $this->last_name = $v;
            $this->modifiedColumns[] = UserPeer::LAST_NAME;
        }


        return $this;
    } // setLastName()

    /**
     * Set the value of [sex] column.
     * 
     * @param      int $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setSex($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->sex !== $v) {
            $this->sex = $v;
            $this->modifiedColumns[] = UserPeer::SEX;
        }


        return $this;
    } // setSex()

    /**
     * Set the value of [logins] column.
     * 
     * @param      int $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setLogins($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->logins !== $v) {
            $this->logins = $v;
            $this->modifiedColumns[] = UserPeer::LOGINS;
        }


        return $this;
    } // setLogins()

    /**
     * Set the value of [lastlogin] column.
     * 
     * @param      int $v new value
     * @return   User The current object (for fluent API support)
     */
    public function setLastlogin($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->lastlogin !== $v) {
            $this->lastlogin = $v;
            $this->modifiedColumns[] = UserPeer::LASTLOGIN;
        }


        return $this;
    } // setLastlogin()

    /**
     * Sets the value of the [activate] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * 
     * @param      boolean|integer|string $v The new value
     * @return   User The current object (for fluent API support)
     */
    public function setActivate($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->activate !== $v) {
            $this->activate = $v;
            $this->modifiedColumns[] = UserPeer::ACTIVATE;
        }


        return $this;
    } // setActivate()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
     * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->username = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->auth_class = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->passwd = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->passwd_salt = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->activationkey = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->email = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->desktop = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->settings = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->first_name = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->last_name = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->sex = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
            $this->logins = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
            $this->lastlogin = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
            $this->activate = ($row[$startcol + 14] !== null) ? (boolean) $row[$startcol + 14] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 15; // 15 = UserPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating User object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = UserPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collSessions = null;

            $this->collUserGroups = null;

            $this->collGroups = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = UserQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(UserPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                UserPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->groupsScheduledForDeletion !== null) {
                if (!$this->groupsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->groupsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    UserGroupQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->groupsScheduledForDeletion = null;
                }

                foreach ($this->getGroups() as $group) {
                    if ($group->isModified()) {
                        $group->save($con);
                    }
                }
            }

            if ($this->sessionsScheduledForDeletion !== null) {
                if (!$this->sessionsScheduledForDeletion->isEmpty()) {
                    foreach ($this->sessionsScheduledForDeletion as $session) {
                        // need to save related object because we set the relation to null
                        $session->save($con);
                    }
                    $this->sessionsScheduledForDeletion = null;
                }
            }

            if ($this->collSessions !== null) {
                foreach ($this->collSessions as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->userGroupsScheduledForDeletion !== null) {
                if (!$this->userGroupsScheduledForDeletion->isEmpty()) {
                    UserGroupQuery::create()
                        ->filterByPrimaryKeys($this->userGroupsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->userGroupsScheduledForDeletion = null;
                }
            }

            if ($this->collUserGroups !== null) {
                foreach ($this->collUserGroups as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = UserPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . UserPeer::ID . ')');
        }
        if (null === $this->id) {
            try {				
				$stmt = $con->query("SELECT nextval('kryn_system_user_id_seq')");
				$row = $stmt->fetch(PDO::FETCH_NUM);
				$this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(UserPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(UserPeer::USERNAME)) {
            $modifiedColumns[':p' . $index++]  = 'USERNAME';
        }
        if ($this->isColumnModified(UserPeer::AUTH_CLASS)) {
            $modifiedColumns[':p' . $index++]  = 'AUTH_CLASS';
        }
        if ($this->isColumnModified(UserPeer::PASSWD)) {
            $modifiedColumns[':p' . $index++]  = 'PASSWD';
        }
        if ($this->isColumnModified(UserPeer::PASSWD_SALT)) {
            $modifiedColumns[':p' . $index++]  = 'PASSWD_SALT';
        }
        if ($this->isColumnModified(UserPeer::ACTIVATIONKEY)) {
            $modifiedColumns[':p' . $index++]  = 'ACTIVATIONKEY';
        }
        if ($this->isColumnModified(UserPeer::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = 'EMAIL';
        }
        if ($this->isColumnModified(UserPeer::DESKTOP)) {
            $modifiedColumns[':p' . $index++]  = 'DESKTOP';
        }
        if ($this->isColumnModified(UserPeer::SETTINGS)) {
            $modifiedColumns[':p' . $index++]  = 'SETTINGS';
        }
        if ($this->isColumnModified(UserPeer::FIRST_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'FIRST_NAME';
        }
        if ($this->isColumnModified(UserPeer::LAST_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'LAST_NAME';
        }
        if ($this->isColumnModified(UserPeer::SEX)) {
            $modifiedColumns[':p' . $index++]  = 'SEX';
        }
        if ($this->isColumnModified(UserPeer::LOGINS)) {
            $modifiedColumns[':p' . $index++]  = 'LOGINS';
        }
        if ($this->isColumnModified(UserPeer::LASTLOGIN)) {
            $modifiedColumns[':p' . $index++]  = 'LASTLOGIN';
        }
        if ($this->isColumnModified(UserPeer::ACTIVATE)) {
            $modifiedColumns[':p' . $index++]  = 'ACTIVATE';
        }

        $sql = sprintf(
            'INSERT INTO kryn_system_user (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'ID':
						$stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'USERNAME':
						$stmt->bindValue($identifier, $this->username, PDO::PARAM_STR);
                        break;
                    case 'AUTH_CLASS':
						$stmt->bindValue($identifier, $this->auth_class, PDO::PARAM_STR);
                        break;
                    case 'PASSWD':
						$stmt->bindValue($identifier, $this->passwd, PDO::PARAM_STR);
                        break;
                    case 'PASSWD_SALT':
						$stmt->bindValue($identifier, $this->passwd_salt, PDO::PARAM_STR);
                        break;
                    case 'ACTIVATIONKEY':
						$stmt->bindValue($identifier, $this->activationkey, PDO::PARAM_STR);
                        break;
                    case 'EMAIL':
						$stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case 'DESKTOP':
						$stmt->bindValue($identifier, $this->desktop, PDO::PARAM_STR);
                        break;
                    case 'SETTINGS':
						$stmt->bindValue($identifier, $this->settings, PDO::PARAM_STR);
                        break;
                    case 'FIRST_NAME':
						$stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);
                        break;
                    case 'LAST_NAME':
						$stmt->bindValue($identifier, $this->last_name, PDO::PARAM_STR);
                        break;
                    case 'SEX':
						$stmt->bindValue($identifier, $this->sex, PDO::PARAM_INT);
                        break;
                    case 'LOGINS':
						$stmt->bindValue($identifier, $this->logins, PDO::PARAM_INT);
                        break;
                    case 'LASTLOGIN':
						$stmt->bindValue($identifier, $this->lastlogin, PDO::PARAM_INT);
                        break;
                    case 'ACTIVATE':
						$stmt->bindValue($identifier, $this->activate, PDO::PARAM_BOOL);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param      mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        } else {
            $this->validationFailures = $res;

            return false;
        }
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggreagated array of ValidationFailed objects will be returned.
     *
     * @param      array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            if (($retval = UserPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collSessions !== null) {
                    foreach ($this->collSessions as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collUserGroups !== null) {
                    foreach ($this->collUserGroups as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = UserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getUsername();
                break;
            case 2:
                return $this->getAuthClass();
                break;
            case 3:
                return $this->getPasswd();
                break;
            case 4:
                return $this->getPasswdSalt();
                break;
            case 5:
                return $this->getActivationkey();
                break;
            case 6:
                return $this->getEmail();
                break;
            case 7:
                return $this->getDesktop();
                break;
            case 8:
                return $this->getSettings();
                break;
            case 9:
                return $this->getFirstName();
                break;
            case 10:
                return $this->getLastName();
                break;
            case 11:
                return $this->getSex();
                break;
            case 12:
                return $this->getLogins();
                break;
            case 13:
                return $this->getLastlogin();
                break;
            case 14:
                return $this->getActivate();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['User'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['User'][$this->getPrimaryKey()] = true;
        $keys = UserPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getUsername(),
            $keys[2] => $this->getAuthClass(),
            $keys[3] => $this->getPasswd(),
            $keys[4] => $this->getPasswdSalt(),
            $keys[5] => $this->getActivationkey(),
            $keys[6] => $this->getEmail(),
            $keys[7] => $this->getDesktop(),
            $keys[8] => $this->getSettings(),
            $keys[9] => $this->getFirstName(),
            $keys[10] => $this->getLastName(),
            $keys[11] => $this->getSex(),
            $keys[12] => $this->getLogins(),
            $keys[13] => $this->getLastlogin(),
            $keys[14] => $this->getActivate(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->collSessions) {
                $result['Sessions'] = $this->collSessions->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUserGroups) {
                $result['UserGroups'] = $this->collUserGroups->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name peer name
     * @param      mixed $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = UserPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setUsername($value);
                break;
            case 2:
                $this->setAuthClass($value);
                break;
            case 3:
                $this->setPasswd($value);
                break;
            case 4:
                $this->setPasswdSalt($value);
                break;
            case 5:
                $this->setActivationkey($value);
                break;
            case 6:
                $this->setEmail($value);
                break;
            case 7:
                $this->setDesktop($value);
                break;
            case 8:
                $this->setSettings($value);
                break;
            case 9:
                $this->setFirstName($value);
                break;
            case 10:
                $this->setLastName($value);
                break;
            case 11:
                $this->setSex($value);
                break;
            case 12:
                $this->setLogins($value);
                break;
            case 13:
                $this->setLastlogin($value);
                break;
            case 14:
                $this->setActivate($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = UserPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setUsername($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setAuthClass($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setPasswd($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setPasswdSalt($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setActivationkey($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setEmail($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setDesktop($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setSettings($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setFirstName($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setLastName($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setSex($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setLogins($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setLastlogin($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setActivate($arr[$keys[14]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(UserPeer::DATABASE_NAME);

        if ($this->isColumnModified(UserPeer::ID)) $criteria->add(UserPeer::ID, $this->id);
        if ($this->isColumnModified(UserPeer::USERNAME)) $criteria->add(UserPeer::USERNAME, $this->username);
        if ($this->isColumnModified(UserPeer::AUTH_CLASS)) $criteria->add(UserPeer::AUTH_CLASS, $this->auth_class);
        if ($this->isColumnModified(UserPeer::PASSWD)) $criteria->add(UserPeer::PASSWD, $this->passwd);
        if ($this->isColumnModified(UserPeer::PASSWD_SALT)) $criteria->add(UserPeer::PASSWD_SALT, $this->passwd_salt);
        if ($this->isColumnModified(UserPeer::ACTIVATIONKEY)) $criteria->add(UserPeer::ACTIVATIONKEY, $this->activationkey);
        if ($this->isColumnModified(UserPeer::EMAIL)) $criteria->add(UserPeer::EMAIL, $this->email);
        if ($this->isColumnModified(UserPeer::DESKTOP)) $criteria->add(UserPeer::DESKTOP, $this->desktop);
        if ($this->isColumnModified(UserPeer::SETTINGS)) $criteria->add(UserPeer::SETTINGS, $this->settings);
        if ($this->isColumnModified(UserPeer::FIRST_NAME)) $criteria->add(UserPeer::FIRST_NAME, $this->first_name);
        if ($this->isColumnModified(UserPeer::LAST_NAME)) $criteria->add(UserPeer::LAST_NAME, $this->last_name);
        if ($this->isColumnModified(UserPeer::SEX)) $criteria->add(UserPeer::SEX, $this->sex);
        if ($this->isColumnModified(UserPeer::LOGINS)) $criteria->add(UserPeer::LOGINS, $this->logins);
        if ($this->isColumnModified(UserPeer::LASTLOGIN)) $criteria->add(UserPeer::LASTLOGIN, $this->lastlogin);
        if ($this->isColumnModified(UserPeer::ACTIVATE)) $criteria->add(UserPeer::ACTIVATE, $this->activate);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(UserPeer::DATABASE_NAME);
        $criteria->add(UserPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return   int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of User (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setUsername($this->getUsername());
        $copyObj->setAuthClass($this->getAuthClass());
        $copyObj->setPasswd($this->getPasswd());
        $copyObj->setPasswdSalt($this->getPasswdSalt());
        $copyObj->setActivationkey($this->getActivationkey());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setDesktop($this->getDesktop());
        $copyObj->setSettings($this->getSettings());
        $copyObj->setFirstName($this->getFirstName());
        $copyObj->setLastName($this->getLastName());
        $copyObj->setSex($this->getSex());
        $copyObj->setLogins($this->getLogins());
        $copyObj->setLastlogin($this->getLastlogin());
        $copyObj->setActivate($this->getActivate());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getSessions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSession($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUserGroups() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUserGroup($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return                 User Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return   UserPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new UserPeer();
        }

        return self::$peer;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('Session' == $relationName) {
            $this->initSessions();
        }
        if ('UserGroup' == $relationName) {
            $this->initUserGroups();
        }
    }

    /**
     * Clears out the collSessions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSessions()
     */
    public function clearSessions()
    {
        $this->collSessions = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collSessions collection.
     *
     * By default this just sets the collSessions collection to an empty array (like clearcollSessions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSessions($overrideExisting = true)
    {
        if (null !== $this->collSessions && !$overrideExisting) {
            return;
        }
        $this->collSessions = new PropelObjectCollection();
        $this->collSessions->setModel('Session');
    }

    /**
     * Gets an array of Session objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|Session[] List of Session objects
     * @throws PropelException
     */
    public function getSessions($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collSessions || null !== $criteria) {
            if ($this->isNew() && null === $this->collSessions) {
                // return empty collection
                $this->initSessions();
            } else {
                $collSessions = SessionQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collSessions;
                }
                $this->collSessions = $collSessions;
            }
        }

        return $this->collSessions;
    }

    /**
     * Sets a collection of Session objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $sessions A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setSessions(PropelCollection $sessions, PropelPDO $con = null)
    {
        $this->sessionsScheduledForDeletion = $this->getSessions(new Criteria(), $con)->diff($sessions);

        foreach ($this->sessionsScheduledForDeletion as $sessionRemoved) {
            $sessionRemoved->setUser(null);
        }

        $this->collSessions = null;
        foreach ($sessions as $session) {
            $this->addSession($session);
        }

        $this->collSessions = $sessions;
    }

    /**
     * Returns the number of related Session objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related Session objects.
     * @throws PropelException
     */
    public function countSessions(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collSessions || null !== $criteria) {
            if ($this->isNew() && null === $this->collSessions) {
                return 0;
            } else {
                $query = SessionQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByUser($this)
                    ->count($con);
            }
        } else {
            return count($this->collSessions);
        }
    }

    /**
     * Method called to associate a Session object to this object
     * through the Session foreign key attribute.
     *
     * @param    Session $l Session
     * @return   User The current object (for fluent API support)
     */
    public function addSession(Session $l)
    {
        if ($this->collSessions === null) {
            $this->initSessions();
        }
        if (!$this->collSessions->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddSession($l);
        }

        return $this;
    }

    /**
     * @param	Session $session The session object to add.
     */
    protected function doAddSession($session)
    {
        $this->collSessions[]= $session;
        $session->setUser($this);
    }

    /**
     * @param	Session $session The session object to remove.
     */
    public function removeSession($session)
    {
        if ($this->getSessions()->contains($session)) {
            $this->collSessions->remove($this->collSessions->search($session));
            if (null === $this->sessionsScheduledForDeletion) {
                $this->sessionsScheduledForDeletion = clone $this->collSessions;
                $this->sessionsScheduledForDeletion->clear();
            }
            $this->sessionsScheduledForDeletion[]= $session;
            $session->setUser(null);
        }
    }

    /**
     * Clears out the collUserGroups collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUserGroups()
     */
    public function clearUserGroups()
    {
        $this->collUserGroups = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collUserGroups collection.
     *
     * By default this just sets the collUserGroups collection to an empty array (like clearcollUserGroups());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUserGroups($overrideExisting = true)
    {
        if (null !== $this->collUserGroups && !$overrideExisting) {
            return;
        }
        $this->collUserGroups = new PropelObjectCollection();
        $this->collUserGroups->setModel('UserGroup');
    }

    /**
     * Gets an array of UserGroup objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|UserGroup[] List of UserGroup objects
     * @throws PropelException
     */
    public function getUserGroups($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collUserGroups || null !== $criteria) {
            if ($this->isNew() && null === $this->collUserGroups) {
                // return empty collection
                $this->initUserGroups();
            } else {
                $collUserGroups = UserGroupQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collUserGroups;
                }
                $this->collUserGroups = $collUserGroups;
            }
        }

        return $this->collUserGroups;
    }

    /**
     * Sets a collection of UserGroup objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $userGroups A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setUserGroups(PropelCollection $userGroups, PropelPDO $con = null)
    {
        $this->userGroupsScheduledForDeletion = $this->getUserGroups(new Criteria(), $con)->diff($userGroups);

        foreach ($this->userGroupsScheduledForDeletion as $userGroupRemoved) {
            $userGroupRemoved->setUser(null);
        }

        $this->collUserGroups = null;
        foreach ($userGroups as $userGroup) {
            $this->addUserGroup($userGroup);
        }

        $this->collUserGroups = $userGroups;
    }

    /**
     * Returns the number of related UserGroup objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related UserGroup objects.
     * @throws PropelException
     */
    public function countUserGroups(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collUserGroups || null !== $criteria) {
            if ($this->isNew() && null === $this->collUserGroups) {
                return 0;
            } else {
                $query = UserGroupQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByUser($this)
                    ->count($con);
            }
        } else {
            return count($this->collUserGroups);
        }
    }

    /**
     * Method called to associate a UserGroup object to this object
     * through the UserGroup foreign key attribute.
     *
     * @param    UserGroup $l UserGroup
     * @return   User The current object (for fluent API support)
     */
    public function addUserGroup(UserGroup $l)
    {
        if ($this->collUserGroups === null) {
            $this->initUserGroups();
        }
        if (!$this->collUserGroups->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddUserGroup($l);
        }

        return $this;
    }

    /**
     * @param	UserGroup $userGroup The userGroup object to add.
     */
    protected function doAddUserGroup($userGroup)
    {
        $this->collUserGroups[]= $userGroup;
        $userGroup->setUser($this);
    }

    /**
     * @param	UserGroup $userGroup The userGroup object to remove.
     */
    public function removeUserGroup($userGroup)
    {
        if ($this->getUserGroups()->contains($userGroup)) {
            $this->collUserGroups->remove($this->collUserGroups->search($userGroup));
            if (null === $this->userGroupsScheduledForDeletion) {
                $this->userGroupsScheduledForDeletion = clone $this->collUserGroups;
                $this->userGroupsScheduledForDeletion->clear();
            }
            $this->userGroupsScheduledForDeletion[]= $userGroup;
            $userGroup->setUser(null);
        }
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this User is new, it will return
     * an empty collection; or if this User has previously
     * been saved, it will retrieve related UserGroups from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in User.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|UserGroup[] List of UserGroup objects
     */
    public function getUserGroupsJoinGroup($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = UserGroupQuery::create(null, $criteria);
        $query->joinWith('Group', $join_behavior);

        return $this->getUserGroups($query, $con);
    }

    /**
     * Clears out the collGroups collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addGroups()
     */
    public function clearGroups()
    {
        $this->collGroups = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collGroups collection.
     *
     * By default this just sets the collGroups collection to an empty collection (like clearGroups());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initGroups()
    {
        $this->collGroups = new PropelObjectCollection();
        $this->collGroups->setModel('Group');
    }

    /**
     * Gets a collection of Group objects related by a many-to-many relationship
     * to the current object by way of the kryn_system_user_group cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this User is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Group[] List of Group objects
     */
    public function getGroups($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collGroups || null !== $criteria) {
            if ($this->isNew() && null === $this->collGroups) {
                // return empty collection
                $this->initGroups();
            } else {
                $collGroups = GroupQuery::create(null, $criteria)
                    ->filterByUser($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collGroups;
                }
                $this->collGroups = $collGroups;
            }
        }

        return $this->collGroups;
    }

    /**
     * Sets a collection of Group objects related by a many-to-many relationship
     * to the current object by way of the kryn_system_user_group cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $groups A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setGroups(PropelCollection $groups, PropelPDO $con = null)
    {
        $this->clearGroups();
        $currentGroups = $this->getGroups();

        $this->groupsScheduledForDeletion = $currentGroups->diff($groups);

        foreach ($groups as $group) {
            if (!$currentGroups->contains($group)) {
                $this->doAddGroup($group);
            }
        }

        $this->collGroups = $groups;
    }

    /**
     * Gets the number of Group objects related by a many-to-many relationship
     * to the current object by way of the kryn_system_user_group cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      PropelPDO $con Optional connection object
     *
     * @return int the number of related Group objects
     */
    public function countGroups($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collGroups || null !== $criteria) {
            if ($this->isNew() && null === $this->collGroups) {
                return 0;
            } else {
                $query = GroupQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByUser($this)
                    ->count($con);
            }
        } else {
            return count($this->collGroups);
        }
    }

    /**
     * Associate a Group object to this object
     * through the kryn_system_user_group cross reference table.
     *
     * @param  Group $group The UserGroup object to relate
     * @return void
     */
    public function addGroup(Group $group)
    {
        if ($this->collGroups === null) {
            $this->initGroups();
        }
        if (!$this->collGroups->contains($group)) { // only add it if the **same** object is not already associated
            $this->doAddGroup($group);

            $this->collGroups[]= $group;
        }
    }

    /**
     * @param	Group $group The group object to add.
     */
    protected function doAddGroup($group)
    {
        $userGroup = new UserGroup();
        $userGroup->setGroup($group);
        $this->addUserGroup($userGroup);
    }

    /**
     * Remove a Group object to this object
     * through the kryn_system_user_group cross reference table.
     *
     * @param      Group $group The UserGroup object to relate
     * @return void
     */
    public function removeGroup(Group $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->collGroups->remove($this->collGroups->search($group));
            if (null === $this->groupsScheduledForDeletion) {
                $this->groupsScheduledForDeletion = clone $this->collGroups;
                $this->groupsScheduledForDeletion->clear();
            }
            $this->groupsScheduledForDeletion[]= $group;
        }
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->username = null;
        $this->auth_class = null;
        $this->passwd = null;
        $this->passwd_salt = null;
        $this->activationkey = null;
        $this->email = null;
        $this->desktop = null;
        $this->settings = null;
        $this->first_name = null;
        $this->last_name = null;
        $this->sex = null;
        $this->logins = null;
        $this->lastlogin = null;
        $this->activate = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volumne/high-memory operations.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collSessions) {
                foreach ($this->collSessions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUserGroups) {
                foreach ($this->collUserGroups as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collGroups) {
                foreach ($this->collGroups as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collSessions instanceof PropelCollection) {
            $this->collSessions->clearIterator();
        }
        $this->collSessions = null;
        if ($this->collUserGroups instanceof PropelCollection) {
            $this->collUserGroups->clearIterator();
        }
        $this->collUserGroups = null;
        if ($this->collGroups instanceof PropelCollection) {
            $this->collGroups->clearIterator();
        }
        $this->collGroups = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(UserPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

} // BaseUser

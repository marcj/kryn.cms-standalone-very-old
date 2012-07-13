<?php


/**
 * Base class that represents a row from the 'kryn_system_domain' table.
 *
 * 
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemDomain extends BaseObject 
{

    /**
     * Peer class name
     */
    const PEER = 'SystemDomainPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        SystemDomainPeer
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
     * The value for the domain field.
     * @var        string
     */
    protected $domain;

    /**
     * The value for the title_format field.
     * @var        string
     */
    protected $title_format;

    /**
     * The value for the lang field.
     * @var        string
     */
    protected $lang;

    /**
     * The value for the startpage_id field.
     * @var        int
     */
    protected $startpage_id;

    /**
     * The value for the alias field.
     * @var        string
     */
    protected $alias;

    /**
     * The value for the redirect field.
     * @var        string
     */
    protected $redirect;

    /**
     * The value for the page404_id field.
     * @var        int
     */
    protected $page404_id;

    /**
     * The value for the page404interface field.
     * @var        string
     */
    protected $page404interface;

    /**
     * The value for the master field.
     * @var        int
     */
    protected $master;

    /**
     * The value for the resourcecompression field.
     * @var        int
     */
    protected $resourcecompression;

    /**
     * The value for the layouts field.
     * @var        string
     */
    protected $layouts;

    /**
     * The value for the phplocale field.
     * @var        string
     */
    protected $phplocale;

    /**
     * The value for the path field.
     * @var        string
     */
    protected $path;

    /**
     * The value for the themeproperties field.
     * @var        string
     */
    protected $themeproperties;

    /**
     * The value for the extproperties field.
     * @var        string
     */
    protected $extproperties;

    /**
     * The value for the email field.
     * @var        string
     */
    protected $email;

    /**
     * The value for the search_index_key field.
     * @var        string
     */
    protected $search_index_key;

    /**
     * The value for the robots field.
     * @var        string
     */
    protected $robots;

    /**
     * The value for the session field.
     * @var        string
     */
    protected $session;

    /**
     * The value for the favicon field.
     * @var        string
     */
    protected $favicon;

    /**
     * @var        PropelObjectCollection|SystemPage[] Collection to store aggregation of SystemPage objects.
     */
    protected $collSystemPages;

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
    protected $systemPagesScheduledForDeletion = null;

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
     * Get the [domain] column value.
     * 
     * @return   string
     */
    public function getDomain()
    {

        return $this->domain;
    }

    /**
     * Get the [title_format] column value.
     * 
     * @return   string
     */
    public function getTitleFormat()
    {

        return $this->title_format;
    }

    /**
     * Get the [lang] column value.
     * 
     * @return   string
     */
    public function getLang()
    {

        return $this->lang;
    }

    /**
     * Get the [startpage_id] column value.
     * 
     * @return   int
     */
    public function getStartpageId()
    {

        return $this->startpage_id;
    }

    /**
     * Get the [alias] column value.
     * 
     * @return   string
     */
    public function getAlias()
    {

        return $this->alias;
    }

    /**
     * Get the [redirect] column value.
     * 
     * @return   string
     */
    public function getRedirect()
    {

        return $this->redirect;
    }

    /**
     * Get the [page404_id] column value.
     * 
     * @return   int
     */
    public function getPage404id()
    {

        return $this->page404_id;
    }

    /**
     * Get the [page404interface] column value.
     * 
     * @return   string
     */
    public function getPage404interface()
    {

        return $this->page404interface;
    }

    /**
     * Get the [master] column value.
     * 
     * @return   int
     */
    public function getMaster()
    {

        return $this->master;
    }

    /**
     * Get the [resourcecompression] column value.
     * 
     * @return   int
     */
    public function getResourcecompression()
    {

        return $this->resourcecompression;
    }

    /**
     * Get the [layouts] column value.
     * 
     * @return   string
     */
    public function getLayouts()
    {

        return $this->layouts;
    }

    /**
     * Get the [phplocale] column value.
     * 
     * @return   string
     */
    public function getPhplocale()
    {

        return $this->phplocale;
    }

    /**
     * Get the [path] column value.
     * 
     * @return   string
     */
    public function getPath()
    {

        return $this->path;
    }

    /**
     * Get the [themeproperties] column value.
     * 
     * @return   string
     */
    public function getThemeproperties()
    {

        return $this->themeproperties;
    }

    /**
     * Get the [extproperties] column value.
     * 
     * @return   string
     */
    public function getExtproperties()
    {

        return $this->extproperties;
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
     * Get the [search_index_key] column value.
     * 
     * @return   string
     */
    public function getSearchIndexKey()
    {

        return $this->search_index_key;
    }

    /**
     * Get the [robots] column value.
     * 
     * @return   string
     */
    public function getRobots()
    {

        return $this->robots;
    }

    /**
     * Get the [session] column value.
     * 
     * @return   string
     */
    public function getSession()
    {

        return $this->session;
    }

    /**
     * Get the [favicon] column value.
     * 
     * @return   string
     */
    public function getFavicon()
    {

        return $this->favicon;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = SystemDomainPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [domain] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setDomain($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->domain !== $v) {
            $this->domain = $v;
            $this->modifiedColumns[] = SystemDomainPeer::DOMAIN;
        }


        return $this;
    } // setDomain()

    /**
     * Set the value of [title_format] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setTitleFormat($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title_format !== $v) {
            $this->title_format = $v;
            $this->modifiedColumns[] = SystemDomainPeer::TITLE_FORMAT;
        }


        return $this;
    } // setTitleFormat()

    /**
     * Set the value of [lang] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setLang($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->lang !== $v) {
            $this->lang = $v;
            $this->modifiedColumns[] = SystemDomainPeer::LANG;
        }


        return $this;
    } // setLang()

    /**
     * Set the value of [startpage_id] column.
     * 
     * @param      int $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setStartpageId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->startpage_id !== $v) {
            $this->startpage_id = $v;
            $this->modifiedColumns[] = SystemDomainPeer::STARTPAGE_ID;
        }


        return $this;
    } // setStartpageId()

    /**
     * Set the value of [alias] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setAlias($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->alias !== $v) {
            $this->alias = $v;
            $this->modifiedColumns[] = SystemDomainPeer::ALIAS;
        }


        return $this;
    } // setAlias()

    /**
     * Set the value of [redirect] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setRedirect($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->redirect !== $v) {
            $this->redirect = $v;
            $this->modifiedColumns[] = SystemDomainPeer::REDIRECT;
        }


        return $this;
    } // setRedirect()

    /**
     * Set the value of [page404_id] column.
     * 
     * @param      int $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setPage404id($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->page404_id !== $v) {
            $this->page404_id = $v;
            $this->modifiedColumns[] = SystemDomainPeer::PAGE404_ID;
        }


        return $this;
    } // setPage404id()

    /**
     * Set the value of [page404interface] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setPage404interface($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->page404interface !== $v) {
            $this->page404interface = $v;
            $this->modifiedColumns[] = SystemDomainPeer::PAGE404INTERFACE;
        }


        return $this;
    } // setPage404interface()

    /**
     * Set the value of [master] column.
     * 
     * @param      int $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setMaster($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->master !== $v) {
            $this->master = $v;
            $this->modifiedColumns[] = SystemDomainPeer::MASTER;
        }


        return $this;
    } // setMaster()

    /**
     * Set the value of [resourcecompression] column.
     * 
     * @param      int $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setResourcecompression($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->resourcecompression !== $v) {
            $this->resourcecompression = $v;
            $this->modifiedColumns[] = SystemDomainPeer::RESOURCECOMPRESSION;
        }


        return $this;
    } // setResourcecompression()

    /**
     * Set the value of [layouts] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setLayouts($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->layouts !== $v) {
            $this->layouts = $v;
            $this->modifiedColumns[] = SystemDomainPeer::LAYOUTS;
        }


        return $this;
    } // setLayouts()

    /**
     * Set the value of [phplocale] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setPhplocale($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->phplocale !== $v) {
            $this->phplocale = $v;
            $this->modifiedColumns[] = SystemDomainPeer::PHPLOCALE;
        }


        return $this;
    } // setPhplocale()

    /**
     * Set the value of [path] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setPath($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->path !== $v) {
            $this->path = $v;
            $this->modifiedColumns[] = SystemDomainPeer::PATH;
        }


        return $this;
    } // setPath()

    /**
     * Set the value of [themeproperties] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setThemeproperties($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->themeproperties !== $v) {
            $this->themeproperties = $v;
            $this->modifiedColumns[] = SystemDomainPeer::THEMEPROPERTIES;
        }


        return $this;
    } // setThemeproperties()

    /**
     * Set the value of [extproperties] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setExtproperties($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->extproperties !== $v) {
            $this->extproperties = $v;
            $this->modifiedColumns[] = SystemDomainPeer::EXTPROPERTIES;
        }


        return $this;
    } // setExtproperties()

    /**
     * Set the value of [email] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setEmail($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[] = SystemDomainPeer::EMAIL;
        }


        return $this;
    } // setEmail()

    /**
     * Set the value of [search_index_key] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setSearchIndexKey($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->search_index_key !== $v) {
            $this->search_index_key = $v;
            $this->modifiedColumns[] = SystemDomainPeer::SEARCH_INDEX_KEY;
        }


        return $this;
    } // setSearchIndexKey()

    /**
     * Set the value of [robots] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setRobots($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->robots !== $v) {
            $this->robots = $v;
            $this->modifiedColumns[] = SystemDomainPeer::ROBOTS;
        }


        return $this;
    } // setRobots()

    /**
     * Set the value of [session] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setSession($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->session !== $v) {
            $this->session = $v;
            $this->modifiedColumns[] = SystemDomainPeer::SESSION;
        }


        return $this;
    } // setSession()

    /**
     * Set the value of [favicon] column.
     * 
     * @param      string $v new value
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function setFavicon($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->favicon !== $v) {
            $this->favicon = $v;
            $this->modifiedColumns[] = SystemDomainPeer::FAVICON;
        }


        return $this;
    } // setFavicon()

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
            $this->domain = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->title_format = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->lang = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->startpage_id = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->alias = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->redirect = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->page404_id = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->page404interface = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->master = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->resourcecompression = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
            $this->layouts = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->phplocale = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->path = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
            $this->themeproperties = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
            $this->extproperties = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
            $this->email = ($row[$startcol + 16] !== null) ? (string) $row[$startcol + 16] : null;
            $this->search_index_key = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
            $this->robots = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
            $this->session = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
            $this->favicon = ($row[$startcol + 20] !== null) ? (string) $row[$startcol + 20] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 21; // 21 = SystemDomainPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating SystemDomain object", $e);
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
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = SystemDomainPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collSystemPages = null;

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
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = SystemDomainQuery::create()
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
            $con = Propel::getConnection(SystemDomainPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                SystemDomainPeer::addInstanceToPool($this);
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

            if ($this->systemPagesScheduledForDeletion !== null) {
                if (!$this->systemPagesScheduledForDeletion->isEmpty()) {
                    foreach ($this->systemPagesScheduledForDeletion as $systemPage) {
                        // need to save related object because we set the relation to null
                        $systemPage->save($con);
                    }
                    $this->systemPagesScheduledForDeletion = null;
                }
            }

            if ($this->collSystemPages !== null) {
                foreach ($this->collSystemPages as $referrerFK) {
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

        $this->modifiedColumns[] = SystemDomainPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . SystemDomainPeer::ID . ')');
        }
        if (null === $this->id) {
            try {				
				$stmt = $con->query("SELECT nextval('kryn_system_domain_id_seq')");
				$row = $stmt->fetch(PDO::FETCH_NUM);
				$this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(SystemDomainPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(SystemDomainPeer::DOMAIN)) {
            $modifiedColumns[':p' . $index++]  = 'DOMAIN';
        }
        if ($this->isColumnModified(SystemDomainPeer::TITLE_FORMAT)) {
            $modifiedColumns[':p' . $index++]  = 'TITLE_FORMAT';
        }
        if ($this->isColumnModified(SystemDomainPeer::LANG)) {
            $modifiedColumns[':p' . $index++]  = 'LANG';
        }
        if ($this->isColumnModified(SystemDomainPeer::STARTPAGE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'STARTPAGE_ID';
        }
        if ($this->isColumnModified(SystemDomainPeer::ALIAS)) {
            $modifiedColumns[':p' . $index++]  = 'ALIAS';
        }
        if ($this->isColumnModified(SystemDomainPeer::REDIRECT)) {
            $modifiedColumns[':p' . $index++]  = 'REDIRECT';
        }
        if ($this->isColumnModified(SystemDomainPeer::PAGE404_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PAGE404_ID';
        }
        if ($this->isColumnModified(SystemDomainPeer::PAGE404INTERFACE)) {
            $modifiedColumns[':p' . $index++]  = 'PAGE404INTERFACE';
        }
        if ($this->isColumnModified(SystemDomainPeer::MASTER)) {
            $modifiedColumns[':p' . $index++]  = 'MASTER';
        }
        if ($this->isColumnModified(SystemDomainPeer::RESOURCECOMPRESSION)) {
            $modifiedColumns[':p' . $index++]  = 'RESOURCECOMPRESSION';
        }
        if ($this->isColumnModified(SystemDomainPeer::LAYOUTS)) {
            $modifiedColumns[':p' . $index++]  = 'LAYOUTS';
        }
        if ($this->isColumnModified(SystemDomainPeer::PHPLOCALE)) {
            $modifiedColumns[':p' . $index++]  = 'PHPLOCALE';
        }
        if ($this->isColumnModified(SystemDomainPeer::PATH)) {
            $modifiedColumns[':p' . $index++]  = 'PATH';
        }
        if ($this->isColumnModified(SystemDomainPeer::THEMEPROPERTIES)) {
            $modifiedColumns[':p' . $index++]  = 'THEMEPROPERTIES';
        }
        if ($this->isColumnModified(SystemDomainPeer::EXTPROPERTIES)) {
            $modifiedColumns[':p' . $index++]  = 'EXTPROPERTIES';
        }
        if ($this->isColumnModified(SystemDomainPeer::EMAIL)) {
            $modifiedColumns[':p' . $index++]  = 'EMAIL';
        }
        if ($this->isColumnModified(SystemDomainPeer::SEARCH_INDEX_KEY)) {
            $modifiedColumns[':p' . $index++]  = 'SEARCH_INDEX_KEY';
        }
        if ($this->isColumnModified(SystemDomainPeer::ROBOTS)) {
            $modifiedColumns[':p' . $index++]  = 'ROBOTS';
        }
        if ($this->isColumnModified(SystemDomainPeer::SESSION)) {
            $modifiedColumns[':p' . $index++]  = 'SESSION';
        }
        if ($this->isColumnModified(SystemDomainPeer::FAVICON)) {
            $modifiedColumns[':p' . $index++]  = 'FAVICON';
        }

        $sql = sprintf(
            'INSERT INTO kryn_system_domain (%s) VALUES (%s)',
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
                    case 'DOMAIN':
						$stmt->bindValue($identifier, $this->domain, PDO::PARAM_STR);
                        break;
                    case 'TITLE_FORMAT':
						$stmt->bindValue($identifier, $this->title_format, PDO::PARAM_STR);
                        break;
                    case 'LANG':
						$stmt->bindValue($identifier, $this->lang, PDO::PARAM_STR);
                        break;
                    case 'STARTPAGE_ID':
						$stmt->bindValue($identifier, $this->startpage_id, PDO::PARAM_INT);
                        break;
                    case 'ALIAS':
						$stmt->bindValue($identifier, $this->alias, PDO::PARAM_STR);
                        break;
                    case 'REDIRECT':
						$stmt->bindValue($identifier, $this->redirect, PDO::PARAM_STR);
                        break;
                    case 'PAGE404_ID':
						$stmt->bindValue($identifier, $this->page404_id, PDO::PARAM_INT);
                        break;
                    case 'PAGE404INTERFACE':
						$stmt->bindValue($identifier, $this->page404interface, PDO::PARAM_STR);
                        break;
                    case 'MASTER':
						$stmt->bindValue($identifier, $this->master, PDO::PARAM_INT);
                        break;
                    case 'RESOURCECOMPRESSION':
						$stmt->bindValue($identifier, $this->resourcecompression, PDO::PARAM_INT);
                        break;
                    case 'LAYOUTS':
						$stmt->bindValue($identifier, $this->layouts, PDO::PARAM_STR);
                        break;
                    case 'PHPLOCALE':
						$stmt->bindValue($identifier, $this->phplocale, PDO::PARAM_STR);
                        break;
                    case 'PATH':
						$stmt->bindValue($identifier, $this->path, PDO::PARAM_STR);
                        break;
                    case 'THEMEPROPERTIES':
						$stmt->bindValue($identifier, $this->themeproperties, PDO::PARAM_STR);
                        break;
                    case 'EXTPROPERTIES':
						$stmt->bindValue($identifier, $this->extproperties, PDO::PARAM_STR);
                        break;
                    case 'EMAIL':
						$stmt->bindValue($identifier, $this->email, PDO::PARAM_STR);
                        break;
                    case 'SEARCH_INDEX_KEY':
						$stmt->bindValue($identifier, $this->search_index_key, PDO::PARAM_STR);
                        break;
                    case 'ROBOTS':
						$stmt->bindValue($identifier, $this->robots, PDO::PARAM_STR);
                        break;
                    case 'SESSION':
						$stmt->bindValue($identifier, $this->session, PDO::PARAM_STR);
                        break;
                    case 'FAVICON':
						$stmt->bindValue($identifier, $this->favicon, PDO::PARAM_STR);
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


            if (($retval = SystemDomainPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collSystemPages !== null) {
                    foreach ($this->collSystemPages as $referrerFK) {
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
        $pos = SystemDomainPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDomain();
                break;
            case 2:
                return $this->getTitleFormat();
                break;
            case 3:
                return $this->getLang();
                break;
            case 4:
                return $this->getStartpageId();
                break;
            case 5:
                return $this->getAlias();
                break;
            case 6:
                return $this->getRedirect();
                break;
            case 7:
                return $this->getPage404id();
                break;
            case 8:
                return $this->getPage404interface();
                break;
            case 9:
                return $this->getMaster();
                break;
            case 10:
                return $this->getResourcecompression();
                break;
            case 11:
                return $this->getLayouts();
                break;
            case 12:
                return $this->getPhplocale();
                break;
            case 13:
                return $this->getPath();
                break;
            case 14:
                return $this->getThemeproperties();
                break;
            case 15:
                return $this->getExtproperties();
                break;
            case 16:
                return $this->getEmail();
                break;
            case 17:
                return $this->getSearchIndexKey();
                break;
            case 18:
                return $this->getRobots();
                break;
            case 19:
                return $this->getSession();
                break;
            case 20:
                return $this->getFavicon();
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
        if (isset($alreadyDumpedObjects['SystemDomain'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['SystemDomain'][$this->getPrimaryKey()] = true;
        $keys = SystemDomainPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getDomain(),
            $keys[2] => $this->getTitleFormat(),
            $keys[3] => $this->getLang(),
            $keys[4] => $this->getStartpageId(),
            $keys[5] => $this->getAlias(),
            $keys[6] => $this->getRedirect(),
            $keys[7] => $this->getPage404id(),
            $keys[8] => $this->getPage404interface(),
            $keys[9] => $this->getMaster(),
            $keys[10] => $this->getResourcecompression(),
            $keys[11] => $this->getLayouts(),
            $keys[12] => $this->getPhplocale(),
            $keys[13] => $this->getPath(),
            $keys[14] => $this->getThemeproperties(),
            $keys[15] => $this->getExtproperties(),
            $keys[16] => $this->getEmail(),
            $keys[17] => $this->getSearchIndexKey(),
            $keys[18] => $this->getRobots(),
            $keys[19] => $this->getSession(),
            $keys[20] => $this->getFavicon(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->collSystemPages) {
                $result['SystemPages'] = $this->collSystemPages->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = SystemDomainPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setDomain($value);
                break;
            case 2:
                $this->setTitleFormat($value);
                break;
            case 3:
                $this->setLang($value);
                break;
            case 4:
                $this->setStartpageId($value);
                break;
            case 5:
                $this->setAlias($value);
                break;
            case 6:
                $this->setRedirect($value);
                break;
            case 7:
                $this->setPage404id($value);
                break;
            case 8:
                $this->setPage404interface($value);
                break;
            case 9:
                $this->setMaster($value);
                break;
            case 10:
                $this->setResourcecompression($value);
                break;
            case 11:
                $this->setLayouts($value);
                break;
            case 12:
                $this->setPhplocale($value);
                break;
            case 13:
                $this->setPath($value);
                break;
            case 14:
                $this->setThemeproperties($value);
                break;
            case 15:
                $this->setExtproperties($value);
                break;
            case 16:
                $this->setEmail($value);
                break;
            case 17:
                $this->setSearchIndexKey($value);
                break;
            case 18:
                $this->setRobots($value);
                break;
            case 19:
                $this->setSession($value);
                break;
            case 20:
                $this->setFavicon($value);
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
        $keys = SystemDomainPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setDomain($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setTitleFormat($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setLang($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setStartpageId($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setAlias($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setRedirect($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setPage404id($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setPage404interface($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setMaster($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setResourcecompression($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setLayouts($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setPhplocale($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setPath($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setThemeproperties($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setExtproperties($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setEmail($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setSearchIndexKey($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setRobots($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setSession($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setFavicon($arr[$keys[20]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SystemDomainPeer::DATABASE_NAME);

        if ($this->isColumnModified(SystemDomainPeer::ID)) $criteria->add(SystemDomainPeer::ID, $this->id);
        if ($this->isColumnModified(SystemDomainPeer::DOMAIN)) $criteria->add(SystemDomainPeer::DOMAIN, $this->domain);
        if ($this->isColumnModified(SystemDomainPeer::TITLE_FORMAT)) $criteria->add(SystemDomainPeer::TITLE_FORMAT, $this->title_format);
        if ($this->isColumnModified(SystemDomainPeer::LANG)) $criteria->add(SystemDomainPeer::LANG, $this->lang);
        if ($this->isColumnModified(SystemDomainPeer::STARTPAGE_ID)) $criteria->add(SystemDomainPeer::STARTPAGE_ID, $this->startpage_id);
        if ($this->isColumnModified(SystemDomainPeer::ALIAS)) $criteria->add(SystemDomainPeer::ALIAS, $this->alias);
        if ($this->isColumnModified(SystemDomainPeer::REDIRECT)) $criteria->add(SystemDomainPeer::REDIRECT, $this->redirect);
        if ($this->isColumnModified(SystemDomainPeer::PAGE404_ID)) $criteria->add(SystemDomainPeer::PAGE404_ID, $this->page404_id);
        if ($this->isColumnModified(SystemDomainPeer::PAGE404INTERFACE)) $criteria->add(SystemDomainPeer::PAGE404INTERFACE, $this->page404interface);
        if ($this->isColumnModified(SystemDomainPeer::MASTER)) $criteria->add(SystemDomainPeer::MASTER, $this->master);
        if ($this->isColumnModified(SystemDomainPeer::RESOURCECOMPRESSION)) $criteria->add(SystemDomainPeer::RESOURCECOMPRESSION, $this->resourcecompression);
        if ($this->isColumnModified(SystemDomainPeer::LAYOUTS)) $criteria->add(SystemDomainPeer::LAYOUTS, $this->layouts);
        if ($this->isColumnModified(SystemDomainPeer::PHPLOCALE)) $criteria->add(SystemDomainPeer::PHPLOCALE, $this->phplocale);
        if ($this->isColumnModified(SystemDomainPeer::PATH)) $criteria->add(SystemDomainPeer::PATH, $this->path);
        if ($this->isColumnModified(SystemDomainPeer::THEMEPROPERTIES)) $criteria->add(SystemDomainPeer::THEMEPROPERTIES, $this->themeproperties);
        if ($this->isColumnModified(SystemDomainPeer::EXTPROPERTIES)) $criteria->add(SystemDomainPeer::EXTPROPERTIES, $this->extproperties);
        if ($this->isColumnModified(SystemDomainPeer::EMAIL)) $criteria->add(SystemDomainPeer::EMAIL, $this->email);
        if ($this->isColumnModified(SystemDomainPeer::SEARCH_INDEX_KEY)) $criteria->add(SystemDomainPeer::SEARCH_INDEX_KEY, $this->search_index_key);
        if ($this->isColumnModified(SystemDomainPeer::ROBOTS)) $criteria->add(SystemDomainPeer::ROBOTS, $this->robots);
        if ($this->isColumnModified(SystemDomainPeer::SESSION)) $criteria->add(SystemDomainPeer::SESSION, $this->session);
        if ($this->isColumnModified(SystemDomainPeer::FAVICON)) $criteria->add(SystemDomainPeer::FAVICON, $this->favicon);

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
        $criteria = new Criteria(SystemDomainPeer::DATABASE_NAME);
        $criteria->add(SystemDomainPeer::ID, $this->id);

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
     * @param      object $copyObj An object of SystemDomain (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setDomain($this->getDomain());
        $copyObj->setTitleFormat($this->getTitleFormat());
        $copyObj->setLang($this->getLang());
        $copyObj->setStartpageId($this->getStartpageId());
        $copyObj->setAlias($this->getAlias());
        $copyObj->setRedirect($this->getRedirect());
        $copyObj->setPage404id($this->getPage404id());
        $copyObj->setPage404interface($this->getPage404interface());
        $copyObj->setMaster($this->getMaster());
        $copyObj->setResourcecompression($this->getResourcecompression());
        $copyObj->setLayouts($this->getLayouts());
        $copyObj->setPhplocale($this->getPhplocale());
        $copyObj->setPath($this->getPath());
        $copyObj->setThemeproperties($this->getThemeproperties());
        $copyObj->setExtproperties($this->getExtproperties());
        $copyObj->setEmail($this->getEmail());
        $copyObj->setSearchIndexKey($this->getSearchIndexKey());
        $copyObj->setRobots($this->getRobots());
        $copyObj->setSession($this->getSession());
        $copyObj->setFavicon($this->getFavicon());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getSystemPages() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSystemPage($relObj->copy($deepCopy));
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
     * @return                 SystemDomain Clone of current object.
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
     * @return   SystemDomainPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new SystemDomainPeer();
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
        if ('SystemPage' == $relationName) {
            $this->initSystemPages();
        }
    }

    /**
     * Clears out the collSystemPages collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addSystemPages()
     */
    public function clearSystemPages()
    {
        $this->collSystemPages = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collSystemPages collection.
     *
     * By default this just sets the collSystemPages collection to an empty array (like clearcollSystemPages());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSystemPages($overrideExisting = true)
    {
        if (null !== $this->collSystemPages && !$overrideExisting) {
            return;
        }
        $this->collSystemPages = new PropelObjectCollection();
        $this->collSystemPages->setModel('SystemPage');
    }

    /**
     * Gets an array of SystemPage objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this SystemDomain is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|SystemPage[] List of SystemPage objects
     * @throws PropelException
     */
    public function getSystemPages($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collSystemPages || null !== $criteria) {
            if ($this->isNew() && null === $this->collSystemPages) {
                // return empty collection
                $this->initSystemPages();
            } else {
                $collSystemPages = SystemPageQuery::create(null, $criteria)
                    ->filterBySystemDomain($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collSystemPages;
                }
                $this->collSystemPages = $collSystemPages;
            }
        }

        return $this->collSystemPages;
    }

    /**
     * Sets a collection of SystemPage objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $systemPages A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setSystemPages(PropelCollection $systemPages, PropelPDO $con = null)
    {
        $this->systemPagesScheduledForDeletion = $this->getSystemPages(new Criteria(), $con)->diff($systemPages);

        foreach ($this->systemPagesScheduledForDeletion as $systemPageRemoved) {
            $systemPageRemoved->setSystemDomain(null);
        }

        $this->collSystemPages = null;
        foreach ($systemPages as $systemPage) {
            $this->addSystemPage($systemPage);
        }

        $this->collSystemPages = $systemPages;
    }

    /**
     * Returns the number of related SystemPage objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related SystemPage objects.
     * @throws PropelException
     */
    public function countSystemPages(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collSystemPages || null !== $criteria) {
            if ($this->isNew() && null === $this->collSystemPages) {
                return 0;
            } else {
                $query = SystemPageQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterBySystemDomain($this)
                    ->count($con);
            }
        } else {
            return count($this->collSystemPages);
        }
    }

    /**
     * Method called to associate a SystemPage object to this object
     * through the SystemPage foreign key attribute.
     *
     * @param    SystemPage $l SystemPage
     * @return   SystemDomain The current object (for fluent API support)
     */
    public function addSystemPage(SystemPage $l)
    {
        if ($this->collSystemPages === null) {
            $this->initSystemPages();
        }
        if (!$this->collSystemPages->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddSystemPage($l);
        }

        return $this;
    }

    /**
     * @param	SystemPage $systemPage The systemPage object to add.
     */
    protected function doAddSystemPage($systemPage)
    {
        $this->collSystemPages[]= $systemPage;
        $systemPage->setSystemDomain($this);
    }

    /**
     * @param	SystemPage $systemPage The systemPage object to remove.
     */
    public function removeSystemPage($systemPage)
    {
        if ($this->getSystemPages()->contains($systemPage)) {
            $this->collSystemPages->remove($this->collSystemPages->search($systemPage));
            if (null === $this->systemPagesScheduledForDeletion) {
                $this->systemPagesScheduledForDeletion = clone $this->collSystemPages;
                $this->systemPagesScheduledForDeletion->clear();
            }
            $this->systemPagesScheduledForDeletion[]= $systemPage;
            $systemPage->setSystemDomain(null);
        }
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->domain = null;
        $this->title_format = null;
        $this->lang = null;
        $this->startpage_id = null;
        $this->alias = null;
        $this->redirect = null;
        $this->page404_id = null;
        $this->page404interface = null;
        $this->master = null;
        $this->resourcecompression = null;
        $this->layouts = null;
        $this->phplocale = null;
        $this->path = null;
        $this->themeproperties = null;
        $this->extproperties = null;
        $this->email = null;
        $this->search_index_key = null;
        $this->robots = null;
        $this->session = null;
        $this->favicon = null;
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
            if ($this->collSystemPages) {
                foreach ($this->collSystemPages as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collSystemPages instanceof PropelCollection) {
            $this->collSystemPages->clearIterator();
        }
        $this->collSystemPages = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(SystemDomainPeer::DEFAULT_STRING_FORMAT);
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

} // BaseSystemDomain

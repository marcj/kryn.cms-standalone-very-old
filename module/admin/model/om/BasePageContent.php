<?php


/**
 * Base class that represents a row from the 'kryn_system_page_content' table.
 *
 * 
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BasePageContent extends BaseObject 
{

    /**
     * Peer class name
     */
    const PEER = 'PageContentPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PageContentPeer
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
     * The value for the page_id field.
     * @var        int
     */
    protected $page_id;

    /**
     * The value for the version_id field.
     * @var        int
     */
    protected $version_id;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the content field.
     * @var        string
     */
    protected $content;

    /**
     * The value for the template field.
     * @var        string
     */
    protected $template;

    /**
     * The value for the type field.
     * @var        string
     */
    protected $type;

    /**
     * The value for the hide field.
     * @var        int
     */
    protected $hide;

    /**
     * The value for the sort field.
     * @var        int
     */
    protected $sort;

    /**
     * The value for the box_id field.
     * @var        int
     */
    protected $box_id;

    /**
     * The value for the owner_id field.
     * @var        int
     */
    protected $owner_id;

    /**
     * The value for the access_from field.
     * @var        int
     */
    protected $access_from;

    /**
     * The value for the access_to field.
     * @var        int
     */
    protected $access_to;

    /**
     * The value for the access_from_groups field.
     * @var        string
     */
    protected $access_from_groups;

    /**
     * The value for the unsearchable field.
     * @var        int
     */
    protected $unsearchable;

    /**
     * @var        Page
     */
    protected $aPage;

    /**
     * @var        PageVersion
     */
    protected $aPageVersion;

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
     * Get the [id] column value.
     * 
     * @return   int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [page_id] column value.
     * 
     * @return   int
     */
    public function getPageId()
    {

        return $this->page_id;
    }

    /**
     * Get the [version_id] column value.
     * 
     * @return   int
     */
    public function getVersionId()
    {

        return $this->version_id;
    }

    /**
     * Get the [title] column value.
     * 
     * @return   string
     */
    public function getTitle()
    {

        return $this->title;
    }

    /**
     * Get the [content] column value.
     * 
     * @return   string
     */
    public function getContent()
    {

        return $this->content;
    }

    /**
     * Get the [template] column value.
     * 
     * @return   string
     */
    public function getTemplate()
    {

        return $this->template;
    }

    /**
     * Get the [type] column value.
     * 
     * @return   string
     */
    public function getType()
    {

        return $this->type;
    }

    /**
     * Get the [hide] column value.
     * 
     * @return   int
     */
    public function getHide()
    {

        return $this->hide;
    }

    /**
     * Get the [sort] column value.
     * 
     * @return   int
     */
    public function getSort()
    {

        return $this->sort;
    }

    /**
     * Get the [box_id] column value.
     * 
     * @return   int
     */
    public function getBoxId()
    {

        return $this->box_id;
    }

    /**
     * Get the [owner_id] column value.
     * 
     * @return   int
     */
    public function getOwnerId()
    {

        return $this->owner_id;
    }

    /**
     * Get the [access_from] column value.
     * 
     * @return   int
     */
    public function getAccessFrom()
    {

        return $this->access_from;
    }

    /**
     * Get the [access_to] column value.
     * 
     * @return   int
     */
    public function getAccessTo()
    {

        return $this->access_to;
    }

    /**
     * Get the [access_from_groups] column value.
     * 
     * @return   string
     */
    public function getAccessFromGroups()
    {

        return $this->access_from_groups;
    }

    /**
     * Get the [unsearchable] column value.
     * 
     * @return   int
     */
    public function getUnsearchable()
    {

        return $this->unsearchable;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = PageContentPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [page_id] column.
     * 
     * @param      int $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setPageId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->page_id !== $v) {
            $this->page_id = $v;
            $this->modifiedColumns[] = PageContentPeer::PAGE_ID;
        }

        if ($this->aPage !== null && $this->aPage->getId() !== $v) {
            $this->aPage = null;
        }


        return $this;
    } // setPageId()

    /**
     * Set the value of [version_id] column.
     * 
     * @param      int $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setVersionId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->version_id !== $v) {
            $this->version_id = $v;
            $this->modifiedColumns[] = PageContentPeer::VERSION_ID;
        }

        if ($this->aPageVersion !== null && $this->aPageVersion->getId() !== $v) {
            $this->aPageVersion = null;
        }


        return $this;
    } // setVersionId()

    /**
     * Set the value of [title] column.
     * 
     * @param      string $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = PageContentPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [content] column.
     * 
     * @param      string $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setContent($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->content !== $v) {
            $this->content = $v;
            $this->modifiedColumns[] = PageContentPeer::CONTENT;
        }


        return $this;
    } // setContent()

    /**
     * Set the value of [template] column.
     * 
     * @param      string $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setTemplate($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->template !== $v) {
            $this->template = $v;
            $this->modifiedColumns[] = PageContentPeer::TEMPLATE;
        }


        return $this;
    } // setTemplate()

    /**
     * Set the value of [type] column.
     * 
     * @param      string $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = PageContentPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [hide] column.
     * 
     * @param      int $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setHide($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->hide !== $v) {
            $this->hide = $v;
            $this->modifiedColumns[] = PageContentPeer::HIDE;
        }


        return $this;
    } // setHide()

    /**
     * Set the value of [sort] column.
     * 
     * @param      int $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setSort($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->sort !== $v) {
            $this->sort = $v;
            $this->modifiedColumns[] = PageContentPeer::SORT;
        }


        return $this;
    } // setSort()

    /**
     * Set the value of [box_id] column.
     * 
     * @param      int $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setBoxId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->box_id !== $v) {
            $this->box_id = $v;
            $this->modifiedColumns[] = PageContentPeer::BOX_ID;
        }


        return $this;
    } // setBoxId()

    /**
     * Set the value of [owner_id] column.
     * 
     * @param      int $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setOwnerId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->owner_id !== $v) {
            $this->owner_id = $v;
            $this->modifiedColumns[] = PageContentPeer::OWNER_ID;
        }


        return $this;
    } // setOwnerId()

    /**
     * Set the value of [access_from] column.
     * 
     * @param      int $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setAccessFrom($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->access_from !== $v) {
            $this->access_from = $v;
            $this->modifiedColumns[] = PageContentPeer::ACCESS_FROM;
        }


        return $this;
    } // setAccessFrom()

    /**
     * Set the value of [access_to] column.
     * 
     * @param      int $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setAccessTo($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->access_to !== $v) {
            $this->access_to = $v;
            $this->modifiedColumns[] = PageContentPeer::ACCESS_TO;
        }


        return $this;
    } // setAccessTo()

    /**
     * Set the value of [access_from_groups] column.
     * 
     * @param      string $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setAccessFromGroups($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->access_from_groups !== $v) {
            $this->access_from_groups = $v;
            $this->modifiedColumns[] = PageContentPeer::ACCESS_FROM_GROUPS;
        }


        return $this;
    } // setAccessFromGroups()

    /**
     * Set the value of [unsearchable] column.
     * 
     * @param      int $v new value
     * @return   PageContent The current object (for fluent API support)
     */
    public function setUnsearchable($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->unsearchable !== $v) {
            $this->unsearchable = $v;
            $this->modifiedColumns[] = PageContentPeer::UNSEARCHABLE;
        }


        return $this;
    } // setUnsearchable()

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
            $this->page_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->version_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->title = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
            $this->content = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->template = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->type = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->hide = ($row[$startcol + 7] !== null) ? (int) $row[$startcol + 7] : null;
            $this->sort = ($row[$startcol + 8] !== null) ? (int) $row[$startcol + 8] : null;
            $this->box_id = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->owner_id = ($row[$startcol + 10] !== null) ? (int) $row[$startcol + 10] : null;
            $this->access_from = ($row[$startcol + 11] !== null) ? (int) $row[$startcol + 11] : null;
            $this->access_to = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
            $this->access_from_groups = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
            $this->unsearchable = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 15; // 15 = PageContentPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating PageContent object", $e);
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

        if ($this->aPage !== null && $this->page_id !== $this->aPage->getId()) {
            $this->aPage = null;
        }
        if ($this->aPageVersion !== null && $this->version_id !== $this->aPageVersion->getId()) {
            $this->aPageVersion = null;
        }
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
            $con = Propel::getConnection(PageContentPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PageContentPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aPage = null;
            $this->aPageVersion = null;
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
            $con = Propel::getConnection(PageContentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PageContentQuery::create()
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
            $con = Propel::getConnection(PageContentPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                PageContentPeer::addInstanceToPool($this);
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

            // We call the save method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aPage !== null) {
                if ($this->aPage->isModified() || $this->aPage->isNew()) {
                    $affectedRows += $this->aPage->save($con);
                }
                $this->setPage($this->aPage);
            }

            if ($this->aPageVersion !== null) {
                if ($this->aPageVersion->isModified() || $this->aPageVersion->isNew()) {
                    $affectedRows += $this->aPageVersion->save($con);
                }
                $this->setPageVersion($this->aPageVersion);
            }

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

        $this->modifiedColumns[] = PageContentPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PageContentPeer::ID . ')');
        }
        if (null === $this->id) {
            try {				
				$stmt = $con->query("SELECT nextval('kryn_system_page_content_id_seq')");
				$row = $stmt->fetch(PDO::FETCH_NUM);
				$this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PageContentPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(PageContentPeer::PAGE_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PAGE_ID';
        }
        if ($this->isColumnModified(PageContentPeer::VERSION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'VERSION_ID';
        }
        if ($this->isColumnModified(PageContentPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'TITLE';
        }
        if ($this->isColumnModified(PageContentPeer::CONTENT)) {
            $modifiedColumns[':p' . $index++]  = 'CONTENT';
        }
        if ($this->isColumnModified(PageContentPeer::TEMPLATE)) {
            $modifiedColumns[':p' . $index++]  = 'TEMPLATE';
        }
        if ($this->isColumnModified(PageContentPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'TYPE';
        }
        if ($this->isColumnModified(PageContentPeer::HIDE)) {
            $modifiedColumns[':p' . $index++]  = 'HIDE';
        }
        if ($this->isColumnModified(PageContentPeer::SORT)) {
            $modifiedColumns[':p' . $index++]  = 'SORT';
        }
        if ($this->isColumnModified(PageContentPeer::BOX_ID)) {
            $modifiedColumns[':p' . $index++]  = 'BOX_ID';
        }
        if ($this->isColumnModified(PageContentPeer::OWNER_ID)) {
            $modifiedColumns[':p' . $index++]  = 'OWNER_ID';
        }
        if ($this->isColumnModified(PageContentPeer::ACCESS_FROM)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_FROM';
        }
        if ($this->isColumnModified(PageContentPeer::ACCESS_TO)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_TO';
        }
        if ($this->isColumnModified(PageContentPeer::ACCESS_FROM_GROUPS)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_FROM_GROUPS';
        }
        if ($this->isColumnModified(PageContentPeer::UNSEARCHABLE)) {
            $modifiedColumns[':p' . $index++]  = 'UNSEARCHABLE';
        }

        $sql = sprintf(
            'INSERT INTO kryn_system_page_content (%s) VALUES (%s)',
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
                    case 'PAGE_ID':
						$stmt->bindValue($identifier, $this->page_id, PDO::PARAM_INT);
                        break;
                    case 'VERSION_ID':
						$stmt->bindValue($identifier, $this->version_id, PDO::PARAM_INT);
                        break;
                    case 'TITLE':
						$stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case 'CONTENT':
						$stmt->bindValue($identifier, $this->content, PDO::PARAM_STR);
                        break;
                    case 'TEMPLATE':
						$stmt->bindValue($identifier, $this->template, PDO::PARAM_STR);
                        break;
                    case 'TYPE':
						$stmt->bindValue($identifier, $this->type, PDO::PARAM_STR);
                        break;
                    case 'HIDE':
						$stmt->bindValue($identifier, $this->hide, PDO::PARAM_INT);
                        break;
                    case 'SORT':
						$stmt->bindValue($identifier, $this->sort, PDO::PARAM_INT);
                        break;
                    case 'BOX_ID':
						$stmt->bindValue($identifier, $this->box_id, PDO::PARAM_INT);
                        break;
                    case 'OWNER_ID':
						$stmt->bindValue($identifier, $this->owner_id, PDO::PARAM_INT);
                        break;
                    case 'ACCESS_FROM':
						$stmt->bindValue($identifier, $this->access_from, PDO::PARAM_INT);
                        break;
                    case 'ACCESS_TO':
						$stmt->bindValue($identifier, $this->access_to, PDO::PARAM_INT);
                        break;
                    case 'ACCESS_FROM_GROUPS':
						$stmt->bindValue($identifier, $this->access_from_groups, PDO::PARAM_STR);
                        break;
                    case 'UNSEARCHABLE':
						$stmt->bindValue($identifier, $this->unsearchable, PDO::PARAM_INT);
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


            // We call the validate method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aPage !== null) {
                if (!$this->aPage->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPage->getValidationFailures());
                }
            }

            if ($this->aPageVersion !== null) {
                if (!$this->aPageVersion->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPageVersion->getValidationFailures());
                }
            }


            if (($retval = PageContentPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
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
        $pos = PageContentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getPageId();
                break;
            case 2:
                return $this->getVersionId();
                break;
            case 3:
                return $this->getTitle();
                break;
            case 4:
                return $this->getContent();
                break;
            case 5:
                return $this->getTemplate();
                break;
            case 6:
                return $this->getType();
                break;
            case 7:
                return $this->getHide();
                break;
            case 8:
                return $this->getSort();
                break;
            case 9:
                return $this->getBoxId();
                break;
            case 10:
                return $this->getOwnerId();
                break;
            case 11:
                return $this->getAccessFrom();
                break;
            case 12:
                return $this->getAccessTo();
                break;
            case 13:
                return $this->getAccessFromGroups();
                break;
            case 14:
                return $this->getUnsearchable();
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
        if (isset($alreadyDumpedObjects['PageContent'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['PageContent'][$this->getPrimaryKey()] = true;
        $keys = PageContentPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getPageId(),
            $keys[2] => $this->getVersionId(),
            $keys[3] => $this->getTitle(),
            $keys[4] => $this->getContent(),
            $keys[5] => $this->getTemplate(),
            $keys[6] => $this->getType(),
            $keys[7] => $this->getHide(),
            $keys[8] => $this->getSort(),
            $keys[9] => $this->getBoxId(),
            $keys[10] => $this->getOwnerId(),
            $keys[11] => $this->getAccessFrom(),
            $keys[12] => $this->getAccessTo(),
            $keys[13] => $this->getAccessFromGroups(),
            $keys[14] => $this->getUnsearchable(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aPage) {
                $result['Page'] = $this->aPage->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aPageVersion) {
                $result['PageVersion'] = $this->aPageVersion->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
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
        $pos = PageContentPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setPageId($value);
                break;
            case 2:
                $this->setVersionId($value);
                break;
            case 3:
                $this->setTitle($value);
                break;
            case 4:
                $this->setContent($value);
                break;
            case 5:
                $this->setTemplate($value);
                break;
            case 6:
                $this->setType($value);
                break;
            case 7:
                $this->setHide($value);
                break;
            case 8:
                $this->setSort($value);
                break;
            case 9:
                $this->setBoxId($value);
                break;
            case 10:
                $this->setOwnerId($value);
                break;
            case 11:
                $this->setAccessFrom($value);
                break;
            case 12:
                $this->setAccessTo($value);
                break;
            case 13:
                $this->setAccessFromGroups($value);
                break;
            case 14:
                $this->setUnsearchable($value);
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
        $keys = PageContentPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setPageId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setVersionId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setTitle($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setContent($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setTemplate($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setType($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setHide($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setSort($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setBoxId($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setOwnerId($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setAccessFrom($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setAccessTo($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setAccessFromGroups($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setUnsearchable($arr[$keys[14]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PageContentPeer::DATABASE_NAME);

        if ($this->isColumnModified(PageContentPeer::ID)) $criteria->add(PageContentPeer::ID, $this->id);
        if ($this->isColumnModified(PageContentPeer::PAGE_ID)) $criteria->add(PageContentPeer::PAGE_ID, $this->page_id);
        if ($this->isColumnModified(PageContentPeer::VERSION_ID)) $criteria->add(PageContentPeer::VERSION_ID, $this->version_id);
        if ($this->isColumnModified(PageContentPeer::TITLE)) $criteria->add(PageContentPeer::TITLE, $this->title);
        if ($this->isColumnModified(PageContentPeer::CONTENT)) $criteria->add(PageContentPeer::CONTENT, $this->content);
        if ($this->isColumnModified(PageContentPeer::TEMPLATE)) $criteria->add(PageContentPeer::TEMPLATE, $this->template);
        if ($this->isColumnModified(PageContentPeer::TYPE)) $criteria->add(PageContentPeer::TYPE, $this->type);
        if ($this->isColumnModified(PageContentPeer::HIDE)) $criteria->add(PageContentPeer::HIDE, $this->hide);
        if ($this->isColumnModified(PageContentPeer::SORT)) $criteria->add(PageContentPeer::SORT, $this->sort);
        if ($this->isColumnModified(PageContentPeer::BOX_ID)) $criteria->add(PageContentPeer::BOX_ID, $this->box_id);
        if ($this->isColumnModified(PageContentPeer::OWNER_ID)) $criteria->add(PageContentPeer::OWNER_ID, $this->owner_id);
        if ($this->isColumnModified(PageContentPeer::ACCESS_FROM)) $criteria->add(PageContentPeer::ACCESS_FROM, $this->access_from);
        if ($this->isColumnModified(PageContentPeer::ACCESS_TO)) $criteria->add(PageContentPeer::ACCESS_TO, $this->access_to);
        if ($this->isColumnModified(PageContentPeer::ACCESS_FROM_GROUPS)) $criteria->add(PageContentPeer::ACCESS_FROM_GROUPS, $this->access_from_groups);
        if ($this->isColumnModified(PageContentPeer::UNSEARCHABLE)) $criteria->add(PageContentPeer::UNSEARCHABLE, $this->unsearchable);

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
        $criteria = new Criteria(PageContentPeer::DATABASE_NAME);
        $criteria->add(PageContentPeer::ID, $this->id);

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
     * @param      object $copyObj An object of PageContent (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setPageId($this->getPageId());
        $copyObj->setVersionId($this->getVersionId());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setContent($this->getContent());
        $copyObj->setTemplate($this->getTemplate());
        $copyObj->setType($this->getType());
        $copyObj->setHide($this->getHide());
        $copyObj->setSort($this->getSort());
        $copyObj->setBoxId($this->getBoxId());
        $copyObj->setOwnerId($this->getOwnerId());
        $copyObj->setAccessFrom($this->getAccessFrom());
        $copyObj->setAccessTo($this->getAccessTo());
        $copyObj->setAccessFromGroups($this->getAccessFromGroups());
        $copyObj->setUnsearchable($this->getUnsearchable());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

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
     * @return                 PageContent Clone of current object.
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
     * @return   PageContentPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PageContentPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Page object.
     *
     * @param                  Page $v
     * @return                 PageContent The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPage(Page $v = null)
    {
        if ($v === null) {
            $this->setPageId(NULL);
        } else {
            $this->setPageId($v->getId());
        }

        $this->aPage = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Page object, it will not be re-added.
        if ($v !== null) {
            $v->addPageContent($this);
        }


        return $this;
    }


    /**
     * Get the associated Page object
     *
     * @param      PropelPDO $con Optional Connection object.
     * @return                 Page The associated Page object.
     * @throws PropelException
     */
    public function getPage(PropelPDO $con = null)
    {
        if ($this->aPage === null && ($this->page_id !== null)) {
            $this->aPage = PageQuery::create()->findPk($this->page_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPage->addPageContents($this);
             */
        }

        return $this->aPage;
    }

    /**
     * Declares an association between this object and a PageVersion object.
     *
     * @param                  PageVersion $v
     * @return                 PageContent The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPageVersion(PageVersion $v = null)
    {
        if ($v === null) {
            $this->setVersionId(NULL);
        } else {
            $this->setVersionId($v->getId());
        }

        $this->aPageVersion = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the PageVersion object, it will not be re-added.
        if ($v !== null) {
            $v->addPageContent($this);
        }


        return $this;
    }


    /**
     * Get the associated PageVersion object
     *
     * @param      PropelPDO $con Optional Connection object.
     * @return                 PageVersion The associated PageVersion object.
     * @throws PropelException
     */
    public function getPageVersion(PropelPDO $con = null)
    {
        if ($this->aPageVersion === null && ($this->version_id !== null)) {
            $this->aPageVersion = PageVersionQuery::create()->findPk($this->version_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPageVersion->addPageContents($this);
             */
        }

        return $this->aPageVersion;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->page_id = null;
        $this->version_id = null;
        $this->title = null;
        $this->content = null;
        $this->template = null;
        $this->type = null;
        $this->hide = null;
        $this->sort = null;
        $this->box_id = null;
        $this->owner_id = null;
        $this->access_from = null;
        $this->access_to = null;
        $this->access_from_groups = null;
        $this->unsearchable = null;
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
        } // if ($deep)

        $this->aPage = null;
        $this->aPageVersion = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PageContentPeer::DEFAULT_STRING_FORMAT);
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

} // BasePageContent

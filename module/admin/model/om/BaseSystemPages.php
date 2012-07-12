<?php


/**
 * Base class that represents a row from the 'kryn_system_pages' table.
 *
 * 
 *
 * @package    propel.generator.kryn.om
 */
abstract class BaseSystemPages extends BaseObject 
{

    /**
     * Peer class name
     */
    const PEER = 'SystemPagesPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        SystemPagesPeer
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
     * The value for the pid field.
     * @var        int
     */
    protected $pid;

    /**
     * The value for the domain_id field.
     * @var        int
     */
    protected $domain_id;

    /**
     * The value for the type field.
     * @var        int
     */
    protected $type;

    /**
     * The value for the title field.
     * @var        string
     */
    protected $title;

    /**
     * The value for the page_title field.
     * @var        string
     */
    protected $page_title;

    /**
     * The value for the url field.
     * @var        string
     */
    protected $url;

    /**
     * The value for the link field.
     * @var        string
     */
    protected $link;

    /**
     * The value for the layout field.
     * @var        string
     */
    protected $layout;

    /**
     * The value for the sort field.
     * @var        int
     */
    protected $sort;

    /**
     * The value for the sort_mode field.
     * @var        string
     */
    protected $sort_mode;

    /**
     * The value for the target field.
     * @var        string
     */
    protected $target;

    /**
     * The value for the visible field.
     * @var        int
     */
    protected $visible;

    /**
     * The value for the access_denied field.
     * @var        string
     */
    protected $access_denied;

    /**
     * The value for the meta field.
     * @var        string
     */
    protected $meta;

    /**
     * The value for the properties field.
     * @var        string
     */
    protected $properties;

    /**
     * The value for the cdate field.
     * @var        int
     */
    protected $cdate;

    /**
     * The value for the mdate field.
     * @var        int
     */
    protected $mdate;

    /**
     * The value for the draft_exist field.
     * @var        int
     */
    protected $draft_exist;

    /**
     * The value for the force_https field.
     * @var        int
     */
    protected $force_https;

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
     * The value for the access_redirectto field.
     * @var        string
     */
    protected $access_redirectto;

    /**
     * The value for the access_nohidenavi field.
     * @var        int
     */
    protected $access_nohidenavi;

    /**
     * The value for the access_need_via field.
     * @var        int
     */
    protected $access_need_via;

    /**
     * The value for the access_from_groups field.
     * @var        string
     */
    protected $access_from_groups;

    /**
     * The value for the cache field.
     * @var        int
     */
    protected $cache;

    /**
     * The value for the search_words field.
     * @var        string
     */
    protected $search_words;

    /**
     * The value for the unsearchable field.
     * @var        int
     */
    protected $unsearchable;

    /**
     * The value for the lft field.
     * @var        int
     */
    protected $lft;

    /**
     * The value for the rgt field.
     * @var        int
     */
    protected $rgt;

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
     * Get the [pid] column value.
     * 
     * @return   int
     */
    public function getPid()
    {

        return $this->pid;
    }

    /**
     * Get the [domain_id] column value.
     * 
     * @return   int
     */
    public function getDomainId()
    {

        return $this->domain_id;
    }

    /**
     * Get the [type] column value.
     * 
     * @return   int
     */
    public function getType()
    {

        return $this->type;
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
     * Get the [page_title] column value.
     * 
     * @return   string
     */
    public function getPageTitle()
    {

        return $this->page_title;
    }

    /**
     * Get the [url] column value.
     * 
     * @return   string
     */
    public function getUrl()
    {

        return $this->url;
    }

    /**
     * Get the [link] column value.
     * 
     * @return   string
     */
    public function getLink()
    {

        return $this->link;
    }

    /**
     * Get the [layout] column value.
     * 
     * @return   string
     */
    public function getLayout()
    {

        return $this->layout;
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
     * Get the [sort_mode] column value.
     * 
     * @return   string
     */
    public function getSortMode()
    {

        return $this->sort_mode;
    }

    /**
     * Get the [target] column value.
     * 
     * @return   string
     */
    public function getTarget()
    {

        return $this->target;
    }

    /**
     * Get the [visible] column value.
     * 
     * @return   int
     */
    public function getVisible()
    {

        return $this->visible;
    }

    /**
     * Get the [access_denied] column value.
     * 
     * @return   string
     */
    public function getAccessDenied()
    {

        return $this->access_denied;
    }

    /**
     * Get the [meta] column value.
     * 
     * @return   string
     */
    public function getMeta()
    {

        return $this->meta;
    }

    /**
     * Get the [properties] column value.
     * 
     * @return   string
     */
    public function getProperties()
    {

        return $this->properties;
    }

    /**
     * Get the [cdate] column value.
     * 
     * @return   int
     */
    public function getCdate()
    {

        return $this->cdate;
    }

    /**
     * Get the [mdate] column value.
     * 
     * @return   int
     */
    public function getMdate()
    {

        return $this->mdate;
    }

    /**
     * Get the [draft_exist] column value.
     * 
     * @return   int
     */
    public function getDraftExist()
    {

        return $this->draft_exist;
    }

    /**
     * Get the [force_https] column value.
     * 
     * @return   int
     */
    public function getForceHttps()
    {

        return $this->force_https;
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
     * Get the [access_redirectto] column value.
     * 
     * @return   string
     */
    public function getAccessRedirectto()
    {

        return $this->access_redirectto;
    }

    /**
     * Get the [access_nohidenavi] column value.
     * 
     * @return   int
     */
    public function getAccessNohidenavi()
    {

        return $this->access_nohidenavi;
    }

    /**
     * Get the [access_need_via] column value.
     * 
     * @return   int
     */
    public function getAccessNeedVia()
    {

        return $this->access_need_via;
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
     * Get the [cache] column value.
     * 
     * @return   int
     */
    public function getCache()
    {

        return $this->cache;
    }

    /**
     * Get the [search_words] column value.
     * 
     * @return   string
     */
    public function getSearchWords()
    {

        return $this->search_words;
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
     * Get the [lft] column value.
     * 
     * @return   int
     */
    public function getLft()
    {

        return $this->lft;
    }

    /**
     * Get the [rgt] column value.
     * 
     * @return   int
     */
    public function getRgt()
    {

        return $this->rgt;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = SystemPagesPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [pid] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setPid($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->pid !== $v) {
            $this->pid = $v;
            $this->modifiedColumns[] = SystemPagesPeer::PID;
        }


        return $this;
    } // setPid()

    /**
     * Set the value of [domain_id] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setDomainId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->domain_id !== $v) {
            $this->domain_id = $v;
            $this->modifiedColumns[] = SystemPagesPeer::DOMAIN_ID;
        }


        return $this;
    } // setDomainId()

    /**
     * Set the value of [type] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = SystemPagesPeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [title] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = SystemPagesPeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [page_title] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setPageTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->page_title !== $v) {
            $this->page_title = $v;
            $this->modifiedColumns[] = SystemPagesPeer::PAGE_TITLE;
        }


        return $this;
    } // setPageTitle()

    /**
     * Set the value of [url] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[] = SystemPagesPeer::URL;
        }


        return $this;
    } // setUrl()

    /**
     * Set the value of [link] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setLink($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->link !== $v) {
            $this->link = $v;
            $this->modifiedColumns[] = SystemPagesPeer::LINK;
        }


        return $this;
    } // setLink()

    /**
     * Set the value of [layout] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setLayout($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->layout !== $v) {
            $this->layout = $v;
            $this->modifiedColumns[] = SystemPagesPeer::LAYOUT;
        }


        return $this;
    } // setLayout()

    /**
     * Set the value of [sort] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setSort($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->sort !== $v) {
            $this->sort = $v;
            $this->modifiedColumns[] = SystemPagesPeer::SORT;
        }


        return $this;
    } // setSort()

    /**
     * Set the value of [sort_mode] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setSortMode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->sort_mode !== $v) {
            $this->sort_mode = $v;
            $this->modifiedColumns[] = SystemPagesPeer::SORT_MODE;
        }


        return $this;
    } // setSortMode()

    /**
     * Set the value of [target] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setTarget($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->target !== $v) {
            $this->target = $v;
            $this->modifiedColumns[] = SystemPagesPeer::TARGET;
        }


        return $this;
    } // setTarget()

    /**
     * Set the value of [visible] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setVisible($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->visible !== $v) {
            $this->visible = $v;
            $this->modifiedColumns[] = SystemPagesPeer::VISIBLE;
        }


        return $this;
    } // setVisible()

    /**
     * Set the value of [access_denied] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setAccessDenied($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->access_denied !== $v) {
            $this->access_denied = $v;
            $this->modifiedColumns[] = SystemPagesPeer::ACCESS_DENIED;
        }


        return $this;
    } // setAccessDenied()

    /**
     * Set the value of [meta] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setMeta($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->meta !== $v) {
            $this->meta = $v;
            $this->modifiedColumns[] = SystemPagesPeer::META;
        }


        return $this;
    } // setMeta()

    /**
     * Set the value of [properties] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setProperties($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->properties !== $v) {
            $this->properties = $v;
            $this->modifiedColumns[] = SystemPagesPeer::PROPERTIES;
        }


        return $this;
    } // setProperties()

    /**
     * Set the value of [cdate] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setCdate($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->cdate !== $v) {
            $this->cdate = $v;
            $this->modifiedColumns[] = SystemPagesPeer::CDATE;
        }


        return $this;
    } // setCdate()

    /**
     * Set the value of [mdate] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setMdate($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->mdate !== $v) {
            $this->mdate = $v;
            $this->modifiedColumns[] = SystemPagesPeer::MDATE;
        }


        return $this;
    } // setMdate()

    /**
     * Set the value of [draft_exist] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setDraftExist($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->draft_exist !== $v) {
            $this->draft_exist = $v;
            $this->modifiedColumns[] = SystemPagesPeer::DRAFT_EXIST;
        }


        return $this;
    } // setDraftExist()

    /**
     * Set the value of [force_https] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setForceHttps($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->force_https !== $v) {
            $this->force_https = $v;
            $this->modifiedColumns[] = SystemPagesPeer::FORCE_HTTPS;
        }


        return $this;
    } // setForceHttps()

    /**
     * Set the value of [access_from] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setAccessFrom($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->access_from !== $v) {
            $this->access_from = $v;
            $this->modifiedColumns[] = SystemPagesPeer::ACCESS_FROM;
        }


        return $this;
    } // setAccessFrom()

    /**
     * Set the value of [access_to] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setAccessTo($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->access_to !== $v) {
            $this->access_to = $v;
            $this->modifiedColumns[] = SystemPagesPeer::ACCESS_TO;
        }


        return $this;
    } // setAccessTo()

    /**
     * Set the value of [access_redirectto] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setAccessRedirectto($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->access_redirectto !== $v) {
            $this->access_redirectto = $v;
            $this->modifiedColumns[] = SystemPagesPeer::ACCESS_REDIRECTTO;
        }


        return $this;
    } // setAccessRedirectto()

    /**
     * Set the value of [access_nohidenavi] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setAccessNohidenavi($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->access_nohidenavi !== $v) {
            $this->access_nohidenavi = $v;
            $this->modifiedColumns[] = SystemPagesPeer::ACCESS_NOHIDENAVI;
        }


        return $this;
    } // setAccessNohidenavi()

    /**
     * Set the value of [access_need_via] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setAccessNeedVia($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->access_need_via !== $v) {
            $this->access_need_via = $v;
            $this->modifiedColumns[] = SystemPagesPeer::ACCESS_NEED_VIA;
        }


        return $this;
    } // setAccessNeedVia()

    /**
     * Set the value of [access_from_groups] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setAccessFromGroups($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->access_from_groups !== $v) {
            $this->access_from_groups = $v;
            $this->modifiedColumns[] = SystemPagesPeer::ACCESS_FROM_GROUPS;
        }


        return $this;
    } // setAccessFromGroups()

    /**
     * Set the value of [cache] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setCache($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->cache !== $v) {
            $this->cache = $v;
            $this->modifiedColumns[] = SystemPagesPeer::CACHE;
        }


        return $this;
    } // setCache()

    /**
     * Set the value of [search_words] column.
     * 
     * @param      string $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setSearchWords($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->search_words !== $v) {
            $this->search_words = $v;
            $this->modifiedColumns[] = SystemPagesPeer::SEARCH_WORDS;
        }


        return $this;
    } // setSearchWords()

    /**
     * Set the value of [unsearchable] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setUnsearchable($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->unsearchable !== $v) {
            $this->unsearchable = $v;
            $this->modifiedColumns[] = SystemPagesPeer::UNSEARCHABLE;
        }


        return $this;
    } // setUnsearchable()

    /**
     * Set the value of [lft] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setLft($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->lft !== $v) {
            $this->lft = $v;
            $this->modifiedColumns[] = SystemPagesPeer::LFT;
        }


        return $this;
    } // setLft()

    /**
     * Set the value of [rgt] column.
     * 
     * @param      int $v new value
     * @return   SystemPages The current object (for fluent API support)
     */
    public function setRgt($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->rgt !== $v) {
            $this->rgt = $v;
            $this->modifiedColumns[] = SystemPagesPeer::RGT;
        }


        return $this;
    } // setRgt()

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
            $this->pid = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->domain_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->type = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->title = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
            $this->page_title = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
            $this->url = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
            $this->link = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->layout = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->sort = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
            $this->sort_mode = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->target = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->visible = ($row[$startcol + 12] !== null) ? (int) $row[$startcol + 12] : null;
            $this->access_denied = ($row[$startcol + 13] !== null) ? (string) $row[$startcol + 13] : null;
            $this->meta = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
            $this->properties = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
            $this->cdate = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
            $this->mdate = ($row[$startcol + 17] !== null) ? (int) $row[$startcol + 17] : null;
            $this->draft_exist = ($row[$startcol + 18] !== null) ? (int) $row[$startcol + 18] : null;
            $this->force_https = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
            $this->access_from = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
            $this->access_to = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
            $this->access_redirectto = ($row[$startcol + 22] !== null) ? (string) $row[$startcol + 22] : null;
            $this->access_nohidenavi = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
            $this->access_need_via = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
            $this->access_from_groups = ($row[$startcol + 25] !== null) ? (string) $row[$startcol + 25] : null;
            $this->cache = ($row[$startcol + 26] !== null) ? (int) $row[$startcol + 26] : null;
            $this->search_words = ($row[$startcol + 27] !== null) ? (string) $row[$startcol + 27] : null;
            $this->unsearchable = ($row[$startcol + 28] !== null) ? (int) $row[$startcol + 28] : null;
            $this->lft = ($row[$startcol + 29] !== null) ? (int) $row[$startcol + 29] : null;
            $this->rgt = ($row[$startcol + 30] !== null) ? (int) $row[$startcol + 30] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 31; // 31 = SystemPagesPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating SystemPages object", $e);
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
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = SystemPagesPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

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
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = SystemPagesQuery::create()
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
            $con = Propel::getConnection(SystemPagesPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                SystemPagesPeer::addInstanceToPool($this);
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

        $this->modifiedColumns[] = SystemPagesPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . SystemPagesPeer::ID . ')');
        }
        if (null === $this->id) {
            try {				
				$stmt = $con->query("SELECT nextval('kryn_system_pages_id_seq')");
				$row = $stmt->fetch(PDO::FETCH_NUM);
				$this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(SystemPagesPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(SystemPagesPeer::PID)) {
            $modifiedColumns[':p' . $index++]  = 'PID';
        }
        if ($this->isColumnModified(SystemPagesPeer::DOMAIN_ID)) {
            $modifiedColumns[':p' . $index++]  = 'DOMAIN_ID';
        }
        if ($this->isColumnModified(SystemPagesPeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'TYPE';
        }
        if ($this->isColumnModified(SystemPagesPeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'TITLE';
        }
        if ($this->isColumnModified(SystemPagesPeer::PAGE_TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'PAGE_TITLE';
        }
        if ($this->isColumnModified(SystemPagesPeer::URL)) {
            $modifiedColumns[':p' . $index++]  = 'URL';
        }
        if ($this->isColumnModified(SystemPagesPeer::LINK)) {
            $modifiedColumns[':p' . $index++]  = 'LINK';
        }
        if ($this->isColumnModified(SystemPagesPeer::LAYOUT)) {
            $modifiedColumns[':p' . $index++]  = 'LAYOUT';
        }
        if ($this->isColumnModified(SystemPagesPeer::SORT)) {
            $modifiedColumns[':p' . $index++]  = 'SORT';
        }
        if ($this->isColumnModified(SystemPagesPeer::SORT_MODE)) {
            $modifiedColumns[':p' . $index++]  = 'SORT_MODE';
        }
        if ($this->isColumnModified(SystemPagesPeer::TARGET)) {
            $modifiedColumns[':p' . $index++]  = 'TARGET';
        }
        if ($this->isColumnModified(SystemPagesPeer::VISIBLE)) {
            $modifiedColumns[':p' . $index++]  = 'VISIBLE';
        }
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_DENIED)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_DENIED';
        }
        if ($this->isColumnModified(SystemPagesPeer::META)) {
            $modifiedColumns[':p' . $index++]  = 'META';
        }
        if ($this->isColumnModified(SystemPagesPeer::PROPERTIES)) {
            $modifiedColumns[':p' . $index++]  = 'PROPERTIES';
        }
        if ($this->isColumnModified(SystemPagesPeer::CDATE)) {
            $modifiedColumns[':p' . $index++]  = 'CDATE';
        }
        if ($this->isColumnModified(SystemPagesPeer::MDATE)) {
            $modifiedColumns[':p' . $index++]  = 'MDATE';
        }
        if ($this->isColumnModified(SystemPagesPeer::DRAFT_EXIST)) {
            $modifiedColumns[':p' . $index++]  = 'DRAFT_EXIST';
        }
        if ($this->isColumnModified(SystemPagesPeer::FORCE_HTTPS)) {
            $modifiedColumns[':p' . $index++]  = 'FORCE_HTTPS';
        }
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_FROM)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_FROM';
        }
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_TO)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_TO';
        }
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_REDIRECTTO)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_REDIRECTTO';
        }
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_NOHIDENAVI)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_NOHIDENAVI';
        }
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_NEED_VIA)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_NEED_VIA';
        }
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_FROM_GROUPS)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_FROM_GROUPS';
        }
        if ($this->isColumnModified(SystemPagesPeer::CACHE)) {
            $modifiedColumns[':p' . $index++]  = 'CACHE';
        }
        if ($this->isColumnModified(SystemPagesPeer::SEARCH_WORDS)) {
            $modifiedColumns[':p' . $index++]  = 'SEARCH_WORDS';
        }
        if ($this->isColumnModified(SystemPagesPeer::UNSEARCHABLE)) {
            $modifiedColumns[':p' . $index++]  = 'UNSEARCHABLE';
        }
        if ($this->isColumnModified(SystemPagesPeer::LFT)) {
            $modifiedColumns[':p' . $index++]  = 'LFT';
        }
        if ($this->isColumnModified(SystemPagesPeer::RGT)) {
            $modifiedColumns[':p' . $index++]  = 'RGT';
        }

        $sql = sprintf(
            'INSERT INTO kryn_system_pages (%s) VALUES (%s)',
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
                    case 'PID':
						$stmt->bindValue($identifier, $this->pid, PDO::PARAM_INT);
                        break;
                    case 'DOMAIN_ID':
						$stmt->bindValue($identifier, $this->domain_id, PDO::PARAM_INT);
                        break;
                    case 'TYPE':
						$stmt->bindValue($identifier, $this->type, PDO::PARAM_INT);
                        break;
                    case 'TITLE':
						$stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case 'PAGE_TITLE':
						$stmt->bindValue($identifier, $this->page_title, PDO::PARAM_STR);
                        break;
                    case 'URL':
						$stmt->bindValue($identifier, $this->url, PDO::PARAM_STR);
                        break;
                    case 'LINK':
						$stmt->bindValue($identifier, $this->link, PDO::PARAM_STR);
                        break;
                    case 'LAYOUT':
						$stmt->bindValue($identifier, $this->layout, PDO::PARAM_STR);
                        break;
                    case 'SORT':
						$stmt->bindValue($identifier, $this->sort, PDO::PARAM_INT);
                        break;
                    case 'SORT_MODE':
						$stmt->bindValue($identifier, $this->sort_mode, PDO::PARAM_STR);
                        break;
                    case 'TARGET':
						$stmt->bindValue($identifier, $this->target, PDO::PARAM_STR);
                        break;
                    case 'VISIBLE':
						$stmt->bindValue($identifier, $this->visible, PDO::PARAM_INT);
                        break;
                    case 'ACCESS_DENIED':
						$stmt->bindValue($identifier, $this->access_denied, PDO::PARAM_STR);
                        break;
                    case 'META':
						$stmt->bindValue($identifier, $this->meta, PDO::PARAM_STR);
                        break;
                    case 'PROPERTIES':
						$stmt->bindValue($identifier, $this->properties, PDO::PARAM_STR);
                        break;
                    case 'CDATE':
						$stmt->bindValue($identifier, $this->cdate, PDO::PARAM_INT);
                        break;
                    case 'MDATE':
						$stmt->bindValue($identifier, $this->mdate, PDO::PARAM_INT);
                        break;
                    case 'DRAFT_EXIST':
						$stmt->bindValue($identifier, $this->draft_exist, PDO::PARAM_INT);
                        break;
                    case 'FORCE_HTTPS':
						$stmt->bindValue($identifier, $this->force_https, PDO::PARAM_INT);
                        break;
                    case 'ACCESS_FROM':
						$stmt->bindValue($identifier, $this->access_from, PDO::PARAM_INT);
                        break;
                    case 'ACCESS_TO':
						$stmt->bindValue($identifier, $this->access_to, PDO::PARAM_INT);
                        break;
                    case 'ACCESS_REDIRECTTO':
						$stmt->bindValue($identifier, $this->access_redirectto, PDO::PARAM_STR);
                        break;
                    case 'ACCESS_NOHIDENAVI':
						$stmt->bindValue($identifier, $this->access_nohidenavi, PDO::PARAM_INT);
                        break;
                    case 'ACCESS_NEED_VIA':
						$stmt->bindValue($identifier, $this->access_need_via, PDO::PARAM_INT);
                        break;
                    case 'ACCESS_FROM_GROUPS':
						$stmt->bindValue($identifier, $this->access_from_groups, PDO::PARAM_STR);
                        break;
                    case 'CACHE':
						$stmt->bindValue($identifier, $this->cache, PDO::PARAM_INT);
                        break;
                    case 'SEARCH_WORDS':
						$stmt->bindValue($identifier, $this->search_words, PDO::PARAM_STR);
                        break;
                    case 'UNSEARCHABLE':
						$stmt->bindValue($identifier, $this->unsearchable, PDO::PARAM_INT);
                        break;
                    case 'LFT':
						$stmt->bindValue($identifier, $this->lft, PDO::PARAM_INT);
                        break;
                    case 'RGT':
						$stmt->bindValue($identifier, $this->rgt, PDO::PARAM_INT);
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


            if (($retval = SystemPagesPeer::doValidate($this, $columns)) !== true) {
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
        $pos = SystemPagesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getPid();
                break;
            case 2:
                return $this->getDomainId();
                break;
            case 3:
                return $this->getType();
                break;
            case 4:
                return $this->getTitle();
                break;
            case 5:
                return $this->getPageTitle();
                break;
            case 6:
                return $this->getUrl();
                break;
            case 7:
                return $this->getLink();
                break;
            case 8:
                return $this->getLayout();
                break;
            case 9:
                return $this->getSort();
                break;
            case 10:
                return $this->getSortMode();
                break;
            case 11:
                return $this->getTarget();
                break;
            case 12:
                return $this->getVisible();
                break;
            case 13:
                return $this->getAccessDenied();
                break;
            case 14:
                return $this->getMeta();
                break;
            case 15:
                return $this->getProperties();
                break;
            case 16:
                return $this->getCdate();
                break;
            case 17:
                return $this->getMdate();
                break;
            case 18:
                return $this->getDraftExist();
                break;
            case 19:
                return $this->getForceHttps();
                break;
            case 20:
                return $this->getAccessFrom();
                break;
            case 21:
                return $this->getAccessTo();
                break;
            case 22:
                return $this->getAccessRedirectto();
                break;
            case 23:
                return $this->getAccessNohidenavi();
                break;
            case 24:
                return $this->getAccessNeedVia();
                break;
            case 25:
                return $this->getAccessFromGroups();
                break;
            case 26:
                return $this->getCache();
                break;
            case 27:
                return $this->getSearchWords();
                break;
            case 28:
                return $this->getUnsearchable();
                break;
            case 29:
                return $this->getLft();
                break;
            case 30:
                return $this->getRgt();
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
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array())
    {
        if (isset($alreadyDumpedObjects['SystemPages'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['SystemPages'][$this->getPrimaryKey()] = true;
        $keys = SystemPagesPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getPid(),
            $keys[2] => $this->getDomainId(),
            $keys[3] => $this->getType(),
            $keys[4] => $this->getTitle(),
            $keys[5] => $this->getPageTitle(),
            $keys[6] => $this->getUrl(),
            $keys[7] => $this->getLink(),
            $keys[8] => $this->getLayout(),
            $keys[9] => $this->getSort(),
            $keys[10] => $this->getSortMode(),
            $keys[11] => $this->getTarget(),
            $keys[12] => $this->getVisible(),
            $keys[13] => $this->getAccessDenied(),
            $keys[14] => $this->getMeta(),
            $keys[15] => $this->getProperties(),
            $keys[16] => $this->getCdate(),
            $keys[17] => $this->getMdate(),
            $keys[18] => $this->getDraftExist(),
            $keys[19] => $this->getForceHttps(),
            $keys[20] => $this->getAccessFrom(),
            $keys[21] => $this->getAccessTo(),
            $keys[22] => $this->getAccessRedirectto(),
            $keys[23] => $this->getAccessNohidenavi(),
            $keys[24] => $this->getAccessNeedVia(),
            $keys[25] => $this->getAccessFromGroups(),
            $keys[26] => $this->getCache(),
            $keys[27] => $this->getSearchWords(),
            $keys[28] => $this->getUnsearchable(),
            $keys[29] => $this->getLft(),
            $keys[30] => $this->getRgt(),
        );

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
        $pos = SystemPagesPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setPid($value);
                break;
            case 2:
                $this->setDomainId($value);
                break;
            case 3:
                $this->setType($value);
                break;
            case 4:
                $this->setTitle($value);
                break;
            case 5:
                $this->setPageTitle($value);
                break;
            case 6:
                $this->setUrl($value);
                break;
            case 7:
                $this->setLink($value);
                break;
            case 8:
                $this->setLayout($value);
                break;
            case 9:
                $this->setSort($value);
                break;
            case 10:
                $this->setSortMode($value);
                break;
            case 11:
                $this->setTarget($value);
                break;
            case 12:
                $this->setVisible($value);
                break;
            case 13:
                $this->setAccessDenied($value);
                break;
            case 14:
                $this->setMeta($value);
                break;
            case 15:
                $this->setProperties($value);
                break;
            case 16:
                $this->setCdate($value);
                break;
            case 17:
                $this->setMdate($value);
                break;
            case 18:
                $this->setDraftExist($value);
                break;
            case 19:
                $this->setForceHttps($value);
                break;
            case 20:
                $this->setAccessFrom($value);
                break;
            case 21:
                $this->setAccessTo($value);
                break;
            case 22:
                $this->setAccessRedirectto($value);
                break;
            case 23:
                $this->setAccessNohidenavi($value);
                break;
            case 24:
                $this->setAccessNeedVia($value);
                break;
            case 25:
                $this->setAccessFromGroups($value);
                break;
            case 26:
                $this->setCache($value);
                break;
            case 27:
                $this->setSearchWords($value);
                break;
            case 28:
                $this->setUnsearchable($value);
                break;
            case 29:
                $this->setLft($value);
                break;
            case 30:
                $this->setRgt($value);
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
        $keys = SystemPagesPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setPid($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDomainId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setType($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setTitle($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setPageTitle($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setUrl($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setLink($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setLayout($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setSort($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setSortMode($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setTarget($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setVisible($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setAccessDenied($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setMeta($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setProperties($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setCdate($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setMdate($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setDraftExist($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setForceHttps($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setAccessFrom($arr[$keys[20]]);
        if (array_key_exists($keys[21], $arr)) $this->setAccessTo($arr[$keys[21]]);
        if (array_key_exists($keys[22], $arr)) $this->setAccessRedirectto($arr[$keys[22]]);
        if (array_key_exists($keys[23], $arr)) $this->setAccessNohidenavi($arr[$keys[23]]);
        if (array_key_exists($keys[24], $arr)) $this->setAccessNeedVia($arr[$keys[24]]);
        if (array_key_exists($keys[25], $arr)) $this->setAccessFromGroups($arr[$keys[25]]);
        if (array_key_exists($keys[26], $arr)) $this->setCache($arr[$keys[26]]);
        if (array_key_exists($keys[27], $arr)) $this->setSearchWords($arr[$keys[27]]);
        if (array_key_exists($keys[28], $arr)) $this->setUnsearchable($arr[$keys[28]]);
        if (array_key_exists($keys[29], $arr)) $this->setLft($arr[$keys[29]]);
        if (array_key_exists($keys[30], $arr)) $this->setRgt($arr[$keys[30]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(SystemPagesPeer::DATABASE_NAME);

        if ($this->isColumnModified(SystemPagesPeer::ID)) $criteria->add(SystemPagesPeer::ID, $this->id);
        if ($this->isColumnModified(SystemPagesPeer::PID)) $criteria->add(SystemPagesPeer::PID, $this->pid);
        if ($this->isColumnModified(SystemPagesPeer::DOMAIN_ID)) $criteria->add(SystemPagesPeer::DOMAIN_ID, $this->domain_id);
        if ($this->isColumnModified(SystemPagesPeer::TYPE)) $criteria->add(SystemPagesPeer::TYPE, $this->type);
        if ($this->isColumnModified(SystemPagesPeer::TITLE)) $criteria->add(SystemPagesPeer::TITLE, $this->title);
        if ($this->isColumnModified(SystemPagesPeer::PAGE_TITLE)) $criteria->add(SystemPagesPeer::PAGE_TITLE, $this->page_title);
        if ($this->isColumnModified(SystemPagesPeer::URL)) $criteria->add(SystemPagesPeer::URL, $this->url);
        if ($this->isColumnModified(SystemPagesPeer::LINK)) $criteria->add(SystemPagesPeer::LINK, $this->link);
        if ($this->isColumnModified(SystemPagesPeer::LAYOUT)) $criteria->add(SystemPagesPeer::LAYOUT, $this->layout);
        if ($this->isColumnModified(SystemPagesPeer::SORT)) $criteria->add(SystemPagesPeer::SORT, $this->sort);
        if ($this->isColumnModified(SystemPagesPeer::SORT_MODE)) $criteria->add(SystemPagesPeer::SORT_MODE, $this->sort_mode);
        if ($this->isColumnModified(SystemPagesPeer::TARGET)) $criteria->add(SystemPagesPeer::TARGET, $this->target);
        if ($this->isColumnModified(SystemPagesPeer::VISIBLE)) $criteria->add(SystemPagesPeer::VISIBLE, $this->visible);
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_DENIED)) $criteria->add(SystemPagesPeer::ACCESS_DENIED, $this->access_denied);
        if ($this->isColumnModified(SystemPagesPeer::META)) $criteria->add(SystemPagesPeer::META, $this->meta);
        if ($this->isColumnModified(SystemPagesPeer::PROPERTIES)) $criteria->add(SystemPagesPeer::PROPERTIES, $this->properties);
        if ($this->isColumnModified(SystemPagesPeer::CDATE)) $criteria->add(SystemPagesPeer::CDATE, $this->cdate);
        if ($this->isColumnModified(SystemPagesPeer::MDATE)) $criteria->add(SystemPagesPeer::MDATE, $this->mdate);
        if ($this->isColumnModified(SystemPagesPeer::DRAFT_EXIST)) $criteria->add(SystemPagesPeer::DRAFT_EXIST, $this->draft_exist);
        if ($this->isColumnModified(SystemPagesPeer::FORCE_HTTPS)) $criteria->add(SystemPagesPeer::FORCE_HTTPS, $this->force_https);
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_FROM)) $criteria->add(SystemPagesPeer::ACCESS_FROM, $this->access_from);
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_TO)) $criteria->add(SystemPagesPeer::ACCESS_TO, $this->access_to);
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_REDIRECTTO)) $criteria->add(SystemPagesPeer::ACCESS_REDIRECTTO, $this->access_redirectto);
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_NOHIDENAVI)) $criteria->add(SystemPagesPeer::ACCESS_NOHIDENAVI, $this->access_nohidenavi);
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_NEED_VIA)) $criteria->add(SystemPagesPeer::ACCESS_NEED_VIA, $this->access_need_via);
        if ($this->isColumnModified(SystemPagesPeer::ACCESS_FROM_GROUPS)) $criteria->add(SystemPagesPeer::ACCESS_FROM_GROUPS, $this->access_from_groups);
        if ($this->isColumnModified(SystemPagesPeer::CACHE)) $criteria->add(SystemPagesPeer::CACHE, $this->cache);
        if ($this->isColumnModified(SystemPagesPeer::SEARCH_WORDS)) $criteria->add(SystemPagesPeer::SEARCH_WORDS, $this->search_words);
        if ($this->isColumnModified(SystemPagesPeer::UNSEARCHABLE)) $criteria->add(SystemPagesPeer::UNSEARCHABLE, $this->unsearchable);
        if ($this->isColumnModified(SystemPagesPeer::LFT)) $criteria->add(SystemPagesPeer::LFT, $this->lft);
        if ($this->isColumnModified(SystemPagesPeer::RGT)) $criteria->add(SystemPagesPeer::RGT, $this->rgt);

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
        $criteria = new Criteria(SystemPagesPeer::DATABASE_NAME);
        $criteria->add(SystemPagesPeer::ID, $this->id);

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
     * @param      object $copyObj An object of SystemPages (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setPid($this->getPid());
        $copyObj->setDomainId($this->getDomainId());
        $copyObj->setType($this->getType());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setPageTitle($this->getPageTitle());
        $copyObj->setUrl($this->getUrl());
        $copyObj->setLink($this->getLink());
        $copyObj->setLayout($this->getLayout());
        $copyObj->setSort($this->getSort());
        $copyObj->setSortMode($this->getSortMode());
        $copyObj->setTarget($this->getTarget());
        $copyObj->setVisible($this->getVisible());
        $copyObj->setAccessDenied($this->getAccessDenied());
        $copyObj->setMeta($this->getMeta());
        $copyObj->setProperties($this->getProperties());
        $copyObj->setCdate($this->getCdate());
        $copyObj->setMdate($this->getMdate());
        $copyObj->setDraftExist($this->getDraftExist());
        $copyObj->setForceHttps($this->getForceHttps());
        $copyObj->setAccessFrom($this->getAccessFrom());
        $copyObj->setAccessTo($this->getAccessTo());
        $copyObj->setAccessRedirectto($this->getAccessRedirectto());
        $copyObj->setAccessNohidenavi($this->getAccessNohidenavi());
        $copyObj->setAccessNeedVia($this->getAccessNeedVia());
        $copyObj->setAccessFromGroups($this->getAccessFromGroups());
        $copyObj->setCache($this->getCache());
        $copyObj->setSearchWords($this->getSearchWords());
        $copyObj->setUnsearchable($this->getUnsearchable());
        $copyObj->setLft($this->getLft());
        $copyObj->setRgt($this->getRgt());
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
     * @return                 SystemPages Clone of current object.
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
     * @return   SystemPagesPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new SystemPagesPeer();
        }

        return self::$peer;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->pid = null;
        $this->domain_id = null;
        $this->type = null;
        $this->title = null;
        $this->page_title = null;
        $this->url = null;
        $this->link = null;
        $this->layout = null;
        $this->sort = null;
        $this->sort_mode = null;
        $this->target = null;
        $this->visible = null;
        $this->access_denied = null;
        $this->meta = null;
        $this->properties = null;
        $this->cdate = null;
        $this->mdate = null;
        $this->draft_exist = null;
        $this->force_https = null;
        $this->access_from = null;
        $this->access_to = null;
        $this->access_redirectto = null;
        $this->access_nohidenavi = null;
        $this->access_need_via = null;
        $this->access_from_groups = null;
        $this->cache = null;
        $this->search_words = null;
        $this->unsearchable = null;
        $this->lft = null;
        $this->rgt = null;
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

    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(SystemPagesPeer::DEFAULT_STRING_FORMAT);
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

} // BaseSystemPages

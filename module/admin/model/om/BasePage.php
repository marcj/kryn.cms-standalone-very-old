<?php


/**
 * Base class that represents a row from the 'kryn_system_page' table.
 *
 * 
 *
 * @package    propel.generator.Kryn.om
 */
abstract class BasePage extends BaseObject 
{

    /**
     * Peer class name
     */
    const PEER = 'PagePeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PagePeer
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
     * The value for the parent_id field.
     * @var        int
     */
    protected $parent_id;

    /**
     * The value for the domain_id field.
     * @var        int
     */
    protected $domain_id;

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
     * The value for the lvl field.
     * @var        int
     */
    protected $lvl;

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
     * The value for the full_url field.
     * @var        string
     */
    protected $full_url;

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
     * The value for the active_version_id field.
     * @var        int
     */
    protected $active_version_id;

    /**
     * @var        Domain
     */
    protected $aDomain;

    /**
     * @var        Page
     */
    protected $aPageRelatedByParentId;

    /**
     * @var        PropelObjectCollection|PageContent[] Collection to store aggregation of PageContent objects.
     */
    protected $collPageContents;

    /**
     * @var        PropelObjectCollection|Page[] Collection to store aggregation of Page objects.
     */
    protected $collPagesRelatedById;

    /**
     * @var        PropelObjectCollection|Urlalias[] Collection to store aggregation of Urlalias objects.
     */
    protected $collUrlaliass;

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

	// nested_set behavior
	
	/**
	 * Queries to be executed in the save transaction
	 * @var        array
	 */
	protected $nestedSetQueries = array();
	
	/**
	 * Internal cache for children nodes
	 * @var        null|PropelObjectCollection
	 */
	protected $collNestedSetChildren = null;
	
	/**
	 * Internal cache for parent node
	 * @var        null|Page
	 */
	protected $aNestedSetParent = null;
	

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $pageContentsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $pagesRelatedByIdScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $urlaliassScheduledForDeletion = null;

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
     * Get the [parent_id] column value.
     * 
     * @return   int
     */
    public function getParentId()
    {

        return $this->parent_id;
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
     * Get the [lvl] column value.
     * 
     * @return   int
     */
    public function getLvl()
    {

        return $this->lvl;
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
     * Get the [full_url] column value.
     * 
     * @return   string
     */
    public function getFullUrl()
    {

        return $this->full_url;
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
     * Get the [active_version_id] column value.
     * 
     * @return   int
     */
    public function getActiveVersionId()
    {

        return $this->active_version_id;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = PagePeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [parent_id] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setParentId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->parent_id !== $v) {
            $this->parent_id = $v;
            $this->modifiedColumns[] = PagePeer::PARENT_ID;
        }

        if ($this->aPageRelatedByParentId !== null && $this->aPageRelatedByParentId->getId() !== $v) {
            $this->aPageRelatedByParentId = null;
        }


        return $this;
    } // setParentId()

    /**
     * Set the value of [domain_id] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setDomainId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->domain_id !== $v) {
            $this->domain_id = $v;
            $this->modifiedColumns[] = PagePeer::DOMAIN_ID;
        }

        if ($this->aDomain !== null && $this->aDomain->getId() !== $v) {
            $this->aDomain = null;
        }


        return $this;
    } // setDomainId()

    /**
     * Set the value of [lft] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setLft($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->lft !== $v) {
            $this->lft = $v;
            $this->modifiedColumns[] = PagePeer::LFT;
        }


        return $this;
    } // setLft()

    /**
     * Set the value of [rgt] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setRgt($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->rgt !== $v) {
            $this->rgt = $v;
            $this->modifiedColumns[] = PagePeer::RGT;
        }


        return $this;
    } // setRgt()

    /**
     * Set the value of [lvl] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setLvl($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->lvl !== $v) {
            $this->lvl = $v;
            $this->modifiedColumns[] = PagePeer::LVL;
        }


        return $this;
    } // setLvl()

    /**
     * Set the value of [type] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setType($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->type !== $v) {
            $this->type = $v;
            $this->modifiedColumns[] = PagePeer::TYPE;
        }


        return $this;
    } // setType()

    /**
     * Set the value of [title] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[] = PagePeer::TITLE;
        }


        return $this;
    } // setTitle()

    /**
     * Set the value of [page_title] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setPageTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->page_title !== $v) {
            $this->page_title = $v;
            $this->modifiedColumns[] = PagePeer::PAGE_TITLE;
        }


        return $this;
    } // setPageTitle()

    /**
     * Set the value of [url] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->url !== $v) {
            $this->url = $v;
            $this->modifiedColumns[] = PagePeer::URL;
        }


        return $this;
    } // setUrl()

    /**
     * Set the value of [full_url] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setFullUrl($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->full_url !== $v) {
            $this->full_url = $v;
            $this->modifiedColumns[] = PagePeer::FULL_URL;
        }


        return $this;
    } // setFullUrl()

    /**
     * Set the value of [link] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setLink($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->link !== $v) {
            $this->link = $v;
            $this->modifiedColumns[] = PagePeer::LINK;
        }


        return $this;
    } // setLink()

    /**
     * Set the value of [layout] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setLayout($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->layout !== $v) {
            $this->layout = $v;
            $this->modifiedColumns[] = PagePeer::LAYOUT;
        }


        return $this;
    } // setLayout()

    /**
     * Set the value of [sort] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setSort($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->sort !== $v) {
            $this->sort = $v;
            $this->modifiedColumns[] = PagePeer::SORT;
        }


        return $this;
    } // setSort()

    /**
     * Set the value of [sort_mode] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setSortMode($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->sort_mode !== $v) {
            $this->sort_mode = $v;
            $this->modifiedColumns[] = PagePeer::SORT_MODE;
        }


        return $this;
    } // setSortMode()

    /**
     * Set the value of [target] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setTarget($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->target !== $v) {
            $this->target = $v;
            $this->modifiedColumns[] = PagePeer::TARGET;
        }


        return $this;
    } // setTarget()

    /**
     * Set the value of [visible] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setVisible($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->visible !== $v) {
            $this->visible = $v;
            $this->modifiedColumns[] = PagePeer::VISIBLE;
        }


        return $this;
    } // setVisible()

    /**
     * Set the value of [access_denied] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setAccessDenied($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->access_denied !== $v) {
            $this->access_denied = $v;
            $this->modifiedColumns[] = PagePeer::ACCESS_DENIED;
        }


        return $this;
    } // setAccessDenied()

    /**
     * Set the value of [meta] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setMeta($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->meta !== $v) {
            $this->meta = $v;
            $this->modifiedColumns[] = PagePeer::META;
        }


        return $this;
    } // setMeta()

    /**
     * Set the value of [properties] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setProperties($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->properties !== $v) {
            $this->properties = $v;
            $this->modifiedColumns[] = PagePeer::PROPERTIES;
        }


        return $this;
    } // setProperties()

    /**
     * Set the value of [cdate] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setCdate($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->cdate !== $v) {
            $this->cdate = $v;
            $this->modifiedColumns[] = PagePeer::CDATE;
        }


        return $this;
    } // setCdate()

    /**
     * Set the value of [mdate] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setMdate($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->mdate !== $v) {
            $this->mdate = $v;
            $this->modifiedColumns[] = PagePeer::MDATE;
        }


        return $this;
    } // setMdate()

    /**
     * Set the value of [draft_exist] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setDraftExist($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->draft_exist !== $v) {
            $this->draft_exist = $v;
            $this->modifiedColumns[] = PagePeer::DRAFT_EXIST;
        }


        return $this;
    } // setDraftExist()

    /**
     * Set the value of [force_https] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setForceHttps($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->force_https !== $v) {
            $this->force_https = $v;
            $this->modifiedColumns[] = PagePeer::FORCE_HTTPS;
        }


        return $this;
    } // setForceHttps()

    /**
     * Set the value of [access_from] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setAccessFrom($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->access_from !== $v) {
            $this->access_from = $v;
            $this->modifiedColumns[] = PagePeer::ACCESS_FROM;
        }


        return $this;
    } // setAccessFrom()

    /**
     * Set the value of [access_to] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setAccessTo($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->access_to !== $v) {
            $this->access_to = $v;
            $this->modifiedColumns[] = PagePeer::ACCESS_TO;
        }


        return $this;
    } // setAccessTo()

    /**
     * Set the value of [access_redirectto] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setAccessRedirectto($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->access_redirectto !== $v) {
            $this->access_redirectto = $v;
            $this->modifiedColumns[] = PagePeer::ACCESS_REDIRECTTO;
        }


        return $this;
    } // setAccessRedirectto()

    /**
     * Set the value of [access_nohidenavi] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setAccessNohidenavi($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->access_nohidenavi !== $v) {
            $this->access_nohidenavi = $v;
            $this->modifiedColumns[] = PagePeer::ACCESS_NOHIDENAVI;
        }


        return $this;
    } // setAccessNohidenavi()

    /**
     * Set the value of [access_need_via] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setAccessNeedVia($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->access_need_via !== $v) {
            $this->access_need_via = $v;
            $this->modifiedColumns[] = PagePeer::ACCESS_NEED_VIA;
        }


        return $this;
    } // setAccessNeedVia()

    /**
     * Set the value of [access_from_groups] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setAccessFromGroups($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->access_from_groups !== $v) {
            $this->access_from_groups = $v;
            $this->modifiedColumns[] = PagePeer::ACCESS_FROM_GROUPS;
        }


        return $this;
    } // setAccessFromGroups()

    /**
     * Set the value of [cache] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setCache($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->cache !== $v) {
            $this->cache = $v;
            $this->modifiedColumns[] = PagePeer::CACHE;
        }


        return $this;
    } // setCache()

    /**
     * Set the value of [search_words] column.
     * 
     * @param      string $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setSearchWords($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->search_words !== $v) {
            $this->search_words = $v;
            $this->modifiedColumns[] = PagePeer::SEARCH_WORDS;
        }


        return $this;
    } // setSearchWords()

    /**
     * Set the value of [unsearchable] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setUnsearchable($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->unsearchable !== $v) {
            $this->unsearchable = $v;
            $this->modifiedColumns[] = PagePeer::UNSEARCHABLE;
        }


        return $this;
    } // setUnsearchable()

    /**
     * Set the value of [active_version_id] column.
     * 
     * @param      int $v new value
     * @return   Page The current object (for fluent API support)
     */
    public function setActiveVersionId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->active_version_id !== $v) {
            $this->active_version_id = $v;
            $this->modifiedColumns[] = PagePeer::ACTIVE_VERSION_ID;
        }


        return $this;
    } // setActiveVersionId()

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
            $this->parent_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->domain_id = ($row[$startcol + 2] !== null) ? (int) $row[$startcol + 2] : null;
            $this->lft = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->rgt = ($row[$startcol + 4] !== null) ? (int) $row[$startcol + 4] : null;
            $this->lvl = ($row[$startcol + 5] !== null) ? (int) $row[$startcol + 5] : null;
            $this->type = ($row[$startcol + 6] !== null) ? (int) $row[$startcol + 6] : null;
            $this->title = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->page_title = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->url = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->full_url = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->link = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->layout = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->sort = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
            $this->sort_mode = ($row[$startcol + 14] !== null) ? (string) $row[$startcol + 14] : null;
            $this->target = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
            $this->visible = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
            $this->access_denied = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
            $this->meta = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
            $this->properties = ($row[$startcol + 19] !== null) ? (string) $row[$startcol + 19] : null;
            $this->cdate = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
            $this->mdate = ($row[$startcol + 21] !== null) ? (int) $row[$startcol + 21] : null;
            $this->draft_exist = ($row[$startcol + 22] !== null) ? (int) $row[$startcol + 22] : null;
            $this->force_https = ($row[$startcol + 23] !== null) ? (int) $row[$startcol + 23] : null;
            $this->access_from = ($row[$startcol + 24] !== null) ? (int) $row[$startcol + 24] : null;
            $this->access_to = ($row[$startcol + 25] !== null) ? (int) $row[$startcol + 25] : null;
            $this->access_redirectto = ($row[$startcol + 26] !== null) ? (string) $row[$startcol + 26] : null;
            $this->access_nohidenavi = ($row[$startcol + 27] !== null) ? (int) $row[$startcol + 27] : null;
            $this->access_need_via = ($row[$startcol + 28] !== null) ? (int) $row[$startcol + 28] : null;
            $this->access_from_groups = ($row[$startcol + 29] !== null) ? (string) $row[$startcol + 29] : null;
            $this->cache = ($row[$startcol + 30] !== null) ? (int) $row[$startcol + 30] : null;
            $this->search_words = ($row[$startcol + 31] !== null) ? (string) $row[$startcol + 31] : null;
            $this->unsearchable = ($row[$startcol + 32] !== null) ? (int) $row[$startcol + 32] : null;
            $this->active_version_id = ($row[$startcol + 33] !== null) ? (int) $row[$startcol + 33] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 34; // 34 = PagePeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Page object", $e);
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

        if ($this->aPageRelatedByParentId !== null && $this->parent_id !== $this->aPageRelatedByParentId->getId()) {
            $this->aPageRelatedByParentId = null;
        }
        if ($this->aDomain !== null && $this->domain_id !== $this->aDomain->getId()) {
            $this->aDomain = null;
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
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PagePeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aDomain = null;
            $this->aPageRelatedByParentId = null;
            $this->collPageContents = null;

            $this->collPagesRelatedById = null;

            $this->collUrlaliass = null;

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
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PageQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
			// nested_set behavior
			if ($this->isRoot()) {
			    throw new PropelException('Deletion of a root node is disabled for nested sets. Use PagePeer::deleteTree($scope) instead to delete an entire tree');
			}
			
			if ($this->isInTree()) {
			    $this->deleteDescendants($con);
			}

            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
				// nested_set behavior
				if ($this->isInTree()) {
				    // fill up the room that was used by the node
				    PagePeer::shiftRLValues(-2, $this->getRightValue() + 1, null, $this->getScopeValue(), $con);
				}

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
            $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
			// nested_set behavior
			if ($this->isNew() && $this->isRoot()) {
			    // check if no other root exist in, the tree
			    $nbRoots = PageQuery::create()
			        ->addUsingAlias(PagePeer::LEFT_COL, 1, Criteria::EQUAL)
			        ->addUsingAlias(PagePeer::SCOPE_COL, $this->getScopeValue(), Criteria::EQUAL)
			        ->count($con);
			    if ($nbRoots > 0) {
			            throw new PropelException(sprintf('A root node already exists in this tree with scope "%s".', $this->getScopeValue()));
			    }
			}
			$this->processNestedSetQueries($con);
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
                PagePeer::addInstanceToPool($this);
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

            if ($this->aDomain !== null) {
                if ($this->aDomain->isModified() || $this->aDomain->isNew()) {
                    $affectedRows += $this->aDomain->save($con);
                }
                $this->setDomain($this->aDomain);
            }

            if ($this->aPageRelatedByParentId !== null) {
                if ($this->aPageRelatedByParentId->isModified() || $this->aPageRelatedByParentId->isNew()) {
                    $affectedRows += $this->aPageRelatedByParentId->save($con);
                }
                $this->setPageRelatedByParentId($this->aPageRelatedByParentId);
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

            if ($this->pageContentsScheduledForDeletion !== null) {
                if (!$this->pageContentsScheduledForDeletion->isEmpty()) {
                    foreach ($this->pageContentsScheduledForDeletion as $pageContent) {
                        // need to save related object because we set the relation to null
                        $pageContent->save($con);
                    }
                    $this->pageContentsScheduledForDeletion = null;
                }
            }

            if ($this->collPageContents !== null) {
                foreach ($this->collPageContents as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->pagesRelatedByIdScheduledForDeletion !== null) {
                if (!$this->pagesRelatedByIdScheduledForDeletion->isEmpty()) {
                    foreach ($this->pagesRelatedByIdScheduledForDeletion as $pageRelatedById) {
                        // need to save related object because we set the relation to null
                        $pageRelatedById->save($con);
                    }
                    $this->pagesRelatedByIdScheduledForDeletion = null;
                }
            }

            if ($this->collPagesRelatedById !== null) {
                foreach ($this->collPagesRelatedById as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->urlaliassScheduledForDeletion !== null) {
                if (!$this->urlaliassScheduledForDeletion->isEmpty()) {
                    foreach ($this->urlaliassScheduledForDeletion as $urlalias) {
                        // need to save related object because we set the relation to null
                        $urlalias->save($con);
                    }
                    $this->urlaliassScheduledForDeletion = null;
                }
            }

            if ($this->collUrlaliass !== null) {
                foreach ($this->collUrlaliass as $referrerFK) {
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

        $this->modifiedColumns[] = PagePeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PagePeer::ID . ')');
        }
        if (null === $this->id) {
            try {				
				$stmt = $con->query("SELECT nextval('kryn_system_page_id_seq')");
				$row = $stmt->fetch(PDO::FETCH_NUM);
				$this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PagePeer::ID)) {
            $modifiedColumns[':p' . $index++]  = 'ID';
        }
        if ($this->isColumnModified(PagePeer::PARENT_ID)) {
            $modifiedColumns[':p' . $index++]  = 'PARENT_ID';
        }
        if ($this->isColumnModified(PagePeer::DOMAIN_ID)) {
            $modifiedColumns[':p' . $index++]  = 'DOMAIN_ID';
        }
        if ($this->isColumnModified(PagePeer::LFT)) {
            $modifiedColumns[':p' . $index++]  = 'LFT';
        }
        if ($this->isColumnModified(PagePeer::RGT)) {
            $modifiedColumns[':p' . $index++]  = 'RGT';
        }
        if ($this->isColumnModified(PagePeer::LVL)) {
            $modifiedColumns[':p' . $index++]  = 'LVL';
        }
        if ($this->isColumnModified(PagePeer::TYPE)) {
            $modifiedColumns[':p' . $index++]  = 'TYPE';
        }
        if ($this->isColumnModified(PagePeer::TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'TITLE';
        }
        if ($this->isColumnModified(PagePeer::PAGE_TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'PAGE_TITLE';
        }
        if ($this->isColumnModified(PagePeer::URL)) {
            $modifiedColumns[':p' . $index++]  = 'URL';
        }
        if ($this->isColumnModified(PagePeer::FULL_URL)) {
            $modifiedColumns[':p' . $index++]  = 'FULL_URL';
        }
        if ($this->isColumnModified(PagePeer::LINK)) {
            $modifiedColumns[':p' . $index++]  = 'LINK';
        }
        if ($this->isColumnModified(PagePeer::LAYOUT)) {
            $modifiedColumns[':p' . $index++]  = 'LAYOUT';
        }
        if ($this->isColumnModified(PagePeer::SORT)) {
            $modifiedColumns[':p' . $index++]  = 'SORT';
        }
        if ($this->isColumnModified(PagePeer::SORT_MODE)) {
            $modifiedColumns[':p' . $index++]  = 'SORT_MODE';
        }
        if ($this->isColumnModified(PagePeer::TARGET)) {
            $modifiedColumns[':p' . $index++]  = 'TARGET';
        }
        if ($this->isColumnModified(PagePeer::VISIBLE)) {
            $modifiedColumns[':p' . $index++]  = 'VISIBLE';
        }
        if ($this->isColumnModified(PagePeer::ACCESS_DENIED)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_DENIED';
        }
        if ($this->isColumnModified(PagePeer::META)) {
            $modifiedColumns[':p' . $index++]  = 'META';
        }
        if ($this->isColumnModified(PagePeer::PROPERTIES)) {
            $modifiedColumns[':p' . $index++]  = 'PROPERTIES';
        }
        if ($this->isColumnModified(PagePeer::CDATE)) {
            $modifiedColumns[':p' . $index++]  = 'CDATE';
        }
        if ($this->isColumnModified(PagePeer::MDATE)) {
            $modifiedColumns[':p' . $index++]  = 'MDATE';
        }
        if ($this->isColumnModified(PagePeer::DRAFT_EXIST)) {
            $modifiedColumns[':p' . $index++]  = 'DRAFT_EXIST';
        }
        if ($this->isColumnModified(PagePeer::FORCE_HTTPS)) {
            $modifiedColumns[':p' . $index++]  = 'FORCE_HTTPS';
        }
        if ($this->isColumnModified(PagePeer::ACCESS_FROM)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_FROM';
        }
        if ($this->isColumnModified(PagePeer::ACCESS_TO)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_TO';
        }
        if ($this->isColumnModified(PagePeer::ACCESS_REDIRECTTO)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_REDIRECTTO';
        }
        if ($this->isColumnModified(PagePeer::ACCESS_NOHIDENAVI)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_NOHIDENAVI';
        }
        if ($this->isColumnModified(PagePeer::ACCESS_NEED_VIA)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_NEED_VIA';
        }
        if ($this->isColumnModified(PagePeer::ACCESS_FROM_GROUPS)) {
            $modifiedColumns[':p' . $index++]  = 'ACCESS_FROM_GROUPS';
        }
        if ($this->isColumnModified(PagePeer::CACHE)) {
            $modifiedColumns[':p' . $index++]  = 'CACHE';
        }
        if ($this->isColumnModified(PagePeer::SEARCH_WORDS)) {
            $modifiedColumns[':p' . $index++]  = 'SEARCH_WORDS';
        }
        if ($this->isColumnModified(PagePeer::UNSEARCHABLE)) {
            $modifiedColumns[':p' . $index++]  = 'UNSEARCHABLE';
        }
        if ($this->isColumnModified(PagePeer::ACTIVE_VERSION_ID)) {
            $modifiedColumns[':p' . $index++]  = 'ACTIVE_VERSION_ID';
        }

        $sql = sprintf(
            'INSERT INTO kryn_system_page (%s) VALUES (%s)',
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
                    case 'PARENT_ID':
						$stmt->bindValue($identifier, $this->parent_id, PDO::PARAM_INT);
                        break;
                    case 'DOMAIN_ID':
						$stmt->bindValue($identifier, $this->domain_id, PDO::PARAM_INT);
                        break;
                    case 'LFT':
						$stmt->bindValue($identifier, $this->lft, PDO::PARAM_INT);
                        break;
                    case 'RGT':
						$stmt->bindValue($identifier, $this->rgt, PDO::PARAM_INT);
                        break;
                    case 'LVL':
						$stmt->bindValue($identifier, $this->lvl, PDO::PARAM_INT);
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
                    case 'FULL_URL':
						$stmt->bindValue($identifier, $this->full_url, PDO::PARAM_STR);
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
                    case 'ACTIVE_VERSION_ID':
						$stmt->bindValue($identifier, $this->active_version_id, PDO::PARAM_INT);
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

            if ($this->aDomain !== null) {
                if (!$this->aDomain->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aDomain->getValidationFailures());
                }
            }

            if ($this->aPageRelatedByParentId !== null) {
                if (!$this->aPageRelatedByParentId->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aPageRelatedByParentId->getValidationFailures());
                }
            }


            if (($retval = PagePeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collPageContents !== null) {
                    foreach ($this->collPageContents as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collPagesRelatedById !== null) {
                    foreach ($this->collPagesRelatedById as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collUrlaliass !== null) {
                    foreach ($this->collUrlaliass as $referrerFK) {
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
        $pos = PagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getParentId();
                break;
            case 2:
                return $this->getDomainId();
                break;
            case 3:
                return $this->getLft();
                break;
            case 4:
                return $this->getRgt();
                break;
            case 5:
                return $this->getLvl();
                break;
            case 6:
                return $this->getType();
                break;
            case 7:
                return $this->getTitle();
                break;
            case 8:
                return $this->getPageTitle();
                break;
            case 9:
                return $this->getUrl();
                break;
            case 10:
                return $this->getFullUrl();
                break;
            case 11:
                return $this->getLink();
                break;
            case 12:
                return $this->getLayout();
                break;
            case 13:
                return $this->getSort();
                break;
            case 14:
                return $this->getSortMode();
                break;
            case 15:
                return $this->getTarget();
                break;
            case 16:
                return $this->getVisible();
                break;
            case 17:
                return $this->getAccessDenied();
                break;
            case 18:
                return $this->getMeta();
                break;
            case 19:
                return $this->getProperties();
                break;
            case 20:
                return $this->getCdate();
                break;
            case 21:
                return $this->getMdate();
                break;
            case 22:
                return $this->getDraftExist();
                break;
            case 23:
                return $this->getForceHttps();
                break;
            case 24:
                return $this->getAccessFrom();
                break;
            case 25:
                return $this->getAccessTo();
                break;
            case 26:
                return $this->getAccessRedirectto();
                break;
            case 27:
                return $this->getAccessNohidenavi();
                break;
            case 28:
                return $this->getAccessNeedVia();
                break;
            case 29:
                return $this->getAccessFromGroups();
                break;
            case 30:
                return $this->getCache();
                break;
            case 31:
                return $this->getSearchWords();
                break;
            case 32:
                return $this->getUnsearchable();
                break;
            case 33:
                return $this->getActiveVersionId();
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
        if (isset($alreadyDumpedObjects['Page'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Page'][$this->getPrimaryKey()] = true;
        $keys = PagePeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getParentId(),
            $keys[2] => $this->getDomainId(),
            $keys[3] => $this->getLft(),
            $keys[4] => $this->getRgt(),
            $keys[5] => $this->getLvl(),
            $keys[6] => $this->getType(),
            $keys[7] => $this->getTitle(),
            $keys[8] => $this->getPageTitle(),
            $keys[9] => $this->getUrl(),
            $keys[10] => $this->getFullUrl(),
            $keys[11] => $this->getLink(),
            $keys[12] => $this->getLayout(),
            $keys[13] => $this->getSort(),
            $keys[14] => $this->getSortMode(),
            $keys[15] => $this->getTarget(),
            $keys[16] => $this->getVisible(),
            $keys[17] => $this->getAccessDenied(),
            $keys[18] => $this->getMeta(),
            $keys[19] => $this->getProperties(),
            $keys[20] => $this->getCdate(),
            $keys[21] => $this->getMdate(),
            $keys[22] => $this->getDraftExist(),
            $keys[23] => $this->getForceHttps(),
            $keys[24] => $this->getAccessFrom(),
            $keys[25] => $this->getAccessTo(),
            $keys[26] => $this->getAccessRedirectto(),
            $keys[27] => $this->getAccessNohidenavi(),
            $keys[28] => $this->getAccessNeedVia(),
            $keys[29] => $this->getAccessFromGroups(),
            $keys[30] => $this->getCache(),
            $keys[31] => $this->getSearchWords(),
            $keys[32] => $this->getUnsearchable(),
            $keys[33] => $this->getActiveVersionId(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aDomain) {
                $result['Domain'] = $this->aDomain->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->aPageRelatedByParentId) {
                $result['PageRelatedByParentId'] = $this->aPageRelatedByParentId->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collPageContents) {
                $result['PageContents'] = $this->collPageContents->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPagesRelatedById) {
                $result['PagesRelatedById'] = $this->collPagesRelatedById->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collUrlaliass) {
                $result['Urlaliass'] = $this->collUrlaliass->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = PagePeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setParentId($value);
                break;
            case 2:
                $this->setDomainId($value);
                break;
            case 3:
                $this->setLft($value);
                break;
            case 4:
                $this->setRgt($value);
                break;
            case 5:
                $this->setLvl($value);
                break;
            case 6:
                $this->setType($value);
                break;
            case 7:
                $this->setTitle($value);
                break;
            case 8:
                $this->setPageTitle($value);
                break;
            case 9:
                $this->setUrl($value);
                break;
            case 10:
                $this->setFullUrl($value);
                break;
            case 11:
                $this->setLink($value);
                break;
            case 12:
                $this->setLayout($value);
                break;
            case 13:
                $this->setSort($value);
                break;
            case 14:
                $this->setSortMode($value);
                break;
            case 15:
                $this->setTarget($value);
                break;
            case 16:
                $this->setVisible($value);
                break;
            case 17:
                $this->setAccessDenied($value);
                break;
            case 18:
                $this->setMeta($value);
                break;
            case 19:
                $this->setProperties($value);
                break;
            case 20:
                $this->setCdate($value);
                break;
            case 21:
                $this->setMdate($value);
                break;
            case 22:
                $this->setDraftExist($value);
                break;
            case 23:
                $this->setForceHttps($value);
                break;
            case 24:
                $this->setAccessFrom($value);
                break;
            case 25:
                $this->setAccessTo($value);
                break;
            case 26:
                $this->setAccessRedirectto($value);
                break;
            case 27:
                $this->setAccessNohidenavi($value);
                break;
            case 28:
                $this->setAccessNeedVia($value);
                break;
            case 29:
                $this->setAccessFromGroups($value);
                break;
            case 30:
                $this->setCache($value);
                break;
            case 31:
                $this->setSearchWords($value);
                break;
            case 32:
                $this->setUnsearchable($value);
                break;
            case 33:
                $this->setActiveVersionId($value);
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
        $keys = PagePeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setParentId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDomainId($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setLft($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setRgt($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setLvl($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setType($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setTitle($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setPageTitle($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setUrl($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setFullUrl($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setLink($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setLayout($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setSort($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setSortMode($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setTarget($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setVisible($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setAccessDenied($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setMeta($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setProperties($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setCdate($arr[$keys[20]]);
        if (array_key_exists($keys[21], $arr)) $this->setMdate($arr[$keys[21]]);
        if (array_key_exists($keys[22], $arr)) $this->setDraftExist($arr[$keys[22]]);
        if (array_key_exists($keys[23], $arr)) $this->setForceHttps($arr[$keys[23]]);
        if (array_key_exists($keys[24], $arr)) $this->setAccessFrom($arr[$keys[24]]);
        if (array_key_exists($keys[25], $arr)) $this->setAccessTo($arr[$keys[25]]);
        if (array_key_exists($keys[26], $arr)) $this->setAccessRedirectto($arr[$keys[26]]);
        if (array_key_exists($keys[27], $arr)) $this->setAccessNohidenavi($arr[$keys[27]]);
        if (array_key_exists($keys[28], $arr)) $this->setAccessNeedVia($arr[$keys[28]]);
        if (array_key_exists($keys[29], $arr)) $this->setAccessFromGroups($arr[$keys[29]]);
        if (array_key_exists($keys[30], $arr)) $this->setCache($arr[$keys[30]]);
        if (array_key_exists($keys[31], $arr)) $this->setSearchWords($arr[$keys[31]]);
        if (array_key_exists($keys[32], $arr)) $this->setUnsearchable($arr[$keys[32]]);
        if (array_key_exists($keys[33], $arr)) $this->setActiveVersionId($arr[$keys[33]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PagePeer::DATABASE_NAME);

        if ($this->isColumnModified(PagePeer::ID)) $criteria->add(PagePeer::ID, $this->id);
        if ($this->isColumnModified(PagePeer::PARENT_ID)) $criteria->add(PagePeer::PARENT_ID, $this->parent_id);
        if ($this->isColumnModified(PagePeer::DOMAIN_ID)) $criteria->add(PagePeer::DOMAIN_ID, $this->domain_id);
        if ($this->isColumnModified(PagePeer::LFT)) $criteria->add(PagePeer::LFT, $this->lft);
        if ($this->isColumnModified(PagePeer::RGT)) $criteria->add(PagePeer::RGT, $this->rgt);
        if ($this->isColumnModified(PagePeer::LVL)) $criteria->add(PagePeer::LVL, $this->lvl);
        if ($this->isColumnModified(PagePeer::TYPE)) $criteria->add(PagePeer::TYPE, $this->type);
        if ($this->isColumnModified(PagePeer::TITLE)) $criteria->add(PagePeer::TITLE, $this->title);
        if ($this->isColumnModified(PagePeer::PAGE_TITLE)) $criteria->add(PagePeer::PAGE_TITLE, $this->page_title);
        if ($this->isColumnModified(PagePeer::URL)) $criteria->add(PagePeer::URL, $this->url);
        if ($this->isColumnModified(PagePeer::FULL_URL)) $criteria->add(PagePeer::FULL_URL, $this->full_url);
        if ($this->isColumnModified(PagePeer::LINK)) $criteria->add(PagePeer::LINK, $this->link);
        if ($this->isColumnModified(PagePeer::LAYOUT)) $criteria->add(PagePeer::LAYOUT, $this->layout);
        if ($this->isColumnModified(PagePeer::SORT)) $criteria->add(PagePeer::SORT, $this->sort);
        if ($this->isColumnModified(PagePeer::SORT_MODE)) $criteria->add(PagePeer::SORT_MODE, $this->sort_mode);
        if ($this->isColumnModified(PagePeer::TARGET)) $criteria->add(PagePeer::TARGET, $this->target);
        if ($this->isColumnModified(PagePeer::VISIBLE)) $criteria->add(PagePeer::VISIBLE, $this->visible);
        if ($this->isColumnModified(PagePeer::ACCESS_DENIED)) $criteria->add(PagePeer::ACCESS_DENIED, $this->access_denied);
        if ($this->isColumnModified(PagePeer::META)) $criteria->add(PagePeer::META, $this->meta);
        if ($this->isColumnModified(PagePeer::PROPERTIES)) $criteria->add(PagePeer::PROPERTIES, $this->properties);
        if ($this->isColumnModified(PagePeer::CDATE)) $criteria->add(PagePeer::CDATE, $this->cdate);
        if ($this->isColumnModified(PagePeer::MDATE)) $criteria->add(PagePeer::MDATE, $this->mdate);
        if ($this->isColumnModified(PagePeer::DRAFT_EXIST)) $criteria->add(PagePeer::DRAFT_EXIST, $this->draft_exist);
        if ($this->isColumnModified(PagePeer::FORCE_HTTPS)) $criteria->add(PagePeer::FORCE_HTTPS, $this->force_https);
        if ($this->isColumnModified(PagePeer::ACCESS_FROM)) $criteria->add(PagePeer::ACCESS_FROM, $this->access_from);
        if ($this->isColumnModified(PagePeer::ACCESS_TO)) $criteria->add(PagePeer::ACCESS_TO, $this->access_to);
        if ($this->isColumnModified(PagePeer::ACCESS_REDIRECTTO)) $criteria->add(PagePeer::ACCESS_REDIRECTTO, $this->access_redirectto);
        if ($this->isColumnModified(PagePeer::ACCESS_NOHIDENAVI)) $criteria->add(PagePeer::ACCESS_NOHIDENAVI, $this->access_nohidenavi);
        if ($this->isColumnModified(PagePeer::ACCESS_NEED_VIA)) $criteria->add(PagePeer::ACCESS_NEED_VIA, $this->access_need_via);
        if ($this->isColumnModified(PagePeer::ACCESS_FROM_GROUPS)) $criteria->add(PagePeer::ACCESS_FROM_GROUPS, $this->access_from_groups);
        if ($this->isColumnModified(PagePeer::CACHE)) $criteria->add(PagePeer::CACHE, $this->cache);
        if ($this->isColumnModified(PagePeer::SEARCH_WORDS)) $criteria->add(PagePeer::SEARCH_WORDS, $this->search_words);
        if ($this->isColumnModified(PagePeer::UNSEARCHABLE)) $criteria->add(PagePeer::UNSEARCHABLE, $this->unsearchable);
        if ($this->isColumnModified(PagePeer::ACTIVE_VERSION_ID)) $criteria->add(PagePeer::ACTIVE_VERSION_ID, $this->active_version_id);

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
        $criteria = new Criteria(PagePeer::DATABASE_NAME);
        $criteria->add(PagePeer::ID, $this->id);

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
     * @param      object $copyObj An object of Page (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setParentId($this->getParentId());
        $copyObj->setDomainId($this->getDomainId());
        $copyObj->setLft($this->getLft());
        $copyObj->setRgt($this->getRgt());
        $copyObj->setLvl($this->getLvl());
        $copyObj->setType($this->getType());
        $copyObj->setTitle($this->getTitle());
        $copyObj->setPageTitle($this->getPageTitle());
        $copyObj->setUrl($this->getUrl());
        $copyObj->setFullUrl($this->getFullUrl());
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
        $copyObj->setActiveVersionId($this->getActiveVersionId());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getPageContents() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPageContent($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPagesRelatedById() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPageRelatedById($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getUrlaliass() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addUrlalias($relObj->copy($deepCopy));
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
     * @return                 Page Clone of current object.
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
     * @return   PagePeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PagePeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Domain object.
     *
     * @param                  Domain $v
     * @return                 Page The current object (for fluent API support)
     * @throws PropelException
     */
    public function setDomain(Domain $v = null)
    {
        if ($v === null) {
            $this->setDomainId(NULL);
        } else {
            $this->setDomainId($v->getId());
        }

        $this->aDomain = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Domain object, it will not be re-added.
        if ($v !== null) {
            $v->addPage($this);
        }


        return $this;
    }


    /**
     * Get the associated Domain object
     *
     * @param      PropelPDO $con Optional Connection object.
     * @return                 Domain The associated Domain object.
     * @throws PropelException
     */
    public function getDomain(PropelPDO $con = null)
    {
        if ($this->aDomain === null && ($this->domain_id !== null)) {
            $this->aDomain = DomainQuery::create()->findPk($this->domain_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aDomain->addPages($this);
             */
        }

        return $this->aDomain;
    }

    /**
     * Declares an association between this object and a Page object.
     *
     * @param                  Page $v
     * @return                 Page The current object (for fluent API support)
     * @throws PropelException
     */
    public function setPageRelatedByParentId(Page $v = null)
    {
        if ($v === null) {
            $this->setParentId(NULL);
        } else {
            $this->setParentId($v->getId());
        }

        $this->aPageRelatedByParentId = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Page object, it will not be re-added.
        if ($v !== null) {
            $v->addPageRelatedById($this);
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
    public function getPageRelatedByParentId(PropelPDO $con = null)
    {
        if ($this->aPageRelatedByParentId === null && ($this->parent_id !== null)) {
            $this->aPageRelatedByParentId = PageQuery::create()->findPk($this->parent_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aPageRelatedByParentId->addPagesRelatedById($this);
             */
        }

        return $this->aPageRelatedByParentId;
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
        if ('PageContent' == $relationName) {
            $this->initPageContents();
        }
        if ('PageRelatedById' == $relationName) {
            $this->initPagesRelatedById();
        }
        if ('Urlalias' == $relationName) {
            $this->initUrlaliass();
        }
    }

    /**
     * Clears out the collPageContents collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPageContents()
     */
    public function clearPageContents()
    {
        $this->collPageContents = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collPageContents collection.
     *
     * By default this just sets the collPageContents collection to an empty array (like clearcollPageContents());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPageContents($overrideExisting = true)
    {
        if (null !== $this->collPageContents && !$overrideExisting) {
            return;
        }
        $this->collPageContents = new PropelObjectCollection();
        $this->collPageContents->setModel('PageContent');
    }

    /**
     * Gets an array of PageContent objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Page is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|PageContent[] List of PageContent objects
     * @throws PropelException
     */
    public function getPageContents($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collPageContents || null !== $criteria) {
            if ($this->isNew() && null === $this->collPageContents) {
                // return empty collection
                $this->initPageContents();
            } else {
                $collPageContents = PageContentQuery::create(null, $criteria)
                    ->filterByPage($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collPageContents;
                }
                $this->collPageContents = $collPageContents;
            }
        }

        return $this->collPageContents;
    }

    /**
     * Sets a collection of PageContent objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $pageContents A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setPageContents(PropelCollection $pageContents, PropelPDO $con = null)
    {
        $this->pageContentsScheduledForDeletion = $this->getPageContents(new Criteria(), $con)->diff($pageContents);

        foreach ($this->pageContentsScheduledForDeletion as $pageContentRemoved) {
            $pageContentRemoved->setPage(null);
        }

        $this->collPageContents = null;
        foreach ($pageContents as $pageContent) {
            $this->addPageContent($pageContent);
        }

        $this->collPageContents = $pageContents;
    }

    /**
     * Returns the number of related PageContent objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related PageContent objects.
     * @throws PropelException
     */
    public function countPageContents(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collPageContents || null !== $criteria) {
            if ($this->isNew() && null === $this->collPageContents) {
                return 0;
            } else {
                $query = PageContentQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByPage($this)
                    ->count($con);
            }
        } else {
            return count($this->collPageContents);
        }
    }

    /**
     * Method called to associate a PageContent object to this object
     * through the PageContent foreign key attribute.
     *
     * @param    PageContent $l PageContent
     * @return   Page The current object (for fluent API support)
     */
    public function addPageContent(PageContent $l)
    {
        if ($this->collPageContents === null) {
            $this->initPageContents();
        }
        if (!$this->collPageContents->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddPageContent($l);
        }

        return $this;
    }

    /**
     * @param	PageContent $pageContent The pageContent object to add.
     */
    protected function doAddPageContent($pageContent)
    {
        $this->collPageContents[]= $pageContent;
        $pageContent->setPage($this);
    }

    /**
     * @param	PageContent $pageContent The pageContent object to remove.
     */
    public function removePageContent($pageContent)
    {
        if ($this->getPageContents()->contains($pageContent)) {
            $this->collPageContents->remove($this->collPageContents->search($pageContent));
            if (null === $this->pageContentsScheduledForDeletion) {
                $this->pageContentsScheduledForDeletion = clone $this->collPageContents;
                $this->pageContentsScheduledForDeletion->clear();
            }
            $this->pageContentsScheduledForDeletion[]= $pageContent;
            $pageContent->setPage(null);
        }
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Page is new, it will return
     * an empty collection; or if this Page has previously
     * been saved, it will retrieve related PageContents from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Page.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|PageContent[] List of PageContent objects
     */
    public function getPageContentsJoinPageVersion($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PageContentQuery::create(null, $criteria);
        $query->joinWith('PageVersion', $join_behavior);

        return $this->getPageContents($query, $con);
    }

    /**
     * Clears out the collPagesRelatedById collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addPagesRelatedById()
     */
    public function clearPagesRelatedById()
    {
        $this->collPagesRelatedById = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collPagesRelatedById collection.
     *
     * By default this just sets the collPagesRelatedById collection to an empty array (like clearcollPagesRelatedById());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPagesRelatedById($overrideExisting = true)
    {
        if (null !== $this->collPagesRelatedById && !$overrideExisting) {
            return;
        }
        $this->collPagesRelatedById = new PropelObjectCollection();
        $this->collPagesRelatedById->setModel('Page');
    }

    /**
     * Gets an array of Page objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Page is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|Page[] List of Page objects
     * @throws PropelException
     */
    public function getPagesRelatedById($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collPagesRelatedById || null !== $criteria) {
            if ($this->isNew() && null === $this->collPagesRelatedById) {
                // return empty collection
                $this->initPagesRelatedById();
            } else {
                $collPagesRelatedById = PageQuery::create(null, $criteria)
                    ->filterByPageRelatedByParentId($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collPagesRelatedById;
                }
                $this->collPagesRelatedById = $collPagesRelatedById;
            }
        }

        return $this->collPagesRelatedById;
    }

    /**
     * Sets a collection of PageRelatedById objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $pagesRelatedById A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setPagesRelatedById(PropelCollection $pagesRelatedById, PropelPDO $con = null)
    {
        $this->pagesRelatedByIdScheduledForDeletion = $this->getPagesRelatedById(new Criteria(), $con)->diff($pagesRelatedById);

        foreach ($this->pagesRelatedByIdScheduledForDeletion as $pageRelatedByIdRemoved) {
            $pageRelatedByIdRemoved->setPageRelatedByParentId(null);
        }

        $this->collPagesRelatedById = null;
        foreach ($pagesRelatedById as $pageRelatedById) {
            $this->addPageRelatedById($pageRelatedById);
        }

        $this->collPagesRelatedById = $pagesRelatedById;
    }

    /**
     * Returns the number of related Page objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related Page objects.
     * @throws PropelException
     */
    public function countPagesRelatedById(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collPagesRelatedById || null !== $criteria) {
            if ($this->isNew() && null === $this->collPagesRelatedById) {
                return 0;
            } else {
                $query = PageQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByPageRelatedByParentId($this)
                    ->count($con);
            }
        } else {
            return count($this->collPagesRelatedById);
        }
    }

    /**
     * Method called to associate a Page object to this object
     * through the Page foreign key attribute.
     *
     * @param    Page $l Page
     * @return   Page The current object (for fluent API support)
     */
    public function addPageRelatedById(Page $l)
    {
        if ($this->collPagesRelatedById === null) {
            $this->initPagesRelatedById();
        }
        if (!$this->collPagesRelatedById->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddPageRelatedById($l);
        }

        return $this;
    }

    /**
     * @param	PageRelatedById $pageRelatedById The pageRelatedById object to add.
     */
    protected function doAddPageRelatedById($pageRelatedById)
    {
        $this->collPagesRelatedById[]= $pageRelatedById;
        $pageRelatedById->setPageRelatedByParentId($this);
    }

    /**
     * @param	PageRelatedById $pageRelatedById The pageRelatedById object to remove.
     */
    public function removePageRelatedById($pageRelatedById)
    {
        if ($this->getPagesRelatedById()->contains($pageRelatedById)) {
            $this->collPagesRelatedById->remove($this->collPagesRelatedById->search($pageRelatedById));
            if (null === $this->pagesRelatedByIdScheduledForDeletion) {
                $this->pagesRelatedByIdScheduledForDeletion = clone $this->collPagesRelatedById;
                $this->pagesRelatedByIdScheduledForDeletion->clear();
            }
            $this->pagesRelatedByIdScheduledForDeletion[]= $pageRelatedById;
            $pageRelatedById->setPageRelatedByParentId(null);
        }
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Page is new, it will return
     * an empty collection; or if this Page has previously
     * been saved, it will retrieve related PagesRelatedById from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Page.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @param      string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Page[] List of Page objects
     */
    public function getPagesRelatedByIdJoinDomain($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PageQuery::create(null, $criteria);
        $query->joinWith('Domain', $join_behavior);

        return $this->getPagesRelatedById($query, $con);
    }

    /**
     * Clears out the collUrlaliass collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addUrlaliass()
     */
    public function clearUrlaliass()
    {
        $this->collUrlaliass = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collUrlaliass collection.
     *
     * By default this just sets the collUrlaliass collection to an empty array (like clearcollUrlaliass());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initUrlaliass($overrideExisting = true)
    {
        if (null !== $this->collUrlaliass && !$overrideExisting) {
            return;
        }
        $this->collUrlaliass = new PropelObjectCollection();
        $this->collUrlaliass->setModel('Urlalias');
    }

    /**
     * Gets an array of Urlalias objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Page is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      PropelPDO $con optional connection object
     * @return PropelObjectCollection|Urlalias[] List of Urlalias objects
     * @throws PropelException
     */
    public function getUrlaliass($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collUrlaliass || null !== $criteria) {
            if ($this->isNew() && null === $this->collUrlaliass) {
                // return empty collection
                $this->initUrlaliass();
            } else {
                $collUrlaliass = UrlaliasQuery::create(null, $criteria)
                    ->filterByPage($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collUrlaliass;
                }
                $this->collUrlaliass = $collUrlaliass;
            }
        }

        return $this->collUrlaliass;
    }

    /**
     * Sets a collection of Urlalias objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      PropelCollection $urlaliass A Propel collection.
     * @param      PropelPDO $con Optional connection object
     */
    public function setUrlaliass(PropelCollection $urlaliass, PropelPDO $con = null)
    {
        $this->urlaliassScheduledForDeletion = $this->getUrlaliass(new Criteria(), $con)->diff($urlaliass);

        foreach ($this->urlaliassScheduledForDeletion as $urlaliasRemoved) {
            $urlaliasRemoved->setPage(null);
        }

        $this->collUrlaliass = null;
        foreach ($urlaliass as $urlalias) {
            $this->addUrlalias($urlalias);
        }

        $this->collUrlaliass = $urlaliass;
    }

    /**
     * Returns the number of related Urlalias objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      PropelPDO $con
     * @return int             Count of related Urlalias objects.
     * @throws PropelException
     */
    public function countUrlaliass(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collUrlaliass || null !== $criteria) {
            if ($this->isNew() && null === $this->collUrlaliass) {
                return 0;
            } else {
                $query = UrlaliasQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByPage($this)
                    ->count($con);
            }
        } else {
            return count($this->collUrlaliass);
        }
    }

    /**
     * Method called to associate a Urlalias object to this object
     * through the Urlalias foreign key attribute.
     *
     * @param    Urlalias $l Urlalias
     * @return   Page The current object (for fluent API support)
     */
    public function addUrlalias(Urlalias $l)
    {
        if ($this->collUrlaliass === null) {
            $this->initUrlaliass();
        }
        if (!$this->collUrlaliass->contains($l)) { // only add it if the **same** object is not already associated
            $this->doAddUrlalias($l);
        }

        return $this;
    }

    /**
     * @param	Urlalias $urlalias The urlalias object to add.
     */
    protected function doAddUrlalias($urlalias)
    {
        $this->collUrlaliass[]= $urlalias;
        $urlalias->setPage($this);
    }

    /**
     * @param	Urlalias $urlalias The urlalias object to remove.
     */
    public function removeUrlalias($urlalias)
    {
        if ($this->getUrlaliass()->contains($urlalias)) {
            $this->collUrlaliass->remove($this->collUrlaliass->search($urlalias));
            if (null === $this->urlaliassScheduledForDeletion) {
                $this->urlaliassScheduledForDeletion = clone $this->collUrlaliass;
                $this->urlaliassScheduledForDeletion->clear();
            }
            $this->urlaliassScheduledForDeletion[]= $urlalias;
            $urlalias->setPage(null);
        }
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->parent_id = null;
        $this->domain_id = null;
        $this->lft = null;
        $this->rgt = null;
        $this->lvl = null;
        $this->type = null;
        $this->title = null;
        $this->page_title = null;
        $this->url = null;
        $this->full_url = null;
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
        $this->active_version_id = null;
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
            if ($this->collPageContents) {
                foreach ($this->collPageContents as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPagesRelatedById) {
                foreach ($this->collPagesRelatedById as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collUrlaliass) {
                foreach ($this->collUrlaliass as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

		// nested_set behavior
		$this->collNestedSetChildren = null;
		$this->aNestedSetParent = null;
        if ($this->collPageContents instanceof PropelCollection) {
            $this->collPageContents->clearIterator();
        }
        $this->collPageContents = null;
        if ($this->collPagesRelatedById instanceof PropelCollection) {
            $this->collPagesRelatedById->clearIterator();
        }
        $this->collPagesRelatedById = null;
        if ($this->collUrlaliass instanceof PropelCollection) {
            $this->collUrlaliass->clearIterator();
        }
        $this->collUrlaliass = null;
        $this->aDomain = null;
        $this->aPageRelatedByParentId = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PagePeer::DEFAULT_STRING_FORMAT);
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

	// nested_set behavior
	
	/**
	 * Execute queries that were saved to be run inside the save transaction
	 */
	protected function processNestedSetQueries($con)
	{
	    foreach ($this->nestedSetQueries as $query) {
	        $query['arguments'][]= $con;
	        call_user_func_array($query['callable'], $query['arguments']);
	    }
	    $this->nestedSetQueries = array();
	}
	
	/**
	 * Proxy getter method for the left value of the nested set model.
	 * It provides a generic way to get the value, whatever the actual column name is.
	 *
	 * @return     int The nested set left value
	 */
	public function getLeftValue()
	{
	    return $this->lft;
	}
	
	/**
	 * Proxy getter method for the right value of the nested set model.
	 * It provides a generic way to get the value, whatever the actual column name is.
	 *
	 * @return     int The nested set right value
	 */
	public function getRightValue()
	{
	    return $this->rgt;
	}
	
	/**
	 * Proxy getter method for the level value of the nested set model.
	 * It provides a generic way to get the value, whatever the actual column name is.
	 *
	 * @return     int The nested set level value
	 */
	public function getLevel()
	{
	    return $this->lvl;
	}
	
	/**
	 * Proxy getter method for the scope value of the nested set model.
	 * It provides a generic way to get the value, whatever the actual column name is.
	 *
	 * @return     int The nested set scope value
	 */
	public function getScopeValue()
	{
	    return $this->domain_id;
	}
	
	/**
	 * Proxy setter method for the left value of the nested set model.
	 * It provides a generic way to set the value, whatever the actual column name is.
	 *
	 * @param      int $v The nested set left value
	 * @return     Page The current object (for fluent API support)
	 */
	public function setLeftValue($v)
	{
	    return $this->setLft($v);
	}
	
	/**
	 * Proxy setter method for the right value of the nested set model.
	 * It provides a generic way to set the value, whatever the actual column name is.
	 *
	 * @param      int $v The nested set right value
	 * @return     Page The current object (for fluent API support)
	 */
	public function setRightValue($v)
	{
	    return $this->setRgt($v);
	}
	
	/**
	 * Proxy setter method for the level value of the nested set model.
	 * It provides a generic way to set the value, whatever the actual column name is.
	 *
	 * @param      int $v The nested set level value
	 * @return     Page The current object (for fluent API support)
	 */
	public function setLevel($v)
	{
	    return $this->setLvl($v);
	}
	
	/**
	 * Proxy setter method for the scope value of the nested set model.
	 * It provides a generic way to set the value, whatever the actual column name is.
	 *
	 * @param      int $v The nested set scope value
	 * @return     Page The current object (for fluent API support)
	 */
	public function setScopeValue($v)
	{
	    return $this->setDomainId($v);
	}
	
	/**
	 * Creates the supplied node as the root node.
	 *
	 * @return     Page The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function makeRoot()
	{
	    if ($this->getLeftValue() || $this->getRightValue()) {
	        throw new PropelException('Cannot turn an existing node into a root node.');
	    }
	
	    $this->setLeftValue(1);
	    $this->setRightValue(2);
	    $this->setLevel(0);
	
	    return $this;
	}
	
	/**
	 * Tests if onbject is a node, i.e. if it is inserted in the tree
	 *
	 * @return     bool
	 */
	public function isInTree()
	{
	    return $this->getLeftValue() > 0 && $this->getRightValue() > $this->getLeftValue();
	}
	
	/**
	 * Tests if node is a root
	 *
	 * @return     bool
	 */
	public function isRoot()
	{
	    return $this->isInTree() && $this->getLeftValue() == 1;
	}
	
	/**
	 * Tests if node is a leaf
	 *
	 * @return     bool
	 */
	public function isLeaf()
	{
	    return $this->isInTree() &&  ($this->getRightValue() - $this->getLeftValue()) == 1;
	}
	
	/**
	 * Tests if node is a descendant of another node
	 *
	 * @param      Page $node Propel node object
	 * @return     bool
	 */
	public function isDescendantOf($parent)
	{
	    if ($this->getScopeValue() !== $parent->getScopeValue()) {
	        throw new PropelException('Comparing two nodes of different trees');
	    }
	
	    return $this->isInTree() && $this->getLeftValue() > $parent->getLeftValue() && $this->getRightValue() < $parent->getRightValue();
	}
	
	/**
	 * Tests if node is a ancestor of another node
	 *
	 * @param      Page $node Propel node object
	 * @return     bool
	 */
	public function isAncestorOf($child)
	{
	    return $child->isDescendantOf($this);
	}
	
	/**
	 * Tests if object has an ancestor
	 *
	 * @param      PropelPDO $con Connection to use.
	 * @return     bool
	 */
	public function hasParent(PropelPDO $con = null)
	{
	    return $this->getLevel() > 0;
	}
	
	/**
	 * Sets the cache for parent node of the current object.
	 * Warning: this does not move the current object in the tree.
	 * Use moveTofirstChildOf() or moveToLastChildOf() for that purpose
	 *
	 * @param      Page $parent
	 * @return     Page The current object, for fluid interface
	 */
	public function setParent($parent = null)
	{
	    $this->aNestedSetParent = $parent;
	
	    return $this;
	}
	
	/**
	 * Gets parent node for the current object if it exists
	 * The result is cached so further calls to the same method don't issue any queries
	 *
	 * @param      PropelPDO $con Connection to use.
	 * @return     mixed 		Propel object if exists else false
	 */
	public function getParent(PropelPDO $con = null)
	{
	    if ($this->aNestedSetParent === null && $this->hasParent()) {
	        $this->aNestedSetParent = PageQuery::create()
	            ->ancestorsOf($this)
	            ->orderByLevel(true)
	            ->findOne($con);
	    }
	
	    return $this->aNestedSetParent;
	}
	
	/**
	 * Determines if the node has previous sibling
	 *
	 * @param      PropelPDO $con Connection to use.
	 * @return     bool
	 */
	public function hasPrevSibling(PropelPDO $con = null)
	{
	    if (!PagePeer::isValid($this)) {
	        return false;
	    }
	
	    return PageQuery::create()
	        ->filterByRgt($this->getLeftValue() - 1)
	        ->inTree($this->getScopeValue())
	        ->count($con) > 0;
	}
	
	/**
	 * Gets previous sibling for the given node if it exists
	 *
	 * @param      PropelPDO $con Connection to use.
	 * @return     mixed 		Propel object if exists else false
	 */
	public function getPrevSibling(PropelPDO $con = null)
	{
	    return PageQuery::create()
	        ->filterByRgt($this->getLeftValue() - 1)
	        ->inTree($this->getScopeValue())
	        ->findOne($con);
	}
	
	/**
	 * Determines if the node has next sibling
	 *
	 * @param      PropelPDO $con Connection to use.
	 * @return     bool
	 */
	public function hasNextSibling(PropelPDO $con = null)
	{
	    if (!PagePeer::isValid($this)) {
	        return false;
	    }
	
	    return PageQuery::create()
	        ->filterByLft($this->getRightValue() + 1)
	        ->inTree($this->getScopeValue())
	        ->count($con) > 0;
	}
	
	/**
	 * Gets next sibling for the given node if it exists
	 *
	 * @param      PropelPDO $con Connection to use.
	 * @return     mixed 		Propel object if exists else false
	 */
	public function getNextSibling(PropelPDO $con = null)
	{
	    return PageQuery::create()
	        ->filterByLft($this->getRightValue() + 1)
	        ->inTree($this->getScopeValue())
	        ->findOne($con);
	}
	
	/**
	 * Clears out the $collNestedSetChildren collection
	 *
	 * This does not modify the database; however, it will remove any associated objects, causing
	 * them to be refetched by subsequent calls to accessor method.
	 *
	 * @return     void
	 */
	public function clearNestedSetChildren()
	{
	    $this->collNestedSetChildren = null;
	}
	
	/**
	 * Initializes the $collNestedSetChildren collection.
	 *
	 * @return     void
	 */
	public function initNestedSetChildren()
	{
	    $this->collNestedSetChildren = new PropelObjectCollection();
	    $this->collNestedSetChildren->setModel('Page');
	}
	
	/**
	 * Adds an element to the internal $collNestedSetChildren collection.
	 * Beware that this doesn't insert a node in the tree.
	 * This method is only used to facilitate children hydration.
	 *
	 * @param      Page $page
	 *
	 * @return     void
	 */
	public function addNestedSetChild($page)
	{
	    if ($this->collNestedSetChildren === null) {
	        $this->initNestedSetChildren();
	    }
	    if (!$this->collNestedSetChildren->contains($page)) { // only add it if the **same** object is not already associated
	        $this->collNestedSetChildren[]= $page;
	        $page->setParent($this);
	    }
	}
	
	/**
	 * Tests if node has children
	 *
	 * @return     bool
	 */
	public function hasChildren()
	{
	    return ($this->getRightValue() - $this->getLeftValue()) > 1;
	}
	
	/**
	 * Gets the children of the given node
	 *
	 * @param      Criteria  $criteria Criteria to filter results.
	 * @param      PropelPDO $con Connection to use.
	 * @return     array     List of Page objects
	 */
	public function getChildren($criteria = null, PropelPDO $con = null)
	{
	    if (null === $this->collNestedSetChildren || null !== $criteria) {
	        if ($this->isLeaf() || ($this->isNew() && null === $this->collNestedSetChildren)) {
	            // return empty collection
	            $this->initNestedSetChildren();
	        } else {
	            $collNestedSetChildren = PageQuery::create(null, $criteria)
	              ->childrenOf($this)
	              ->orderByBranch()
	                ->find($con);
	            if (null !== $criteria) {
	                return $collNestedSetChildren;
	            }
	            $this->collNestedSetChildren = $collNestedSetChildren;
	        }
	    }
	
	    return $this->collNestedSetChildren;
	}
	
	/**
	 * Gets number of children for the given node
	 *
	 * @param      Criteria  $criteria Criteria to filter results.
	 * @param      PropelPDO $con Connection to use.
	 * @return     int       Number of children
	 */
	public function countChildren($criteria = null, PropelPDO $con = null)
	{
	    if (null === $this->collNestedSetChildren || null !== $criteria) {
	        if ($this->isLeaf() || ($this->isNew() && null === $this->collNestedSetChildren)) {
	            return 0;
	        } else {
	            return PageQuery::create(null, $criteria)
	                ->childrenOf($this)
	                ->count($con);
	        }
	    } else {
	        return count($this->collNestedSetChildren);
	    }
	}
	
	/**
	 * Gets the first child of the given node
	 *
	 * @param      Criteria $query Criteria to filter results.
	 * @param      PropelPDO $con Connection to use.
	 * @return     array 		List of Page objects
	 */
	public function getFirstChild($query = null, PropelPDO $con = null)
	{
	    if ($this->isLeaf()) {
	        return array();
	    } else {
	        return PageQuery::create(null, $query)
	            ->childrenOf($this)
	            ->orderByBranch()
	            ->findOne($con);
	    }
	}
	
	/**
	 * Gets the last child of the given node
	 *
	 * @param      Criteria $query Criteria to filter results.
	 * @param      PropelPDO $con Connection to use.
	 * @return     array 		List of Page objects
	 */
	public function getLastChild($query = null, PropelPDO $con = null)
	{
	    if ($this->isLeaf()) {
	        return array();
	    } else {
	        return PageQuery::create(null, $query)
	            ->childrenOf($this)
	            ->orderByBranch(true)
	            ->findOne($con);
	    }
	}
	
	/**
	 * Gets the siblings of the given node
	 *
	 * @param      bool			$includeNode Whether to include the current node or not
	 * @param      Criteria $query Criteria to filter results.
	 * @param      PropelPDO $con Connection to use.
	 *
	 * @return     array 		List of Page objects
	 */
	public function getSiblings($includeNode = false, $query = null, PropelPDO $con = null)
	{
	    if ($this->isRoot()) {
	        return array();
	    } else {
	         $query = PageQuery::create(null, $query)
	                ->childrenOf($this->getParent($con))
	                ->orderByBranch();
	        if (!$includeNode) {
	            $query->prune($this);
	        }
	
	        return $query->find($con);
	    }
	}
	
	/**
	 * Gets descendants for the given node
	 *
	 * @param      Criteria $query Criteria to filter results.
	 * @param      PropelPDO $con Connection to use.
	 * @return     array 		List of Page objects
	 */
	public function getDescendants($query = null, PropelPDO $con = null)
	{
	    if ($this->isLeaf()) {
	        return array();
	    } else {
	        return PageQuery::create(null, $query)
	            ->descendantsOf($this)
	            ->orderByBranch()
	            ->find($con);
	    }
	}
	
	/**
	 * Gets number of descendants for the given node
	 *
	 * @param      Criteria $query Criteria to filter results.
	 * @param      PropelPDO $con Connection to use.
	 * @return     int 		Number of descendants
	 */
	public function countDescendants($query = null, PropelPDO $con = null)
	{
	    if ($this->isLeaf()) {
	        // save one query
	        return 0;
	    } else {
	        return PageQuery::create(null, $query)
	            ->descendantsOf($this)
	            ->count($con);
	    }
	}
	
	/**
	 * Gets descendants for the given node, plus the current node
	 *
	 * @param      Criteria $query Criteria to filter results.
	 * @param      PropelPDO $con Connection to use.
	 * @return     array 		List of Page objects
	 */
	public function getBranch($query = null, PropelPDO $con = null)
	{
	    return PageQuery::create(null, $query)
	        ->branchOf($this)
	        ->orderByBranch()
	        ->find($con);
	}
	
	/**
	 * Gets ancestors for the given node, starting with the root node
	 * Use it for breadcrumb paths for instance
	 *
	 * @param      Criteria $query Criteria to filter results.
	 * @param      PropelPDO $con Connection to use.
	 * @return     array 		List of Page objects
	 */
	public function getAncestors($query = null, PropelPDO $con = null)
	{
	    if ($this->isRoot()) {
	        // save one query
	        return array();
	    } else {
	        return PageQuery::create(null, $query)
	            ->ancestorsOf($this)
	            ->orderByBranch()
	            ->find($con);
	    }
	}
	
	/**
	 * Inserts the given $child node as first child of current
	 * The modifications in the current object and the tree
	 * are not persisted until the child object is saved.
	 *
	 * @param      Page $child	Propel object for child node
	 *
	 * @return     Page The current Propel object
	 */
	public function addChild(Page $child)
	{
	    if ($this->isNew()) {
	        throw new PropelException('A Page object must not be new to accept children.');
	    }
	    $child->insertAsFirstChildOf($this);
	
	    return $this;
	}
	
	/**
	 * Inserts the current node as first child of given $parent node
	 * The modifications in the current object and the tree
	 * are not persisted until the current object is saved.
	 *
	 * @param      Page $parent	Propel object for parent node
	 *
	 * @return     Page The current Propel object
	 */
	public function insertAsFirstChildOf($parent)
	{
	    if ($this->isInTree()) {
	        throw new PropelException('A Page object must not already be in the tree to be inserted. Use the moveToFirstChildOf() instead.');
	    }
	    $left = $parent->getLeftValue() + 1;
	    // Update node properties
	    $this->setLeftValue($left);
	    $this->setRightValue($left + 1);
	    $this->setLevel($parent->getLevel() + 1);
	    $scope = $parent->getScopeValue();
	    $this->setScopeValue($scope);
	    // update the children collection of the parent
	    $parent->addNestedSetChild($this);
	
	    // Keep the tree modification query for the save() transaction
	    $this->nestedSetQueries []= array(
	        'callable'  => array('PagePeer', 'makeRoomForLeaf'),
	        'arguments' => array($left, $scope, $this->isNew() ? null : $this)
	    );
	
	    return $this;
	}
	
	/**
	 * Inserts the current node as last child of given $parent node
	 * The modifications in the current object and the tree
	 * are not persisted until the current object is saved.
	 *
	 * @param      Page $parent	Propel object for parent node
	 *
	 * @return     Page The current Propel object
	 */
	public function insertAsLastChildOf($parent)
	{
	    if ($this->isInTree()) {
	        throw new PropelException('A Page object must not already be in the tree to be inserted. Use the moveToLastChildOf() instead.');
	    }
	    $left = $parent->getRightValue();
	    // Update node properties
	    $this->setLeftValue($left);
	    $this->setRightValue($left + 1);
	    $this->setLevel($parent->getLevel() + 1);
	    $scope = $parent->getScopeValue();
	    $this->setScopeValue($scope);
	    // update the children collection of the parent
	    $parent->addNestedSetChild($this);
	
	    // Keep the tree modification query for the save() transaction
	    $this->nestedSetQueries []= array(
	        'callable'  => array('PagePeer', 'makeRoomForLeaf'),
	        'arguments' => array($left, $scope, $this->isNew() ? null : $this)
	    );
	
	    return $this;
	}
	
	/**
	 * Inserts the current node as prev sibling given $sibling node
	 * The modifications in the current object and the tree
	 * are not persisted until the current object is saved.
	 *
	 * @param      Page $sibling	Propel object for parent node
	 *
	 * @return     Page The current Propel object
	 */
	public function insertAsPrevSiblingOf($sibling)
	{
	    if ($this->isInTree()) {
	        throw new PropelException('A Page object must not already be in the tree to be inserted. Use the moveToPrevSiblingOf() instead.');
	    }
	    $left = $sibling->getLeftValue();
	    // Update node properties
	    $this->setLeftValue($left);
	    $this->setRightValue($left + 1);
	    $this->setLevel($sibling->getLevel());
	    $scope = $sibling->getScopeValue();
	    $this->setScopeValue($scope);
	    // Keep the tree modification query for the save() transaction
	    $this->nestedSetQueries []= array(
	        'callable'  => array('PagePeer', 'makeRoomForLeaf'),
	        'arguments' => array($left, $scope, $this->isNew() ? null : $this)
	    );
	
	    return $this;
	}
	
	/**
	 * Inserts the current node as next sibling given $sibling node
	 * The modifications in the current object and the tree
	 * are not persisted until the current object is saved.
	 *
	 * @param      Page $sibling	Propel object for parent node
	 *
	 * @return     Page The current Propel object
	 */
	public function insertAsNextSiblingOf($sibling)
	{
	    if ($this->isInTree()) {
	        throw new PropelException('A Page object must not already be in the tree to be inserted. Use the moveToNextSiblingOf() instead.');
	    }
	    $left = $sibling->getRightValue() + 1;
	    // Update node properties
	    $this->setLeftValue($left);
	    $this->setRightValue($left + 1);
	    $this->setLevel($sibling->getLevel());
	    $scope = $sibling->getScopeValue();
	    $this->setScopeValue($scope);
	    // Keep the tree modification query for the save() transaction
	    $this->nestedSetQueries []= array(
	        'callable'  => array('PagePeer', 'makeRoomForLeaf'),
	        'arguments' => array($left, $scope, $this->isNew() ? null : $this)
	    );
	
	    return $this;
	}
	
	/**
	 * Moves current node and its subtree to be the first child of $parent
	 * The modifications in the current object and the tree are immediate
	 *
	 * @param      Page $parent	Propel object for parent node
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     Page The current Propel object
	 */
	public function moveToFirstChildOf($parent, PropelPDO $con = null)
	{
	    if (!$this->isInTree()) {
	        throw new PropelException('A Page object must be already in the tree to be moved. Use the insertAsFirstChildOf() instead.');
	    }
	    if ($parent->getScopeValue() != $this->getScopeValue()) {
	        throw new PropelException('Moving nodes across trees is not supported');
	    }
	    if ($parent->isDescendantOf($this)) {
	        throw new PropelException('Cannot move a node as child of one of its subtree nodes.');
	    }
	
	    $this->moveSubtreeTo($parent->getLeftValue() + 1, $parent->getLevel() - $this->getLevel() + 1, $con);
	
	    return $this;
	}
	
	/**
	 * Moves current node and its subtree to be the last child of $parent
	 * The modifications in the current object and the tree are immediate
	 *
	 * @param      Page $parent	Propel object for parent node
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     Page The current Propel object
	 */
	public function moveToLastChildOf($parent, PropelPDO $con = null)
	{
	    if (!$this->isInTree()) {
	        throw new PropelException('A Page object must be already in the tree to be moved. Use the insertAsLastChildOf() instead.');
	    }
	    if ($parent->getScopeValue() != $this->getScopeValue()) {
	        throw new PropelException('Moving nodes across trees is not supported');
	    }
	    if ($parent->isDescendantOf($this)) {
	        throw new PropelException('Cannot move a node as child of one of its subtree nodes.');
	    }
	
	    $this->moveSubtreeTo($parent->getRightValue(), $parent->getLevel() - $this->getLevel() + 1, $con);
	
	    return $this;
	}
	
	/**
	 * Moves current node and its subtree to be the previous sibling of $sibling
	 * The modifications in the current object and the tree are immediate
	 *
	 * @param      Page $sibling	Propel object for sibling node
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     Page The current Propel object
	 */
	public function moveToPrevSiblingOf($sibling, PropelPDO $con = null)
	{
	    if (!$this->isInTree()) {
	        throw new PropelException('A Page object must be already in the tree to be moved. Use the insertAsPrevSiblingOf() instead.');
	    }
	    if ($sibling->isRoot()) {
	        throw new PropelException('Cannot move to previous sibling of a root node.');
	    }
	    if ($sibling->getScopeValue() != $this->getScopeValue()) {
	        throw new PropelException('Moving nodes across trees is not supported');
	    }
	    if ($sibling->isDescendantOf($this)) {
	        throw new PropelException('Cannot move a node as sibling of one of its subtree nodes.');
	    }
	
	    $this->moveSubtreeTo($sibling->getLeftValue(), $sibling->getLevel() - $this->getLevel(), $con);
	
	    return $this;
	}
	
	/**
	 * Moves current node and its subtree to be the next sibling of $sibling
	 * The modifications in the current object and the tree are immediate
	 *
	 * @param      Page $sibling	Propel object for sibling node
	 * @param      PropelPDO $con	Connection to use.
	 *
	 * @return     Page The current Propel object
	 */
	public function moveToNextSiblingOf($sibling, PropelPDO $con = null)
	{
	    if (!$this->isInTree()) {
	        throw new PropelException('A Page object must be already in the tree to be moved. Use the insertAsNextSiblingOf() instead.');
	    }
	    if ($sibling->isRoot()) {
	        throw new PropelException('Cannot move to next sibling of a root node.');
	    }
	    if ($sibling->getScopeValue() != $this->getScopeValue()) {
	        throw new PropelException('Moving nodes across trees is not supported');
	    }
	    if ($sibling->isDescendantOf($this)) {
	        throw new PropelException('Cannot move a node as sibling of one of its subtree nodes.');
	    }
	
	    $this->moveSubtreeTo($sibling->getRightValue() + 1, $sibling->getLevel() - $this->getLevel(), $con);
	
	    return $this;
	}
	
	/**
	 * Move current node and its children to location $destLeft and updates rest of tree
	 *
	 * @param      int	$destLeft Destination left value
	 * @param      int	$levelDelta Delta to add to the levels
	 * @param      PropelPDO $con		Connection to use.
	 */
	protected function moveSubtreeTo($destLeft, $levelDelta, PropelPDO $con = null)
	{
	    $left  = $this->getLeftValue();
	    $right = $this->getRightValue();
	    $scope = $this->getScopeValue();
	
	    $treeSize = $right - $left +1;
	
	    if ($con === null) {
	        $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
	    }
	
	    $con->beginTransaction();
	    try {
	        // make room next to the target for the subtree
	        PagePeer::shiftRLValues($treeSize, $destLeft, null, $scope, $con);
	
	        if ($left >= $destLeft) { // src was shifted too?
	            $left += $treeSize;
	            $right += $treeSize;
	        }
	
	        if ($levelDelta) {
	            // update the levels of the subtree
	            PagePeer::shiftLevel($levelDelta, $left, $right, $scope, $con);
	        }
	
	        // move the subtree to the target
	        PagePeer::shiftRLValues($destLeft - $left, $left, $right, $scope, $con);
	
	        // remove the empty room at the previous location of the subtree
	        PagePeer::shiftRLValues(-$treeSize, $right + 1, null, $scope, $con);
	
	        // update all loaded nodes
	        PagePeer::updateLoadedNodes(null, $con);
	
	        $con->commit();
	    } catch (PropelException $e) {
	        $con->rollback();
	        throw $e;
	    }
	}
	
	/**
	 * Deletes all descendants for the given node
	 * Instance pooling is wiped out by this command,
	 * so existing Page instances are probably invalid (except for the current one)
	 *
	 * @param      PropelPDO $con Connection to use.
	 *
	 * @return     int 		number of deleted nodes
	 */
	public function deleteDescendants(PropelPDO $con = null)
	{
	    if ($this->isLeaf()) {
	        // save one query
	        return;
	    }
	    if ($con === null) {
	        $con = Propel::getConnection(PagePeer::DATABASE_NAME, Propel::CONNECTION_READ);
	    }
	    $left = $this->getLeftValue();
	    $right = $this->getRightValue();
	    $scope = $this->getScopeValue();
	    $con->beginTransaction();
	    try {
	        // delete descendant nodes (will empty the instance pool)
	        $ret = PageQuery::create()
	            ->descendantsOf($this)
	            ->delete($con);
	
	        // fill up the room that was used by descendants
	        PagePeer::shiftRLValues($left - $right + 1, $right, null, $scope, $con);
	
	        // fix the right value for the current node, which is now a leaf
	        $this->setRightValue($left + 1);
	
	        $con->commit();
	    } catch (Exception $e) {
	        $con->rollback();
	        throw $e;
	    }
	
	    return $ret;
	}
	
	/**
	 * Returns a pre-order iterator for this node and its children.
	 *
	 * @return     RecursiveIterator
	 */
	public function getIterator()
	{
	    return new NestedSetRecursiveIterator($this);
	}

} // BasePage

<?php

namespace Admin;

use Admin\Controller\ObjectCrudController;
use Core\Config\EntryPoint;
use Core\Config\Field;
use Core\Config\Model;
use Core\Kryn;
use Core\Object;
use Core\Permission;

class ObjectCrud
{
    /**
     * Defines the table which should be accessed.
     * This variable has to be set by any subclass.
     *
     * Use this only if you know, what you're doing,
     * normally this comes from the object settings.
     *
     * @var string table name
     */
    protected $table = '';

    /**
     * Defines the object which should be listed.
     *
     * @var string object key
     */
    protected $object = '';

    /**
     * Copy of the object definition
     *
     * @var \Core\Config\Object
     */
    protected $objectDefinition;

    /**
     * Defines your primary fiels as a array.
     * Example: $primary = array('id');
     * Example: $primary = array('id', 'name');
     *
     * Use this only if you know, what you're doing,
     * normally this comes from the object settings.
     *
     * @abstract
     * @var array
     */
    protected $primary = array();

    /**
     * The primary key of the current object.
     * If the class created a item through addItem(),
     * it contains the primary key of the newly created
     * item.
     *
     * array(
     *    'id' => 1234
     * )
     *
     * array(
     *     'id' => 1234,
     *     'subId' => 5678
     * )
     *
     * @var array
     * @see getPrimaryKey()
     */
    protected $primaryKey = array();

    /**
     * Defines the fields of your edit/add window which should be displayed.
     * Can contains several fields nested, via 'children', also type 'tab' are allowed.
     * Every ka.field is allowed.
     *
     * @abstract
     * @var array
     */
    protected $fields = array();

    /**
     * Defines the fields of your table which should be displayed.
     * Only one level, no children, no tabs. Use the window editor,
     * to get the list of possible types.
     *
     * @abstract
     * @var array
     */
    protected $columns = null;

    /**
     * Defines how many rows should be displayed per page.
     *
     * @var integer number of rows per page
     */
    protected $defaultLimit = 15;

    /**
     * Order field
     *
     * @var string
     */
    protected $orderBy = '';

    /**
     * Order field
     *
     * @private
     * @var string
     */
    protected $customOrderBy = '';

    /**
     * Order direction
     *
     * @var string
     */
    protected $orderByDirection = 'ASC';

    /**
     * Default order
     *
     * array(
     *      array('field' => 'group_id', 'direction' => 'asc'),
     *      array('field' => 'title', 'direction' => 'asc')
     * );
     *
     * or
     *
     * array(
     *     'group_id' => 'asc',
     *     'title' => 'desc'
     * )
     *
     * @var array
     */
    protected $order = array();

    /**
     * Contains the fields for the search.
     *
     * @var array
     */
    protected $filter = array();

    /**
     * Defines the icon for the add button. Relative to media/ or #className for vector images
     *
     * @var string name of image
     */
    protected $addIcon = '#icon-plus-5';

    /**
     * Defines the icon for the edit button. Relative to media/ or #className for vector images
     *
     * @var string name of image
     */
    protected $editIcon = '#icon-pencil-2';

    /**
     * Defines the icon for the remove/delete button. Relative to media/ or #className for vector images
     *
     * @var string name of image
     */
    protected $removeIcon = '#icon-minus-5';

    /**
     * Defines the icon for the remove/delete button. Relative to media/ or #className for vector images
     *
     * @var string name of image
     */
    protected $removeIconItem = '#icon-minus-5';

    /**
     * The system opens this entrypoint when clicking on the add newt_button(left, top, text)n.
     * Default is <current>/.
     *
     * Relative or absolute paths are allowed.
     * Empty means current entrypoint.
     *
     * @var string
     */
    protected $addEntrypoint = '';

    /**
     * The system opens this entrypoint when clicking on the edit button.
     * Default is <current>/.
     *
     * Relative or absolute paths are allowed.
     * Empty means current entrypoint.
     *
     * @var string
     */
    protected $editEntrypoint = '';

    /**
     * @var string
     */
    protected $removeEntrypoint = '';

    /**
     * Allow a client to select own fields through the REST api.
     * (?fields=...)
     *
     * @var bool
     */
    protected $allowCustomSelectFields = false;

    protected $nestedRootEdit = false;
    protected $nestedRootAdd = false;
    protected $nestedAddWithPositionSelection = true;
    protected $nestedRootAddIcon = '#icon-plus-2';
    protected $nestedRootNewLabel = '[[New Root]]';
    protected $nestedRootRemove = false;

    protected $nestedRootEditEntrypoint = 'root/';
    protected $nestedRootAddEntrypoint = 'root/';

    protected $nestedRootRemoveEntrypoint = 'root/';

    /**
     * Defines whether the add button should be displayed
     *
     * @var boolean
     */
    protected $add = false;
    protected $newLabel = '[[New]]';
    protected $addMultiple = false;
    protected $addMultipleFieldContainerWidth = '70%';

    protected $addMultipleFields = array();

    protected $addMultipleFixedFields = array();

    protected $startCombine = false;

    /**
     * Defines whether the remove/delete button should be displayed
     * Also on each row the Delete-Button and the checkboxes.
     *
     * @var boolean
     */
    protected $remove = false;
    /**
     * Defines whether the edit button should be displayed
     *
     * @var boolean
     */
    protected $edit = false;

    protected $nestedMoveable = true;

    /**
     * Defines whether the list windows should display the language select box.
     * Note: Your table need a field 'lang' varchar(2). The windowList class filter by this.
     *
     * @var bool
     */
    protected $multiLanguage = false;

    /**
     * Defines whether the list windows should display the domain select box.
     * Note: Your table need a field 'domain_id' int. The windowList class filter by this.
     *
     * @var bool
     */
    protected $domainDepended = false;

    /**
     * Defines whether the workspace slider should appears or not.
     * Needs a column workspace_id in the table or active workspace at object.
     *
     * @var bool
     */
    protected $workspace = false;

    /**
     * @var string
     */
    protected $itemLayout = '';

    /**
     * The admin entry point out which this class has been called.
     *
     * @var array|null
     */
    protected $entryPoint = array();

    /**
     * @var array
     */
    protected $filterFields = array();

    /**
     * Flatten list of fields.
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * Defines whether the class checks, if the user has account to requested object item.
     *
     * @var boolean
     */
    protected $permissionCheck = true;

    /**
     * If the object is a nested set, then you should switch this property to true.
     *
     * @var bool
     */
    protected $asNested = false;


    /**
     * @var int
     */
    protected $itemsPerPage = 15;

    /**
     * Uses the HTTP 'PATCH' instead of the 'PUT'.
     * 'PUT' requires that you send all field, and 'PATCH'
     * only the fields that need to be updated.
     *
     * @var bool
     */
    protected $usePatch = true;

    /**
     * Constructor
     */
    public function __construct(EntryPoint $entryPoint = null)
    {
        $this->entryPoint = $entryPoint;
    }

    /**
     * @param bool $withoutObjectCheck
     *
     * @throws \ObjectNotFoundException
     */
    public function initialize($withoutObjectCheck = false)
    {
        $this->objectDefinition = \Core\Object::getDefinition($this->getObject());
        if (!$this->objectDefinition && $this->getObject() && !$withoutObjectCheck) {
            throw new \ObjectNotFoundException("Can not find object '" . $this->getObject() . "'");
        }

        if ($this->objectDefinition) {
            if (!$this->table) {
                $this->table = $this->objectDefinition->getTable();
            }
            if (!$this->fields) {
                $this->fields = $this->objectDefinition->getFields(true);
            }
            if (!$this->titleField) {
                $this->titleField = $this->objectDefinition->getLabel();
            }
        }

        //resolve shortcuts
        if ($this->fields) {
            $this->prepareFieldDefinition($this->fields);
            ObjectCrudController::translateFields($this->fields);
        }

        if ($this->fields) {
            foreach ($this->fields as $key => &$field) {
                if (is_array($field)) {
                    $fieldInstance = new Field();
                    $fieldInstance->fromArray($field);
                    $fieldInstance->setId($key);
                    $field = $fieldInstance;
                }
            }
        }

        if ($this->addMultipleFixedFields) {
            foreach ($this->addMultipleFixedFields as $key => &$field) {
                if (is_array($field)) {
                    $fieldInstance = new Field();
                    $fieldInstance->fromArray($field);
                    $fieldInstance->setId($key);
                    $field = $fieldInstance;
                }
            }
        }

        if ($this->addMultipleFields) {
            foreach ($this->addMultipleFields as $key => &$field) {
                if (is_array($field)) {
                    $fieldInstance = new Field();
                    $fieldInstance->fromArray($field);
                    $fieldInstance->setId($key);
                    $field = $fieldInstance;
                }
            }
        }

        if ($this->columns) {
            $this->prepareFieldDefinition($this->columns);
            ObjectCrudController::translateFields($this->columns);
        }

        if ($this->addMultipleFields) {
            $this->prepareFieldDefinition($this->addMultipleFields);
            ObjectCrudController::translateFields($this->addMultipleFields);
        }

        if ($this->addMultipleFixedFields) {
            $this->prepareFieldDefinition($this->addMultipleFixedFields);
            ObjectCrudController::translateFields($this->addMultipleFixedFields);
        }

        //do magic with type select and add all fields to _fields.
        if (count($this->fields) > 0) {
            $this->prepareFieldItem($this->fields);
        }

        if (is_string($this->primary)) {
            $this->primary = explode(',', str_replace(' ', '', $this->primary));
        }

        if (getArgv('order')) {
            $this->setOrder(getArgv('order'));
        }

        if (!$this->order || count($this->order) == 0) {

            /* compatibility */
            $this->orderByDirection = (strtolower($this->orderByDirection) == 'asc') ? 'asc' : 'desc';
            if ($this->orderBy) {
                $this->order = array($this->orderBy => $this->orderByDirection);
            }

        }

        if ((!$this->order || count($this->order) == 0) && $this->columns) {
            reset($this->columns);
            $this->order[key($this->columns)] = 'asc';
        }

        //normalize order array
        if (count($this->order) > 0 && is_numeric(key($this->order))) {
            $newOrder = array();
            foreach ($this->order as $order) {
                $newOrder[$order['field']] = $order['direction'];
            }
            $this->order = $newOrder;
        }

        $this->filterFields = array();

        if ($this->filter) {
            foreach ($this->filter as $key => $val) {

                if (is_numeric($key)) {
                    //no special definition
                    $fieldKey = $val;
                    $field = $this->fields[$val];
                } else {
                    $field = $val;
                    $fieldKey = $key;
                }

                //$this->prepareFieldItem($field);
                $this->filterFields[$fieldKey] = $field;
            }
        }

        if (!$this->primary) {
            $this->primary = array();
            if ($this->objectDefinition) {
                foreach ($this->objectDefinition->getPrimaryKeys() as $sfield) {
                    $this->primary[] = $sfield->getId();
                }
            }
        }

        $this->translate($this->nestedRootNewLabel);
        $this->translate($this->newLabel);
    }

    public function translate(&$field)
    {
        if (is_string($field) && substr($field, 0, 2) == '[[' && substr($field, -2) == ']]') {
            $field = t(substr($field, 2, -2));
        }

    }

    public function getInfo()
    {
        $vars = get_object_vars($this);
        $blacklist = array('objectDefinition', 'entryPoint');
        $result = array();

        foreach ($vars as $var => $val) {
            if (in_array($var, $blacklist)) {
                continue;
            }
            $method = 'get' . ucfirst($var);
            if (method_exists($this, $method)) {
                $result[$var] = $this->$method();
            }
        }

        if ($result['fields']) {
            foreach ($result['fields'] as &$field) {
                if ($field instanceof Model) {
                    $field = $field->toArray();
                }
            }
        }
        if ($result['addMultipleFixedFields']) {
            foreach ($result['addMultipleFixedFields'] as &$field) {
                if ($field instanceof Model) {
                    $field = $field->toArray();
                }
            }
        }
        if ($result['addMultipleFields']) {
            foreach ($result['addMultipleFields'] as &$field) {
                if ($field instanceof Model) {
                    $field = $field->toArray();
                }
            }
        }

        return $result;

    }

    /**
     * prepares $fields. Replace array items which are only a key (with no array definition) with
     * the array definition of the proper field from the object fields.
     *
     * @param $fields
     */
    public function prepareFieldDefinition(&$fields)
    {
        $i = 0;

        foreach ($fields as $key => $field) {
            if (is_numeric($key)) {

                $newItem = $this->objectDefinition->getField($field);
                if ($newItem) {
                    $newItem = $newItem->toArray();
                } else {
                    continue;
                }
                if (!$newItem['label']) {
                    $newItem['label'] = $field;
                }

                $fields = array_merge(
                    array_slice($fields, 0, $i),
                    array($field => $newItem),
                    array_slice($fields, $i + 1)
                );
                reset($fields);
                $i = -1;
            }
            $i++;
        }

        foreach ($fields as $key => &$field) {
            if (!is_array($field)) {
                continue;
            }

            $oField = $this->objectDefinition->getField($key);
            if ($oField) {
                if (!isset($field['type'])) {
                    $field['type'] = 'predefined';
                }
                if (strtolower($field['type']) == 'predefined' && !$field['object']) {
                    $field['object'] = $this->getObject();
                }
                if (strtolower($field['type']) == 'predefined' && !$field['field']) {
                    $field['field'] = $key;
                }

                if (!isset($field['label'])) {
                    $field['label'] = $oField->getLabel();
                }

                if (!isset($field['desc'])) {
                    $field['desc'] = $oField->getDesc();
                }

                if (!isset($field['label'])) {
                    $field['label'] = '!!No title defined (either object or in objectWindow class!!';
                }
            }

            if ($field['depends']) {
                $this->prepareFieldDefinition($field['depends']);
            }
            if ($field['children']) {
                $this->prepareFieldDefinition($field['children']);
            }
        }

    }

    /**
     * Prepare fields. Loading tableItems by select and file fields.
     *
     * @param array $fields
     *
     * @throws \Exception
     */
    public function prepareFieldItem($fields)
    {
        if (is_array($fields)) {
            foreach ($fields as &$field) {
                $this->prepareFieldItem($field);
            }
        } else {

            /*TODO
            if ($fields['needAccess'] && !Kryn::checkUrlAccess($fields['needAccess'])) {
                $fields = null;

                return;
            }*/

            if (substr($fields->getId(), 0, 2) != '__' && substr($fields->getId(), -2) != '__') {
                switch ($fields->getType()) {
                    case 'predefined':

                        if (!$fields->getObject()) {
                            throw new \Exception(tf(
                                'Fields of type `predefined` need a `object` option. [%s]',
                                $fields->toArray()
                            ));
                        }

                        if (!$fields->getField()) {
                            throw new \Exception(tf(
                                'Fields of type `predefined` need a `field` option. [%s]',
                                $fields->toArray()
                            ));
                        }

                        $object = Object::getDefinition($fields->getObject());
                        if (!$object) {
                            throw new \Exception(tf(
                                'Object `%s` does not exist [%s]',
                                $fields->getObject(),
                                $fields->toArray()
                            ));
                        }
                        $def = $object->getField($fields->getField());
                        if (!$def) {
                            $objectArray = $object->toArray();
                            $fields2 = $objectArray['fields'];
                            throw new \Exception(tf(
                                "Object `%s` does not have field `%s`. \n[%s]\n[%s]",
                                $fields->getObject(),
                                $fields->getField(),
                                $fields->toArray(),
                                json_format($fields2)
                            ));
                        }
                        if ($def) {
                            $fields = $def;
                        }

                        break;
                    case 'select':

//                        if ($fields->getTable()) {
//                            $fields['tableItems'] = dbTableFetchAll($fields['table']);
//                        } else if ($fields['sql']) {
//                            $fields['tableItems'] = dbExFetchAll(str_replace('%pfx%', pfx, $fields['sql']));
//                        } else if ($fields['method']) {
//                            $nam = $fields['method'];
//                            if (method_exists($this, $nam)) {
//                                $fields['tableItems'] = $this->$nam($fields);
//                            }
//                        }
//
//                        if ($fields['modifier'] && !empty($fields['modifier']) &&
//                            method_exists($this, $fields['modifier'])
//                        ) {
//                            $fields['tableItems'] = $this->$fields['modifier']($fields['tableItems']);
//                        }

                        break;
                }
                $this->_fields[$fields->getId()] = $fields;
            }

            if (is_array($fields->getChildren())) {
                $this->prepareFieldItem($fields->getChildren());
            }

        }
    }

    public function getDefaultFieldList()
    {
        $fields = array();

        foreach ($this->_fields as $key => $field) {
            if (!$field->isVirtual() && !$field['customValue'] && !$field['startEmpty']) {
                $fields[] = $field->getId();
            }
        }

        if ($this->getMultiLanguage()) {
            $fields[] = 'lang';
        }

        return $fields;
    }

    public function getPosition($pk)
    {
        /*$obj = \Core\Object::getClass($this->object);
        $primaryKey = $obj->normalizePrimaryKey($pk);

        $condition = $this->getCondition();

        if ($customCondition = $this->getCustomListingCondition())
            $condition = $condition ? array_merge($condition, $customCondition) : $customCondition;

        $options['permissionCheck'] = $this->permissionCheck;
        */
        $obj = \Core\Object::getClass($this->object);
        $primaryKey = $obj->normalizePrimaryKey($pk);
        $items = $this->getItems();

        $position = 0;

        if (count($primaryKey) == 1) {
            $singlePrimaryKey = key($primaryKey);
            $singlePrimaryValue = current($primaryKey);
        }

        foreach ($items as $item) {

            if ($singlePrimaryKey) {
                if ($item[$singlePrimaryKey] == $singlePrimaryValue) {
                    break;
                }
            } else {
                $isItem = true;
                foreach ($primaryKey as $prim => $val) {
                    if ($item[$prim] != $val) {
                        $isItem = false;
                    }
                }
                if ($isItem) {
                    break;
                }
            }

            $position++;
        }

        return $position;

    }

    /**
     * Returns a single item.
     *
     * $pk is an array with the primary key values.
     *
     * If one primary key:
     *   array(
     *    'id' => 1234
     *   )
     *
     * If multiple primary keys:
     *   array(
     *    'id' => 1234
     *    'secondId' => 5678
     *   )
     *
     * Use dbPrimaryKeyToCondition() to convert it to a full condition definition.
     *
     * @param  array $pk
     * @param  array $fields
     * @param  bool $withAcl
     *
     * @return array
     */
    public function getItem($pk, $fields = null, $withAcl = false)
    {
        $this->primaryKey = $pk;
        $options['fields'] = $this->getSelection($fields);

        $options['permissionCheck'] = $this->getPermissionCheck();

        $item = \Core\Object::get($this->object, $pk, $options);

        //add custom values
        foreach ($this->_fields as $key => $field) {
            if ($field['customValue'] && method_exists($this, $method = $field['customValue'])) {
                $item[$key] = $this->$method($field, $key);
            }
        }

        //check against additionaly our own custom condition
        if ($item && $condition = $this->getCondition()) {
            if (!\Core\Object::satisfy($item, $condition)) {
                $item = null;
            }
        }

        if ($item && $withAcl) {
            $this->prepareRow($item);
            $this->prepareFieldAcl($item);
        }

        return $item;
    }

    public function prepareFieldAcl(&$item)
    {
        if (false === $item['_editable']) {
            return;
        }
        $def = $this->getObjectDefinition();
        $acl = [];
        foreach ($def->getFields() as $field) {
            if (!Permission::checkUpdateExact($this->getObject(), $item, [$field->getId()])) {
                $acl[] = $field->getId();
            }
        }

        $item['_notEditable'] = $acl;
    }

    /**
     * Returns items with some information.
     *
     *   array(
     *       'items' => $items,
     *       'count' => $maxItems,
     *       'pages' => $maxPages
     *   );
     *
     * @param  array $filter
     * @param  int   $limit
     * @param  int   $offset
     * @param  string $query
     * @param  string $fields
     *
     * @return array
     */
    public function getItems($filter = null, $limit = null, $offset = null, $query = '', $fields = null, $orderBy = [])
    {
        $options = array();
        $options['permissionCheck'] = $this->getPermissionCheck();
        $options['offset'] = $offset;
        $options['limit'] = $limit ? $limit : $this->defaultLimit;

        $condition = $this->getCondition();

        if ($extraCondition = $this->getCustomListingCondition()) {
            $condition = !$condition ? $extraCondition : array($condition, 'AND', $extraCondition);
        }

        $options['order'] = $orderBy ?: $this->getOrder();
        $options['fields'] = $this->getSelection($fields);

        if ($filter) {
            $condition = self::buildFilter($filter);
        }

        if ($this->getMultiLanguage()) {
            //does the object have a lang field?
            if ($this->objectDefinition->getField('lang')) {

                //add language condition
                $langCondition = array(
                    array('lang', '=', substr((string)getArgv('lang'), 0, 3)),
                    'OR',
                    array('lang', 'IS NULL'),
                );
                if ($condition) {
                    $condition = array($condition, 'AND', $langCondition);
                } else {
                    $condition = $langCondition;
                }
            }
        }

        if ($query) {
            $queryCondition = $this->getQueryCondition($query, $options['fields']);
            if ($condition) {
                $condition = array($condition, 'AND', $queryCondition);
            } else {
                $condition = $queryCondition;
            }
        }

        $items = \Core\Object::getList($this->object, $condition, $options);

        if (is_array($items)) {
            foreach ($items as &$item) {
                if ($item) {
                    $this->prepareRow($item);
                }
            }
        }

        return $items;
    }

    /**
     * Returns the selection (field names)
     *
     * @param array $fields
     * @return array
     */
    public function getSelection($fields)
    {
        $result = [];
        if ($fields && $this->getAllowCustomSelectFields()) {
            if (!is_array($fields)) {
                if ('*' !== $fields) {
                    $result = explode(',', trim(preg_replace('/[^a-zA-Z0-9_\.\-,]/', '', $fields)));
                }
            }
        }

        if (!$result) {
            $result = array_keys($this->getColumns() ? : array());
        }

        if (!$result) {
            $result = $this->getDefaultFieldList();
        }

        return $result;
    }

    public function getQueryCondition($query, $fields)
    {
        $fields = $this->getSelection($fields);

        $query = preg_replace('/(?<!\\\\)\\*/', '$1%', $query);
        $query = str_replace('\\*', '*', $query);

        $result = [];
        foreach ($fields as $field) {
            if ($result) {
                $result[] = 'OR';
            }

            $result[] = [
                $field, 'LIKE', $query . '%'
            ];
        }

        return $result;
    }

    /**
     * @param  array      $filter
     *
     * @return array|null
     */
    public static function buildFilter($filter)
    {
        $condition = null;

        if (is_array($filter)) {
            //build condition query
            $condition = array();
            foreach ($filter as $k => $v) {
                if ($condition) {
                    $condition[] = 'and';
                }

                $k = camelcase2Underscore(substr($k, 1));

                if (strpos($v, '*') !== false) {
                    $condition[] = array($k, 'LIKE', str_replace('*', '%', $v));
                } else {
                    $condition[] = array($k, '=', $v);
                }
            }
        }

        return $condition;
    }

    /**
     *
     *
     * @param  string $fields
     *
     * @return array
     */
    public function getTreeFields($fields = null)
    {
        //use default fields from object definition
        $definition = $this->objectDefinition;
        $fields2 = array();

        if ($fields && $this->getAllowCustomSelectFields()) {
            if (is_array($fields)) {
                $fields2 = $fields;
            } else {
                $fields2 = explode(',', trim(preg_replace('/[^a-zA-Z0-9_,]/', '', $fields)));
            }
        }

        if ($definition && !$fields2) {

            if ($treeFields = $definition->getTreeFields()) {
                $fields2 = explode(',', trim(preg_replace('/[^a-zA-Z0-9_,]/', '', $treeFields)));
            } else {
                $fields2 = ($definition->getDefaultSelection()) ? explode(
                    ',',
                    trim(preg_replace('/[^a-zA-Z0-9_,]/', '', $definition->getDefaultSelection()))
                ) : array();
            }

            $fields2[] = $definition->getFieldLabel();

            if ($definition->getTreeIcon()) {
                $fields2[] = $definition->getTreeIcon();
            }
        }

        return $fields2;

    }

    /**
     * Returns items per branch.
     *
     * @param  mixed $pk
     * @param  array $filter
     * @param  mixed $fields
     * @param  mixed $scope
     * @param  int   $depth
     * @param  int   $limit
     * @param  int   $offset
     *
     * @return mixed
     */
    public function getBranchItems(
        $pk = null,
        $filter = null,
        $fields = null,
        $scope = null,
        $depth = 1,
        $limit = null,
        $offset = null
    ) {
        $options = array();
        $options['permissionCheck'] = $this->getPermissionCheck();
        $options['offset'] = $offset;
        $options['limit'] = $limit ? $limit : $this->defaultLimit;

        $condition = $this->getCondition();

        if ($filter) {
            if ($condition) {
                $condition = array($condition, 'AND', self::buildFilter($filter));
            } else {
                $condition = self::buildFilter($filter);
            }
        }

        if ($extraCondition = $this->getCustomListingCondition()) {
            $condition = !$condition ? $extraCondition : array($condition, 'AND', $extraCondition);
        }

        $options['order'] = $this->getOrder();

        $options['fields'] = array_keys($this->getColumns() ? : array());
        if (!$options['fields']) {
            $options['fields'] = array();
        }

        $options['fields'] += $this->getTreeFields();

        if ($this->getMultiLanguage()) {

            //does the object have a lang field?
            if ($this->objectDefinition->getfield('lang')) {

                //add language condition
                $langCondition = array(
                    array('lang', '=', (string)getArgv('lang')),
                    'OR',
                    array('lang', 'IS NULL'),
                );
                if ($condition) {
                    $condition = array($condition, 'AND', $langCondition);
                } else {
                    $condition = $langCondition;
                }
            }
        }

        $items = \Core\Object::getBranch($this->object, $pk, $condition, $depth, $scope, $options);

        if (is_array($items)) {
            foreach ($items as &$item) {
                $this->prepareRow($item);
            }
        }

        return $items;
    }

    /**
     * Returns items count per branch.
     *
     * @param  mixed $pk
     * @param  mixed $scope
     * @param  array $filter
     *
     * @return array
     */
    public function getBranchChildrenCount($pk = null, $scope = null, $filter = null)
    {
        $condition = $this->getCondition();

        if ($filter) {
            if ($condition) {
                $condition = array($condition, 'AND', self::buildFilter($filter));
            } else {
                $condition = self::buildFilter($filter);
            }
        }

        if ($extraCondition = $this->getCustomListingCondition()) {
            $condition = !$condition ? $extraCondition : array($condition, 'AND', $extraCondition);
        }

        $options['order'] = $this->getOrder();
        $options['permissionCheck'] = $this->getPermissionCheck();

        $options['fields'] = array_keys($this->getColumns());

        return \Core\Object::getBranchChildrenCount($this->object, $pk, $condition, $scope, $options);

    }

    public function getCount()
    {
        $options['permissionCheck'] = $this->getPermissionCheck();

        return \Core\Object::getCount($this->object);

    }

    public function getParent($pk)
    {
        $options = array('permissionCheck' => $this->getPermissionCheck());
        $primaryKey = Object::normalizePkString($this->object, $pk);

        return \Core\Object::getParent($this->object, $primaryKey, $options);

    }

    public function getParents($pk)
    {
        $options = array('permissionCheck' => $this->getPermissionCheck());
        $primaryKey = Object::normalizePkString($this->object, $pk);

        return \Core\Object::getParents($this->object, $primaryKey, $options);

    }

    public function moveItem($pk, $targetPk, $position = 'first', $targetObjectKey = '')
    {
        $options = array('permissionCheck' => $this->getPermissionCheck());

        $targetPk = \Core\Object::normalizePkString(
            $targetObjectKey ? $targetObjectKey : $this->getObject(),
            $targetPk
        );

        return \Core\Object::move($this->getObject(), $pk, $targetPk, $position, $targetObjectKey, $options);
    }

    public function getRoots()
    {
        $options['permissionCheck'] = $this->getPermissionCheck();

        return \Core\Object::getRoots($this->object, $options);

    }

    public function getRoot($scope = null)
    {
        $options['permissionCheck'] = $this->getPermissionCheck();

        return \Core\Object::getRoot($this->object, $scope, $options);

    }

    /**
     * Here you can define additional conditions for all operations (edit/listing).
     *
     * See phpDoc of global function dbConditionToSql for more details of the array structure of the result.
     *
     * @return array condition definition
     */
    public function getCondition()
    {
    }

    /**
     * Here you can define additional conditions for edit operations.
     *
     * See phpDoc of global function dbConditionToSql for more details.
     *
     * @return array condition definition
     */
    public function getCustomEditCondition()
    {
    }

    /**
     * Here you can define additional conditions for listing operations.
     *
     * See phpDoc of global function dbConditionToSql for more details.
     *
     * @return array condition definition
     */
    public function getCustomListingCondition()
    {
    }

    /**
     *
     * Adds multiple entries.
     *
     * We need as POST following data:
     *
     * {
     *
     *    _items: [
     *         {field1: 'foo', field2: 'bar'},
     *         {field1: 'foo2', field2: 'bar2'},
     *          ....
     *     ],
     *
     *     fixedField1: 'asd',
     *     fixedField2: 'fgh',
     *
     *     _: 'first', //take a look at `\Core\Object::add()` at parameter `$pPosition`
     *     _pk: {
     *         id: 123132
     *     },
     *     _targetObjectKey: 'node' //can differ between the actual object and the target (if we have a different object as root,
     *                              //then only position `first` and 'last` are available.)
     *
     *
     * }
     *
     * @return array|mixed
     */
    public function addMultiple()
    {
        $inserted = array();

        $fixedFields = $this->getAddMultipleFixedFields();

        $fixedData = array();

        if ($fixedFields) {
            $fixedData = $this->collectData($fixedFields);
        }

        $fields = $this->getAddMultipleFields();

        $position = getArgv('_position');

        if (!is_array($_REQUEST['_items'])) {
            $_REQUEST['_items'] = [];
        }

        $items = $_REQUEST['_items'];

        if ($position == 'first' || $position == 'next') {
            $items = array_reverse($_REQUEST['_items']);
        }

        foreach ($items as $item) {

            $data = $fixedData;
            $data += $this->collectData($fields, $item);

            try {
                $inserted[] = $this->add($data, getArgv('_pk'), $position, getArgv('_targetObjectKey'));
            } catch (\Exception $e) {
                $inserted[] = array('error' => $e);
            }

        }

        return $inserted;

    }

    /**
     * Adds a new item.
     *
     * Data is passed as POST.
     *
     * @param  array      $data
     * @param  array      $pk
     * @param  string     $position        If nested set. `first` (child), `last` (child), `prev` (sibling), `next` (sibling)
     * @param  int|string $targetObjectKey
     *
     * @return mixed      False if some went wrong or a array with the new primary keys.
     */
    public function add($data = null, $pk = null, $position = null, $targetObjectKey = null)
    {
        //collect values
        if ($data) {
            $data2 = $data;
        } else {
            $data2 = $this->collectData();
        }

        //do normal add through Core\Object
        $this->primaryKey = \Core\Object::add(
            $this->getObject(),
            $data2,
            $pk,
            $position,
            $targetObjectKey,
            array('permissionCheck' => $this->getPermissionCheck())
        );

        //handle customPostSave
        foreach ($this->_fields as $key => $field) {
            if ($field['customPostSave'] && method_exists($this, $method = $field['customPostSave'])) {
                $this->$method($field, $key);
            }
        }

        return $this->primaryKey;
    }


    /**
     * @param $pk
     *
     * @return bool
     */
    public function remove($pk)
    {
        $this->primaryKey = $pk;
        $options['permissionCheck'] = $this->getPermissionCheck();

        return \Core\Object::remove($this->object, $pk, $options);
    }


    /**
     * Updates a object entry. This means, all fields which are not defined will be saved as NULL.
     *
     * @param  array                        $pk
     *
     * @return bool
     * @throws \ObjectItemNotFoundException
     */
    public function update($pk)
    {
        $this->primaryKey = $pk;

        $options['fields'] = '';
        $options['permissionCheck'] = $this->getPermissionCheck();

        //collect values
        $data = $this->collectData();

        //check against additionally our own custom condition
        if ($condition = $this->getCondition()) {
            $item = \Core\Object::get($this->getObject(), $pk, $options);
            if (!\Core\Object::satisfy($item, $condition)) {
                return null;
            }
        }

        //do normal update through Core\Object
        $result = \Core\Object::update(
            $this->getObject(),
            $pk,
            $data,
            array('permissionCheck' => $this->getPermissionCheck())
        );

        return $result;
    }

    /**
     * Patches a object entry. This means, only defined fields will be saved. Fields which are not defined will
     * not be overwritten.
     *
     * @param  array                        $pk
     *
     * @return bool
     * @throws \ObjectItemNotFoundException
     */
    public function patch($pk)
    {
        $this->primaryKey = $pk;

        $options['fields'] = '';
        $options['permissionCheck'] = $this->getPermissionCheck();

        $item = \Core\Object::get($this->getObject(), $pk, $options);

        //collect values
        $allData = $this->collectData(null, $item);
        $data = [];

        foreach ($allData as $k => $v){
            if ($item[$k] != $allData[$k]) {
                $data[$k] = $v;
            }
        }

        //check against additionally our own custom condition
        if ($condition = $this->getCondition()) {
            if (!\Core\Object::satisfy($item, $condition)) {
                return null;
            }
        }

        //do normal update through Core\Object
        $result = \Core\Object::patch(
            $this->getObject(),
            $pk,
            $data,
            array('permissionCheck' => $this->getPermissionCheck())
        );

        return $result;
    }

    /**
     * Collects all data from GET/POST that has to be saved.
     * Iterates only through all defined fields in $fields.
     *
     * @param  \Core\Config\Field[] $fields The fields definition. If empty we use $this->fields.
     * @param  mixed $data  Default data. Is used if a field is not defined through _POST or _GET
     * @param  mixed $fallbackValues
     *
     * @return array
     * @throws \Core\Exceptions\InvalidFieldValueException
     */
    public function collectData($fields = null, $data = null)
    {
        $data2 = array();

        if ($fields) {
            $fields2 = $fields;
        } else {
            $fields2 = $this->_fields;
        }

        if ($this->getMultiLanguage()) {
            $langField = new Field();
            $langField->setId('lang');
            $langField->setRequired(true);
            $fields2[] = $langField;
        }

        $form = new \Core\Form\Form($fields2);

        foreach ($fields2 as $field) {
            $key = lcfirst($field->getId());
            $value = ($_POST[$key] ? : $_GET[$key]);
            if (null == $value && $data) {
                $value = $data[$key];
            }

            if ($field['customValue'] && method_exists($this, $method = $field['customValue'])) {
                $value = $this->$method($field, $key);
            }

            $field->setValue($value);
        }

        foreach ($fields2 as $field) {
            $key = $field->getId();
            if ($field['noSave']) {
                continue;
            }

            if ($field['customSave'] && method_exists($this, $method = $field['customValue'])) {
                $this->$method($field, $key);
                continue;
            }

            if (($field['saveOnlyFilled'] || $field['saveOnlyIfFilled']) && ($value === '' || $data2[$key] === null)) {
                continue;
            }

            if ($field->isValid()) {
                $data2[$key] = $field->getValue();
            } else {
                throw new \Core\Exceptions\InvalidFieldValueException(tf('The field `%s` has a invalid value.', $key));
            }
        }

        return $data2;
    }

    /**
     * Each item goes through this function in getItems(). Defines whether a item is editable or deleteable.
     * You can attach here extra action icons, too.
     *
     * Result should be:
     *
     * $item['_editable'] = true|false
     * $item['_deleteable'] = true|false
     * $item['_actions'] = array(
     *         array('/* action * /') //todo
     *     )
     * )
     *
     * @param array $item
     *
     * @return array
     */
    public function prepareRow(&$item)
    {
        $item['_editable'] = Permission::isUpdatable($this->getObject(), $item);
        $item['_deletable'] = Permission::isDeletable($this->getObject(), $item);
    }

    /**
     * @return array
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @return bool
     */
    public function getPermissionCheck()
    {
        return $this->permissionCheck;
    }

    /**
     * @param boolean $add
     */
    public function setAdd($add)
    {
        $this->add = $add;
    }

    /**
     * @return boolean
     */
    public function getAdd()
    {
        return $this->add;
    }

    /**
     * @param string $addEntrypoint
     */
    public function setAddEntrypoint($addEntrypoint)
    {
        $this->addEntrypoint = $addEntrypoint;
    }

    /**
     * @return string
     */
    public function getAddEntrypoint()
    {
        return $this->addEntrypoint;
    }

    /**
     * @param string $addIcon
     */
    public function setAddIcon($addIcon)
    {
        $this->addIcon = $addIcon;
    }

    /**
     * @return string
     */
    public function getAddIcon()
    {
        return $this->addIcon;
    }

    /**
     * @param string $customOrderBy
     */
    public function setCustomOrderBy($customOrderBy)
    {
        $this->customOrderBy = $customOrderBy;
    }

    /**
     * @return string
     */
    public function getCustomOrderBy()
    {
        return $this->customOrderBy;
    }

    /**
     * @param string $customOrderByDirection
     */
    public function setCustomOrderByDirection($customOrderByDirection)
    {
        $this->customOrderByDirection = $customOrderByDirection;
    }

    /**
     * @return string
     */
    public function getCustomOrderByDirection()
    {
        return $this->customOrderByDirection;
    }

    /**
     * @param boolean $domainDepended
     */
    public function setDomainDepended($domainDepended)
    {
        $this->domainDepended = $domainDepended;
    }

    /**
     * @return boolean
     */
    public function getDomainDepended()
    {
        return $this->domainDepended;
    }

    /**
     * @param boolean $edit
     */
    public function setEdit($edit)
    {
        $this->edit = $edit;
    }

    /**
     * @return boolean
     */
    public function getEdit()
    {
        return $this->edit;
    }

    /**
     * @param string $editEntrypoint
     */
    public function setEditEntrypoint($editEntrypoint)
    {
        $this->editEntrypoint = $editEntrypoint;
    }

    /**
     * @return string
     */
    public function getEditEntrypoint()
    {
        return $this->editEntrypoint;
    }

    /**
     * @param string $editIcon
     */
    public function setEditIcon($editIcon)
    {
        $this->editIcon = $editIcon;
    }

    /**
     * @return string
     */
    public function getEditIcon()
    {
        return $this->editIcon;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        $this->_fields = array();
        $this->prepareFieldItem($this->fields);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param array $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param int $defaultLimit
     */
    public function setDefaultLimit($defaultLimit)
    {
        $this->defaultLimit = $defaultLimit;
    }

    /**
     * @return int
     */
    public function getDefaultLimit()
    {
        return $this->defaultLimit;
    }

    /**
     * @param boolean $multiLanguage
     */
    public function setMultiLanguage($multiLanguage)
    {
        $this->multiLanguage = $multiLanguage;
    }

    /**
     * @return boolean
     */
    public function getMultiLanguage()
    {
        return $this->multiLanguage;
    }

    /**
     * @param string $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param array $objectDefinition
     */
    public function setObjectDefinition($objectDefinition)
    {
        $this->objectDefinition = $objectDefinition;
    }

    /**
     * @return \Core\Config\Object
     */
    public function getObjectDefinition()
    {
        return $this->objectDefinition;
    }

    /**
     * @param array $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return array
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param boolean $remove
     */
    public function setRemove($remove)
    {
        $this->remove = $remove;
    }

    /**
     * @return boolean
     */
    public function getRemove()
    {
        return $this->remove;
    }

    /**
     * @param string $removeIcon
     */
    public function setRemoveIcon($removeIcon)
    {
        $this->removeIcon = $removeIcon;
    }

    /**
     * @return string
     */
    public function getRemoveIcon()
    {
        return $this->removeIcon;
    }

    /**
     * @param string $removeIconItem
     */
    public function setRemoveIconItem($removeIconItem)
    {
        $this->removeIconItem = $removeIconItem;
    }

    /**
     * @return string
     */
    public function getRemoveIconItem()
    {
        return $this->removeIconItem;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param boolean $workspace
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
    }

    /**
     * @return boolean
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    public function setFilterFields($filterFields)
    {
        $this->filterFields = $filterFields;
    }

    public function getFilterFields()
    {
        return $this->filterFields;
    }

    public function setItemLayout($itemLayout)
    {
        $this->itemLayout = $itemLayout;
    }

    public function getItemLayout()
    {
        return $this->itemLayout;
    }

    /**
     * @param array $primary
     */
    public function setPrimary($primary)
    {
        $this->primary = $primary;
    }

    /**
     * @return array
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * @param EntryPoint $entryPoint
     */
    public function setEntryPoint($entryPoint)
    {
        $this->entryPoint = $entryPoint;
    }

    /**
     * @return EntryPoint
     */
    public function getEntryPoint()
    {
        return $this->entryPoint;
    }

    /**
     * @param boolean $asNested
     */
    public function setAsNested($asNested)
    {
        $this->asNested = $asNested;
    }

    /**
     * @return boolean
     */
    public function getAsNested()
    {
        return $this->asNested;
    }

    public function setNestedMove($nestedMove)
    {
        $this->nestedMove = $nestedMove;
    }

    public function getNestedMove()
    {
        return $this->nestedMove;
    }

    public function setNestedRootAdd($nestedRootAdd)
    {
        $this->nestedRootAdd = $nestedRootAdd;
    }

    public function getNestedRootAdd()
    {
        return $this->nestedRootAdd;
    }

    public function setNestedRootAddEntrypoint($nestedRootAddEntrypoint)
    {
        $this->nestedRootAddEntrypoint = $nestedRootAddEntrypoint;
    }

    public function getNestedRootAddEntrypoint()
    {
        return $this->nestedRootAddEntrypoint;
    }

    public function setNestedRootAddIcon($nestedRootAddIcon)
    {
        $this->nestedRootAddIcon = $nestedRootAddIcon;
    }

    public function getNestedRootAddIcon()
    {
        return $this->nestedRootAddIcon;
    }

    public function setNestedRootNewLabel($nestedRootNewLabel)
    {
        $this->nestedRootNewLabel = $nestedRootNewLabel;
    }

    public function getNestedRootNewLabel()
    {
        return $this->nestedRootNewLabel;
    }

    public function setNestedRootEdit($nestedRootEdit)
    {
        $this->nestedRootEdit = $nestedRootEdit;
    }

    public function getNestedRootEdit()
    {
        return $this->nestedRootEdit;
    }

    public function setNestedRootEditEntrypoint($nestedRootEditEntrypoint)
    {
        $this->nestedRootEditEntrypoint = $nestedRootEditEntrypoint;
    }

    public function getNestedRootEditEntrypoint()
    {
        return $this->nestedRootEditEntrypoint;
    }

    public function setNestedRootRemove($nestedRootRemove)
    {
        $this->nestedRootRemove = $nestedRootRemove;
    }

    public function getNestedRootRemove()
    {
        return $this->nestedRootRemove;
    }

    public function setNestedRootRemoveEntrypoint($nestedRootRemoveEntrypoint)
    {
        $this->nestedRootRemoveEntrypoint = $nestedRootRemoveEntrypoint;
    }

    public function getNestedRootRemoveEntrypoint()
    {
        return $this->nestedRootRemoveEntrypoint;
    }

    public function setNestedMoveable($nestedMoveable)
    {
        $this->nestedMoveable = $nestedMoveable;
    }

    public function getNestedMoveable()
    {
        return $this->nestedMoveable;
    }

    public function setNestedAddWithPositionSelection($nestedAddWithPositionSelection)
    {
        $this->nestedAddWithPositionSelection = $nestedAddWithPositionSelection;
    }

    public function getNestedAddWithPositionSelection()
    {
        return $this->nestedAddWithPositionSelection;
    }

    public function setNewLabel($newLabel)
    {
        $this->newLabel = $newLabel;
    }

    public function getNewLabel()
    {
        return $this->newLabel;
    }

    public function setRemoveEntrypoint($removeEntrypoint)
    {
        $this->removeEntrypoint = $removeEntrypoint;
    }

    public function getRemoveEntrypoint()
    {
        return $this->removeEntrypoint;
    }

    public function setAddMultiple($addMultiple)
    {
        $this->addMultiple = $addMultiple;
    }

    public function getAddMultiple()
    {
        return $this->addMultiple;
    }

    public function setAddMultipleFieldContainerWidth($addMultipleFieldContainerWidth)
    {
        $this->addMultipleFieldContainerWidth = $addMultipleFieldContainerWidth;
    }

    public function getAddMultipleFieldContainerWidth()
    {
        return $this->addMultipleFieldContainerWidth;
    }

    public function setAddMultipleFields($addMultipleFields)
    {
        $this->addMultipleFields = $addMultipleFields;
    }

    public function getAddMultipleFields()
    {
        return $this->addMultipleFields;
    }

    public function setAddMultipleFixedFields($addMultipleFixedFields)
    {
        $this->addMultipleFixedFields = $addMultipleFixedFields;
    }

    public function getAddMultipleFixedFields()
    {
        return $this->addMultipleFixedFields;
    }

    /**
     * @param boolean $allowCustomSelectFields
     */
    public function setAllowCustomSelectFields($allowCustomSelectFields)
    {
        $this->allowCustomSelectFields = $allowCustomSelectFields;
    }

    /**
     * @return boolean
     */
    public function getAllowCustomSelectFields()
    {
        return $this->allowCustomSelectFields;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @param boolean $usePatch
     */
    public function setUsePatch($usePatch)
    {
        $this->usePatch = $usePatch;
    }

    /**
     * @return boolean
     */
    public function getUsePatch()
    {
        return $this->usePatch;
    }

    /**
     * @param boolean $startCombine
     */
    public function setStartCombine($startCombine)
    {
        $this->startCombine = $startCombine;
    }

    /**
     * @return boolean
     */
    public function getStartCombine()
    {
        return $this->startCombine;
    }

}

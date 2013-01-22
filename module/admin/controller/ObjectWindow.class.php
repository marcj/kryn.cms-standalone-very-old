<?php

namespace Admin;

use \Core\Kryn;
use \Core\Object;

abstract class ObjectWindow {


    /**
     * Defines the table which should be accessed.
     * This variable has to be set by any subclass.
     *
     * Use this only if you know, what you're doing,
     * normally this comes from the object settings.
     *
     * @var string table name
     */
    public $table = '';

    /**
     * Defines the object which should be listed.
     *
     * @var string object key
     */
    public $object = '';

    /**
     * Copy of the object definition
     *
     * @var array
     */
    public $objectDefinition = array();

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
    public $primary = array();

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
    public $primaryKey = array();

    /**
     * Defines the fields of your edit/add window which should be displayed.
     * Can contains several fields nested, via 'children', also type 'tab' are allowed.
     * Every ka.field is allowed.
     *
     * @abstract
     * @var array
     */
    public $fields = array();

    /**
     * Defines the fields of your table which should be displayed.
     * Only one level, no children, no tabs. Use the window editor,
     * to get the list of possible types.
     *
     * @abstract
     * @var array
     */
    public $columns = array();

    /**
     * Defines how many rows should be displayed per page.
     *
     * @var integer number of rows per page
     */
    public $itemsPerPage = 10;

    /**
     * Order field
     *
     * @var string
     */
    public $orderBy = '';

    /**
     * Order field
     *
     * @private
     * @var string
     */
    public $customOrderBy = '';

    /**
     * Order direction
     *
     * @var string
     */
    public $orderByDirection = 'ASC';


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
    public $order = array();

    /**
     * Contains the fields for the search.
     *
     * @var array
     */
    public $filter = array();


    /**
     * Defines the icon for the add button. Relative to media/ or #className for vector images
     *
     * @var string name of image
     */
    public $addIcon = '#icon-plus-5';

    /**
     * Defines the icon for the edit button. Relative to media/ or #className for vector images
     *
     * @var string name of image
     */
    public $editIcon = '#icon-pencil-8';

    /**
     * Defines the icon for the remove/delete button. Relative to media/ or #className for vector images
     *
     * @var string name of image
     */
    public $removeIcon = '#icon-minus-5';

    /**
     * Defines the icon for the remove/delete button. Relative to media/ or #className for vector images
     *
     * @var string name of image
     */
    public $removeIconItem = '#icon-remove-3';


    /**
     * The system opens this entrypoint when clicking on the add newt_button(left, top, text)n.
     * Default is <current>/.
     * 
     * Relative or absolute paths are allowed.
     * Empty means current entrypoint.
      *
     * @var string
     */
    public $addEntrypoint = '';

    /**
     * The system opens this entrypoint when clicking on the edit button.
     * Default is <current>/.
     *
     * Relative or absolute paths are allowed.
     * Empty means current entrypoint.
     *
     * @var string
     */
    public $editEntrypoint = '';

    public $removeEntrypoint = '';


    public $nestedRootEdit = false;
    public $nestedRootAdd = false;
    public $nestedAddWithPositionSelection = true;
    public $nestedRootAddIcon = '#icon-plus-2';
    public $nestedRootAddLabel = '[[Add root]]';
    public $nestedRootRemove = false;

    public $nestedRootEditEntrypoint = 'root/';
    public $nestedRootAddEntrypoint = 'root/';

    public $nestedRootRemoveEntrypoint = 'root/';

    /**
     * Defines whether the add button should be displayed
     *
     * @var boolean
     */
    public $add = false;
    public $addLabel = '[[Add]]';
    public $addMultiple = false;
    public $addMultipleFieldContainerWidth = '70%';

    public $addMultipleFields = array();

    public $addMultipleFixedFields = array();




    /**
     * Defines whether the remove/delete button should be displayed
     * Also on each row the Delete-Button and the checkboxes.
     *
     * @var boolean
     */
    public $remove = false;
    /**
     * Defines whether the edit button should be displayed
     *
     * @var boolean
     */
    public $edit = false;


    public $nestedMoveable = true;

    /**
     * Defines whether the list windows should display the language select box.
     * Note: Your table need a field 'lang' varchar(2). The windowList class filter by this.
     *
     * @var bool
     */
    public $multiLanguage = false;

    /**
     * Defines whether the list windows should display the domain select box.
     * Note: Your table need a field 'domain_id' int. The windowList class filter by this.
     *
     * @var bool
     */
    public $domainDepended = false;

    /**
     * Defines whether the workspace slider should appears or not.
     * Needs a column workspace_id in the table or active workspace at object.
     *
     * @var bool
     */
    public $workspace = false;

    /**
     * @var string
     */
    public $itemLayout = '';

    /**
     * The admin entry point out which this class has been called.
     *
     * @var array|null
     */
    public $entryPoint = array();

    /**
     * @var array
     */
    public $filterFields = array();

    /**
     * Flatten list of fields.
     * 
     * @var array
     */
    public $_fields = array();

    /**
     * Defines whether the class checks, if the user has account to requested object item.
     * @var boolean
     */
    public $permissionCheck = true;


    /**
     * If the object is a nested set, then you should switch this property to true.
     *
     * @var bool
     */
    public $asNested = false;

    /**
     * Constructor
     */
    public function __construct($pEntryPoint = null, $pWithoutObjectCheck = false) {

        $this->entryPoint = $pEntryPoint;

        $this->objectDefinition = \Core\Object::getDefinition($this->object);
        if (!$this->objectDefinition && $this->object && !$pWithoutObjectCheck){
            throw new \ObjectNotFoundException("Can not find object '".$this->object."'");
        }

        $this->table = $this->objectDefinition['table'];
        $this->primary = array();
        foreach ($this->objectDefinition['fields'] as $key => &$field){
            if($field['primaryKey']){
                $this->primary[] = $key;
            }
        }

        if (!$this->titleField)
            $this->titleField = $this->objectDefinition['label'];

        //resolve shortcuts
        if ($this->fields){
            $this->prepareFieldDefinition($this->fields);
            ObjectWindowController::translateFields($this->fields);
        }

        if ($this->columns){
            $this->prepareFieldDefinition($this->columns);
            ObjectWindowController::translateFields($this->columns);
        }

        if ($this->addMultipleFields){
            $this->prepareFieldDefinition($this->addMultipleFields);
            ObjectWindowController::translateFields($this->addMultipleFields);
        }

        if ($this->addMultipleFixedFields){
            $this->prepareFieldDefinition($this->addMultipleFixedFields);
            ObjectWindowController::translateFields($this->addMultipleFixedFields);
        }




        //do magic with type select and add all fields to _fields.
        $this->prepareFieldItem($this->fields);

        if (is_string($this->primary)){
            $this->primary = explode(',', str_replace(' ', '', $this->primary));
        }

        if (getArgv('order'))
            $this->setOrder(getArgv('order'));

        if (!$this->order || count($this->order) == 0){

            /* compatibility */
            $this->orderByDirection = (strtolower($this->orderByDirection) == 'asc') ? 'asc' : 'desc';
            if ($this->orderBy)
                $this->order = array($this->orderBy => $this->orderByDirection);

        }

        if (!$this->order || count($this->order) == 0){
            $this->order[key($this->columns)] = 'asc';
        }

        //normalize order array
        if (count($this->order) > 0 && is_numeric(key($this->order))){
            $newOrder = array();
            foreach ($this->order as $order){
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

        $this->translate($this->nestedRootAddLabel);
        $this->translate($this->addLabel);

    }

    public function translate(&$pField){

        if (is_string($pField) && substr($pField, 0, 2) == '[[' && substr($pField, -2) == ']]')
            $pField = t(substr($pField, 2, -2));

    }

    public function getInfo(){

        $vars = get_object_vars($this);
        $blacklist = array('objectDefinition', 'entryPoint');
        $result = array();

        foreach ($vars as $var => $val){
            if (in_array($var, $blacklist)) continue;
            $method = 'get'.ucfirst($var);
            if (method_exists($this, $method))
                $result[$var] = $this->$method();
        }

        return $result;

    }

    /**
     * prepares $pFields. Replace array items which are only a key (with no array definition) with
     * the array definition of the proper field from the object fields.
     *
     * @param $pFields
     */
    public function prepareFieldDefinition(&$pFields){

        $i = 0;
        foreach ($pFields as $key => $field){
            if (is_numeric($key)){

                $newItem = $this->objectDefinition['fields'][lcfirst($field)];
                if (!$newItem['label']) $newItem['label'] = $field;

                $pFields = array_merge(
                    array_slice($pFields, 0, $i),
                    array($field => $newItem),
                    array_slice($pFields, $i+1)
                );
                reset($pFields);
                $i = -1;
            }
            $i++;
        }


        foreach ($pFields as $key => &$field){

            if (!is_array($field)) continue;

            if (!isset($field['label']) && $this->objectDefinition['fields'][$key]['label'])
                $field['label'] = $this->objectDefinition['fields'][$key]['label'];

            if (!isset($field['desc']) && $this->objectDefinition['fields'][$key]['desc'])
                $field['desc'] = $this->objectDefinition['fields'][$key]['desc'];

            if (!isset($field['label']))
                $field['label'] = '!!No title defined (either object or in objectWindow class!!';

            if ($field['depends']) $this->prepareFieldDefinition($field['depends']);
            if ($field['children']) $this->prepareFieldDefinition($field['children']);
        }

    }

    /**
     * Prepare fields. Loading tableItems by select and file fields.
     *
     * @param array $pFields
     * @param bool  $pKey
     */
    public function prepareFieldItem(&$pFields, $pKey = false) {

        if (is_array($pFields) && !is_string($pFields['type']) && !is_string($pFields['label'])) {
            foreach ($pFields as $key => &$field) {
                $this->prepareFieldItem($field, $key);
            }
        } else {

            /*TODO

            if ($pFields['needAccess'] && !Kryn::checkUrlAccess($pFields['needAccess'])) {
                $pFields = null;
                return;
            }*/

            if(substr($pKey,0,2) != '__' && substr($pKey, -2) != '__'){

                $this->_fields[$pKey] = $pFields;

                switch ($pFields['type']) {
                    case 'predefined':

                        $def = Object::getDefinition($pFields['object']);
                        $def = $def['fields'][$pFields['field']];
                        if ($def){
                            unset($pFields['object']);
                            unset($pFields['field']);
                            foreach ($def as $k => $v){
                                $pFields[$k] = $v;
                            }
                            unset($pFields['primaryKey']);

                        }

                        break;
                    case 'select':

                        if (!empty($field['eval']))
                            $pFields['tableItems'] = eval($field['eval']);
                        elseif ($pFields['relation'] == 'n-n')
                            $pFields['tableItems'] = dbTableFetchAll($pFields['n-n']['right']);
                        else if ($pFields['table'])
                            $pFields['tableItems'] = dbTableFetchAll($pFields['table']);
                        else if ($pFields['sql'])
                            $pFields['tableItems'] = dbExFetchAll(str_replace('%pfx%', pfx, $pFields['sql']));
                        else if ($pFields['method']) {
                            $nam = $pFields['method'];
                            if (method_exists($this, $nam))
                                $pFields['tableItems'] = $this->$nam($pFields);
                        }

                        if ($pFields['modifier'] && !empty($pFields['modifier']) &&
                            method_exists($this, $pFields['modifier'])
                        )
                            $pFields['tableItems'] = $this->$pFields['modifier']($pFields['tableItems']);

                        break;
                }
            }

            if (is_array($pFields['depends']))
                $this->prepareFieldItem($pFields['depends']);

            if (is_array($pFields['children']))
                $this->prepareFieldItem($pFields['children']);
            
        }
    }


    public function getFieldList(){

        $fields = array();

        foreach ($this->_fields as $key => $field){
            if (!$field['customValue'] && !$field['startEmpty']){
                $fields[] = $key;
            }
        }
        return $fields;
    }


    public function getPosition($pPk){

        /*$obj = \Core\Object::getClass($this->object);
        $primaryKey = $obj->normalizePrimaryKey($pPk);

        $condition = $this->getCondition();

        if ($customCondition = $this->getCustomListingCondition())
            $condition = $condition ? array_merge($condition, $customCondition) : $customCondition;

        $options['permissionCheck'] = $this->permissionCheck;
        */
        $obj = \Core\Object::getClass($this->object);
        $primaryKey = $obj->normalizePrimaryKey($pPk);
        $items = $this->getItems();

        $position = 0;

        if (count($primaryKey) == 1){
            $singlePrimaryKey = key($primaryKey);
            $singlePrimaryValue = current($primaryKey);
        }

        foreach ($items as $item){

            if ($singlePrimaryKey){
                if ($item[$singlePrimaryKey] == $singlePrimaryValue) break;
            } else {
                $isItem = true;
                foreach ($primaryKey as $prim => $val){
                    if ($item[$prim] != $val) $isItem = false;
                }
                if ($isItem) break;
            }

            $position++;
        }

        return $position;

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
     * @param int $pLimit
     * @param int $pOffset
     * @return array
     */
    public function getItems($pLimit = null, $pOffset = null) {

        $options   = array();
        $options['permissionCheck'] = $this->getPermissionCheck();
        $options['offset'] = $pOffset;
        $options['limit'] = $pLimit ? $pLimit : $this->itemsPerPage;

        $obj = \Core\Object::getClass($this->object);

        $condition = $this->getCondition();

        if ($extraCondition = $this->getCustomListingCondition())
            $condition = !$condition ? $extraCondition : array($condition, 'AND', $extraCondition);

        $options['order'] = $this->getOrder();

        $options['fields'] = array_keys($this->getColumns());

        $maxItems = \Core\Object::getCount($this->object, $condition, $options);

        if ($maxItems > 0)
            $maxPages = ceil($maxItems / $this->itemsPerPage);
        else
            $maxPages = 0;

        if ($this->getMultiLanguage()){
            //add language condition
            $langCondition = array(
                array('lang', '=', getArgv('lang')+""),
                'OR',
                array('lang', 'IS NULL'),
            );
            if ($condition)
                $condition = array($condition, 'AND', $langCondition);
            else
                $condition = $langCondition;
        }

        $items = \Core\Object::getList($this->object, $condition, $options);

        foreach ($items as &$item){
            $item = $this->prepareRow($item);
        }

        return array(
            'items' => $items,
            'count' => $maxItems,
            'pages' => $maxPages
        );
    }

    /**
     * Here you can define additional conditions for all operations (edit/listing).
     *
     * See phpDoc of global function dbConditionToSql for more details of the array structure of the result.
     * 
     * @return array condition definition
     */
    public function getCondition(){

    }


    /**
     * Here you can define additional conditions for edit operations.
     *
     * See phpDoc of global function dbConditionToSql for more details.
     * 
     * @return array condition definition
     */
    public function getCustomEditCondition(){

    }

    /**
     * Here you can define additional conditions for listing operations.
     *
     * See phpDoc of global function dbConditionToSql for more details.
     * 
     * @return array condition definition
     */
    public function getCustomListingCondition(){

    }

    /**
     * Returns a single item.
     *
     * $pPk is an array with the primary key values.
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
     * @param  array $pPk
     * @return array
     */
    public function getItem($pPk) {

        $this->primaryKey = $pPk;

        $options['fields'] = $this->getFieldList();
        $options['permissionCheck'] = $this->getPermissionCheck();

        $item = \Core\Object::get($this->object, $pPk, $options);

        //add custom values
        foreach ($this->_fields as $key => $field){

            if ($field['customValue'] && method_exists($this, $method = $field['customValue'])){
                $item[$key] = $this->$method($field, $key);
            }

        }

        //check against additionaly our own custom condition
        if ($item && $condition = $this->getCondition())
            if (!\Core\Object::satisfy($item, $condition)) $item = null;

        return array(
            'values' => $item
        );
    }

    /**
     * Adds a new item.
     *
     *
     *
     * @return mixed False if some went wrong or a array with the new primary keys.
     */
    public function add(){

        //collect values
        $data = $this->collectData();

        //todo
        $branchId = null;
        $treePos  = null;
        $scopeId  = null;

        //do normal add through Core\Object
        $this->primaryKey = \Core\Object::add($this->getObject(), $data,
            $branchId,
            $treePos,
            $scopeId,
            array('permissionCheck' => $this->getPermissionCheck())
        );

        //handle customSaves
        foreach ($this->_fields as $key => $field){
            if ($field['customPostSave'] && method_exists($this, $method = $field['customPostSave']))
                $this->$method($field, $key);
        }

        return $this->primaryKey;
    }



    public function remove($pPk){
        $this->primaryKey = $pPk;
        $options['permissionCheck'] = $this->getPermissionCheck();
        return \Core\Object::remove($this->object, $pPk, $options);
    }


    public function update($pPk){

        $this->primaryKey = $pPk;

        $options['fields'] = $this->getFieldList();
        $options['permissionCheck'] = $this->getPermissionCheck();

        $item = \Core\Object::get($this->object, $pPk, $options);

        //check against additionaly our own custom condition
        if ($item && $condition = $this->getCondition())
            if (!\Core\Object::satisfy($item, $condition)) $item = false;

        if (!$item) throw new \ObjectItemNotFoundException(tf('Can not find the object item with primaryKey %s', print_r($pPk, true)));

        //collect values
        $data = $this->collectData($pPk);

        //do normal update through Core\Object
        $result = \Core\Object::update($this->getObject(), $pPk, $data, array('permissionCheck' => $this->getPermissionCheck()));

        //handle customSaves
        foreach ($this->_fields as $key => $field){
            if ($field['customSave']){
                if (method_exists($this, $field['customSave']))
                    call_user_method($field['customSave'], $this);
            }
        }

        return $result;
    }

    /**
     * Collects all data from GET/POST that has to be saved.
     * Iterates only through all defined fields in $fields.
     *
     * @param mixed $pPk primary key if this has been called for updating, and empty for creating a new record.
     * @return array
     * @throws \FieldCanNotBeEmptyException
     */
    public function collectData($pPk = null){

        $data = array();

        foreach ($this->_fields as $key => $field){
            if ($field['noSave']) continue;

            $data[$key] = $_POST[$key]?:$_GET[$key];

            if ($field['customValue'] && method_exists($this, $method = $field['customValue'])){
                $data[$key] = $this->$method($field, $key);
            }

            if ($field['customSave'] && method_exists($this, $method = $field['customValue'])){
                $this->$method($field, $key);
                continue;
            }

            if (($field['saveOnlyFilled'] || $field['saveOnlyIsFilled']) && ($data[$key] === '' || $data[$key] === null))
                unset($data[$key]);

            if ($field['required'] && ($data[$key] === '' || $data[$key] === null) )
                throw new \FieldCanNotBeEmptyException(tf('The field %s is required.', $key));

        }

        return $data;
    }


    /**
     * Each item goes through this function in getItems(). Defines whether a item is editable or deleteable.
     * You can attach here extra action icons, too.
     *
     * Result should be:
     *
     * array(
     *     'values' => $pItem,
     *     'edit' => bool (can be edited),
     *     'remove' => bool (can be removed),
     *     'actions' => array(
     *         array('/* action * /') //todo
     *     )
     * )
     *
     * @param array $pItem
     *
     * @return array
     */
    function prepareRow($pItem) {

        $visible = true;
        $editable = $this->edit; //todo get from ACL
        $deleteable = $this->remove; //todo, get from acl

        $res = null;
        if ($visible) {
            $res = array();
            $res['values'] = $pItem;
            $res['edit'] = $editable;
            $res['remove'] = $deleteable;
        }
        return $res;
    }

    /**
     * @return array
     */
    public function getPrimaryKey(){
        return $this->primaryKey;
    }


    /**
     * @return bool
     */
    public function getPermissionCheck(){
        return $this->permissionCheck;
    }

    /**
     * @param boolean $add
     */
    public function setAdd($add){
        $this->add = $add;
    }

    /**
     * @return boolean
     */
    public function getAdd(){
        return $this->add;
    }

    /**
     * @param string $addEntrypoint
     */
    public function setAddEntrypoint($addEntrypoint){
        $this->addEntrypoint = $addEntrypoint;
    }

    /**
     * @return string
     */
    public function getAddEntrypoint(){
        return $this->addEntrypoint;
    }

    /**
     * @param string $addIcon
     */
    public function setAddIcon($addIcon){
        $this->addIcon = $addIcon;
    }

    /**
     * @return string
     */
    public function getAddIcon(){
        return $this->addIcon;
    }

    /**
     * @param string $customOrderBy
     */
    public function setCustomOrderBy($customOrderBy){
        $this->customOrderBy = $customOrderBy;
    }

    /**
     * @return string
     */
    public function getCustomOrderBy(){
        return $this->customOrderBy;
    }

    /**
     * @param string $customOrderByDirection
     */
    public function setCustomOrderByDirection($customOrderByDirection){
        $this->customOrderByDirection = $customOrderByDirection;
    }

    /**
     * @return string
     */
    public function getCustomOrderByDirection(){
        return $this->customOrderByDirection;
    }

    /**
     * @param boolean $domainDepended
     */
    public function setDomainDepended($domainDepended){
        $this->domainDepended = $domainDepended;
    }

    /**
     * @return boolean
     */
    public function getDomainDepended(){
        return $this->domainDepended;
    }

    /**
     * @param boolean $edit
     */
    public function setEdit($edit){
        $this->edit = $edit;
    }

    /**
     * @return boolean
     */
    public function getEdit(){
        return $this->edit;
    }

    /**
     * @param string $editEntrypoint
     */
    public function setEditEntrypoint($editEntrypoint){
        $this->editEntrypoint = $editEntrypoint;
    }

    /**
     * @return string
     */
    public function getEditEntrypoint(){
        return $this->editEntrypoint;
    }

    /**
     * @param string $editIcon
     */
    public function setEditIcon($editIcon){
        $this->editIcon = $editIcon;
    }

    /**
     * @return string
     */
    public function getEditIcon(){
        return $this->editIcon;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields){
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function getFields(){
        return $this->fields;
    }

    /**
     * @param array $filter
     */
    public function setFilter($filter){
        $this->filter = $filter;
    }

    /**
     * @param array $columns
     */
    public function setColumns($columns){
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getColumns(){
        return $this->columns;
    }


    /**
     * @return array
     */
    public function getFilter(){
        return $this->filter;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage){
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @return int
     */
    public function getItemsPerPage(){
        return $this->itemsPerPage;
    }

    /**
     * @param boolean $multiLanguage
     */
    public function setMultiLanguage($multiLanguage){
        $this->multiLanguage = $multiLanguage;
    }

    /**
     * @return boolean
     */
    public function getMultiLanguage(){
        return $this->multiLanguage;
    }

    /**
     * @param string $object
     */
    public function setObject($object){
        $this->object = $object;
    }

    /**
     * @return string
     */
    public function getObject(){
        return $this->object;
    }

    /**
     * @param array $objectDefinition
     */
    public function setObjectDefinition($objectDefinition){
        $this->objectDefinition = $objectDefinition;
    }

    /**
     * @return array
     */
    public function getObjectDefinition(){
        return $this->objectDefinition;
    }

    /**
     * @param array $order
     */
    public function setOrder($order){
        $this->order = $order;
    }

    /**
     * @return array
     */
    public function getOrder(){
        return $this->order;
    }

    /**
     * @param boolean $remove
     */
    public function setRemove($remove){
        $this->remove = $remove;
    }

    /**
     * @return boolean
     */
    public function getRemove(){
        return $this->remove;
    }

    /**
     * @param string $removeIcon
     */
    public function setRemoveIcon($removeIcon){
        $this->removeIcon = $removeIcon;
    }

    /**
     * @return string
     */
    public function getRemoveIcon(){
        return $this->removeIcon;
    }

    /**
     * @param string $removeIconItem
     */
    public function setRemoveIconItem($removeIconItem){
        $this->removeIconItem = $removeIconItem;
    }

    /**
     * @return string
     */
    public function getRemoveIconItem(){
        return $this->removeIconItem;
    }

    /**
     * @param string $table
     */
    public function setTable($table){
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getTable(){
        return $this->table;
    }

    /**
     * @param boolean $workspace
     */
    public function setWorkspace($workspace){
        $this->workspace = $workspace;
    }

    /**
     * @return boolean
     */
    public function getWorkspace(){
        return $this->workspace;
    }

    public function setFilterFields($filterFields){
        $this->filterFields = $filterFields;
    }

    public function getFilterFields(){
        return $this->filterFields;
    }

    public function setItemLayout($itemLayout){
        $this->itemLayout = $itemLayout;
    }

    public function getItemLayout(){
        return $this->itemLayout;
    }

    /**
     * @param array $primary
     */
    public function setPrimary($primary){
        $this->primary = $primary;
    }

    /**
     * @return array
     */
    public function getPrimary(){
        return $this->primary;
    }

    /**
     * @param array|null $entryPoint
     */
    public function setEntryPoint($entryPoint){
        $this->entryPoint = $entryPoint;
    }

    /**
     * @return array|null
     */
    public function getEntryPoint(){
        return $this->entryPoint;
    }

    /**
     * @param boolean $asNested
     */
    public function setAsNested($asNested){
        $this->asNested = $asNested;
    }

    /**
     * @return boolean
     */
    public function getAsNested(){
        return $this->asNested;
    }

    public function setNestedMove($nestedMove){
        $this->nestedMove = $nestedMove;
    }

    public function getNestedMove(){
        return $this->nestedMove;
    }

    public function setNestedRootAdd($nestedRootAdd){
        $this->nestedRootAdd = $nestedRootAdd;
    }

    public function getNestedRootAdd(){
        return $this->nestedRootAdd;
    }

    public function setNestedRootAddEntrypoint($nestedRootAddEntrypoint){
        $this->nestedRootAddEntrypoint = $nestedRootAddEntrypoint;
    }

    public function getNestedRootAddEntrypoint(){
        return $this->nestedRootAddEntrypoint;
    }

    public function setNestedRootAddIcon($nestedRootAddIcon){
        $this->nestedRootAddIcon = $nestedRootAddIcon;
    }

    public function getNestedRootAddIcon(){
        return $this->nestedRootAddIcon;
    }

    public function setNestedRootAddLabel($nestedRootAddLabel){
        $this->nestedRootAddLabel = $nestedRootAddLabel;
    }

    public function getNestedRootAddLabel(){
        return $this->nestedRootAddLabel;
    }

    public function setNestedRootEdit($nestedRootEdit){
        $this->nestedRootEdit = $nestedRootEdit;
    }

    public function getNestedRootEdit(){
        return $this->nestedRootEdit;
    }

    public function setNestedRootEditEntrypoint($nestedRootEditEntrypoint){
        $this->nestedRootEditEntrypoint = $nestedRootEditEntrypoint;
    }

    public function getNestedRootEditEntrypoint(){
        return $this->nestedRootEditEntrypoint;
    }

    public function setNestedRootRemove($nestedRootRemove){
        $this->nestedRootRemove = $nestedRootRemove;
    }

    public function getNestedRootRemove(){
        return $this->nestedRootRemove;
    }

    public function setNestedRootRemoveEntrypoint($nestedRootRemoveEntrypoint){
        $this->nestedRootRemoveEntrypoint = $nestedRootRemoveEntrypoint;
    }

    public function getNestedRootRemoveEntrypoint(){
        return $this->nestedRootRemoveEntrypoint;
    }

    public function setNestedMoveable($nestedMoveable){
        $this->nestedMoveable = $nestedMoveable;
    }

    public function getNestedMoveable(){
        return $this->nestedMoveable;
    }

    public function setNestedAddWithPositionSelection($nestedAddWithPositionSelection){
        $this->nestedAddWithPositionSelection = $nestedAddWithPositionSelection;
    }

    public function getNestedAddWithPositionSelection(){
        return $this->nestedAddWithPositionSelection;
    }

    public function setAddLabel($addLabel){
        $this->addLabel = $addLabel;
    }

    public function getAddLabel(){
        return $this->addLabel;
    }

    public function setRemoveEntrypoint($removeEntrypoint){
        $this->removeEntrypoint = $removeEntrypoint;
    }

    public function getRemoveEntrypoint(){
        return $this->removeEntrypoint;
    }

    public function setAddMultiple($addMultiple){
        $this->addMultiple = $addMultiple;
    }

    public function getAddMultiple(){
        return $this->addMultiple;
    }

    public function setAddMultipleFieldContainerWidth($addMultipleFieldContainerWidth){
        $this->addMultipleFieldContainerWidth = $addMultipleFieldContainerWidth;
    }

    public function getAddMultipleFieldContainerWidth(){
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

    public function setAddMultipleFixedFields($addMultipleFixedFields){
        $this->addMultipleFixedFields = $addMultipleFixedFields;
    }

    public function getAddMultipleFixedFields(){
        return $this->addMultipleFixedFields;
    }
    
    
    

}
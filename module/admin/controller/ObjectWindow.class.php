<?php

namespace Admin;

use \Core\Kryn;

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
     * Defines the fields of your table which should be displayed.
     *
     * @abstract
     * @var array
     */
    public $fields = array();

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
    public $editIcon = '#icon-pencil';

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
    public $removeIconItem = '#icon-minus';


    /**
     * The system opens this entrypoint when clicking on the add newt_button(left, top, text)n.
     * Default is <current>/add.
     * 
     * Relative or absolute paths are allowed.
      *
     * @var string
     */
    public $addEntrypoint = 'add';

    /**
     * The system opens this entrypoint when clicking on the edit button.
     * Default is <current>/edit.
     *
     * Relative or absolute paths are allowed.
     *
     * @var string
     */
    public $editEntrypoint = 'edit';


    /**
     * Defines whether the add button should be displayed
     *
     * @var boolean
     */
    public $add = false;
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
     * Constructor
     */
    public function __construct($pEntryPoint = null) {

        $this->entryPoint = $pEntryPoint;

        $this->objectDefinition = Kryn::$objects[$this->object];
        if (!$this->objectDefinition){
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

        //resolv shortcuts
        $this->prepareFieldDefinition($this->fields);

        //do magic with type select and add all fields to _fields.
        $this->prepareFieldItem($this->fields);

        if (is_string($this->primary)){
            $this->primary = explode(',', str_replace(' ', '', $this->primary));
        }

        if (!$this->orderBy && count($this->order) == 0){
            foreach ($this->fields as $colId => $col){
                $this->orderBy = $colId;
                break;
            }
        }

        $this->orderByDirection = (strtolower($this->orderByDirection) == 'asc') ? 'asc' : 'desc';

        if (getArgv('orderBy') != '')
            $this->customOrderBy = getArgv('orderBy', 2);

        if (getArgv('orderByDirection') != '')
            $this->customOrderByDirection = (strtolower(getArgv('orderByDirection')) == 'asc') ? 'asc' : 'desc';

        if (!$this->order && $this->orderBy){
            $this->order = array($this->orderBy => $this->orderByDirection);
        }

        if ($this->customOrderBy){
            $this->order = array($this->customOrderBy => $this->customOrderByDirection);
        }

        if (getArgv('order')){
            $this->order = getArgv('order');
        }

        $this->filterFields = array();

        ObjectWindowController::translateFields($this->fields);

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

        foreach ($pFields as $key => &$field)
            if ($field['depends']) $this->prepareFieldDefinition($field['depends']);

        foreach ($pFields as $key => &$field)
            if ($field['children']) $this->prepareFieldDefinition($field['children']);

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
            if ($pFields['needAccess'] && !Kryn::checkUrlAccess($pFields['needAccess'])) {
                $pFields = null;
                return;
            }


            if(substr($pKey,0,2) != '__' && substr($pKey, -2) != '__'){

                $this->_fields[$pKey] = $pFields;

                switch ($pFields['type']) {
                    case 'predefined':

                        $def = \Core\Kryn::$objects[$pFields['object']]['fields'][$pFields['field']];
                        if ($def){
                            foreach ($def as $k => $v){
                                $pFields[$k] = $v;
                            }

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


    function getFieldList(){

        $fields = array();

        foreach ($this->_fields as $key => $field){
            if (!$field['customValue'] && !$field['startEmpty']){
                $fields[] = $key;
            }
        }
        return $fields;
    }


    public function getPosition($pPk){

        $obj = \Core\Object::getClass($this->object);
        $primaryKey = $obj->normalizePrimaryKey($pPk);

        $condition = $this->getCondition();

        if ($customCondition = $this->getCustomListingCondition())
            $condition = $condition ? array_merge($condition, $customCondition) : $customCondition;

        $options['permissionCheck'] = $this->permissionCheck;
        $items = $obj->getItems(null, $options);

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
     * Returns items with some informations.
     * 
     *   array(
     *       'items' => $items,
     *       'maxPages' => $maxPages,
     *       'maxItems' => $maxItems
     *   );
     *   
     * @param string $pPage
     * @return array
     */
    public function getItems($pPage) {

        $pPage = $pPage?$pPage:1;

        $options   = array();
        $options['offset'] = ($pPage * $this->itemsPerPage) - $this->itemsPerPage;
        $options['limit'] = $this->itemsPerPage;

        $obj = \Core\Object::getClass($this->object);

        $condition = $this->getCondition();

        if ($customCondition = $this->getCustomListingCondition())
            $condition = $condition + $condition;

        $options['fields'] = $this->getFieldList();

        $maxItems = $obj->getCount($condition, $options);

        if ($maxItems > 0)
            $maxPages = ceil($maxItems / $this->itemsPerPage);
        else
            $maxPages = 0;

        $items = $obj->getItems($condition, $options);

        foreach ($items as &$item){
            $item = $this->prepareRow($item);
        }

        return array(
            'items' => $items,
            'maxPages' => $maxPages,
            'maxItems' => $maxItems
        );
    }

    /**
     * Returns the condition object. Based on getAclCondition and getCustomCondition().
     * 
     * @return array condition definition
     */
    public function getCondition(){

        $result = array();

        if ($this->permissionCheck && $condition = \Core\Acl::getListingCondition($this->ormClass->object))
            $result = $condition;

        if ($condition = $this->getCustomCondition())
            $result = $result + $condition;

        return $result;
    }

    /**
     * Here you can define additional conditions for all operations (edit/listing).
     *
     * See phpDoc of global function dbConditionToSql for more details.
     * 
     * @return array condition definition
     */
    public function getCustomCondition(){

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

        $obj = \Core\Object::getClass($this->object);
        $this->primaryKey = $obj->normalizePrimaryKey($pPk);

        $condition = $this->getCondition();

        if ($customCondition = $this->getCustomListingCondition())
            $condition = $condition ? array_merge($condition, $customCondition) : $customCondition;

        $options['fields'] = $this->getFieldList();
        $item = $obj->getItem($this->primaryKey, $options);

        if (!\Core\Object::satisfy($item, $condition)) return false;

        return array(
            'values' => $item
        );
    }

    public function addItem(){

        $obj = \Core\Object::getClass($this->object);

        //collect values
        $data = $this->collectData();

        //todo
        $branchId = null;
        $treePos  = null;
        $scopeId  = null;

        //do normal add through Core\Object
        $result = \Core\Object::add($this->getObject(), $data,
            $branchId,
            $treePos,
            $scopeId,
            array('permissionCheck' => $this->getPermissionCheck())
        );

        //handle customSaves
        foreach ($this->_fields as $key => $field){
            if ($field['customSave']){
                if (method_exists($this, $field['customSave']))
                    call_user_method($field['customSave'], $this);
            }
        }

        return $result;
    }

    public function saveItem($pPk){

        $this->primaryKey = $pPk;
        $obj = \Core\Object::getClass($this->object);

        //collect values
        $data = $this->collectData();

        //this is for the conditions/limitations from getCustomCondition/getCustomEditCondition
        $condition  = dbPrimaryKeyToCondition($pPk);
        $condition += $this->getCondition();
        
        if ($customCondition = $this->getCustomEditCondition())
            $condition = $condition + $customCondition;

        $item = $obj->getItem($condition);
        if (!$item) throw new \ObjectItemNotFoundException(tf('Can not find the object item with primaryKey %s', print_r($pPk, true)));

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

    public function collectData(){

        $data = array();

        foreach ($this->_fields as $key => $field){
            if ($field['noSave']) continue;

            if ($field['customValue'] && method_exists($this, $field['customValue'])){
                $data[$key] = call_user_method($field['customValue'], $this);
            } else if (!$field['customSave']){
                $data[$key] = $_POST[$key]?:$_GET[$key];
            }

            if (($field['saveOnlyFilled'] || $field['saveOnlyFilled']) && ($data[$field] === '' || $data[$field] === null))
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
        $editable = $this->edit;
        $deleteable = $this->remove;

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
    public function setEntryPoint($entryPoint)
    {
        $this->entryPoint = $entryPoint;
    }

    /**
     * @return array|null
     */
    public function getEntryPoint()
    {
        return $this->entryPoint;
    }


}
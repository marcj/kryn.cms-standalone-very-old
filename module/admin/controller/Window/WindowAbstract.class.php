<?php

namespace Admin\Window;

use \Core\Kryn;

abstract class WindowAbstract {


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
    private $objectDefinition = array();

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
     * @deprecated Use $order instead
     * @var string
     */
    public $orderBy = '';

    /**
     * Order field
     *
     * @private
     * @var string
     */
    private $customOrderBy = '';

    /**
     * Order direction
     *
     * @deprecated Use $order instead
     * @var string
     */
    public $orderByDirection = 'ASC';

    /**
     * Order direction
     *
     * @private
     * @var string
     */
    private $customOrderByDirection = 'ASC';

    /**
     * Default order
     *
     * $order = array(
     *      array('field' => 'group_id', 'direction' => 'asc'),
     *      array('field' => 'title', 'direction' => 'asc')
     * );
     *
     * @var array
     */
    public $order = array();


    /**
     * Defines the icon for the add button. Relative to media/ or #className for vector images
     *
     * @var string name of image
     */
    public $addIcon = '#icon-plus-4';

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
    public $removeIcon = '#icon-minus-4';

    /**
     * Defines the icon for the remove/delete button. Relative to media/ or #className for vector images
     *
     * @var string name of image
     */
    public $removeIconItem = '#icon-minus';


    /**
     * The system opens this entrypoint when clicking on the add button
     * Default is <current>/add
     * @var string
     */
    public $addEntrypoint = '';

    /**
     * The system opens this entrypoint when clicking on the edit button
     * Default is <current>/edit
     *
     * @var string
     */
    public $editEntrypoint = '';


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
     * Constructor
     */
    function __construct() {


        if ($this->object){
            $this->objectDefinition = Kryn::$objects[$this->object];
            if (!$this->objectDefinition){
                throw new Exception("Can not find object '".$this->object."'");
            }
            $this->table = $this->objectDefinition['table'];
            foreach ($this->objectDefinition['fields'] as $key => &$field){
                if($field['primaryKey']){
                    $this->primary[] = $key;
                }
            }

            if ($this->fields){
                $this->prepareFieldDefinition($this->fields);
            }
        }

        if (is_string($this->primary)){
            $this->primary = explode(',', str_replace(' ', '', $this->primary));
        }

        if (!$this->orderBy && count($this->order) == 0){
            foreach ($this->fields as $colId => $col){
                $this->orderBy = $colId;
                break;
            }
        }

        $this->orderByDirection = (strtolower($this->orderByDirection) == 'asc') ? 'ASC' : 'DESC';

        if (getArgv('orderBy') != '')
            $this->customOrderBy = getArgv('orderBy', 2);

        if (getArgv('orderByDirection') != '')
            $this->customOrderByDirection = (strtolower(getArgv('orderByDirection')) == 'asc') ? 'ASC' : 'DESC';

        if (!$this->order && $this->orderBy){
            $this->order = array($this->orderBy => $this->orderByDirection);
        }

        if ($this->customOrderBy){
            $this->order = array($this->customOrderBy => $this->customOrderByDirection);
        }

        if (getArgv('order')){
            $this->order = getArgv('order');
        }

        $this->_fields = array();
        $this->filterFields = array();

        Controller::translateFields($this->fields);

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


                $this->prepareFieldItem($field);
                $this->filterFields[$fieldKey] = $field;
            }

            $this->prepareFieldItem($this->fields);
        }

    }

    /**
     * prepares $pFields. Replace array items which are only a key (with no array definition) with
     * the array definition of the proper field from the object fields.
     *
     * @param $pFields
     */
    private function prepareFieldDefinition(&$pFields){

        $i = 0;
        foreach ($pFields as $key => $field){
            if (is_numeric($key)){

                $newItem = $this->objectDefinition['fields'][$field];
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
            if ($field['depends']) $this->prepareFieldDefinition($field['depends']);
        }

    }

    /**
     * Prepare fields. Loading tableItems by select and file fields.
     *
     * @param array $pFields
     * @param bool  $pKey
     */
    private function prepareFieldItem(&$pFields, $pKey = false) {
        if (is_array($pFields) && $pFields['type'] == '') {
            foreach ($pFields as $key => &$field) {
                if ($field['type'] != '' && is_array($field)) {
                    $this->prepareFieldItem($field, $key);
                }
            }
        } else {
            if ($pFields['needAccess'] && !Kryn::checkUrlAccess($pFields['needAccess'])) {
                $pFields = null;
                return;
            }

            if(substr($pKey,0,2) != '__' && substr($pKey, -2) != '__'){

                $this->_fields[$pKey] = $pFields;

                switch ($pFields['type']) {
                    case 'select':

                        if (!empty($field['eval']))
                            $pFields['tableItems'] = eval($field['eval']);
                        elseif ($pFields['relation'] == 'n-n')
                            $pFields['tableItems'] = dbTableFetch($pFields['n-n']['right'], DB_FETCH_ALL);
                        else if ($pFields['table'])
                            $pFields['tableItems'] = dbTableFetch($pFields['table'], DB_FETCH_ALL);
                        else if ($pFields['sql'])
                            $pFields['tableItems'] = dbExFetch($pFields['sql'], DB_FETCH_ALL);
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
                    case 'files':

                        $files = Kryn::readFolder($pFields['directory'], $pFields['withExtension']);
                        if (count($files) > 0) {
                            foreach ($files as $file) {
                                $pFields['tableItems'][] = array('id' => $file, 'label' => $file);
                            }
                        } else {
                            $pFields['tableItems'] = array();
                        }
                        $pFields['table_key'] = 'id';
                        $pFields['table_label'] = 'label';
                        $pFields['type'] = 'select';

                        break;
                }
            }

            if (is_array($pFields['depends'])) {
                $this->prepareFieldItem($pFields['depends']);
            }
        }
    }
    

}
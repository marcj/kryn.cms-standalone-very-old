<?php

namespace Core\Config;

class Field extends Model
{
    protected $attributes = ['id', 'type', 'primaryKey', 'autoIncrement'];

    /**
     * @var string
     */
    protected $id;

    /**
     * The label.
     *
     * @var string
     */
    protected $label;

    /**
     * Shows a grayed description text. __Warning__: This value is set as HTML. So escape `<` and `>`.
     *
     * @var string
     */
    protected $desc;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $object;

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var mixed
     */
    protected $needValue;

    /**
     * @var string
     */
    protected $againstField;

    /**
     * The default/initial value.
     *
     * @var mixed
     */
    protected $default = null;

    /**
     * If this field starts with a empty value (on initialisation).
     *
     * @var bool
     */
    protected $startEmpty = false;

    /**
     * If this field returns the value even though it's the `default` value (in a form).
     *
     * @var bool
     */
    protected $returnDefault = false;

    /**
     * Defines if this field needs a valid value.
     *
     * @var bool
     */
    protected $required = false;

    /**
     * If this field injects a `tr`+2x`td` instead of `div`.
     *
     * @var bool
     */
    protected $tableItem = false;

    /**
     * If this fields is disabled or not.
     *
     * @var bool
     */
    protected $disabled = false;

    /**
     * If this fields contains a default wrapper div with title, description etc or only the input itself.
     *
     * @var bool
     */
    protected $noWrapper = false;

    /**
     * Shows a little help icon and points to the given help id.
     *
     * @var string
     */
    protected $help;

    /**
     * @var string
     */
    protected $objectRelation;

    /**
     * @var bool
     */
    protected $primaryKey = false;

    /**
     * @var bool
     */
    protected $autoIncrement = false;

    /**
     * @var array
     */
    protected $children;

    /**
     * @var Options
     */
    protected $options;

    /**
     * @param array  $values
     * @param string $key
     */
    public function fromArray($values, $key = null)
    {
        parent::fromArray($values, $key);
        if (is_string($key)) {
            $this->setId($key);
        }
    }

    /**
     * @param Options $options
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
    }

    /**
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getOption($key)
    {
        return $this->options ? $this->options->getOption($key) : null;
    }


    /**
     * @param Field[] $children
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
    }

    /**
     * @return Field[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getChildrenArray(){
        if (null !== $this->children) {
            $children = [];
            foreach ($this->children as $child) {
                $children[$child->getId()] = $child->toArray();
            }
            return $children;
        }
    }

    /**
     * @return bool
     */
    public function isPrimaryKey()
    {
        return true === $this->primaryKey;
    }

    /**
     * @return bool
     */
    public function isAutoIncrement()
    {
        return true === $this->autoIncrement;
    }

    /**
     * @param boolean $autoIncrement
     */
    public function setAutoIncrement($autoIncrement)
    {
        $this->autoIncrement = filter_var($autoIncrement, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return boolean
     */
    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    /**
     * @param string $desc
     */
    public function setDesc($desc)
    {
        $this->desc = $desc;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->desc;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param boolean $primaryKey
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = filter_var($primaryKey, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return boolean
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * @param string $objectRelation
     */
    public function setObjectRelation($objectRelation)
    {
        $this->objectRelation = $objectRelation;
    }

    /**
     * @return string
     */
    public function getObjectRelation()
    {
        return $this->objectRelation;
    }

    /**
     * @param string $layout
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $tableItem
     */
    public function setTableItem($tableItem)
    {
        $this->tableItem = $tableItem;
    }

    /**
     * @return mixed
     */
    public function getTableItem()
    {
        return $this->tableItem;
    }

    /**
     * @param boolean $startEmpty
     */
    public function setStartEmpty($startEmpty)
    {
        $this->startEmpty = $startEmpty;
    }

    /**
     * @return boolean
     */
    public function getStartEmpty()
    {
        return $this->startEmpty;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $returnDefault
     */
    public function setReturnDefault($returnDefault)
    {
        $this->returnDefault = $returnDefault;
    }

    /**
     * @return boolean
     */
    public function getReturnDefault()
    {
        return $this->returnDefault;
    }

    /**
     * @param boolean $noWrapper
     */
    public function setNoWrapper($noWrapper)
    {
        $this->noWrapper = $noWrapper;
    }

    /**
     * @return boolean
     */
    public function getNoWrapper()
    {
        return $this->noWrapper;
    }

    /**
     * @param string $help
     */
    public function setHelp($help)
    {
        $this->help = $help;
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param boolean $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }

    /**
     * @return boolean
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param mixed $needValue
     */
    public function setNeedValue($needValue)
    {
        $this->needValue = $needValue;
    }

    /**
     * @return mixed
     */
    public function getNeedValue()
    {
        return $this->needValue;
    }

    /**
     * @param string $againstField
     */
    public function setAgainstField($againstField)
    {
        $this->againstField = $againstField;
    }

    /**
     * @return string
     */
    public function getAgainstField()
    {
        return $this->againstField;
    }

}
<?php

namespace Core\Config;

class Field extends Model
{
    protected $attributes = ['id', 'primaryKey', 'autoIncrement'];

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
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
     * @var bool
     */
    protected $tableItem = false;

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
        return $this->options->getOption($key);
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


}
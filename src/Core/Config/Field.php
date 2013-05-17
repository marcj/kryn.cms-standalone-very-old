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
     * @param array $children
     */
    public function setChildren(array $children)
    {
        $this->children = array();
        foreach ($children as $key => $field) {
            if ($field instanceof Field) {
                $this->children[] = $field;
            } else if (is_array($field)) {
                $instance = new Field();
                $instance->fromArray($field);
                if (null === $instance->getId()) {
                    $instance->setId($key);
                }
                $this->children[] = $instance;
            }
        }
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        if (null === $this->children) {
            $childrenElement = $this->getDirectChild('children');
            if ($childrenElement) {
                foreach ($childrenElement->childNodes as $field) {
                    $this->children[] = $this->getModelInstance($field);
                }
            }
        }

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

}
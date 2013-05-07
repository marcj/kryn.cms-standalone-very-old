<?php

namespace Core\Config;

class Field extends Model
{

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
     * @var bool
     */
    protected $primaryKey = false;

    /**
     * @var array
     */
    protected $children;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var bool
     */
    protected $autoIncrement = false;

    public function setupObject()
    {
        $this->setAttributeVar('id');
        $this->setVar('label');
        $this->setVar('type');
        $this->setVar('desc');
        $this->setAttributeVar('primaryKey', 'boolean');
        $this->setAttributeVar('autoIncrement', 'boolean');

        foreach ($this->element->childNodes as $child) {
            $name = $child->nodeName;
            if ('#text' !== $name && 'children' !== $name && !$this->$name) {
                $this->options[$name] = $child->nodeValue;
            }
        }
    }

    /**
     * @param $method
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $operation = substr($method, 0, 3);
        $varName = lcfirst(substr($method, 3));
        if (!$this->$varName){
            if ('get' === $operation) {
                return $this->options[$varName];
            } else if ('set' === $operation) {
                $this->options[$varName] = $arguments[0];
            }
        }
    }

    /**
     * @param null $element
     *
     * @return array
     */
    public function toArray($element = null)
    {
        $result = parent::toArray($element);
        foreach ($this->options as $key => $option) {
            $result[$key] = $option;
        }
        return $result;
    }

    /**
     * @param array $values
     */
    public function fromArray(array $values)
    {
        parent::fromArray($values);
        $this->options = array();
        foreach ($values as $key => $value) {
            if (!$this->$key) {
                $this->options[$key] = $value;
            }
        }
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
            } else if(is_array($field)) {
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

}
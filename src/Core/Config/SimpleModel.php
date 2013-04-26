<?php

namespace Core\Config;

class SimpleModel extends Model {

    /**
     * @var string
     */
    protected $idKey = 'id';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $value;

    public function setupObject(){
        $this->id = $this->element->attributes->getNamedItem($this->idKey)->nodeValue;
        $this->value = $this->element->nodeValue;
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
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
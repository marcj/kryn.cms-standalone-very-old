<?php

namespace Core\Config;

class Plugin extends Model
{
    protected $attributes = ['id'];

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var Route[]
     */
    protected $routes;

    /**
     * @var Field[]
     */
    protected $options;

    /**
     * @param Route[] $routes
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
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
     * @param Field[] $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return Field[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Field $option
     */
    public function addOption(Field $option)
    {
        $this->options[] = $option;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

}
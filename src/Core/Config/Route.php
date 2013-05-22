<?php

namespace Core\Config;

class Route extends Model
{
    protected $attributes = ['id', 'pattern'];

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var RouteDefault[]
     */
    protected $defaults;

    /**
     * @var RouteRequirement[]
     */
    protected $requirements;
//
//    public function setupObject()
//    {
//        parent::setupObject();
//
//        $defaults = $this->element->getElementsByTagName('default');
//        $this->defaults = array();
//        foreach ($defaults as $default) {
//            $this->defaults[] = new RouteDefault($default);
//        }
//
//        $requirements = $this->element->getElementsByTagName('requirement');
//        $this->requirements = array();
//        foreach ($requirements as $requirement) {
//            $this->requirements[] = new RouteRequirement($requirement);
//        }
//    }

    /**
     * @param RouteDefault[] $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @return RouteDefault[]
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    public function getArrayDefaults()
    {
        if (null !== $this->defaults) {
            $result = array();
            foreach ($this->defaults as $default) {
                $result[$default->getId()] = $default->getValue();
            }
            return $result;
        }
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
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param RouteRequirement[] $requirements
     */
    public function setRequirements(array $requirements)
    {
        $this->requirements = $requirements;
    }

    /**
     * @return RouteRequirement[]
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * @return RouteRequirement[]
     */
    public function getArrayRequirements()
    {
        if (null !== $this->requirements) {
            var_dump($this->requirements);
            $result = array();
            foreach ($this->requirements as $requirement) {
                $result[$requirement->getId()] = $requirement->getValue();
            }
            var_dump($result);
            return $result;
        }
    }

}
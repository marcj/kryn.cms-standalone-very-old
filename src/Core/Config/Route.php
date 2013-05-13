<?php

namespace Core\Config;

class Route extends Model
{
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

    public function setupObject()
    {
        $this->id = $this->element->attributes->getNamedItem('id')->nodeValue;
        $this->pattern = $this->element->attributes->getNamedItem('pattern')->nodeValue;
    }

    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
    }

    public function getDefaults()
    {
        if (null === $this->defaults) {
            $defaults = $this->element->getElementsByTagName('default');
            $this->defaults = array();
            foreach ($defaults as $default) {
                $this->defaults[] = new RouteDefault($default);
            }
        }

        return $this->defaults;
    }

    public function getArrayDefaults()
    {
        $this->defaults = $this->defaults ? : $this->getDefaults();
        $result = array();
        foreach ($this->defaults as $default) {
            $result[$default->getId()] = $default->getValue();
        }
        return $result;
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

    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;
    }

    public function getRequirements()
    {
        if (null === $this->requirements) {
            $requirements = $this->element->getElementsByTagName('requirement');
            $this->requirements = array();
            foreach ($requirements as $requirement) {
                $this->requirements[] = new RouteRequirement($requirement);
            }
        }

        return $this->requirements;
    }

    public function getArrayRequirements()
    {
        $this->requirements = $this->requirements ? : $this->getRequirements();
        $result = array();
        foreach ($this->requirements as $requirement) {
            $result[$requirement->getId()] = $requirement->getValue();
        }
        return $result;
    }


}
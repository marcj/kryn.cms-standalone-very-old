<?php

namespace Core\Config;

class Route extends Model
{
    protected $attributes = ['id', 'pattern'];

    protected $elementToArray = ['requirement' => 'requirements', 'default' => 'defaults'];



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

    /**
     * @param RouteDefault[] $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @param RouteDefault $default
     */
    public function addDefault(RouteDefault $default)
    {
        $this->defaults[] = $default;
    }

    /**
     * @param RouteRequirement $requirement
     */
    public function addRequirement(RouteRequirement $requirement)
    {
        $this->requirements[] = $requirement;
    }

    /**
     * @return RouteDefault[]
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param string $key
     * @return RouteDefault
     */
    public function getDefault($key)
    {
        if ($this->defaults) {
            foreach ($this->defaults as $default) {
                if (strtolower($default->getKey()) == strtolower($key)) {
                    return $default;
                }
            }
        }
    }

    /**
     * @param string $key
     * @return string
     */
    public function getDefaultValue($key)
    {
        $default = $this->getDefault($key);
        if ($default) {
            return $default->getValue();
        }
    }

//    public function getArrayDefaults()
//    {
//        if (null !== $this->defaults) {
//            $result = array();
//            foreach ($this->defaults as $default) {
//                $result[$default->getId()] = $default->getValue();
//            }
//            return $result;
//        }
//    }

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

}
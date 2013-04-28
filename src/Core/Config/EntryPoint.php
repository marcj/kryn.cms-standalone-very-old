<?php


namespace Core\Config;

class EntryPoint extends Model
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $icon;

    /**
     * @var boolean
     */
    protected $link = false;

    /**
     * @var label
     */
    protected $label;

    /**
     * @var boolean
     */
    protected $multi = false;

    /**
     * @var EntryPoint[]
     */
    protected $children;

    public function setupObject()
    {
        $this->path  = $this->element->attributes->getNamedItem('path')->nodeValue;
        $this->icon  = $this->element->attributes->getNamedItem('icon')->nodeValue;
        $this->type  = $this->element->attributes->getNamedItem('type')->nodeValue;
        $this->link  = (boolean)$this->element->attributes->getNamedItem('link')->nodeValue ? : false;
        $this->multi = (boolean)$this->element->attributes->getNamedItem('multi')->nodeValue ? : false;
        $this->label = $this->element->getElementsByTagName('label')->item(0)->nodeValue;
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function getChildren()
    {
        if (null === $this->children) {
            $childrenElement = $this->getDirectChild('children');
            $this->children  = array();
            $children        = $childrenElement->childNodes;
            if ($children) {
                foreach ($children as $child) {
                    if ('entry-point' === $child->nodeName) {
                        $this->children[] = $this->getModelInstance($child);
                    }
                }
            }
        }

        return $this->children;
    }

    public function getChildrenArray()
    {
        $entryPoints = array();
        foreach ($this->getChildren() as $entryPoint) {
            $entryPoints[$entryPoint->getPath()] = $entryPoint->toArray();
        }
        return $entryPoints;
    }


    public function getParentInstance()
    {
        // we need to jump two elements, since we have <entry-point><children><entry-point>
        return $this->getModelInstance($this->element->parentNode->parentNode);
    }

    public function toArray($element = null)
    {
        $result = parent::toArray($element);
        $result['fullPath'] = $this->getFullPath();
        return $result;
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        $path[] = $this->getPath();
        $instance = $this;
        while ($instance = $instance->getParentInstance()){
            if (!($instance instanceof EntryPoint)) {
                break;
            }
            array_unshift($path, $instance->getPath());
        }
        return implode('/', $path);
    }

    /**
     * @param $path
     *
     * @return EntryPoint
     */
    public function getChild($path)
    {
        $this->children = $this->children ? : $this->getChildren();
        $first          = (false === ($pos = strpos($path, '/'))) ? $path : substr($path, 0, $pos);

        foreach ($this->children as $child) {
            if ($first == $child->getPath()) {
                if (false !== strpos($path, '/')) {
                    return $child->getChild(substr($path, $pos + 1));
                } else {
                    return $child;
                }
            }
        }
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
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param boolean $link
     */
    public function setLink($link)
    {
        $this->link = filter_var($link, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return boolean
     */
    public function getLink()
    {
        return $this->link;
    }

    public function isLink()
    {
        return true === $this->link;
    }

    /**
     * @param \Core\Config\label $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return \Core\Config\label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param boolean $multi
     */
    public function setMulti($multi)
    {
        $this->multi = filter_var($multi, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return boolean
     */
    public function getMulti()
    {
        return $this->multi;
    }

    /**
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
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

}
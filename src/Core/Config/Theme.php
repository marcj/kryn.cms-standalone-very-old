<?php

namespace Core\Config;

class Theme extends Model
{
    protected $attributes = ['id'];
    protected $elementMap = ['content' => 'ThemeContent', 'layout' => 'ThemeLayout'];

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var ThemeContent[]
     */
    protected $contents;

    /**
     * @var ThemeNavigation[]
     */
    protected $navigations;

    /**
     * @var ThemeLayout[]
     */
    protected $layouts;

    /**
     * @var Field[]
     */
    protected $options;

    /**
     * @param ThemeContent[] $contents
     */
    public function setContents(array $contents = null)
    {
        $this->contents = $contents;
    }

    /**
     * @param bool $orCreate creates the value of not exists.
     *
     * @return ThemeContent[]
     */
    public function getContents($orCreate = false)
    {
        return $this->contents;
    }

    /**
     * @param ThemeLayout[] $layouts
     */
    public function setLayouts(array $layouts = null)
    {
        $this->layouts = $layouts;
    }

    /**
     * @return ThemeLayout[]
     */
    public function getLayouts()
    {
        return $this->layouts;
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
    public function setOptions(array $options = null)
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
     * @param ThemeNavigation[] $navigations
     */
    public function setNavigations(array $navigations = null)
    {
        $this->navigations = $navigations;
    }

    /**
     * @return ThemeNavigation[]
     */
    public function getNavigations()
    {
        return $this->navigations;
    }


}
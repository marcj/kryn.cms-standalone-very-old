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
     * @var ThemeLayout[]
     */
    protected $layouts;

    /**
     * @param ThemeContent[] $contents
     */
    public function setContents(array $contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return ThemeContent[]
     */
    public function getContents()
    {
        if (null === $this->contents) {
            $contents = $this->element->getElementsByTagName('content');
            $this->contents = array();
            foreach ($contents as $content) {
                $this->contents[] = new ThemeContent($content);
            }
        }

        return $this->contents;
    }

    /**
     * @param ThemeLayout[] $layouts
     */
    public function setLayouts(array $layouts)
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

}
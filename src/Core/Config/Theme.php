<?php

namespace Core\Config;

class Theme extends Model {
    /**
     * @var string
     */
    protected $id;

    /**
     * @var ThemeContent[]
     */
    protected $contents;

    /**
     * @var ThemeLayout[]
     */
    protected $layouts;

    public function setupObject(){

        $layouts = $this->element->getElementsByTagName('layout');
        foreach ($layouts as $layout) {
            $this->layouts[] = new ThemeLayout($layout);
        }

        $this->setAttributeVar('id');
    }

    /**
     * @param ThemeContent[] $contents
     */
    public function setContents($contents)
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
    public function setLayouts($layouts)
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

}
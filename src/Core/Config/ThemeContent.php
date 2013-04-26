<?php

namespace Core\Config;

class ThemeContent extends Model {
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $file;

    public function setupObject(){
        $this->label = $this->element->getElementsByTagName('label')->item(0)->nodeValue;
        $this->file = $this->element->getElementsByTagName('file')->item(0)->nodeValue;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
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
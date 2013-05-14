<?php

namespace Core\Render;

use Core\Models\Content;

abstract class AbstractType implements TypeInterface
{
    /**
     * @var Content
     */
    private $content;

    /**
     * @var array
     */
    private $parameters;

    public function __construct(Content $content, array $parameters)
    {
        $this->content = $content;
        $this->parameters = $parameters;
    }

    /**
     * @param Content $content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    /**
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}

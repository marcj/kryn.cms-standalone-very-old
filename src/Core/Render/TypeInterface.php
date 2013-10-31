<?php

namespace Core\Render;

use Core\Models\Content;

interface TypeInterface
{
    /**
     * @param Content $content
     * @param array   $parameter
     */
    public function __construct(Content $content, array $parameter);

    /**
     * @return string
     */
    public function render();

}
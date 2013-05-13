<?php

namespace Core\Render;

use Core\Models\Content;

class TypeText implements TypeInterface
{

    public function render(Content $content, $parameter)
    {
        return $content->getContent();
    }

}
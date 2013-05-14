<?php

namespace Core\Render;

use Core\Models\Content;

class TypeText extends AbstractType
{
    public function render()
    {
        return $this->getContent()->getContent();
    }

}
<?php

namespace Core\Render;

use Core\Models\Content;

class TypeHtml extends AbstractType
{
    public function render()
    {
        return $this->getContent()->getContent() ?: '';
    }
}
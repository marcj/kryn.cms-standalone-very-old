<?php

namespace Core\Render;

use Core\Models\Content;

class TypeImage extends AbstractType
{
    public function render()
    {
        if ($this->getContent()->getContent()) {
            $info = json_decode($this->getContent()->getContent(), true);
            return sprtinf('<img src="%s" />', $info['src']);
        }
    }
}
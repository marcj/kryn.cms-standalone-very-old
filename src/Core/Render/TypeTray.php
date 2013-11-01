<?php

namespace Core\Render;

use Core\Models\Content;

class TypeTray extends AbstractType
{
    public function render()
    {
        if ($this->getContent()->getContent()) {
            $value = json_decode($this->getContent()->getContent(), true);
            return \Core\Render::getInstance($value['node']+0)->getRenderedSlot();
        }
    }
}
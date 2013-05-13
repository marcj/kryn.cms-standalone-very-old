<?php

namespace Core\Render;

use Core\Models\Content;

interface TypeInterface
{

    public function render(Content $content, $parameter);

}
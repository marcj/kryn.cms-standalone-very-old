<?php

namespace Core\Template\Engines;

/**
 * Template engine None.
 */
class None implements EnginesInterface
{

    public function render($file, $data = null)
    {
        return file_get_contents($file);
    }

}
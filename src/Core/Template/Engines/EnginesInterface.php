<?php

namespace Core\Template\Engines;

interface EnginesInterface {

    public function render($file, $data = null);

}
<?php

namespace Core;

class CoreBundle extends Bundle {

    public function getComposerPath()
    {
        return __DIR__. '/../../composer.json';
    }

}
<?php

namespace Core;

class CoreBundle extends Bundle
{
    public function getComposerPath()
    {
        return __DIR__ . '/../../composer.json';
    }

    public function getComposer()
    {
        $composer = parent::getComposer();
        $composer['activated'] = true;
        return $composer;
    }

}
<?php

namespace Core\File;

trait FileInfoTrait {

    public function getName()
    {
        return basename($this->getPath());
    }

    public function getDir()
    {
        return dirname($this->getPath());
    }

    public function isDir()
    {
        return 'file' !== $this->getType();
    }

    public function isFile()
    {
        return 'file' === $this->getType();
    }

    public function getIcon()
    {
        return '';
    }

    public function isMountPoint()
    {
        return false;
    }

}
<?php

namespace Core\File;

trait FileInfoTrait {

    public function getName()
    {
        return basename($this->getPath()) ?: '/';
    }

    public function getDir()
    {
        return dirname($this->getPath()) ?: '/';
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

    public function getExtension()
    {
        $lastDot = strrpos($this->getName(), '.');
        return false === $lastDot ? null : strtolower(substr($this->getName(), $lastDot + 1));
    }

}
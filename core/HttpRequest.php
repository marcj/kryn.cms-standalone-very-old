<?php

namespace core;

use \Symfony\Component\HttpFoundation\Request;

class HttpRequest extends Request
{
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
            //fix / and \ escape 'feature-bug' in apache
            $this->pathInfo = str_replace('%252F', '%2F', $this->pathInfo);

        }

        return $this->pathInfo;
    }

}

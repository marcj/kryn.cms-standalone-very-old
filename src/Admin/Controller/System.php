<?php

namespace Admin\Controller;

use Core\Kryn;

class System
{
    public function getSystemInformation()
    {
        $res['version'] = Kryn::$configs['kryn']['version'];

        return $res;
    }

}

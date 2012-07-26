<?php

namespace Admin;

use Core\Kryn;

class System extends \RestServerController {

    public function getSystemInformation(){
        $res['version'] = Kryn::$configs['kryn']['version'];
        return $res;
    }

}
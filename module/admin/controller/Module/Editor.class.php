<?php


namespace Admin\Module;

use Core\Kryn;
use Admin\Module\Manager;

class Editor extends \RestServerController {

    public function getConfig($pName){
        return Manager::loadInfo($pName);
    }

    public static function getWindows($pName) {
        Manager::prepareName($pName);

        $classes   = find(PATH_MODULE . $pName . '/*.class.php');
        $windows   = array();
        $whiteList = array('windowlist', 'windowadd', 'windowedit', 'windowcombine');

        foreach ($classes as $class){

            $content = Kryn::fileRead($class);

            if (preg_match('/class ([a-zA-Z0-9_]*) extends (admin|)([a-zA-Z0-9_]*)\s*{/', $content, $matches)){
                if (in_array(strtolower($matches[3]), $whiteList))
                    $windows[] = $matches[1];
            }

        }

        return $windows;
    }

    public function getObjects($pName) {
        $config = $this->getConfig($pName);
        return $config['objects'];
    }


}
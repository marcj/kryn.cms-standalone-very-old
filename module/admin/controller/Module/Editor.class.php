<?php


namespace Admin\Module;

use Core\Kryn;
use Admin\Module\Manager;

class Editor extends \RestServerController {

    public function getConfig($pName){
        return Manager::loadInfo($pName);
    }

    public function setConfig($pName, $pConfig){
        Manager::prepareName($pName);

        $json = json_format(json_encode($pConfig));

        $path = "core/config.json";

        if ($pName != 'kryn')
            $path = PATH_MODULE . "$pName/config.json";

        if (!is_writeable($path)){
            $this->sendError('file_not_writable', tf('The config file %s for %s is not writeable.', $path ,$pName));
        }

        return Kryn::fileWrite($path, $json);
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
        Manager::prepareName($pName);
        $config = $this->getConfig($pName);
        return $config['objects'];
    }

    public function saveObjects($pName) {
        Manager::prepareName($pName);

        $config = $this->getConfig($pName);

        $objects = json_decode(getArgv('objects'), true);
        $config['objects'] = $objects;

        return $this->setConfig($pName, $config);
    }


    public function getModel($pName){
        Manager::prepareName($pName);

        $path = PATH_MODULE . "$pName/model.xml";

        if (!file_exists($path)){
            throw new \FileNotExistException(tf('The config file %s for %s is not writeable.', $path ,$pName));
        }

        return file_get_contents($path);

    }


}
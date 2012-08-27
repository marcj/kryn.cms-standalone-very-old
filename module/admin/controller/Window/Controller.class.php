<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

namespace Admin\Window;

class Controller {

    private $entryPoint;

    public function handle() {

        switch (getArgv('cmd')) {
            case 'custom':
                return self::custom();
            case 'sessionbasedFileUpload':
                return self::sessionbasedFileUpload();
            case 'getItems':
            case 'exportItems':
            case 'getItem':
            case 'saveItem':
            case 'deleteItem':
            case 'removeSelected':
            default:
                return self::loadClass();
        }
    }

    public function translateFields(&$pFields){

        if (is_array($pFields)){
            foreach ($pFields as &$field){
                if ($field['label'] && substr($field['label'],0,2) == '[[' && substr($field['label'],-2) == ']]'){
                    $field['label'] = t(substr($field['label'], 2, -2));
                } else if ($field['title'] && substr($field['title'],0,2) == '[[' && substr($field['title'],-2) == ']]')
                    $field['title'] = t(substr($field['title'], 2, -2));
                else if(is_array($field['depends'])){
                    self::translateFields($field['depends']);
                }
            }
        }

    }

    public function setEntryPoint($pEntryPoint){
        $this->entryPoint = $pEntryPoint;
    }

    public function getItems(){

        $obj = $this->getClass();
        return $obj->getItems();

        $condition = null;
        $options   = array();

        return $obj->getItems($condition, $options);
    }


    public function getBranch(){

        $obj = $this->getClass();
        return $obj->getBranch();

    }

    public function getClass(){

        $clazz = $this->entryPoint['class'];

        if (!$clazz && $this->entryPoint['object']){
            //we use normal ORM wrapper
            return \Core\Object::getClassObject($this->entryPoint['object']);
        }

        if (class_exists($clazz)){
            $obj = new $clazz($this->entryPoint);
        } else {
            throw new \Exception(tf('Class %s not found', $clazz));
        }

        return $obj;

    }


    public function getInfo(){

        $obj = $this->getObj();
        return $obj;
    }


    public function getObj() {

        $info = \Admin\Utils::getPathItem(\Core\Kryn::getRequestedPath());

        $class = $info['class'];

        $module2LoadClass = $info['_module'];

        if (class_exists($class)){
            $obj = new $class($info, getArgv('cmd'));
        } else {
            throw new \Exception(tf('Class %s not found', $class));
        }
        return $obj;

    }


    public function call($pCmd) {
        $obj = $this->getObj();

        switch ($pCmd) {
            case 'getItems':
                $obj->params = json_decode(getArgv('params'));
                $res = $obj->getItems(getArgv('page') ? getArgv('page') : false);
                break;
            case 'exportItems':
                $obj->params = json_decode(getArgv('params'));
                $res = $obj->exportItems(getArgv('page') ? getArgv('page') : false);
                break;
            case 'getItem':
                $res = $obj->getItem();
                break;
            case 'saveItem':
                $res = $obj->saveItem();
                break;
            case 'deleteItem':
                $res = $obj->deleteItem();
                break;
            case 'removeSelected':
                $res = $obj->removeSelected();
                break;
            case 'getClassDefinition':
                $res = $obj;
        }

        $obj = NULL;
        return $res;
    }

    public static function sessionbasedFileUpload() {
        global $client;

        //get session id
        $sessionId = $client->token;

        //path for session folder
        $path = PATH_MEDIA . getArgv('path') . $sessionId;
        $path = str_replace("..", "", $path);

        //make folder if not exists
        if (!is_dir($path))
            mkdir($path);

        //override path
        $_REQUEST['path'] = $_POST['path'] = $_GET['path'] = getArgv('path') . $sessionId;

        //now upload the file
        return adminFilemanager::uploadFile($_REQUEST['path']);

    }

}

?>

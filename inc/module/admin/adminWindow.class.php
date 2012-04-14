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


class adminWindow {

    public static function handle() {

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

    public static function translateFields(&$pFields){

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

    public static function getModule() {
        $url = kryn::getRequestPath();
        //admin/ = 6
        $url = substr($url, 6);
        $firstSlash = strpos($url, '/');
        return substr($url, 0, $firstSlash);
    }

    public static function getCode() {
        $url = kryn::getRequestPath();
        //admin/ = 6
        $url = substr($url, 6);
        $firstSlash = strpos($url, '/');
        return substr($url, $firstSlash + 1);
    }

    public static function loadClass() {
        global $kdb;

        require(PATH_MODULE . 'admin/adminWindowList.class.php');
        require(PATH_MODULE . 'admin/adminWindowCombine.class.php');
        require(PATH_MODULE . 'admin/adminWindowEdit.class.php');
        require(PATH_MODULE . 'admin/adminWindowAdd.class.php');

        $info = admin::getPathItem(kryn::getRequestPath());
        $class = $info['class'];

        $module2LoadClass = $info['_module'];

        if (strpos($class, '/')) {
            $t = explode('/', $class);
            $module2LoadClass = $t[0];
            $class = $t[1];
        }

        if (file_exists(PATH_MODULE . "$module2LoadClass/$class.class.php")) {
            require_once(PATH_MODULE . "$module2LoadClass/$class.class.php");
            $obj = new $class($info, getArgv('cmd'));
        }

        switch (getArgv('cmd')) {
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
                if (!$obj){
                    return(array('error' => 'class_not_found'));
                }
                $res = $obj;
        }

        $obj = NULL;
        json($res);
    }

    public static function sessionbasedFileUpload() {
        global $client;

        //get session id
        $sessionId = $client->token;

        //path for session folder
        $path = 'inc/template' . getArgv('path') . $sessionId;
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

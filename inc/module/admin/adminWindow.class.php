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
            $obj = new $class;
        } else {
            $form = kryn::fileRead(PATH_MODULE . "$module2LoadClass/forms/$class.json");
            $formObj = json_decode($form, 1);

            $type = $formObj['type'];

            $obj = new $type();

            foreach ($formObj as $optKey => $optVal)
                $obj->$optKey = $optVal;

        }

        $config = kryn::$configs[$module];

        //        $dbFile = PATH_MODULE."$module/db.php";
        //        if( file_exists($dbFile) )
        //            require($dbFile);

        $obj->db = ($config['db']) ? $config['db'] : array();
        $obj->module = $info['_module'];
        $obj->code = $info['_code'];
        $obj->init();

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
            default:
                $res = $obj->init(true);
                if (getArgv('relation_table')) {
                    $res->relation = database::getRelation(getArgv('relation_table'), $res->table);
                }
                break;
        }

        $obj = NULL;
        json($res);
    }

    public static function sessionbasedFileUpload() {
        global $client;

        // Get session id
        $sessionId = $client->token;

        // Path for session folder
        $path = getArgv('path') . $sessionId;

        // Make folder if not exists
        if (!krynFile::exists($path))
            krynFile::createFolder($path);

        // Override path
        $_REQUEST['path'] = $_POST['path'] = $_GET['path'] = getArgv('path') . $sessionId;

        // Now upload the file
        return adminFilemanager::uploadFile($path);

    }

}

?>

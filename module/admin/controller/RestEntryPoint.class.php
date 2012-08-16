<?php

namespace Admin;

/**
 * RestController for the entry points which are from type store or framework window.
 *
 */
class RestEntryPoint extends \RestService\Server {

    public function run($pEntryPoint){

        if ($pEntryPoint['type'] == 'store') {

            if (!$pEntryPoint['class']) {
                $obj = new adminStore();
            } else {
                require_once(PATH_MODULE . '' . $pEntryPoint['_module'] . '/' . $pEntryPoint['class'] . '.class.php');
                $clazz = $pEntryPoint['class'];
                $obj = new $clazz();
            }

            try {
                $this->send($obj->handle($pEntryPoint));
            } catch (Exception $e){
                $this->sendError('admin_store', array('exception' => $e->getMessage(), 'entrypoint' => $pEntryPoint));
            }
        } else {
            $adminWindows = array('edit', 'list', 'add', 'combine');
            $windowController = new Window\Controller();

            $windowController->setEntryPoint($pEntryPoint);

            if ($_GET['cmd'] == 'getInfo') {

                $this->send($pEntryPoint);

            } else if (in_array($pEntryPoint['type'], $adminWindows)) {

                try {

                    //add routes
                    $this->addSubController($pEntryPoint['_module'].'/'.$pEntryPoint['_code'], $windowController)
                        ->addGetRoute('', 'getItems')
                        ->addPostRoute('', 'saveItem')
                        ->addPutRoute('', 'addItem')
                        ->addDeleteRoute('', 'removeItem');

                    //run parent
                    parent::run();

                    //$this->send($windowController->handle($pEntryPoint));
                    $this->sendError('invalid_action');

                } catch (Exception $e){
                    $this->sendError('admin_store', array('exception' => $e, 'entrypoint' => $pEntryPoint));
                }
            }
        }

    }

}
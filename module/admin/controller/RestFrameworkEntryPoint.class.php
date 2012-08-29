<?php

namespace Admin;

/**
 * RestController for the entry points which are from type store or framework window.
 *
 */
class RestFrameworkEntryPoint extends \RestService\Server {

    public function exceptionHandler($pException){
        if (get_class($pException) != 'AccessDeniedException')
            \Core\Utils::exceptionHandler($pException);
    }

    public function run($pEntryPoint){


        
        if (\Core\Kryn::$config['displayRestErrors']){
            if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'){
                $exceptionHandler = array($this, 'exceptionHandler');
            }
            $debugMode = true;
        }

        $this->setDebugMode($debugMode);

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

            if ($this->getClient()->getMethod() == 'head') {

                $this->send($pEntryPoint);

            } else if (in_array($pEntryPoint['type'], $adminWindows)) {

                //add routes
                $this->addSubController($pEntryPoint['_module'].'/'.$pEntryPoint['_code'], $windowController)
                    ->addGetRoute('', 'getItems')
                    ->addPostRoute('', 'saveItem')
                    ->addPutRoute('', 'addItem')
                    ->addDeleteRoute('', 'removeItem')
                    ->addOptionsRoute('', 'getInfo');

                //run parent
                parent::run();
            }
        }

    }

}
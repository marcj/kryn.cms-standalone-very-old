<?php

/**
 * RestController for the entry points which are from type store or framework window.
 *
 */
class adminRestEntryPoint extends RestServer {

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
                $this->sendError('admin_store', array('exception' => $e, 'entrypoint' => $pEntryPoint));
            }
        } else {
            $adminWindows = array('edit', 'list', 'add', 'combine');
            $obj = new adminWindow();

            if ($_GET['cmd'] == 'getInfo') {

                $this->send($pEntryPoint);

            } else if (in_array($pEntryPoint['type'], $adminWindows)) {

                try {
                    $this->send($obj->handle($pEntryPoint));
                } catch (Exception $e){
                    $this->sendError('admin_window_handle', $e);
                    $this->sendError('admin_store', array('exception' => $e, 'entrypoint' => $pEntryPoint));
                }
            }
        }

    }

}
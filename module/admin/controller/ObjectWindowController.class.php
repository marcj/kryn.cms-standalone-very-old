<?php

namespace Admin;

use RestService\Server;

/**
 * RestController for the entry points which are from type store or framework window.
 *
 */
class ObjectWindowController extends Server {

    public $entryPoint;

    public function exceptionHandler($pException){
        if (get_class($pException) != 'AccessDeniedException')
            \Core\Utils::exceptionHandler($pException);
    }

    public function run($pEntryPoint){


        $this->entryPoint = $pEntryPoint;

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

            if (in_array($pEntryPoint['type'], $adminWindows)) {


                //add routes
                $trigger = $pEntryPoint['_module'].'/'.$pEntryPoint['_code'];

                $this
                    ->addGetRoute($trigger, 'getItems')
                    ->addPostRoute($trigger, 'saveItem')
                    ->addPutRoute($trigger, 'addItem')
                    ->addDeleteRoute($trigger, 'removeItem')
                    ->addOptionsRoute($trigger, 'getInfo');

                //run parent
                parent::run();
            }
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

    public function saveItem($pObject = null){

        $obj = $this->getObj();

        $pk = \Core\Object::parsePk($obj->getObject(), $pObject);

        return $obj->saveItem($pk[0]);
    }

    public function addItem(){

        $obj = $this->getObj();

        return $obj->addItem();
    }

    public function getItems($pObject = null){

        $obj = $this->getObj();

        if ($pObject !== null){
            $pk = \Core\Object::parsePk($obj->getObject(), $pObject);
            return $obj->getItem($pk[0]);
        } else {
            return $obj->getItems();
        }

    }

    public function getInfo(){

        $obj = $this->getObj();
        return $obj->getInfo();
    }


    public function getObj() {

        $class = $this->entryPoint['class'];

        $module2LoadClass = $this->entryPoint['_module'];

        if (class_exists($class)){
            $obj = new $class($this->entryPoint);
        } else {
            throw new \Exception(tf('Class %s not found', $class));
        }
        return $obj;

    }


}
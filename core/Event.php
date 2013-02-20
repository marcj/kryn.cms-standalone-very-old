<?php

namespace Core;

class Event {


    private static $events = array();

    /**
     * Adds $pFn to $pEventKey event.
     *
     * @static
     * @param string $pEventKey
     * @param mixed  $pFn 'myClass::staticFn', array($obj, 'methodName'), 'globalFunction'
     */
    public static function listen($pEventKey, $pFn){
        $pEventKey = strtolower($pEventKey);
        self::$events[$pEventKey][] = $pFn;
    }


    /**
     * Triggers an event. Calls all functions mapped through krynEvent::listen().
     *
     * @static
     * @param string $pEventKey
     * @param mixed $pArgument
     */
    public static function fire($pEventKey, &$pArgument = null){

        $pEventKey = strtolower($pEventKey);
        if (self::$events[$pEventKey]){
            foreach (self::$events[$pEventKey] as $fn){
                call_user_func_array($fn, array($pArgument));
            }
        }

    }

}


?>
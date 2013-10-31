<?php

namespace Core;

class Event
{
    private static $events = array();

    /**
     * Adds $fn to $eventKey event.
     *
     * @static
     *
     * @param string $eventKey
     * @param mixed  $fn       'myClass::staticFn', array($obj, 'methodName'), 'globalFunction'
     */
    public static function listen($eventKey, $fn)
    {
        $eventKey = strtolower($eventKey);
        self::$events[$eventKey][] = $fn;
    }

    /**
     * Triggers an event. Calls all functions mapped through krynEvent::listen().
     *
     * @static
     *
     * @param string $eventKey
     * @param mixed  $argument
     */
    public static function fire($eventKey, &$argument = null)
    {
        $eventKey = strtolower($eventKey);
        if (self::$events[$eventKey]) {
            foreach (self::$events[$eventKey] as $fn) {
                call_user_func_array($fn, array($argument));
            }
        }

    }

}

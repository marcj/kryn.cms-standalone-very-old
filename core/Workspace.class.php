<?php

namespace Core;

//TODO all

class Workspace {

    private static $current = 0;

    public static function getCurrent(){
        return static::$current;
    }

    public static function setCurrent($pId){
        static::$current = $pId;
    }

}
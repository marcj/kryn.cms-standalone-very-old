<?php

namespace Core;

class PropelBasePeer extends \BasePeer {

    public static $ignoreNextDoSelect = false;

    public static function getIgnoreNextDoSelect(){
        return static::$ignoreNextDoSelect;
    }

    public static function setIgnoreNextDoSelect($pValue){
        static::$ignoreNextDoSelect = $pValue;
    }

    public static function doSelect(\Criteria $criteria, \PropelPDO $con = null){

        if (self::getIgnoreNextDoSelect()){
            self::setIgnoreNextDoSelect(false);
            return;
        }

        return \BasePeer::doSelect($criteria, $con);
    }
}
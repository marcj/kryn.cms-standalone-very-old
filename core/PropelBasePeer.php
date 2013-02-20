<?php

namespace Core;

class PropelBasePeer extends \BasePeer
{
    public static $ignoreNextDoSelect = false;

    public static function getIgnoreNextDoSelect()
    {
        return static::$ignoreNextDoSelect;
    }

    public static function setIgnoreNextDoSelect($pValue)
    {
        static::$ignoreNextDoSelect = $pValue;
    }

    public static function doUpdate(\Criteria $selectCriteria, \Criteria $updateValues, \PropelPDO $con)
    {
        //remove propel cache
        $dbMap = \Propel::getDatabaseMap($selectCriteria->getDbName());

        $tableName = $selectCriteria->getPrimaryTableName();

        if (!$tableName) {
            $keys = $selectCriteria->keys();
            if (!empty($keys))
                $tableName = $selectCriteria->getTableName( $keys[0] );
        }
        if ($tableName) {
            $table = $dbMap->getTable($tableName);
            $peer = $table->getPeerClassname();
            Kryn::removePropelCacheObject($peer::OM_CLASS);
        }

        return parent::doUpdate($selectCriteria, $updateValues, $con);
    }

    public static function doSelect(\Criteria $criteria, \PropelPDO $con = null)
    {
        if (self::getIgnoreNextDoSelect()) {
            self::setIgnoreNextDoSelect(false);

            return;
        }

        return \BasePeer::doSelect($criteria, $con);
    }
}

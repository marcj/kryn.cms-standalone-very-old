<?php

namespace Core;

class PropelBasePeer extends \BasePeer {

    public static $returnSqlInNextSelect = false;

    public static function getReturnSqlInNextSelect(){
        return static::$returnSqlInNextSelect;
    }

    public static function setReturnSqlInNextSelect($pValue){
        static::$returnSqlInNextSelect = $pValue;
    }

    public static function doSelect(\Criteria $criteria, \PropelPDO $con = null){

        $dbMap = \Propel::getDatabaseMap($criteria->getDbName());
        $db = \Propel::getDB($criteria->getDbName());
        $stmt = null;

        $tableName = $criteria->getPrimaryTableName();

        if (!$tableName){
            $keys = $criteria->keys();
            if (!$keys)
                $keys = $criteria->getSelectColumns();
            if ($keys){
                $tableName = $criteria->getTableName( $keys[0] );
            }
        }

        if ($tableName)
            $peer = $dbMap->getTable($tableName)->getPeerClassname();

        if ($con === null) {
            $con = \Propel::getConnection($criteria->getDbName(), \Propel::CONNECTION_READ);
        }

        try {

            $params = array();
            $sql = self::createSelectSql($criteria, $params);

            if ($peer && $peer::getReturnSqlInNextSelect()){
                $peer::setReturnSqlInNextSelect(false);
                return array($sql, $params);
            }

            $stmt = $con->prepare($sql);

            $db->bindValues($stmt, $params, $dbMap);

            $stmt->execute();

        } catch (\Exception $e) {
            if ($stmt) {
                $stmt = null; // close
            }
            \Propel::log($e->getMessage(), \Propel::LOG_ERR);
            throw new \PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }

        return $stmt;
    }


}
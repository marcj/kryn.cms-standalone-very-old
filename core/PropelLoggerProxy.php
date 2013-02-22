<?php

namespace core;

use \Propel;

class PropelLoggerProxy implements \BasicLogger
{
    public function emergency($m)
    {
        $this->log($m, Propel::LOG_EMERG);
    }
    public function alert($m)
    {
        $this->log($m, Propel::LOG_ALERT);
    }
    public function crit($m)
    {
        $this->log($m, Propel::LOG_CRIT);
    }
    public function err($m)
    {
        $this->log($m, Propel::LOG_ERR);
    }
    public function warning($m)
    {
        $this->log($m, Propel::LOG_WARNING);
    }
    public function notice($m)
    {
        $this->log($m, Propel::LOG_NOTICE);
    }
    public function info($m)
    {
        $this->log($m, Propel::LOG_INFO);
    }
    public function debug($m)
    {
        $this->log($m, Propel::LOG_DEBUG);
    }

    public function log($message, $severity = null)
    {
        switch ($severity) {
            case Propel::LOG_EMERG: $severity = 500; break;
            case Propel::LOG_CRIT: $severity = 500; break;
            case Propel::LOG_ALERT: $severity = 550; break;
            case Propel::LOG_ERR: $severity = 400; break;
            case Propel::LOG_WARNING: $severity = 300; break;
            case Propel::LOG_NOTICE: $severity = 250; break;
            case Propel::LOG_INFO: $severity = 200; break;
            case Propel::LOG_DEBUG: $severity = 100; break;
            default:
                $severity = 250;
        }

        Kryn::getLogger()->log($severity, $message);
    }

}

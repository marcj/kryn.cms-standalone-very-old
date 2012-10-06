<?php

namespace Core;

class Utils {

    private static $inErrorHandler = false;

    public static function exceptionHandler($pException){
        $exceptionArray = array(
            'type' => $pException->getCode(),
            'message' => $pException->getMessage(),
            'file' => $pException->getFile(),
            'line' => $pException->getLine(),
        );
        self::errorHandler(get_class($pException).' ['.$pException->getCode().']',
            $pException->getMessage(), $pException->getFile(), $pException->getLine(),
            array_merge(array($exceptionArray), $pException->getTrace()));
    }

    public static function shutdownHandler(){
        chdir(PATH);
        $error = error_get_last();
        if($error['type'] == 1){
            self::errorHandler($error['type'], $error['message'], $error['file'], $error['line'], array(
                $error
            ));
        }
    }
    public static function errorHandler($pErrorCode, $pErrorStr, $pFile, $pLine, $pBacktrace = null){


        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            $response = array(
                'status' => 500,
                'error' => $pErrorCode.' '.$pErrorStr
            );

            if (Kryn::$config['displayRestErrors']){
                $response['file'] = $pFile;
                $response['line'] = $pLine;
                $response['backstrace'] = $pBacktrace ? $pBacktrace : debug_backtrace();
            }

            json($response);
        }

        if (self::$inErrorHandler === true){
            print $pErrorCode.', '.$pErrorStr.' in '.$pFile.' at '.$pLine;
            if ($pBacktrace)
                print_r($pBacktrace);
            else 
                print_r(debug_backtrace());
            exit;
        }

        self::$inErrorHandler = true;

        if (is_numeric($pErrorCode)){
            $errorCodes = array(
                E_ERROR => 'E_ERROR',
                E_WARNING => 'E_WARNING',
                E_PARSE => 'E_PARSE',
                E_NOTICE => 'E_NOTICE',
                E_CORE_ERROR => 'E_CORE_ERROR',
                E_CORE_WARNING => 'E_CORE_WARNING',
                E_STRICT => 'E_STRICT',
                E_COMPILE_ERROR => 'E_COMPILE_ERROR',
                E_COMPILE_WARNING => 'E_COMPILE_WARNING',
                E_USER_ERROR => 'E_USER_ERROR',
                E_USER_WARNING => 'E_USER_WARNING',
                E_USER_NOTICE => 'E_USER_NOTICE',
            );
            if ($errorCodes[$pErrorCode]){
                $pErrorCode = $errorCodes[$pErrorCode];
            }
        }

        $msg = '<div style="margin-bottom: 15px; background-color: white; padding: 5px;">'.$pErrorStr.'</div>';

        $backtrace = $pBacktrace;
        if (!$backtrace){
            $backtrace = debug_backtrace();
        }

        tAssign('loadCodemirror', true);

        $traces = array();
        $count = count($backtrace);
        foreach ($backtrace as $trace){

            $trace['file'] = substr($trace['file'], strlen(PATH));
            $trace['id'] = $count--;
            // if ($trace['file'] == 'core/bootstrap.php' && $trace['line'] == 74) continue;
            if ($trace['file'] == 'core/global/internal.global.php' && $trace['line'] == 40) continue;

            $trace['code'] = self::getFileContent($trace['file'], $trace['line'], 5);
            $trace['relLine'] = $trace['line']-5;
            //$trace['args_string'] = implode(', ', $trace['args']);
            $traces[] = $trace;
        }

        tAssign('backtrace', $traces);
        //backtrace
        //$msg .= '<div style="padding: 5px; white-space: pre;">'..'</div>';

        //$msg .= self::getHighlightedFile($pFile, $pLine);


        kryn::internalError($pErrorCode, $msg, false);

        self::$inErrorHandler = true;

        exit;

    }

    public static function getFileContent($pFile, $pLine, $pOffset = 10){

        $fh = fopen($pFile, 'r');

        if ($fh){
            $line = 1;
            $code = '';
            while (($buffer = fgets($fh, 4096)) !== false) {
                
                if ($line >= ($pLine-$pOffset) && $line <= ($pLine+$pOffset))
                    $code .= $buffer;

                if ($line == $pLine)
                    $highlightLine = $line;

                $line++;
            }
            return $code;
        }
        return '';
    }


    /**
     * Stores all locked keys, so that we can release all,
     * on process terminating.
     * @var array
     */
    public static $lockedKeys = array();

    /**
     * Releases all locked aquired by this process.
     *
     * Will be called during process shutdown. (register_shutdown_function)
     */
    public static function releaseLocks(){
        foreach (self::$lockedKeys as $key => $value) {
            self::appRelease($key);
        }
    }

    /**
     * Locks the process until the lock of $pId has been acquired for this process.
     * If no lock has been acquired for this id, it returns without waiting true.
     * 
     * @param  string $pId
     * @param  integer $pTimeout Milliseconds
     * @return boolean
     */
    public static function appLock($pId, $pTimeout = 15){

        if (self::appTryLock($pId, $pTimeout))
            return true;
        else {
            for($i=0; $i<1000; $i++){
                usleep(15*1000); //15ms
                if (self::appTryLock($pId, $pTimeout))
                    return true;
            }
        }

        
    }

    /**
     * Tries to lock given id. If the id is already locked,
     * the function returns without waiting.
     *
     * @see appLock()
     * 
     * @param  string $pId
     * @param  int    $pTimeout Default is 30sec
     * @return bool
     */
    public static function appTryLock($pId, $pTimeout = 30){

        //already aquired by this process?
        if (self::$lockedKeys[$pId] === true) return true;

        $now     = ceil(microtime(true)*1000);
        $timeout = $now+$pTimeout;

        dbDelete('system_app_lock', 'timeout <= '.$now);

        try {
            dbInsert('system_app_lock', array('id' => $pId, 'timeout' => $timeout));
            self::$lockedKeys[$pId] = true;
            return true;
        } catch(\Exception $e){
            return false;
        }
    }

    /**
     * Releases a lock.
     * If you're not the owner of the lock with $pId, then you'll kill it anyway.
     * 
     * @param  string $pId
     */
    public static function appRelease($pId){

        unset(self::$lockedKeys[$pId]);

        try {
            dbDelete('system_app_lock', array('id' => $pId));
        } catch(\Exception $e){
        }
    }

}


//when we'll be loaded, then we register our releaseLocks 
register_shutdown_function('\Core\Utils::releaseLocks');
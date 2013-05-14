<?php

namespace Core;

use Symfony\Component\HttpFoundation\Response;

class Utils
{
    private static $inErrorHandler = false;

    public static $latency = array();

    public static function exceptionHandler(\Exception $pException)
    {
        $output = ob_get_clean();

        if (!Kryn::$config['displayErrors']) {
            Kryn::internalError(
                'Internal Server Error',
                tf(
                    'The server encountered an internal error and was unable to complete your request. Please contact the administrator. %s',
                    Kryn::$config['email']
                )
            );
        }

        if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ||
            php_sapi_name() == 'cli'
        ) {
            $response = array(
                'status' => 500,
                'error' => get_class($pException),
                'message' => $pException->getMessage(). "[$output]",
                'previous' => $pException->getPrevious()
            );

            if (Kryn::$config['displayDetailedRestErrors']) {
                $response['file'] = $pException->getFile();
                $response['line'] = $pException->getLine();
                $response['backstrace'] = $pException->getTrace();
            }

            json($response);
        }

        if (self::$inErrorHandler === true) {
            print get_class($pException) . ', ' . $pException->getMessage() . ' in ' .
                $pException->getFile() . ' +'. $pException->getLine();
            if ($trace = $pException->getTrace()) {
                print_r($trace);
            } else {
                print_r(debug_backtrace());
            }
            exit;
        }

        self::$inErrorHandler = true;


        $exceptions = array();
        self::extractException($pException, $exceptions);
        $data = array(
            'exceptions' => $exceptions
        );

        $response = new Response(Kryn::translate(
            Kryn::getInstance()->renderView('@CoreBundle/internal-error.html.smarty', $data)
        ), 500);
        $response->send();
        exit;
    }

    public static function extractException(\Exception $pException, array &$exceptions)
    {
        $exception = array(
            'title' => get_class($pException),
            'message' => $pException->getMessage(),
            'line' => $pException->getLine(),
            'file' => $pException->getFile()
        );

        $backtrace = $pException->getTrace();
        array_unshift($backtrace, $exception);

        $exception['file'] = substr($pException->getFile(), strlen(PATH));

        $traces = array();
        $count = count($backtrace);
        foreach ($backtrace as $trace) {
            $trace['file'] = substr($trace['file'], strlen(PATH));
            $trace['id'] = $count--;
            if ($trace['file'] == 'src/Core/global/internal.global.php' && $trace['line'] == 40) {
                continue;
            }

            $code = self::getFileContent($trace['file'], $trace['line']);
            $trace['countLines'] = substr_count($code, "\n");

            $inserted = false;
            if (false === strpos($trace['code'], '<?php')) {
                $code = "<?php\n" . $code;
                $inserted = true;
            }
            $code = highlight_string($code, true);

            if ($inserted) {
                $code = preg_replace('/&lt;\?php<br \/>/', '', $code, 1);
            }

            $trace['startLine'] = 10 > $trace['line'] ? 1 : ($trace['line'] - 10);
            if (1 >= $trace['startLine']){
                $trace['startLine'] = 1;
            }


            $trace['code'] = $code;
            $traces[] = $trace;
        }

        $exception['backtrace'] = $traces;
        array_unshift($exceptions, $exception);

        if ($pException->getPrevious()){
            self::extractException($pException->getPrevious(), $exceptions);
        }
    }

    /**
     * @param array  $files
     * @param string $includePath The directory where to compressed css is. with trailing slash!
     *
     * @return string
     */
    public static function compressCss(array $files, $includePath = '')
    {
        $toGecko = array(
            "-moz-border-radius-topleft",
            "-moz-border-radius-topright",
            "-moz-border-radius-bottomleft",
            "-moz-border-radius-bottomright",
            "-moz-border-radius",
        );

        $toWebkit = array(
            "-webkit-border-top-left-radius",
            "-webkit-border-top-right-radius",
            "-webkit-border-bottom-left-radius",
            "-webkit-border-bottom-right-radius",
            "-webkit-border-radius",
        );
        $from = array(
            "border-top-left-radius",
            "border-top-right-radius",
            "border-bottom-left-radius",
            "border-bottom-right-radius",
            "border-radius",
        );

        $content = '';
        foreach ($files as $assetPath) {
            $content .= "\n\n/* file: $assetPath */\n\n";

            $cssFile = Kryn::resolvePublicPath($assetPath); //admin/css/style.css
            $cssDir = dirname($cssFile) . '/'; //admin/css/...
            $cssDir = str_repeat('../', substr_count($includePath, '/')) . $cssDir;

            if (file_exists($file = 'web/bundles/' . $cssFile) || file_exists($file = 'web/' . $cssFile)) {
                $h = fopen($file, "r");
                if ($h) {
                    while (!feof($h) && $h) {
                        $buffer = fgets($h, 4096);

                        $buffer = preg_replace('/@import \'([^\/].*)\'/', '@import \'' . $cssDir . '$1\'', $buffer);
                        $buffer = preg_replace('/@import "([^\/].*)"/', '@import "' . $cssDir . '$1"', $buffer);
                        $buffer = preg_replace('/url\(\'([^\/].*)\'\)/', 'url(\'' . $cssDir . '$1\')', $buffer);
                        $buffer = preg_replace('/url\((?!data:image)([^\/\'].*)\)/', 'url(' . $cssDir . '$1)', $buffer);
                        $buffer = str_replace(array('  ', '    ', "\t", "\n", "\r"), '', $buffer);
                        $buffer = str_replace(': ', ':', $buffer);

                        $content .= $buffer;
                        $newLine = str_replace($from, $toWebkit, $buffer);
                        if ($newLine != $buffer) {
                            $content .= $newLine;
                        }
                        $newLine = str_replace($from, $toGecko, $buffer);
                        if ($newLine != $buffer) {
                            $content .= $newLine;
                        }
                    }
                    fclose($h);
                }
            } else {
                Kryn::getLogger()->addError(tf('Can not find css file `%s` [%s]', $file, $assetPath));
            }
        }
        return $content;
    }

    public static function shutdownHandler()
    {
        global $_start;

        if (class_exists('PHPUnit_Framework_TestCase') || defined('KRYN_INSTALLER')) {
            return;
        }

        chdir(PATH);

        $key = 'kryn' === getArgv(1) ? 'backend' : 'frontend';
        \Core\Utils::$latency[$key] = microtime(true) - $_start;

        $error = error_get_last();
        if ($error['type'] == 1) {
            self::exceptionHandler($error);
        } else {
            self::latencySnapshot();
        }
    }

    public static function latenctySnapshot()
    {
        $lastLatency = Kryn::getFastCache('core/latency');

        if (self::$latency['cache']) {
            self::$latency['cache'] = array_sum(self::$latency['cache']) / count(self::$latency['cache']);
        }
        if (self::$latency['session']) {
            self::$latency['session'] = array_sum(self::$latency['session']) / count(self::$latency['session']);
        }

        $max = 20;
        foreach (array('frontend', 'backend', 'cache', 'session') as $key) {
            if (!self::$latency[$key]) {
                continue;
            }
            $lastLatency[$key] = (array)$lastLatency[$key] ? : array();
            array_unshift($lastLatency[$key], self::$latency[$key]);
            if ($max < count($lastLatency[$key])) {
                array_splice($lastLatency[$key], $max);
            }
        }

        self::$latency = array();
        Kryn::setFastCache('core/latency', $lastLatency);
    }

    public static function getFileContent($pFile, $pLine, $pOffset = 10)
    {
        if (!file_exists($pFile)) {
            return;
        }
        $fh = fopen($pFile, 'r');

        if ($fh) {
            $line = 1;
            $code = '';
            while (($buffer = fgets($fh, 4096)) !== false) {

                if ($line >= ($pLine - $pOffset) && $line <= ($pLine + $pOffset)) {
                    $code .= $buffer;
                }

                if ($line == $pLine) {
                    $highlightLine = $line;
                }

                $line++;
            }

            if ("\n" !== substr($code, 0, -1)){
                $code .= "\n";
            }

            return $code;
        }

        return '';
    }

    /**
     * Stores all locked keys, so that we can release all,
     * on process terminating.
     *
     * @var array
     */
    public static $lockedKeys = array();

    /**
     * Releases all locked aquired by this process.
     *
     * Will be called during process shutdown. (register_shutdown_function)
     */
    public static function releaseLocks()
    {
        foreach (self::$lockedKeys as $key => $value) {
            self::appRelease($key);
        }
    }

    /**
     * Locks the process until the lock of $pId has been acquired for this process.
     * If no lock has been acquired for this id, it returns without waiting true.
     *
     * @param  string  $pId
     * @param  integer $pTimeout Milliseconds
     *
     * @return boolean
     */
    public static function appLock($pId, $pTimeout = 15)
    {
        if (self::appTryLock($pId, $pTimeout)) {
            return true;
        } else {
            for ($i = 0; $i < 1000; $i++) {
                usleep(15 * 1000); //15ms
                if (self::appTryLock($pId, $pTimeout)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Tries to lock given id. If the id is already locked,
     * the function returns without waiting.
     *
     * @see appLock()
     *
     * @param  string $pId
     * @param  int    $pTimeout Default is 30sec
     *
     * @return bool
     */
    public static function appTryLock($pId, $pTimeout = 30)
    {
        //already aquired by this process?
        if (self::$lockedKeys[$pId] === true) {
            return true;
        }

        $now = ceil(microtime(true) * 1000);
        $timeout = $now + $pTimeout;

        dbDelete('system_app_lock', 'timeout <= ' . $now);

        try {
            dbInsert('system_app_lock', array('id' => $pId, 'timeout' => $timeout));
            self::$lockedKeys[$pId] = true;

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Releases a lock.
     * If you're not the owner of the lock with $pId, then you'll kill it anyway.
     *
     * @param string $pId
     */
    public static function appRelease($pId)
    {
        unset(self::$lockedKeys[$pId]);

        try {
            dbDelete('system_app_lock', array('id' => $pId));
        } catch (\Exception $e) {
        }
    }

}

//when we'll be loaded, then we register our releaseLocks
register_shutdown_function('\Core\Utils::releaseLocks');

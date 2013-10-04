<?php

namespace Core;

use Symfony\Component\HttpFoundation\Response;

class Utils
{
    private static $inErrorHandler = false;

    public static $latency = array();

    /**
     * @param string $text
     */
    public static function showFullDebug($text = null)
    {
        $exception = new \InternalErrorException();
        $exception->setMessage($text ?: 'Debug stop.');

        static::exceptionHandler($exception);
    }

    /**
     * Returns debug information.
     *
     * @return string
     */
    public static function getDebug()
    {
        $routes = [];
        /** @var \Symfony\Component\Routing\Route[] $setupRoutes */
        $setupRoutes = iterator_to_array(Kryn::$routes->getIterator());
        foreach ($setupRoutes as $route) {
            $routes[] = [
                'path' => $route->getPath(),
                'defaults' => $route->getDefaults(),
                'options' => $route->getOptions(),
            ];
        }

        $data['routes'] = $routes;

        $html = '';

        return $html;
    }

    public static function exceptionHandler(\Exception $exception)
    {
        $output = '';
        for ($i = ob_get_level(); $i >= 0; $i--) {
            $output .= ob_get_clean();
        }

        if (!Kryn::getSystemConfig()->getErrors()->getDisplay()) {
            Kryn::internalError(
                'Internal Server Error',
                tf(
                    'The server encountered an internal error and was unable to complete your request. Please contact the administrator. %s',
                    Kryn::getSystemConfig()->getEmail()
                )
            );
        }

        if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ||
            php_sapi_name() == 'cli'
        ) {
            $response = array(
                'status' => 500,
                'error' => get_class($exception),
                'message' => $exception->getMessage(). "[$output]",
                'previous' => $exception->getPrevious()
            );

            if (Kryn::getSystemConfig()->getErrors()->getDisplayRest()) {
                $response['file'] = $exception->getFile();
                $response['line'] = $exception->getLine();
                $response['backstrace'] = $exception->getTrace();
            }

            json($response);
        }

        if (self::$inErrorHandler === true) {
            print get_class($exception) . ', ' . $exception->getMessage() . ' in ' .
                $exception->getFile() . ' +'. $exception->getLine();
            if ($trace = $exception->getTrace()) {
                print_r($trace);
            } else {
                print_r(debug_backtrace());
            }
            exit;
        }

        self::$inErrorHandler = true;

        $exception2s = array();
        self::extractException($exception, $exception2s);
        $data = array(
            'exceptions' => $exception2s,
            'output' => $output
        );

        $response = new Response(Kryn::translate(
            Kryn::getInstance()->renderView('@CoreBundle/internal-error.html.smarty', $data)
        ), 500);
        $response->send();
        exit;
    }

    public static function extractException(\Exception $exception, array &$exception2s)
    {
        $exception2 = array(
            'title' => get_class($exception),
            'message' => $exception->getMessage(),
            'line' => $exception->getLine(),
            'file' => $exception->getFile()
        );

        $backtrace = [$exception2] + $exception->getTrace();

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

        $exception2['backtrace'] = $traces;
        $exception2['file'] = substr($exception->getFile(), strlen(PATH));
        $exception2s[] = $exception2;

        if ($exception->getPrevious()){
            self::extractException($exception->getPrevious(), $exception2s);
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

            $cssFile = Kryn::resolvePublicPath($assetPath); //admin/css/style.css
            $cssDir = dirname($cssFile) . '/'; //admin/css/...
            $cssDir = str_repeat('../', substr_count($includePath, '/')) . $cssDir;

            $content .= "\n\n/* file: $assetPath */\n\n";
            if (file_exists($file = 'web/bundles/' . $cssFile) || file_exists($file = 'web/' . $cssFile)) {
                $h = fopen($file, "r");
                if ($h) {
                    while (!feof($h) && $h) {
                        $buffer = fgets($h, 4096);

                        $buffer = preg_replace('/@import \'([^\/].*)\'/', '@import \'' . $cssDir . '$1\'', $buffer);
                        $buffer = preg_replace('/@import "([^\/].*)"/', '@import "' . $cssDir . '$1"', $buffer);
                        $buffer = preg_replace('/url\(\'([^\/][^\)]*)\'\)/', 'url(\'' . $cssDir . '$1\')', $buffer);
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
                $content .= '/* File `' . $cssFile . '` not exist. */';
                Kryn::getLogger()->addError(tf('Can not find css file `%s` [%s]', $file, $assetPath));
            }
        }
        return $content;
    }

    public static function shutdownHandler()
    {
        global $_start;

        if (defined('KRYN_TESTS') || defined('KRYN_INSTALLER')) {
            return;
        }

        chdir(PATH);

        $key = 'kryn' === getArgv(1) ? 'backend' : 'frontend';
        \Core\Utils::$latency[$key] = microtime(true) - $_start;

        $error = error_get_last();
        if ($error['type'] == 1) {
            $exception = new \InternalErrorException();
            $exception->setCode($error['type']);
            $exception->setMessage($error['message']);
            $exception->setFile($error['file']);
            $exception->setLine($error['line']);
            self::exceptionHandler($exception);
        } else {
            self::latencySnapshot();
        }
    }

    public static function latencySnapshot()
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

    public static function getFileContent($file, $line, $offset = 10)
    {
        if (!file_exists($file)) {
            return;
        }
        $fh = fopen($file, 'r');

        if ($fh) {
            $line2 = 1;
            $code = '';
            while (($buffer = fgets($fh, 4096)) !== false) {

                if ($line2 >= ($line - $offset) && $line2 <= ($line + $offset)) {
                    $code .= $buffer;
                }

                if ($line2 == $line) {
                    $highlightLine = $line2;
                }

                $line2++;
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
     * Locks the process until the lock of $id has been acquired for this process.
     * If no lock has been acquired for this id, it returns without waiting true.
     *
     * @param  string  $id
     * @param  integer $timeout Milliseconds
     *
     * @return boolean
     */
    public static function appLock($id, $timeout = 15)
    {
        if (self::appTryLock($id, $timeout)) {
            return true;
        } else {
            for ($i = 0; $i < 1000; $i++) {
                usleep(15 * 1000); //15ms
                if (self::appTryLock($id, $timeout)) {
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
     * @param  string $id
     * @param  int    $timeout Default is 30sec
     *
     * @return bool
     */
    public static function appTryLock($id, $timeout = 30)
    {
        //already aquired by this process?
        if (self::$lockedKeys[$id] === true) {
            return true;
        }

        $now = ceil(microtime(true) * 1000);
        $timeout2 = $now + $timeout;

        dbDelete('system_app_lock', 'timeout <= ' . $now);

        try {
            dbInsert('system_app_lock', array('id' => $id, 'timeout' => $timeout2));
            self::$lockedKeys[$id] = true;

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Releases a lock.
     * If you're not the owner of the lock with $id, then you'll kill it anyway.
     *
     * @param string $id
     */
    public static function appRelease($id)
    {
        unset(self::$lockedKeys[$id]);

        try {
            dbDelete('system_app_lock', array('id' => $id));
        } catch (\Exception $e) {
        }
    }

}

//when we'll be loaded, then we register our releaseLocks
register_shutdown_function('\Core\Utils::releaseLocks');

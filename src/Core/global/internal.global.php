<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@Kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

/**
 * Internal functions
 *
 * @author MArc Schmidt <marc@Kryn.org>
 * @internal
 */

$errorHandlerInside = false;

class InternalErrorException extends Exception {
    protected $code;
    protected $line;
    protected $file;
    protected $message;
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param integer $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

}

/** proxy */
function coreUtilsErrorHandler($pErrorCode, $pErrorStr, $pFile, $pLine)
{
    $exception = new InternalErrorException();
    $exception->setCode($pErrorCode);
    $exception->setMessage($pErrorStr);
    $exception->setFile($pFile);
    $exception->setLine($pLine);
    \Core\Utils::exceptionHandler($exception);
}

function coreUtilsExceptionHandler($pException)
{
    \Core\Utils::exceptionHandler($pException);
}

function coreUtilsShutdownHandler()
{
    chdir(PATH);
    if (\Core\Kryn::getClient()) {
        \Core\Kryn::getClient()->syncStore();
    }

    if (\Core\Kryn::getAdminClient() && \Core\Kryn::getAdminClient() != \Core\Kryn::getClient()) {
        \Core\Kryn::getAdminClient()->syncStore();
    }

    \Core\Utils::shutdownHandler();
}

/**
 * Deactivate magic quotes
 */
if (get_magic_quotes_gpc()) {
    function magicQuotes_awStripslashes(&$value, $key)
    {
        $value = stripslashes($value);
    }

    $gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    array_walk_recursive($gpc, 'magicQuotes_awStripslashes');
}

/**
 * Handles errors and store it to database log.
 *
 * @param  [type]  $pCode [description]
 * @param  [type]  $pMsg  [description]
 * @param  boolean $pFile [description]
 * @param  boolean $pLine [description]
 *
 * @return [type]         [description]
 */
function errorDbHandler($pCode, $pMsg, $pFile = false, $pLine = false)
{
    global $errorHandlerInside, $client, $cfg;

    if ($errorHandlerInside) {
        return;
    }
    if ($pCode == 8) {
        return;
    }

    $errorHandlerInside = true;
    $username = $client->user['username'] ? $client->user['username'] : 'Unknown';
    $ip = $_SERVER['REMOTE_ADDR'];

    $msg =
        '[' . date(
            'd.m.y H:i:s'
        ) . '] (' . $ip . ') ' . $username . ", $pCode: $pMsg" . (($pFile) ? " in $pFile on $pLine\n" : '') .
            "\n";

    if (array_key_exists('krynInstaller', $GLOBALS) && $GLOBALS['krynInstaller'] == true) {
        @error_log($msg, 3, 'install.log');

        return;
    }

    if ($cfg['logErrors'] == '1') {

        @error_log($msg, 3, $cfg['logErrorsFile']);

    } else {

        if (php_sapi_name() == "cli") {

            print $msg;

        } else {

            $username = $client->user['username'];
            $pCode = preg_replace('/\W/', '-', $pCode);
            $msg = htmlspecialchars($pMsg);

            dbInsert(
                'system_log',
                array(
                     'date' => time(),
                     'ip' => $ip,
                     'username' => $username,
                     'code' => $pCode,
                     'message' => htmlspecialchars($pMsg)
                )
            );
        }
    }
    $errorHandlerInside = false;

}

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

/** proxie */
function coreUtilsErrorHandler($pErrorCode, $pErrorStr, $pFile, $pLine)
{
    \Core\Utils::errorHandler($pErrorCode, $pErrorStr, $pFile, $pLine);
}

function coreUtilsExceptionHandler($pException)
{
    \Core\Utils::exceptionHandler($pException);
}

function coreUtilsShutdownHandler()
{
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

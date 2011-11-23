<?php


/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */



/**
 * Internal functions
 * 
 * @author MArc Schmidt <marc@kryn.org>
 * @internal
 */

$errorHandlerInside = false;



function kryn_shutdown(){
    global $client;

    $client->syncStore();

    if( $adminClient != $client && $adminClient ){
        $adminClient->syncStore();
    }

}



/**
 * Deactivate magic quotes
 */
if (get_magic_quotes_gpc()) {
    function magicQuotes_awStripslashes(&$value, $key) {$value = stripslashes($value);}
    $gpc = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    array_walk_recursive($gpc, 'magicQuotes_awStripslashes');
}






function errorHandler( $pCode, $pMsg, $pFile = false, $pLine = false ){
    global $errorHandlerInside, $client, $cfg;

    if( $errorHandlerInside ) return;
    if( $pCode == 8 ) return;
    
    $errorHandlerInside = true;
    $username = $client->user['username']?$client->user['username']:'Unknown';
    
    $msg = '['.date('d.m.y H:i:s').'] '.$username." - $pCode: $pMsg".(($pFile)?" in $pFile on $pLine\n":'')."\n";
    $msg = htmlspecialchars($msg);
    
    if( array_key_exists('krynInstaller', $GLOBALS) && $GLOBALS['krynInstaller'] == true ){
	    @error_log($msg, 3, 'install.log');
        return;
    }
    
    if( $cfg['log_errors'] == '1' ){

	   @error_log($msg, 3, $cfg['log_errors_file']);

    } else {
    
        $ip = $_SERVER['REMOTE_ADDR'];
        $username = $user->user['username'];
        $pCode = preg_replace('/\W/', '-', $pCode);
        $msg = htmlspecialchars($pMsg);
    
        if( !class_exists('database') )
        	die('Error in boot: '.$pCode.': '.$pMsg.' '.$pFile.':'.$pLine);
    	
        database::$hideSql = true;
        $qry = dbInsert('system_log', array(
            'date' => time(),
            'ip' => $ip,
            'username' => $username,
            'code' => $pCode,
            'message' => htmlspecialchars($pMsg)
        ));
        database::$hideSql = false;
    }

}



/**
 * Kryn exception handler
 * @internal
 */
function kExceptionHandler( $pException ){
	if( $pException )
		klog('php exception', $pException );
}


?>

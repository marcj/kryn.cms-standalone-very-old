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
 * Index.php
 *
 * @author MArc Schmidt <marc@kryn.org>
 */

if ($_SERVER['REQUEST_URI'] == '/test') {

    error_log(print_r($_SERVER, true));
    function encode($text) {
        // 0x1 text frame (FIN + opcode)
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($text);

        if($length <= 125)
            $header = pack('CC', $b1, $length);
        elseif($length > 125 && $length < 65536)
            $header = pack('CCS', $b1, 126, $length);
        elseif($length >= 65536)
            $header = pack('CCN', $b1, 127, $length);

        return $header.$text;
    }

    $key = $_SERVER['HTTP_SEC_WEBSOCKET_KEY'];
    $acceptKey = $key.'258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
    $acceptKey = base64_encode(sha1($acceptKey, true));

    $upgrade = "HTTP/1.1 101 Switching Protocols\n".
        "Upgrade: websocket\n".
        "Connection: Upgrade\n".
        "Sec-WebSocket-Accept: $acceptKey".
        "\n\n";

    foreach (explode("\n", $upgrade) as $line) {
        header($line);
    }
    error_log($upgrade);

    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    ob_implicit_flush(1);
    ob_flush();
    flush();

    sleep(10);

    echo encode("hi was geht ?");
    ob_flush();
    flush();
    sleep(10);
    echo encode("hi was geht 2?");
    ob_flush();
    flush();
    sleep(10);

    error_log('exit');
    exit;
}

//load main config, setup some constants and check some requirements.
require(__DIR__.'/../src/Core/bootstrap.php');

Core\Kryn::checkStaticCaching();

//attach error handler, init propel, load module configs, initialise main controllers and setup the autoloader.
require(__DIR__.'/../src/Core/bootstrap.startup.php');

//Setup the HTTPKernel.
Core\Kryn::setupHttpKernel();

//Handle the request.
Core\Kryn::handleRequest();
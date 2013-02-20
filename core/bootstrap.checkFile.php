<?php

/**
 * Filechecker for tiny URLs.
 * tiny URLs are in Kryn URLs to media/ but without media/ in the path.
 *
 * @internal
 * @author MArc Schmidt <marc@Kryn.org>
 */

if (array_key_exists('__url', $_GET)) {
    $pfile = preg_replace('/\.\.+/', '.', trim($_REQUEST['__url']));
    $temp = 'media/';
    $file = false;

    if (file_exists($temp . $pfile)) {
        $file = $temp . $pfile;
    } elseif (file_exists($temp . substr($pfile, 3, strlen($pfile)))) {
        $file = $temp . substr($pfile, 3, strlen($pfile));
    }

    if ($file && !is_dir($file)) {
        $cfg['path'] = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        header("HTTP/1.1 301 Moved Permanently");
        header('Location: ' . $cfg['path'] . $file);
        exit;
    }
}

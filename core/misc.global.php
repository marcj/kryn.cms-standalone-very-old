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
 * Global misc functions
 * @author MArc Schmidt <marc@Kryn.org>
 */


function resizeImageCached($pPath, $pResolution, $pThumb = false, $pFixSide = false) {
    global $cfg;

    $pathPrefix = PATH_MEDIA;
    if(strpos($pPath, '/') === 0)
        $pathPrefix = '';
    $path = str_replace('..', '', $pathPrefix . $pPath);

    $mdate = filemtime($path);

    $cachepath = $cfg['media_cache'] . '/' . Kryn::toModRewrite($path) . Kryn::toModRewrite($pResolution) . $mdate .
                 basename($pPath);

    if (!file_exists($cachepath)) {
        resizeImage($path, $cachepath, $pResolution, $pThumb, $pFixSide);
    }

    return $cachepath;
}


/**
 * Replaces escaped ' back
 *
 * @param string $p
 *
 * @return string Unescaped string
 */
function unesc($p) {
    $p = str_replace("\'", "'", $p);
    return $p;
}


function copyr($source, $dest) {
    if (is_file($source)) {
        return copy($source, $dest);
    }
    if (!is_dir($dest)) {
        mkdir($dest, 0777, true);
    }
    if (is_link($source)) {
        $link_dest = readlink($source);
        return @symlink($link_dest, $dest);
    }
    $dir = dir($source);
    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            if ($dest !== "$source/$entry") {
                copyr("$source/$entry", "$dest/$entry");
            }
        }
        $dir->close();
    }
    return true;
}

function readFolder($pPath) {
    //$pPath must end with /

    $res = array();
    if (is_dir($pPath)) {
        $h = opendir($pPath);
        while (false !== ($file = readdir($h))) {
            if ($file == '.' || $file == '..' || $file == '.svn' || $file == '.DS_Store') continue;
            $path = $pPath . $file;
            if (is_dir($path)) {
                $res[$path . '/'] = readFolder($path . '/');
            } else {
                $res[] = $path;
            }
        }
        closedir($h);
    }
    return $res;
}

function find($pPath, $pRecursive = true) {

    $res = array();
    $items = glob($pPath);
    if (is_array($items)) {
        foreach ($items as $f) {
            if (is_dir($f) && $pRecursive) {
                $res = array_merge($res, find($f . '/*'));
            }
            $res[] = $f;
        }
    }

    return $res;
}

/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * Original at http://recursive-design.com/blog/2008/03/11/format-json-with-php/
 *
 * @param string $json The original JSON string to process.
 *
 * @return string Indented version of the original JSON string.
 */
function json_format($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;

            // If this character is the end of an element,
            // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        } else if ($char == ':' && $outOfQuotes){
            $char .= ' ';
        }

        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        $prevChar = $char;
    }

    return $result;
}


function mkdirr($pathname, $mode = 0775) {
    is_dir(dirname($pathname)) || mkdirr(dirname($pathname), $mode);
    return is_dir($pathname) || @mkdir($pathname, $mode);
}


function delDir($dirName) {
    if (empty($dirName)) {
        return;
    }
    if (file_exists($dirName)) {
        $dir = dir($dirName);
        if ($dir) {
            while ($file = $dir->read()) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($dirName . '/' . $file)) {
                        delDir($dirName . '/' . $file);
                    } else {
                        @unlink($dirName . '/' . $file);
                    }
                }
            }
        }
        @rmdir($dirName . '/' . $file);
    } else {
    }
}


/*
* json_encode ()
*/

if (!function_exists('json_encode')) {
    require('inc/lib/pear/JSON/JSON.php');
    $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
    function json_encode($p = false) {
        global $json;
        return $json->encode($p);
    }

    function json_decode($p = false, $pDisabled = true) {
        global $json;
        return $json->decode($p);
    }
}


/**
 * Sents a http request to $pUrl and returns the result.
 * @source
 *
 * @param string Complete URL
 * @param string If specified the request result will saved to $pToFile
 * @param mixed  If is a array, the key-value pairs will be sent in the post block
 *
 * @return string returns the request result

 */
function wget($pUrl, $pToFile = false, $pPostFiles = false) {

    $parsedurl = @parse_url($pUrl);
    if (empty($parsedurl['host'])) return false;
    $host = $parsedurl['host'];
    $documentpath = empty($parsedurl['path']) ? '/' : $documentpath = $parsedurl['path'];

    if (!empty($parsedurl['query']))
        $documentpath .= '?' . $parsedurl['query'];

    $port = empty($parsedurl['port']) ? 80 : $port = $parsedurl['port'];

    $timeout = 15;
    $fp = fsockopen($host, $port, $errno, $errstr, $timeout);
    if (!$fp)
        return false;

    srand((double)microtime() * 1000000);
    $boundary = "---------------------------" . substr(md5(rand(0, 32000)), 0, 10);
    $data = "--$boundary";

    if ($pPostFiles) {
        if (!is_array($pPostFiles)) $pPostFiles = array($pPostFiles);
        $i = 0;
        foreach ($pPostFiles as $file) {
            $i++;

            $content_file = Kryn::fileRead($file);
            $content_type = mime_content_type($file);
            $data .= "
Content-Disposition: form-data; name=\"file" . ($i) . "\"; filename=\"$file\"
Content-Type: $content_type

$content_file
--$boundary";

        }
    }
    $data .= "--\r\n\r\n";

    if ($pPostFiles) {
        $post = "POST $documentpath HTTP/1.0
Host: $host
User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7
Content-Type: multipart/form-data; boundary=$boundary
Content-Length: " . strlen($data) . "\r\n\r\n";
        fputs($fp, $post . $data);
    } else {
        $post = "GET $documentpath HTTP/1.0
Host: $host
User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; de; rv:1.9.1.7) Gecko/20091221 Firefox/3.5.7
Content-Type: application/x-www-form-urlencoded\r\n\r\n";
        fputs($fp, $post . $data);

    }

    $header = '';
    do {
        $line = chop(fgets($fp));
        $header .= $line . "\n";
    } while (!empty($line) and !feof($fp));


    $result = '';
    while (!feof($fp)) {
        $result .= fgets($fp);
    }
    fclose($fp);

    preg_match('~^HTTP/1\.\d (?P<status>\d+)~', $header, $matches);
    $status = $matches['status'];
    if ($status == 200) { // OK
    } elseif ($status == 204 or $status == 304) {
        return '';
    } elseif (in_array($status, Array(300, 301, 302, 303, 307))) {
        preg_match('~Location: (?P<location>\S+)~', $header, $match);
        $result = wget($match['location'], $pToFile);
    } elseif ($status >= 400) { // Any error
        return false;
    }

    if ($pToFile) {
        $h = fopen($pToFile, "w+");
        if (!$h) return false;
        fputs($h, $result);
    }

    return $result;
}


/**
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 * Parameters are passed by reference, though only for performance reasons. They're not
 * altered by this function.
 *
 * @param array $array1
 * @param mixed $array2
 *
 * @return array
 * @author daniel@danielsmedegaardbuus.dk
 */
function &array_merge_recursive_distinct(array &$array1, &$array2 = null) {
    $merged = $array1;

    if (is_array($array2))
        foreach ($array2 as $key => $val)
            if (is_array($array2[$key]))
                $merged[$key] = is_array($merged[$key]) ? array_merge_recursive_distinct($merged[$key], $array2[$key]) :
                    $array2[$key];
            else
                $merged[$key] = $val;

    return $merged;
}


if (!function_exists('mime_content_type')) {

    function mime_content_type($pPath) {
        if (mime_content_type_for_name($pPath)) {
            return mime_content_type_for_name($pPath);
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $pPath);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }

}

function mime_content_type_for_name($pPath){
    $mime_types = array(

        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'cab' => 'application/vnd.ms-cab-compressed',
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        'rtf' => 'application/rtf',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'xls' => 'application/vnd.ms-excel',
        'doc' => 'application/msword',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ico' => 'image/vnd.microsoft.icon',
    );

    $ext = strtolower(array_pop(explode('.', $pPath)));
    if (array_key_exists($ext, $mime_types))
        return $mime_types[$ext];
    return false;
}

function clearfolder($pFolder) {

    $items = find($pFolder, false);
    foreach ($items as $item) {
        if (is_dir($item))
            deldir($item);
        else
            unlink($item);
    }

}


/* 
 * Resize a image to a fix resolution or to max dimension.
 *
 * @param string|resource $pSource Defines the target. As path or as image resource.
 * @param string|bool     $pTarget Defines the target. As path if your wanna save it down or true if you wanna get it
 *                                 as resource.
 *
 * @param string $pResolution Defines the resolution of the target image. e.g 1024x700, 1500x100, 500x500
 * @param bool   $pThumb      If you want to resize the image to fix resolution (thumbnails)
 * @param string $pFixSide
 *
 * @static
*/
function resizeImage($pSource, $pTarget, $pResolution, $pThumb = false, $pFixSide = '') {

    if (is_resource($pSource)){
        $oriWidth = imagesx($pSource);
        $oriHeight = imagesy($pSource);
        $img = $pSource;
    } else {
        list($oriWidth, $oriHeight, $type) = getimagesize($pSource);
        switch ($type) {
            case 1:
                $imagecreate = 'imagecreatefromgif';
                $imagesave = 'imagegif';
                break;
            case 2:
                $imagecreate = 'imagecreatefromjpeg';
                $imagesave = 'imagejpeg';
                break;
            case 3:
                $imagecreate = 'imagecreatefrompng';
                $imagesave = 'imagepng';
                break;
            default:
                return false;
        }
        $img = $imagecreate($pSource);
    }



    list($newWidth, $newHeight) = explode('x', $pResolution);
    $thumpWidth = $newWidth;
    $thumpHeight = $newHeight;

    //
    // render Thump
    //
    if ($pThumb) {
        $thumpImage = imagecreatetruecolor($thumpWidth, $thumpHeight);
        imagealphablending($thumpImage, false);

        if ($oriWidth > $oriHeight) {

            $ratio = $thumpHeight / ($oriHeight / 100);
            $_width = ceil($oriWidth * $ratio / 100);

            $top = 0;
            if ($_width < $thumpWidth) {
                $ratio = $_width / ($thumpWidth / 100);
                $nHeight = $thumpHeight * $ratio / 100;
                $top = ($thumpHeight - $nHeight) / 2;
                $_width = $thumpWidth;
            }

            $tempImg = imagecreatetruecolor($_width, $thumpHeight);
            imagealphablending($tempImg, false);
            imagecopyresampled($tempImg, $img, 0, 0, 0, 0, $_width, $thumpHeight, $oriWidth, $oriHeight);
            $_left = ($_width / 2) - ($thumpWidth / 2);

            imagecopyresampled($thumpImage, $tempImg, 0, 0, $_left, 0, $thumpWidth, $thumpHeight, $thumpWidth, $thumpHeight);

        } else {
            $ratio = $thumpWidth / ($oriWidth / 100);
            $_height = ceil($oriHeight * $ratio / 100);
            $tempImg = imagecreatetruecolor($thumpWidth, $_height);
            imagealphablending($tempImg, false);
            imagecopyresampled($tempImg, $img, 0, 0, 0, 0, $thumpWidth, $_height, $oriWidth, $oriHeight);
            $_top = ($_height / 2) - ($thumpHeight / 2);
            imagecopyresampled($thumpImage, $tempImg, 0, 0, 0, $_top, $thumpWidth, $thumpHeight, $thumpWidth, $thumpHeight);
        }

        if ($type == 3) {

            imagealphablending($thumpImage, false);
            imagesavealpha($thumpImage, true);
        }

        if ($pTarget === true) return $thumpImage;
        $imagesave($thumpImage, $pTarget);

    } else {

        if ($pFixSide == 'y' || (!$pFixSide && $oriHeight > $oriWidth)) {
            $ratio = $newHeight / ($oriHeight / 100);
            $_width = ceil($oriWidth * $ratio / 100);
            $newImage = imagecreatetruecolor($_width, $newHeight);
            imagealphablending($newImage, false);

            imagecopyresampled($newImage, $img, 0, 0, 0, 0, $_width, $newHeight, $oriWidth, $oriHeight);
        }

        if ($pFixSide == 'x' || (!$pFixSide && $oriHeight < $oriWidth)) {
            $ratio = $newWidth / ($oriWidth / 100);
            $_height = ceil($oriHeight * $ratio / 100);
            $newImage = imagecreatetruecolor($newWidth, $_height);
            imagealphablending($newImage, false);

            imagecopyresampled($newImage, $img, 0, 0, 0, 0, $newWidth, $_height, $oriWidth, $oriHeight);
        }

        if ($type == 3) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }


        if ($pTarget === true) return $newImage;
        $imagesave($newImage, $pTarget);

    }

}


?>
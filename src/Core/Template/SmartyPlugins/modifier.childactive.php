<?php
function smarty_modifier_childactive($string)
{
    if (is_numeric($string)) {
        $rsn = $string;
    } else {
        $rsn = $string['rsn'];
    }

    if ($rsn == kryn::$page['rsn']) {
        return false;
    }

    $url = kryn::pageUrl(kryn::$page['rsn'], false, true);
    $purl = kryn::pageUrl($rsn, false, true);

    $pos = strpos($url, $purl);
    if ($url == '/' || $purl == '/' || $pos === false) {
        return false;
    } else {
        return true;
    }
}

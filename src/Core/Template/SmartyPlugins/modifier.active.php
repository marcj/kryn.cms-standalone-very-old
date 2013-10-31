<?php
function smarty_modifier_active($string)
{
    if (is_numeric($string)) {
        $rsn = $string;
    } else {
        $rsn = $string['rsn'];
    }

    if ($rsn == kryn::$page['rsn']) {
        return true;
    }

    $url = kryn::pageUrl(kryn::$page['rsn'], false, true);
    $purl = kryn::pageUrl($rsn, false, true);

    $pos = strpos($url, $purl);
    if ($url == '/' || $pos != 0 || $pos === false) {
        return false;
    } else {
        return true;
    }
}

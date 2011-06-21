<?php
function smarty_modifier_childactive($string){
    global $kryn;

    if( is_numeric( $string ) )
        $rsn = $string;
    else
        $rsn = $string['rsn'];

    if( $rsn == kryn::$page['rsn'] ) return false;

    $kcache['realUrl'] = kryn::readCache( 'urls' );
    $url = $kcache['realUrl']['rsn'][ 'rsn=' . kryn::$page['rsn'] ] . '/';
    $purl = $kcache['realUrl']['rsn'][ 'rsn=' . $rsn ] . '/';
    
    $pos = strpos( $url, $purl );
    if( $url == '/' || $purl == '/' || $pos === false){
        return false;
    } else {
        return true;
    }
}
?>

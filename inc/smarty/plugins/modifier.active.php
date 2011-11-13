<?php
function smarty_modifier_active($string){

    if( is_numeric( $string ) )
        $rsn = $string;
    else
        $rsn = $string['rsn'];

    if( $rsn == kryn::$page['rsn'] ) return true;
    
    $url = kryn::pageUrl( kryn::$page['rsn'] );
    $purl = kryn::pageUrl( $rsn );

    $pos = strpos( $url, $purl );
    if( $url == '/' || $pos != 0  || $pos === false){
        return false;
    } else {
        return true;
    }
}
?>

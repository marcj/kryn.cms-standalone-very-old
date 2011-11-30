<?php

require_once('inc/lib/smarty/plugins/function.krynContent.php');

function smarty_function_slot( $params, &$smarty ){
    return smarty_function_krynContent( $params, $smarty );
}
?>
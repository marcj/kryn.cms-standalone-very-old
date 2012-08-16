<?php
function smarty_function_navigation($params, &$smarty){
        return Core\Navigation::get( $params );
}
?>

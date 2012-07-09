<?php
function smarty_function_navigation($params, &$smarty){
        return krynNavigation::get( $params );
}
?>

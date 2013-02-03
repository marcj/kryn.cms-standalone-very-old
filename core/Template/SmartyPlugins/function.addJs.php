<?php
function smarty_function_addJs($params, &$smarty){
    Core\Kryn::addJs( $params['file'] );
}
?>

<?php
function smarty_function_addCss($params, &$smarty){
    Core\Kryn::addCss( $params['file'] );
}
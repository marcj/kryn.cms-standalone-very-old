<?php
function smarty_function_addCss($params, &$smarty){
    Core\Kryn::getResponse()->addCssFile( $params['file'] );
}
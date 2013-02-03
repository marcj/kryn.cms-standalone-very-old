<?php


function smarty_function_slot( $params, &$smarty ){
    if( getArgv(1) == 'admin' && Core\Kryn::$forceKrynContent != true ){
        return '<div class="kryn_layout_slot" params="'.htmlspecialchars(json_encode($params)).'"></div>';
    }
    return Core\PageController::getSlotHtml($params['id'], $params);
}
?>
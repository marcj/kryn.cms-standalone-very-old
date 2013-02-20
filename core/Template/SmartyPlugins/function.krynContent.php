<?php
function smarty_function_krynContent( $params, &$smarty )
{
    if (Core\Kryn::isEditMode()) {
        return '<div class="ka-slot" params="'.htmlspecialchars(json_encode($params)).'"></div>';
    }

    return Core\PageController::getSlotHtml($params['id'], $params);

}

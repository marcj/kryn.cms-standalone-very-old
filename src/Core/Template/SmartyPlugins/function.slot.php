<?php

function smarty_function_slot($params, &$smarty)
{
    if (Core\Kryn::isEditMode()) {
        return '<div class="ka-slot" params="' . htmlspecialchars(json_encode($params)) . '"></div>';
    }

    return \Core\Render::getInstance()->getRenderedSlot($params['id'], $params);
}

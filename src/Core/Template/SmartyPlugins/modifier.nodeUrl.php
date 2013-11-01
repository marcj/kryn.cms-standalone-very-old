<?php
function smarty_modifier_nodeUrl($params)
{
    if (!is_array($params)) {
        $t = (int)($params) + 0;
        $params = array('rsn' => $t);
        $id = $t;
    } else {
        $id = $params['rsn'];
    }

    if (is_array($params) && $params['type'] == 1 && $params['link'] + 0 > 0) {
        $id = $params['link'];
    }

    if (!$id) {
        return '';
    }

    return Core\Kryn::pageUrl($id);
}

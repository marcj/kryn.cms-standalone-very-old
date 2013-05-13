<?php
function smarty_function_pageUrl($params, &$smarty)
{
    return Core\Kryn::pageUrl($params['id']);
}

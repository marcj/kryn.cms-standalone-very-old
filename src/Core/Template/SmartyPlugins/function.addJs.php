<?php
function smarty_function_addJs($params, &$smarty)
{
    Core\Kryn::getResponse()->addJsFile($params['file'], $params['position'] ? : 'top');
}

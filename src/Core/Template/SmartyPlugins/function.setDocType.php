<?php
function smarty_function_setDocType($params, &$smarty)
{
    Core\Kryn::setDocType($params['value']);
}

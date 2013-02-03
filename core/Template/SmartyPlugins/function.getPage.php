<?php
function smarty_function_getPage($params, &$smarty){

        $tId = $params['name'];
        $rsn = $params['id']+0;
        
        $page =& Core\Kryn::getPage( $rsn );
        tAssign( $tId, $page );
        return $page;
}
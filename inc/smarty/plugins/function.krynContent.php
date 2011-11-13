<?php
function smarty_function_krynContent( $params, &$smarty ){

        if( getArgv(1) == 'admin' && kryn::$forceKrynContent != true ){
            return '<div class="kryn_layout_slot" params="'.htmlspecialchars(json_encode($params)).'"></div>';
        }
        return krynHtml::renderContents( kryn::$contents[$params['id']], $params);
        
}
?>
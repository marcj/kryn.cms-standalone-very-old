<?php
function smarty_function_content( $params, &$smarty ){

        if( getArgv(1) == 'admin'  ){
            return '<div class="kryn_layout_content" params="'.htmlspecialchars(json_encode($params)).'"></div>';
        }
        
        return krynHtml::renderContents( kryn::$contents[$params['id']], $params);
        
}
?>
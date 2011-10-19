<?php
function smarty_function_content( $params, &$smarty ){

        if( getArgv(1) == 'admin'  ){
            return '<div class="kryn_layout_content" params="'.htmlspecialchars(json_encode($params)).'"></div>';
        }
        
        return kryn::renderContents( kryn::$contents[$params['id']], $params);
        
        //FRONTEND\
        
        //$content = kryn::$currentContent[ $params['id'] ];
        
        //print kryn::printContent( $content );
        
        //switch($content['type']) ...
        
        
        
        //Wenn {content} in layout ist, dann anhand der $params[id]
        
        //Wenn {content} in layoutElementsTemplate ist, dann anhand der kryn::$currentContent
        
}
?>
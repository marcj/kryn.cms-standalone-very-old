<?php

function smarty_function_page( $params, &$smarty ){


    $params['withRessources'] = $params['withRessources'] ? true : false;
    
    if( $params['id']+0 > 0 )
        return krynHtml::renderPageContents( $params['id'], $params['slot'], $params );
    else 
        return '';
        
}

?>
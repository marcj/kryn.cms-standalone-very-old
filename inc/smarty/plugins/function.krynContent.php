<?php
function smarty_function_krynContent( $params, &$smarty ){

        if( getArgv(1) == 'admin' && kryn::$forceKrynContent != true ){
            $return = "{slot";
            foreach( $params as $key => $val ){
                $return .= ' '.$key.'="'.str_replace('"', '\"', $val).'"';
            }
            $return .= "}";
            
            return $return;
        }
        return kryn::renderContents( kryn::$contents[$params['id']], $params);
        
}
?>
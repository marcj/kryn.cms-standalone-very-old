<?php
function smarty_function_kryn($params, &$smarty){
        global $modules, $user;

        $module = $params['module'];
        $method = $params['plugin'];

        switch($module){
        case 'navigation':
            print krynNavigation::plugin( $params );
            break;
        case 'template':
            print tpl::plugin( $params['get'] );
            break;
        case 'page':
            return kryn::renderPageContents( $params['id'], $params['slot'], $params );
            break;
        default:

            if( method_exists($modules[$module], $method) ){
                print $modules[$module]->$method( $params );
            } else {
                print sprintf(t('Can not found method %s of %s.'), $method, $module);
            }
        }
}

?>

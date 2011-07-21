<?php
function smarty_modifier_pageIcon($page){
    global $cfg;

    print adminPages::getIcons( $page['rsn'] );
    return;

    #return $cfg['path'] . 'inc/template/admin/images/icons/'.$png.'.png';
}
?>

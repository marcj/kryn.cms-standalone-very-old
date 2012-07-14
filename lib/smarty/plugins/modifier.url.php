<?php

function smarty_modifier_url($string){

    return Core\Kryn::toModRewrite($string);
}

?>

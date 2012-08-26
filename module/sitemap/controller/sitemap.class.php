<?php

class sitemap extends krynModule {
    

    public function defaultSitemap( $pConf ){
    
        kryn::addCss('sitemap/css/'.$pConf['template'].'.css');
        return tFetch('sitemap/frontend/'.$pConf['template'].'.tpl');
    }
}

?>

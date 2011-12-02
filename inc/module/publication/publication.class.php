<?php

class publication extends krynModule 
{
    public static function newsDetailFixed( $pConf ){
        $_REQUEST['e2'] = $pConf['news_rsn'];
        require_once(PATH_MODULE.'publication/publicationNews.class.php');
        return publicationNews::itemDetail( $pConf );
    }
    
    public static function newsList( $pConf ){
        // Check if RSS is requested
        if($pConf['enableRss'] && getArgv('publication_rss')+0 == 1)
            self::rssList($pConf); // rssList calls die(), no return needed
        
        require_once(PATH_MODULE.'publication/publicationNews.class.php');
        return publicationNews::itemList( $pConf );
    }

    public static function newsDetail( $pConf ){
        require_once( PATH_MODULE.'publication/publicationNews.class.php');
        return publicationNews::itemDetail( $pConf );
    }

    public function categoryList( $pConf ){

        $categories = implode($pConf['category_rsn'], ",");
        tAssign('pConf', $pConf);
        
        if( count($categories) > 0 )
            $where = " category_rsn IN ($categories) AND ";
        
        $cacheKey = 'publicationCategoryList_'.md5($where);
        
        $categoryItems =& kryn::getCache( $cacheKey );

        if( !$categoryItems ){
            $sqlCount = "
                SELECT
                    MAX(c.rsn) as rsn, MAX(c.url) as url, 
                    MAX(c.title) as title, count(n.rsn) as count
                FROM %pfx%publication_news n, %pfx%publication_news_category c
                WHERE
                 n.category_rsn = c.rsn AND deactivate = 0 GROUP BY category_rsn";
            $categoryItems = dbExfetch( $sqlCount, -1 );
            kryn::setCache( $cacheKey, $categoryItems );
        }
        
        if( count($categoryItems) >0 && !$categoryItems[0]['url'] ){            
            //compatibility
            $categories = dbTableFetch('publication_news_category');
            foreach( $categories as $category ){
                dbUpdate('publication_news_category', array('rsn'=>$category['rsn']), array(
                    'url' => kryn::toModRewrite( $category['title'] )
                ));
            }
            kryn::deleteCache( $cacheKey );
            return self::categoryList( $pConf );
        }

        tAssignRef('categories', $categoryItems);
        
        return tFetch('publication/categoryList/'.$pConf['template'].'.tpl');
    
    }
    
    public function getOrderDirectionOptions( $pFields ){
        $array['desc'] = _l('Descending');
        $array['asc'] = _l('Ascending');
        return $array;
    }
    
    
    public static function rssList( $pConf )
    {
        require_once( PATH_MODULE.'publication/publicationNews.class.php');
        return publicationNews::rssList($pConf);
    }

}


?>

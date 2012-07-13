<?php

class publication extends krynModule {
    public static function newsDetailFixed($pConf) {
        $_REQUEST['e2'] = $pConf['news_id'];
        require_once(PATH_MODULE . 'publication/publicationNews.class.php');
        return publicationNews::itemDetail($pConf);
    }

    public static function newsList($pConf) {
        // Check if RSS is requested
        if ($pConf['enableRss'] && getArgv('publication_rss') + 0 == 1)
            self::rssList($pConf); // rssList calls die(), no return needed

        require_once(PATH_MODULE . 'publication/publicationNews.class.php');
        return publicationNews::itemList($pConf);
    }

    public static function newsDetail($pConf) {
        require_once(PATH_MODULE . 'publication/publicationNews.class.php');
        return publicationNews::itemDetail($pConf);
    }

    public function categoryList($pConf) {

        $categories = implode($pConf['category_id'], ",");
        tAssign('pConf', $pConf);

        if (count($categories) > 0)
            $where = " category_id IN ($categories) AND ";

        $cacheKey = 'publicationCategoryList_' . md5($where);

        $categoryItems =& kryn::getCache($cacheKey);

        if (!$categoryItems) {
            $sqlCount = "
                SELECT
                    MAX(c.id) as id, MAX(c.url) as url,
                    MAX(c.title) as title, count(n.id) as count
                FROM %pfx%publication_news n, %pfx%publication_news_category c
                WHERE
                 n.category_id = c.id AND deactivate = 0 GROUP BY category_id";
            $categoryItems = dbExfetch($sqlCount, -1);
            kryn::setCache($cacheKey, $categoryItems);
        }

        if (count($categoryItems) > 0 && !$categoryItems[0]['url']) {
            //compatibility
            $categories = dbTableFetch('publication_news_category');
            foreach ($categories as $category) {
                dbUpdate('publication_news_category', array('id' => $category['id']), array(
                    'url' => kryn::toModRewrite($category['title'])
                ));
            }
            kryn::deleteCache($cacheKey);
            return self::categoryList($pConf);
        }

        tAssignRef('categories', $categoryItems);

        return tFetch('publication/categoryList/' . $pConf['template'] . '.tpl');

    }


    public static function rssList($pConf) {
        require_once(PATH_MODULE . 'publication/publicationNews.class.php');
        return publicationNews::rssList($pConf);
    }

}


?>

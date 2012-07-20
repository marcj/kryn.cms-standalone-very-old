<?php

class publicationNews {

    public static function itemDetail($pConf) {
        global $user, $client;


        $replaceTitle = $pConf['replaceTitle'] + 0 == 1;
        $categoryRsn = $pConf['category_id'];
        $allowComments = $pConf['allowComments'] + 0;
        $template = $pConf['template'];


        $id = getArgv('e2') + 0;

        if ($id > 0) {

            // Create category where clause
            $whereCategories = "";
            if (count($categoryRsn))
                $whereCategories = "AND n.category_id IN (" . implode(",", $categoryRsn) . ")";

            if (getArgv('kryn_framework_code') == 'publication/newsDetail' &&
                getArgv('kryn_framework_version_id') && kryn::checkUrlAccess('admin/publication/news/edit')
            ) {

                $news =
                    admin::getVersion('publication_news', array('id' => $id), getArgv('kryn_framework_version_id'));

                if ($news) {
                    $category = dbTableFetch('publication_news_category', 'id = ' . $news['category_id'], 1);
                    $news['categoryTitle'] = $category['title'];
                }
            } else {

                // Create query
                $now = time();
                $sql = "
                    SELECT
                        n.*,
                        c.title as category_title,
                        c.url as category_url
                    FROM
                        %pfx%publication_news n,
                        %pfx%publication_news_category c
                    WHERE
                        1=1
                        $whereCategories
                        AND n.deactivate = 0
                        AND c.id = n.category_id
                        AND n.id = $id
                        AND (n.releaseAt = 0 OR n.releaseAt <= $now)
                ";

                $news = dbExfetch($sql, 1);
            }

            // Is it a valid news row
            $isNews = $news !== false;
            tAssign('isNews', $isNews);

            if ($isNews) {
                // Set title if allowed
                if ($replaceTitle)
                    kryn::$page['title'] = $news['title'];

                // Handle comment calls
                if ($allowComments && $news['deactivateComments'] + 0 == 0) {
                    if (getArgv('publication-add-comment') + 0) {
                        $name = $user->user_id == 0 ? getArgv('name', 1) : $user->user['username'];
                        if ($name != "") {
                            dbInsert(
                                'publication_comments',
                                array(
                                    'parent_id' => $id,
                                    'owner_id' => $user->user_id,
                                    'owner_username' => $name,
                                    'created' => time(),
                                    'ip' => $_SERVER['REMOTE_ADDR'],
                                    'session_id' => $client->token,
                                    'subject',
                                    'website',
                                    'email',
                                    'message'
                                )
                            );
                            self::updateCommentsCount($id);
                            $news['commentscount']++;
                        }
                    }

                    // Default itemsPerPage if not set
                    $itemsPerPage = $pConf['itemsPerPage'] + 0;
                    if (!$itemsPerPage)
                        $itemsPerPage = 15;

                    // From which comment page are we looking?
                    $page = getArgv('e3') + 0;
                    if (!$page)
                        $page = 1;

                    // Default max pages if not set
                    $maxPages = $pConf['maxPages'] + 0;

                    // Set comments start
                    $start = $itemsPerPage * $page - $itemsPerPage;

                    // Count comments
                    $sqlCount = "
                        SELECT
                            count(*) as commentsCount
                        FROM
                            %pfx%publication_comments
                        WHERE
                            parent_id = $id
                    ";
                    $countRow = dbExfetch($sqlCount, 1);
                    $count = $countRow['commentsCount'];
                    tAssign('commentsCount', $count);

                    // Set amount of pages
                    $pages = 1;
                    if ($count && $itemsPerPage)
                        $pages = ceil($count / $itemsPerPage);

                    // Update max pages when needed
                    if (!$maxPages)
                        $pConf['maxPages'] = $pages;

                    tAssign('pages', $pages);
                    tAssign('currentCommentPage', $page);

                    // Fetch comments
                    $comments =
                        dbTableFetch('publication_comments', -1, "parent_id = $id LIMIT $itemsPerPage OFFSET $start");
                    if ($comments !== false)
                        tAssign('comments', $comments);
                }

                // Retrieve content of news
                $json = json_decode($news['content'], true);
                if ($json && $json['contents'] && file_exists(PATH_MEDIA . $json['template'])) {
                    $oldContents = kryn::$contents;
                    kryn::$contents = $json['contents'];
                    $news['content'] = tFetch($json['template']);
                    kryn::$contents = $oldContents;
                }

                // Retrieve intro of news
                $json = json_decode($news['intro'], true);
                if ($json && $json['contents'] && file_exists(PATH_MEDIA . $json['template'])) {
                    $oldContents = kryn::$contents;
                    kryn::$contents = $json['contents'];
                    $news['intro'] = tFetch($json['template']);
                    kryn::$contents = $oldContents;
                }

                tAssign('news', $news);
            }
            else
                tAssign('isNews', false); // Not found (or not visible)
        }
        else
            tAssign('isNews', false); // Not a valid news item

        // Assign config and load template
        tAssign('pConf', $pConf);
        kryn::addCss("publication/news/css/detail/$template.css");
        return tFetch("publication/news/detail/$template.tpl");
    }

    public static function updateCommentsCount($pNewsRsn) {
        $comments =
            dbExfetch('SELECT count(*) as comcount FROM %pfx%publication_comments WHERE parent_id = ' . $pNewsRsn);
        dbUpdate('publication_news', array('id' => $pNewsRsn), array('commentscount' => $comments['comcount']));
    }

    public static function itemList($pConf) {

        // Get important variables from config
        $categoryRsn = $pConf['category_id'];
        $itemsPerPage = $pConf['itemsPerPage'] + 0;
        $maxPages = $pConf['maxPages'] + 0;
        $order = $pConf['order'];
        $orderDirection = $pConf['orderDirection'];
        $template = $pConf['template'];

        // Create category where clause
        $whereCategories = "";
        if (count($categoryRsn))
            $whereCategories = "AND category_id IN (" . implode(",", $categoryRsn) . ")";

        if (getArgv('e1') == 'category') {

            $whereCategories = "AND c.url = '" . getArgv('e2', 1) . "'";

            $category =
                dbExfetch("SELECT title FROM %pfx%publication_news_category WHERE url = '" . getArgv('e2', 1) . "'");
            tAssign('category_title', $category['title']);

            if (!$category) {
                //compatibility
                $categories = dbTableFetch('publication_news_category');
                foreach ($categories as $category) {
                    dbUpdate('publication_news_category', array('id' => $category['id']), array(
                        'url' => kryn::toModRewrite($category['title'])
                    ));
                }

                $category = dbExfetch(
                    "SELECT title FROM %pfx%publication_news_category WHERE url = '" . getArgv('e2', 1) . "'");
                tAssign('category_title', $category['title']);
            }


        }

        // Get current page
        $page = getArgv('e1') + 0;
        if (!$page)
            $page = 1;

        // If items per page is not set, make it default value
        if (!$itemsPerPage)
            $itemsPerPage = 5;

        // Set start of lookup
        $start = $itemsPerPage * $page - $itemsPerPage;

        // Create order by
        $orderBy = "releaseDate DESC";
        if ($order)
            $orderBy = "$order $orderDirection";

        $cacheKey = 'publicationNewsList_' . kryn::$page['id'] . '-' .
                    md5($template . '.' . $whereCategories . '.' . $start . '.' . $itemsPerPage . '.' . $orderBy);

        if (kryn::$domainProperties['publication']['cache'] == 1) {

            $cached =& kryn::getCache($cacheKey . '-full');
            if ($cached) {

                kryn::addCss($cached['css']);
                kryn::addJs($cached['js']);
                return $cached['html'];
                return;
            }
        }


        $list =& kryn::getCache($cacheKey);

        if (!$list) {
            // Create query
            $now = time();
            $sql = "
                SELECT
                    n.*,
                    c.title as category_title,
                    c.url as category_url
                FROM
                    %pfx%publication_news n,
                    %pfx%publication_news_category c
                WHERE
                    1=1
                    $whereCategories
                    AND n.deactivate = 0
                    AND c.id = n.category_id
                    AND (n.releaseat = 0 OR n.releaseat <= $now)
                ORDER BY $orderBy
                LIMIT $itemsPerPage OFFSET $start
            ";
            $list = dbExfetch($sql, -1);

            // Create count query
            $sqlCount = "
                SELECT
                    count(*) as newscount
                FROM
                    %pfx%publication_news n
                WHERE
                    1=1
                    $whereCategories
                    AND deactivate = 0
                    AND (n.releaseat = 0 OR n.releaseat <= $now)
            ";
            $countRow = dbExfetch($sqlCount, 1);
            $count = $countRow['newscount'];
            tAssign('count', $count);

            // Set pages
            $pages = 1;
            if ($count && $itemsPerPage)
                $pages = ceil($count / $itemsPerPage);

            if (!$maxPages)
                $pConf['maxPages'] = $pages;

            // Assign pages to template
            tAssign('pages', $pages);
            tAssign('currentNewsPage', $page);

            kryn::setCache($cacheKey, $list);

        }

        if (count($list) > 0 && !$list[0]['category_title']) {
            //compatibility
            $categories = dbTableFetch('publication_news_category');
            foreach ($categories as $category) {
                dbUpdate('publication_news_category', array('id' => $category['id']), array(
                    'url' => kryn::toModRewrite($category['title'])
                ));
            }
            kryn::deleteCache($cacheKey);
            return self::itemList($pConf);
        }


        // Process news items
        foreach ($list as &$news)
        {
            // Retrieve content of news
            $json = json_decode($news['content'], true);
            if ($json && $json['contents'] && file_exists(PATH_MEDIA . $json['template'])) {
                $oldContents = kryn::$contents;
                kryn::$contents = $json['contents'];
                $news['content'] = tFetch($json['template']);
                kryn::$contents = $oldContents;
            }

            // Retrieve intro of news
            $json = json_decode($news['intro'], true);
            if ($json && $json['contents'] && file_exists(PATH_MEDIA . $json['template'])) {
                $oldContents = kryn::$contents;
                kryn::$contents = $json['contents'];
                $news['intro'] = tFetch($json['template']);
                kryn::$contents = $oldContents;
            }
        }

        tAssignRef('items', $list);
        tAssignRef('pConf', $pConf);

        if ($template != 'default') {
            kryn::addCss("publication/news/css/list/$template.css");
            kryn::addJs("publication/news/js/list/$template.js");
        }

        $res = tFetch("publication/news/list/$template.tpl");


        if (kryn::$domainProperties['publication']['cache'] !== 0) {

            kryn::setCache($cacheKey . '-full', array(
                'css' => kryn::$cssFiles,
                'js' => kryn::$jsFiles,
                'html' => $res
            ), 60);

        }

        return $res;
    }

    public static function rssList($pConf) {
        // Fetch important vars from conf var
        $categoryRsn = $pConf['category_id'];
        $itemsPerPage = $pConf['itemsPerPage'] + 0; // Make sure it's set
        $template = $pConf['rssTemplate'];

        // Create category where clause
        $whereCategories = "";
        if (count($categoryRsn))
            $whereCategories = "AND n.category_id IN (" . implode(",", $categoryRsn) . ") ";

        // Set items per page to default when not set
        if ($itemsPerPage < 1)
            $itemsPerPage = 10; // Default

        // Create query
        $now = time();
        $sql = "
            SELECT
                n.*, 
                c.title as category_title,
                c.url as category_url
            FROM
                %pfx%publication_news n, 
                %pfx%publication_news_category c 
            WHERE
                    1=1
                $whereCategories 
                AND n.deactivate = 0
                AND n.category_id = c.id
                AND (n.releaseAt = 0 OR n.releaseAt <= $now)
            ORDER BY
                releaseDate DESC
            LIMIT $itemsPerPage";

        $list = dbExFetch($sql, DB_FETCH_ALL);

        $hasItems = $list !== false;
        tAssign('hasItems', $hasItems); // Tells template if the query failed or not

        if ($hasItems) {
            foreach ($list as $index => $item)
            {
                $list[$index]['title'] = strip_tags(html_entity_decode($item['title'], ENT_NOQUOTES, 'UTF-8'));

                $json = json_decode($item['intro'], true);
                if ($json && $json['contents'] && file_exists(PATH_MEDIA . $json['template'])) {
                    $oldContents = kryn::$contents;
                    kryn::$contents = $json['contents'];
                    $item['intro'] = tFetch($json['template']);
                    kryn::$contents = $oldContents;
                }

                $list[$index]['intro'] = strip_tags(html_entity_decode($item['intro'], ENT_NOQUOTES, 'UTF-8'));
            }
        }

        // Assign list to template
        tAssign('items', $list);
        // Assign config to template
        tAssign('pConf', $pConf);

        // Clear current output
        @ob_end_clean();

        // Assign accept language to template
        tAssign('local', substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 5));

        // Set header as XML
        header("Content-type: text/xml");

        // Ouput formatted XML list and die
        echo tFetch("publication/news/rss/$template.tpl");
        die();
    }

}

?>

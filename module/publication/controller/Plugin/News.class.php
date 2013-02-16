<?php

namespace Publication\Plugin;

use Core\Controller;
use Publication\NewsQuery;

class News extends Controller {

    /**
     * News listing
     *
     * @param array $options
     * @param int   $page
     * @return string
     */
    public function listing(array $options, $page = 1){

        $page     = (int) $page ?: 1;
        $cacheKey = 'publication/news/list/'.$page;
        $view     = 'publication/news/list/'.$options['template'];


        if (!$this->isValidCache($cacheKey)){

        }

        return $this->renderCached($cacheKey, $view, function(){

            $items = NewsQuery::create()
                ->paginate($page, $options['itemsPerPage'] ?: 10)
                ->getResults()
                ->toArray(null, null, \BasePeer::TYPE_STUDLYPHPNAME);

            return array('items' => $items);

        });
    }

}
<?php

namespace Publication\Plugin;

use Publication\NewsQuery;
use Core\Controller;
use Core\Kryn;

class News extends Controller
{
    /**
     * News listing
     *
     * @param  array  $options
     * @param  int    $page
     * @return string
     */
    public function listing(array $options, $page = 1)
    {
        $page     = (int) $page ?: 1;
        $cacheKey = 'publication/news/list/'.$page;
        $view     = 'publication/news/list/'.$options['template'];

        return $this->renderFullCached($cacheKey, $view, function() use ($page, $options) {

            $paginate = NewsQuery::create()->paginate($page, $options['itemsPerPage'] ?: 10);

            if ($page > $paginate->getLastPage()) {
                return null;
                //because if we return here NULL the `renderFullCached` function returns NULL as well,
                //and this means that we say to the HTTPKernel 'we (this route) are not responsible' for the current request.
                //the HTTPKernel removes this current route then from the RouteCollection
                //and starts a new SUB_REQUEST. Maybe another route is here that handles this request.
            }

            $items    = $paginate
                ->getResults()
                ->toArray(null, null, \BasePeer::TYPE_STUDLYPHPNAME);

            foreach ($items as &$item) {
                $item['url'] = ($options['detailPage'] ? Kryn::getPageUrl($options['detailPage']) : '') . $item['uri'];
            }

            return array(
                'items'     => $items,
                'options'   => $options
            );

        });
    }

}

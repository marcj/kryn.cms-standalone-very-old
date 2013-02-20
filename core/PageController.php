<?php

namespace Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PageController extends Controller {

    /**
     * Cache for getPublicUrl().
     *
     * @var array
     */
    private static $cachedUrls = array();

    /**
     * Cache for the slot contents.
     *
     * @var array
     */
    private static $slotContents = array();

    /**
     * Build the page and return the Response of Core\Kryn::getResponse().
     *
     * @return Response
     */
    public function handle(){

        //is link
        if (Kryn::$page->getType() == 1) {
            $to = Kryn::$page->getLink();
            if (!$to) {
                Kryn::internalError(t('Redirect failed'), tf('Current page with title %s has no target link.', Kryn::$page->getTitle()));
            }

            if ($to+0 > 0) {
                return new RedirectResponse(self::getPageUrl($to), 301);
            } else {
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: $to");
                return new RedirectResponse($to, 301);
            }
        }

        return Kryn::getResponse();
    }


    public static function getSlotContents($pPageId, $pSlotId){

        $cacheKey     = 'core/contents/'.$pPageId.'.'.$pSlotId;
        $cache        = Kryn::getFastCache($cacheKey);
        $cacheCreated = Kryn::getCache($cacheKey.'.created');

        if (!$cache || $cache['created'] != $cacheCreated){

            $contents = ContentQuery::create()
                ->filterByNodeId($pPageId)
                ->filterByBoxId($pSlotId)
                ->orderBySortableId()
                ->find();

            $cache['data']    = serialize($contents);
            $cache['created'] = microtime();
            Kryn::setFastCache($cacheKey, $cache);
            Kryn::setCache($cacheKey.'.created', $cache['created']);
        }

        return $contents ?: unserialize($cache['data']);

    }

    public static function getSlotHtml($pSlotId, $pSlotProperties){

        if (!self::$slotContents[$pSlotId])
            self::$slotContents[$pSlotId] = self::getSlotContents(Kryn::$page->getId(), $pSlotId);

        return Render::renderContents(self::$slotContents[$pSlotId], $pSlotProperties);

    }

    /**
     * Returns the public url for the Core\Node object.
     *
     * @param string $pObjectKey
     * @param string $pObjectPk
     * @param array $pPlugin
     * @return string
     */
    public static function getPublicUrl($pObjectKey, $pObjectPk, $pPlugin = null){
        return Node::getUrl($pObjectPk['id']+0);
    }

    /**
     * Returns a permanent(301) redirectResponse object.
     *
     * @return RedirectResponse
     */
    public function redirectToStartPage(){

        $response = new RedirectResponse(Kryn::getBaseUrl(), 301);

        return $response;
    }
}
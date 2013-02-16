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
     * Build the page and return the modified Response of Core\Kryn::getResponse().
     *
     * @param Request $request
     * @return Response
     */
    public function send(Request $request){

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

        return Kryn::getResponse()->setContent($this->buildBody());
    }

    /**
     * Builds the html body of the current page.
     *
     * @return string
     */
    public function buildBody(){

        $page   = Kryn::getPage();

        Kryn::$themeProperties = array();
        $propertyPath = '';

        foreach (Kryn::$themes as $extKey => &$themes) {
            foreach ($themes as $tKey => &$theme) {
                if ($theme['layouts']) {
                    foreach ($theme['layouts'] as $lKey => &$layout) {
                        if ($layout == Kryn::$page->getLayout()) {
                            $propertyPath = $extKey.'/'.$tKey;
                            break;
                        }
                    }
                }
                if ($propertyPath) break;
            }
            if ($propertyPath) break;
        }

        if ($propertyPath) {
            if ($themeProperties = kryn::$domain->getThemeProperties())
                Kryn::$themeProperties = $themeProperties->getByPath($propertyPath);
        }

        $layout = $page->getLayout();

        return $this->render($layout, array(
            'themeProperties' => Kryn::$themeProperties
        ));

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
        return self::getPageUrl($pObjectPk['id']);
    }

    public static function getPageUrl($pPageId){

        if (Kryn::$page && Kryn::$page->getId() == $pPageId){
            $domainId = Kryn::$page->getDomainId();
        } else {
            $domainId = Kryn::getDomainOfPage($pPageId);
        }

        if (!$domainId) {
            return null;
        }

        $cachedUrls = self::$cachedUrls[$domainId];

        if (!$cachedUrls){
            $cachedUrls =& Kryn::getCache('core/node-ids-to-url-' . $domainId);

            if (!$cachedUrls || !$cachedUrls['id']) {
                $cachedUrls = Render::updateUrlCache($domainId);
            }
            self::$cachedUrls[$domainId] = $cachedUrls;
        }

        $url = $cachedUrls['id']['id=' . $pPageId];

        if ($domainId != Kryn::$domain->getId()){
            if ($domainId != Kryn::$domain->getId())
                $domain = Kryn::getDomain($domainId);
            else
                $domain = Kryn::$domain;

            $domainName = $domain->getRealDomain();
            if ($domain->getMaster() != 1) {
                $url = $domainName . $domain->getPath() . $domain->getLang() . '/' . $url;
            } else {
                $url = $domainName . $domain->getPath() . $url;
            }

            $url = 'http' . (Kryn::$ssl ? 's' : '') . '://' . $url;
        }

        if (substr($url, -1) == '/')
            $url = substr($url, 0, -1);

        if ($url == '/')
            $url = '.';

        if (substr($url, -1) == '/')
            $url = substr($url, 0, -1);

        if ($url == '/')
            $url = '.';

        return $url;
    }

    public function redirectToStartPage(){

        $response = new RedirectResponse(Kryn::getBaseUrl(), 301);

        return $response;
    }
}
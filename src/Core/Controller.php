<?php

namespace Core;

use Symfony\Component\HttpFoundation\Response;

/**
 * Controller class for controllers.
 *
 * Provides view methods, if you want to handle coped view data.
 *
 */
class Controller
{
    /**
     * View data.
     *
     * @var array
     */
    private $viewData = array();

    /**
     * Last checked cache key through isValidCache().
     * This gives us a performance gain since the renderCache() doesn't need
     *
     * @var string
     */
    private $lastCheckedCacheKey = '';

    /**
     * Assign data to a variable inside this controller.
     * This data is used in $this->render() if you don't pass a data array.
     *
     * @param string $pKey
     * @param mixed  $pValue
     */
    public function assign($pKey, $pValue)
    {
        $this->viewData[$pKey] = $pValue;
    }

    /**
     * Assign data by reference to a variable inside this controller.
     * This data is used in $this->render() if you don't pass a data array.
     *
     * @param string $pKey
     * @param mixed  &$pValue
     */
    public function assignByRef($pKey, &$pValue)
    {
        $this->viewData[$pKey] = $pValue;
    }


    /**
     * Returns true if the specified name has a value assigned.
     *
     * @param  string $pKey
     *
     * @return bool
     */
    public function assigned($pKey)
    {
        return null !== $this->viewData[$pKey];
    }


    /**
     * Clears all assigned data.
     */
    public function clearAllAssign()
    {
        $this->viewData = array();
    }


    /**
     * Returns the rendered view output.
     *
     * If you've extended your main controller with this controller,
     * then $pView will be prefixed with the current module key,
     * so that the views of <moduleKey>/views/ will be loaded primarily.
     * If the view does not exists there, it tries to load the view from root.
     *
     * Depending on the file name of the view we use Smarty, Twig, plain PHP or nothing.
     *
     * Examples:
     *    plugin1/default.php           -> php
     *    plugin1/default.tpl           -> none
     *    plugin1/default.html          -> none
     *    plugin1/default.html.smarty   -> smarty
     *    plugin1/default.html.twig     -> twig
     *
     * @param  string $view
     * @param  array  $data Use this data instead of the data assigned through $this->assign()
     *
     * @return string
     */
    public function renderView($view, $data = null)
    {
        $engine = Kryn::getTemplateEngineForFileName($view);

        $view = Kryn::resolvePath($view, 'Views/');

        $html = (string)$engine->render($view, $data ? array_merge($this->viewData, $data) : $this->viewData, $this);
        return Kryn::translate($html);
    }

    public function getViewDir()
    {
        $dir = __DIR__;
        $parts = explode('\\', __NAMESPACE__);
        for ($i = count($parts); $i > 1; $i--) {
            $dir .= '/..';
        }

        return $dir . '/Views';
    }

    public function getViewMTime($view)
    {
        $view = Kryn::resolvePath($view, 'Views/');

        if (!file_exists($view)) {
            throw new \FileNotFoundException(sprintf('File `%s` not found.', $view));
        }
        return filemtime($view);
    }

    /**
     * Returns whether this cache is valid(exists) or not.
     *
     * @param  string  $pCacheKey
     *
     * @return boolean
     */
    public function isValidCache($pCacheKey)
    {
        return Kryn::getDistributedCache($pCacheKey) !== null;
    }

    /**
     * Returns a rendered view. If we find data behind the given cache
     * it uses this data instead of calling $pData. So this function
     * does not cache the whole rendered html. Tho do so use renderFullCache().
     *
     * Example:
     *
     *  return $this->renderCache('myCache', 'plugin1/default.tpl', function(){
     *     return array('items' => heavyDbQuery());
     * });
     *
     * Note: The $pData callable is only called if the cache needs to regenerate.
     * If the callable $pData returns NULL, then this will return NULL, too.
     *
     * @param string         $pCacheKey
     * @param string         $pView
     * @param array|callable $pData     Pass the data as array or a data provider function.
     *
     * @see method `render` to get more information.
     *
     * @return string
     */
    public function renderCached($pCacheKey, $pView, $pData = null)
    {
        $cache = Kryn::getDistributedCache($pCacheKey);
        $mTime = $this->getViewMTime($pView);

        if (!$cache || !$cache['data'] || !is_array($cache) || $mTime != $cache['fileMTime']) {

            $data = $pData;
            if (is_callable($pData)) {
                $data = call_user_func($pData, $pView);
                if ($data === null) {
                    return null;
                }
            }

            $cache = array(
                'data' => $data,
                'fileMTime' => $mTime
            );

            Kryn::setDistributedCache($pCacheKey, $cache);
        }

        return $this->renderView($pView, $cache['data']);

    }

    /**
     * Returns a rendered view. If we find html behind the given cache
     * it returns this directly. This is a couple os ms faster than `renderCached`
     * since the template engine is never used when there's a valid cache.
     *
     * Example:
     *
     *  return $this->renderFullCached('myCache', 'plugin1/default.tpl', function(){
     *     return array('items' => heavyDbQuery());
     * });
     *
     * Note: The $pData callable is only called if the cache needs to regenerate.
     *
     * If the callable $pData returns NULL, then this will return NULL, too, without entering
     * the actual rendering process.
     *
     * @param string         $pCacheKey
     * @param string         $pView
     * @param array|callable $pData     Pass the data as array or a data provider function.
     *
     * @see method `render` to get more information.
     *
     * @return string
     */
    public function renderFullCached($pCacheKey, $pView, $pData = null)
    {
        $cache = Kryn::getDistributedCache($pCacheKey);
        $mTime = $this->getViewMTime($pView);

        if (!$cache || !$cache['content'] || !is_array($cache) || $mTime != $cache['fileMTime']) {

            $oldResponse = clone Kryn::getResponse();

            $data = $pData;
            if (is_callable($pData)) {
                $data = call_user_func($pData, $pView);
                if (null === $data) {
                    //the data callback returned NULL so this means
                    //we aren't the correct controller for the request
                    //or the request contains invalid input
                    return null;
                }
            }

            $content = $this->renderView($pView, $data);
            $response = Kryn::getResponse();
            $diff = $oldResponse->diff($response);

            $cache = array(
                'content' => $content,
                'fileMTime' => $mTime,
                'responseDiff' => $diff
            );

            Kryn::setDistributedCache($pCacheKey, $cache);

        }

        if ($cache['responseDiff']) {
            Kryn::getResponse()->patch($cache['responseDiff']);
        }

        return $cache['content'];
    }

}

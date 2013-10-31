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
     * @param string $key
     * @param mixed  $value
     */
    public function assign($key, $value)
    {
        $this->viewData[$key] = $value;
    }

    /**
     * Assign data by reference to a variable inside this controller.
     * This data is used in $this->render() if you don't pass a data array.
     *
     * @param string $key
     * @param mixed  &$value
     */
    public function assignByRef($key, &$value)
    {
        $this->viewData[$key] = $value;
    }


    /**
     * Returns true if the specified name has a value assigned.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function assigned($key)
    {
        return null !== $this->viewData[$key];
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
     * @param  bool   $translate
     *
     * @return string
     */
    public function renderView($view, $data = null, $translate = true)
    {
        $engine = Kryn::getTemplateEngineForFileName($view);

        $view = Kryn::resolvePath($view, 'Resources/views/');

        $html = (string)$engine->render($view, $data ? array_merge($this->viewData, $data) : $this->viewData, $this);
        return $translate ? Kryn::translate($html) : $html;
    }

    public function getViewDir()
    {
        $dir = __DIR__;
        $parts = explode('\\', __NAMESPACE__);
        for ($i = count($parts); $i > 1; $i--) {
            $dir .= '/..';
        }

        return $dir . '/Resources/views';
    }

    public function getViewMTime($view)
    {
        $view = Kryn::resolvePath($view, 'Resources/views/');

        if (!file_exists($view)) {
            throw new \FileNotFoundException(sprintf('File `%s` not found.', $view));
        }
        return filemtime($view);
    }

    /**
     * Returns whether this cache is valid(exists) or not.
     *
     * @param  string  $cacheKey
     *
     * @return boolean
     */
    public function isValidCache($cacheKey)
    {
        return Kryn::getDistributedCache($cacheKey) !== null;
    }

    /**
     * Returns a rendered view. If we find data behind the given cache
     * it uses this data instead of calling $data. So this function
     * does not cache the whole rendered html. Tho do so use renderFullCache().
     *
     * Example:
     *
     *  return $this->renderCache('myCache', 'plugin1/default.tpl', function(){
     *     return array('items' => heavyDbQuery());
     * });
     *
     * Note: The $data callable is only called if the cache needs to regenerate.
     * If the callable $data returns NULL, then this will return NULL, too.
     *
     * @param string         $cacheKey
     * @param string         $view
     * @param array|callable $data     Pass the data as array or a data provider function.
     *
     * @see method `render` to get more information.
     *
     * @return string
     */
    public function renderCached($cacheKey, $view, $data = null)
    {
        $cache = Kryn::getDistributedCache($cacheKey);
        $mTime = $this->getViewMTime($view);

        if (!$cache || !$cache['data'] || !is_array($cache) || $mTime != $cache['fileMTime']) {

            $data2 = $data;
            if (is_callable($data)) {
                $data2 = call_user_func($data, $view);
                if ($data2 === null) {
                    return null;
                }
            }

            $cache = array(
                'data' => $data2,
                'fileMTime' => $mTime
            );

            Kryn::setDistributedCache($cacheKey, $cache);
        }

        return $this->renderView($view, $cache['data']);

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
     * Note: The $data callable is only called if the cache needs to regenerate.
     *
     * If the callable $data returns NULL, then this will return NULL, too, without entering
     * the actual rendering process.
     *
     * @param string         $cacheKey
     * @param string         $view
     * @param array|callable $data     Pass the data as array or a data provider function.
     * @param bool           $force    Force to bypass the cache and always call $data. For debuggin purposes.
     *
     * @see method `render` to get more information.
     *
     * @return string
     */
    public function renderFullCached($cacheKey, $view, $data = null, $force = false)
    {
        $cache = Kryn::getDistributedCache($cacheKey);
        $mTime = $this->getViewMTime($view);

        if ($force || !$cache || !$cache['content'] || !is_array($cache) || $mTime != $cache['fileMTime']) {

            $oldResponse = clone Kryn::getResponse();

            $data2 = $data;
            if (is_callable($data)) {
                $data2 = call_user_func($data, $view);
                if (null === $data2) {
                    //the data callback returned NULL so this means
                    //we aren't the correct controller for the request
                    //or the request contains invalid input
                    return null;
                }
            }

            $content = $this->renderView($view, $data2);
            $response = Kryn::getResponse();
            $diff = $oldResponse->diff($response);

            $cache = array(
                'content' => $content,
                'fileMTime' => $mTime,
                'responseDiff' => $diff
            );

            Kryn::setDistributedCache($cacheKey, $cache);

        }

        if ($cache['responseDiff']) {
            Kryn::getResponse()->patch($cache['responseDiff']);
        }

        return $cache['content'];
    }

}

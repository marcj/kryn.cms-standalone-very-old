<?php

namespace Core;

/**
 * Controller class for controllers.
 *
 * Provides view methods, if you want to handle coped view data.
 * 
 */
class Controller {

	private $viewData = array();


	/**
	 * Assign data to a variable inside this controller.
	 * This data is used in $this->render() if you don't pass a data array.
	 *
	 * @param string $pKey
	 * @param mixed  $pValue
	 */
	public function assign($pKey, $pValue){
        $this->viewData[$pKey] = $pValue;
	}

	/**
     * Assign data by reference to a variable inside this controller.
     * This data is used in $this->render() if you don't pass a data array.
	 *
	 * @param string $pKey
	 * @param mixed &$pValue
	 */
	public function assignByRef($pKey, &$pValue){
        $this->viewData[$pKey] = $pValue;
	}


	/**
	 * Returns true if the specified name has a value assigned.
	 *
	 * @param string $pKey
	 * @return bool
	 */
	public function assigned($pKey) {
        return $this->viewData[$pKey] !== null;
	}


	/**
	 * Clears all assigned data.
	 * 
	 */
	public function clearAllAssign(){
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
     * Meaning both is a valid view:
     *   publication/news/list/default.tpl
     *   news/list/default.tpl (if the current class is \Publication\News\Bar)
     *
     * Depending on the file name of the view we use Smarty, Twig, plain PHP or nothing.
     *
     * Examples:
     *    plugin1/default.php           -> php
     *    plugin1/default.tpl           -> smarty
     *    plugin1/default.smarty.tpl    -> smarty
     *    plugin1/default.twig.tpl      -> twig
     *    plugin1/default.html          -> none
	 *
	 * @param  string $pView
	 * @param  array  $pData Use this data instead of the data assigned through $this->assign()
	 * @return string
	 */
    public function render($pView, $pData = null){
		
		$clazz = get_class($this);
		if (($pos = strpos($clazz, '\\')) !== false) {
			$namespace = substr($clazz, 0, $pos);
			$clazz = substr($clazz, $pos+1);
			if (Kryn::isActiveModule($namespace) && $clazz == 'Controller'){
				$view = tPath($namespace.'/'.$pView);	
			}
		}

        $view = str_replace('..', '', $view);

        //todo, detect the file extension and load the appropriate template engine.
		if (!file_exists($view)){
			$view = tPath($pView);
		}
        if (!Kryn::$smarty)
            tInit();

		$tpl = Kryn::$smarty->createTemplate($view, $pData?$pData:$this->viewData);
		$html = $tpl->fetch();

        return Kryn::translate($html);
	}

    public function getFileMTime($pView){
        $view = tPath($pView);
        return filemtime($view);
    }

    /**
     *
     *
     * @param string $pCacheKey
     * @param string $pView
     * @param array  $pData
     *
     * @return string
     */
    public function renderCached($pCacheKey, $pView, $pData = null){

        $data  = Kryn::getDistributedCache($pCacheKey);
        $mTime = $this->getFileMTime($pView);

        if (!$data || !is_array($data) || $mTime != $data['fileMTime']){
            $data = array(
                'content' => $this->render($pView, $pData),
                'fileMTime' => $mTime
            );

            Kryn::setDistributedCache($pCacheKey, $data);
        }

        return $data['content'];
    }

} 
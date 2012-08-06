<?php

namespace Core;

use \Core\Kryn;

/**
 * Controller class for controllers.
 *
 * Provides view methods, if you want to handle coped view data.
 * 
 */
class Controller {

	private $viewData = null;


	/**
	 * Assign data to a variable inside the template engine.
	 * The assigned data belongs to the scope of the current object.
	 *
	 * Use tAssign() for global assignment.
	 * 
	 * @param  string $pName
	 * @param  mixed &$pValue
	 */
	public function assign($pName, $pValue){
		if (!$viewData)
			$this->viewData = Kryn::$smarty->createData();

		$this->viewData->assign($pName, $pValue);
	}

	/**
	 * Assign data to a variable inside the template engine by reference.
	 * The assigned data belongs to the scope of the current object.
	 *
	 * Use tAssignByRef() for global assignment.
	 * 
	 * @param  string $pName
	 * @param  mixed &$pValue
	 */
	public function assignByRef($pName, &$pValue){
		if (!$viewData)
			$this->viewData = Kryn::$smarty->createData();

		$this->viewData->assignByRef($pName, $pValue);
	}


	/**
	 * Returns true if the specified name has a value assigned in this scope.
	 *
	 * @param $pName
	 * @return bool
	 */
	public function assigned($pName) {
		if (!$this->viewData) return false;
	    return $this->viewData->getTemplateVars($pName) !== null;
	}


	/**
	 * Clears all assigned data.
	 * 
	 */
	public function clearAllAssign(){
		if ($this->viewData)
	    	$this->viewData->clearAllAssign();
	}


	/**
	 * Returns the view output.
	 *
	 * If you've extended your main controller with this controller,
	 * then $pTemplate will be prefixed with the current module key,
	 * so that the views of <moduleKey>/views/ will be loaded primarily.
	 * If the view does not exists there, it trys to load the view from root.
	 * 
	 * @param  string $pTemplate
	 * @param  array  $pData Use this data instead of the data assigned through $this->assign()
	 * @return string
	 */
	public function view($pView, $pData = null){
		
		$clazz = get_class($this);
		if (($pos = strpos($clazz, '\\')) !== false) {
			$namespace = substr($clazz, 0, $pos);
			$clazz = substr($clazz, $pos+1);
			if (Kryn::isActiveModule($namespace) && $clazz == 'Controller'){
				$view = tPath($namespace.'/'.$pView);	
			}
		}
		if (!file_exists($view)){
			$view = tPath($pView);
		}
		$tpl = Kryn::$smarty->createTemplate($view, $pData?$pData:$this->viewData);
		return $tpl->fetch();
	}

} 
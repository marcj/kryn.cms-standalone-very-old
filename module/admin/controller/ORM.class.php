<?php

namespace Admin;

use Core\PropelHelper;

class ORM {


	public function buildEnvironment(){
		return PropelHelper::callGen('environment');
	}

	public function writeModels(){
		return PropelHelper::callGen('models');
	}

	public function updateScheme(){
		return PropelHelper::callGen('update');
	}

	public function checkScheme(){
		return ($errors)?$error:true;
	}

}


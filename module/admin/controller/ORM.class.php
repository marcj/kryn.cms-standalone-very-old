<?php

namespace Admin;

class ORM {


	public function buildEnvironment(){
		return \propelHelper::callGen('environment');
	}

	public function writeModels(){
		return \propelHelper::callGen('models');
	}

	public function updateScheme(){
		return \propelHelper::callGen('update');
	}

	public function checkScheme(){
		return ($errors)?$error:true;
	}

}


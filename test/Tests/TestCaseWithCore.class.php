<?php

namespace Tests;

/**
 * This class provides in setUp an fresh installation and bootup Kryn.cms core, s
 * you can work in your tests as you would do in a Kryn.cms module.
 *
 * This uninstalls (removes config.php as well) in tearDown().
 *
 */
class TestCaseWithCore extends \PHPUnit_Framework_TestCase {

    public $currentDir = '';

    public function run(\PHPUnit_Framework_TestResult $result = NULL){

        $this->currentDir = getcwd();

        if ($result === NULL) {
            $result = $this->createResult();
        }
        if (!$this->bootUp++){
            try {

                if (!file_exists('config.php')){
                    Manager::freshInstallation();
                }
                Manager::bootupCore();
            } catch (\Exception $ex){
                die($ex);
                $result->addError($this, $ex, 0);
                return $result;
            }
        }

        $result = parent::run($result);

        return $result;

    }

}
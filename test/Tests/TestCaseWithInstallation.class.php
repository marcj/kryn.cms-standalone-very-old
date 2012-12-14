<?php

namespace Tests;

/**
 * This class provides in setUp an fresh installation and bootup Kryn.cms core, s
 * you can work in your tests as you would do in a Kryn.cms module.
 *
 * This uninstalls (removes config.php as well) in tearDown().
 *
 */
class TestCaseWithInstallation extends \PHPUnit_Framework_TestCase {

    public $currentDir = '';

    public function run(\PHPUnit_Framework_TestResult $result = NULL){

        $this->currentDir = getcwd();

        if ($result === NULL) {
            $result = $this->createResult();
        }
        if (!$this->bootUp++){
            try {
                Manager::freshInstallation();
                Manager::bootupCore();
            } catch (\Exception $ex){
                die($ex);
                $result->addError($this, $ex, 0);
                return $result;
            }
        }

        chdir('..');
        var_dump(getcwd());
        $result = parent::run($result);
        chdir($this->currentDir);

        try {
            Manager::uninstall();
        } catch (\Exception $ex){
            die($ex);
            $result->addError($this, $ex, 0);
            return $result;
        }

        return $result;

    }

}
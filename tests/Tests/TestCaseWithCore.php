<?php

namespace Tests;

/**
 * This class provides in setUp an fresh installation and bootup Kryn.cms core, s
 * you can work in your tests as you would do in a Kryn.cms module.
 *
 * This uninstalls (removes config.php as well) in tearDown().
 *
 */
class TestCaseWithCore extends \PHPUnit_Framework_TestCase
{
    public $currentDir = '';

    public function run(\PHPUnit_Framework_TestResult $result = NULL)
    {
        $this->currentDir = getcwd();

        if ($result === NULL) {
            $result = $this->createResult();
        }
        if (!$this->bootUp++) {
            if (!file_exists('app/config/config.xml')) {
                Manager::freshInstallation();
            }
            Manager::bootupCore();
        }

        $result = parent::run($result);

        \Admin\Utils::clearCache();

        return $result;

    }

}

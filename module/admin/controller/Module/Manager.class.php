<?php

namespace Admin\Module;

use \Core\Kryn;

class Manager extends \RestServerController {

    function __construct(){
        define('KRYN_MANAGER', true);
    }

    function __destruct(){
        define('KRYN_MANAGER', false);
    }


    public function check4Updates(){


        global $cfg;

        $res['found'] = false;

        # add kryn-core
        $tmodules = Kryn::$configs;

        foreach ($tmodules as $key => $config) {
            $version = '0';
            $name = $key;
            $version = wget($cfg['repoServer'] . "/?version=$name");
            if ($version && $version != '' && self::versionCompareToServer($config['version'], $version) == '<') {
                $res['found'] = true;
                $temp = array();
                $temp['newVersion'] = $version;
                $temp['name'] = $name;
                $res['modules'][] = $temp;
            }
        }

        json($res);

    }

    /**
     * Returns if all dependencies are fine.
     *
     * @param $pName
     *
     * @return boolean
     */
    public function dependenciesCheck($pName){



    }

    /**
     * Returns a list of open dependencies.
     *
     * @param $pName
     */
    public function dependenciesOpen($pName){



    }

    /**
     * Executes the installation pre-script.
     * Pre some database content, backup some files or stuff like that.
     *
     * @param $pName
     * @return string
     */
    public function installPre($pName){

        $file = $this->getScriptFile($pName, 'install-pre');
        if (file_exists($file)){

            try {
                require($file);
            } catch(\Exception $e){
                $this->sendError('execution_failed', $e);
            }

            return 'execution_successful';
        }
        return false;

    }

    /**
     * Executes the install-extract.php-script.
     * Maybe fore some file adjustments.
     *
     * @param $pName
     * @return string
     */
    public function installFireExtract($pName){

        $file = $this->getScriptFile($pName, 'install-extract');
        if (file_exists($file)){

            try {
                require($file);
            } catch(\Exception $e){
                $this->sendError('execution_failed', $e);
            }

            return 'execution_successful';
        }
        return false;

    }

    /**
     * Extract the files and fires the the install-extract.php script..
     *
     * @param $pName
     */
    public function installExtract($pName){



    }

    /**
     * Executes the installation database schema synchronisation.
     *
     * @param $pName
     * @return string
     */
    public function installDatabase($pName){

        $file = $this->getScriptFile($pName, 'install-database');
        if (file_exists($file)){

            try {
                include($file);
            } catch(\Exception $e){
                $this->sendError('execution_failed', $e);
            }

            return 'execution_successful';
        }
        return false;

    }


    /**
     * Executes the installation post-script.
     * Insert database values, convert some content etc.
     *
     * @param $pName
     */
    public function installPost($pName){




    }


    /**
     * Executes the update pre-script.
     * Pre some database content, backup some files or stuff like that.
     *
     * @param $pName
     */
    public function updatePre($pName){


    }

    /**
     * Executes the update database schema synchronisation.
     *
     * @param $pName
     */
    public function updateDatabase($pName){



    }


    /**
     * Executes the update file extraction.
     *
     * @param $pName
     */
    public function updateExtract($pName){



    }


    /**
     * Executes the update post-script.
     * Insert database values, convert some content etc.
     *
     * @param $pName
     */
    public function updatePost($pName){




    }


    private function getScriptFile($pExtension, $pName){

        $name = esc($pExtension, 2);
        return PATH_MODULE . $name . '/package/' . $pName . '.php';

    }
}
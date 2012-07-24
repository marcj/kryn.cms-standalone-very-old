<?php

namespace Admin\Module;

use Core\Kryn;

class Manager extends \RestServerController {

    public function __construct(){
        define('KRYN_MANAGER', true);
    }

    public function __destruct(){
        define('KRYN_MANAGER', false);
    }

    public function getLocal(){

        $modules = Kryn::readFolder(PATH_MODULE);
        $modules[] = 'kryn';
        $res = array();

        foreach ($modules as $module) {
            $config = $this->loadInfo($module);
            unset($config['db']);
            unset($config['admin']);
            unset($config['objects']);
            unset($config['plugins']);
            unset($config['widgetsLayout']);
            unset($config['widgets']);
            unset($config['adminJavascript']);
            unset($config['adminCss']);
            $res[$module] = $config;
            $res[$module]['activated'] = (Kryn::$configs[$module]) ? 1 : 0;
        }

        return $res;
    }


    public function loadInfo($pModuleName, $pType = false, $pExtract = false) {
        global $cfg;

        /*
        * pType: false => load from local (dev) PATH_MODULE/$pModuleName
        * pType: path  => load from zip (module upload)
        * pType: true =>  load from inet
        */

        $pModuleName = str_replace(".", "", $pModuleName);
        $configFile = PATH_MODULE . "$pModuleName/config.json";

        if ($pModuleName == 'kryn')
            $configFile = "core/config.json";

        $extract = false;

        // inet
        if ($pType === true || $pType == 1) {

            $res = wget($cfg['repoServer'] . "/?install=$pModuleName");
            if ($res === false)
                return array('cannotConnect' => 1);

            $info = json_decode($res, 1);

            if (!$info['id'] > 0) {
                return array('notExist' => 1);
            }

            if (!@file_exists('data/upload'))
                if (!@mkdir('data/upload'))
                    klog('core', t('FATAL ERROR: Can not create folder data/upload.'));

            if (!@file_exists('data/packages/modules'))
                if (!@mkdir('data/packages/modules'))
                    klog('core', _l('FATAL ERROR: Can not create folder data/packages/modules.'));

            $configFile = "data/packages/modules/$pModuleName.config.json";
            @unlink($configFile);
            wget($cfg['repoServer'] . "/modules/$pModuleName/config.json", $configFile);
            if ($pExtract) {
                $extract = true;
                $zipFile = 'data/packages/modules/' . $info['filename'];
                wget($cfg['repoServer'] . "/modules/$pModuleName/" . $info['filename'], $zipFile);
            }
        }

        //local zip
        if (($pType !== false && $pType != "0") && ($pType !== true && $pType != "1")) {
            if (file_exists(PATH_MEDIA . $pType)) {
                $pType = PATH_MEDIA . $pType;
            }
            $zipFile = $pType;
            $bname = basename($pType);
            $t = explode("-", $bname);
            $pModuleName = $t[0];
            $extract = true;
        }

        if ($extract) {
            @mkdir("data/packages/modules/$pModuleName");
            include_once('File/Archive.php');
            $toDir = "data/packages/modules/$pModuleName/";
            $zipFile .= "/";
            $res = File_Archive::extract($zipFile, $toDir);
            $configFile = "data/packages/modules/$pModuleName/module/$pModuleName/config.json";
            if ($pModuleName == 'kryn')
                $configFile = "data/packages/modules/kryn/core/config.json";
        }

        if ($configFile) {
            if (!file_exists($configFile)) {
                return array('noConfig' => 1);
            }
            $json = Kryn::fileRead($configFile);
            $config = json_decode($json, true);

            if (!$pExtract) {
                @rmDir("data/packages/modules/$pModuleName");
                @unlink($zipFile);
            }

            //if locale
            if ($pType == false) {
                if (is_dir(PATH_MEDIA."$pModuleName/_screenshots")) {
                    $config['screenshots'] = Kryn::readFolder(PATH_MEDIA."$pModuleName/_screenshots");
                }
            }

            $config['__path'] = dirname($configFile);
            if (is_array(Kryn::$configs) && array_key_exists($pModuleName, Kryn::$configs))
                $config['installed'] = true;

            $config['extensionCode'] = $pModuleName;

            if (Kryn::$configs)
                foreach (Kryn::$configs as $extender => &$modConfig) {
                    if (is_array($modConfig['extendConfig'])) {
                        foreach ($modConfig['extendConfig'] as $extendModule => $extendConfig) {
                            if ($extendModule == $pModuleName) {
                                $config['extendedFrom'][$extender] = $extendConfig;
                            }
                        }
                    }
                }

            return $config;
        }

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
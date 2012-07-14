<?php

namespace Admin\Module;

class Manager extends \RestServerController {


    /**
     * Returns if all dependencies are fine.
     *
     * @param $pMethod
     * @param $pName
     *
     * @return boolean
     */
    public function dependenciesCheck($pMethod, $pName){



    }

    /**
     * Returns a list of open dependencies.
     *
     * @param $pMethod
     * @param $pName
     */
    public function dependenciesOpen($pMethod, $pName){



    }

    /**
     * Executes the installation pre-script.
     * Pre some database content, backup some files or stuff like that.
     *
     * @param $pMethod
     * @param $pName
     */
    public function installPre($pMethod, $pName){


    }

    /**
     * Executes the installation file extraction.
     *
     * @param $pMethod
     * @param $pName
     */
    public function installExtract($pMethod, $pName){



    }

    /**
     * Executes the installation database schema synchronisation.
     *
     * @param $pMethod
     * @param $pName
     */
    public function installDatabase($pMethod, $pName){



    }


    /**
     * Executes the installation post-script.
     * Insert database values, convert some content etc.
     *
     * @param $pMethod
     * @param $pName
     */
    public function installPost($pMethod, $pName){




    }


    /**
     * Executes the update pre-script.
     * Pre some database content, backup some files or stuff like that.
     *
     * @param $pMethod
     * @param $pName
     */
    public function updatePre($pMethod, $pName){


    }

    /**
     * Executes the update database schema synchronisation.
     *
     * @param $pMethod
     * @param $pName
     */
    public function updateDatabase($pMethod, $pName){



    }


    /**
     * Executes the update file extraction.
     *
     * @param $pMethod
     * @param $pName
     */
    public function updateExtract($pMethod, $pName){



    }


    /**
     * Executes the update post-script.
     * Insert database values, convert some content etc.
     *
     * @param $pMethod
     * @param $pName
     */
    public function updatePost($pMethod, $pName){




    }
}
<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

/**
 * Index.php
 *
 * @author MArc Schmidt <marc@kryn.org>
 */

require('inc/kryn/checkFile.php');
require('inc/kryn/bootstrap.php');

/*
 * Initialize the inc/config.php values. Make some vars compatible to older versions etc.
 */
kryn::initConfig();

/*
 * Load cached list of active modules
 */
kryn::loadActiveModules();

if (getArgv(1) != 'admin') {
    kryn::searchDomain();
}

/*
 * Load cached themes, db scheme and object definitions from configs
 */
kryn::loadModuleConfigs();

/*
 * Initialize the krynAuth user objects.
 */
kryn::initAuth();

/*
 * Load current language
 */
kryn::loadLanguage();


tAssignRef("request", $_REQUEST); #compatibility
tAssignRef("user", $user->user); #compatibility


if (getArgv(1) == 'admin') {

    /*
     * Load the whole config of all modules
     */
    kryn::loadConfigs();

    /*
     * initialize administration controller
     */
    require('inc/kryn/adminForm.class.php');
    require(PATH_MODULE . 'admin/admin.class.php');
    kryn::$modules['admin'] = new admin();

    /*
     * Check url access
     */
    kryn::checkAccess();

} else {

    /*
     * Initialize some search params
     */
    krynSearch::initSearch();

}

//register the shutdown function
register_shutdown_function('kryn_shutdown');

kryn::$admin = (getArgv(1) == 'admin');
tAssign('admin', kryn::$admin);

if (getArgv(1) == 'admin') {

    /*
     * Start backend controller
     */
    kryn::$modules['admin']->content();

} else {

    /*
     * Start frontend controller
     */

    kryn::display();
}

?>
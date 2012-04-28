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


if (getArgv(1) != 'admin') {
    kryn::searchDomain();
}

/*
 * Initialize the krynAuth user objects for backend and frontend.
 */
kryn::initAuth();

tAssignRef("request", $_REQUEST); #compatibility
tAssignRef("user", $user->user); #compatibility


if (kryn::$admin) {

    /*
     * initialize administration controller
     */
    require(PATH_MODULE . 'admin/admin.class.php');
    kryn::$modules['admin'] = new admin();

    /*
     * Check url access
     */
    kryn::checkAccess();

}

//register the shutdown function
register_shutdown_function('kryn_shutdown');

if (kryn::$admin) {

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
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

$_SERVER['SERVER_NAME'] = 'ilee';

/**
 * Index.php
 *
 * @author MArc Schmidt <marc@kryn.org>
 */

require('core/bootstrap.checkFile.php');
require('core/bootstrap.php');


debugStop();

if (getArgv(1) != 'admin') {
    Core\Kryn::searchDomain();
}
/*
 * Initialize the krynAuth user objects for backend and frontend.
 */
Core\Kryn::initAuth();


if (Core\Kryn::$admin) {

    /*
     * initialize administration controller
     */
    Core\Kryn::$modules['admin'] = new Admin\AdminController();

    /*
     * Check url access
     */
    Core\Kryn::checkAccess();

}

//register the shutdown function
register_shutdown_function('kryn_shutdown');

if (Core\Kryn::$admin) {

    /*
     * Start backend controller
     */
    Core\Kryn::$modules['admin']->run();

} else {

    /*
     * Start frontend controller
     */
    Core\Kryn::display();
}

?>
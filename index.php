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

require('core/bootstrap.checkFile.php');
require('core/bootstrap.php');

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
    Core\Kryn::$modules['admin'] = new Admin\Controller();

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
    Core\Kryn::$modules['admin']->admin();

} else {

    /*
     * Start frontend controller
     */
    Core\Kryn::display();
}

?>
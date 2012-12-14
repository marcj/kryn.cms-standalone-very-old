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

require('core/bootstrap.checkFile.php'); //deprecated, we should delete that.
require('core/bootstrap.php');
require('core/bootstrap.startup.php');

/*
 * Search domain
 */
if (!Core\Kryn::$admin) {
    Core\Kryn::searchDomain();
}

/*
 * Initialize the client objects for backend and frontend.
 */
Core\Kryn::initClient();


if (Core\Kryn::$admin) {

    /*
     * initialize administration controller
     */
    Core\Kryn::$modules['admin']  = $admin = new Admin\AdminController();

    /*
     * Start backend controller
     */

    $admin->run();

} else {

    /*
     * Start frontend controller
     */
    
    Core\Kryn::display();
}

?>
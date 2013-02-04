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

//load main config, setup some constants and check some requirements.
require('core/bootstrap.php');

//attach error handler, init propel, load module configs, initialise main controllers and setup the autoloader.
require('core/bootstrap.startup.php');


Core\Kryn::getLogger()->addDebug('Bootstrap loaded.');

//Setup the HTTPKernel.
Core\Kryn::setupHttpKernel();

//Initialize the client objects for backend and frontend.
Core\Kryn::initClient();

//Setup application routes.
if (Core\Kryn::isAdmin()) {

    //backend
    Core\Kryn::$modules['admin'] = new Admin\AdminController();
    Core\Kryn::$modules['admin']->run();

} else {

    //frontend
    Core\Kryn::setupPageRoutes();
}

//handle request.
Core\Kryn::handleRequest();


?>
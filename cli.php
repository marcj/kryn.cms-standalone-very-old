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
 * 
 * @author MArc Schmidt <marc@kryn.org>
 * 
 */

if (php_sapi_name() !== 'cli') exit;

if (count($argv) == 1) die("params failed.\nUse: php cli.php <module>/entry/path");

$_GET['__url'] = 'admin/'.$argv[1];

require('core/bootstrap.php');

@ini_set('display_errors', 1);

/*
* initialize administration controller
*/
require(PATH_MODULE . 'admin/admin.class.php');
Core\Kryn::$modules['admin'] = new admin();

//register the shutdown function
register_shutdown_function('kryn_shutdown');

Core\Kryn::$admin = (getArgv(1) == 'admin');


/*
* Start backend controller
*/
Core\Kryn::$modules['admin']->content();


?>
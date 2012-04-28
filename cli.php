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

$_REQUEST['_kurl'] = 'admin/'.$argv[1];

require('inc/kryn/bootstrap.php');


@ini_set('display_errors', 1);

/*
* initialize administration controller
*/
require('inc/kryn/adminForm.class.php');
require(PATH_MODULE . 'admin/admin.class.php');
kryn::$modules['admin'] = new admin();

//register the shutdown function
register_shutdown_function('kryn_shutdown');

kryn::$admin = (getArgv(1) == 'admin');
tAssign('admin', kryn::$admin);


/*
* Start backend controller
*/
kryn::$modules['admin']->content();


?>
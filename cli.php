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

$_SERVER['HTTP_ACCEPT'] = 'xml';

$_GET['__url'] = 'admin/'.$argv[1];

require 'core/bootstrap.php';
require 'core/bootstrap.startup.php';

/*
* initialize administration controller
*/
Core\Kryn::$modules['admin'] = new Admin\AdminController();

Core\Kryn::$admin = (getArgv(1) == 'admin');

/*
* Start backend controller
*/
Core\Kryn::$modules['admin']->run();

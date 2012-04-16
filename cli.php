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

$_REQUEST['_kurl'] = 'admin/'.$argv[1];

require('inc/kryn/bootstrap.php');


/*
 * Initialize the inc/config.php values. Make some vars compatible to older versions etc.
 */
kryn::initConfig();


@ini_set('display_errors', 1);


/*
 * Load list of active modules
 */
kryn::loadActiveModules();


/*
 * Load themes, db scheme and object definitions from configs
 */
kryn::loadModuleConfigs();


/*
 * Load current language
 */
kryn::loadLanguage();


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


//register the shutdown function
register_shutdown_function('kryn_shutdown');

kryn::$admin = (getArgv(1) == 'admin');
tAssign('admin', kryn::$admin);


/*
* Start backend controller
*/
kryn::$modules['admin']->content();


?>
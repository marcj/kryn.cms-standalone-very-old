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

require('inc/kryn/bootstrap.php');

$_REQUEST[1] = 'admin';

/*
 * Initialize the inc/config.php values. Make some vars compatible to older versions etc.
 */
kryn::initConfig();


/*
 * Load list of active modules
 */
kryn::loadActiveModules();


/*
 * Load themes, db scheme and object definitions from configs
 */
kryn::loadModuleConfigs();


if( $argv[2] == 'backup' ){
    include(PATH_MODULE.'admin/adminBackup.class.php');
    adminBackup::doBackup( $argv[3] );
}

?>
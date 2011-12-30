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

@set_include_path(get_include_path() . PATH_SEPARATOR . './inc/lib/pear/');

/**
 * Define globals
 * @globals 
 */
$cfg = array();
$modules = array();
$languages = array();
$kcache = array();
$_AGET = array();
define('PATH', dirname(__FILE__).'/');
define('PATH_MODULE', dirname(__FILE__) . '/inc/module/');
define('PATH_TEMPLATE', dirname(__FILE__) . '/inc/template/');

ini_set('display_errors', 1);


# install
if( !file_exists('inc/config.php') ){
    exit;
};
include('inc/config.php');

if( $argv[1] != $cfg['cronjob_key'] || !$cfg['cronjob_key'] ) exit;

define('pfx', $cfg['db_prefix']);

include('inc/kryn/misc.global.php');
include('inc/kryn/database.global.php');
include('inc/kryn/template.global.php');
include('inc/kryn/internal.global.php');
include('inc/kryn/framework.global.php');


# Load important classes
include('inc/lib/smarty/Smarty.class.php');
include('inc/kryn/database.class.php');
include('inc/kryn/krynModule.class.php');

include('inc/kryn/kryn.class.php');
include('inc/kryn/krynCache.class.php');
include('inc/kryn/krynAcl.class.php');
include('inc/kryn/krynNavigation.class.php');
include('inc/kryn/krynHtml.class.php');
include('inc/kryn/krynAuth.class.php');
include('inc/kryn/krynFile.class.php');
include('inc/kryn/krynLanguage.class.php');
include('inc/kryn/krynSearch.class.php');
date_default_timezone_set( $cfg['timezone'] );



# Init classes and globals
$tpl = new Smarty();
$tpl->template_dir = 'inc/template/';
$tpl->compile_dir = 'cache/smarty_compile/';

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
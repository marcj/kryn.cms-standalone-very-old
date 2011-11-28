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

@set_include_path(get_include_path() . PATH_SEPARATOR . './inc/pear/');

/**
 * Define globals
 * @globals 
 */
$cfg = array();
$modules = array();
$searchIndexMode = false;
$languages = array();
$kcache = array();
$_AGET = array();
define('PATH', dirname(__FILE__).'/');


# install
if( !file_exists('inc/config.php') ){
    exit;
};
include('inc/config.php');

if( $argv[1] != $cfg['cronjob_key'] || !$cfg['cronjob_key'] ) exit;

define('pfx', $cfg['db_prefix']);

$umask = (in_array('umask', $cfg))?$cfg['umask']:002;
@umask($umask);

include('inc/kryn/misc.global.php');
include('inc/kryn/database.global.php');
include('inc/kryn/template.global.php');
include('inc/kryn/internal.global.php');
include('inc/kryn/framework.global.php');

include('inc/kryn/kryn.class.php');
include('inc/kryn/database.class.php');
include('inc/kryn/krynCache.class.php');
include('inc/kryn/krynAcl.class.php');
include('inc/kryn/krynNavigation.class.php');
include('inc/kryn/krynHtml.class.php');
include('inc/kryn/krynAuth.class.php');
date_default_timezone_set( $cfg['timezone'] );


if( $argv[2] == 'backup' ){
    include('inc/modules/admin/adminBackup.class.php');
    adminBackup::doBackup( $argv[3] );
}

?>
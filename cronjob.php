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


# install
if( !file_exists('inc/config.php') ){
    exit;
};
include('inc/config.php');

if( $argv[1] != $cfg['cronjob_key'] || !$cfg['cronjob_key'] ) exit;

$umask = (in_array('umask', $cfg))?$cfg['umask']:002;
@umask($umask);

include('inc/kryn/cache.class.php');
include('inc/kryn/misc.global.php');
include('inc/kryn/database.global.php');
include('inc/kryn/template.global.php');
include('inc/kryn/internal.global.php');
include('inc/kryn/framework.global.php');
include('inc/kryn/baseModule.class.php');
include('inc/kryn/database.class.php');
include('inc/kryn/kryn.class.php');
date_default_timezone_set( $cfg['timezone'] );


$kdb = new database(
             $cfg['db_type'],
             $cfg['db_server'],
             $cfg['db_user'],
             $cfg['db_passwd'],
             $cfg['db_name'],
             ($cfg['db_pdo']+0 == 1 || $cfg['db_pdo'] === '' )?true:false,
             ($cfg['db_forceutf8']=='1')?true:false
);

/*if( !database::isActive() ){
    exec("logger die. not active");
    die('Can not connect to database. Please check your ./inc/config.php. <div style="color: red;">'.$kdb->lastError().'</div>');
}*/


if( $argv[2] == 'backup' ){
    include('inc/modules/admin/adminBackup.class.php');
    adminBackup::doBackup( $argv[3] );
}

?>
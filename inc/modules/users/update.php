<?php

//auto updater for structure changes

//
//   ALL under 0.7.0 RELEASE
//


if( $GLOBALS['krynInstaller'] != true ){
    if( kryn::$canCompare == true ){
        
        if( kryn::compareVersion('kryn', '<', '0.7.0') ){
            require_once("inc/modules/admin/admin.class.php");
            require_once("inc/modules/admin/adminModule.class.php");
            require_once("inc/modules/admin/adminDb.class.php");
            adminModule::installModule('kryn', true);
            $die = true;
        }
            
        if( kryn::$configs['admin']['version'] != '' && kryn::compareVersion('admin', '<', '0.7.0') ){
            require_once("inc/modules/admin/admin.class.php");
            require_once("inc/modules/admin/adminModule.class.php");
            require_once("inc/modules/admin/adminDb.class.php");
            adminModule::installModule('admin', true);
            $die = true;
        }
        
    } else {
        
        //we have to check manually if admin or kryn is not 0.7.0
        if( kryn::$configs['kryn']['version'] != '0.7.0' ){
            require_once("inc/modules/admin/admin.class.php");
            require_once("inc/modules/admin/adminModule.class.php");
            require_once("inc/modules/admin/adminDb.class.php");
            adminModule::installModule('kryn', true);
            $die = true;
        }
        
        if( kryn::$configs['admin']['version'] != '' && kryn::$configs['admin']['version'] != '0.7.0' ){
            require_once("inc/modules/admin/admin.class.php");
            require_once("inc/modules/admin/adminModule.class.php");
            require_once("inc/modules/admin/adminDb.class.php");
            adminModule::installModule('admin', true);
            $die = true;
        }
    }
    if( $die == true )
        die("System cores via users updated - Please reloead.");
}

?>
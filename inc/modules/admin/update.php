<?php

//auto updater for structure changes

//
//   ALL under 0.7.0 RELEASE
//

        
if( $GLOBALS['krynInstaller'] != true ){
    
    
    if( kryn::$canCompare == true ){
        
        if( kryn::compareVersion('kryn', '<', '0.7.0') ){
            require_once("inc/modules/admin/adminModule.class.php");
            require_once("inc/modules/admin/adminDb.class.php");
            adminModule::installModule('kryn', true);
            $die = true;
        }
            
        if( kryn::compareVersion('users', '<', '0.7.0') ){
            require_once("inc/modules/admin/adminModule.class.php");
            require_once("inc/modules/admin/adminDb.class.php");
            adminModule::installModule('users', true);
            $die = true;
        }
        
    } else {
        //we have to check manually if admin or kryn is not 0.7.0
        if( kryn::$configs['kryn']['version'] != '0.7.0' ){
            require_once("inc/modules/admin/adminModule.class.php");
            require_once("inc/modules/admin/adminDb.class.php");
            adminModule::installModule('kryn', true);
            $die = true;
        }
        
        if( kryn::$configs['users']['version'] != '0.7.0' ){
            require_once("inc/modules/admin/adminModule.class.php");
            require_once("inc/modules/admin/adminDb.class.php");
            adminModule::installModule('users', true);
            $die = true;
        }
    }
    if( $die == true )
        die("System cores via admin updated - Please reloead.");
}
    


?>
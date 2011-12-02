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
 * 
 * Motherclass for all extension classes.
 * 
 * 
 * @author MArc Schmidt <marc@kryn.org>
 */

class krynModule {
    
    
    /**
     * 
     * Framework controller
     * How its work:
     * <domain>/admin/myExtension/my/entry/point/function
     * calls => $this->my_entry_point_function() if exists
     *
     * Just overwrite this method to build your own url controller
     *
     */
    public function admin(){
        $found = true;
        if( getArgv(3) == ''){
            $found = false;
        }
        $method = '';
        $c = 3;
        while( $found ){
            $method .= getArgv($c).'_';
            $c++;
            if( getArgv($c) == '' )
                $found = false;
        }
        
        $method = substr( $method, 0, strlen($method)-1 );
        if( method_exists( $this, $method ) ){
            $refl = new ReflectionMethod($this, $method);
            if( $refl->isPublic() )
                return $this->$method();
        }
        
        json( array('error' => 'method_not_found') );
    
    }

    public function install(){
        /* caledl when installing your extensions through the extension manager */
    }

    public function deinstall(){
        /* called when removing your extensions through the extension manager */
    }

}

class modul extends baseModule {

}

class baseModule extends krynModule {

}

?>

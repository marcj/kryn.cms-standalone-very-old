<?php

class adminLanguages {


    public function init(){
        
        switch(getArgv(4)){
            case 'getExtensionLang':
                json( self::getExtensionLang(getArgv('extensions')) );
            case 'getAllLanguages':
                json( self::getAllLanguages(getArgv('lang')) );
            case 'saveAllLanguages':
                json( self::saveAllLanguages() );
        }

    }

    public function saveAllLanguages( ){
        $lang = getArgv('lang',2);

        $langs = json_decode( getArgv('langs'), true );
        foreach( $langs as $key => &$mylangs ){
            if( count($mylangs) > 0 ){
                adminModule::saveLanguage( $key, $lang, $mylangs );
            }
        }
    }

    public function getAllLanguages( $pLang = 'en' ){
        global $kryn;
    
        if( $pLang == '' ) $pLang = 'en';

        $res = array(); 
        foreach( $kryn->installedMods as $key => $mod ){
            $res[ $key ]['config'] = $mod;
            $res[ $key ]['lang'] = adminModule::extractLanguage( $key );
            if( count($res[ $key ]['lang']) > 0 ){
                $translate = adminModule::getLanguage( $key, $pLang );
                foreach( $res[ $key ]['lang'] as $key => &$lang ){
                    if( $translate[ $key ] != '' )
                        $lang = $translate[ $key ];
                    else
                        $lang = '';
                }
            }
        }

        return $res;
        
    }

    public function getExtensionLang( $pModule ){




        return $res;

    }

}

?>

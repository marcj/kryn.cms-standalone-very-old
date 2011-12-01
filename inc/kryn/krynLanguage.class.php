<?php


/**
 * krynLanguage - a class that handles .po files
 *
 */
class krynLanguage {


    public static function getLanguage( $pModuleName, $pLang ){
        
        $res = self::parsePo( $pModuleName, $pLang );
      
        $pluralForm = self::getPluralForm($pLang);
        preg_match('/^nplurals=([0-9]+);/', $pluralForm, $match);
        
        $res['pluralCount'] = intval($match[1]);
        $res['pluralCount'] = $pluralForm;
        return $res;
        
        /*
        $path = PATH_MODULE.''.$pModuleName.'/lang/'.$pLang.'.json';
        //load old method: json
        if( file_exists($path) ){
            $json = kryn::fileRead( PATH_MODULE.''.$pModuleName.'/lang/'.$pLang.'.json' );
            $res = json_decode($json,true);
        } else {
            $res = array();
        }
        return array(
            'translations' => $res,
            'pluralCount' => 2
        );
        */
    }
    
    public static function getPluralForm( $pLang ){
        //csv based on (c) http://translate.sourceforge.net/wiki/l10n/pluralforms
    
        $fh = fopen(PATH_MODULE.'admin/gettext-plural-forms.csv', 'r');
        
        while( ($buffer = fgetcsv($fh, 1000)) !== false ){
            if( $buffer[0] == $pLang ){
                fclose($fh);
                return $buffer[2];
            }
        }
        
    }

    public static function toPoString( $pString ){
        
        $res  = '"';
        $res .= str_replace("\n", '\n"'."\n".'"', $pString);
        $res  .= '"';
        return $res;    
    }
    
    public static function parsePo( $pModuleName, $pLang ){
        
        $file = 'inc/module/'.$pModuleName.'/lang/'.$pLang.'.po';
        if( $pModuleName == 'kryn' )
            $file = 'inc/kryn/lang/'.$pLang.'.po';
            
            
        $res = array('header' => array(), 'translations' => array());
        if( !file_exists($file) ) return $res;

        $fh = fopen($file, 'r');

        while( ($buffer = fgets($fh)) !== false) {
            
            
            if( preg_match('/^msgctxt "(.*)"/', $buffer, $match) ){
                $lastWasPlura = false;
                $nextIsThisContext = $match[1];
            }
            
            if( preg_match('/^msgid "(.*)"/', $buffer, $match) ){
                $lastWasPlural = false;
                if( $match[1] == '' ){
                    $inHeader = true;
                } else {
                    $inHeader = false;
                    $lastId = $match[1];
                    if( $nextIsThisContext ){
                        $lastId = $nextIsThisContext."\004".$lastId;
                        $nextIsThisContext = false;
                    }
                    
                }
            }
            
            if( preg_match('/^msgstr "(.*)"/', $buffer, $match) ){
                if( $inHeader == false ){
                    $lastWasPlural = false;
                    $res['translations'][$lastId] = str_replace('\n', "\n", $match[1]);
                }
            }

            if( preg_match('/^msgid_plural "(.*)"/', $buffer, $match) ){
                if( $inHeader == false ){
                    $lastWasPlural = true;
                    $res['plurals'][$lastId] = str_replace('\n', "\n", $match[1]);
                }
            }

            if( preg_match('/^msgstr\[([0-9]+)\] "(.*)"/', $buffer, $match) ){
                if( $inHeader == false ){
                    $lastPluralId = intval($match[1]);
                    $res['translations'][$lastId][$lastPluralId] = str_replace('\n', "\n", $match[2]);
                }
            }
            
            if( preg_match('/^"(.*)"/', $buffer, $match) ){
                if( $inHeader == true ){
                    $fp = strpos($match[1], ': ');
                    $res['header'][ substr($match[1],0,$fp) ] = str_replace('\n', '', substr($match[1], $fp+2));
                } else {
                    if( is_array($res['translations'][$lastId]) ){
                        $res['translations'][$lastId][$lastPluralId] .= str_replace('\n', "\n", $match[1]);
                    } else {
                        if( $lastWasPlural )
                            $res['plurals'][$lastId] .= str_replace('\n', "\n", $match[1]);
                        else
                            $res['translations'][$lastId] .= str_replace('\n', "\n", $match[1]);
                    }
                }
            }
            
        }
        
        return $res;
    
    }

    public static function saveLanguage( $pModuleName, $pLang, $pLangs ){

        kryn::clearLanguageCache( $pLang );
        $file = 'inc/module/'.$pModuleName.'/lang/'.$pLang.'.po';
        if( $pModuleName == 'kryn' )
            $file = 'inc/kryn/lang/'.$pLang.'.po';
        
        $translations = json_decode( $pLangs, true );
        
        $current = self::parsePo( $pModuleName, $pLang );
        
        $fh = fopen( $file, 'w');
        
        if( $fh == false ) return false;
        
        //todo
        $pluralForms = 'nplurals=2; plural=(n!=1);';
        if( self::getPluralForm($pLang) )
            $pluralForms = self::getPluralForm($pLang);
        
        if( $current ){
        
            $current['header']['Plural-Forms'] = $pluralForms;
            $current['header']['PO-Revision-Date'] = date('Y-m-d H:iO');
            
            fwrite( $fh, 'msgid ""'."\n".'msgstr ""'."\n");
        
            foreach( $current['header'] as $k => $v ){
                fwrite($fh, '"'.$k.': '.$v.'\n"'."\n");
            }
            fwrite($fh, "\n\n");
        
        } else {
        
            //write initial header
            fwrite( $fh, '
msgid ""
msgstr ""
"Project-Id-Version: Kryn.cms - '.$pModuleName.'\n"
"PO-Revision-Date: '.date('Y-m-d H:iO').'\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Language: '.$pLang.'\n"
"Plural-Forms: '.$pluralForms.'\n"'."\n\n");

        }
        if( count($translations) > 0 ){
            
            foreach( $translations as $key => $translation ){
            
                if( strpos($key, "\004") !== false ){
                    //we have a context
                    $context = self::toPoString(substr($key, 0, strpos($key, "\004")));
                    $id = self::toPoString(substr($key, strpos($key, "\004")+1));
                    fwrite( $fh, 'msgctxt '.$context."\n");
                    fwrite( $fh, 'msgid '.$id."\n");
                } else {
                    fwrite( $fh, 'msgid '.self::toPoString($key)."\n");
                }
                
                if( is_array($translation) ){

                    fwrite( $fh, 'msgid_plural '.self::toPoString($translation['plural'])."\n");
                    unset($translation['plural']);
    
                    foreach( $translation as $k => $v ){
                        fwrite( $fh, 'msgstr['.$k.'] '.self::toPoString($v)."\n");
                    }

                } else {
                    fwrite( $fh, 'msgstr '.self::toPoString($translation)."\n");
                }
                
                fwrite( $fh, "\n");
                
            }
        
        }
        fclose($fh);
        
        return true;

        if( $pModuleName == 'kryn' ){
            kryn::fileWrite( 'inc/kryn/lang/'.$pLang.'.json', json_format($pLangs) );
        } else {
            @mkdir( PATH_MODULE.''.$pModuleName.'/lang/' );
            kryn::fileWrite( PATH_MODULE.''.$pModuleName.'/lang/'.$pLang.'.json', json_format($pLangs) );
        }
        kryn::clearLanguageCache( $pLang );
        return true;

    }

    public static function extractLanguage( $pModuleName ){
    
        $GLOBALS['moduleTempLangs'] = array();

        $mod = $pModuleName;

        if( $pModuleName == 'kryn' ){
            
            $config = 'inc/kryn/config.json';
            self::readDirectory( 'inc/kryn/' );
            self::readDirectory( 'inc/template/kryn' );
        } else {
            self::readDirectory( PATH_MODULE.''.$mod );
            self::readDirectory( 'inc/template/'.$mod );
            $config = PATH_MODULE.''.$mod.'/config.json';
        }
        
        self::extractFile( $config );

        $classes = glob(PATH_MODULE.''.$mod.'/*.class.php');
        if( count($classes) > 0 ){
            require_once(PATH_MODULE.'admin/adminWindowEdit.class.php');
            require_once(PATH_MODULE.'admin/adminWindowAdd.class.php');
            require_once(PATH_MODULE.'admin/adminWindowList.class.php');
            foreach( $classes as $class ){
                //todo extract $fields usw 
                $classPlain = kryn::fileRead( $class );
                if( preg_match('/ extends window(Add|List|Edit)/', $classPlain )){
                    require_once( $class );
                    $className = str_replace( PATH_MODULE.''.$mod.'/', '', $class );
                    $className = str_replace( '.class.php', '', $className );
                    $tempObj = new $className();
                    if( $tempObj->columns ){
                        self::extractFrameworkFields( $tempObj->columns );
                    }
                    if( $tempObj->fields ){
                        self::extractFrameworkFields( $tempObj->fields );
                    }
                    if( $tempObj->tabFields ){
                        foreach( $tempObj->tabFields as $key => $fields ){
                             $GLOBALS['moduleTempLangs'][$key] = $key;
                            self::extractFrameworkFields( $fields );
                        }
                    }
                }
            }
        }

        unset($GLOBALS['moduleTempLangs']['']);
        
        return $GLOBALS['moduleTempLangs'];
    }

    public static function extractFrameworkFields( $pFields ){
        foreach( $pFields as $field ){
            $GLOBALS['moduleTempLangs'][$field['label']] = $field['label'];
            $GLOBALS['moduleTempLangs'][$field['desc']] = $field['desc'];
        }
    }

    public static function extractAdmin( $pAdmin ){
        if( is_array($pAdmin) ){
            foreach( $pAdmin as $key => $value ){
                if( $value['title'] )
                    $GLOBALS['moduleTempLangs'][$value['title']] = $value['title'];
                if( $value['type'] == 'add' || $value['type'] == 'edit' || $value['type'] == 'list' ){

                }
                if( is_array($value['childs']) ){
                    self::extractAdmin( $value['childs'] );
                }
            }
        }
    }

    public static function extractFile( $pFile ){
        error_log($pFile);
        $content = file_get_contents( $pFile );

        // t('a', 'as', 3, 'myContext') plural and context
        $content = preg_replace_callback(
            '/t\(\s*["\'](.*[^\\\\])["\']\s*,\s*["\'](.*[^\\\\])["\']\s*,\s*(.+)\s*,\s*["\'](.*[^\\\\])["\']\s*\)/',
            create_function(
                '$pP','$GLOBALS[\'moduleTempLangs\'][$pP[4]."\004".$pP[1]] = array($pP[1], $pP[2]); return "";'
            ), $content 
        );
        
        // t('a', 'as', 3) plural
        $content = preg_replace_callback(
            '/t\(\s*["\'](.*[^\\\\])["\']\s*,\s*["\'](.*[^\\\\])["\']\s*,\s*(.+)\s*\)/',
            create_function(
                '$pP','$GLOBALS[\'moduleTempLangs\'][$pP[1]] = array($pP[1], $pP[2]); return "";'
            ), $content 
        );
    
        // t('a')
        $content = preg_replace_callback(
            '/t\(\s*["\'](.*[^\\\\])["\']\s*\)/',
            create_function(
                '$pP','$GLOBALS[\'moduleTempLangs\'][$pP[1]] = $pP[1]; return "";'
            ), $content 
        );
        
        // tc('a', 'b')
        $content = preg_replace_callback(
            '/tc\(\s*["\'](.*[^\\\\])["\']\s*,\s*["\'](.*[^\\\\])["\']\s*\)/',
            create_function(
                '$pP','$GLOBALS[\'moduleTempLangs\'][$pP[1]."\004".$pP[2]] = $pP[2]; return "";'
            ), $content 
        );
        
        //old _l('bla')
        preg_replace_callback(
            "/_[l]?\('([^']*)'\)/",
            create_function(
                '$pP',
                '
                $GLOBALS[\'moduleTempLangs\'][$pP[1]] = $pP[1];
                '
            ),
            $content 
        );
        
        //template [[bla]]
        preg_replace_callback(
            '/\[\[([^\]]*)\]\]/',
            create_function(
                '$pP',
                '
                $GLOBALS[\'moduleTempLangs\'][$pP[1]] = $pP[1];
                '
            ),
            $content 
        ); 
    }

    public static function readDirectory( $pPath ){
        $h = opendir( $pPath );
        while( $file = readdir($h) ){
            if( $file == '.' || $file == '..' || $file == '.svn' ) continue;
            if( is_dir( $pPath.'/'.$file ) ){
                self::readDirectory($pPath.'/'.$file);
            } else {
                self::extractFile( $pPath.'/'.$file );
            }
        }
    }



}

?>
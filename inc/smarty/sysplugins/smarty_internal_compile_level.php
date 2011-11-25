<?php

/**
 * Smarty Internal Plugin Compile Level
 *
 * Compiles the {levle} tag 
 * @package Smarty
 * @subpackage Compiler
 * @author MArc Schmidt <marc@kryn.org>
 */

/**
 * Smarty Internal Plugin Compile Level Class
 */ 
class Smarty_Internal_Compile_Level extends Smarty_Internal_CompileBase {
    public $optional_attributes = array('id'); 
   	public $shorttag_order = array('id');

    public function compile($args, $compiler)
    {
        $this->compiler = $compiler; 
        
        // check and get attributes
        $this->required_attributes = array('id');
        $this->shorttag_order = array('id'); 
        $_attr = $this->_get_attributes($args); 
        
        $level = str_replace('"', '',$_attr['id']);
        $level = intval($level);
        $id = 'level_block_'.str_replace('-', 'a',$level).'_'.str_replace(',','',microtime(true)).mt_rand().mt_rand();
        

        $code = ''; 
        $this->compiler->levelingStates[ $level ][] = $id;
        
    
        $code .= '<?php $_smarty_levels_id = \''.$id.'\';'."\n";
        if( $level > 0 )
            $code .= 'echo \'{'.$id.'}\'; ';
        $code .= ' function '.$id.'($_smarty_tpl){'."\n";
        $code .= '    global $_smarty_levels_contents;';
        $code .= '    ob_start(); $id = \''.$id.'\';';
        
        return $code . ' ?>';
        
    }
} 


class Smarty_Internal_Compile_Levelclose extends Smarty_Internal_CompileBase {
    public $optional_attributes = array('assign'); 

    public function compile($args, $compiler)
    {
        return '<?php $_smarty_levels_contents[$id] = ob_get_clean(); }; echo $_smarty_levels_contents[$_smarty_levels_id]; ?>';
    }
} 

?>
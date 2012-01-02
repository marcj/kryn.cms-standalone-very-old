<?php

/**
 * Smarty Internal Plugin Compile Level
 *
 * Compiles the {t} tag
 * @package Smarty
 * @subpackage Compiler
 * @author MArc Schmidt <marc@kryn.org>
 */

/**
 * Smarty Internal Plugin Compile Level Class
 */
class Smarty_Internal_Compile_T extends Smarty_Internal_CompileBase {

    public $optional_attributes = array('plural', 'count', 'context');

    public $shorttag_order = array('singular', 'plural', 'count', 'context');
    public $required_attributes = array('singular');

    public function compile($args, $compiler){
        $this->compiler = $compiler;

        // check and get attributes
        //$this->required_attributes = array('singular');
        //$this->shorttag_order = array('singular');

        $_attr = $this->_get_attributes($args);

        if (!$_attr['context']) $_attr['context'] = "''";
        if (!$_attr['count']) $_attr['count'] = "1";

        if ($_attr['plural'])
            return '<?php print t('.$_attr['singular'].','.$_attr['plural'].','.$_attr['count'].','.$_attr['context'].'); ?>';
        else
            return '<?php print t('.$_attr['singular'].'); ?>';

    }
}


?>
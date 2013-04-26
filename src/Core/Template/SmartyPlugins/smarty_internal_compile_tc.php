<?php

/**
 * Smarty Internal Plugin Compile Level
 *
 * Compiles the {tc} tag
 * @package Smarty
 * @subpackage Compiler
 * @author MArc Schmidt <marc@kryn.org>
 */

/**
 * Smarty Internal Plugin Compile Level Class
 */
class Smarty_Internal_Compile_Tc extends Smarty_Internal_CompileBase
{
    public $shorttag_order = array('context', 'msg');
    public $required_attributes = array('context', 'msg');

    public function compile($args, $compiler)
    {
        $this->compiler = $compiler;
        $args = $this->getAttributes($compiler, $args);

        return '<?php print tc('.$args['context'].','.$args['msg'].'); ?>';

    }
}

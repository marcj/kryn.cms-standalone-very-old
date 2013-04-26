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
class Smarty_Internal_Compile_Asset extends Smarty_Internal_CompileBase
{
    public $optional_attributes = array('cache');

    public $shorttag_order = array('path');
    public $required_attributes = array('path');

    public function compile($args, $compiler)
    {
        $this->compiler = $compiler;
        $args = $this->getAttributes($compiler, $args);

        return '<?php print asset('.$args['path'].'); ?>';
    }
}
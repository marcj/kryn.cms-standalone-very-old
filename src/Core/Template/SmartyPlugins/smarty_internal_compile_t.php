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
class Smarty_Internal_Compile_T extends Smarty_Internal_CompileBase
{
    public $optional_attributes = array('plural', 'count', 'context');

    public $shorttag_order = array('singular', 'plural', 'count', 'context');
    public $required_attributes = array('singular');

    public function compile($args, $compiler)
    {
        $this->compiler = $compiler;

        $args = $this->getAttributes($compiler, $args);
        if (!$args['context']) $args['context'] = "''";
        if (!$args['count']) $args['count'] = "1";

        if ($args['plural'])
            return '<?php print t('.$args['singular'].','.$args['plural'].','.$args['count'].','.$args['context'].'); ?>';
        else
            return '<?php print t('.$args['singular'].'); ?>';

    }
}

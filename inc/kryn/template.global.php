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
 * Global template functions
 * @author MArc Schmidt <marc@kryn.org>
 */

/**
 * Defines a value to the specified name in the template engine
 * Accessible in template engine {$<$pName>}
 *
 * @param type $pName
 * @param type $pVal
 */
function tAssign($pName, $pVal) {
    global $tpl;
    tInit();
    return $tpl->assign($pName, $pVal);
}

/**
 * Defines a value by reference to the specified name in the template engine
 * Accessible in template engine {$<$pName>}
 *
 * @param type $pName
 * @param type $pVal
 */
function tAssignRef($pName, &$pVal) {
    global $tpl;
    tInit();
    return $tpl->assignByRef($pName, $pVal);
}

/**
 * Parse and compiled specified template and return the parsed template.
 * Path pFile is relative to inc/template/
 *
 * @param type $pFile
 *
 * @return string Parsed template file
 */
function tFetch($pFile) {
    tInit();
    if ($pFile == "") return;
    global $tpl;
    return kryn::translate($tpl->fetch($pFile));
}

/**
 * Initialize the Smarty object to $tpl
 */
function tInit(){
    global $tpl, $cfg;

    if (!$tpl){

        include('inc/lib/smarty/Smarty.class.php');

        if ($cfg['tpl_cpl'] && !file_exists($cfg['tpl_cpl']))
            @mkdir($cfg['tpl_cpl']);

        $tpl = new Smarty();
        $tpl->template_dir = 'inc/template/';
        $tpl->compile_dir = $cfg['tpl_cpl']?$cfg['tpl_cpl']:'cache/smarty_compile/';
    }
}

?>

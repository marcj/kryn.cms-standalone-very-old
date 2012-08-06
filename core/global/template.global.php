<?php


/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license information, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */


/**
 * Global template functions
 * @author MArc Schmidt <marc@kryn.org>
 * @author Ferdi van der Werf <ferdi@slashdev.nl>
 */

/**
 * Assigns a global value to the specified name in the template engine.
 * Accessible in template engine {$<$pName>}
 *
 * @param type $pName
 * @param type $pVal
 */
function tAssign($pName, $pVal) {
    tInit();
    Core\Kryn::$smarty->assign($pName, $pVal);
}

/**
 * Assigns a global value by reference to the specified name in the template engine.
 * Accessible in template engine {$<$pName>}
 *
 * @param type $pName
 * @param type $pVal
 */
function tAssignRef($pName, &$pVal) {
    tInit();
    Core\Kryn::$smarty->assignByRef($pName, $pVal);
}

/**
 * Returns true if the specified name has a value assigned in global scope.
 *
 * @param $pName
 * @return bool
 */
function tAssigned($pName) {
    tInit();
    return Core\Kryn::$smarty->getTemplateVars($pName) !== null;
}

/**
 * Parse and compiled specified template and return the parsed template.
 * This function also replaces all [[.*]] translation strings through Core\Kryn::translate().
 *
 * @param $pPath <module>/<template_file_in_views>
 * @return mixed
 */
function tFetch($pPath) {

    tInit();

    $path = tPath($pPath);
    return Core\Kryn::translate(Core\Kryn::$smarty->fetch($path));
}

/**
 * Returns the path of given view/template file.
 *
 * @param $pPath
 * @return string
 */
function tPath($pPath){
    $pos = strpos($pPath, '/');
    $file = substr($pPath, $pos+1);
    $module = substr($pPath, 0, $pos);
    return (($module == 'kryn' || $module == 'core')? PATH_CORE : PATH_MODULE . $module . '/') . 'view/' . $file;
}

/**
 * Returns the modification timestamp of given view/template file.
 *
 * @param $pPath
 * @return int
 */
function tModTime($pPath){
    $path = tPath($pPath);
    return file_exists($path) ? filemtime($path): false;
}

/**
 * Initialize the Smarty object to $tpl
 */
function tInit(){

    if (!Core\Kryn::$smarty){

        include('lib/smarty/Smarty.class.php');

        Core\Kryn::$smarty = new Smarty();
        Core\Kryn::$smarty->template_dir = './';
        Core\Kryn::$smarty->registerClass('Kryn', 'Core\Kryn');
        Core\Kryn::$smarty->compile_dir = 'cache/smarty_compile/';
    }
}

?>

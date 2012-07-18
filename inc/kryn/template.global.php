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
 * @author Ferdi van der Werf <ferdi@slashdev.nl>
 */

/**
 * Defines a value to the specified name in the template engine
 * Accessible in template engine {$<$pName>}
 *
 * @param string $pName
 * @param mixed $pVal
 */
function tAssign($pName, $pVal) {
    global $tpl;
    return $tpl->assign($pName, $pVal);
}

/**
 * Defines a value by reference to the specified name in the template engine
 * Accessible in template engine {$<$pName>}
 *
 * @param string $pName
 * @param mixed $pVal
 */
function tAssignRef($pName, &$pVal) {
    global $tpl;
    return $tpl->assignByRef($pName, $pVal);
}

/**
 * Returns true if the specified name has a value assigned to it
 *
 * @param $pName
 * @return bool
 */
function tAssigned($pName) {
    global $tpl;
    return $tpl->getTemplateVars($pName) !== null;
}

/**
 * Parse and compiled specified template and return the parsed template.
 * Path pFile is relative to inc/template/
 *
 * @param string $pFile
 *
 * @return string Parsed template file
 */
function tFetch($pFile) {
    if ($pFile == "") return;
    global $tpl;
    return kryn::translate($tpl->fetch($pFile));
}

?>

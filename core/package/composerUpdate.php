<?php

include __DIR__.'/../global/misc.global.php';

/*
 * Move important ckeditor from vendor to our public available folder
 */
$files = array(
    'core',
    'lang',
    'skins',
    'plugins',
    'ckeditor.js',
    'styles.js',
    'config.js',
    'LICENSE.md',
    'README.md'
);

foreach ($files as $file) {
    copyr('vendor/ckeditor/ckeditor/'.$file,  'web/core/ckeditor/'.$file,  false);
}

/*
 * Move important CodeMirror from vendor to our public available folder
 */
$files = array(
    'addon',
    'keymap',
    'lib',
    'mode',
    'theme',
    'LICENSE',
    'README.md'
);

foreach ($files as $file) {
    copyr('vendor/marijnh/CodeMirror/'.$file,  'web/core/codemirror/'.$file,  false);
}

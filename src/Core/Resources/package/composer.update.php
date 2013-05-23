<?php

include __DIR__ . '/../../global/misc.global.php';

$createSymlink = function ($path, $link) {
    mkdirr(dirname($link));
    if (file_exists($link)) {
        unlink($link);
    }
    symlink(realpath($path), $link);
};

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

$dir = 'src/Core/Resources/public/ckeditor/';

foreach ($files as $file) {
    $createSymlink('vendor/ckeditor/ckeditor/' . $file, $dir . $file);
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

$dir = 'src/Core/Resources/public/codemirror/';

foreach ($files as $file) {
    $createSymlink('vendor/marijnh/CodeMirror/' . $file, $dir . $file);
}

chmod('vendor/google/closure-compiler/compiler.jar', 0644);
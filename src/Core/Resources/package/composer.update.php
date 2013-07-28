<?php

include __DIR__ . '/../../global/misc.global.php';

$createSymlink = function ($path, $link) {
    mkdirr(dirname($link));
    if (file_exists($link)) {
        unlink($link);
    }
    symlink(realpath($path), $link);
};

$dir = 'src/Core/Resources/public/tinymce';
$createSymlink('vendor/tinymce/tinymce/js/tinymce', $dir);

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
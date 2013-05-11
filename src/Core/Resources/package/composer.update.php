<?php

include __DIR__ . '/../../global/misc.global.php';

$createSymlink = function ($path, $link) {
    mkdirr(dirname($link));
    if (file_exists($link)) {
        unlink($link);
    }
    symlink(realpath($path), $link);
};

$createSymlink('vendor/tinymce/tinymce/js/tinymce', 'src/Core/Resources/public/tinymce');

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

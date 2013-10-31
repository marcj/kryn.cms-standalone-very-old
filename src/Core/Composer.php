<?php

namespace Core;

class Composer
{

    public static function update()
    {
        require_once __DIR__ . '/global/misc.global.php';

        $createSymlink = function ($path, $link) {
            mkdirr(dirname($link));
            if (file_exists($link)) {
                unlink($link);
            }
            symlink(realpath($path), $link);
        };

        $dir = __DIR__ . '/Resources/public/tinymce';
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

        $dir = __DIR__ . '/Resources/public/codemirror/';

        foreach ($files as $file) {
            $createSymlink('vendor/marijnh/codemirror/' . $file, $dir . $file);
        }

        chmod('vendor/google/closure-compiler/compiler.jar', 0644);
    }

}
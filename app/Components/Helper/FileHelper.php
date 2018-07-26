<?php

namespace App\Components\Helper;

class FileHelper
{
    public static function files($directory, $prefix = null)
    {
        $glob = glob($directory . '/' . $prefix . '*');

        if ($glob === false) {
            return [];
        }

        return array_filter($glob, function ($file) {
            return filetype($file) == 'file';
        });
    }

    public static function sortByFilemtime($files)
    {
        usort($files, function ($a, $b) {
            return filemtime($a) > filemtime($b);
        });

        return $files;
    }
}

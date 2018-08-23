<?php

namespace App\Components\Helper;

use Illuminate\Http\UploadedFile;

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

    public static function uploadImage(UploadedFile $file, $name, $uploadPath = '')
    {
        $uploadPath = $uploadPath ? public_path($uploadPath) : public_path('img/uploads/');
        $fileName = $name . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $fileName);
        return $fileName;
    }

    public static function uploadExcel(UploadedFile $file, $name, $uploadPath = '')
    {
        $uploadPath = $uploadPath ? storage_path($uploadPath) : storage_path('app/punch-record/');
        $fileName = $name . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $fileName);
        return $fileName;
    }
}

<?php

namespace App\Custom;

class FileManager
    /*
    this class is for hanlding file io on the server (i.e. the hard drive)
    this code is local and not http
    */
{
    public static function storeFile($file, $name): string
    {
        $filePath = public_path() . "/" . $name;
        move_uploaded_file($file, $filePath);
        return $filePath;
    }
}

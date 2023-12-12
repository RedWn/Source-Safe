<?php

namespace App\Custom;

use App\Models\File;

class FileManager
    /*
    this class is for hanlding file io on the server (i.e. the hard drive)
    this code is local and not http
    */
{
    public static function storeFile($file, $name): string
    {
        $filePath = public_path('storage') . "/" . $name;
        move_uploaded_file($file, $filePath);
        return $filePath;
    }

    public static function deleteFile($fileID): bool
    {
        $file = File::where("id", $fileID)->first();
        return unlink(public_path('storage') . $file["serverPath"] . $fileID);
    }

    public static function getFilePath($fileID): string
    {
        return public_path('storage') . "/" . $fileID;
    }

    public static function exists($fileID): bool
    {
        return (File::where("id", $fileID)->first() != null);
    }
}

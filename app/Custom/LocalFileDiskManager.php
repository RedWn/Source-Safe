<?php

namespace App\Custom;

use App\Models\File;

class LocalFileDiskManager
{
    public static function storeFile($file, $fileID): string
    {
        $filePath = self::getFilePath($fileID);
        move_uploaded_file($file, $filePath);
        return $filePath;
    }

    public static function deleteFile($fileID): bool
    {
        $file = File::findOrFail($fileID);
        return unlink($file["serverPath"]);
    }

    public static function getFilePath($fileID): string
    {
        return storage_path() . "\\" . $fileID;
    }

    public static function fileExists($fileID): bool
    {
        $file = File::findOrFail($fileID);
        return file_exists($file["serverPath"]);
    }

    public static function getFileToDownload($fileID): string
    {
        $file = File::findOrFail($fileID);
        copy(self::getFilePath($fileID), storage_path() . "\\" . $file["name"]);
        return storage_path() . "\\" . $file["name"];
    }

    public static function deleteDownloadFile($fileID): bool
    {
        $file = File::findOrFail($fileID);
        return unlink(storage_path() . "\\" . $file["name"]);
    }
}

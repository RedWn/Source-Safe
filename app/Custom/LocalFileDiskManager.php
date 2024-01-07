<?php

namespace App\Custom;

use App\Models\File;

class LocalFileDiskManager
{
    public static function storeFile($file, $fileID, $fileName): string
    {
        $filePath = self::setFilePath($fileID . "-" . $fileName);
        move_uploaded_file($file, $filePath);
        return $filePath;
    }

    public static function deleteFile($fileID): bool
    {
        $file = File::findOrFail($fileID);
        return unlink($file["serverPath"]);
    }

    public static function getFilePath(int $fileID): string
    {
        $file = File::findOrFail($fileID);
        return storage_path() . "\\" . $fileID . "-" . $file["name"];
    }

    public static function setFilePath(string $fileName): string
    {
        return storage_path() . "\\" . $fileName;
    }
}

<?php

namespace App\Http\Controllers;

use App\Custom\FileManager;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File as FileValidationRule;

class FilesController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => FileValidationRule::default()->max('5mb'),
            'filename' => 'required|string|max:200',
            'folderID' => 'required|exists:folders,id',
            'projectID' => 'required|exists:projects,id'
        ]);

        $serverPath = FileManager::storeFile($request->file("file"), $request->input("filename"));

        File::create([
            'serverPath' => $serverPath,
            'name' => $request->input("filename"),
            'folderID' => $request->input("folderID"),
            'projectID' => $request->input("projectID"),
        ]);

        return $this->success(message: 'File added successfully', status: 201);
    }

    public static function download(int $fileId)
    {
        $file = File::findOrFail($fileId);
        return response()->download(FileManager::getFilePath($fileId), name: $file["name"]);
    }

    public function delete(int $fileId)
    {
        $file = File::findOrFail($fileId);

        $file->delete();
        FileManager::deleteFileFromStorage($fileId);

        return $this->success(message: 'Deleted file successfully!');
    }
}

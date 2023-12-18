<?php

namespace App\Http\Controllers;

use App\Custom\LocalFileDiskManager;
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

        $file = File::create([
            'serverPath' => '',
            'name' => $request->input("filename"),
            'folder_id' => $request->input("folderID"),
            'project_id' => $request->input("projectID"),
        ]);

        $serverPath = LocalFileDiskManager::storeFile($request->file("file"), $file->id);
        $file['serverPath'] = $serverPath;

        $file->save();

        return $this->success(message: 'File added successfully', status: 201);
    }

    public static function download(int $fileId)
    {
        $file = File::findOrFail($fileId);
        return response()->download(LocalFileDiskManager::getFilePath($fileId), name: $file["name"]);
    }

    public function delete(int $fileId)
    {
        $file = File::findOrFail($fileId);
        LocalFileDiskManager::deleteFile($fileId);
        $file->delete();

        return $this->success(message: 'Deleted file successfully!');
    }
}

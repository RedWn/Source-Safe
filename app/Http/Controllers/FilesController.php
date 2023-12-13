<?php

namespace App\Http\Controllers;

use App\Custom\FileManager;
use App\Models\File;
use Exception;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    public function uploadFile(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file',
                'filename' => 'required|string',
                'folderID' => 'required',
                'projectID' => 'required'

            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        $file = $request->file("file");
        $fID = $request->input("folderID");
        $name = $request->input("filename");
        $pID = $request->input("projectID");
        try {
            $dbfile = File::create([
                'serverPath' => '/',
                'name' => $name,
                'folderID' => $fID,
                'projectID' => $pID,
                'checked' => 0,
            ]);
        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage()], 400);
        }
        $serverPath = FileManager::storeFile($file, $dbfile['id']);
        $dbfile['serverPath'] = $serverPath;
        return $this->success(message: 'File added successfully', status: 201);
    }

    public static function downloadFile(int $fileId)
    {
        $file = File::where('id', $fileId)->first();
        $file_name = $file["name"];
        $file_path = $file["serverPath"];

        if (!FileManager::exists($fileId)) {
            return response()->json(['msg' => "$file_name not found"], 400);
        }
        return response()->download(FileManager::getFilePath($fileId), name: $file_name);
    }
    public function deleteFile(int $fileId)
    {
        if ($fileId != "" && FileManager::exists($fileId)) {
            FileManager::deleteFile($fileId);
            File::destroy($fileId);
            return $this->success(message: 'successed');
        } else {
            return response()->json(['msg' => 'please insert valid id'], 400);
        }
    }
}

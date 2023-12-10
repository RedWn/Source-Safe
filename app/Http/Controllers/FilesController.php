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
                'path' => 'required|string',
                'filename' => 'required|string',
                'projectID' => 'required'

            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        $file = $request->file("file");
        $path = $request->input("path");
        $name = $request->input("filename");
        $pID = $request->input("projectID");
        try {
            $dbfile = File::create([
                'projectPath' => $path,
                'serverPath' => '/',
                'name' => $name,
                'projectID' => $pID,
            ]);
        } catch (\Exception $e) {
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

        if (!file_exists(public_path() . $file_path . $file["id"])) {
            return response()->json(['msg' => "$file_name not found"], 400);
        }
        return response()->download(public_path() . $file_path . $file["id"], $name = $file_name);
    }
    public function deleteFile(int $fileId)
    {
        if ($fileId != "" && FileManager::exist($fileId)) {
            FileManager::deleteFile($fileId);
            File::destroy($fileId);
            return $this->success(message: 'successed');
        } else {
            return response()->json(['msg' => 'please insert valid id'], 400);
        }
    }

    public function getAllFiles()
    {
        $files = File::all();
        $fileVals = [];
        foreach ($files as $file) {
            $fileVals[] = $file;
        }
        $fileVals = array_unique($fileVals);

        return $this->success($fileVals, 'Successed');
    }
}

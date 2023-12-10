<?php

namespace App\Http\Controllers;

use App\Custom\FileManager;
use App\Models\File;
use Exception;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    public static function uploadFile(Request $request)
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
        return response()->json([
            'msg' => 'File added successfully',
        ], 201);
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
    public static function deleteFile(Request $request, int $id)
    {
        $name = $request->input("filename");

        if ($id != "") {
            if (File::destroy($id))
                return response()->json(['msg' => 'successed'], 200);
        } else if ($name != "") {
            File::destroy($name);
            return response()->json(['msg' => 'successed'], 200);
        } else {
            return response()->json(['msg' => 'please insert id or name'], 400);
        }
        return response()->json(['msg' => 'wrong parameters'], 400);
    }

    public static function getAllFiles()
    {
        $files = File::all();
        $fileVals = [];
        foreach ($files as $file) {
            $fileVals[] = $file;
        }
        $fileVals = array_unique($fileVals);

        return response()->json(['msg' => 'Successed', 'files' => array_values($fileVals)], 200);
    }
}

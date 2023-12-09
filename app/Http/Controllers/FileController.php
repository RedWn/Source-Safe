<?php

namespace App\Http\Controllers;

use App\Models\File;
use Exception;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public static function addFile(Request $request)
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
        $serverPath = self::storeFile($file, $dbfile['id'], $pID);
        $dbfile['serverPath'] = $serverPath;
        return response()->json([
            'msg' => 'File added successfully',
        ], 201);
    }

    private static function storeFile($file, $name, $projectID): string
    {
        $fileURL = $projectID . '-' . $name;
        $filePath = public_path() . $fileURL;
        move_uploaded_file($file, $filePath);
        return $filePath;
    }
}

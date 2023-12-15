<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Exception;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'folderID' => 'required',
        ]);
        $name = $request->input("name");
        $fID = $request->input("folderID");
        $pID = $request->input("projectID");
        try {
            $newFolder = Folder::create([
                'name' => $name,
                'folderID' => $fID,
                'projectID' => $pID,
            ]);
        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage()], 400);
        }
        return $this->success(message: 'Folder added successfully', data: $newFolder, status: 201);
    }

    public function getSubFolders(int $folderID)
    {
        $folders = Folder::where('folderID', $folderID)->get();
        $files = File::where('folderID', $folderID)->get();
        $Vals["folders"] = $folders;
        $Vals["files"] = $files;
        return $this->success($Vals, 'Successed');
    }
}

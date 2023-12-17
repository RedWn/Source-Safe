<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'folderID' => 'required|exists:folders,id',
            'projectID' => 'required|exists:projects,id'
        ]);

        $folder = Folder::create([
            'name' =>  $request->input("name"),
            'folderID' => $request->input("folderID"),
            'projectID' => $request->input("projectID"),
        ]);

        return $this->success($folder, 'Folder added successfully', 201);
    }

    public function getFolderContents(int $folderID)
    {
        $folders = Folder::where('folderID', $folderID)->get();
        $files = File::where('folderID', $folderID)->get();

        $data = [
            "folders" => $folders,
            "files" => $files,
        ];

        return $this->success($data);
    }
}

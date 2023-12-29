<?php

namespace App\Http\Controllers;

use App\Http\Resources\FileResource;
use App\Http\Resources\FolderResource;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FolderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:1|max:255',
            'folderID' => 'required|exists:folders,id',
            'projectID' => 'required|exists:projects,id'
        ]);

        $folder = Folder::create([
            'name' => $request->input("name"),
            'folder_id' => $request->input("folderID"),
            'project_id' => $request->input("projectID"),
        ]);

        return $this->success(new FolderResource($folder), 'Folder added successfully', 201);
    }

    public function getFolderContents(int $folderID)
    {
        $folders = Folder::where('folder_id', $folderID)->get();
        $files = File::where('folder_id', $folderID)->get();

        return $this->success([
            "folders" => FolderResource::collection($folders),
            "files" => FileResource::collection($files),
        ]);
    }

    public function delete(int $folderID)
    {
        DB::transaction(function () use ($folderID) {
            $folder = Folder::lockForUpdate()->findOrFail($folderID);

            $folder->files()->delete();
            $folder->delete();
        });

        return $this->success(message: "Folder deleted successfully.");
    }
}

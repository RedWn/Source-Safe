<?php

namespace App\Http\Controllers;

use App\Http\Resources\FileResource;
use App\Http\Resources\FolderResource;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class FolderController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:1|max:255',
            'parent_folder_id' => 'required|exists:folders,id',
        ]);

        $parentFolder = Folder::findOrFail($request->input("parent_folder_id"));

        Gate::authorize("update-project-resource", $parentFolder->project_id);

        $folder = Folder::create([
            'name' => $request->input("name"),
            'folder_id' => $parentFolder->id,
            'project_id' => $parentFolder->project_id,
        ]);

        return $this->success(new FolderResource($folder), 'Folder created successfully', 201);
    }

    public function update(Request $request, int $folderId)
    {
        $request->validate([
            'name' => 'required|string|min:1|max:255',
        ]);

        $folder = Folder::findOrFail($folderId);

        Gate::authorize("update-project-resource", $folder->project_id);

        $folder->update([
            'name' => $request->input('name')
        ]);

        return $this->success(new FolderResource($folder), 'Folder updated successfully', 201);
    }

    public function getFolderContents(int $folderID)
    {
        $rootFolder = Folder::findOrFail($folderID);

        $folders = Folder::where('folder_id', $folderID)->where('project_id', $rootFolder->project_id)->get();
        $files = File::where('folder_id', $folderID)->where('project_id', $rootFolder->project_id)->get();

        return $this->success([
            "folders" => FolderResource::collection($folders),
            "files" => FileResource::collection($files),
        ]);
    }

    public function delete(int $folderID)
    {
        DB::beginTransaction();

        $folder = Folder::lockForUpdate()->findOrFail($folderID);

        Gate::authorize("update-project-resource", $folder->project_id);

        $folder->files()->delete();
        $folder->delete();

        DB::commit();

        return $this->success(message: "Folder deleted successfully.");
    }
}

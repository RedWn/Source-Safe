<?php

namespace App\Http\Controllers;

use App\Custom\LocalFileDiskManager;
use App\Models\Checkin;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\File as FileValidationRule;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => FileValidationRule::default()->max('5mb'),
            'filename' => 'required|string|max:200',
            'parent_folder_id' => 'required|exists:folders,id'
        ]);

        $parentFolder = Folder::findOrFail($request->input("parent_folder_id"));

        Gate::authorize('update-project-resource', $parentFolder->project_id);

        $file = File::create([
            'serverPath' => '',
            'name' => $request->input("filename"),
            'folder_id' => $parentFolder->id,
            'project_id' => $parentFolder->project_id,
        ]);

        $serverPath = LocalFileDiskManager::storeFile($request->file("file"), $file->id, $file->name);
        $file['serverPath'] = $serverPath;

        $file->save();

        return $this->success(message: 'File added successfully', status: 201);
    }

    public function download(int $fileId)
    {
        return response()->download(LocalFileDiskManager::getFilePath($fileId));
    }

    public function delete(int $fileId)
    {
        $file = File::findOrFail($fileId);

        Gate::authorize('update-project-resource', $file->project_id);

        LocalFileDiskManager::deleteFile($fileId);
        $file->delete();

        return $this->success(message: 'Deleted file successfully!');
    }

    public function report(int $fileId)
    {
        $entries = Checkin::where('file_id', $fileId)
            ->select('created_at', 'updated_at', 'user_id', 'checkout_date', 'done')
            ->get();
        $array = array();
        $array[] = ['Time', 'Operation', 'user_id', 'checkout_date'];
        foreach ($entries as $entry) {
            $array[] = [$entry->created_at, 'check in', $entry->user_id, $entry->checkout_date];
            if ($entry->done == 1) {
                $array[] = [$entry->updated_at, 'check out', $entry->user_id, $entry->checkout_date];
            }
        }
        return response()->download(LocalFileDiskManager::writetoCSV($fileId, $array));
    }
}

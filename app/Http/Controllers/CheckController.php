<?php

namespace App\Http\Controllers;

use App\Custom\LocalFileDiskManager;
use App\Models\Checkin;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CheckController extends Controller
{
    public function checkout(Request $request, int $fileID)
    {
        DB::beginTransaction();

        $request->validate([
            'file' => 'nullable|file'
        ]);

        $file = File::findOrFail($fileID);

        if ($file->checked_in_by == null) {
            DB::rollBack();
            return $this->error("File is not checked in.");
        }
        if ($file->checked_in_by != $request->user()->id) {
            DB::rollBack();
            return $this->error("You do not have permissions to check out this file", 403);
        }

        $file->checked_in_by = null;
        $file->markPendingCheckinsAsDone();
        $file->save();

        DB::commit();

        if (!$request->has('file')) {
            return $this->success(message: "File is checked out and reverted.");
        }

        $requestFile = $request->file("file");
        LocalFileDiskManager::storeFile($requestFile, $fileID);

        return $this->success(message: "File is checked out and updated.");
    }

    public function checkin(Request $request): JsonResponse
    {
        $request->validate([
            'durationInDays' => 'required|int|min:1|max:3',
            'fileIDs' => 'required|array'
        ]);

        DB::beginTransaction();

        $userId = request()->user()->id;
        $files = File::lockForUpdate()->find($request->input('fileIDs'));

        foreach ($files as $file) {
            if ($file->checked_in_by != null) {
                DB::rollBack();
                return $this->error("Some files are already checked in.");
            }
        }

        foreach ($files as $file) {
            Checkin::insert([
                'file_id' => $file->id,
                'user_id' => $userId,
                'done' => false,
                'checkout_date' => now()->addDays($request->input('durationInDays')),
            ]);
        }

        // Why toQuery()? https://github.com/livewire/livewire/discussions/4193
        $files->toQuery()->update(['checked_in_by' => $userId]);

        DB::commit();

        return $this->success(message: "File(s) checked in successfully!");
    }
}

<?php

namespace App\Http\Controllers;

use App\Custom\LocalFileDiskManager;
use App\Models\Checkin;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckController extends Controller
{
    public function checkout(Request $request, int $fileID)
    {
        $request->validate([
            'file' => 'nullable|file'
        ]);

        $file = File::findOrFail($fileID);

        if ($file->checkedInBy == null)  return $this->error("File is not checked in.");
        if ($file->checkedInBy != $request->user()->id) return $this->error("You do not have permissions to check out this file", 403);

        $file->update(['checkedInBy' => null]);

        if (!$request->has('file')) {
            return $this->success(message: "File is checked out and reverted.");
        }

        $requestFile = $request->file("file");
        LocalFileDiskManager::storeFile($requestFile, $fileID);

        return $this->success(message: "File is checked out and updated.");
    }

    public function checkin(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'durationInDays' => 'required|int|min:1|max:3'
        ]);

        $file = File::lockForUpdate()->findOrFail($id);
        $userId = request()->user()->id;

        if ($file->checkedInBy != null) {
            if ($file->checkedInBy == $userId)
                return $this->error("File is already checked in by your account.");
            else
                return $this->error("File is already checked in by another user! Please try again later.");
        }

        $file->update(['checkedInBy' => $userId]);

        Checkin::insert([
            'checkout_date' => now()->addDays($request->input('durationInDays')),
            'user_id' => $userId,
            'file_id' => $id,
        ]);

        return $this->success(message: "File checked in successfully!");
    }
}

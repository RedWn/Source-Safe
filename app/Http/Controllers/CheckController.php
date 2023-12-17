<?php

namespace App\Http\Controllers;

use App\Custom\FileManager;
use App\Models\Checkin;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'fileID' => 'required|exists:files,id',
        ]);
        if (!$request->has('file')) {
            $fileID = $request->input("fileID");
            $file = File::findOrFail($fileID);

            if ($file->checkedInBy == null)
                return $this->error("File is not checked in.");
            if ($file->checkedInBy != $request->user()->id)
                return $this->error("You do not have permissions to check out this file", 403);
            $file->update(['checkedInBy' => null]);
            return $this->success(message: "File is checked out and reverted.");
        }
        $requestFile = $request->file("file");
        $fileID = $request->input("fileID");

        $file = File::findOrFail($fileID);

        if ($file->checkedInBy == null)
            return $this->error("File is not checked in.");
        if ($file->checkedInBy != $request->user()->id)
            return $this->error("You do not have permissions to check out this file", 403);

        FileManager::storeFile($requestFile, $fileID);
        $file->update(['checkedInBy' => null]);

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
            if ($file->checkedInBy == $userId) return $this->error("File is already checked in by your account.");
            else return $this->error("File is already checked out! Please try again later.");
        }

        $file->update(['checkedInBy' => $userId]);

        Checkin::insert([
            'checkout_date' => now()->addDays($request->input('durationInDays')),
            'userID' => $userId,
            'fileID' => $id,
        ]);

        return $this->success(message: "File checked out successfully!");
    }
}

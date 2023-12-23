<?php

namespace App\Http\Controllers;

use App\Custom\LocalFileDiskManager;
use App\Models\Checkin;
use App\Models\File;
use App\Models\Ledger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckController extends Controller
{
    public function checkout(Request $request, int $fileID)
    {
        $request->validate([
            'file' => 'nullable|file'
        ]);

        $checkin = Checkin::where('file_id', $fileID)->where('done', 0)->first();

        if (!$checkin)
            return $this->error("File is not checked in.");
        if ($checkin->user_id != $request->user()->id)
            return $this->error("You do not have permissions to check out this file", 403);

        $checkin->update(['done' => 1]);

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

        $userId = request()->user()->id;
        $checkin = Checkin::where('file_id', $id)->where('done', 0)->first();

        if ($checkin) {
            if ($checkin->user_id == $userId) {
                return $this->error("File is already checked in by your account.");
            }
            return $this->error("File is already checked in by another user! Please try again later.");
        }

        Checkin::insert([
            'checkout_date' => now()->addDays($request->input('durationInDays')),
            'user_id' => $userId,
            'file_id' => $id,
        ]);

        return $this->success(message: "File checked in successfully!");
    }
}

<?php

namespace App\Http\Controllers;

use App\Custom\LocalFileDiskManager;
use App\Models\Checkin;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckController extends Controller
{
    public function checkout(Request $request)
    {
        DB::beginTransaction();

        $request->validate([
            'file' => 'nullable|file',
            'file_ids' => 'required|array',
            'file_ids.*' => 'integer'
        ]);

        $files = File::findOrFail($request->input('file_ids'));

        foreach ($files as $file) {
            if ($file->checked_in_by == null) {
                DB::rollBack();
                return $this->error("File ($file->id) is not checked in.");
            }
            if ($file->checked_in_by != $request->user()->id) {
                DB::rollBack();
                return $this->error("You do not have permissions to check out file ($file->id)", 403);
            }
        }

        foreach ($files as $file) {
            $file->checked_in_by = null;

            $file->markPendingCheckinsAsDone();
            $file->save();
        }

        DB::commit();

        if (count($files) > 1) {
            return $this->success(message: "Files are checked out and reverted.");
        }

        if (!$request->has('file')) {
            return $this->success(message: "File is checked out and reverted.");
        }

        $requestFile = $request->file("file");
        LocalFileDiskManager::storeFile($requestFile, $request->input('file_ids')[0]);

        return $this->success(message: "File is checked out and updated.");
    }

    public function checkin(Request $request): JsonResponse
    {
        $dateAfterThreeDays = Carbon::now()->addDays(3)->toString();

        $request->validate([
            'checkoutDate' => "required|date_format:Y-m-d|before:$dateAfterThreeDays|after:today",
            'fileIDs' => 'required|array'
        ]);

        DB::beginTransaction();

        $userId = request()->user()->id;
        $files = File::lockForUpdate()->findOrFail($request->input('fileIDs'));

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
                'checkout_date' => $request->input('checkoutDate'),
            ]);
        }

        // Why toQuery()? https://github.com/livewire/livewire/discussions/4193
        $files->toQuery()->update(['checked_in_by' => $userId]);

        DB::commit();

        $checkedInFileIds = $files
            ->map(fn (File $file) => $file->id)
            ->join(", ");

        $username = $request->user()->username;
        $duration = $request->input('checkoutDate');

        Log::info("File(s) $checkedInFileIds were checked in by $username. Checkout date: $duration.");

        return $this->success(message: "File(s) checked in successfully!");
    }
}

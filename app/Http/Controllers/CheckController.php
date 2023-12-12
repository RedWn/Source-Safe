<?php

namespace App\Http\Controllers;

use App\Custom\FileManager;
use App\Models\Checkin;
use App\Models\File;
use Illuminate\Http\Request;
use Exception;

class CheckController extends Controller
{
    public function checkoutFile(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file',
                'fileID' => 'required',
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        $file = $request->file("file");
        $fileID = $request->input("fileID");
        $checks = Checkin::where("fileID", $fileID)->where('checkedOut', 0)->get();
        if ($checks->isEmpty()) {
            return response()->json(["message" => "file is not checked in"], 400);
        }

        foreach ($checks as $check) {
            if ($check["userID"] != $request->user()->id) {
                return response()->json(["message" => "file is not checked in by user"], 403);
            } else {
                FileManager::storeFile($file, $fileID);
                Checkin::where("fileID", $fileID)->update(["checkedOut" => 1]);
                File::where("id", $fileID)->update(["checked" => 0]);
                return $this->success(message: "file is checked out and updated", status: 201);
            }
        }
        return response()->json(["message" => "error"], 400);
    }

    public function checkoutFileAuto(Request $request)
    {
        $fileID = $request->input("fileID");
        $checks = Checkin::where("fileID", $fileID)->where('checkedOut', 0)->get();
        if ($checks->isEmpty()) {
            return response()->json(["message" => "file is not checked in"], 400);
        }

        foreach ($checks as $check) {
            if ($check["userID"] != $request->user()->id) {
                return response()->json(["message" => "file is not checked in by user"], 403);
            } else {
                Checkin::where("fileID", $fileID)->update(["checkedOut" => 1]);
                File::where("id", $fileID)->update(["checked" => 0]);
                return $this->success(message: "file is checked out and reverted", status: 200);
            }
        }
    }
}

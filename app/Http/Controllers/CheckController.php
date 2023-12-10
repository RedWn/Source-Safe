<?php

namespace App\Http\Controllers;

use App\Models\Checkin;
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
        $checks = Checkin::where("fileID", $fileID)->where('checkedOut', false)->get();
        if (!$checks->isEmpty()) {
            return response()->json(["message" => "file is not checked in"], 400);
        }
        /*
        this code is disabled until I know how to get the userID
        foreach ($checks as $check) {
        if ($check["userID"] != $request->$user["id"]) {
            return response()->json(["message"=> "file is not checked in by user"],403);
        }else{
        $check["checkedOut"] = true;
        FileManager::storeFile($file, $fileID);
        return response()->json(["message" => "file is checked out and updated"], 201);
        }
        }
        */
    }

    public function checkoutFileAuto(Request $request)
    {
        $fileID = $request->input("fileID");
        $checks = Checkin::where("fileID", $fileID)->where('checkedOut', false)->get();
        if (!$checks->isEmpty()) {
            return response()->json(["message" => "file is not checked in"], 400);
        }
        /*
        this code is disabled until I know how to get the userID
        foreach ($checks as $check) {
        if ($check["userID"] != $request->$user["id"]) {
            return response()->json(["message"=> "file is not checked in by user"],403);
        }else{
        $check["checkedOut"] = true;
        return response()->json(["message" => "file is checked out and reverted"], 200);
        }
        }
        */
    }
}

<?php

namespace App\Http\Controllers;

use App\Custom\LocalFileDiskManager;
use App\Http\Resources\UserResource;
use App\Models\Checkin;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function search(string $name)
    {
        $users = User::where("username", "like", "%$name%")->get();
        return $this->success(UserResource::collection($users));
    }

    public function report(int $userId)
    {
        $entries = Checkin::where('user_id', $userId)
            ->select('created_at', 'updated_at', 'file_id', 'checkout_date', 'done')
            ->get();
        $array = array();
        $array[] = ['Time', 'Operation', 'File_id', 'checkout_date'];
        foreach ($entries as $entry) {
            $array[] = [$entry->created_at, 'check in', $entry->file_id, $entry->checkout_date];
            if ($entry->done == 1) {
                $array[] = [$entry->updated_at, 'check out', $entry->file_id, $entry->checkout_date];
            }
        }
        return response()->download(LocalFileDiskManager::writetoCSV($userId, $array));
    }
}

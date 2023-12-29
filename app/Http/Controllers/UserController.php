<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function search(string $name)
    {
        $users = User::where("username", "like", "%$name%")->get();
        return $this->success(UserResource::collection($users));
    }
}

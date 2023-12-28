<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function search(string $name)
    {
        $users = User::select('id', 'username')->where("username", "like", "%$name%")->get();
        return $this->success($users);

    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($request->username)->plainTextToken;
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users|string|min:3|max:200',
            'password' => 'required|string|min:6|max:200',
        ]);

        $user = User::create($validated);

        return $user->createToken($request->username)->plainTextToken;
    }
}

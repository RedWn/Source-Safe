<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function success(mixed $data = [], string $message = "", int $status = 200) {
        return response()->json([
            'data' => $data,
            'message' => $message
        ], $status);
    }
}

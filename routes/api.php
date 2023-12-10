<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('/files')->group(function () {
        Route::get('/', [\App\Http\Controllers\FilesController::class, 'getAllFiles']);
        Route::get('/{id}', [\App\Http\Controllers\FilesController::class, 'downloadFile'])->whereNumber('id');
        Route::delete('/{id}', [\App\Http\Controllers\FilesController::class, 'deleteFile'])->whereNumber('id');
        Route::post('/upload', [\App\Http\Controllers\FilesController::class, 'uploadFile']);
    });
});

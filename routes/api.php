<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\FolderController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('/files')->group(function () {
        Route::get('/', [FilesController::class, 'getAllFiles']);

        Route::post('/upload', [FilesController::class, 'upload']);
        Route::post('/checkOut', [CheckController::class, 'checkout']);
        Route::post('/autoCheckOut', [CheckController::class, 'checkoutAuto']);

        Route::delete('/{id}', [FilesController::class, 'delete'])->whereNumber('id');
        Route::get('/download/{id}', [FilesController::class, 'download'])->whereNumber('id');
        Route::post('/checkin/{id}', [CheckController::class, 'checkin'])->whereNumber('id');
    });

    Route::prefix('/folders')->group(function () {
        Route::get('/{id}', [FolderController::class, 'getFolderContents']);
        Route::post('/new', [FolderController::class, 'createFolder']);
    });
});

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

// Mobile Authentication
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('/files')->group(function () {
        Route::get('/', [\App\Http\Controllers\FilesController::class, 'getAllFiles']);
        Route::get('/download/{id}', [\App\Http\Controllers\FilesController::class, 'downloadFile']); //this should be download because there will probably a get file by id 
        Route::delete('/{id}', [\App\Http\Controllers\FilesController::class, 'deleteFile']);
        Route::post('/upload', [\App\Http\Controllers\FilesController::class, 'uploadFile']);
        Route::post('/checkOut', [\App\Http\Controllers\CheckController::class, 'checkoutFile']);
        Route::post('/autoCheckOut', [\App\Http\Controllers\CheckController::class, 'checkoutFileAuto']);

    });
    Route::prefix('/folders')->group(function () {
        Route::get('/{id}', [\App\Http\Controllers\FolderController::class, 'getSubFolders']);
        Route::post('/new', [\App\Http\Controllers\FolderController::class, 'createFolder']);
    });
});

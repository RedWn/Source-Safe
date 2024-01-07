<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
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
Route::get('/searchUsers/{name}', [UserController::class, 'search'])->whereAlpha('name');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('/files')->group(function () {
        Route::post('/upload', [FileController::class, 'upload']);

        Route::delete('/{id}', [FileController::class, 'delete'])->whereNumber('id');
        Route::get('/{id}/download', [FileController::class, 'download'])->whereNumber('id');

        Route::post('/checkout', [CheckController::class, 'checkout']);
        Route::post('/checkin', [CheckController::class, 'checkin']);
    });

    Route::prefix('/folders')->group(function () {
        Route::get('/{id}', [FolderController::class, 'getFolderContents'])->whereNumber('id');
        Route::put('/{id}', [FolderController::class, 'update'])->whereNumber('id');
        Route::delete('/{id}', [FolderController::class, 'delete'])->whereNumber('id');
        Route::post('/', [FolderController::class, 'create']);
    });

    Route::prefix('/projects')->group(function () {
        Route::post('/', [ProjectController::class, 'create']);
        Route::put('/{id}', [ProjectController::class, 'edit'])->whereNumber('id');
        Route::delete('/{id}', [ProjectController::class, 'delete'])->whereNumber('id');

        Route::get('/{id}/users', [ProjectController::class, 'getProjectUsers'])->whereNumber('id');
        Route::post('/{id}/adduser', [ProjectController::class, 'addUser'])->whereNumber('id');
        Route::post('/{id}/removeuser', [ProjectController::class, 'removeUser'])->whereNumber('id');
    });

    Route::prefix('/my')->group(function () {
        Route::get('/projects', [ProjectController::class, 'getMyProjects']);
    });
});

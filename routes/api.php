<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::prefix('/users')->group(function () {
    Route::get('/search/{name}', [UserController::class, 'search'])->whereAlpha('name');
    Route::get('/{id}/report', [UserController::class, 'report'])->whereNumber('id');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('/files')->group(function () {
        Route::delete('/{id}', [FileController::class, 'delete'])->whereNumber('id');
        Route::get('/{id}/download', [FileController::class, 'download'])->whereNumber('id');
        Route::get('/{id}/report', [FileController::class, 'report'])->whereNumber('id');

        Route::post('/upload', [FileController::class, 'upload']);
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

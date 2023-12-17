<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProjectController;
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
        Route::post('/upload', [FilesController::class, 'upload']);
        Route::post('/checkout', [CheckController::class, 'checkout']);

        Route::delete('/{id}', [FilesController::class, 'delete'])->whereNumber('id');
        Route::get('/download/{id}', [FilesController::class, 'download'])->whereNumber('id');
        Route::post('/checkin/{id}', [CheckController::class, 'checkin'])->whereNumber('id');
    });

    Route::prefix('/folders')->group(function () {
        Route::get('/{id}', [FolderController::class, 'getFolderContents'])->whereNumber('id');
        Route::post('/new', [FolderController::class, 'createFolder']);
    });

    Route::prefix('/projects')->group(function () {
        Route::get('/', [ProjectController::class, 'getUserProjects']);
        Route::get('/users/{id}', [ProjectController::class, 'getProjectUsers'])->whereNumber('id');

        Route::post('/adduser', [ProjectController::class, 'addUser']);
        Route::post('/removeuser', [ProjectController::class, 'removeUser']);

        Route::post('/new', [ProjectController::class, 'create']);
        Route::post('/edit', [ProjectController::class, 'edit'])->whereNumber('id');
        Route::delete('/{id}', [ProjectController::class, 'delete'])->whereNumber('id');
    });
});

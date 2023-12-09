<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/addFile', [\App\Http\Controllers\FileController::class, 'addFile']);
Route::get('/downloadFile', [\App\Http\Controllers\FileController::class, 'downloadFile']);
Route::post('/deleteFile', [\App\Http\Controllers\FileController::class, 'deleteFile']);
Route::get('/getAllFiles', [\App\Http\Controllers\FileController::class, 'getAllFiles']);

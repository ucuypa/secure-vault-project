<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VaultFileController;
use App\Http\Controllers\Api\ActivityLogController;
// use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route Publik untuk Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // API untuk melihat daftar file milir user
    Route::get('/files', [VaultFileController::class, 'index']);

    // API untuk upload file dan enkripsi file baru
    Route::post('/files', [VaultFileController::class, 'store']);
});
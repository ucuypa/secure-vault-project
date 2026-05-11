<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VaultFileController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    
    // --- 1. AUTH & PROFILE ---
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']); 
    
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    // --- 2. FILES & FOLDERS ---
    Route::get('/files', [VaultFileController::class, 'index']);
    Route::post('/files', [VaultFileController::class, 'store']);
    Route::get('/files/{id}/download', [VaultFileController::class, 'download']);
    Route::put('/files/{id}', [VaultFileController::class, 'update']);
    Route::delete('/files/{id}', [VaultFileController::class, 'destroy']);

    // --- 3. ACTIVITY LOGS ---
    Route::get('/activity', [ActivityLogController::class, 'index']);

});
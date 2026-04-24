<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VaultFileController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Route Download
    Route::get('files/{id}/download', [VaultFileController::class, 'download']);
    
    // Resource CRUD
    Route::apiResource('files', VaultFileController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    // Resource Vault Files
    Route::get('files/{id}/download', [VaultFileController::class, 'download']);
    Route::apiResource('files', VaultFileController::class);

    // Resource Activity Logs
    Route::apiResource('logs', ActivityLogController::class);
});
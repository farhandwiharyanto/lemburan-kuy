<?php

use App\Http\Controllers\Api\OvertimeApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::apiResource('overtimes', OvertimeApiController::class);
    Route::post('/overtimes/{id}/approve', [OvertimeApiController::class, 'approve']);
    Route::post('/logout', [AuthController::class, 'apiLogout']);
});
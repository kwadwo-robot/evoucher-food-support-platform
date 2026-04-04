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

Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('/unread', [\App\Http\Controllers\Api\NotificationController::class, 'unread']);
    Route::get('/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unread']);
    Route::post('/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
});

Route::middleware('auth:sanctum')->prefix('surplus')->group(function () {
    Route::get('/allocations/{foodListingId}', [\App\Http\Controllers\Api\SurplusAllocationController::class, 'getCurrentAllocation']);
    Route::post('/claim/{foodListingId}', [\App\Http\Controllers\Api\SurplusAllocationController::class, 'claim']);
    Route::get('/my-allocations', [\App\Http\Controllers\Api\SurplusAllocationController::class, 'myAllocations']);
});

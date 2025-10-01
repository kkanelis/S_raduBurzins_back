<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SpecialDayController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\ChristmasLotteryController;
use App\Http\Controllers\UserStatusController;

// Public routes for authentication
Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::get('/special-days', [SpecialDayController::class, 'index']);
Route::get('/christmas-lottery', [ChristmasLotteryController::class, 'index']);

// Protected routes that require authentication
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::post('/special-days', [SpecialDayController::class, 'store']);
    Route::get('/user/special-days', [SpecialDayController::class, 'userSpecialDays']);

    Route::get('/users/status', [UserStatusController::class, 'index']);
    Route::post('/users/status', [UserStatusController::class, 'updateStatus']);

});


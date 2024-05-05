<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::prefix('/auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::prefix('/users')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::get('/', 'index');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/profile', 'profile');
            Route::post('/profile/update', 'update');
            Route::prefix('/friendship')->group(function () {
                Route::post('/{target_id}', 'requestFriendship');
                Route::get('/request', 'getAllRequestFriendship');
                Route::delete('/reject/{id}', 'rejectFriendship');
                Route::put('/accept/{id}', 'acceptFriendship');
                Route::get('/', 'getAllMyFriends');
                Route::get('/details/{friend_id}', 'friendDetail');
            });
            Route::put('/update-password', [AuthController::class, 'passwordUpdate'])->middleware('auth:sanctum');
            Route::get('/colek', 'colek');
        });
    });
});

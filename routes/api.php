<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppController;

Route::prefix('/v1')->group(function () {

    Route::prefix('/auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    Route::prefix('/users')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/', 'index');

            Route::middleware('auth:sanctum')->group(function () {
                Route::get('/profile', 'profile');
                Route::put('/profile', 'update');
                Route::prefix('/friendship')->group(function () {
                    Route::post('/{target_id}', 'requestFriendship');
                    Route::get('/request', 'getAllRequestFriendship');
                    Route::delete('/reject/{id}', 'rejectFriendship');
                    Route::put('/accept/{id}', 'acceptFriendship');
                    Route::get('/', 'getAllMyFriends');
                    Route::get('/details/{friend_id}', 'friendDetail');
                });
                Route::put('/update-password', [AuthController::class, 'passwordUpdate']);
                Route::get('/colek', 'colek');
            });
        });
        Route::get('/whatsapp/{phone}', [WhatsAppController::class, 'sendMessage'])->middleware('auth:sanctum');
    });

    Route::prefix('/schools')->controller(SchoolController::class)->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');

        Route::middleware(['auth:sanctum', 'admin_access'])->group(function () {
            Route::post('/', 'create');
            Route::put('/{id}', 'update');
            Route::delete('/{id}', 'delete');
        });
    });
});

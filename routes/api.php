<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgressController;
use App\Models\User;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('edit-profile', [AuthController::class, 'updateProfile']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('progress', [ProgressController::class, 'index']);
    Route::post('progress', [ProgressController::class, 'store']);
    Route::get('progress/{id}', [ProgressController::class, 'show']);
    Route::put('progress/{id}', [ProgressController::class, 'update']);
    Route::delete('progress/{id}', [ProgressController::class, 'destroy']);
    Route::post('progress/upsert', [ProgressController::class, 'upsert']);
    Route::get('/user-progress', [ProgressController::class, 'getUserProgress']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::get('users', function () {
        return User::all();
    });
});
